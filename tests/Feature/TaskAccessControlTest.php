<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Committee;
use App\Models\User;
use App\Models\AttendanceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private Committee $hrCommittee;
    private Committee $techCommittee;
    private AttendanceSession $hrSession;
    private AttendanceSession $techSession;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for granting authorizations
        $this->admin = User::factory()->create(['role' => 'top_management', 'status' => 'active']);

        // Create committees
        $this->hrCommittee = Committee::factory()->create(['name' => 'HR']);
        $this->techCommittee = Committee::factory()->create(['name' => 'Tech']);

        // Create sessions
        $this->hrSession = AttendanceSession::factory()->create(['committee_id' => $this->hrCommittee->id]);
        $this->techSession = AttendanceSession::factory()->create(['committee_id' => $this->techCommittee->id]);
    }

    /**
     * Helper method to attach authorized committees with required granted_by field
     */
    private function authorizeForCommittee(User $user, Committee $committee): void
    {
        $user->authorizedCommittees()->attach($committee->id, ['granted_by' => $this->admin->id]);
    }

    // ============================================
    // MEMBER TESTS
    // ============================================

    public function test_member_can_view_own_committee_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);

        $response = $this->actingAs($member)->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_member_cannot_view_other_committee_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $hrTask = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);

        $response = $this->actingAs($member)->get(route('tasks.show', $hrTask));

        $response->assertStatus(403);
    }

    public function test_member_can_submit_own_committee_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create([
            'committee_id' => $this->techCommittee->id,
            'session_id' => $this->techSession->id,
            'deadline' => now()->addDays(7),
        ]);

        $response = $this->actingAs($member)->post(route('tasks.submit', $task), [
            'submission_link' => 'https://github.com/test/repo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_submissions', [
            'task_id' => $task->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_member_cannot_submit_other_committee_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $hrTask = Task::factory()->create([
            'committee_id' => $this->hrCommittee->id,
            'session_id' => $this->hrSession->id,
            'deadline' => now()->addDays(7),
        ]);

        $response = $this->actingAs($member)->post(route('tasks.submit', $hrTask), [
            'submission_link' => 'https://github.com/test/repo',
        ]);

        $response->assertStatus(403);
    }

    public function test_member_cannot_create_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $response = $this->actingAs($member)->get(route('tasks.create'));

        $response->assertStatus(403);
    }

    public function test_member_cannot_review_tasks(): void
    {
        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($member)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 80,
            'feedback' => 'Great work!',
        ]);

        $response->assertStatus(403);
    }

    // ============================================
    // COMMITTEE HEAD TESTS
    // ============================================

    public function test_committee_head_can_view_own_committee_tasks(): void
    {
        $head = User::factory()->create(['role' => 'committee_head', 'status' => 'active']);
        $this->authorizeForCommittee($head, $this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);

        $response = $this->actingAs($head)->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_committee_head_cannot_view_other_committee_tasks(): void
    {
        $head = User::factory()->create(['role' => 'committee_head', 'status' => 'active']);
        $this->authorizeForCommittee($head, $this->techCommittee);

        $hrTask = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);

        $response = $this->actingAs($head)->get(route('tasks.show', $hrTask));

        $response->assertStatus(403);
    }

    public function test_committee_head_can_create_tasks_for_own_committee(): void
    {
        $head = User::factory()->create(['role' => 'committee_head', 'status' => 'active']);
        $this->authorizeForCommittee($head, $this->techCommittee);

        $response = $this->actingAs($head)->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'committee_id' => $this->techCommittee->id,
            'type' => 'basic',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'session_id' => $this->techSession->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_committee_head_cannot_create_tasks_for_other_committee(): void
    {
        $head = User::factory()->create(['role' => 'committee_head', 'status' => 'active']);
        $this->authorizeForCommittee($head, $this->techCommittee);

        $response = $this->actingAs($head)->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'committee_id' => $this->hrCommittee->id,
            'type' => 'basic',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'session_id' => $this->hrSession->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_committee_head_can_review_own_committee_submissions(): void
    {
        $head = User::factory()->create(['role' => 'committee_head', 'status' => 'active']);
        $this->authorizeForCommittee($head, $this->techCommittee);

        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($head)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 85,
            'feedback' => 'Excellent work!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_submissions', [
            'id' => $submission->id,
            'status' => 'reviewed',
            'rating' => 85,
        ]);
    }

    // ============================================
    // HR BOARD TESTS
    // ============================================

    public function test_hr_board_can_view_all_tasks(): void
    {
        $board = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $this->authorizeForCommittee($board, $this->hrCommittee);

        $techTask = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $hrTask = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);

        $responseTech = $this->actingAs($board)->get(route('tasks.show', $techTask));
        $responseHr = $this->actingAs($board)->get(route('tasks.show', $hrTask));

        $responseTech->assertStatus(200);
        $responseHr->assertStatus(200);
    }

    public function test_hr_board_can_create_tasks_for_hr_committee(): void
    {
        $board = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $this->authorizeForCommittee($board, $this->hrCommittee);

        $response = $this->actingAs($board)->post(route('tasks.store'), [
            'title' => 'HR Task',
            'description' => 'HR Description',
            'committee_id' => $this->hrCommittee->id,
            'type' => 'basic',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'session_id' => $this->hrSession->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'HR Task']);
    }

    public function test_hr_board_cannot_create_tasks_for_other_committee(): void
    {
        $board = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $this->authorizeForCommittee($board, $this->hrCommittee);

        $response = $this->actingAs($board)->post(route('tasks.store'), [
            'title' => 'Tech Task',
            'description' => 'Tech Description',
            'committee_id' => $this->techCommittee->id,
            'type' => 'basic',
            'deadline' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'session_id' => $this->techSession->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_hr_board_can_review_hr_committee_submissions(): void
    {
        $board = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $this->authorizeForCommittee($board, $this->hrCommittee);

        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->hrCommittee);

        $task = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($board)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 90,
            'feedback' => 'Great job!',
        ]);

        $response->assertRedirect();
    }

    public function test_hr_board_cannot_review_other_committee_submissions(): void
    {
        $board = User::factory()->create(['role' => 'board', 'status' => 'active']);
        $this->authorizeForCommittee($board, $this->hrCommittee);

        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($board)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 90,
            'feedback' => 'Great job!',
        ]);

        $response->assertStatus(403);
    }

    // ============================================
    // HR MEMBER TESTS
    // ============================================

    public function test_hr_member_can_view_authorized_committee_tasks(): void
    {
        $hrMember = User::factory()->create(['role' => 'hr', 'status' => 'active']);
        $hrMember->committees()->attach($this->hrCommittee); // Member of HR
        $this->authorizeForCommittee($hrMember, $this->techCommittee); // Authorized to view Tech

        $techTask = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);

        $response = $this->actingAs($hrMember)->get(route('tasks.show', $techTask));

        $response->assertStatus(200);
    }

    public function test_hr_member_can_view_hr_committee_tasks(): void
    {
        $hrMember = User::factory()->create(['role' => 'hr', 'status' => 'active']);
        $hrMember->committees()->attach($this->hrCommittee); // Member of HR

        $hrTask = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);

        $response = $this->actingAs($hrMember)->get(route('tasks.show', $hrTask));

        $response->assertStatus(200);
    }

    public function test_hr_member_cannot_submit_tasks(): void
    {
        $hrMember = User::factory()->create(['role' => 'hr', 'status' => 'active']);
        $hrMember->committees()->attach($this->hrCommittee);
        $this->authorizeForCommittee($hrMember, $this->techCommittee);

        $techTask = Task::factory()->create([
            'committee_id' => $this->techCommittee->id,
            'session_id' => $this->techSession->id,
            'deadline' => now()->addDays(7),
        ]);

        $response = $this->actingAs($hrMember)->post(route('tasks.submit', $techTask), [
            'submission_link' => 'https://github.com/test/repo',
        ]);

        $response->assertStatus(403);
    }

    public function test_hr_member_cannot_review_tasks(): void
    {
        $hrMember = User::factory()->create(['role' => 'hr', 'status' => 'active']);
        $hrMember->committees()->attach($this->hrCommittee);
        $this->authorizeForCommittee($hrMember, $this->techCommittee);

        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($hrMember)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 80,
            'feedback' => 'Good work!',
        ]);

        $response->assertStatus(403);
    }

    // ============================================
    // TOP MANAGEMENT TESTS
    // ============================================

    public function test_top_management_can_view_all_tasks(): void
    {
        $topManagement = User::factory()->create(['role' => 'top_management', 'status' => 'active']);

        $hrTask = Task::factory()->create(['committee_id' => $this->hrCommittee->id, 'session_id' => $this->hrSession->id]);
        $techTask = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);

        $responseHr = $this->actingAs($topManagement)->get(route('tasks.show', $hrTask));
        $responseTech = $this->actingAs($topManagement)->get(route('tasks.show', $techTask));

        $responseHr->assertStatus(200);
        $responseTech->assertStatus(200);
    }

    public function test_top_management_cannot_create_tasks(): void
    {
        $topManagement = User::factory()->create(['role' => 'top_management', 'status' => 'active']);

        $response = $this->actingAs($topManagement)->get(route('tasks.create'));

        $response->assertStatus(403);
    }

    public function test_top_management_cannot_submit_tasks(): void
    {
        $topManagement = User::factory()->create(['role' => 'top_management', 'status' => 'active']);

        $task = Task::factory()->create([
            'committee_id' => $this->techCommittee->id,
            'session_id' => $this->techSession->id,
            'deadline' => now()->addDays(7),
        ]);

        $response = $this->actingAs($topManagement)->post(route('tasks.submit', $task), [
            'submission_link' => 'https://github.com/test/repo',
        ]);

        $response->assertStatus(403);
    }

    public function test_top_management_cannot_review_tasks(): void
    {
        $topManagement = User::factory()->create(['role' => 'top_management', 'status' => 'active']);

        $member = User::factory()->create(['role' => 'member', 'status' => 'active']);
        $member->committees()->attach($this->techCommittee);

        $task = Task::factory()->create(['committee_id' => $this->techCommittee->id, 'session_id' => $this->techSession->id]);
        $submission = TaskSubmission::factory()->create(['task_id' => $task->id, 'user_id' => $member->id]);

        $response = $this->actingAs($topManagement)->patch(route('submissions.update', $submission), [
            'status' => 'reviewed',
            'rating' => 85,
            'feedback' => 'Good work!',
        ]);

        $response->assertStatus(403);
    }
}
