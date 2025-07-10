<?php

namespace App\Containers\BookingContainer\Providers;

use App\Containers\BookingContainer\Actions\GetBookingsAction;
use App\Containers\BookingContainer\Contracts\GetBookingActionContract;
use Illuminate\Support\ServiceProvider;

final class BookingServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom("app/Containers/BookingContainer/Migrations");
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(GetBookingActionContract::class, GetBookingsAction::class);

        $this->app->register(ApiRouteServiceProvider::class);
    }
}
