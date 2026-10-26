<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farm>
 */
class FarmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'name'      => fake()->unique()->words(2, true),
            'latitude'  => fake()->latitude(32, 37),
            'longitude' => fake()->longitude(35, 42),
            'area' => fake()->randomFloat(2, 50, 500),
            ];
    }
}
