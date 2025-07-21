<?php

namespace App\Containers\RoomBookingContainer\Factories;

use App\Containers\RoomBookingContainer\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Containers\Core\Handbooks\TimePeriodsHandbook;
use Illuminate\Support\Carbon;

/**
 * @extends Factory
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availabilityPeriod = TimePeriodsHandbook::getRandomPeriod();

        return [
            'title' => fake()->unique()->word(),
            'description' => fake()->text(),
            'capacity' => fake()->numberBetween(1, 100),
            'floor' => fake()->numberBetween(1, 25),
            'available_from' => $availabilityPeriod[0],
            'available_to' => $availabilityPeriod[1],
        ];
    }
}
