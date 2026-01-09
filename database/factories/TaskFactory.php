<?php

namespace Database\Factories;

use App\Models\Committee;
use App\Models\User;
use App\Models\AttendanceSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'committee_id' => Committee::inRandomOrder()->first()->id ?? null,
            'session_id' => AttendanceSession::inRandomOrder()->first()->id ?? null,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['basic', 'extra']),
            'deadline' => $this->faker->dateTimeBetween('now', '+1 month'),
            'created_by' => User::whereIn('role', ['board', 'head'])->inRandomOrder()->first()->id ?? 1,
        ];
    }
}
