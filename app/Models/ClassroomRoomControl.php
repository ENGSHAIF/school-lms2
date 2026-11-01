<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomRoomControl extends Model
{
    protected $fillable = [
        'room_id',
        'policy',
        'is_live',
        'kicked_user_ids',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'kicked_user_ids' => 'array',
    ];

    public static function getControlForRoom(string $roomId): self
    {
        return self::firstOrCreate(
            ['room_id' => $roomId],
            ['policy' => 'open', 'is_live' => true, 'kicked_user_ids' => []]
        );
    }
}
