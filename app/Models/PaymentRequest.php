<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'request_no',
        'request_type',
        'requested_by',
        'beneficiary_name',
        'account_id',
        'cost_center_id',
        'amount',
        'currency',
        'purpose',
        'attachment_path',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'voucher_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'             => 'مسودة',
            'pending_financial' => 'بانتظار المراجعة المالية',
            'pending_manager'   => 'بانتظار اعتماد الإدارة',
            'approved'          => 'معتمد وجاهز للتنفيذ',
            'executed'          => 'تم الصرف/القبض بنجاح',
            'rejected'          => 'مرفوض',
            default             => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft'             => 'bg-slate-100 text-slate-700 border-slate-200',
            'pending_financial' => 'bg-amber-50 text-amber-800 border-amber-200',
            'pending_manager'   => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            'approved'          => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'executed'          => 'bg-teal-50 text-teal-800 border-teal-200',
            'rejected'          => 'bg-rose-50 text-rose-800 border-rose-200',
            default             => 'bg-slate-50 text-slate-800 border-slate-200',
        };
    }
}
