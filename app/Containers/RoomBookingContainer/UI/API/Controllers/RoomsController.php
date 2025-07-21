<?php

declare(strict_types=1);

namespace App\Containers\RoomBookingContainer\UI\API\Controllers;

use App\Containers\Core\Exceptions\ServiceUnavailableException;
use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\RoomBookingContainer\UI\API\Resources\RoomCollection;
use App\Containers\RoomBookingContainer\UI\API\Resources\RoomResource;
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
     * @throws Throwable
     */
    public function getAll(): RoomCollection
    {
        try {
            return new RoomCollection(Room::paginate());
        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }
    // TODO добавить флаг доступности?
    public function getAvailable(): JsonResponse
    {

    }

    // TODO добавить флаг доступности?
    public function getBooked(): JsonResponse
    {

    }

    public function getRoomsEquipment(): JsonResponse
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
    public function getRoomById(int $id): RoomResource
    {
        try {
            return new RoomResource(Room::findOrFail($id));
        } catch (Throwable $exception) {
            report($exception);
            throw new ServiceUnavailableException();
        }
    }


}
