<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\GetBookingActionContract;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetBookingsAction implements GetBookingActionContract
{
    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @return Collection
     */
    public function execute(): Collection {
        return $this->bookingRepository->getAll();
    }
}
