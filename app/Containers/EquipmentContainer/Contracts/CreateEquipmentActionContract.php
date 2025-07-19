<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Transporters\CreateEquipmentRequestData;

interface CreateEquipmentActionContract
{
    /**
     * @param CreateEquipmentRequestData $data
     * @return Equipment
     */
    public function execute(CreateEquipmentRequestData $data): Equipment;
}
