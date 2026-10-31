<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order_index',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function resources()
    {
        return $this->hasMany(LevelResource::class, 'course_section_id')->orderBy('order_index');
    }

    public function activeResources()
    {
        return $this->resources()->where('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
