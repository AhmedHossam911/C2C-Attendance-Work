<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AttendanceSession::with('committee')->withCount('records');

        // CONTROL VIEW LOGIC
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            // Can view all sessions
        } elseif ($user->hasRole('hr')) {
            // HR: View sessions for Authorized Committees ONLY (Manage)
            // They do NOT see sessions they are members of here.
            $authorizedIds = $user->authorizedCommittees->pluck('id');
            $query->whereIn('committee_id', $authorizedIds);
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            // Heads: View sessions for Authorized Committees ONLY (View Only - or Manage if policy allows)
            $query->whereIn('committee_id', $user->authorizedCommittees->pluck('id'));
        } else {
            // Members: Should not be here typically if Sidebar is correct, but if they access it:
            // return redirect()->route('sessions.history'); // Optional redirect?
            // For now, let's just show empty or their member sessions if we want fail-safe?
            // User asked for separation. Let's return empty or 403?
            // Let's safe-guard: if member, redirect to history.
            return redirect()->route('sessions.history');
        }

        if ($request->filled('committee_id')) {
            $query->where('committee_id', $request->committee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sessions = $query->latest()->paginate(10)->withQueryString();

        // Filter valid committees for the filter dropdown
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            $committees = \App\Models\Committee::all();
        } elseif ($user->hasRole('hr')) {
            $committees = $user->authorizedCommittees;
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            $committees = $user->authorizedCommittees;
        } else {
            $committees = collect();
        }

        // Prepare authorizedCommitteeIds for View Logic (used in manage checks)
        $authorizedCommitteeIds = [];
        if ($user->authorizedCommittees->isNotEmpty()) {
            $authorizedCommitteeIds = $user->authorizedCommittees->pluck('id')->toArray();
        }

        return view('Common.Sessions.index', compact('sessions', 'committees', 'authorizedCommitteeIds'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        // HISTORY VIEW: Sessions where user is a member
        $memberCommitteeIds = $user->committees->pluck('id');

        // Also include sessions where they are a member even if they are HR? 
        // "My Sessions" (History) should show where they are a MEMBER.

        $query = AttendanceSession::with(['committee', 'records' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
            ->whereIn('committee_id', $memberCommitteeIds);

        $sessions = $query->latest()->paginate(10);

        return view('Common.Sessions.history', compact('sessions'));
    }

    public function export(AttendanceSession $session)
    {
        $user = Auth::user();

        if ($user->hasRole('top_management')) {
            // Allowed
        } elseif ($user->hasRole('board')) {
            // Board: Full Access
            // Allowed
        } elseif ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized action.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SessionExport($session), 'session_' . $session->id . '_attendance.xlsx');
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('top_management')) {
            $committees = \App\Models\Committee::all();
        } elseif ($user->hasRole('board')) {
            // Board: Full Access (All Committees)
            $committees = \App\Models\Committee::all();
        } elseif ($user->hasRole('hr')) {
            $committees = $user->authorizedCommittees;
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized.');
        }
        return view('Board.Sessions.create', compact('committees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'late_threshold_minutes' => 'required|integer|min:0',
            'counts_for_attendance' => 'boolean',
            'committee_id' => 'required|exists:committees,id',
        ]);

        $user = Auth::user();
        $user = Auth::user();

        // Enforce authorization
        if ($user->hasRole('top_management')) {
            // Allowed
        } elseif ($user->hasRole('board')) {
            // Allowed for all
        } elseif ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($validated['committee_id'])) {
                return back()->withErrors(['committee_id' => 'You are not authorized for this committee.']);
            }
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized.');
        }

        // Check if there is already an open session for this committee
        $existingSession = AttendanceSession::where('committee_id', $validated['committee_id'])
            ->where('status', 'open')
            ->exists();

        if ($existingSession) {
            return back()->withErrors(['committee_id' => 'There is already an open session for this committee. Please close it first.']);
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open'; // Default to closed

        AttendanceSession::create($validated);

        return redirect()->route('sessions.index')->with('success', 'Session created successfully.');
    }

    public function show(AttendanceSession $session, Request $request) // Typehint Request
    {
        $user = Auth::user();
        $canManageSession = false;
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            $canManageSession = true;
        } elseif ($user->hasRole('hr') || $user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            $canManageSession = $user->authorizedCommittees->contains($session->committee_id);
        }

        // CONTROL VIEW: Only for managers
        if (!$canManageSession) {
            abort(403, 'Unauthorized access to session control.');
        }

        $query = $session->records()
            ->with(['user', 'scanner', 'updater'])
            ->orderBy('scanned_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $records = $query->paginate(20)->withQueryString();

        return view('Common.Sessions.show', compact('session', 'records', 'canManageSession'));
    }

    public function memberDetails(AttendanceSession $session, Request $request)
    {
        $user = Auth::user();

        // Pass only the current user's record
        $records = $session->records()->where('user_id', $user->id)->get();

        return view('Common.Sessions.member_show', compact('session', 'records'));
    }

    public function toggleStatus(AttendanceSession $session)
    {
        $user = Auth::user();

        if ($user->hasRole('top_management')) {
            // Allowed
        } elseif ($user->hasRole('board')) {
            // Allowed for all
        } elseif ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized.');
        }

        $session->status = $session->status === 'open' ? 'closed' : 'open';
        $session->save();

        return back()->with('success', 'Session status updated.');
    }
}
