<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'stage',
        'grade_substage',
        'learning_goals',
        'prior_english_exposure',
        'preferred_time',
        'preferred_frequency',
        'parent_consent',
        'is_trial_booked',
    ];

    protected $casts = [
        'learning_goals' => 'array',
        'parent_consent' => 'boolean',
        'is_trial_booked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
