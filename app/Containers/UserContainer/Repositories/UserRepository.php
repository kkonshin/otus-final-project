<?php

namespace App\Containers\UserContainer\Repositories;

use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Transporters\RegistrationUserData;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Создание нового пользователя
     *
     * @param RegistrationUserData $data
     * @return User
     */
    public function create(RegistrationUserData $data): User
    {
        return User::query()->create([
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
        ]);
    }
}
