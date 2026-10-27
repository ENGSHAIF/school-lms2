<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'birth_place',
        'birth_date',
        'educational_level',
        'study_phone_whatsapp',
        'parent_phone',
        'needs_placement_test',
        'course_interest',
        'notes',
        'term',
        'academic_number',
        'course_assigned',
        'program_duration',
        'status',
        'user_id',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'needs_placement_test' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getCleanStudyPhoneAttribute(): string
    {
        return preg_replace('/[^0-9]/', '', (string) $this->study_phone_whatsapp);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'قيد المراجعة ⏳',
            'contacted' => 'تم التواصل 📞',
            'approved' => 'معتمد ومقبول ✅',
            'rejected' => 'مرفوض ❌',
            default => 'غير محدد',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'contacted' => 'bg-blue-100 text-blue-800 border-blue-300',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
