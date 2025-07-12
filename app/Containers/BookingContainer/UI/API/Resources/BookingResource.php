<?php

namespace App\Containers\BookingContainer\UI\API\Resources;

use DateMalformedStringException;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws DateMalformedStringException
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->resource['id'],
            'user_id' => $this->resource['user_id'],
            'status' => $this->resource['status'],
            'start_at' => (new DateTime($this->resource['start_at']))->format('Y-m-d H:i:s'),
            'end_at' => (new DateTime($this->resource['end_at']))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTime($this->resource['updated_at']))->format('Y-m-d H:i:s'),
            'created_at' => (new DateTime($this->resource['created_at']))->format('Y-m-d H:i:s'),
        ];
    }
}
