<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Attendance Stats
        $attendanceStats = [
            'present' => $user->attendanceRecords()->where('status', 'present')->count(),
            'late' => $user->attendanceRecords()->where('status', 'late')->count(),
            'total' => $user->attendanceRecords()->count(),
        ];

        // 2. Upcoming/Open Sessions
        // If HR/Top Management, show sessions they can manage/scan? Or sessions they can attend?
        // Requirement says "Members can attend any committee". So show ALL open sessions for everyone.
        $upcomingSessions = AttendanceSession::with('committee')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        // 3. Recent Session History (Closed sessions)
        $recentSessions = AttendanceSession::with('committee')
            ->where('status', 'closed')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('attendanceStats', 'upcomingSessions', 'recentSessions'));
    }
}
