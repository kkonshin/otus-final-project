<?php

namespace App\Containers\BookingContainer\Actions;


use App\Containers\BookingContainer\Contracts\UpdateBookingActionContract;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Repositories\BookingRepository;
use App\Containers\BookingContainer\Transporters\UpdateBookingsRequestData;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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
        $booking = $this->bookingRepository->findById($data->id);

        if (empty($data->roomId)) {
            $roomId = $booking->id;
        } else {
            $roomId = $data->roomId;
        }

        if ($data->startAt && $data->endAt) {
            /** @var CheckBookingTimeAction $checkBookingTimeAction */
            $checkBookingTimeAction = app(CheckBookingTimeAction::class);

            $checkBookingTimeAction->execute($data->startAt, $data->endAt);

            $booked = $this->bookingRepository->getBooked($roomId, $data->startAt, $data->endAt);

            if ($booked->isNotEmpty()) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Бронь на данное время занята',
                ], Response::HTTP_CONFLICT));
            }
        }

        $updateResult = $this->bookingRepository->update($data->id, $data);

        if(!$updateResult) {
            throw new Exception('Строка не была обновлена');
        }

        return $this->bookingRepository->findById($data->id);
    }
}
