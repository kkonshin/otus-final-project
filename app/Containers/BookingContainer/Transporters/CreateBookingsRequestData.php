<?php

namespace App\Containers\BookingContainer\Transporters;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class CreateBookingsRequestData extends Data
{
    public function __construct(
        public int $userId,
        public int $roomId,
        public Carbon $startAt,
        public Carbon $endAt
    ) {
    }
}
