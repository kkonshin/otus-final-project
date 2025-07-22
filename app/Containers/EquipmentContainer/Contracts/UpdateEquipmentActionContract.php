<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Transporters\UpdateEquipmentRequestData;

interface UpdateEquipmentActionContract
{
    /**
     * @param UpdateEquipmentRequestData $data
     * @return Equipment|null
     */
    public function execute(UpdateEquipmentRequestData $data): ?Equipment;
}
