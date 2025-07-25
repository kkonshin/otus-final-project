<?php

namespace App\Containers\TelegramContainer\UI\API\Controllers;

use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TelegramController extends Controller
{
    public function webhook(string $token, TelegramWebhookActionContract $telegramWebhookAction): JsonResponse
    {
        if (
            $token !== config('telegram.bots.bindroom_bot.token')
            || !config('telegram.bots.bindroom_bot.enabled')
        ) {
            return response()->json([
                'message' => 'Not Found',
            ], 404);
        }
        try {
            $telegramWebhookAction->execute();

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
            ], 500);
        }
    }
}
