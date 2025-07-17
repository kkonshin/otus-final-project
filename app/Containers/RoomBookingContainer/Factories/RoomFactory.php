<?php

namespace App\Containers\RoomBookingContainer\Factories;

use App\Containers\RoomBookingContainer\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'title' => fake()->unique()->word(),
            'description' => fake()->text(),
            'capacity' => fake()->numberBetween(1, 100),
            'floor' => fake()->numberBetween(1, 25),
            'available_from' => fake()->dateTime('now'),
            'available_to' => fake()->dateTime('now'),
        ];
    }
}
