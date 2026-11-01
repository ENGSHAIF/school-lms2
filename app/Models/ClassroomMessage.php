<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomMessage extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'type',
        'content',
        'file_path',
        'file_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function isImage(): bool
    {
        return in_array($this->type, ['image', 'worksheet']);
    }
}
