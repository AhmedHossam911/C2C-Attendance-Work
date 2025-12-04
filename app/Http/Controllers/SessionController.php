<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('hr')) {
            // HR sees sessions for their committees
            $committeeIds = $user->committees->pluck('id');
            $sessions = AttendanceSession::whereIn('committee_id', $committeeIds)->latest()->get();
        } else {
            $sessions = AttendanceSession::latest()->get();
        }
        return view('sessions.index', compact('sessions'));
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
