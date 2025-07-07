<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Transporters;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

final class LoginUserData extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        #[Password(min: 8)]
        public string $password,
    ) {
    }
}
