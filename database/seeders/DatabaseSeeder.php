<?php

namespace Database\Seeders;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\UserContainer\Models\User;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Room::factory()
            ->count(10)
            ->create();

        User::factory()
            ->count(20)
            ->create();

        Booking::factory()
            ->count(20)
            ->create();

        Equipment::factory()
            ->count(20)
            ->create();

        RoomEquipment::factory()
            ->count(20)
            ->create();
    }
}
