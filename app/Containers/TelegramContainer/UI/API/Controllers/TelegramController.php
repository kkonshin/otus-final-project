<?php

namespace App\Containers\TelegramContainer\UI\API\Controllers;

use App\Containers\RoomBookingContainer\Models\Room;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\EditedMessage;


/**
 * @method handleEditedMessage(EditedMessage|null $editedMessage)
 */
class TelegramController extends Controller
{
    public function webhook(): JsonResponse
    {
        try {
            $update = Telegram::getWebhookUpdate();

            // Определяем тип обновления через objectType()
            $updateType = $update->objectType();

            // Обработка callback_query
            if ($updateType === 'callback_query') {
                $this->handleCallbackQuery($update->callbackQuery);
                return response()->json(['status' => 'callback_handled']);
            }

            // Обработка команд (если нужно)
            if ($updateType === 'message' && $update->message->has('text') && str_starts_with($update->message->text, '/')) {
                Telegram::commandsHandler(true);
                return response()->json(['status' => 'command_handled']);
            }

            return response()->json([
                'status' => 'success',
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        // 1. Сначала отвечаем на callback (это важно сделать в первые секунды)
        try {
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery->id,
                'text' => 'Обрабатываю запрос ...' // Опциональное уведомление
            ]);
        } catch (\Exception $e) {
            report($e);
            return;
        }

        // 2. Извлекаем данные
        $data = $callbackQuery->data;
        $chatId = $callbackQuery->message->chat->id;
        $messageId = $callbackQuery->message->messageId;

        // 3. Обрабатываем в фоновом режиме (если операция долгая)
        dispatch(function() use ($data, $chatId, $messageId) {
            try {
                switch ($data) {
                    case 'room_list':
                        $this->executeRoomListCommand($chatId);
                        break;
                    // Другие обработчики...
                }
            } catch (\Exception $e) {
                report($e);
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Произошла ошибка при обработке запроса'
                ]);
            }
        });
    }

    /**
     * @param $chatId
     * @return void
     * @throws TelegramSDKException
     */
    protected function executeRoomListCommand($chatId): void
    {
        try {
            $rooms = Room::query()->get();

            $keyboard = Keyboard::make()->inline();
            foreach ($rooms as $room) {
                $keyboard->row([
                    Keyboard::inlineButton([
                        'text' => "🏢 {$room->title}",
                        'callback_data' => "room_detail_{$room->id}"
                    ])
                ]);
            }

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Список доступных комнат:',
                'reply_markup' => $keyboard
            ]);

        } catch (\Exception $e) {
            report($e);
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Не удалось загрузить список комнат'
            ]);
        }
    }
}
