<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Actions;

use App\Containers\TelegramContainer\Contracts\TelegramWebhookActionContract;
use App\Containers\TelegramContainer\Services\TelegramService;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Facades\Telegram;

final readonly class TelegramWebhookAction implements TelegramWebhookActionContract
{
    public function __construct(
        private TelegramService $telegramService,
    ) {
    }

    /**
     * @return void
     * @throws TelegramSDKException
     */
    public function execute(): void
    {
        $update = Telegram::getWebhookUpdate();
        $this->telegramService->routeUpdate($update);
    }
}
