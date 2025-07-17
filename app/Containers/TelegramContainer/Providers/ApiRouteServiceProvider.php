<?php

namespace App\Containers\TelegramContainer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

final class ApiRouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Containers\TelegramContainer\UI\API\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        Route::prefix('api/v1/telegram')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('app/Containers/TelegramContainer/UI/API/Routes/api.php'));
    }
}
