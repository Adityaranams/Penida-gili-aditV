<?php

namespace Database\Factories;

use App\Models\BoatOperator;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BoatOperator> */
class BoatOperatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Fast Boat',
            'description' => fake()->sentence(12),
            'tagline' => fake()->sentence(10),
            'rating' => fake()->randomFloat(1, 4.0, 5.0),
            'review_count' => fake()->numberBetween(10, 300),
            'image' => null,
            'hero_image' => 'hero-boat-detail.png',
            'top_speed_knots' => fake()->numberBetween(25, 40),
            'capacity' => fake()->numberBetween(60, 150),
            'facilities' => [
                ['icon' => 'air-conditioning.svg', 'label' => 'Air Conditioning'],
                ['icon' => 'life-jackets.svg', 'label' => 'Life Jackets'],
            ],
            'gallery' => [],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
