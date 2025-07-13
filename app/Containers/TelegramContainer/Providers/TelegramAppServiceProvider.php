<?php

namespace App\Containers\TelegramContainer\Providers;

use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

final class TelegramAppServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     * @throws TelegramSDKException
     */
    public function boot(): void
    {
        if (config('telegram.bots.bindroom_bot.enabled')) {
            Telegram::addCommands(config('telegram.bots.bindroom_bot.commands'));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(ApiRouteServiceProvider::class);
    }
}
