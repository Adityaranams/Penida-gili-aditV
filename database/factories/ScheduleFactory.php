<?php

namespace Database\Factories;

use App\Enums\ListingStatus;
use App\Models\BoatOperator;
use App\Models\Port;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Schedule> */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $hour = fake()->numberBetween(7, 16);

        return [
            'boat_operator_id' => BoatOperator::factory(),
            'vessel_id' => null,
            'from_port_id' => Port::factory(),
            'to_port_id' => Port::factory(),
            'departure_time' => sprintf('%02d:00', $hour),
            'arrival_time' => sprintf('%02d:45', $hour),
            'price_adult' => 180_000,
            'price_child' => 135_000,
            'price_foreign' => null,
            'price_foreign_child' => null,
            'days' => null,
            'status' => ListingStatus::Active,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => ListingStatus::Draft]);
    }
}
