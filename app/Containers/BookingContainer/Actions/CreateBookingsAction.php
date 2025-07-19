<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\CreateBookingActionContract;
use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;

final readonly class CreateBookingsAction implements CreateBookingActionContract
{
    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @param CreateBookingsRequestData $data
     * @return Booking
     */
    public function execute(CreateBookingsRequestData $data): Booking {
        $dbResponse = $this->bookingRepository->create($data);
        return $dbResponse->fill(['status' => Status::WAITING_CONFIRMATION->value]);
    }
}
