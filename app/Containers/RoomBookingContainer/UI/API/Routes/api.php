<?php

use App\Containers\RoomBookingContainer\UI\API\Controllers\RoomsController;
use Illuminate\Support\Facades\Route;

Route::get('/all', [RoomsController::class, 'getAll'])
    ->name('get-all-rooms');

Route::get('/booked', [RoomsController::class, 'getBooked'])
    ->name('get-booked-rooms');

Route::get('/available', [RoomsController::class, 'getAvailable'])
    ->name('get-available-rooms');

Route::get('/equipment', [RoomsController::class, 'getRoomsEquipment'])
    ->name('get-rooms-equipment');

Route::post('/add', [RoomsController::class, 'addRoomToPool'])
    ->name('add-room');

Route::get('/{id}', [RoomsController::class, 'getRoomById'])
    ->name('get-room-by-id');
