<?php

namespace App\Containers\EquipmentContainer\Contracts;

interface DeleteRoomEquipmentActionContract
{
    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void;
}
