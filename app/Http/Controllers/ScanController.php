<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Ensure $user is defined

        if (in_array($user->role, ['top_management', 'board'])) {
            // Full Access: Show all open sessions
            $activeSessions = AttendanceSession::where('status', 'open')->get();
        } elseif ($user->hasRole('hr')) {
            // HR: Authorized Only
            $activeSessions = AttendanceSession::where('status', 'open')
                ->whereIn('committee_id', $user->authorizedCommittees->pluck('id'))
                ->get();
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized.');
        }
        return view('Board.Scan.index', compact('activeSessions'));
    }

    public function store(Request $request, AttendanceSession $session)
    {
        if ($session->status !== 'open') {
            return response()->json(['message' => 'Session is closed.'], 400);
        }

        $user = Auth::user();

        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            // Allowed
        } elseif ($user->hasRole('hr')) {
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // Heads & Members: Deny
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // Check if already scanned
        $existingRecord = AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRecord) {
            return response()->json(['message' => 'Member already scanned.'], 409);
        }

        // Calculate status (present/late)
        $now = Carbon::now();
        $sessionStart = $session->created_at; // Assuming session start is creation time for simplicity, or we could add a started_at column
        // If the session was opened later, we might want to track 'opened_at'.
        // For now, let's assume the 'late' logic is based on when the session was created/opened.
        // Let's use the session created_at as the reference point.

        $diffInMinutes = $sessionStart->diffInMinutes($now);
        $status = $diffInMinutes > $session->late_threshold_minutes ? 'late' : 'present';

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => Auth::id(),
            'scanned_at' => $now,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Scan successful.',
            'user' => $user->name,
            'status' => $status
        ]);
    }
}
