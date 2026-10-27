<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
        'status',
        'enrolled_at',
        'completed_at',
        'approved_by',
        'approved_at',
        'revoked_by',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }
}
