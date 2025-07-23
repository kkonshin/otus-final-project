<?php

namespace App\Containers\TelegramContainer\UI\CLI\Telegram;

use App\Containers\UserContainer\Models\User;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Начать работу с ботом';

    public function handle(): void
    {
        $chatId = $this->getUpdate()->getChat()->get('id');
        $user = User::query()->where('telegram_chat_id', $chatId)->first();

        if (empty($user)) {
            Cache::put("user_state_{$chatId}", 'awaiting_email', now()->addMinutes(10));

            $this->replyWithMessage([
                'text' => 'Введите ваш email для регистрации:',
                'reply_markup' => Keyboard::forceReply()
            ]);
        } else {
            $keyboard = Keyboard::make()->inline();

            $keyboard->row([
                Keyboard::inlineButton([
                'text' => '🏢 Список комнат',
                    'callback_data' => '/room_list'
                ]),
                Keyboard::inlineButton([
                    'text' => '📅 Мои бронирования',
                    'callback_data' => '/my_bookings'
                ])
            ]);

            $this->replyWithMessage([
                'text' => 'Добро пожаловать в систему бронирования!',
                'reply_markup' => $keyboard,
                'parse_mode' => 'HTML'
            ]);
        }
    }
}
