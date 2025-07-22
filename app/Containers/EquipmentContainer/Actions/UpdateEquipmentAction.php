<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\UpdateEquipmentActionContract;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Repositories\EquipmentRepository;
use App\Containers\EquipmentContainer\Transporters\UpdateEquipmentRequestData;
use Exception;

final readonly class UpdateEquipmentAction implements UpdateEquipmentActionContract
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private EquipmentRepository $equipmentRepository) {
    }

    /**
     * @param UpdateEquipmentRequestData $data
     * @return Equipment|null
     * @throws Exception
     */
    public function execute(UpdateEquipmentRequestData $data): ?Equipment {
        $updateResult = $this->equipmentRepository->update($data->id, $data);

        if(!$updateResult) {
            throw new Exception('Строка не была обновлена');
        }

        return $this->equipmentRepository->findById($data->id);
    }
}
