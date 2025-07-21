<?php

namespace App\Containers\BookingContainer\UI\API\Controllers;

use App\Containers\BookingContainer\Actions\CreateBookingsAction;
use App\Containers\BookingContainer\Actions\DeleteBookingsAction;
use App\Containers\BookingContainer\Actions\GetBookingsAction;
use App\Containers\BookingContainer\Actions\OneBookingsAction;
use App\Containers\BookingContainer\Actions\UpdateBookingsAction;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use App\Containers\BookingContainer\Transporters\UpdateBookingsRequestData;
use App\Containers\BookingContainer\UI\API\Requests\CreateRequest;
use App\Containers\BookingContainer\UI\API\Requests\UpdateRequest;
use App\Containers\BookingContainer\UI\API\Resources\BookingResource;
use App\Containers\Core\Exceptions\ServiceUnavailableException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Throwable;

class BookingController extends Controller
{
    /**
     * @param GetBookingsAction $getBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function get(GetBookingsAction $getBookingsAction): JsonResponse {
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
     * @param string $id
     * @param OneBookingsAction $oneBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function one(
        string $id,
        OneBookingsAction $oneBookingsAction
    ): JsonResponse {
        try {
            $booking = $oneBookingsAction->execute($id);

            return response()->json([
                'success' => true,
                'data' => empty($booking)
                    ? []
                    : new BookingResource($booking)
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
                roomId: $validated['room_id'],
                startAt: new Carbon($validated['start_at']),
                endAt: new Carbon($validated['end_at']),
            ));

            return response()->json([
                'success' => true,
                'message' => "Комната успешно забронирована",
                'data' => new BookingResource($booking),
            ], 201);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param UpdateRequest $request
     * @param UpdateBookingsAction $updateBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function update(
        UpdateRequest $request,
        UpdateBookingsAction $updateBookingsAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $booking = $updateBookingsAction->execute(new UpdateBookingsRequestData(
                id: $validated['id'],
                userId: $validated['user_id'] ?? null,
                roomId: $validated['room_id'] ?? null,
                status: $validated['status'] ?? null,
                startAt: empty($validated['start_at'])
                    ? null
                    : new Carbon($validated['start_at']),
                endAt: empty($validated['end_at'])
                    ? null
                    : new Carbon($validated['end_at']),
            ));

            return response()->json([
                'success' => true,
                'message' => "Бронь комнаты успешно изменена",
                'data' => new BookingResource($booking),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param string $id
     * @param DeleteBookingsAction $deleteBookingsAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function delete(
        string $id,
        DeleteBookingsAction $deleteBookingsAction
    ): JsonResponse {
        try {
            $deleteBookingsAction->execute($id);

            return response()->json([
                'success' => true,
                'message' => "Бронь комнаты успешно удалена",
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }
}
