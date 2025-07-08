<?php

use App\Containers\UserContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/registration', [Controllers\UserController::class, 'registration']);
Route::post('/login', [Controllers\UserController::class, 'login']);

Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
