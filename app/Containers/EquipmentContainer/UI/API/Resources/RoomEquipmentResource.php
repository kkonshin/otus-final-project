<?php

namespace App\Containers\EquipmentContainer\UI\API\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomEquipmentResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->resource->id,
            'equipment_id' => $this->resource->equipment_id,
            'room_id' => $this->resource->room_id,
            'quantity' => $this->resource->room_id,
            'equipment' => $this->resource->equipment,
            'room' => $this->resource->room,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
