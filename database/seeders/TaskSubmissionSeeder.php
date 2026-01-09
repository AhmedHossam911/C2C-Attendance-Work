<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskSubmission;
use Illuminate\Database\Seeder;

class TaskSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $members = User::where('role', 'member')->get();

        if ($tasks->isEmpty() || $members->isEmpty()) {
            return;
        }

        foreach ($tasks as $task) {
            // For each task, have random 70% of members submit it
            // Filter members who belong to the committee of the task?
            // Assuming committee members submit their own committee tasks primarily.
            // But for seeding simplicity, let's look for members ENROLLED in that committee.

            // Assuming we have committee-user relation.
            // Let's refetch members associated with the task's committee.

            $committeeMembers = $task->committee->users()->where('role', 'member')->get();

            if ($committeeMembers->isEmpty()) {
                continue;
            }

            foreach ($committeeMembers as $member) {
                // 80% submission rate
                if (rand(1, 100) <= 80) {
                    TaskSubmission::factory()->create([
                        'task_id' => $task->id,
                        'user_id' => $member->id,
                    ]);
                }
            }
        }
    }
}
