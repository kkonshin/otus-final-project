<?php

namespace App\Containers\BookingContainer\Contracts;

use App\Containers\BookingContainer\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

interface GetBookingActionContract
{
    /**
     * @return Collection<int, Booking>
     */
    public function execute(): Collection;
}
