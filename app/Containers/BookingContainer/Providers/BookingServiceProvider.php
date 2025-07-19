<?php

namespace App\Containers\BookingContainer\Providers;

use App\Containers\BookingContainer\Actions\CreateBookingsAction;
use App\Containers\BookingContainer\Actions\DeleteBookingsAction;
use App\Containers\BookingContainer\Actions\GetBookingsAction;
use App\Containers\BookingContainer\Actions\OneBookingsAction;
use App\Containers\BookingContainer\Actions\UpdateBookingsAction;
use App\Containers\BookingContainer\Contracts\CreateBookingActionContract;
use App\Containers\BookingContainer\Contracts\DeleteBookingActionContract;
use App\Containers\BookingContainer\Contracts\GetBookingActionContract;
use App\Containers\BookingContainer\Contracts\OneBookingActionContract;
use App\Containers\BookingContainer\Contracts\UpdateBookingActionContract;
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
        $this->app->bind(OneBookingActionContract::class, OneBookingsAction::class);
        $this->app->bind(UpdateBookingActionContract::class, UpdateBookingsAction::class);
        $this->app->bind(CreateBookingActionContract::class, CreateBookingsAction::class);
        $this->app->bind(DeleteBookingActionContract::class, DeleteBookingsAction::class);

        $this->app->register(ApiRouteServiceProvider::class);
    }
}
