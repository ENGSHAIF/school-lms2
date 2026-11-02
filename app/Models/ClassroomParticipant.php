<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomParticipant extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'peer_id',
        'role',
        'has_camera',
        'has_mic',
        'is_hand_raised',
        'is_muted_by_host',
        'admission_status',
        'last_seen_at',
    ];

    protected $casts = [
        'has_camera' => 'boolean',
        'has_mic' => 'boolean',
        'is_hand_raised' => 'boolean',
        'is_muted_by_host' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gte(now()->subSeconds(10));
    }
}
