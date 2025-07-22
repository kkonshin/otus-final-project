<?php

namespace App\Containers\TelegramContainer\Enums;

enum UserState: string
{
    case AWAITING_EMAIL = 'awaiting_email';
    case AWAITING_CODE = 'awaiting_code';
}
