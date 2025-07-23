<?php

namespace App\Containers\BookingContainer\Enums;

enum Status: string
{
    case DECLINED = 'declined';
    case ACCEPTED = 'accepted';
    case WAITING_CONFIRMATION = 'waiting_confirmation';

    /**
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
