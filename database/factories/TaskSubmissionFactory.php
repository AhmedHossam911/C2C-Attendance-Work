<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskSubmission>
 */
class TaskSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::inRandomOrder()->first()->id ?? Task::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'submission_link' => $this->faker->url(),
            'status' => $this->faker->randomElement(['pending', 'reviewed']),
            'rating' => $this->faker->optional(0.7)->numberBetween(0, 10), // 70% chance of having a rating
            'feedback' => $this->faker->optional(0.7)->sentence(),
            'is_late' => $this->faker->boolean(20), // 20% chance of being late
            'submitted_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
