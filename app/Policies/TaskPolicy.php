<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Models\Committee;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Helper to check if a committee is the HR committee.
     * HR committee is identified by name = 'HR' or if board user is authorized for it.
     */
    private function isHrCommittee($committeeId): bool
    {
        return Committee::where('id', $committeeId)
            ->where('name', 'HR')
            ->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can potentially view tasks (filtered by their access)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Top Management: Global read-only access to ALL tasks
        if ($user->role === 'top_management') {
            return true;
        }

        // HR Board: View all tasks across all committees (read-only for non-HR)
        if ($user->role === 'board') {
            return true;
        }

        // HR Member: Member-like access to own committee + read-only access to authorized committees
        if ($user->role === 'hr') {
            // If they are a member of this committee (HR committee), allow access like a member
            if ($user->committees()->where('committees.id', $task->committee_id)->exists()) {
                return true;
            }
            // Otherwise, check if they have explicit read-only authorization
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Committee Head: View tasks of their authorized committees only
        if ($user->role === 'committee_head') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Member: View tasks of committees they belong to only
        if ($user->role === 'member') {
            return $user->committees()->where('committees.id', $task->committee_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only CommitteeHead and HR Board can create tasks
        return in_array($user->role, ['committee_head', 'board']);
    }

    /**
     * Determine whether the user can create a task for a specific committee.
     * Custom check to be called from Controller.
     */
    public function createForCommittee(User $user, $committeeId): bool
    {
        // Committee Head can create tasks for their authorized committees
        if ($user->role === 'committee_head' && $user->authorizedCommittees->contains($committeeId)) {
            return true;
        }

        // HR Board can only create tasks for Authorized committees
        if ($user->role === 'board') {
            return $user->authorizedCommittees->contains($committeeId);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // HR Member, Top Management are Read-Only (HR is allowed for authorized below? NO, View Only)
        if (in_array($user->role, ['hr', 'top_management'])) {
            return false;
        }

        // Board can only update authorized committee tasks
        if ($user->role === 'board') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        return $this->createForCommittee($user, $task->committee_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Same rules as update - maintain consistency
        return $this->update($user, $task);
    }

    /**
     * Determine whether the user can review submissions for the task.
     */
    public function review(User $user, Task $task): bool
    {
        // Top Management: Read-only, CANNOT review tasks
        if ($user->role === 'top_management') {
            return false;
        }

        // HR Member: Read-only, CANNOT review tasks
        if ($user->role === 'hr') {
            return false;
        }

        // HR Board: Can review submissions for HR committee only (authorized committees)
        if ($user->role === 'board') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Committee Head: Can review submissions for their authorized committees
        if ($user->role === 'committee_head') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Members cannot review
        return false;
    }

    /**
     * Determine whether the user can submit work for the task.
     */
    public function submit(User $user, Task $task): bool
    {
        // HR Board, Top Management, and Committee Head should NEVER submit tasks
        if (in_array($user->role, ['board', 'top_management', 'committee_head'])) {
            return false;
        }

        // HR Member: Can submit only for their own committee (HR committee)
        if ($user->role === 'hr') {
            return $user->committees()->where('committees.id', $task->committee_id)->exists();
        }

        // Member: Can submit only if they belong to the committee (membership only)
        if ($user->role === 'member') {
            return $user->committees()->where('committees.id', $task->committee_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view a specific submission.
     */
    public function viewSubmission(User $user, Task $task, TaskSubmission $submission): bool
    {
        // Top Management: Can view all submissions (read-only)
        if ($user->role === 'top_management') {
            return true;
        }

        // HR Board: Can view all submissions across all committees
        if ($user->role === 'board') {
            return true;
        }

        // HR Member: Authorized (Manager) OR Own (Participant)
        if ($user->role === 'hr') {
            return $user->authorizedCommittees->contains($task->committee_id) || $submission->user_id === $user->id;
        }

        // Committee Head: Can view all submissions of their authorized committees
        if ($user->role === 'committee_head') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Member: Can only view their OWN submission
        if ($user->role === 'member') {
            return $submission->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view all submissions for a task (review list).
     */
    public function viewAllSubmissions(User $user, Task $task): bool
    {
        // Top Management: Can view all submissions (read-only)
        if ($user->role === 'top_management') {
            return true;
        }

        // HR Board: Can view all submissions across ALL committees (read-only for non-HR)
        if ($user->role === 'board') {
            return true;
        }

        // HR Member: Can view submissions for authorized committees (read-only)
        if ($user->role === 'hr') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Committee Head: Can view all submissions for their authorized committees
        if ($user->role === 'committee_head') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Members cannot view all submissions (only their own via viewSubmission)
        return false;
    }

    /**
     * Determine whether the user can view feedback on a submission.
     */
    public function viewFeedback(User $user, Task $task, TaskSubmission $submission): bool
    {
        // Top Management: Can view all feedback (read-only)
        if ($user->role === 'top_management') {
            return true;
        }

        // HR Board: Can view all feedback
        if ($user->role === 'board') {
            return true;
        }

        // HR Member: Authorized (Manager) OR Own (Participant)
        if ($user->role === 'hr') {
            return $user->authorizedCommittees->contains($task->committee_id) || $submission->user_id === $user->id;
        }

        // Committee Head: Can view feedback for their authorized committees
        if ($user->role === 'committee_head') {
            return $user->authorizedCommittees->contains($task->committee_id);
        }

        // Member: Can only view feedback on their OWN submission
        if ($user->role === 'member') {
            return $submission->user_id === $user->id;
        }

        return false;
    }
}
