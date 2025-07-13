<?php

namespace App\Containers\TelegramContainer\UI\CLI\Telegram;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Начать работу с ботом';

    public function handle(): void
    {
        $keyboard = Keyboard::make()->inline(); // Важно: делаем клавиатуру inline

        $keyboard->row([
            Keyboard::inlineButton([ // Используем inlineButton вместо button
                'text' => '🏢 Список комнат',
                'callback_data' => 'room_list'
            ]),
            Keyboard::inlineButton([
                'text' => '📅 Мои бронирования',
                'callback_data' => 'my_bookings'
            ])
        ]);

        $keyboard->row([
            Keyboard::inlineButton([
                'text' => '➕ Новая бронь',
                'callback_data' => 'new_booking'
            ]),
            Keyboard::inlineButton([
                'text' => '❌ Отменить бронь',
                'callback_data' => 'cancel_booking'
            ])
        ]);

        $this->replyWithMessage([
            'text' => 'Добро пожаловать в систему бронирования!',
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }
}
