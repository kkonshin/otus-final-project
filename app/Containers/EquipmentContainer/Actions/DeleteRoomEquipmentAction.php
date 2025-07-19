<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\DeleteRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Repositories\RoomEquipmentRepository;

final readonly class DeleteRoomEquipmentAction implements DeleteRoomEquipmentActionContract
{
    /**
     * @param RoomEquipmentRepository $roomEquipmentRepository
     */
    public function __construct(private RoomEquipmentRepository $roomEquipmentRepository) {
    }

    /**
     * @param string $id
     * @return void
     */
    public function execute(string $id): void {
        $this->roomEquipmentRepository->delete($id);
    }
}
