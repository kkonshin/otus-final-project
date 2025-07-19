<?php

namespace App\Containers\EquipmentContainer\Transporters;

use DateTime;
use Spatie\LaravelData\Data;

class UpdateEquipmentRequestData extends Data
{
    public function __construct(
        public int $id,
        public ?string $title,
    ) {
    }
}
