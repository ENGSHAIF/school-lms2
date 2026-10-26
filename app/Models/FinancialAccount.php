<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    protected $fillable = [
        'parent_id',
        'level',
        'is_parent',
        'account_code',
        'account_name',
        'account_type',
        'analytical_type',
        'account_category',
        'nature',
        'allow_cost_center',
        'box_nature',
        'box_category',
        'user_id',
        'custodian_id',
        'currency',
        'bank_name',
        'account_number',
        'opening_balance',
        'current_balance',
        'description',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_parent' => 'boolean',
        'allow_cost_center' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * إلزام قاعدة الرتبة الخامسة الصارمة:
     * - كل الحسابات في الرتب (1، 2، 3، 4) تكون حسابات رئيسية تجميعية (is_parent = true).
     * - الرتبة الخامسة فقط تكون حسابات فرعية تقبل الحركات (is_parent = false).
     */
    protected static function booted()
    {
        static::saving(function ($account) {
            // ربط آلي بحساب المراقبة المناسب في الرتبة الرابعة إذا لم يتم تحديد أب يدوياً
            if (empty($account->parent_id) && $account->level != 1) {
                if ($account->account_type === 'student') {
                    $parent = static::where('account_code', '1121')->first();
                    if ($parent) $account->parent_id = $parent->id;
                } elseif ($account->account_type === 'teacher') {
                    $parent = static::where('account_code', '2121')->first();
                    if ($parent) $account->parent_id = $parent->id;
                } elseif ($account->account_type === 'cash_box') {
                    $parent = static::where('account_code', '1111')->first();
                    if ($parent) $account->parent_id = $parent->id;
                } elseif ($account->account_type === 'bank') {
                    $parent = static::where('account_code', '1112')->first();
                    if ($parent) $account->parent_id = $parent->id;
                }
            }

            if ($account->parent_id) {
                $parent = static::find($account->parent_id);
                if ($parent) {
                    $account->level = min($parent->level + 1, 5);
                    if (empty($account->account_category)) {
                        $account->account_category = $parent->account_category;
                    }
                }
            }

            if ($account->level < 5) {
                $account->is_parent = true;
            } else {
                $account->level = 5;
                $account->is_parent = false;
            }
        });
    }

    public function isLeaf(): bool
    {
        return $this->level === 5 && !$this->is_parent;
    }

    public function scopePostable($query)
    {
        return $query->where('level', 5)->where('is_parent', false);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function parent()
    {
        return $this->belongsTo(FinancialAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FinancialAccount::class, 'parent_id')->orderBy('account_code');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'account_id');
    }

    public function boxVouchers()
    {
        return $this->hasMany(Voucher::class, 'cash_or_bank_account_id');
    }

    // دوال الاستعلام المخصصة (Scopes)
    public function scopeCashBoxesAndBanks($query)
    {
        return $query->whereIn('account_type', ['cash_box', 'bank']);
    }

    public function scopeReceiptAllowed($query)
    {
        return $query->whereIn('account_type', ['cash_box', 'bank'])
                     ->whereIn('box_nature', ['both', 'receipt', 'pos'])
                     ->where('is_active', true);
    }

    public function scopePaymentAllowed($query)
    {
        return $query->whereIn('account_type', ['cash_box', 'bank'])
                     ->whereIn('box_nature', ['both', 'payment'])
                     ->where('is_active', true);
    }

    // مسميات طبيعة وتصنيف الصندوق بالعربية
    public function getBoxNatureLabelAttribute(): string
    {
        return match($this->box_nature) {
            'receipt' => 'قبض فقط (تحصيل إيراد)',
            'payment' => 'صرف فقط (مصروفات وعُهد)',
            'pos'     => 'نقطة بيع (كاشير استقبال)',
            default   => 'قبض وصرف (عام)',
        };
    }

    public function getBoxCategoryLabelAttribute(): string
    {
        return match($this->box_category) {
            'main_box'        => 'صندوق رئيسي',
            'sub_box'         => 'صندوق فرعي',
            'general_revenue' => 'صندوق إيراد عام',
            'pos'             => 'نقطة بيع / كاشير',
            'bank'            => 'حساب بنكي',
            'electronic_wallet' => 'محفظة إلكترونية',
            default           => 'صندوق نقدي',
        };
    }

    public function getCurrencyNameAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::format($this->current_balance, $this->currency);
    }

    // احتساب إجمالي الرصيد التراكمي (يشمل أرصدة الصناديق الفرعية إذا كان صندوقاً رئيسياً)
    public function getTotalBalanceAttribute(): float
    {
        $sum = (float)$this->current_balance;
        if ($this->children()->exists()) {
            $sum += (float)$this->children()->sum('current_balance');
        }
        return $sum;
    }

    public function getFormattedTotalBalanceAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::format($this->total_balance, $this->currency);
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function getAccountCategoryLabelAttribute(): string
    {
        return match($this->account_category) {
            'asset'     => 'الأصول (Assets)',
            'liability' => 'الخصوم (Liabilities)',
            'equity'    => 'حقوق الملكية (Equity)',
            'revenue'   => 'الإيرادات (Revenues)',
            'expense'   => 'المصروفات (Expenses)',
            default     => 'أصول',
        };
    }

    public function getNatureLabelAttribute(): string
    {
        return $this->nature === 'credit' ? 'دائن (Credit)' : 'مدين (Debit)';
    }

    public function getFullDisplayNameAttribute(): string
    {
        return "[{$this->account_code}] {$this->account_name}";
    }

    public function getAnalyticalTypeLabelAttribute(): string
    {
        return match($this->analytical_type) {
            'student'   => 'طالب (دارس)',
            'teacher'   => 'مدرس / مدرب',
            'employee'  => 'موظف / إداري',
            'parent'    => 'ولي أمر',
            'cash_box'  => 'صندوق نقدي',
            'bank'      => 'بنك / مصرف',
            'customer'  => 'عميل / جهة',
            'supplier'  => 'مورد / التزامات',
            'revenue'   => 'إيراد نشاط',
            'expense'   => 'مصروف تشغيلي',
            default     => 'عام / غير محدد',
        };
    }

    public function getAnalyticalTypeBadgeAttribute(): string
    {
        return match($this->analytical_type) {
            'student'   => 'bg-cyan-50 text-cyan-800 border-cyan-300',
            'teacher'   => 'bg-indigo-50 text-indigo-800 border-indigo-300',
            'employee'  => 'bg-violet-50 text-violet-800 border-violet-300',
            'parent'    => 'bg-teal-50 text-teal-800 border-teal-300',
            'cash_box'  => 'bg-emerald-50 text-emerald-800 border-emerald-300',
            'bank'      => 'bg-blue-50 text-blue-800 border-blue-300',
            'customer'  => 'bg-sky-50 text-sky-800 border-sky-300',
            'supplier'  => 'bg-orange-50 text-orange-800 border-orange-300',
            'revenue'   => 'bg-green-50 text-green-800 border-green-300',
            'expense'   => 'bg-rose-50 text-rose-800 border-rose-300',
            default     => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }
}
