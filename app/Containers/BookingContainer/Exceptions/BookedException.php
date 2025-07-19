<?php

namespace App\Containers\BookingContainer\Exceptions;

use Exception;

class BookedException extends Exception
{
    protected $code = 409;
    protected $message = 'Бронь занята';
}
