<?php

declare(strict_types=1);

namespace App\Containers\RoomBookingContainer\UI\API\Controllers;

use App\Containers\Core\Exceptions\ServiceUnavailableException;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\RoomBookingContainer\UI\API\Resources\RoomCollection;
use App\Containers\RoomBookingContainer\UI\API\Resources\RoomResource;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

class RoomsController extends Controller
{
    // TODO логировать запросы
    public function __construct()
    {
    }

    /**
     * Получение всех существующих комнат
     *
     * @return RoomCollection
     * @throws ServiceUnavailableException
     */
    // TODO with equipment
    public function getAll(): RoomCollection
    {
        try {
            return new RoomCollection(
                Room::all()
            );
        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * Получение всех доступных комнат
     *
     * @return RoomCollection
     * @throws ServiceUnavailableException
     */
    // TODO with equipment
    // TODO AvailableRoomResource?
    public function getAvailable(): RoomCollection
    {
        try {
            return new RoomCollection(
                Room::with('bookings')
                    ->doesntHave('bookings')
                    ->orWhereHas(
                        'bookings',
                        function ($query) {
                            $query
                                ->where(function ($query) {
                                    $query
                                        // FIXME комната должна иметь последний по времени статус declined
                                        ->where('status', '!=', 'accepted')
                                        ->first();
                                });
                        }
                    )
                    ->get()
            );

        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }
    // TODO with equipment
    /**
     * @return RoomCollection
     * @throws ServiceUnavailableException
     */
    // FIXME booked_from/to BookedRoomResource?
    public function getBooked(): RoomCollection
    {
        try {
            return new RoomCollection(
                Room::with('bookings')
                    ->whereHas(
                        'bookings',
                        function (Builder $query) {
                            $query
                                ->where('status', '=', 'accepted');
                        }
                    )
                    ->get()
            );
        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }

    public function getRoomEquipment(string $roomId): JsonResponse
    {

    }

    public function getBookedForCurrentUser(): JsonResponse
    {

    }

    // TODO добавить booked_by?
    public function getBookedByUserId(): JsonResponse
    {

    }

    // TODO добавить equipment?
    public function addRoomsToPool(): JsonResponse
    {

    }

    public function removeRoomsFromPool(): JsonResponse
    {

    }

    // TODO комнате прикреплять список из equipment?
    public function addRoomEquipment(): JsonResponse
    {

    }

    // TODO комнате прикреплять список из equipment?
    public function removeRoomEquipment(): JsonResponse
    {

    }

    /**
     * @throws ServiceUnavailableException
     */
    public function getRoomById(string $id): RoomResource
    {
        try {
            return new RoomResource(
                Room::findOrFail($id)
            );
        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }


}
