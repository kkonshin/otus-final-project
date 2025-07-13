<?php

namespace App\Containers\RoomBookingContainer\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;
}
