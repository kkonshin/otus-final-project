<?php

namespace App\Containers\EquipmentContainer\Actions;


use App\Containers\EquipmentContainer\Contracts\GetRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Repositories\RoomEquipmentRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetRoomEquipmentAction implements GetRoomEquipmentActionContract
{
    /**
     * @param RoomEquipmentRepository $roomEquipmentRepository
     */
    public function __construct(private RoomEquipmentRepository $roomEquipmentRepository) {
    }

    /**
     * @return Collection
     */
    public function execute(): Collection {
        return $this->roomEquipmentRepository->getAll();
    }
}
