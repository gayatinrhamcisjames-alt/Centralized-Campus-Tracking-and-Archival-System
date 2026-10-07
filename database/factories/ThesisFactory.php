<?php

namespace Database\Factories;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thesis>
 */
class ThesisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'abstract' => fake()->paragraph(),
            'keywords' => fake()->words(3, true),
            'program' => fake()->randomElement(['BS Information Technology', 'BS Computer Science', 'BA Communication']),
            'department' => fake()->randomElement(['Computing Studies', 'Arts and Sciences']),
            'academic_year' => '2025-2026',
            'school_year' => '2025-2026',
            'student_id' => User::factory()->create(['role' => User::STUDENT]),
            'adviser_id' => User::factory()->create(['role' => User::STAFF]),
            'status' => Thesis::DRAFT,
        ];
    }
}
