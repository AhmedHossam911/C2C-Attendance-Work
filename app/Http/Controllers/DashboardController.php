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
        } elseif ($user->hasRole('committee_head')) {
            // "cards for analysis must be shown as total members for his commite with out him or any hr or other head"
            $authorizedCommittees = $user->authorizedCommittees;
            $committeeIds = $authorizedCommittees->pluck('id');

            // Count unique members across these committees, strictly role 'member'
            $totalMembers = User::where('role', 'member')
                ->whereHas('committees', function ($q) use ($committeeIds) {
                    $q->whereIn('committees.id', $committeeIds);
                })->count();

            $data['headStats'] = [
                'my_committees' => $authorizedCommittees->count(),
                'total_members' => $totalMembers,
                'open_sessions' => AttendanceSession::whereIn('committee_id', $committeeIds)->where('status', 'open')->count(),
                'pending_reviews' => \App\Models\TaskSubmission::whereHas('task', function ($q) use ($committeeIds) {
                    $q->whereIn('committee_id', $committeeIds);
                })->where('status', 'pending')->count(),
            ];
        } else {
            $memberCommitteeIds = $user->committees->pluck('id');
            $data['memberStats'] = [
                'present' => $user->attendanceRecords()->where('status', 'present')->count(),
                'late' => $user->attendanceRecords()->where('status', 'late')->count(),
                'total' => $user->attendanceRecords()->count(),
                'pending_tasks' => \App\Models\Task::whereIn('committee_id', $memberCommitteeIds)
                    ->where('deadline', '>', now()) // Only active tasks
                    ->whereDoesntHave('submissions', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->count(),
            ];
        }

        // 2. Base Query
        $sessionsQuery = AttendanceSession::with('committee');

        if (!$user->hasRole('top_management')) {
            if ($user->hasRole('board') || $user->hasRole('committee_head') || $user->hasRole('hr')) {
                $committeeIds = $user->authorizedCommittees->pluck('id');
                // Board sees all, or authorized? "Board + Top Management sees all" in routes comments, 
                // but typically board might be restricted. Assuming authorized for consistency or all if board logic implies all.
                // Actually, TaskPolicy says Board sees all. Let's keep Board seeing all if they are 'board'.
                // Wait, TaskPolicy said "HR Board: View all tasks". DashboardController says: 
                // if (!top_mgmt && !board) { filter by user->committees } => Board sees all.
                // But wait, existing code was: if (!top && !board). So Board saw all.

                // However, for Committee Head, we MUST filter.
                if ($user->hasRole('committee_head') || $user->hasRole('hr')) {
                    $committeeIds = $user->authorizedCommittees->pluck('id');
                    $sessionsQuery->whereIn('committee_id', $committeeIds);
                }
                // Board skips this filter, seeing all.
            } else {
                // Regular Member
                $committeeIds = $user->committees()->pluck('committees.id');
                $sessionsQuery->whereIn('committee_id', $committeeIds);
            }
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

        return view('Common.dashboard', $data);
    }
}
