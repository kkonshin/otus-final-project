<?php

namespace App\Containers\EquipmentContainer\Factories;

use App\Containers\EquipmentContainer\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'title' => $this->faker->unique()->word(),
        ];
    }
}
