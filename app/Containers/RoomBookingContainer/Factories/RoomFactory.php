<?php

namespace App\Containers\RoomBookingContainer\Factories;

use App\Containers\RoomBookingContainer\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Containers\Core\Handbooks\TimePeriodsHandbook;

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

        $title = fake()->unique()->randomNumber(3, true);

        return [
            'title' => $title,
            'description' => fake()->text(100),
            'capacity' => fake()->numberBetween(1, 100),
            'floor' => substr($title, 0, 1),
            'available_from' => $availabilityPeriod[0],
            'available_to' => $availabilityPeriod[1],
        ];
    }
}
