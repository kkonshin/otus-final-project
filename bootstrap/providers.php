<?php

return [
    App\Providers\AppServiceProvider::class,

    /**
     *  Container Service Providers
     */
    App\Containers\UserContainer\Providers\UserServiceProvider::class,
    App\Containers\Common\ContainerTree\Providers\ContainerTreeServiceProvider::class,
];
