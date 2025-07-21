<?php

namespace App\Containers\EquipmentContainer\Contracts;

interface DeleteEquipmentActionContract
{
    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void;
}
