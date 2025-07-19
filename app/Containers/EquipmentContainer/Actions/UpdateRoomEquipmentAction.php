<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\UpdateRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\EquipmentContainer\Repositories\RoomEquipmentRepository;
use App\Containers\EquipmentContainer\Transporters\UpdateRoomEquipmentRequestData;
use Exception;

final readonly class UpdateRoomEquipmentAction implements UpdateRoomEquipmentActionContract
{
    /**
     * @param RoomEquipmentRepository $roomEquipmentRepository
     */
    public function __construct(private RoomEquipmentRepository $roomEquipmentRepository) {
    }

    /**
     * @param UpdateRoomEquipmentRequestData $data
     * @return Equipment|null
     * @throws Exception
     */
    public function execute(UpdateRoomEquipmentRequestData $data): ?RoomEquipment {
        $updateResult = $this->roomEquipmentRepository->update($data->id, $data);

        if(!$updateResult) {
            throw new Exception('Строка не была обновлена');
        }

        return $this->roomEquipmentRepository->findById($data->id);
    }
}
