<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Actions;

use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use App\Containers\TelegramContainer\Services\TelegramService;
use App\Containers\TelegramContainer\UI\CLI\Telegram\StartCommand;
use App\Containers\UserContainer\Models\User;
use App\Mail\TelegramConfirmationCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;
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
        elseif (
            $updateType === 'message'
            && $update->message->has('text')
            && str_starts_with($update->message->text, '/')
        ) {
            Telegram::commandsHandler(true);
        }

        // Обработка обычных сообщений
        elseif ($update->isType('message') && $update->message->text) {
            $this->handleMessage($update);
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

        dispatch(function () use ($data, $chatId, $messageId, $userTelegramId) {
            try {
                $service = app(TelegramService::class);

                if ($data === '/room_list') {
                    $service->generateRoomListKeyboard($chatId, 1, $messageId);
                } elseif (str_starts_with($data, '/rooms_page_')) {
                    $page = (int)str_replace('/rooms_page_', '', $data);
                    $service->generateRoomListKeyboard($chatId, $page, $messageId);
                } elseif (str_starts_with($data, '/room_detail_')) {
                    $roomId = (int)str_replace('/room_detail_', '', $data);
                    $service->getRoomDetail($chatId, $roomId, $messageId);
                } elseif (str_starts_with($data, '/booking_times_')) {
                    $roomId = (int)str_replace('/booking_times_', '', $data);
                    $service->showBookingTimes($chatId, $roomId, $messageId);
                } elseif (str_starts_with($data, '/confirm_booking_')) {
                    $parts = explode('_', $data);
                    $roomId = (int)$parts[2];
                    $startTime = $parts[3];
                    $service->confirmBooking($chatId, $roomId, $startTime, $messageId);
                } elseif (str_starts_with($data, '/finalize_booking_')) {
                    $parts = explode('_', $data);
                    $roomId = (int)$parts[2];
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

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleMessage(Update $update): void
    {
        $chatId = $update->message->chat->id;
        $userState = Cache::get("user_state_$chatId");

        if ($userState === 'awaiting_email') {
            $this->handleEmailInput($update);
        } elseif ($userState === 'awaiting_code') {
            $this->handleConfirmationCode($update);
        }
    }

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    public function handleEmailInput(Update $update): void
    {
        $chatId = $update->message->chat->id;
        $email = $update->message->text;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '❌ Неверный формат email. Попробуйте ещё раз:'
            ]);
        } else {
            $user = User::query()->where('email', $email)->first();

            if (!$user) {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '❌ Пользователь с таким email не найден.'
                ]);
            } else {
                $code = config('app.env') !== 'production' ? '9999' : rand(100000, 999999);
                Cache::put("telegram_confirm_$chatId", [
                    'user_id' => $user->id,
                    'code' => $code
                ], now()->addMinutes(5));

                Mail::to($user)->send(new TelegramConfirmationCode($code));

                Cache::put("user_state_$chatId", 'awaiting_code', now()->addMinutes(5));

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => '📩 Код подтверждения отправлен на ваш email. Введите его:'
                ]);
            }
        }
    }

    /**
     * @param Update $update
     * @return void
     * @throws TelegramSDKException
     */
    protected function handleConfirmationCode(Update $update): void
    {
        $chatId = $update->message->chat->id;
        $code = $update->message->text;
        $cacheKey = "telegram_confirm_$chatId";

        $data = Cache::get($cacheKey);

        if (!$data || $data['code'] != $code) {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '❌ Неверный код. Попробуйте ещё раз.'
            ]);
        } else {
            User::query()->where('id', $data['user_id'])->update([
                'telegram_chat_id' => $chatId,
            ]);

            Cache::forget($cacheKey);
            Cache::forget("user_state_$chatId");

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => '✅ Ваш аккаунт успешно привязан!'
            ]);

            $command = new StartCommand();
            $command->setTelegram(Telegram::bot());

            $update = new Update([
                'message' => [
                    'chat' => ['id' => $chatId],
                    'text' => '/start'
                ]
            ]);

            $command->make(
                new Api(),
                $update,
                [
                'offset' => 0,
                'length' => 6,
                'type' => 'bot_command'
                ]
            );
        }
    }
}
