<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\OneBookingActionContract;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;

final readonly class OneBookingsAction implements OneBookingActionContract
{
    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @param string $id
     * @return Booking|null
     */
    public function execute(string $id): ?Booking {
        return $this->bookingRepository->findById($id);
    }
}
