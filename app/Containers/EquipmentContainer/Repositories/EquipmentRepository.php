<?php

namespace App\Containers\EquipmentContainer\Repositories;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Transporters\CreateEquipmentRequestData;
use App\Containers\EquipmentContainer\Transporters\UpdateEquipmentRequestData;
use Illuminate\Database\Eloquent\Collection;

class EquipmentRepository
{
    /**
     * @return Collection
     */
    public function getAll(): Collection {
        return Equipment::all();
    }

    /**
     * @param string $id
     * @return Equipment|null
     */
    public function findById(string $id): ?Equipment {
        return Equipment::query()->find($id);
    }

    /**
     * @param CreateEquipmentRequestData $data
     * @return Equipment
     */
    public function create(CreateEquipmentRequestData $data): Equipment {
        return Equipment::query()->create([
            'title' => $data->title,
        ]);
    }

    /**
     * @param string $id
     * @param UpdateEquipmentRequestData $data
     * @return int
     */
    public function update(string $id, UpdateEquipmentRequestData $data): int {
        $updateData = [
            'title' => $data->title,
        ];

        $updateData = array_filter($updateData);

        return Equipment::query()
            ->where('id', $id)
            ->update($updateData);
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        Equipment::query()
            ->where('id', $id)
            ->delete();
    }
}
