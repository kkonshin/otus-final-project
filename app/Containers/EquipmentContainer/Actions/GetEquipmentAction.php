<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\GetEquipmentActionContract;
use App\Containers\EquipmentContainer\Repositories\EquipmentRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetEquipmentAction implements GetEquipmentActionContract
{
    /**
     * @param EquipmentRepository $equipmentRepository
     */
    public function __construct(private EquipmentRepository $equipmentRepository) {
    }

    /**
     * @return Collection
     */
    public function execute(): Collection {
        return $this->equipmentRepository->getAll();
    }
}
