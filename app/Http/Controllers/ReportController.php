<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Committee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ReportController extends Controller
{
    public function index()
    {
        // Dashboard Landing - Access check is strictly implicit via routes/middleware mostly, 
        // but we can add a check if needed. HR/Board/Top Management only.
        if (!Auth::user()->hasRole('top_management') && !Auth::user()->hasRole('board') && !Auth::user()->hasRole('hr') && !Auth::user()->hasRole('committee_head')) {
            abort(403);
        }
        return view('Heads.Reports.index');
    }

    public function committees(Request $request)
    {
        // 1. Get Authorized Committee List for Dropdown
        $user = Auth::user();

        // Reusing getCommitteesData logic to respect all authorization rules
        $allCommitteesRaw = $this->getCommitteesData();

        // Prepare simple list for dropdown
        $committeesList = $allCommitteesRaw->map(fn($c) => (object)['id' => $c->id, 'name' => $c->name]);

        // 2. Determine Selected Committee
        $selectedId = $request->input('committee_id');

        // Default to first if not set
        if (!$selectedId && $committeesList->isNotEmpty()) {
            $selectedId = $committeesList->first()->id;
        }

        // 3. Get Selected Committee Data
        $selectedCommittee = $allCommitteesRaw->firstWhere('id', $selectedId);

        // Edge case: Invalid ID or empty list
        if (!$selectedCommittee) {
            return view('Heads.Reports.committees', [
                'committeesList' => $committeesList,
                'selectedCommittee' => null,
                'members' => new LengthAwarePaginator([], 0, 12),
                'selectedId' => $selectedId
            ]);
        }

        // 4. Process Members for Selected Committee
        $selectedCommittee->load('sessions');
        $committeeSessionIds = $selectedCommittee->sessions->pluck('id');
        $totalCommitteeSessions = $committeeSessionIds->count();

        // Members are already loaded and privacy-filtered in getCommitteesData
        // We map them to stats objects
        $membersData = $selectedCommittee->users->map(function ($user) use ($committeeSessionIds, $totalCommitteeSessions) {
            $myRecords = $user->attendanceRecords->whereIn('attendance_session_id', $committeeSessionIds);

            $present = $myRecords->where('status', 'present')->count();
            $late = $myRecords->where('status', 'late')->count();
            $attended = $myRecords->count();
            $rate = $totalCommitteeSessions > 0 ? round(($attended / $totalCommitteeSessions) * 100) : 0;

            return (object) [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'total_sessions_count' => $totalCommitteeSessions,
                'attended_count' => $attended,
                'present' => $present,
                'late' => $late,
                'absent' => max(0, $totalCommitteeSessions - $attended),
                'attendance_rate' => $rate,
                'status' => $user->status
            ];
        });

        // 5. Filter by Search Query (if present)
        $search = $request->input('search');
        if ($search) {
            $membersData = $membersData->filter(function ($member) use ($search) {
                return stripos($member->name, $search) !== false;
            });
        }

        // 6. Manual Pagination of the Collection
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 12; // Cards per page
        $items = $membersData->forPage($page, $perPage);

        $paginatedMembers = new LengthAwarePaginator(
            $items,
            $membersData->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
        // Append query parameters to pagination links
        $paginatedMembers->appends($request->all());

        return view('Heads.Reports.committees', [
            'committeesList' => $committeesList,
            'selectedCommittee' => $selectedCommittee,
            'members' => $paginatedMembers,
            'selectedId' => $selectedId
        ]);
    }

    public function ghostMembers(Request $request)
    {
        $accessLevel = \App\Models\ReportPermission::getAccessLevel('ghost_members', Auth::user()->role);

        if ($accessLevel === \App\Models\ReportPermission::ACCESS_NONE) {
            abort(403, 'Unauthorized access to Ghost Members report.');
        }

        // Logic: Members with 0 attendance records AND 0 task submissions
        $query = User::where('role', 'member')
            ->whereDoesntHave('attendanceRecords')
            ->whereDoesntHave('taskSubmissions');

        // Filter: Committee
        if ($request->filled('committee_id')) {
            $query->whereHas('committees', function ($q) use ($request) {
                $q->where('committees.id', $request->committee_id);
            });
        }

        // Apply Scope Constraints
        if ($accessLevel === \App\Models\ReportPermission::ACCESS_OWN) {
            // Restricted to Authorized Committees
            $committeeIds = Auth::user()->authorizedCommittees->pluck('id');
            if ($committeeIds->isEmpty()) abort(403, 'No assigned committees.');

            // Verify requested committee is owned
            if ($request->filled('committee_id') && !$committeeIds->contains($request->committee_id)) {
                abort(403, 'Unauthorized access to this committee.');
            }

            $query->whereHas('committees', function ($q) use ($committeeIds) {
                $q->whereIn('committees.id', $committeeIds);
            });
            $committees = Auth::user()->authorizedCommittees;
        } else {
            // ACCESS_GLOBAL
            $committees = Committee::orderBy('name')->get();
        }

        // Filter: Search Name/Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $ghosts = $query->with('committees')->paginate(12)->withQueryString();

        return view('Heads.Reports.ghost_members', compact('ghosts', 'committees'));
    }

    public function topPerformers(Request $request)
    {
        $accessLevel = \App\Models\ReportPermission::getAccessLevel('top_performers', Auth::user()->role);

        if ($accessLevel === \App\Models\ReportPermission::ACCESS_NONE) {
            abort(403, 'Unauthorized access to Top Performers report.');
        }

        // Logic: Attendance + Tasks
        $query = User::where('role', 'member')
            ->withCount(['attendanceRecords', 'taskSubmissions']);

        // Filter: Committee
        if ($request->filled('committee_id')) {
            $query->whereHas('committees', function ($q) use ($request) {
                $q->where('committees.id', $request->committee_id);
            });
        }

        // Apply Scope Constraints
        if ($accessLevel === \App\Models\ReportPermission::ACCESS_OWN) {
            // Restricted to Authorized Committees
            $committeeIds = Auth::user()->authorizedCommittees->pluck('id');
            if ($committeeIds->isEmpty()) abort(403, 'No assigned committees.');

            // Verify requested committee is owned
            if ($request->filled('committee_id') && !$committeeIds->contains($request->committee_id)) {
                abort(403, 'Unauthorized access to this committee.');
            }

            $query->whereHas('committees', function ($q) use ($committeeIds) {
                $q->whereIn('committees.id', $committeeIds);
            });
            $committees = Auth::user()->authorizedCommittees;
        } else {
            // ACCESS_GLOBAL
            $committees = Committee::orderBy('name')->get();
        }

        $performers = $query->orderByDesc('attendance_records_count')
            ->orderByDesc('task_submissions_count')
            ->paginate(20)
            ->withQueryString(); // Use paginate instead of take/get

        return view('Heads.Reports.top_performers', compact('performers', 'committees'));
    }

    public function committeePerformance()
    {
        $accessLevel = \App\Models\ReportPermission::getAccessLevel('committee_performance', Auth::user()->role);

        if ($accessLevel === \App\Models\ReportPermission::ACCESS_NONE) {
            abort(403, 'Unauthorized access to Committee Performance report.');
        }

        $committees = Committee::withCount(['users', 'sessions', 'tasks']) // Basic counts needed
            ->with(['users.attendanceRecords', 'tasks.submissions']) // Eager load for aggregation
            ->get();

        // Filter Scope
        if ($accessLevel === \App\Models\ReportPermission::ACCESS_OWN) {
            $authorizedIds = Auth::user()->authorizedCommittees->pluck('id')->toArray();
            $committees = $committees->whereIn('id', $authorizedIds);
        }

        // Calculate Stats
        $performance = $committees->map(function ($committee) {
            $totalMembers = $committee->users_count ?: 1; // Avoid divide by zero
            $totalSessions = $committee->sessions_count;

            // Avg Attendance Rate
            // Total possible attendance = sessions * members
            // Actual attendance = count of all attendance records for sessions of this committee
            // Simplification: We iterate users and check their attendance
            $totalPossibleAttendance = $totalMembers * $totalSessions;
            $actualAttendance = 0;

            // We need a more efficient way or mapped way. 
            // Let's use the loaded users.
            foreach ($committee->users as $user) {
                $actualAttendance += $user->attendanceRecords->whereIn('attendance_session_id', $committee->sessions->pluck('id'))->count();
            }

            $attendanceRate = $totalPossibleAttendance > 0 ? round(($actualAttendance / $totalPossibleAttendance) * 100) : 0;

            // Task Completion Rate
            $totalTasks = $committee->tasks_count;
            $totalPossibleSubmissions = $totalTasks * $totalMembers;
            $actualSubmissions = 0;

            foreach ($committee->tasks as $task) {
                $actualSubmissions += $task->submissions->count();
            }

            $taskRate = $totalPossibleSubmissions > 0 ? round(($actualSubmissions / $totalPossibleSubmissions) * 100) : 0;

            return [
                'name' => $committee->name,
                'members' => $totalMembers,
                'attendance_rate' => $attendanceRate,
                'task_rate' => $taskRate
            ];
        });

        return view('Heads.Reports.committee_performance', compact('performance'));
    }

    public function sessionQuality(Request $request)
    {
        // Top Management & Committee Head (for their sessions)
        if (!\App\Models\ReportPermission::check('session_quality', Auth::user()->role)) {
            abort(403, 'Restricted access.');
        }

        $query = \App\Models\AttendanceSession::query();

        if (Auth::user()->hasRole('committee_head')) {
            $committeeIds = Auth::user()->authorizedCommittees->pluck('id');
            $query->whereIn('committee_id', $committeeIds);
            // If filter is applied, ensure it's one of theirs
            if ($request->filled('committee_id') && !$committeeIds->contains($request->committee_id)) {
                abort(403, 'Unauthorized committee.');
            }
        }

        // Filter: Committee
        if ($request->filled('committee_id')) {
            $query->where('committee_id', $request->committee_id);
        }

        // Logic: Fetch sessions with feedback, calc average ratings
        $allSessions = $query->with('feedbacks', 'committee')
            ->withCount('feedbacks')
            ->get()
            ->map(function ($session) {
                if ($session->feedbacks_count == 0) return null;

                $avgRating = $session->feedbacks->avg(function ($f) {
                    return ($f->session_rating + $f->instructor_rating) / 2;
                });

                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'committee' => $session->committee->name ?? 'General',
                    'date' => $session->created_at->format('M d, Y'),
                    'avg_rating' => round($avgRating, 1),
                    'feedback_count' => $session->feedbacks_count
                ];
            })
            ->filter()
            ->sortByDesc('avg_rating')
            ->values();

        // Manual Pagination for Collection
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $allSessions->slice($offset, $perPage),
            $allSessions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $committees = Committee::orderBy('name')->get(); // For filter

        return view('Heads.Reports.session_quality', ['sessions' => $paginatedItems, 'committees' => $committees]);
    }

    public function showSessionFeedback(\App\Models\AttendanceSession $session)
    {
        $user = Auth::user();

        // 1. Role Check: Only Top Management, Board, and Head can view the LIST
        if (!in_array($user->role, ['top_management', 'board', 'committee_head', 'vice_head'])) {
            abort(403, 'Unauthorized. HR and Members cannot view feedback lists.');
        }

        // 2. Authorization Scope Check
        if ($user->hasRole('top_management')) {
            // Can view anything
        } elseif ($user->hasRole('board') || $user->hasRole('committee_head') || $user->hasRole('vice_head')) {
            // Must be authorized for this committee
            if (!$user->authorizedCommittees->contains($session->committee_id)) {
                abort(403, 'Unauthorized. You do not have access to this committee\'s feedback.');
            }
        }

        // 3. Fetch Feedback
        $feedbacks = $session->feedbacks()->with('user')->get();

        // 4. Anonymity Logic
        // Top Management -> See Names
        // Board/Head -> Anonymous
        $shouldAnonymize = !$user->hasRole('top_management');

        if ($shouldAnonymize) {
            $feedbacks->transform(function ($feedback) {
                unset($feedback->user); // Remove relation
                $feedback->user_name = 'Anonymous Member'; // Placeholder
                return $feedback;
            });
        } else {
            $feedbacks->transform(function ($feedback) {
                $feedback->user_name = $feedback->user->name ?? 'Unknown';
                return $feedback;
            });
        }

        return view('Heads.Reports.feedback_details', compact('session', 'feedbacks', 'shouldAnonymize'));
    }

    public function attendanceTrends()
    {
        // Top Management & Committee Head
        if (!\App\Models\ReportPermission::check('attendance_trends', Auth::user()->role)) {
            abort(403, 'Restricted access.');
        }

        $query = \App\Models\AttendanceSession::latest();

        if (Auth::user()->hasRole('committee_head')) {
            $committeeIds = Auth::user()->authorizedCommittees->pluck('id');
            $query->whereIn('committee_id', $committeeIds);
        }

        // Logic: Get last 10 sessions, calc attendance % for each
        $sessions = $query->take(10)->get()->reverse();

        $trends = $sessions->map(function ($session) {
            // Total Active Members (Approximation: All users with 'member' role at time of session? 
            // Simplification: Current count of 'member' role users)
            // Better: Count of distinct users who CAN have attended (e.g. committee members + unassigned?)
            // Use committee members count if committee session, else all members.

            if ($session->committee_id) {
                $totalMembers = $session->committee->users()->count();
            } else {
                $totalMembers = User::where('role', 'member')->count();
            }

            $attended = $session->records()->count();
            $rate = $totalMembers > 0 ? round(($attended / $totalMembers) * 100) : 0;

            return [
                'label' => $session->title . ' (' . $session->created_at->format('M d') . ')',
                'rate' => $rate
            ];
        });

        return view('Heads.Reports.attendance_trends', compact('trends'));
    }

    public function exportCommittees()
    {
        $committees = $this->getCommitteesData();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CommitteeAttendanceExport($committees), 'committee_attendance.xlsx');
    }

    public function member(Request $request)
    {
        // RESTRICTION: Top Management, Board, HR, Committee Head (for their members)
        if (!\App\Models\ReportPermission::check('member', Auth::user()->role)) {
            abort(403, 'Unauthorized. This report is restricted.');
        }

        $members = $this->getMembersData($request);
        return view('Top Management.Reports.member', compact('members'));
    }

    public function exportMembers(Request $request)
    {
        $members = $this->getMembersData($request);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MemberAttendanceExport($members), 'member_attendance.xlsx');
    }

    private function getCommitteesData()
    {
        $user = Auth::user();
        $accessLevel = \App\Models\ReportPermission::getAccessLevel('committees', $user->role);

        if ($accessLevel === \App\Models\ReportPermission::ACCESS_NONE) {
            abort(403, 'Unauthorized action.');
        }

        if ($accessLevel === \App\Models\ReportPermission::ACCESS_OWN) {
            // Fetch committees assigned to User (Head/HR/Member restricted)
            $committees = $user->authorizedCommittees()->with(['users.attendanceRecords'])->get();

            if ($committees->isEmpty()) {
                // If scope is OWN but they have no committees, show empty or abort?
                // Abort is safer to indicate "Access granted but no data found for you"
                // But returning empty collection is more robust for UI.
                // Let's return empty collection to accept "Authorized but no assignments"
            }
        } else {
            // ACCESS_GLOBAL: Top Management, Board (usually), or unrestricted HR
            $committees = Committee::with(['users.attendanceRecords'])->get();
        }

        // Privacy Filter: Hide Top Management, Board, and HR from everyone except Top Management
        if (!$user->hasRole('top_management')) {
            $committees->each(function ($committee) use ($user) {
                $filteredUsers = $committee->users->filter(function ($member) use ($user) {
                    $hiddenRoles = ['top_management', 'board', 'hr'];
                    if ($user->hasRole('board')) {
                        $hiddenRoles = ['top_management', 'board']; // Board encounters HR
                    }
                    return !in_array($member->role, $hiddenRoles);
                })->values(); // Reset indices
                $committee->setRelation('users', $filteredUsers);
            });
        }

        return $committees;
    }

    private function getMembersData(Request $request)
    {
        $user = Auth::user();
        $query = User::query();

        // HR Restriction: Must be assigned to a committee
        // HR Restriction: Must be assigned to a committee (UPDATED: HR has Global View)
        if ($user->hasRole('committee_head')) {
            $committeeIds = $user->authorizedCommittees->pluck('id');
            if ($committeeIds->isEmpty()) abort(403, 'No assigned committees.');
            $query->whereHas('committees', function ($q) use ($committeeIds) {
                $q->whereIn('committees.id', $committeeIds);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search) // Exact ID match often preferred for ID search
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
            $members = $query->with('attendanceRecords.session')->get();
        } else {
            // Return empty collection if no search
            $members = collect([]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1);
        }

        // Manual Privacy Filter: Hide Top Management, Board, AND HR from search results unless the user is Top Management
        if (!$user->hasRole('top_management')) {
            $members = $members->filter(function ($member) use ($user) {
                $hiddenRoles = ['top_management', 'board', 'hr'];
                if ($user->hasRole('board')) {
                    $hiddenRoles = ['top_management', 'board'];
                }
                return !in_array($member->role, $hiddenRoles);
            })->values();
        }

        // Manual Pagination
        $page = $request->input('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $itemsForCurrentPage = $members->slice($offset, $perPage)->all();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $members->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
