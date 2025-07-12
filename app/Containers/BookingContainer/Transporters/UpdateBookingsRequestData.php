<?php

namespace App\Containers\BookingContainer\Transporters;

use DateTime;
use Spatie\LaravelData\Data;

class UpdateBookingsRequestData extends Data
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?string $status,
        public ?DateTime $startAt,
        public ?DateTime $endAt
    ) {
    }
}
