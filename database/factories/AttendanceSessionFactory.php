<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'status' => 'open',
            'created_by' => \App\Models\User::factory(),
            'committee_id' => \App\Models\Committee::factory(),
            'late_threshold_minutes' => 15,
            'counts_for_attendance' => true,
        ];
    }
}
