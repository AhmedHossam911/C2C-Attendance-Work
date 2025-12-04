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
            // HR sees sessions for their committees
            $committeeIds = $user->committees->pluck('id');
            $query->whereIn('committee_id', $committeeIds);
        }

        if ($request->filled('committee_id')) {
            $query->where('committee_id', $request->committee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(10)->withQueryString();
        $committees = \App\Models\Committee::all(); // For filter dropdown

        return view('sessions.index', compact('sessions', 'committees'));
    }

    public function export(AttendanceSession $session)
    {
        // Check permission: HR can only export their own committee sessions
        $user = Auth::user();
        if ($user->hasRole('hr') && !$user->committees->contains($session->committee_id)) {
            abort(403, 'Unauthorized action.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SessionExport($session), 'session_' . $session->id . '_attendance.xlsx');
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('hr')) {
            $committees = $user->committees;
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
            // Verify HR is assigned to this committee
            if (!$user->committees->contains($validated['committee_id'])) {
                return back()->withErrors(['committee_id' => 'You are not assigned to this committee.']);
            }
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'closed'; // Default to closed

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
