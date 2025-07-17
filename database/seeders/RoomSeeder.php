<?php

namespace Database\Seeders;

use App\Containers\RoomBookingContainer\Models\Room;

use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Room::factory()
            ->count(50)
            ->create();
    }
}
