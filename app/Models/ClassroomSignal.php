<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomSignal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'from_peer_id',
        'to_peer_id',
        'signal_type',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
