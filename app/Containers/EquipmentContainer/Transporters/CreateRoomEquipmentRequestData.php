<?php

namespace App\Containers\EquipmentContainer\Transporters;

use Spatie\LaravelData\Data;

class CreateRoomEquipmentRequestData extends Data
{
    public function __construct(
        public int $equipmentId,
        public int $roomId,
        public int $quantity = 1,
    ) {
    }
}


