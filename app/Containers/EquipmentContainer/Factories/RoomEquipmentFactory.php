<?php

namespace App\Containers\EquipmentContainer\Factories;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\RoomBookingContainer\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class RoomEquipmentFactory extends Factory
{
    protected $model = RoomEquipment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'equipment_id' => $this->faker->randomElement(Equipment::all())['id'],
            'room_id' => $this->faker->randomElement(Room::all())['id'],
            'quantity' => rand(1, 15),
        ];
    }
}
