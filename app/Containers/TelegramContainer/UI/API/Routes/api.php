<?php

use App\Containers\TelegramContainer\UI\API\Controllers;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::post('/message', [Controllers\TelegramController::class, 'sendMessage'])->middleware('auth:api');

Route::post('/7937069288:AAHzcPNg9ZiBGmFVjl72_bBFoB7uOgMz6fg/webhook', [Controllers\TelegramController::class, 'webhook']);
