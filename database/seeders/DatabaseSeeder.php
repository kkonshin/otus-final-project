<?php

namespace Database\Seeders;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\UserContainer\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoomSeeder::class,
        ]);

        User::factory()->count(50)->create();

        Booking::factory()->count(50)->create();
    }
}
