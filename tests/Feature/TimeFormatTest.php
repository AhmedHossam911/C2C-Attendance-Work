<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_time_is_displayed_in_12_hour_format()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $session = AttendanceSession::factory()->create();

        // Create a record scanned at 2:30 PM (14:30)
        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $user->id,
            'scanned_at' => now()->setTime(14, 30, 0),
            'status' => 'present',
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('sessions.show', $session));

        // Assert
        $response->assertStatus(200);
        // Should contain '02:30:00 PM'
        $response->assertSee('02:30:00 PM');
    }
}
