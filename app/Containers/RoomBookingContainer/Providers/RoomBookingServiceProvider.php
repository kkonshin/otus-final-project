<?php

declare(strict_types=1);

namespace App\Containers\RoomBookingContainer\Providers;

use Illuminate\Support\ServiceProvider;

class RoomBookingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom('app/Containers/RoomBookingContainer/Migrations');
    }

    public function register(): void
    {
        $this->app->register(
            RoomsRoutesServiceProvider::class
        );
    }
}
