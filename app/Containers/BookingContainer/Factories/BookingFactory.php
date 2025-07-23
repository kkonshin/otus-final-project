<?php

namespace App\Containers\BookingContainer\Factories;

use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\UserContainer\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        $randDays = random_int(1, 3);
        $randHours = random_int(1, 4);

        return [
            'user_id' => $this->faker->randomElement(User::all())['id'],
            'room_id' => $this->faker->randomElement(Room::all())['id'],
            'status' => Status::values()[random_int(0, 2)],
            'start_at' => now()->addDays($randDays),
            'end_at' => now()->addDays($randDays)->addHours($randHours),
        ];
    }
}
