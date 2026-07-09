<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => \App\Models\User::factory(),
        'date' => $this->faker->unique()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
        'status' => '退勤済',
    ];
    }
}
