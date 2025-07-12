<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\CreateBookingActionContract;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;

final readonly class CreateBookingsAction implements CreateBookingActionContract
{
    public function __construct(private BookingRepository $bookingRepository) {
    }

    public function execute(CreateBookingsRequestData $data): Booking {
        return $this->bookingRepository->create($data);
    }
}
