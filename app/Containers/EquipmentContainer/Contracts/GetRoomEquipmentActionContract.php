<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\RoomEquipment;
use Illuminate\Database\Eloquent\Collection;

interface GetRoomEquipmentActionContract
{
    /**
     * @return Collection<int, RoomEquipment>
     */
    public function execute(): Collection;
}
