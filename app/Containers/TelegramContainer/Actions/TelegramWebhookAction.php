<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Actions;

use App\Containers\RoomBookingContainer\Models\Room;
use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Throwable;

final class TelegramWebhookAction implements TelegramWebhookActionContract
{
    /**
     * @return void
     * @throws TelegramSDKException
     */
    public function execute(): void
    {
        $update = Telegram::getWebhookUpdate();

        // Определяем тип обновления через objectType()
        $updateType = $update->objectType();

        // Обработка callback_query
        if ($updateType === 'callback_query') {
            $this->handleCallbackQuery($update->callbackQuery);
        }

        // Обработка команд
        if (
            $updateType === 'message'
            && $update->message->has('text')
            && str_starts_with($update->message->text, '/')
        ) {
            Telegram::commandsHandler(true);
        }
    }

    /**
     * @param CallbackQuery $callbackQuery
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleCallbackQuery(CallbackQuery $callbackQuery): void
    {
        // Сначала отвечаем на callback (это важно сделать в первые секунды)
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->id,
            'text' => 'Обрабатываю запрос ...'
        ]);

        $data = $callbackQuery->data;
        $chatId = $callbackQuery->message->chat->id;
        $messageId = $callbackQuery->message->messageId;

        // Обрабатываем в фоновом режиме (если операция долгая)
        dispatch(function() use ($data, $chatId, $messageId) {
            try {
                switch ($data) {
                    case 'room_list':
                        $this->executeRoomListCommand($chatId);
                        break;
                    // Другие обработчики...
                }
            } catch (Throwable $e) {
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

            $rooms->each(function ($room) use ($keyboard) {
                $keyboard->row([
                    Keyboard::inlineButton([
                        'text' => "🏢 $room->title",
                        'callback_data' => "room_detail_$room->id"
                    ])
                ]);
            });

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Список доступных комнат:',
                'reply_markup' => $keyboard
            ]);
        } catch (Throwable $e) {
            report($e);
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Не удалось загрузить список комнат'
            ]);
        }
    }
}
