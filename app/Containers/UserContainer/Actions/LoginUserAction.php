<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Contracts\LoginUserActionContract;
use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Transporters\LoginResponseData;
use App\Containers\UserContainer\Transporters\LoginUserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginUserAction implements LoginUserActionContract
{
    /**
     * @param LoginUserData $data
     * @return LoginResponseData
     */
    public function execute(LoginUserData $data): LoginResponseData
    {
        $user = User::query()->where('email', $data->email)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные'],
            ]);
        }

        return new LoginResponseData(
            user: $user,
            token: $user->createToken('auth_token')->plainTextToken
        );
    }
}
