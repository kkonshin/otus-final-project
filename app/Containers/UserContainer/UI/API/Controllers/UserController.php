<?php

namespace App\Containers\UserContainer\UI\API\Controllers;

use App\Containers\CoreContainer\Exceptions\ServiceUnavailableException;
use App\Containers\UserContainer\Actions\RegistrationUserAction;
use App\Containers\UserContainer\Transporters\RegistrationUserData;
use App\Containers\UserContainer\UI\API\Requests\RegistrationRequest;
use App\Containers\UserContainer\UI\API\Resources\RegistrationUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Регистрация нового пользователя
     *
     * @param RegistrationRequest $request
     * @param RegistrationUserAction $registrationUserAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function registration(RegistrationRequest $request, RegistrationUserAction $registrationUserAction): JsonResponse
    {
        try {
            $user = $registrationUserAction->execute(RegistrationUserData::from($request->validated()));

            return response()->json([
                'success' => true,
                 'data' => [
                    'user' => new RegistrationUserResource($user),
//                    'access_token' => $user->createToken($request->device_name)->accessToken,
                ],
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }
}
