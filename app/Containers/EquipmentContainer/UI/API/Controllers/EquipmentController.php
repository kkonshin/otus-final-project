<?php

namespace App\Containers\EquipmentContainer\UI\API\Controllers;

use App\Containers\EquipmentContainer\Actions\CreateEquipmentAction;
use App\Containers\EquipmentContainer\Actions\CreateRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\DeleteEquipmentAction;
use App\Containers\EquipmentContainer\Actions\DeleteRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\GetEquipmentAction;
use App\Containers\EquipmentContainer\Actions\GetRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\OneEquipmentAction;
use App\Containers\EquipmentContainer\Actions\UpdateEquipmentAction;
use App\Containers\EquipmentContainer\Actions\UpdateRoomEquipmentAction;
use App\Containers\EquipmentContainer\Transporters\CreateEquipmentRequestData;
use App\Containers\EquipmentContainer\Transporters\CreateRoomEquipmentRequestData;
use App\Containers\EquipmentContainer\Transporters\UpdateEquipmentRequestData;
use App\Containers\EquipmentContainer\Transporters\UpdateRoomEquipmentRequestData;
use App\Containers\EquipmentContainer\UI\API\Requests\CreateRequest;
use App\Containers\EquipmentContainer\UI\API\Requests\CreateRoomEquipmentRequest;
use App\Containers\EquipmentContainer\UI\API\Requests\UpdateRequest;
use App\Containers\EquipmentContainer\UI\API\Requests\UpdateRoomEquipmentRequest;
use App\Containers\EquipmentContainer\UI\API\Resources\EquipmentResource;
use App\Containers\Core\Exceptions\ServiceUnavailableException;
use App\Containers\EquipmentContainer\UI\API\Resources\RoomEquipmentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

class EquipmentController extends Controller
{
    /**
     * @param GetEquipmentAction $getEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function get(GetEquipmentAction $getEquipmentAction): JsonResponse {
        try {
            $equipment = $getEquipmentAction->execute();

            return response()->json([
                'success' => true,
                'data' => $equipment->toArray(),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param string $id
     * @param OneEquipmentAction $oneEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function one(
        string $id,
        OneEquipmentAction $oneEquipmentAction
    ): JsonResponse {
        try {
            $equipment = $oneEquipmentAction->execute($id);

            return response()->json([
                'success' => true,
                'data' => empty($equipment)
                    ? []
                    : new EquipmentResource($equipment)
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param CreateRequest $request
     * @param CreateEquipmentAction $createEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function create(
        CreateRequest $request,
        CreateEquipmentAction $createEquipmentAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $equipment = $createEquipmentAction->execute(new CreateEquipmentRequestData(
                title: $validated['title'] ?? null,
            ));

            return response()->json([
                'success' => true,
                'message' => "Оборудование успешно создано",
                'data' => new EquipmentResource($equipment),
            ], 201);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param UpdateRequest $request
     * @param UpdateEquipmentAction $updateEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function update(
        UpdateRequest $request,
        UpdateEquipmentAction $updateEquipmentAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $equipment = $updateEquipmentAction->execute(new UpdateEquipmentRequestData(
                id: $validated['id'],
                title: $validated['title'] ?? null,
            ));

            return response()->json([
                'success' => true,
                'message' => "Оборудование успешно изменено",
                'data' => new EquipmentResource($equipment),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param string $id
     * @param DeleteEquipmentAction $deleteEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function delete(
        string $id,
        DeleteEquipmentAction $deleteEquipmentAction
    ): JsonResponse {
        try {
            $deleteEquipmentAction->execute($id);

            return response()->json([
                'success' => true,
                'message' => "Оборудование успешно удалено",
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param GetRoomEquipmentAction $getRoomEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function getRoomEquipment(GetRoomEquipmentAction $getRoomEquipmentAction): JsonResponse {
        try {
            $equipment = $getRoomEquipmentAction->execute();

            return response()->json([
                'success' => true,
                'data' => $equipment->toArray(),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param CreateRoomEquipmentRequest $request
     * @param CreateRoomEquipmentAction $createRoomEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function createRoomEquipment(
        CreateRoomEquipmentRequest $request,
        CreateRoomEquipmentAction $createRoomEquipmentAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $equipment = $createRoomEquipmentAction->execute(new CreateRoomEquipmentRequestData(
                equipmentId: $validated['equipment_id'],
                roomId: $validated['room_id'],
                quantity: $validated['quantity'] ?? 1
            ));

            return response()->json([
                'success' => true,
                'message' => "Оборудование успешно прикреплено к комнате",
                'data' => new RoomEquipmentResource($equipment),
            ], 201);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param UpdateRoomEquipmentRequest $request
     * @param UpdateRoomEquipmentAction $updateRoomEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function updateRoomEquipment(
        UpdateRoomEquipmentRequest $request,
        UpdateRoomEquipmentAction $updateRoomEquipmentAction
    ): JsonResponse {
        try {
            $validated = $request->validated();

            $equipment = $updateRoomEquipmentAction->execute(new UpdateRoomEquipmentRequestData(
                id: $validated['id'],
                equipmentId: $validated['equipment_id'] ?? null,
                roomId: $validated['room_id'] ?? null,
                quantity: $validated['quantity'] ?? null
            ));

            return response()->json([
                'success' => true,
                'message' => "Данные прикрепления оборудования и комнаты успешно изменены",
                'data' => new RoomEquipmentResource($equipment),
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * @param string $id
     * @param DeleteRoomEquipmentAction $deleteRoomEquipmentAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function deleteRoomEquipment(
        string $id,
        DeleteRoomEquipmentAction $deleteRoomEquipmentAction
    ): JsonResponse {
        try {
            $deleteRoomEquipmentAction->execute($id);

            return response()->json([
                'success' => true,
                'message' => "Оборудование успешно откреплено от комнаты",
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }
}
