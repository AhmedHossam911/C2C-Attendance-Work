<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Committee;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // 1. Stats
        if ($user->hasRole('top_management')) {
            $data['adminStats'] = [
                'committees' => Committee::count(),
                'open_sessions' => AttendanceSession::where('status', 'open')->count(),
                'attendees_today' => AttendanceRecord::whereDate('created_at', now()->today())->whereIn('status', ['present', 'late'])->count(),
                'total_users' => User::count(),
            ];
        } else {
            $data['attendanceStats'] = [
                'present' => $user->attendanceRecords()->where('status', 'present')->count(),
                'late' => $user->attendanceRecords()->where('status', 'late')->count(),
                'total' => $user->attendanceRecords()->count(),
            ];
        }

        // 2. Base Query
        $sessionsQuery = AttendanceSession::with('committee');

        if (!$user->hasRole('top_management') && !$user->hasRole('board')) {
             $committeeIds = $user->committees()->pluck('committees.id');
             $sessionsQuery->whereIn('committee_id', $committeeIds);
        }

        // 3. Upcoming/Open Sessions
        $data['upcomingSessions'] = (clone $sessionsQuery)
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        // 4. Recent Session History (Closed sessions)
        $data['recentSessions'] = (clone $sessionsQuery)
            ->where('status', 'closed')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', $data);
    }
}
