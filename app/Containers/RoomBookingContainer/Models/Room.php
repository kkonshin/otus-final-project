<?php

namespace App\Containers\RoomBookingContainer\Models;

use App\Containers\RoomBookingContainer\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Eloquent;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $capacity
 * @property int $floor
 * @property Carbon|null $available_from
 * @property Carbon|null $available_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static RoomFactory factory($count = null, $state = [])
 * @method static Builder<static>|Room newModelQuery()
 * @method static Builder<static>|Room newQuery()
 * @method static Builder<static>|Room query()
 * @method static Builder<static>|Room whereAvailableFrom($value)
 * @method static Builder<static>|Room whereAvailableTo($value)
 * @method static Builder<static>|Room whereCapacity($value)
 * @method static Builder<static>|Room whereCreatedAt($value)
 * @method static Builder<static>|Room whereDescription($value)
 * @method static Builder<static>|Room whereFloor($value)
 * @method static Builder<static>|Room whereId($value)
 * @method static Builder<static>|Room whereTitle($value)
 * @method static Builder<static>|Room whereUpdatedAt($value)
 * @mixin Eloquent
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
