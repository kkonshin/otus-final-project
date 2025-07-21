<?php

namespace Tests\Feature;

use App\Containers\EquipmentContainer\Models\Equipment;
use App\Containers\RoomBookingContainer\Models\Room;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    /**
     * Получения списка оборудования
     */
    public function testGet(): void
    {
        $response = $this->get('/api/v1/equipment', [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Получение оборудования по id
     */
    public function testOne(): void
    {
        $equipment = Equipment::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $equipment = Equipment::query()->create([
            'equipment_id' => $equipment->id,
            'room_id' => $room->id,
            'quantity' => 1,
        ]);

        $response = $this->get('/api/v1/equipment/' . $equipment->id, [
            'Accept' => 'application/json',
        ]);

        $equipment->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Создание оборудования
     */
    public function testCreate(): void
    {
        $equipment = Equipment::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $response = $this->post(
            '/api/v1/equipment',
            [
                'equipment_id' => $equipment->id,
                'room_id' => $room->id,
                'quantity' => 1,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        Equipment::query()
            ->where('id', $response['data']['id'])
            ->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(201);
    }

    /**
     * Обновление оборудования
     */
    public function testUpdate(): void
    {
        $equipment = Equipment::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $equipment = Equipment::query()->create([
            'equipment_id' => $equipment->id,
            'room_id' => $room->id,
            'quantity' => 1,
        ]);

        $response = $this->put(
            '/api/v1/equipment',
            [
                'id' => $equipment->id,
                'equipment_id' => $equipment->id,
                'room_id' => $room->id,
                'quantity' => 1,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $equipment->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Удаление оборудования
     */
    public function testDelete(): void
    {
        $equipment = Equipment::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $equipment = Equipment::query()->create([
            'equipment_id' => $equipment->id,
            'room_id' => $room->id,
            'quantity' => 1,
        ]);

        $response = $this->delete(
            '/api/v1/equipment/' . $equipment->id,
            [
                'Accept' => 'application/json',
            ]
        );

        $response->assertStatus(200);

        $equipment = Equipment::query()->find($equipment->id);

        $this->assertEmpty($equipment);
    }
}
