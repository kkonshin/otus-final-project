<?php

use App\Containers\RoomBookingContainer\UI\API\Controllers\RoomsController;
use Illuminate\Support\Facades\Route;

Route::get('/all', [RoomsController::class, 'getAll'])
    ->name('get-all-rooms');

Route::get('/booked', [RoomsController::class, 'getBooked'])
    ->name('get-booked-rooms');

Route::get('/available', [RoomsController::class, 'getAvailable'])
    ->name('get-available-rooms');

// TODO массив параметров
Route::get('/equipment', [RoomsController::class, 'getRoomsEquipment'])
    ->name('get-rooms-equipment');

// TODO auth:sanctum
// Route::middleware('auth:sanctum')
Route::get('/{id}', [RoomsController::class, 'getRoomById'])
    ->name('get-room-by-id');
