<?php

namespace App\Containers\BookingContainer\Factories;

use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\Core\Handbooks\TimePeriodsHandbook;
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
        $bookingPeriod = TimePeriodsHandbook::getRandomPeriod();

        return [
            'user_id' => $this->faker->randomElement(User::all())['id'],
            'room_id' => $this->faker->randomElement(Room::all())['id'],
            'status' => Status::values()[random_int(0, 2)],
            'start_at' => $bookingPeriod[0],
            'end_at' => $bookingPeriod[1],
        ];
    }
}
