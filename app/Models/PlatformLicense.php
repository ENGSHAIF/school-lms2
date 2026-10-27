<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformLicense extends Model
{
    use HasFactory;

    protected $table = 'platform_licenses';

    protected $fillable = [
        'title',
        'license_number',
        'issuing_authority',
        'image_path',
        'issue_date',
        'expiry_date',
        'notes',
        'status',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
