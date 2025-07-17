<?php

namespace App\Containers\BookingContainer\Contracts;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;

interface CreateBookingActionContract
{
    /**
     * @param CreateBookingsRequestData $data
     * @return Booking
     */
    public function execute(CreateBookingsRequestData $data): Booking;
}
