<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroupStudent extends Model
{
    protected $fillable = [
        'study_group_id',
        'student_id',
        'status',
        'enrolled_at',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
