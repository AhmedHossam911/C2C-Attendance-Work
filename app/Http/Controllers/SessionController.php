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

        if ($user->hasRole('hr')) {
            // HR sees sessions for their authorized committees
            $committeeIds = $user->authorizedCommittees->pluck('id');
            $query->whereIn('committee_id', $committeeIds);
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
        $committees = \App\Models\Committee::all(); // For filter dropdown

        return view('sessions.index', compact('sessions', 'committees'));
    }

    public function export(AttendanceSession $session)
    {
        // Check permission: HR can only export their own authorized committee sessions
        $user = Auth::user();
        if ($user->hasRole('hr') && !$user->authorizedCommittees->contains($session->committee_id)) {
            abort(403, 'Unauthorized action.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SessionExport($session), 'session_' . $session->id . '_attendance.xlsx');
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('hr')) {
            $committees = $user->authorizedCommittees;
        } else {
            $committees = \App\Models\Committee::all();
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
        if ($user->hasRole('hr')) {
            // Verify HR is authorized for this committee
            if (!$user->authorizedCommittees->contains($validated['committee_id'])) {
                return back()->withErrors(['committee_id' => 'You are not authorized for this committee.']);
            }
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

    public function show(AttendanceSession $session)
    {
        $session->load('records.user');
        return view('sessions.show', compact('session'));
    }

    public function toggleStatus(AttendanceSession $session)
    {
        $session->status = $session->status === 'open' ? 'closed' : 'open';
        $session->save();

        return back()->with('success', 'Session status updated.');
    }
}
