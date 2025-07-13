<?php

namespace App\Containers\TelegramContainer\UI\API\Controllers;

use App\Containers\Core\Exceptions\ServiceUnavailableException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\EditedMessage;
use Telegram\Bot\Objects\Message;

/**
 * @method handleEditedMessage(EditedMessage|null $editedMessage)
 */
class TelegramController extends Controller
{
    public function webhook(): JsonResponse
    {
        try {
            $update = Telegram::getWebhookUpdate();

            // 1. Определяем тип обновления
            $updateType = $update->objectType();

            // 2. Обработка команд (только для текстовых сообщений)
            if (
                $updateType === 'message'
                && $update->message->has('text')
                && str_starts_with($update->message->text, '/')
            ) {
                Telegram::commandsHandler(true);

                return response()->json([
                    'status' => 'command_handled',
                ]);
            }

            // 3. Обработка callback-запросов от кнопок
            if ($updateType === 'callback_query') {
                $this->handleCallbackQuery($update->callbackQuery);

                return response()->json([
                    'status' => 'callback_handled',
                ]);
            }

            // 4. Обработка других типов сообщений
            switch ($updateType) {
                case 'message':
                    $this->handleMessage($update->message);
                    break;
                case 'edited_message':
                    $this->handleEditedMessage($update->editedMessage);
                    break;
            }

            return response()->json([
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
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
        $data = $callbackQuery->data;
        $chatId = $callbackQuery->message->chat->id;
        $messageId = $callbackQuery->message->message_id;

        // Ответ на callback (убирает "часики")
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->id
        ]);

        if (str_starts_with($data, 'room_list_page_')) {
            $page = (int) str_replace('room_list_page_', '', $data);
            $this->processRoomListCommand($callbackQuery->message->chat->id, $page);
            return;
        }

        if ($data === 'room_list') {
            // Вместо triggerCommand используем прямое выполнение команды
            $this->processRoomListCommand($chatId, $messageId);
            return;
        }

        // Остальная логика обработки callback...
    }

    protected function processRoomListCommand($chatId, $messageId = null)
    {
        // Создаем искусственный Update объект
        $update = new \Telegram\Bot\Objects\Update([
            'update_id' => time(),
            'message' => [
                'message_id' => $messageId ?? time(),
                'chat' => ['id' => $chatId],
                'text' => '/rooms',
                'entities' => [
                    [
                        'type' => 'bot_command',
                        'offset' => 0,
                        'length' => 6
                    ]
                ]
            ]
        ]);

        // Получаем CommandBus и выполняем команду
        $commandBus = Telegram::bot('bindroom_bot')->getCommandBus();
        $commandBus->execute('rooms', $update);
    }

    protected function showRoomDetails($chatId, $roomId, $messageId = null)
    {
//        $room = MeetingRoom::findOrFail($roomId);
        $room = [
            'id' => $roomId,
            'name' => 'Комната 1',
            'location' => 'Москва, ул. Ленина, 123',
            'description' => 'Тестовая комната для тестов',
            'capacity' => 20,
        ];

        $equipment = implode(', ', $room['equipment'] ?? []);
        $photos = $room['photos'] ?? [];

        $text = "🏢 <b>{$room['name']}</b>\n\n"
            . "📍 <b>Местоположение:</b> {$room['location']}\n"
            . "👥 <b>Вместимость:</b> {$room['capacity']} чел.\n"
            . "🛠 <b>Оборудование:</b> {$equipment}\n\n"
            . "{$room['description']}";

        $keyboard = Keyboard::make()->inline();
        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '📅 Забронировать',
                'callback_data' => "book_room_{$room['id']}"
            ]),
            Keyboard::inlineButton([
                'text' => '🔙 Назад к списку',
                'callback_data' => 'room_list'
            ])
        ]);

        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ];

        if ($messageId) {
            // Редактируем существующее сообщение
            $params['message_id'] = $messageId;
            Telegram::editMessageText($params);
        } else {
            // Отправляем новое сообщение
            Telegram::sendMessage($params);
        }

        // Отправка фото, если есть
        if (!empty($photos)) {
            $this->sendRoomPhotos($chatId, $photos);
        }
    }

    /**
     * @param $chatId
     * @param $photos
     * @return void
     * @throws TelegramSDKException
     */
    protected function sendRoomPhotos($chatId, $photos): void
    {
        $media = [];
        foreach ($photos as $index => $photo) {
            $media[] = [
                'type' => 'photo',
                'media' => $photo,
                'caption' => $index === 0 ? 'Фото переговорной комнаты' : null
            ];
        }

        Telegram::sendMediaGroup([
            'chat_id' => $chatId,
            'media' => json_encode($media)
        ]);
    }

    /**
     * @param Message $message
     * @return void
     */
    protected function handleMessage(Message $message): void
    {
        $chatId = $message->chat->id;

        // Обработка только текстовых сообщений, которые не являются командами
        if ($message->has('text') && !str_starts_with($message->text, '/')) {
            $this->processTextMessage($chatId, $message->text);
        }

        // Можно добавить обработку других типов сообщений (фото, документы и т.д.)
    }

    /**
     * Информация о текущем пользователе
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ServiceUnavailableException
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $response = Telegram::sendMessage([
                'chat_id' => $request->get('chat_id', 887374379),
                'text' => $request->get('message', 'Test')
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $response->toArray()
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            throw new ServiceUnavailableException();
        }
    }
}
