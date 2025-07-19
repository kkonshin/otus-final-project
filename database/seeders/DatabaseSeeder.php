<?php

namespace Database\Seeders;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoomSeeder::class,
        ]);

        Booking::factory()->count(50)->create();

        Equipment::factory()->count(50)->create();
        RoomEquipment::factory()->count(50)->create();
    }
}
