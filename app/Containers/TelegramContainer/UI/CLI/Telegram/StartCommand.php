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
        $keyboard = Keyboard::make()
            ->row([
                Keyboard::button(['text' => '🏢 Список комнат', 'callback_data' => 'room_list']),
                Keyboard::button(['text' => '📅 Мои бронирования', 'callback_data' => 'my_bookings']),
            ])
            ->row([
                Keyboard::button(['text' => '➕ Новая бронь', 'callback_data' => 'new_booking']),
                Keyboard::button(['text' => '❌ Отменить бронь', 'callback_data' => 'cancel_booking']),
            ]);

        $this->replyWithMessage([
            'text' => 'Добро пожаловать в систему бронирования переговорных комнат!',
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }
}
