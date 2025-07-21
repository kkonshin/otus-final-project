<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Transporters;

use App\Containers\UserContainer\Models\User;
use Spatie\LaravelData\Data;

final class LoginResponseData extends Data
{
    public function __construct(
        public User $user,
        public string $token,
    ) {
    }
}
