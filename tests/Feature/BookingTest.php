<?php

namespace Tests\Feature;

use App\Containers\BookingContainer\Models\Booking;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\UserContainer\Models\User;
use Tests\TestCase;

class BookingTest extends TestCase
{
    /**
     * Получения списка брони
     */
    public function testGet(): void
    {
        $response = $this->get('/api/v1/booking', [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Получение брони по id
     */
    public function testOne(): void
    {
        $user = User::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addHours(28),
        ]);

        $response = $this->get('/api/v1/booking/' . $booking->id, [
            'Accept' => 'application/json',
        ]);

        $booking->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Создание брони
     */
    public function testCreate(): void
    {
        $user = User::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $response = $this->post(
            '/api/v1/booking',
            [
                'user_id' => $user->id,
                'room_id' => $room->id,
                'start_at' => now()->addDay(),
                'end_at' => now()->addHours(28),
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        Booking::query()
            ->where('id', $response['data']['id'])
            ->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(201);
    }

    /**
     * Обновление брони
     */
    public function testUpdate(): void
    {
        $user = User::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_at' => now()->addHours(25),
            'end_at' => now()->addHours(26),
        ]);

        $response = $this->put(
            '/api/v1/booking',
            [
                'id' => $booking->id,
                'user_id' => $user->id,
                'room_id' => $room->id,
                'start_at' => now()->addDay(),
                'end_at' => now()->addHours(28),
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $booking->delete();

        $response
            ->assertJsonFragment(['success' => true])
            ->assertStatus(200);
    }

    /**
     * Удаление брони
     */
    public function testDelete(): void
    {
        $user = User::query()->inRandomOrder()->firstOrFail();
        $room = Room::query()->inRandomOrder()->firstOrFail();

        $booking = Booking::query()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_at' => now()->addHours(25),
            'end_at' => now()->addHours(26),
        ]);

        $response = $this->delete(
            '/api/v1/booking/' . $booking->id,
            [
                'Accept' => 'application/json',
            ]
        );

        $response->assertStatus(200);

        $booking = Booking::query()->find($booking->id);

        $this->assertEmpty($booking);
    }
}
