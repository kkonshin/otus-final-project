<?php

namespace App\Containers\TelegramContainer\Exceptions;

class SetTelegramWebhookException extends \Exception
{
    const int CODE = 500;
    public $message = 'Failed to set Telegram webhook';
}
