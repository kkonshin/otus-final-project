<?php

namespace App\Containers\BookingContainer\Transporters;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class UpdateBookingsRequestData extends Data
{
    public function __construct(
        public int $id,
        public ?int $userId,
        public ?int $roomId,
        public ?string $status,
        public ?Carbon $startAt,
        public ?Carbon $endAt
    ) {
    }
}
