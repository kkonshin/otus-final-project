<?php

namespace App\Containers\EquipmentContainer\Transporters;

use Spatie\LaravelData\Data;

class CreateEquipmentRequestData extends Data
{
    public function __construct(
        public string $title,
    ) {
    }
}
