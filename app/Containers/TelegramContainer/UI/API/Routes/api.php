<?php

use App\Containers\TelegramContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;

Route::post('/message', [Controllers\TelegramController::class, 'sendMessage'])->middleware('auth:api');

Route::post('/{token}/webhook', [Controllers\TelegramController::class, 'webhook']);
