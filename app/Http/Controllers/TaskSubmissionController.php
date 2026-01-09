<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskSubmissionController extends Controller
{
    public function store(Request $request, Task $task)
    {
        // Member can submit? 
        // We assume 'view' policy is enough to see the task, but need to check if they can submit?
        // Usually if they are in the committee (view access), they can submit.
        if (Auth::user()->cannot('submit', $task)) {
            abort(403, 'You are not authorized to submit this task.');
        }

        $request->validate([
            'submission_link' => 'required|url',
            'note' => 'nullable|string|max:1000',
        ]);

        $isLate = Carbon::now()->greaterThan($task->deadline);

        // Check existing
        $submission = $task->submissionFor(Auth::id());
        if ($submission) {
            // Update existing
            $submission->update([
                'submission_link' => $request->submission_link,
                'note' => $request->note,
                'is_late' => $isLate ? true : $submission->is_late, // If specific update happens late, does it mark generic late? Yes.
                'submitted_at' => Carbon::now(),
            ]);
            $message = 'Submission updated.';
        } else {
            // Create New
            $submission = TaskSubmission::create([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'submission_link' => $request->submission_link,
                'note' => $request->note,
                'is_late' => $isLate,
                'status' => 'pending',
                'submitted_at' => Carbon::now(),
            ]);
            $message = 'Task submitted successfully.';
        }

        if ($isLate) {
            $message .= ' (Marked as Late)';
            // Notify task creator (CommitteeHead/Board) about late submission
            if ($task->creator) {
                $task->creator->notify(new \App\Notifications\LateSubmissionNotification($submission));
            }
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, TaskSubmission $submission)
    {
        $task = $submission->task;

        // Check permission: User must be able to review the task
        if (Auth::user()->cannot('review', $task)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed',
            'rating' => 'nullable|integer|min:0|max:100', // Assuming 0-100 scale
            'feedback' => 'nullable|string',
        ]);

        $submission->update($validated);

        // Notify the user that their submission was reviewed
        if ($validated['status'] === 'reviewed') {
            $submission->user->notify(new \App\Notifications\TaskReviewedNotification($submission));
        }

        return back()->with('success', 'Submission reviewed.');
    }
}
