<?php

namespace App\Containers\EquipmentContainer\Contracts;

use App\Containers\EquipmentContainer\Models\Equipment;
use Illuminate\Database\Eloquent\Collection;

interface GetEquipmentActionContract
{
    /**
     * @return Collection<int, Equipment>
     */
    public function execute(): Collection;
}
