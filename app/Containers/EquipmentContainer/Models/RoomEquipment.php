<?php

namespace App\Containers\EquipmentContainer\Models;

use App\Containers\EquipmentContainer\Factories\RoomEquipmentFactory;
use App\Containers\RoomBookingContainer\Models\Room;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $equipment_id
 * @property int $room_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static RoomEquipmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|RoomEquipment newModelQuery()
 * @method static Builder<static>|RoomEquipment newQuery()
 * @method static Builder<static>|RoomEquipment query()
 * @method static Builder<static>|RoomEquipment whereCreatedAt($value)
 * @method static Builder<static>|RoomEquipment whereEndAt($value)
 * @method static Builder<static>|RoomEquipment whereId($value)
 * @method static Builder<static>|RoomEquipment whereRoomId($value)
 * @method static Builder<static>|RoomEquipment whereStartAt($value)
 * @method static Builder<static>|RoomEquipment whereStatus($value)
 * @method static Builder<static>|RoomEquipment whereUpdatedAt($value)
 * @method static Builder<static>|RoomEquipment whereUserId($value)
 * @mixin Eloquent
 */
class RoomEquipment extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    /**
     * @return RoomEquipmentFactory|Factory
     */
    protected static function newFactory(): RoomEquipmentFactory|Factory {
        return RoomEquipmentFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function equipment(): BelongsTo {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * @return BelongsTo
     */
    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }
}
