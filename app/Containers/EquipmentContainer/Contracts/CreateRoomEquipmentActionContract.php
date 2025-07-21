<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\EquipmentContainer\Transporters\CreateRoomEquipmentRequestData;

interface CreateRoomEquipmentActionContract
{
    /**
     * @param CreateRoomEquipmentRequestData $data
     * @return RoomEquipment
     */
    public function execute(CreateRoomEquipmentRequestData $data): RoomEquipment;
}
