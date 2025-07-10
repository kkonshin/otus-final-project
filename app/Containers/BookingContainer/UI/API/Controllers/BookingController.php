<?php

namespace App\Containers\BookingContainer\UI\API\Controllers;

use App\Containers\BookingContainer\Actions\GetBookingsAction;
use App\Containers\CoreContainer\Exceptions\ServiceUnavailableException;
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
}
