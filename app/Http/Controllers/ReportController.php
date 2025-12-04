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

        $query = Committee::with(['users.attendanceRecords']);

        // HR Restriction: Only their committees
        if ($user->hasRole('hr')) {
            // Fetch committees assigned to HR
            $committees = $user->committees()->with(['users.attendanceRecords'])->get();

            if ($committees->isEmpty()) {
                abort(403, 'You are not assigned to any committees.');
            }

            // Manually filter users to ensure privacy (hide Top Management, Board, and HR)
            $committees->each(function ($committee) {
                $filteredUsers = $committee->users->filter(function ($member) {
                    return !in_array($member->role, ['top_management', 'board', 'hr']);
                })->values(); // Reset indices
                $committee->setRelation('users', $filteredUsers);
            });

            return $committees;
        }

        return $query->get();
    }

    private function getMembersData(Request $request)
    {
        $user = Auth::user();
        $query = User::query();

        // HR Restriction: Must be assigned to a committee
        if ($user->hasRole('hr')) {
            $committeeIds = $user->committees->pluck('id');

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

        // Manual Privacy Filter: Hide Top Management, Board, AND HR from search results unless the user is Top Management or Board
        if (!$user->hasRole('top_management') && !$user->hasRole('board')) {
            $members = $members->filter(function ($member) {
                return !in_array($member->role, ['top_management', 'board', 'hr']);
            })->values();
        }

        return $members;
    }
}
