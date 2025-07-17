<?php

namespace App\Containers\RoomBookingContainer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RoomsRoutesServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Containers\RoomBookingContainer\UI\API\Controllers';

    /**
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * @return void
     */
    public function mapApiRoutes(): void
    {
        Route::prefix('api/v1/rooms')
            ->middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('app/Containers/RoomBookingContainer/UI/API/Routes/api.php'));
    }
}
