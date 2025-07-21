<?php

namespace App\Containers\EquipmentContainer\Providers;

use App\Containers\EquipmentContainer\Actions\CreateEquipmentAction;
use App\Containers\EquipmentContainer\Actions\CreateRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\DeleteEquipmentAction;
use App\Containers\EquipmentContainer\Actions\DeleteRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\GetEquipmentAction;
use App\Containers\EquipmentContainer\Actions\GetRoomEquipmentAction;
use App\Containers\EquipmentContainer\Actions\OneEquipmentAction;
use App\Containers\EquipmentContainer\Actions\UpdateEquipmentAction;
use App\Containers\EquipmentContainer\Actions\UpdateRoomEquipmentAction;
use App\Containers\EquipmentContainer\Contracts\CreateEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\CreateRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\DeleteEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\DeleteRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\GetEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\GetRoomEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\OneEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\UpdateEquipmentActionContract;
use App\Containers\EquipmentContainer\Contracts\UpdateRoomEquipmentActionContract;
use Illuminate\Support\ServiceProvider;

final class EquipmentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom("app/Containers/EquipmentContainer/Migrations");
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(GetEquipmentActionContract::class, GetEquipmentAction::class);
        $this->app->bind(OneEquipmentActionContract::class, OneEquipmentAction::class);
        $this->app->bind(CreateEquipmentActionContract::class, CreateEquipmentAction::class);
        $this->app->bind(UpdateEquipmentActionContract::class, UpdateEquipmentAction::class);
        $this->app->bind(DeleteEquipmentActionContract::class, DeleteEquipmentAction::class);

        $this->app->bind(GetRoomEquipmentActionContract::class, GetRoomEquipmentAction::class);
        $this->app->bind(UpdateRoomEquipmentActionContract::class, UpdateRoomEquipmentAction::class);
        $this->app->bind(DeleteRoomEquipmentActionContract::class, DeleteRoomEquipmentAction::class);
        $this->app->bind(CreateRoomEquipmentActionContract::class, CreateRoomEquipmentAction::class);

        $this->app->register(ApiRouteServiceProvider::class);
    }
}
