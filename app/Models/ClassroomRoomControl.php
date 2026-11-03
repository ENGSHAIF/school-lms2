<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomRoomControl extends Model
{
    protected $fillable = [
        'room_id',
        'policy',
        'is_live',
        'requires_admission',
        'kicked_user_ids',
        'whiteboard_state',
        'whiteboard_version',
        'laser_state',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'requires_admission' => 'boolean',
        'kicked_user_ids' => 'array',
        'whiteboard_version' => 'integer',
        'laser_state' => 'array',
    ];

    public static function getControlForRoom(string $roomId): self
    {
        return self::firstOrCreate(
            ['room_id' => $roomId],
            [
                'policy' => 'open',
                'is_live' => true,
                'requires_admission' => false,
                'kicked_user_ids' => [],
                'whiteboard_version' => 0,
            ]
        );
    }
}
