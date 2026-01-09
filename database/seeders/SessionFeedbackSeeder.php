<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\SessionFeedback;
use App\Models\User;
use Illuminate\Database\Seeder;

class SessionFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sessions that count for attendance
        $sessions = AttendanceSession::where('counts_for_attendance', true)->get();

        if ($sessions->isEmpty()) {
            return;
        }

        foreach ($sessions as $session) {
            // Get users who "attended" this session.
            // Since we don't have attendance records seeded yet, let's just pick random committee members if it's a committee session
            // or random users if it's general.

            if ($session->committee_id) {
                $potentialAttendees = User::whereHas('committees', function ($q) use ($session) {
                    $q->where('committees.id', $session->committee_id);
                })->get();
            } else {
                $potentialAttendees = User::where('role', 'member')->get();
            }

            if ($potentialAttendees->isEmpty()) {
                continue;
            }

            foreach ($potentialAttendees as $attendee) {
                // 60% chance they give feedback
                if (rand(1, 100) <= 60) {
                    SessionFeedback::factory()->create([
                        'attendance_session_id' => $session->id,
                        'user_id' => $attendee->id,
                    ]);
                }
            }
        }
    }
}
