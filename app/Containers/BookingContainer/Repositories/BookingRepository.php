<?php

namespace App\Containers\BookingContainer\Repositories;

use App\Containers\BookingContainer\Enums\Status;
use App\Containers\BookingContainer\Models\Booking;
use App\Containers\BookingContainer\Transporters\CreateBookingsRequestData;
use App\Containers\BookingContainer\Transporters\UpdateBookingsRequestData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class BookingRepository
{
    /**
     * @return Collection
     */
    public function getAll(): Collection {
        return Booking::all();
    }

    /**
     * @param string $id
     * @return Booking|null
     */
    public function findById(string $id): ?Booking {
        return Booking::query()->find($id);
    }

    /**
     * @param CreateBookingsRequestData $data
     * @return Booking
     */
    public function create(CreateBookingsRequestData $data): Booking {
        return Booking::query()->create([
            'user_id' => $data->userId,
            'room_id' => $data->roomId,
            'start_at' => $data->startAt,
            'end_at' => $data->endAt,
        ]);
    }

    /**
     * @param string $id
     * @param UpdateBookingsRequestData $data
     * @return int
     */
    public function update(string $id, UpdateBookingsRequestData $data): int {
        $updateData = [
            'user_id' => $data->userId,
            'room_id' => $data->roomId,
            'status' => $data->status,
            'start_at' => $data->startAt,
            'end_at' => $data->endAt,
        ];

        $updateData = array_filter($updateData);

        return Booking::query()
            ->where('id', $id)
            ->update($updateData);
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        Booking::query()
            ->where('id', $id)
            ->delete();
    }

    /**
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @return Collection
     */
    public function getBooked(Carbon $startAt, Carbon $endAt): Collection {
        return Booking::query()
            ->where('status', '!=', Status::DECLINED)
            ->whereBetween('start_at', [$startAt, $endAt->addSeconds(-1)])
            ->orWhereBetween('end_at', [$startAt->addSecond(), $endAt])
            ->get();
    }

}
