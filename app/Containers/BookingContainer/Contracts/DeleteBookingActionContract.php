<?php

namespace App\Containers\BookingContainer\Contracts;

interface DeleteBookingActionContract
{
    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void;
}
