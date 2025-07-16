<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Actions;

use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use App\Containers\TelegramContainer\Services\TelegramService;
use Telegram\Bot\Exceptions\TelegramSDKException;
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
        $userTelegramId = $callbackQuery->from->id;

        dispatch(function() use ($data, $chatId, $messageId, $userTelegramId) {
            try {
                $service = app(TelegramService::class);

                if ($data === '/room_list') {
                    $service->generateRoomListKeyboard($chatId);
                }
                elseif (str_starts_with($data, '/rooms_page_')) {
                    $page = (int) str_replace('/rooms_page_', '', $data);
                    $service->generateRoomListKeyboard($chatId, $page, $messageId);
                }
                elseif (str_starts_with($data, '/room_detail_')) {
                    $roomId = (int) str_replace('/room_detail_', '', $data);
                    $service->getRoomDetail($chatId, $roomId, $messageId);
                }
                elseif (str_starts_with($data, '/booking_times_')) {
                    $roomId = (int) str_replace('/booking_times_', '', $data);
                    $service->showBookingTimes($chatId, $roomId, $messageId);
                }
                elseif (str_starts_with($data, '/confirm_booking_')) {
                    $parts = explode('_', $data);
                    $roomId = (int) $parts[2];
                    $startTime = $parts[3];
                    $service->confirmBooking($chatId, $roomId, $startTime, $messageId);
                }
                elseif (str_starts_with($data, '/finalize_booking_')) {
                    $parts = explode('_', $data);
                    $roomId = (int) $parts[2];
                    $startTime = $parts[3];
                    $service->finalizeBooking($chatId, $roomId, $startTime, $userTelegramId, $messageId);
                }

            } catch (Throwable $e) {
                report($e);
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Не удалось выполнить команду. Пожалуйста, попробуйте позже.'
                ]);
            }
        });
    }
}
