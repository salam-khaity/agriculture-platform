<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farm_id'   => Farm::factory(),
            'name'      => fake()->unique()->words(2, true),
            'area'      => fake()->randomFloat(2, 1, 10),
            'soil_type' => fake()->randomElement(['clay', 'sandy', 'loamy', 'silty', 'rocky']),
        ];
    }
}
