<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\SessionFeedback;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionFeedbackController extends Controller
{
    /**
     * Show the feedback creation form.
     */
    public function create(AttendanceSession $session)
    {
        $user = Auth::user();

        // Check if user has already submitted
        $userFeedback = SessionFeedback::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        return view('Common.Feedbacks.create', compact('session', 'userFeedback'));
    }

    /**
     * Store or update feedback.
     */
    public function store(Request $request, AttendanceSession $session)
    {
        $user = Auth::user();

        // 1. Role Restrictions
        if (in_array($user->role, ['top_management', 'board', 'committee_head', 'vice_head'])) {
            return back()->withErrors(['feedback' => 'Your role is not eligible to submit feedback.']);
        }

        // 2. Session Status
        if ($session->status !== 'closed') {
            return back()->withErrors(['feedback' => 'Feedback can only be submitted when the session is closed.']);
        }

        // 3. Attendance Check
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
            'feedback' => 'nullable|string',
            'overall_satisfaction' => 'required|integer|min:1|max:10',
            'room_suitability' => 'required|string',
            'attendance_system_rating' => 'required|integer|min:1|max:10',
            'attendance_system_suggestions' => 'nullable|string',
            'future_suggestions' => 'nullable|string',
        ]);

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

    /**
     * Display a listing of sessions with feedback (Feedback Dashboard).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Access Control
        if (!in_array($user->role, ['top_management', 'board', 'hr', 'committee_head', 'vice_head'])) {
            abort(403);
        }

        $query = AttendanceSession::whereHas('feedbacks')
            ->withCount('feedbacks')
            ->withAvg('feedbacks', 'overall_satisfaction');

        // Committee Filtering based on Role or Request
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            // Full access, optional filter
            if ($request->filled('committee_id')) {
                $query->where('committee_id', $request->committee_id);
            }
        } elseif ($user->hasRole('hr')) {
            // Only authorized committees
            // If user selects a specific committee, verify access
            if ($request->filled('committee_id')) {
                if (!$user->authorizedCommittees->contains($request->committee_id)) {
                    abort(403, 'Unauthorized access to this committee.');
                }
                $query->where('committee_id', $request->committee_id);
            } else {
                // Show all authorized
                $query->whereIn('committee_id', $user->authorizedCommittees->pluck('id'));
            }
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            // Only own committee
            // (Assuming heads are linked via authorizedCommittees or by specific committee_id logic depending on app structure)
            // Using authorizedCommittees generic approach as per previous context
            $query->whereIn('committee_id', $user->authorizedCommittees->pluck('id'));
        }

        $sessions = $query->latest()
            ->paginate(10)
            ->through(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'committee' => $session->committee->name ?? 'General',
                    'date' => $session->created_at->format('M d, Y'),
                    'feedback_count' => $session->feedbacks_count,
                    'avg_rating' => $session->feedbacks_avg_overall_satisfaction ?? 0,
                ];
            });

        // Provide committees for filter dropdown (if accessible)
        $committees = [];
        if ($user->hasRole('top_management') || $user->hasRole('board') || $user->hasRole('hr')) {
            if ($user->hasRole('hr')) {
                $committees = $user->authorizedCommittees;
            } else {
                $committees = Committee::all();
            }
        }

        return view('Common.Feedbacks.index', compact('sessions', 'committees'));
    }

    /**
     * Display specific feedback results for a session.
     */
    public function show(AttendanceSession $session)
    {
        $user = Auth::user();

        // 1. Role Check
        if (!in_array($user->role, ['top_management', 'board', 'committee_head', 'vice_head', 'hr'])) {
            abort(403, 'Unauthorized access to feedback results.');
        }

        // 2. Authorization Scope Check
        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            // Can view all
        } elseif ($user->hasRole('committee_head') || $user->hasRole('vice_head') || $user->hasRole('hr')) {
            // Must be authorized for this committee
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized. You do not have access to this committee\'s feedback.');
            }
        }

        // 3. Fetch Data & Calculate Stats
        $feedbacks = $session->feedbacks()->with('user')->latest()->paginate(12);

        $total = $session->feedbacks()->count();
        if ($total > 0) {
            $stats = [
                'total' => $total,
                'avg_satisfaction' => $session->feedbacks()->avg('overall_satisfaction'),
                'avg_objectives' => $session->feedbacks()->avg('objectives_clarity'),
                'avg_instructor' => $session->feedbacks()->avg('instructor_understanding'),
                'avg_system' => $session->feedbacks()->avg('attendance_system_rating'),
                'room_suitability' => $session->feedbacks()
                    ->select('room_suitability')
                    ->selectRaw('count(*) as count')
                    ->groupBy('room_suitability')
                    ->pluck('count', 'room_suitability')
            ];
        } else {
            $stats = [
                'total' => 0,
                'avg_satisfaction' => 0,
                'avg_objectives' => 0,
                'avg_instructor' => 0,
                'avg_system' => 0,
                'room_suitability' => []
            ];
        }

        // 4. Anonymity Logic
        $shouldAnonymize = !$user->hasRole('top_management');

        if ($shouldAnonymize) {
            $feedbacks->getCollection()->transform(function ($feedback) {
                unset($feedback->user);
                $feedback->user_name = 'Anonymous Member';
                return $feedback;
            });
        }

        return view('Common.Feedbacks.show', compact('session', 'feedbacks', 'stats', 'shouldAnonymize'));
    }
}
