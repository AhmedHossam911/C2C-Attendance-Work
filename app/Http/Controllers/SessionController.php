<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = AttendanceSession::latest()->get();
        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'late_threshold_minutes' => 'required|integer|min:0',
            'counts_for_attendance' => 'boolean',
        ]);

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
