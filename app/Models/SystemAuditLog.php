<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SystemAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'details',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * قائمة الوحدات والأقسام الرقابية في النظام مع إعداداتها وألوانها
     */
    public static function getModules(): array
    {
        return [
            'finance' => [
                'key' => 'finance',
                'name' => 'المالية والسندات والصناديق',
                'setting_key' => 'audit_module_finance',
                'icon' => '💳',
                'color' => 'emerald',
                'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'description' => 'مراقبة إصدار وتعديل وحذف سندات القبض والصرف، تسوية الصناديق، تحصيل الأقساط والمصروفات.',
            ],
            'pricing' => [
                'key' => 'pricing',
                'name' => 'الأسعار والمناهج والدورات',
                'setting_key' => 'audit_module_pricing',
                'icon' => '🏷️',
                'color' => 'amber',
                'badge_bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'description' => 'مراقبة تعديل أسعار المناهج والدورات، رسوم الطلاب، أجور المعلمين، إنشاء وتعديل الباقات والمستويات.',
            ],
            'students' => [
                'key' => 'students',
                'name' => 'شؤون الطلاب والمجموعات',
                'setting_key' => 'audit_module_students',
                'icon' => '👨‍🎓',
                'color' => 'blue',
                'badge_bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'description' => 'مراقبة تسجيل الطلاب، التوزيع على المجموعات، تعديل المستويات، تغيير حالات الطلاب.',
            ],
            'exams' => [
                'key' => 'exams',
                'name' => 'الاختبارات والدرجات والشهائد',
                'setting_key' => 'audit_module_exams',
                'icon' => '🎯',
                'color' => 'purple',
                'badge_bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'description' => 'مراقبة تعديل درجات الطلاب، اعتماد الاختبارات والكويزات، إصدار الشهادات واختبارات المستوى.',
            ],
            'users' => [
                'key' => 'users',
                'name' => 'المستخدمين والصلاحيات والأمان',
                'setting_key' => 'audit_module_users',
                'icon' => '👥',
                'color' => 'rose',
                'badge_bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'description' => 'مراقبة تسجيل الدخول والخروج، تغيير كلمات المرور، تعديل الصلاحيات والأدوار، حذف الحسابات.',
            ],
            'settings' => [
                'key' => 'settings',
                'name' => 'إعدادات النظام والخدمات',
                'setting_key' => 'audit_module_settings',
                'icon' => '⚙️',
                'color' => 'slate',
                'badge_bg' => 'bg-slate-100 text-slate-700 border-slate-300',
                'description' => 'مراقبة تغيير إعدادات المنصة، الهوية البصرية، مفاتيح الربط، نظام الرسائل والواتساب.',
            ],
        ];
    }

    /**
     * استنتاج القسم التابع له الإجراء
     */
    public static function getModuleFromAction(string $action): string
    {
        $action = strtolower($action);

        if (
            str_starts_with($action, 'voucher.') ||
            str_starts_with($action, 'cash_box.') ||
            str_starts_with($action, 'payment.') ||
            str_starts_with($action, 'receipt.') ||
            str_starts_with($action, 'installment.') ||
            str_starts_with($action, 'account.') ||
            str_starts_with($action, 'financial.')
        ) {
            return 'finance';
        }

        if (
            str_contains($action, 'pricing') ||
            str_starts_with($action, 'course.fee') ||
            str_starts_with($action, 'package.') ||
            str_starts_with($action, 'level.')
        ) {
            return 'pricing';
        }

        if (
            str_starts_with($action, 'student.') ||
            str_starts_with($action, 'student_level.') ||
            str_starts_with($action, 'enrollment.') ||
            str_starts_with($action, 'study_group.')
        ) {
            return 'students';
        }

        if (
            str_starts_with($action, 'exam.') ||
            str_starts_with($action, 'quiz.') ||
            str_starts_with($action, 'grade.') ||
            str_starts_with($action, 'certificate.') ||
            str_starts_with($action, 'placement.')
        ) {
            return 'exams';
        }

        if (
            str_starts_with($action, 'user.') ||
            str_starts_with($action, 'auth.') ||
            str_starts_with($action, 'role.') ||
            str_starts_with($action, 'permission.') ||
            str_contains($action, 'login') ||
            str_contains($action, 'logout')
        ) {
            return 'users';
        }

        if (
            str_starts_with($action, 'setting.') ||
            str_starts_with($action, 'system.') ||
            str_starts_with($action, 'whatsapp.')
        ) {
            return 'settings';
        }

        return 'general';
    }

    /**
     * التحقق هل المراقبة مفعلة لهذا الإجراء بحسب المفتاح الرئيسي ومفتاح الوحدة
     */
    public static function isLoggingEnabledFor(string $action, ?string $module = null): bool
    {
        // 1. فحص المفتاح الرئيسي للمراقبة الشاملة (Master Switch)
        $masterEnabled = SiteSetting::get('audit_master_enabled', '1');
        if ($masterEnabled === '0' || $masterEnabled === false || $masterEnabled === 'off') {
            return false;
        }

        // 2. فحص مفتاح الوحدة التابع لها الإجراء
        $module = $module ?? static::getModuleFromAction($action);
        $settingKey = "audit_module_{$module}";

        $moduleEnabled = SiteSetting::get($settingKey, '1');
        if ($moduleEnabled === '0' || $moduleEnabled === false || $moduleEnabled === 'off') {
            return false;
        }

        return true;
    }

    /**
     * تحليل السلسلة النصية للمتصفح (User Agent) واستخراج اسم الجهاز ونظام التشغيل والمتصفح بدقة
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'os' => 'غير محدد',
                'browser' => 'غير محدد',
                'device_type' => 'desktop',
                'device_icon' => '💻',
                'device_name' => '💻 جهاز غير محدد',
            ];
        }

        // 1. نظام التشغيل (OS)
        $os = 'نظام غير معروف';
        if (preg_match('/iPhone/i', $userAgent)) {
            preg_match('/OS\s*([0-9_]+)/i', $userAgent, $m);
            $ver = !empty($m[1]) ? str_replace('_', '.', $m[1]) : '';
            $os = $ver ? "iOS {$ver} (iPhone)" : 'iPhone (iOS)';
        } elseif (preg_match('/iPad/i', $userAgent)) {
            preg_match('/OS\s*([0-9_]+)/i', $userAgent, $m);
            $ver = !empty($m[1]) ? str_replace('_', '.', $m[1]) : '';
            $os = $ver ? "iPadOS {$ver} (iPad)" : 'iPad (iPadOS)';
        } elseif (preg_match('/Android/i', $userAgent)) {
            preg_match('/Android\s*([0-9\.]+)/i', $userAgent, $m);
            $os = !empty($m[1]) ? "Android {$m[1]}" : 'Android';
        } elseif (preg_match('/Windows NT 10\.0/i', $userAgent)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6\.3/i', $userAgent)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6\.2/i', $userAgent)) {
            $os = 'Windows 8';
        } elseif (preg_match('/Windows NT 6\.1/i', $userAgent)) {
            $os = 'Windows 7';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            $os = 'macOS Apple';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        }

        // 2. المتصفح (Browser)
        $browser = 'متصفح غير معروف';
        if (preg_match('/Edg\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/OPR\/([0-9\.]+)/i', $userAgent) || preg_match('/Opera\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/Safari\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Apple Safari';
        }

        // 3. نوع الجهاز (Device Type)
        $deviceType = 'desktop';
        $deviceIcon = '💻';
        if (preg_match('/iPad|tablet/i', $userAgent)) {
            $deviceType = 'tablet';
            $deviceIcon = '📟';
        } elseif (preg_match('/Mobile|iPhone|Android/i', $userAgent)) {
            $deviceType = 'mobile';
            $deviceIcon = '📱';
        }

        $typeNameAr = match($deviceType) {
            'mobile' => 'هاتف محمول',
            'tablet' => 'جهاز لوحي',
            default => 'كمبيوتر مكتبي / محمول',
        };

        return [
            'os' => $os,
            'browser' => $browser,
            'device_type' => $deviceType,
            'device_icon' => $deviceIcon,
            'device_name' => "{$deviceIcon} {$typeNameAr} ({$os} - {$browser})",
        ];
    }

    /**
     * تسجيل حركة أو عملية إدارية / مالية في سجل تدقيق النظام مع دعم الرقابة المتقدمة
     */
    public static function record(
        string $action,
        string $description,
        $model = null,
        ?array $details = null,
        ?int $userId = null
    ): ?self {
        // فحص هل المراقبة مفعلة للإجراء والوحدة
        if (!static::isLoggingEnabledFor($action)) {
            return null;
        }

        $userId = $userId ?? auth()->id();
        $ip = request()?->ip() ?? '127.0.0.1';
        $ua = request()?->userAgent();
        $deviceInfo = static::parseUserAgent($ua);
        $module = static::getModuleFromAction($action);

        $details = $details ?? [];
        $details['module'] = $details['module'] ?? $module;
        $details['device_info'] = [
            'name' => $deviceInfo['device_name'],
            'os' => $deviceInfo['os'],
            'browser' => $deviceInfo['browser'],
            'type' => $deviceInfo['device_type'],
            'icon' => $deviceInfo['device_icon'],
            'ip' => $ip,
        ];

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? (is_string($model) ? $model : get_class($model)) : null,
            'model_id' => ($model && is_object($model) && method_exists($model, 'getKey')) ? $model->getKey() : null,
            'description' => $description,
            'details' => $details,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);
    }

    /**
     * حالة المراقبة الشاملة لكافة الأقسام والمفتاح الرئيسي
     */
    public static function getSurveillanceStatus(): array
    {
        $modules = static::getModules();
        $master = SiteSetting::get('audit_master_enabled', '1');
        $masterEnabled = ($master === '1' || $master === true || $master === 'on' || $master === 1);

        $moduleStatuses = [];
        foreach ($modules as $key => $mod) {
            $val = SiteSetting::get($mod['setting_key'], '1');
            $moduleStatuses[$key] = ($val === '1' || $val === true || $val === 'on' || $val === 1);
        }

        $retentionDays = (int) SiteSetting::get('audit_retention_days', 90);

        return [
            'master_enabled' => $masterEnabled,
            'modules' => $moduleStatuses,
            'retention_days' => $retentionDays,
        ];
    }

    /**
     * حذف السجلات القديمة تلقائياً أو يدوياً
     */
    public static function pruneOld(int $days = 90, bool $keepFinancial = true): int
    {
        $cutoffDate = Carbon::now()->subDays($days);
        $query = static::where('created_at', '<', $cutoffDate);

        if ($keepFinancial) {
            $query->whereNotIn('action', [
                'voucher.create', 'voucher.receipt_batch_create', 'voucher.payment_create',
                'voucher.update', 'voucher.delete', 'cash_box.reconcile'
            ]);
        }

        return $query->delete();
    }

    /* -------------------------------------------------------------------------- */
    /* Accessors & Mutators للواجهات والتقارير                                   */
    /* -------------------------------------------------------------------------- */

    public function getDeviceDisplayAttribute(): string
    {
        if (!empty($this->details['device_info']['name'])) {
            return $this->details['device_info']['name'];
        }

        $parsed = static::parseUserAgent($this->user_agent);
        return $parsed['device_name'];
    }

    public function getDeviceOsAttribute(): string
    {
        return $this->details['device_info']['os'] ?? static::parseUserAgent($this->user_agent)['os'];
    }

    public function getDeviceBrowserAttribute(): string
    {
        return $this->details['device_info']['browser'] ?? static::parseUserAgent($this->user_agent)['browser'];
    }

    public function getDeviceIconAttribute(): string
    {
        return $this->details['device_info']['icon'] ?? static::parseUserAgent($this->user_agent)['device_icon'];
    }

    public function getModuleAttribute(): string
    {
        return $this->details['module'] ?? static::getModuleFromAction($this->action);
    }

    public function getModuleInfoAttribute(): array
    {
        $modules = static::getModules();
        $moduleKey = $this->module;
        return $modules[$moduleKey] ?? [
            'key' => 'general',
            'name' => 'عام / غير مصنف',
            'icon' => '📌',
            'color' => 'slate',
            'badge_bg' => 'bg-slate-100 text-slate-700 border-slate-300',
            'description' => 'عمليات وإجراءات عامة في النظام',
        ];
    }

    public function getActionBadgeAttribute(): array
    {
        $action = strtolower($this->action);

        if (str_contains($action, 'delete') || str_contains($action, 'destroy') || str_contains($action, 'cancel')) {
            return [
                'type' => 'delete',
                'label' => 'حذف / إلغاء مستند',
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icon' => '🗑️',
            ];
        }

        if (str_contains($action, 'update') || str_contains($action, 'edit') || str_contains($action, 'reconcile')) {
            return [
                'type' => 'update',
                'label' => 'تعديل بيانات',
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'icon' => '✏️',
            ];
        }

        if (str_contains($action, 'pricing')) {
            return [
                'type' => 'pricing',
                'label' => 'تغيير تسعيرة',
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icon' => '🏷️',
            ];
        }

        if (str_contains($action, 'login') || str_contains($action, 'auth')) {
            return [
                'type' => 'auth',
                'label' => 'أمان وتسجيل دخول',
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'icon' => '🔐',
            ];
        }

        return [
            'type' => 'create',
            'label' => 'إنشاء / إصدار جديد',
            'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'icon' => '✨',
        ];
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : '';
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('h:i:s A') : '';
    }

    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->created_at) return '';
        $amPm = $this->created_at->format('A') === 'AM' ? 'ص' : 'م';
        return $this->created_at->format('Y/m/d - h:i:s ') . $amPm;
    }

    public function getHasDiffAttribute(): bool
    {
        return !empty($this->details['old_values']) || !empty($this->details['old_amount']) || !empty($this->details['old_fee']) || !empty($this->details['diff']);
    }

    public function getOldValuesAttribute(): ?array
    {
        if (isset($this->details['old_values']) && is_array($this->details['old_values'])) {
            return $this->details['old_values'];
        }

        // تحويل الحقول الفردية مثل old_amount, old_status, etc. إلى مصفوفة قابلة للعرض
        $auto = [];
        if (isset($this->details['old_amount'])) {
            $auto['المبلغ السابق'] = number_format((float)$this->details['old_amount'], 2);
        }
        if (isset($this->details['old_fee'])) {
            $auto['الرسم السابق'] = number_format((float)$this->details['old_fee'], 2);
        }
        if (isset($this->details['old_hours'])) {
            $auto['الساعات السابقة'] = $this->details['old_hours'] . ' ساعة';
        }
        if (isset($this->details['old_status'])) {
            $auto['الحالة السابقة'] = $this->details['old_status'];
        }
        if (isset($this->details['old_box'])) {
            $auto['الصندوق السابق'] = "صندوق رقم #{$this->details['old_box']}";
        }

        return !empty($auto) ? $auto : null;
    }

    public function getNewValuesAttribute(): ?array
    {
        if (isset($this->details['new_values']) && is_array($this->details['new_values'])) {
            return $this->details['new_values'];
        }

        $auto = [];
        if (isset($this->details['new_amount'])) {
            $auto['المبلغ الجديد'] = number_format((float)$this->details['new_amount'], 2);
        }
        if (isset($this->details['new_fee'])) {
            $auto['الرسم الجديد'] = number_format((float)$this->details['new_fee'], 2);
        }
        if (isset($this->details['new_hours'])) {
            $auto['الساعات الجديدة'] = $this->details['new_hours'] . ' ساعة';
        }
        if (isset($this->details['new_status'])) {
            $auto['الحالة الجديدة'] = $this->details['new_status'];
        }
        if (isset($this->details['new_box'])) {
            $auto['الصندوق الجديد'] = "صندوق رقم #{$this->details['new_box']}";
        }

        return !empty($auto) ? $auto : null;
    }
}
