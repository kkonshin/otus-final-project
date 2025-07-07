<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Contracts\LoginUserActionContract;
use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Transporters\LoginUserData;

final class LoginUserAction implements LoginUserActionContract
{
    public function execute(LoginUserData $data): User
    {
        return User::query()->first(); //TODO: auth
    }
}
