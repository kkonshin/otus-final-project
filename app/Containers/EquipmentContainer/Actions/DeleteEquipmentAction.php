<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\DeleteEquipmentActionContract;
use App\Containers\EquipmentContainer\Repositories\EquipmentRepository;

final readonly class DeleteEquipmentAction implements DeleteEquipmentActionContract
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private EquipmentRepository $equipmentRepository) {
    }

    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void {
        $this->equipmentRepository->delete($id);
    }
}
