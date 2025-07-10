<?php

use App\Containers\BookingContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\BookingController::class, 'get']);
