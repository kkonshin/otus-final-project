<?php

namespace App\Containers\EquipmentContainer\Transporters;

use Spatie\LaravelData\Data;

class UpdateRoomEquipmentRequestData extends Data
{
    public function __construct(
        public int $id,
        public ?int $equipmentId,
        public ?int $roomId,
        public ?int $quantity,
    ) {
    }
}
