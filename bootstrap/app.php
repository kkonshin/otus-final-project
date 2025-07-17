<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
//        then: function () {
//            Route::middleware('api')
//                ->prefix('api/user')
//                ->name('user.')
//                ->group(base_path('app/Containers/UserContainer/UI/API/Routes/api.php'));
//        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withCommands([
        __DIR__.'/../app/Containers/TelegramContainer/UI/CLI/Commands',
    ])
//    ->withSchedule(function (Schedule $schedule) {
////        $schedule->command('passport:purge --expired')->daily();
//    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
