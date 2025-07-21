<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\EquipmentContainer\Transporters\UpdateRoomEquipmentRequestData;

interface UpdateRoomEquipmentActionContract
{
    /**
     * @param UpdateRoomEquipmentRequestData $data
     * @return Equipment|null
     */
    public function execute(UpdateRoomEquipmentRequestData $data): ?RoomEquipment;
}
