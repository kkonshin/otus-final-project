<?php

namespace App\Containers\BookingContainer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

final class ApiRouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespace = 'App\Containers\BookingContainer\UI\API\Controllers';

    /**
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * @return void
     */
    public function map(): void
    {
        Route::prefix('api/v1/booking')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('app/Containers/BookingContainer/UI/API/Routes/api.php'));
    }
}
