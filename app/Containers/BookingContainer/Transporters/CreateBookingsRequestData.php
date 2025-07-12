<?php

namespace App\Containers\BookingContainer\Transporters;

use DateTime;
use Spatie\LaravelData\Data;

class CreateBookingsRequestData extends Data
{
    public function __construct(
        public int $userId,
        public string $status,
        public DateTime $startAt,
        public DateTime $endAt
    ) {
    }
}
