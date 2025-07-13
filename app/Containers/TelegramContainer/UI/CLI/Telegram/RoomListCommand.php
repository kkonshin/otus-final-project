<?php

namespace App\Containers\TelegramContainer\UI\CLI\Telegram;

use App\Containers\RoomBookingContainer\Models\Room;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class RoomListCommand extends Command
{
    protected string $name = 'room_list';
    protected string $description = 'Показать список доступных комнат';

    public function handle(): void
    {

        $rooms = Room::query()->get();

        if ($rooms->isEmpty()) {
            $this->replyWithMessage([
                'text' => 'Нет доступных переговорных комнат',
                'parse_mode' => 'HTML'
            ]);
            return;
        }

        $keyboard = Keyboard::make()->inline();

        foreach ($rooms as $room) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => "🏢 {$room->title} ({$room->capacity} чел.)",
                    'callback_data' => "room_detail_{$room->id}"
                ])
            ]);
        }

        $this->replyWithMessage([
            'text' => '📋 <b>Список переговорных комнат</b>',
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }
}
