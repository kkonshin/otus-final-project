<?php

namespace App\Containers\BookingContainer\Contracts;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Transporters\UpdateBookingsRequestData;

interface UpdateBookingActionContract
{
    /**
     * @param UpdateBookingsRequestData $data
     * @return Booking|null
     */
    public function execute(UpdateBookingsRequestData $data): ?Booking;
}
