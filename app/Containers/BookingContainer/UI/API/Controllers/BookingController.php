<?php

namespace App\Containers\BookingContainer\UI\API\Controllers;

use App\Containers\BookingContainer\Actions\CreateBookingsAction;
use App\Containers\BookingContainer\Actions\GetBookingsAction;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use App\Containers\BookingContainer\UI\API\Requests\CreateRequest;
use App\Containers\BookingContainer\UI\API\Resources\BookingResource;
use App\Containers\CoreContainer\Exceptions\ServiceUnavailableException;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

class BookingController extends Controller
{
    /**
     * @param GetBookingsAction $getBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function get(GetBookingsAction $getBookingsAction): JsonResponse
    {
        try {
            $bookings = $getBookingsAction->execute();

            return response()->json([
                'success' => true,
                'data' => $bookings->toArray(),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param CreateRequest $request
     * @param CreateBookingsAction $createBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function create(
        CreateRequest $request,
        CreateBookingsAction $createBookingsAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $booking = $createBookingsAction->execute(new CreateBookingsRequestData(
                userId: $validated['user_id'],
                status: $validated['status'],
                startAt: new DateTime($validated['start_at']),
                endAt: new DateTime($validated['end_at']),
            ));

            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking->toArray()),
            ], 201);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

}
