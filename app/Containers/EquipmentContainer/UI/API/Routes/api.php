<?php

use App\Containers\EquipmentContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'room'], function () {
    Route::get('/', [Controllers\EquipmentController::class, 'getRoomEquipment']);
    Route::post('/', [Controllers\EquipmentController::class, 'createRoomEquipment']);
    Route::put('/', [Controllers\EquipmentController::class, 'updateRoomEquipment']);
    Route::delete('/{id}', [Controllers\EquipmentController::class, 'deleteRoomEquipment']);
});

Route::get('/', [Controllers\EquipmentController::class, 'get']);
Route::get('/{id}', [Controllers\EquipmentController::class, 'one']);
Route::post('/', [Controllers\EquipmentController::class, 'create']);
Route::put('/', [Controllers\EquipmentController::class, 'update']);
Route::delete('/{id}', [Controllers\EquipmentController::class, 'delete']);
