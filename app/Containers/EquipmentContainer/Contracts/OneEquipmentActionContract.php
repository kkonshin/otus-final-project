<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\Equipment;

interface OneEquipmentActionContract
{
    /**
     * @param string $id
     * @return Equipment|null
     */
    public function execute(string $id): ?Equipment;
}
