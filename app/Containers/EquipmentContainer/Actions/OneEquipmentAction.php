<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\OneEquipmentActionContract;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Repositories\EquipmentRepository;

final readonly class OneEquipmentAction implements OneEquipmentActionContract
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private EquipmentRepository $equipmentRepository) {
    }

    /**
     * @param string $id
     * @return Equipment|null
     */
    public function execute(string $id): ?Equipment {
        return $this->equipmentRepository->findById($id);
    }
}
