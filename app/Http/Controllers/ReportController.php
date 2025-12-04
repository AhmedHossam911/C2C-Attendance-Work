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
        $user = Auth::user();
        $committees = collect();

        if ($user->hasRole('top_management') || $user->hasRole('board')) {
            $committees = Committee::with(['users.attendanceRecords'])->get();
        } elseif ($user->hasRole('hr')) {
            // HR sees committees they are assigned to? Or all? Requirement says "assigned to".
            // Assuming HR is assigned to committees via the pivot table.
            $committees = $user->committees()->with(['users.attendanceRecords'])->get();
        }

        return view('reports.index', compact('committees'));
    }

    public function member(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $members = $query->with('attendanceRecords.session')->get();

        return view('reports.member', compact('members'));
    }
}
