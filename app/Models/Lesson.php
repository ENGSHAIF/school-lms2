<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'level_id',
        'teacher_id',
        'title',
        'description',
        'video_url',
        'duration_minutes',
        'order_index',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
