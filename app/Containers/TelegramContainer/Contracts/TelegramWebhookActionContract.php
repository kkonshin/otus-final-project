<?php

declare(strict_types=1);

namespace App\Containers\TelegramContainer\Contracts;

interface TelegramWebhookActionContract
{
    public function execute(): void;
}
