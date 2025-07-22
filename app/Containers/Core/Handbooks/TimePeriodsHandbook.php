<?php

namespace App\Containers\Core\Handbooks;

use Illuminate\Support\Carbon;

class TimePeriodsHandbook
{
    protected const array START_TIME = [
        "08:00:00",
        "09:00:00",
    ];

    protected const array END_TIME = [
        "22:00:00",
        "23:00:00",
    ];

    public static function getRandomPeriod(): array
    {
        return collect(self::START_TIME)
            ->crossJoin(self::END_TIME)
            ->map(function ($item) {
                if (Carbon::parse($item[0])->lessThan(Carbon::parse($item[1]))) {
                    return $item;
                }
                return null;
            })
            ->filter()
            ->values()
            ->random();
    }
}
