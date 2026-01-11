<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\SessionFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionFeedbackController extends Controller
{
    public function store(Request $request, AttendanceSession $session)
    {
    public function store(Request $request, AttendanceSession $session)
    {
        $user = Auth::user();

        // 1. Role Restrictions: Top Management, Board, and Head cannot submit feedback
        if (in_array($user->role, ['top_management', 'board', 'committee_head', 'vice_head'])) {
            return back()->withErrors(['feedback' => 'Your role is not eligible to submit feedback.']);
        }

        // 2. Session Status: Must be closed
        if ($session->status !== 'closed') {
            return back()->withErrors(['feedback' => 'Feedback can only be submitted when the session is closed.']);
        }

        // 3. Attendance Check: Must be Present or Late
        $attended = $session->records()
            ->where('user_id', $user->id)
            ->whereIn('status', ['present', 'late'])
            ->exists();

        if (!$attended) {
            return back()->withErrors(['feedback' => 'You must have attended this session to submit feedback.']);
        }

        $validated = $request->validate([
            'objectives_clarity' => 'required|integer|min:1|max:5',
            'instructor_understanding' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string', // "Please share your thoughts about sessions performance"
            'overall_satisfaction' => 'required|integer|min:1|max:10',
            'room_suitability' => 'required|string',
            'attendance_system_rating' => 'required|integer|min:1|max:10',
            'attendance_system_suggestions' => 'nullable|string',
            'future_suggestions' => 'nullable|string',
        ]);

        // Check availability (One feedback per user per session)
        $existing = SessionFeedback::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->update($validated);
            $message = 'Feedback updated successfully.';
        } else {
            SessionFeedback::create(array_merge($validated, [
                'attendance_session_id' => $session->id,
                'user_id' => $user->id,
            ]));
            $message = 'Thank you! Your feedback has been submitted.';
        }

        return back()->with('success', $message);
    }
}
