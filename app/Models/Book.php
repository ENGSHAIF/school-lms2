<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'level_id',
        'title',
        'description',
        'file_path',
        'file_size',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
