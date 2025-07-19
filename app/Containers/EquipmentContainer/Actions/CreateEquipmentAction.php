<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\CreateEquipmentActionContract;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Repositories\EquipmentRepository;
use App\Containers\EquipmentContainer\Transporters\CreateEquipmentRequestData;

final readonly class CreateEquipmentAction implements CreateEquipmentActionContract
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private EquipmentRepository $equipmentRepository) {
    }

    /**
     * @param CreateEquipmentRequestData $data
     * @return Equipment
     */
    public function execute(CreateEquipmentRequestData $data): Equipment {
        return $this->equipmentRepository->create($data);
    }
}
