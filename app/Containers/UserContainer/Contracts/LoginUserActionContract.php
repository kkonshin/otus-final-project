<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Contracts;

use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Transporters\LoginUserData;

interface LoginUserActionContract
{
    public function execute(LoginUserData $data): User;
}
