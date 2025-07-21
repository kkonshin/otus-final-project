<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\UpdateBookingActionContract;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\UpdateBookingsRequestData;
use Exception;

final readonly class UpdateBookingsAction implements UpdateBookingActionContract
{
    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(private BookingRepository $bookingRepository) {
    }

    /**
     * @param UpdateBookingsRequestData $data
     * @return Booking|null
     * @throws Exception
     */
    public function execute(UpdateBookingsRequestData $data): ?Booking {
        $updateResult = $this->bookingRepository->update($data->id, $data);

        if(!$updateResult) {
            throw new Exception('Строка не была обновлена');
        }

        return $this->bookingRepository->findById($data->id);
    }
}
