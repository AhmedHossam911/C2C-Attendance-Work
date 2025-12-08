<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Committee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $committees = $this->getCommitteesData();
        return view('reports.index', compact('committees'));
    }

    public function exportCommittees()
    {
        $committees = $this->getCommitteesData();
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CommitteeAttendanceExport($committees), 'committee_attendance.xlsx');
    }

    public function member(Request $request)
    {
        $members = $this->getMembersData($request);
        return view('reports.member', compact('members'));
    }

    public function exportMembers(Request $request)
    {
        $members = $this->getMembersData($request);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MemberAttendanceExport($members), 'member_attendance.xlsx');
    }

    private function getCommitteesData()
    {
        $user = Auth::user();

        if (!$user->hasRole('top_management') && !$user->hasRole('board') && !$user->hasRole('hr')) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->hasRole('hr')) {
            // Fetch committees assigned to HR
            $committees = $user->authorizedCommittees()->with(['users.attendanceRecords'])->get();

            if ($committees->isEmpty()) {
                abort(403, 'You are not assigned to any committees.');
            }
        } else {
            // Top Management and Board fetch all
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
        if ($user->hasRole('hr')) {
            $committeeIds = $user->authorizedCommittees->pluck('id');

            if ($committeeIds->isEmpty()) {
                abort(403, 'You are not assigned to any committees.');
            }

            // Restrict to members of HR's committees
            $query->whereHas('committees', function ($q) use ($committeeIds) {
                $q->whereIn('committees.id', $committeeIds);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Execute query first
        $members = $query->with('attendanceRecords.session')->get();

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
