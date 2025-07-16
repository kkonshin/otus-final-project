<?php

namespace App\Containers\BookingContainer\Models;

use App\Containers\BookingContainer\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
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
     * @return BookingFactory|Factory
     */
    protected static function newFactory(): BookingFactory|Factory {
        return BookingFactory::new();
    }
}
