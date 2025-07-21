<?php

namespace App\Containers\TelegramContainer\UI\CLI\Telegram;

use App\Containers\TelegramContainer\Services\TelegramService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;

class RoomListCommand extends Command
{
    protected string $name = 'room_list';
    protected string $description = 'Показать список доступных комнат';

    /**
     * @throws TelegramSDKException
     */
    public function handle(): void
    {
        $service = app(TelegramService::class);

        $chatId = $this->getUpdate()->getChat()->get('id');
        $messageId = $this->getUpdate()->getMessage()->get('id');

        $service->generateRoomListKeyboard($chatId, 1, $messageId);
    }
}
