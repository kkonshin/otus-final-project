<?php

return [
    App\Containers\BookingContainer\Providers\BookingServiceProvider::class,
    App\Containers\Common\ContainerTree\Providers\ContainerTreeServiceProvider::class,
    App\Containers\RoomBookingContainer\Providers\RoomBookingServiceProvider::class,
//    App\Containers\TelegramContainer\Providers\TelegramAppServiceProvider::class,
    App\Containers\UserContainer\Providers\UserServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\MoonShineServiceProvider::class,
    Telegram\Bot\Laravel\TelegramServiceProvider::class,
];
