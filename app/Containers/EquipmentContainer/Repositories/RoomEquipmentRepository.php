<?php

namespace App\Containers\EquipmentContainer\Repositories;

use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\EquipmentContainer\Transporters\CreateRoomEquipmentRequestData;
use App\Containers\EquipmentContainer\Transporters\UpdateRoomEquipmentRequestData;
use Illuminate\Database\Eloquent\Collection;

class RoomEquipmentRepository
{
    /**
     * @return Collection
     */
    public function getAll(): Collection {
        return RoomEquipment::query()
            ->with('equipment', 'room')
            ->get();
    }

    /**
     * @param string $id
     * @return RoomEquipment|null
     */
    public function findById(string $id): ?RoomEquipment {
        return RoomEquipment::query()
            ->with('equipment', 'room')
            ->find($id);
    }

    /**
     * @param CreateRoomEquipmentRequestData $data
     * @return RoomEquipment
     */
    public function create(CreateRoomEquipmentRequestData $data): RoomEquipment {
        return RoomEquipment::query()->create([
            'equipment_id' => $data->equipmentId,
            'room_id' => $data->roomId,
            'quantity' => $data->quantity,
        ]);
    }

    /**
     * @param string $id
     * @param UpdateRoomEquipmentRequestData $data
     * @return int
     */
    public function update(string $id, UpdateRoomEquipmentRequestData $data): int {
        $updateData = [
            'equipment_id' => $data->equipmentId,
            'room_id' => $data->roomId,
            'quantity' => $data->quantity,
        ];

        $updateData = array_filter($updateData);

        return RoomEquipment::query()
            ->where('id', $id)
            ->update($updateData);
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        RoomEquipment::query()
            ->where('id', $id)
            ->delete();
    }
}
