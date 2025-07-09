<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Contracts\RegistrationUserActionContract;
use App\Containers\UserContainer\Models\User;
use App\Containers\UserContainer\Repositories\UserRepository;
use App\Containers\UserContainer\Transporters\RegistrationUserData;

final readonly class RegistrationUserAction implements RegistrationUserActionContract
{
    public function __construct(
        protected UserRepository $repository,
    ) {
    }

    /**
     * @param RegistrationUserData $data
     * @return User
     */
    public function execute(RegistrationUserData $data): User
    {
        $user = $this->repository->create($data);

        //TODO: task отправка уведомления успешной регистрации $user->notify(new Welcome());

        return $user;
    }
}
