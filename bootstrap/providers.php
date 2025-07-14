<?php

return [
    App\Providers\AppServiceProvider::class,

    /**
     *  Container Service Providers
     */
    App\Containers\UserContainer\Providers\UserServiceProvider::class,
    App\Containers\Common\ContainerTree\Providers\ContainerTreeServiceProvider::class,
    App\Containers\RoomBookingContainer\Providers\RoomBookingServiceProvider::class,
    Telegram\Bot\Laravel\TelegramServiceProvider::class,
    App\Containers\TelegramContainer\Providers\TelegramAppServiceProvider::class,
    App\Containers\BookingContainer\Providers\BookingServiceProvider::class,
];
