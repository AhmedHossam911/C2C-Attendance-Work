<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportPermission;

class ReportPermissionSeeder extends Seeder
{
    public function run()
    {
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

        // 1. Board Defaults
        // Board can see everything EXCEPT session_quality and attendance_trends (per current controller logic)
        $boardReports = ['committees', 'ghost_members', 'top_performers', 'committee_performance', 'member'];

        // 2. HR Defaults
        // HR same as Board (authorized committees context logic handles the data scoping, but permission to VIEW the page is here)
        $hrReports = ['committees', 'ghost_members', 'top_performers', 'committee_performance', 'member'];

        // 3. Head Defaults
        // Head sees everything
        $headReports = $reports; // All

        foreach ($roles as $role) {
            foreach ($reports as $key) {
                // Default: None
                $level = ReportPermission::ACCESS_NONE;

                // Board: Global Access for assigned reports
                if ($role === 'board' && in_array($key, $boardReports)) {
                    $level = ReportPermission::ACCESS_GLOBAL;
                }

                // HR: Authorized/Own Committee Access
                if ($role === 'hr' && in_array($key, $hrReports)) {
                    $level = ReportPermission::ACCESS_OWN;
                }

                // Head: Authorized/Own Committee Access
                if ($role === 'committee_head' && in_array($key, $headReports)) {
                    $level = ReportPermission::ACCESS_OWN;
                }

                ReportPermission::updateOrCreate(
                    ['report_key' => $key, 'role' => $role],
                    ['access_level' => $level]
                );
            }
        }
    }
}
