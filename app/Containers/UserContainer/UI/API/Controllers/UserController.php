<?php

namespace App\Containers\UserContainer\UI\API\Controllers;

use App\Containers\Core\Exceptions\ServiceUnavailableException;
use App\Containers\UserContainer\Actions\RegistrationUserAction;
use App\Containers\UserContainer\Contracts\LoginUserActionContract;
use App\Containers\UserContainer\Transporters\LoginUserData;
use App\Containers\UserContainer\Transporters\RegistrationUserData;
use App\Containers\UserContainer\UI\API\Requests\LoginRequest;
use App\Containers\UserContainer\UI\API\Requests\RegistrationRequest;
use App\Containers\UserContainer\UI\API\Resources\RegistrationUserResource;
use App\Containers\UserContainer\UI\API\Resources\UserInfoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

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
            $validated = $request->validated();

            $user = $registrationUserAction->execute(new RegistrationUserData(
                email: $validated['email'],
                password: $validated['password'],
                firstName: $validated['first_name'],
                lastName: $validated['last_name']
            ));

            return response()->json([
                'success' => true,
                 'data' => [
                    'user' => new RegistrationUserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ], 201);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * Авторизация пользователя по логину и паролю
     *
     * @param LoginRequest $request
     * @param LoginUserActionContract $loginUserAction
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function login(LoginRequest $request, LoginUserActionContract $loginUserAction): JsonResponse
    {
        try {
            $validated = $request->validated();

            $loginResponse = $loginUserAction->execute(new LoginUserData(
                email: $validated['email'],
                password: $validated['password'],
            ));

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new RegistrationUserResource($loginResponse->user),
                    'token' => $loginResponse->token,
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }

    /**
     * Информация о текущем пользователе
     *
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function info(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => UserInfoResource::make(auth()->user()),
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }
}
