<?php

namespace App\Containers\TelegramContainer\Services;

use App\Containers\RoomBookingContainer\Models\Room;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    /**
     * @throws TelegramSDKException
     */
    public function generateRoomListKeyboard($chatId): void
    {
        $rooms = Room::query()->get();
        $keyboard = Keyboard::make()->inline();

        $rooms->each(function ($room) use ($keyboard) {
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => "🏢 $room->title ($room->capacity чел.)",
                    'callback_data' => "/room_detail_$room->id"
                ])
            ]);
        });

        if ($rooms->count()) {
            $message = [
                'chat_id' => $chatId,
                'text' => '📋 <b>Список переговорных комнат</b>',
                'reply_markup' => $keyboard,
                'parse_mode' => 'HTML'
            ];
        } else {
            $message = [
                'chat_id' => $chatId,
                'text' => 'Нет доступных переговорных комнат',
                'parse_mode' => 'HTML'
            ];
        }

        Telegram::sendMessage($message);
    }
}
