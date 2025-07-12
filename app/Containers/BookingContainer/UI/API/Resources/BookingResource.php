<?php

namespace App\Containers\BookingContainer\UI\API\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'status' => $this->resource->status,
            'start_at' => $this->resource->start_at?->format('Y-m-d H:i:s'),
            'end_at' => $this->resource->end_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
