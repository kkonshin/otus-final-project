<?php

namespace App\Containers\BookingContainer\Contracts;


use Illuminate\Support\Carbon;

interface CheckBookingTimeActionContract
{
    /**
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @return void
     */
    public function execute(Carbon $startAt, Carbon $endAt): void;
}
