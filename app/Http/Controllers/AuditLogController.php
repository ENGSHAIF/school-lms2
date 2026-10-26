<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\SystemAuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * التحقق من صلاحية الوصول لمركز الرقابة والتدقيق
     */
    protected function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports') && !$user->hasPermission('system.audit_view'))) {
            abort(403, 'عذراً، ليس لديك صلاحية الوصول لمركز الرقابة وسجلات التدقيق.');
        }
        return $user;
    }

    /**
     * العرض الرئيسي لمركز الرقابة والتدقيق الشامل
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $modules = SystemAuditLog::getModules();
        $surveillance = SystemAuditLog::getSurveillanceStatus();

        // بناء استعلام السجلات الرقابية مع الفلاتر
        $query = SystemAuditLog::with('user')->latest();

        // 1. فلتر البحث النصي (الكلمة المفتاحية)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('user_code', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // 2. فلتر المستخدم المنفذ للعملية
        if ($request->filled('user_id')) {
            $query->where('user_id', (int)$request->user_id);
        }

        // 3. فلتر القسم والوحدة (Module)
        if ($request->filled('module') && $request->module !== 'all') {
            $modKey = $request->module;
            $query->where(function ($q) use ($modKey) {
                $q->where('details->module', $modKey);

                // توافق مع السجلات القديمة عبر بادئات الـ action
                if ($modKey === 'finance') {
                    $q->orWhere('action', 'like', 'voucher.%')
                      ->orWhere('action', 'like', 'cash_box.%')
                      ->orWhere('action', 'like', 'teacher_entitlement.%');
                } elseif ($modKey === 'pricing') {
                    $q->orWhere('action', 'like', '%pricing%')
                      ->orWhere('action', 'like', 'course.pricing%')
                      ->orWhere('action', 'like', 'level.pricing%')
                      ->orWhere('action', 'like', 'package.pricing%');
                } elseif ($modKey === 'users') {
                    $q->orWhere('action', 'like', 'user.%')
                      ->orWhere('action', 'like', 'auth.%');
                } elseif ($modKey === 'exams') {
                    $q->orWhere('action', 'like', 'grade.%')
                      ->orWhere('action', 'like', 'certificate.%')
                      ->orWhere('action', 'like', 'exam.%');
                } elseif ($modKey === 'settings') {
                    $q->orWhere('action', 'like', 'whatsapp.%')
                      ->orWhere('action', 'like', 'setting.%');
                }
            });
        }

        // 4. فلتر نوع الإجراء (Action Type)
        if ($request->filled('action_type') && $request->action_type !== 'all') {
            $type = $request->action_type;
            if ($type === 'delete') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%delete%')
                      ->orWhere('action', 'like', '%destroy%')
                      ->orWhere('action', 'like', '%cancel%');
                });
            } elseif ($type === 'update') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%update%')
                      ->orWhere('action', 'like', '%edit%')
                      ->orWhere('action', 'like', '%reconcile%');
                });
            } elseif ($type === 'create') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%create%')
                      ->orWhere('action', 'like', '%store%')
                      ->orWhere('action', 'like', '%batch%');
                });
            } elseif ($type === 'pricing') {
                $query->where('action', 'like', '%pricing%');
            } elseif ($type === 'auth') {
                $query->where(function ($q) {
                    $q->where('action', 'like', 'auth.%')
                      ->orWhere('action', 'like', '%login%')
                      ->orWhere('action', 'like', '%logout%');
                });
            }
        }

        // 5. فلتر التاريخ من وإلى
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // الإحصائيات العامة لمركز الرقابة
        $stats = [
            'total' => SystemAuditLog::count(),
            'today' => SystemAuditLog::whereDate('created_at', Carbon::today())->count(),
            'deletions' => SystemAuditLog::where(function ($q) {
                $q->where('action', 'like', '%delete%')
                  ->orWhere('action', 'like', '%destroy%')
                  ->orWhere('action', 'like', '%cancel%');
            })->count(),
            'finance' => SystemAuditLog::where(function ($q) {
                $q->where('details->module', 'finance')
                  ->orWhere('action', 'like', 'voucher.%')
                  ->orWhere('action', 'like', 'cash_box.%');
            })->count(),
            'pricing' => SystemAuditLog::where('action', 'like', '%pricing%')->count(),
        ];

        // قائمة المستخدمين لفلتر البحث
        $usersList = User::whereIn('role', ['admin', 'supervisor', 'accountant', 'teacher'])
            ->orWhereIn('id', SystemAuditLog::select('user_id')->distinct())
            ->orderBy('name')
            ->get();

        $logs = $query->paginate(30)->appends($request->query());

        return view('admin.audit_logs.index', compact(
            'logs',
            'modules',
            'surveillance',
            'stats',
            'usersList'
        ));
    }

    /**
     * حفظ وتحديث إعدادات الرقابة والمفاتيح العامة والفرعية
     */
    public function updateSettings(Request $request)
    {
        $this->authorizeAccess();

        // 1. المفتاح الرئيسي للرقابة
        $masterEnabled = $request->has('audit_master_enabled') ? '1' : '0';
        SiteSetting::set('audit_master_enabled', $masterEnabled, 'audit');

        // 2. مفاتيح الوحدات الفردية
        $modules = SystemAuditLog::getModules();
        foreach ($modules as $key => $mod) {
            $val = $request->has("audit_module_{$key}") ? '1' : '0';
            SiteSetting::set($mod['setting_key'], $val, 'audit');
        }

        // 3. فترة الاحتفاظ بالسجلات (بالأيام)
        $retentionDays = max(7, (int)$request->input('audit_retention_days', 90));
        SiteSetting::set('audit_retention_days', (string)$retentionDays, 'audit');

        // توثيق تعديل إعدادات الرقابة نفسها
        SystemAuditLog::record(
            'setting.audit_control_update',
            "تحديث وضبط إعدادات ومفاتيح نظام الرقابة والتدقيق الشامل (المفتاح الرئيسي: " . ($masterEnabled === '1' ? 'مفعل' : 'معطل') . ")",
            null,
            [
                'master_enabled' => $masterEnabled === '1',
                'retention_days' => $retentionDays,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ وتحديث إعدادات نظام الرقابة بنجاح 🛡️',
                'surveillance' => SystemAuditLog::getSurveillanceStatus(),
            ]);
        }

        return redirect()->back()->with('success', 'تم حفظ وتحديث إعدادات ومفاتيح نظام الرقابة والتدقيق بنجاح 🛡️');
    }

    /**
     * عرض تفاصيل سجل محدد (JSON للنافذة المنبثقة)
     */
    public function show($id)
    {
        $this->authorizeAccess();

        $log = SystemAuditLog::with('user')->findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'action' => $log->action,
            'description' => $log->description,
            'formatted_time' => $log->formatted_date_time,
            'user' => $log->user ? [
                'name' => $log->user->name,
                'user_code' => $log->user->user_code,
                'role' => $log->user->role,
            ] : null,
            'ip' => $log->ip_address,
            'device' => $log->device_display,
            'module' => $log->module_info,
            'badge' => $log->action_badge,
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'diff' => $log->details['diff'] ?? null,
            'details' => $log->details,
        ]);
    }

    /**
     * طباعة تقرير رقابي رسمي معتمد
     */
    public function printReport(Request $request)
    {
        $this->authorizeAccess();

        $query = SystemAuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('description', 'like', "%{$search}%");
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int)$request->user_id);
        }
        if ($request->filled('module') && $request->module !== 'all') {
            $modKey = $request->module;
            $query->where('details->module', $modKey);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(500)->get();

        return view('admin.audit_logs.print', compact('logs'));
    }

    /**
     * تصدير السجلات الرقابية كملف CSV معتمد
     */
    public function exportCsv(Request $request)
    {
        $this->authorizeAccess();

        $query = SystemAuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . trim($request->search) . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int)$request->user_id);
        }
        if ($request->filled('module') && $request->module !== 'all') {
            $query->where('details->module', $request->module);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(2000)->get();

        $filename = "audit_report_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            // إضافة BOM للتعامل الصحيح مع اللغة العربية في Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'رقم السجل',
                'التاريخ والوقت',
                'المستخدم المنفذ',
                'رقم المستخدم',
                'القسم / الوحدة',
                'نوع الإجراء',
                'بيان العملية',
                'عنوان IP',
                'اسم الجهاز والمتصفح',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->formatted_date_time,
                    $log->user ? $log->user->name : 'النظام / تلقائي',
                    $log->user ? $log->user->user_code : '-',
                    $log->module_info['name'],
                    $log->action_badge['label'],
                    $log->description,
                    $log->ip_address,
                    $log->device_display,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * تنظيف وحذف السجلات القديمة مع الحفاظ الإلزامي على السجلات المالية الحساسة
     */
    public function clearOld(Request $request)
    {
        $user = $this->authorizeAccess();

        if (!$user->isAdmin()) {
            return redirect()->back()->with('error', 'عذراً، صلاحية مسح السجلات القديمة مقتصرة حصرياً على مدير النظام.');
        }

        $days = (int)$request->input('days', SiteSetting::get('audit_retention_days', 90));
        $keepFinancial = $request->boolean('keep_financial', true);

        $deletedCount = SystemAuditLog::pruneOld($days, $keepFinancial);

        // توثيق عملية مسح السجلات
        SystemAuditLog::record(
            'system.audit_prune',
            "قام مدير النظام ({$user->name}) بتنظيف الأرشيف الرقابي وحذف ({$deletedCount}) سجل أقدم من ({$days}) يوم مع " . ($keepFinancial ? "حماية العمليات المالية" : "مسح كامل"),
            null,
            ['deleted_count' => $deletedCount, 'days' => $days, 'keep_financial' => $keepFinancial]
        );

        return redirect()->back()->with('success', "تم بنجاح تنظيف وأرشفة ({$deletedCount}) سجل رقابي أقدم من ({$days}) يوماً مع الحفاظ على الأمان 🧹");
    }
}
