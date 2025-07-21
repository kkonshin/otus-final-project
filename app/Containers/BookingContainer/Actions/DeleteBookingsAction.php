<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\DeleteBookingActionContract;
use App\Containers\BookingContainer\Repositories\BookingRepository;

final readonly class DeleteBookingsAction implements DeleteBookingActionContract
{
    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void {
        $this->bookingRepository->delete($id);
    }
}
