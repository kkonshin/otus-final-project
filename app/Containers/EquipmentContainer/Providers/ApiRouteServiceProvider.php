<?php

namespace App\Containers\EquipmentContainer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

final class ApiRouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespace = 'App\Containers\EquipmentContainer\UI\API\Controllers';

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
        Route::prefix('api/v1/equipment')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('app/Containers/EquipmentContainer/UI/API/Routes/api.php'));
    }
}
