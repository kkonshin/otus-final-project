<?php

namespace App\Containers\TelegramContainer\UI\CLI\Telegram;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\MeetingRoom;

class RoomListCommand extends Command
{
    protected string $name = 'rooms';
    protected string $description = 'Показать список доступных комнат';
    protected string $pattern = '{page}'; // Добавляем поддержку пагинации

    public function handle()
    {
        $page = (int)$this->argument('page', 1);
        $perPage = 5;

        $rooms = MeetingRoom::where('is_active', true)
            ->paginate($perPage, ['*'], 'page', $page);

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
                    'text' => "🏢 {$room->name} ({$room->capacity} чел.)",
                    'callback_data' => "room_detail_{$room->id}"
                ])
            ]);
        }

        // Добавляем пагинацию
        if ($rooms->hasPages()) {
            $paginationRow = [];

            if ($rooms->currentPage() > 1) {
                $paginationRow[] = Keyboard::inlineButton([
                    'text' => '⬅️ Назад',
                    'callback_data' => "room_list_page_" . ($page - 1)
                ]);
            }

            if ($rooms->currentPage() < $rooms->lastPage()) {
                $paginationRow[] = Keyboard::inlineButton([
                    'text' => 'Вперед ➡️',
                    'callback_data' => "room_list_page_" . ($page + 1)
                ]);
            }

            $keyboard->row($paginationRow);
        }

        $this->replyWithMessage([
            'text' => '📋 <b>Список переговорных комнат</b>',
            'reply_markup' => $keyboard,
            'parse_mode' => 'HTML'
        ]);
    }
}
}
