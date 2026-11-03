<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    protected static function booted(): void
    {
        static::saved(function (self $model) {
            Cache::forget("room_control_id_{$model->room_id}");
        });

        static::deleted(function (self $model) {
            Cache::forget("room_control_id_{$model->room_id}");
        });
    }

    public static function getControlForRoom(string $roomId): self
    {
        $cachedId = Cache::remember("room_control_id_{$roomId}", 60, function () use ($roomId) {
            $ctrl = self::firstOrCreate(
                ['room_id' => $roomId],
                [
                    'policy' => 'open',
                    'is_live' => true,
                    'requires_admission' => false,
                    'kicked_user_ids' => [],
                    'whiteboard_version' => 0,
                ]
            );

            return $ctrl->id;
        });

        $model = self::find($cachedId);
        if (! $model) {
            Cache::forget("room_control_id_{$roomId}");

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

        return $model;
    }
}
