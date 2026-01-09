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

        // Role-based Access Control
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            // Can view all sessions
        } elseif ($user->hasRole('hr')) {
            // HR: View sessions for Authorized Committees AND Member Committees
            $authorizedIds = $user->authorizedCommittees->pluck('id');
            $memberIds = $user->committees->pluck('id');
            $query->whereIn('committee_id', $authorizedIds->merge($memberIds)->unique());
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            // Heads: View sessions for Authorized Committees
            $query->whereIn('committee_id', $user->authorizedCommittees->pluck('id'));
        } else {
            // Members: View sessions for Member Committees only
            $query->whereIn('committee_id', $user->committees->pluck('id'));
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
            $committees = $user->authorizedCommittees->merge($user->committees)->unique('id');
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            $committees = $user->authorizedCommittees;
        } else {
            $committees = $user->committees;
        }

        // Prepare authorizedCommitteeIds for View Logic (used in manage checks)
        $authorizedCommitteeIds = [];
        if ($user->authorizedCommittees->isNotEmpty()) {
            $authorizedCommitteeIds = $user->authorizedCommittees->pluck('id')->toArray();
        }

        return view('sessions.index', compact('sessions', 'committees', 'authorizedCommitteeIds'));
    }

    public function export(AttendanceSession $session)
    {
        // Check permission: HR/Head/Board can only export their own authorized committee sessions
        // Board/Top Management might be allowed all? Assuming Board follows Authorized for specific actions or Global?
        // Let's allow Board/Top Global, others Authorized.
        $user = Auth::user();

        if ($user->hasRole('top_management')) {
            // Allowed
        } elseif ($user->hasRole('board')) {
            // Board viewed all, so should be able to export all? Or restricted? 
            // "Board can only update authorized" in TaskPolicy. Let's restrict to Authorized for consistency if Board is managing specific committees, but allowing global view.
            // However, for Sessions, usually Board has high oversight.
            // User's reverted edit: "if in_array(hr, board...) && !authorized -> abort". This implies Board also restricted to Authorized for ACTIONS.
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (in_array($user->role, ['hr', 'committee_head', 'vice_head'])) {
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // Regular members cannot export
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
            // Board can create for Authorized only? 
            // "HR Board can only create tasks for Authorized committees" (TaskPolicy).
            $committees = $user->authorizedCommittees;
        } elseif ($user->hasRole('hr') || $user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            $committees = $user->authorizedCommittees;
        } else {
            abort(403, 'Unauthorized.');
        }
        return view('sessions.create', compact('committees'));
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
        } elseif (in_array($user->role, ['hr', 'board', 'committee_head', 'vice_head'])) {
            if (!$user->authorizedCommittees->contains($validated['committee_id'])) {
                return back()->withErrors(['committee_id' => 'You are not authorized for this committee.']);
            }
        } else {
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

        return view('sessions.show', compact('session', 'records'));
    }

    public function toggleStatus(AttendanceSession $session)
    {
        $user = Auth::user();

        if ($user->hasRole('top_management')) {
            // Allowed
        } elseif (in_array($user->role, ['hr', 'board', 'committee_head', 'vice_head'])) {
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            abort(403, 'Unauthorized.');
        }

        $session->status = $session->status === 'open' ? 'closed' : 'open';
        $session->save();

        return back()->with('success', 'Session status updated.');
    }
}
