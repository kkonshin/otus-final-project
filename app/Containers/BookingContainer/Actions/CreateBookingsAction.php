<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\CreateBookingActionContract;
use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

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
     * @throws HttpResponseException
     */
    public function execute(CreateBookingsRequestData $data): Booking {
//        $startAt = clone $data->startAt;
//        $endAt = clone $data->endAt;
//
//        $booked = $this->bookingRepository->getBooked($startAt, $endAt);
//        dd($booked->toArray());
        $booked = $this->bookingRepository->getBooked($data->startAt, $data->endAt);
dd($booked->toArray());
        if ($booked->isNotEmpty()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Бронь на данное время занята',
            ], Response::HTTP_CONFLICT));
        }

        $dbResponse = $this->bookingRepository->create($data);
        return $dbResponse->fill(['status' => Status::WAITING_CONFIRMATION->value]);
    }
}
