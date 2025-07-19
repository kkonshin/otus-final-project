<?php

namespace App\Containers\EquipmentContainer\Models;

use App\Containers\EquipmentContainer\Factories\EquipmentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static EquipmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Equipment newModelQuery()
 * @method static Builder<static>|Equipment newQuery()
 * @method static Builder<static>|Equipment query()
 * @method static Builder<static>|Equipment whereCreatedAt($value)
 * @method static Builder<static>|Equipment whereEndAt($value)
 * @method static Builder<static>|Equipment whereId($value)
 * @method static Builder<static>|Equipment whereRoomId($value)
 * @method static Builder<static>|Equipment whereStartAt($value)
 * @method static Builder<static>|Equipment whereStatus($value)
 * @method static Builder<static>|Equipment whereUpdatedAt($value)
 * @method static Builder<static>|Equipment whereUserId($value)
 * @mixin Eloquent
 */
class Equipment extends Model
{
    /** @var string */
    protected $table = 'equipments';

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
     * @return EquipmentFactory|Factory
     */
    protected static function newFactory(): EquipmentFactory|Factory {
        return EquipmentFactory::new();
    }
}
