<?php

namespace Database\Seeders;

use App\Containers\BookingContainer\Models\Booking;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoomSeeder::class,
        ]);

        Booking::factory()->count(50)->create();
    }
}
