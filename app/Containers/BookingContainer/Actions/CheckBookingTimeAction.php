<?php

namespace App\Containers\BookingContainer\Actions;

use App\Containers\BookingContainer\Contracts\CheckBookingTimeActionContract;
use App\Containers\BookingContainer\Exceptions\BookingTimeException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckBookingTimeAction implements CheckBookingTimeActionContract
{
    /**
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @return void
     */
    public function execute(Carbon $startAt, Carbon $endAt): void {
        try {
            $now = now();

            if ($startAt->isBefore($now) || $now->diffInMinutes($startAt) < 60) {
                throw new BookingTimeException("Время начала брони должно быть позже нынешнего времени на 60 минут");
            }

            if ($endAt->isBefore($startAt) || $startAt->diffInMinutes($endAt) < 60) {
                throw new BookingTimeException("Время окончания брони должно быть позже начала времени бронирования на 60 минут");
            }

            if ($now->diffInDays($startAt) > 14) {
                throw new BookingTimeException("Дата брони должно быть не позже 14-ти дней");
            }
        } catch (BookingTimeException $exception) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT));
        }
    }

}
