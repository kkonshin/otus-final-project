<?php

namespace App\Containers\BookingContainer\Repositories;

use App\Containers\BookingContainer\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    /**
     * @return Collection
     */
    public function getAll(): Collection {
        return Booking::all();
    }
}
