<?php

namespace App\Containers\CoreContainer\Exceptions;

use Illuminate\Http\JsonResponse;

class ServiceUnavailableException extends CustomException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Сервис временно не доступен.',
        ], 500);
    }
}
