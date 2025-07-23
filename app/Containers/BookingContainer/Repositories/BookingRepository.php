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
            'start_at' => $data->startAt->format('Y-m-d H:i'),
            'end_at' => $data->endAt->format('Y-m-d H:i'),
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
     * @param int $roomId
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @return Collection
     */
    public function getBooked(
        int $roomId,
        Carbon $startAt,
        Carbon $endAt
    ): Collection {
        $formatedStartAt = $startAt->format('Y-m-d H:i');
        $formatedEndAt = $endAt->format('Y-m-d H:i');

        return Booking::query()
            ->where('status', '!=', Status::DECLINED)
            ->where('room_id', $roomId)
            ->whereDate('created_at', now())
            ->where(function ($query) use ($formatedStartAt, $formatedEndAt) {
                $query
                    ->whereTime('start_at', '!=', $formatedEndAt)
                    ->whereTime('end_at', '!=', $formatedStartAt);
            })
            ->where(function ($query) use ($formatedStartAt, $formatedEndAt) {
                $query
                    ->whereBetween('start_at', [$formatedStartAt, $formatedEndAt])
                    ->orWhereBetween('end_at', [$formatedStartAt, $formatedEndAt]);
            })
            ->get();
    }
}
