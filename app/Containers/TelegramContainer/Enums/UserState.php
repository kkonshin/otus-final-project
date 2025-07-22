<?php

namespace app\Containers\TelegramContainer\Enums;

enum CallbackCommand: string
{
    case ROOM_LIST = '/room_list';
    case ROOMS_PAGE = '/rooms_page_';
    case ROOM_DETAIL = '/room_detail_';
    case BOOKING_TIMES = '/booking_times_';
    case CONFIRM_BOOKING = '/confirm_booking_';
    case FINALIZE_BOOKING = '/finalize_booking_';
    case MY_BOOKINGS = '/my_bookings';
    case CANCEL_BOOKING = '/cancel_booking_';

    /**
     * @param string $data
     * @return array|int[]
     */
    public function extractParams(string $data): array
    {
        return match ($this) {
            self::ROOMS_PAGE => ['page' => (int)str_replace(self::ROOMS_PAGE->value, '', $data)],
            self::ROOM_DETAIL,
            self::BOOKING_TIMES => ['roomId' => (int)str_replace($this->value, '', $data)],
            self::CONFIRM_BOOKING,
            self::FINALIZE_BOOKING => $this->extractBookingParams($data),
            self::CANCEL_BOOKING => ['bookingId' => (int)str_replace($this->value, '', $data)],
            default => [],
        };
    }

    /**
     * @param string $data
     * @return array
     */
    private function extractBookingParams(string $data): array
    {
        $parts = explode('_', $data);
        return [
            'roomId' => (int)$parts[2],
            'startTime' => $parts[3],
        ];
    }
}
