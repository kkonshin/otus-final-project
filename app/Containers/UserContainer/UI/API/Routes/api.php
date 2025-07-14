<?php

use App\Containers\UserContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;

Route::post('/registration', [Controllers\UserController::class, 'registration']);
Route::post('/login', [Controllers\UserController::class, 'login'])->name('login');

Route::get('/info', [Controllers\UserController::class, 'info'])->middleware('auth:api');
