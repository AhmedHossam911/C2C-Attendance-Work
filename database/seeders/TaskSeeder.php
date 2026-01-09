<?php

namespace Database\Seeders;

use App\Models\Committee;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all committees
        $committees = Committee::all();
        // Get all admin or board users to set as creators
        $creators = User::whereIn('role', ['top_management', 'board', 'hr', 'committee_head'])->get();

        if ($committees->isEmpty() || $creators->isEmpty()) {
            return;
        }

        foreach ($committees as $committee) {
            // Create 5 basic tasks for each committee
            Task::factory()->count(5)->create([
                'committee_id' => $committee->id,
                'created_by' => $creators->random()->id,
                'type' => 'basic',
            ]);

            // Create 2 extra tasks
            Task::factory()->count(2)->create([
                'committee_id' => $committee->id,
                'created_by' => $creators->random()->id,
                'type' => 'extra',
            ]);
        }
    }
}
