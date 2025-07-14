<?php

namespace App\Containers\TelegramContainer\UI\API\Controllers;

use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Telegram\Bot\Objects\EditedMessage;


/**
 * @method handleEditedMessage(EditedMessage|null $editedMessage)
 */
class TelegramController extends Controller
{
    public function webhook(TelegramWebhookActionContract $telegramWebhookAction): JsonResponse
    {
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
