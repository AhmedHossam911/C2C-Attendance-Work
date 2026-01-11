<?php

namespace App\Http\Controllers;

use App\Models\ReportPermission;
use Illuminate\Http\Request;

class ReportPermissionController extends Controller
{
    public function index()
    {
        // Fetch all permissions
        $permissions = ReportPermission::all();

        $roles = ['board', 'hr', 'committee_head', 'member'];
        $reports = [
            'committees' => 'Committees Report',
            'ghost_members' => 'Ghost Members',
            'top_performers' => 'Top Performers',
            'committee_performance' => 'Committee Performance',
            'session_quality' => 'Session Quality',
            'attendance_trends' => 'Attendance Trends',
            'member' => 'Individual Member Report'
        ];

        $matrix = [];
        foreach ($reports as $key => $label) {
            foreach ($roles as $role) {
                // Default: None
                $matrix[$key][$role] = ReportPermission::ACCESS_NONE;
            }
        }

        foreach ($permissions as $p) {
            $matrix[$p->report_key][$p->role] = $p->access_level;
        }

        return view('Top Management.ReportPermissions.index', compact('matrix', 'roles', 'reports'));
    }

    public function update(Request $request)
    {
        // Data: permissions[report_key][role] = 'none' | 'own' | 'global'
        $roles = ['board', 'hr', 'committee_head', 'member'];
        $reports = [
            'committees',
            'ghost_members',
            'top_performers',
            'committee_performance',
            'session_quality',
            'attendance_trends',
            'member'
        ];

        $input = $request->input('permissions', []);

        foreach ($reports as $reportKey) {
            foreach ($roles as $role) {
                // Default to NONE if key missing
                $level = $input[$reportKey][$role] ?? ReportPermission::ACCESS_NONE;

                ReportPermission::updateOrCreate(
                    ['report_key' => $reportKey, 'role' => $role],
                    ['access_level' => $level]
                );
            }
        }

        return back()->with('success', 'Permissions updated successfully.');
    }
}
