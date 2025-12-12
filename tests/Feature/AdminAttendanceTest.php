<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Committee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_attendance_status_and_notes()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $user = User::factory()->create(['status' => 'active']);
        $committee = Committee::factory()->create();

        $session = AttendanceSession::factory()->create();

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
            'status' => 'late',
            'notes' => 'Original note',
        ]);

        // Act
        $response = $this->actingAs($admin)->put(route('attendance.update', $record), [
            'status' => 'present',
            'notes' => 'Updated note',
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'present',
            'notes' => 'Updated note',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_admin_can_soft_delete_attendance_record()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $user = User::factory()->create(['status' => 'active']);
        $session = AttendanceSession::factory()->create();

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
            'status' => 'present',
        ]);

        // Act
        $response = $this->actingAs($admin)->delete(route('attendance.destroy', $record));

        // Assert
        $response->assertRedirect();

        // Check soft delete: record should stay in DB but having deleted_at set.
        // assertDatabaseHas will look for non-deleted records by default if SoftDeletes is on? 
        // Actually, assertSoftDeleted is better helpers.
        $this->assertSoftDeleted('attendance_records', [
            'id' => $record->id,
        ]);
    }

    public function test_hr_can_update_notes_but_not_status()
    {
        // Arrange
        $hrContext = User::factory()->create(['role' => 'hr', 'status' => 'active']);
        $user = User::factory()->create(['status' => 'active']);
        $session = AttendanceSession::factory()->create();

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $hrContext->id,
            'scanned_at' => now(),
            'status' => 'late',
            'notes' => 'Original note',
        ]);

        // Act
        // Attempt to update Status AND Notes
        $response = $this->actingAs($hrContext)->put(route('attendance.update', $record), [
            'status' => 'present',
            'notes' => 'HR updated note',
        ]);

        // Assert
        $response->assertRedirect(); // Should be allowed now

        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'late', // Status should remain unchanged (ignored)
            'notes' => 'HR updated note', // Notes should be updated
            'updated_by' => $hrContext->id,
        ]);
    }

    public function test_board_can_update_status_and_notes()
    {
        // Arrange
        $boardMember = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $user = User::factory()->create(['status' => 'active']);
        $session = AttendanceSession::factory()->create();

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $boardMember->id,
            'scanned_at' => now(),
            'status' => 'late',
            'notes' => 'Original note',
        ]);

        // Act
        $response = $this->actingAs($boardMember)->put(route('attendance.update', $record), [
            'status' => 'present',
            'notes' => 'Board updated note',
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'status' => 'present', // Status SHOULD change
            'notes' => 'Board updated note',
            'updated_by' => $boardMember->id,
        ]);

        // Verify Scanned At did NOT change
        $this->assertEquals($record->scanned_at->toDateTimeString(), $record->fresh()->scanned_at->toDateTimeString());
    }

    public function test_scanned_at_does_not_change_on_update()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $session = AttendanceSession::factory()->create();
        $originalTime = now()->subHour()->startOfSecond(); // Specific time

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => User::factory()->create()->id,
            'scanned_by' => $admin->id,
            'scanned_at' => $originalTime,
            'status' => 'late',
            'notes' => 'Original note',
        ]);

        // Act
        $this->actingAs($admin)->put(route('attendance.update', $record), [
            'status' => 'present',
            'notes' => 'Updated note',
        ]);

        // Assert
        $record->refresh();
        $this->assertEquals($originalTime->toDateTimeString(), $record->scanned_at->toDateTimeString());
        // Verify strict equality of timestamp if needed, but string comparison is usually sufficient for DB roundtrip
    }

    public function test_search_attendance_by_name()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $session = AttendanceSession::factory()->create();

        $user1 = User::factory()->create(['name' => 'Alice Wonderland']);
        $user2 = User::factory()->create(['name' => 'Bob Marley']);

        AttendanceRecord::create(['attendance_session_id' => $session->id, 'user_id' => $user1->id, 'scanned_by' => $admin->id, 'scanned_at' => now(), 'status' => 'present']);
        AttendanceRecord::create(['attendance_session_id' => $session->id, 'user_id' => $user2->id, 'scanned_by' => $admin->id, 'scanned_at' => now(), 'status' => 'present']);

        // Act
        $response = $this->actingAs($admin)->get(route('sessions.show', ['session' => $session, 'search' => 'Alice']));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('records', function ($records) use ($user1, $user2) {
            return $records->contains($user1->attendanceRecords->first())
                && !$records->contains($user2->attendanceRecords->first());
        });
    }

    public function test_non_admin_cannot_delete_attendance()
    {
        // Arrange
        $nonAdmin = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $user = User::factory()->create(['status' => 'active']);
        $session = AttendanceSession::factory()->create();

        $record = AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'user_id' => $user->id,
            'scanned_by' => $nonAdmin->id,
            'scanned_at' => now(),
            'status' => 'present',
        ]);

        // Act
        $response = $this->actingAs($nonAdmin)->delete(route('attendance.destroy', $record));

        // Assert
        $response->assertForbidden();
        // Record should remain not deleted
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_export_session()
    {
        // Arrange
        \Maatwebsite\Excel\Facades\Excel::fake();
        $admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);
        $session = AttendanceSession::factory()->create();

        // Act
        $response = $this->actingAs($admin)->get(route('sessions.export', $session));

        // Assert
        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('session_' . $session->id . '_attendance.xlsx');
    }
}
