<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SessionFeedback>
 */
class SessionFeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::inRandomOrder()->first()->id ?? AttendanceSession::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'feedback' => $this->faker->optional()->paragraph(),
            'objectives_clarity' => $this->faker->numberBetween(1, 5),
            'instructor_understanding' => $this->faker->numberBetween(1, 5),
            'overall_satisfaction' => $this->faker->numberBetween(1, 10),
            'room_suitability' => $this->faker->randomElement(['Definitely Yes', 'Mostly Yes', 'Mostly No', 'Definitely No']),
            'attendance_system_rating' => $this->faker->numberBetween(1, 10),
            'attendance_system_suggestions' => $this->faker->optional()->sentence(),
            'future_suggestions' => $this->faker->optional()->sentence(),
        ];
    }
}
