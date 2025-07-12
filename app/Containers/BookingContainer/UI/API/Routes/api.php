<?php

use App\Containers\BookingContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\BookingController::class, 'get']);
//Route::get('/{id}', [Controllers\BookingController::class, 'one']);
Route::post('/', [Controllers\BookingController::class, 'create']);
//Route::put('/', [Controllers\BookingController::class, 'update']);
