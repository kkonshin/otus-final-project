<?php

namespace App\Containers\BookingContainer\Repositories;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    /**
     * @return Collection
     */
    public function getAll(): Collection {
        return Booking::all();
    }

    /**
     * @param CreateBookingsRequestData $data
     * @return Booking
     */
    public function create(CreateBookingsRequestData $data): Booking {
        return Booking::query()->create([
            'user_id' => $data->userId,
            'status' => $data->status,
            'start_at' => $data->startAt,
            'end_at' => $data->endAt,
        ]);
    }
}
