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
        $user = Auth::user();

        // Block HR from creating feedback
        if ($user->role === 'hr') {
            return back()->withErrors(['feedback' => 'HR members cannot submit feedback.']);
        }

        // VALIDATION: Session must be closed
        if ($session->status !== 'closed') {
            return back()->withErrors(['feedback' => 'Feedback can only be submitted when the session is closed.']);
        }

        // VALIDATION: Member must have attended (Status Present)
        $attended = $session->records()
            ->where('user_id', $user->id)
            ->where('status', 'present')
            ->exists();

        // Allow late attendees to submit feedback? "Present" usually implies attended. 
        // User prompt didn't specify, but safer to include 'late' if they were there. 
        // Let's stick to 'present' for now as per original code, unless 'late' is also considered attended.
        // Actually, looking at Record statuses: 'present', 'late'. Both mean they were there.
        if (!$attended) {
            // Check for 'late' status too
            $attended = $session->records()
                ->where('user_id', $user->id)
                ->where('status', 'late')
                ->exists();
        }

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
