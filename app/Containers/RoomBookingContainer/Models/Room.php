<?php

namespace App\Containers\RoomBookingContainer\Models;

use App\Containers\RoomBookingContainer\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }
}
