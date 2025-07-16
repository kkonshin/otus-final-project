<?php

namespace App\Containers\RoomBookingContainer\Models;

use App\Containers\RoomBookingContainer\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property int $floor
 * @property int $capacity
 * @property ?string $description
 * @property ?Carbon $available_from
 * @property ?Carbon available_to
 */
class Room extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_to' => 'datetime',
    ];

    protected static function newFactory(): RoomFactory
    {
        return RoomFactory::new();
    }
}
