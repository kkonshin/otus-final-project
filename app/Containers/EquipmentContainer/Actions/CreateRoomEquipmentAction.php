<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\CreateRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Models\RoomEquipment;
use App\Containers\EquipmentContainer\Repositories\RoomEquipmentRepository;
use App\Containers\EquipmentContainer\Transporters\CreateRoomEquipmentRequestData;

final readonly class CreateRoomEquipmentAction implements CreateRoomEquipmentActionContract
{
    /**
     * @param RoomEquipmentRepository $roomEquipmentRepository
     */
    public function __construct(private RoomEquipmentRepository $roomEquipmentRepository) {
    }

    /**
     * @param CreateRoomEquipmentRequestData $data
     * @return RoomEquipment
     */
    public function execute(CreateRoomEquipmentRequestData $data): RoomEquipment {
        return $this->roomEquipmentRepository->create($data);
    }
}
