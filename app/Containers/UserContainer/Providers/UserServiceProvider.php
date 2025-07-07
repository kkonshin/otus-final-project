<?php

namespace App\Containers\UserContainer\Providers;

use App\Containers\UserContainer\Actions\LoginUserAction;
use App\Containers\UserContainer\Actions\RegistrationUserAction;
use App\Containers\UserContainer\Contracts\LoginUserActionContract;
use App\Containers\UserContainer\Contracts\RegistrationUserActionContract;
use Illuminate\Support\ServiceProvider;
//use Laravel\Passport\Passport;

final class UserServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom("app/Containers/UserContainer/Migrations");

//        Passport::tokensExpireIn(now()->addDays(15));
//        Passport::refreshTokensExpireIn(now()->addDays(30));
//        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(RegistrationUserActionContract::class, RegistrationUserAction::class);
        $this->app->bind(LoginUserActionContract::class, LoginUserAction::class);

        $this->app->register(ApiRouteServiceProvider::class);
    }
}
