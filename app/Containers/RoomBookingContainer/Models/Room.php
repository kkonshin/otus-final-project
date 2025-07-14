<?php

namespace App\Containers\RoomBookingContainer\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected array $protected = ['id'];

    protected $table = 'rooms';
}
