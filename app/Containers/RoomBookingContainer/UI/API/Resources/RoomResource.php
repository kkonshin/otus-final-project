<?php

namespace App\Containers\RoomBookingContainer\UI\API\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'description' => $this->description,
            'available_from' => $this->available_from,
            'available_to' => $this->available_to,

        ];
    }
}
