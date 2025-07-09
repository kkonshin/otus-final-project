<?php

namespace App\Containers\UserContainer\Exceptions;

use Illuminate\Http\JsonResponse;

class LoginFailedException extends \Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
