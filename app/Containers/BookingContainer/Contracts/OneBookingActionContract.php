<?php

namespace App\Containers\BookingContainer\Contracts;

use App\Containers\BookingContainer\Models\Booking;

interface OneBookingActionContract
{
    /**
     * @param string $id
     * @return Booking|null
     */
    public function execute(string $id): ?Booking;
}
