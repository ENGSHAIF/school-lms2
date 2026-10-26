<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWallet extends Model
{
    protected $fillable = [
        'user_id',
        'total_lessons',
        'used_lessons',
        'remaining_lessons',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
