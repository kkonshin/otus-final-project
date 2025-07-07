<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Contracts;

use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Transporters\RegistrationUserData;

interface RegistrationUserActionContract
{
    public function execute(RegistrationUserData $data): User;
}
