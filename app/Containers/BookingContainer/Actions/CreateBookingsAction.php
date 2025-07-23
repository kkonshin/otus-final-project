<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\CreateBookingActionContract;
use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var CheckBookingTimeAction $checkBookingTimeAction */
        $checkBookingTimeAction = app(CheckBookingTimeAction::class);

        $checkBookingTimeAction->execute($data->startAt, $data->endAt);

        $booked = $this->bookingRepository->getBooked($data->roomId, $data->startAt, $data->endAt);

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
