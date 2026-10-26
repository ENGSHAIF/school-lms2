<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\WhatsAppLog;
use App\Models\User;
use App\Models\StudyGroup;
use App\Models\Course;
use App\Models\SystemAuditLog;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class WhatsAppManagementController extends Controller
{
    /**
     * لوحة تحكم وإدارة الواتساب الذكي
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('manage_whatsapp') && !$user->hasPermission('system.settings')) {
            return redirect()->route('admin.dashboard')->with('error', 'عذراً، ليس لديك صلاحية الوصول إلى مركز إدارة الواتساب.');
        }

        $settings = WhatsAppService::getSettings();
        $templates = WhatsAppService::defaultTemplates();

        // إحصاءات الرسائل
        $stats = [
            'total' => WhatsAppLog::count(),
            'sent' => WhatsAppLog::where('status', 'sent')->count(),
            'failed' => WhatsAppLog::where('status', 'failed')->count(),
            'logged' => WhatsAppLog::where('status', 'logged')->count(),
            'suppressed' => WhatsAppLog::where('status', 'suppressed')->count(),
            'today' => WhatsAppLog::whereDate('created_at', today())->count(),
        ];

        // إحصاءات وضوابط المستخدمين
        $userNotificationStats = [
            'total' => User::count(),
            'with_phone' => User::whereNotNull('phone_whatsapp')->where('phone_whatsapp', '!=', '')->count(),
            'enabled' => User::where('whatsapp_notifications_enabled', true)->count(),
            'excluded' => User::where('whatsapp_notifications_enabled', false)->count(),
        ];

        // إدارة المستخدمين واستثناءات الإشعارات (Tab 5)
        $managedUsersQuery = User::query()->orderBy('name');

        if ($request->filled('user_search')) {
            $us = trim($request->user_search);
            $managedUsersQuery->where(function ($q) use ($us) {
                $q->where('name', 'like', "%{$us}%")
                  ->orWhere('phone_whatsapp', 'like', "%{$us}%")
                  ->orWhere('email', 'like', "%{$us}%");
            });
        }

        if ($request->filled('user_role')) {
            $managedUsersQuery->where('role', $request->user_role);
        }

        if ($request->filled('user_status')) {
            if ($request->user_status === 'enabled') {
                $managedUsersQuery->where('whatsapp_notifications_enabled', true);
            } elseif ($request->user_status === 'excluded') {
                $managedUsersQuery->where('whatsapp_notifications_enabled', false);
            }
        }

        $managedUsers = $managedUsersQuery->paginate(15, ['*'], 'users_page')->withQueryString();

        // بيانات الاستهداف للحملات الجماعية
        $studyGroups = StudyGroup::with(['course', 'teacher', 'students'])->orderBy('name')->get();
        $courses = Course::with('level')->orderBy('course_order')->get();
        $usersList = User::select('id', 'name', 'role', 'phone_whatsapp')
            ->whereNotNull('phone_whatsapp')
            ->where('phone_whatsapp', '!=', '')
            ->orderBy('name')
            ->get();

        $audienceCounts = [
            'all_users' => User::whereNotNull('phone_whatsapp')->where('phone_whatsapp', '!=', '')->count(),
            'all_students' => User::where('role', 'student')->whereNotNull('phone_whatsapp')->where('phone_whatsapp', '!=', '')->count(),
            'teachers_and_supervisors' => User::whereIn('role', ['teacher', 'supervisor'])->whereNotNull('phone_whatsapp')->where('phone_whatsapp', '!=', '')->count(),
            'parents' => User::where('role', 'parent')->whereNotNull('phone_whatsapp')->where('phone_whatsapp', '!=', '')->count(),
        ];

        // سجل رسائل الواتساب مع الفلترة والبحث
        $logsQuery = WhatsAppLog::with('user')->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $logsQuery->where(function ($q) use ($s) {
                $q->where('phone_number', 'like', "%{$s}%")
                  ->orWhere('message_body', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('type')) {
            $logsQuery->where('message_type', $request->type);
        }

        if ($request->filled('status')) {
            $logsQuery->where('status', $request->status);
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        return view('admin.whatsapp.index', compact(
            'settings',
            'templates',
            'stats',
            'userNotificationStats',
            'managedUsers',
            'studyGroups',
            'courses',
            'usersList',
            'audienceCounts',
            'logs'
        ));
    }

    /**
     * تحديث إعدادات ربط رقم الواتساب والبوابة ومصفوفة العمليات
     */
    public function updateSettings(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل إعدادات الواتساب.');
        }

        $validated = $request->validate([
            'whatsapp_platform_number' => 'required|string|max:50',
            'whatsapp_gateway_provider' => 'required|in:ultramsg,greenapi,wasender,twilio,custom,web_only,local_gateway',
            'whatsapp_instance_id' => 'nullable|string|max:100',
            'whatsapp_api_token' => 'nullable|string|max:255',
            'whatsapp_api_url' => 'nullable|url|max:255',
            'whatsapp_max_age_hours' => 'nullable|integer|min:0|max:720',
        ]);

        // المفتاح العام وفترة الصلاحية
        SiteSetting::set('whatsapp_master_switch', $request->has('whatsapp_master_switch') ? '1' : '0', 'whatsapp');
        SiteSetting::set('whatsapp_max_age_hours', $request->input('whatsapp_max_age_hours', 24), 'whatsapp');

        // بيانات المزود والرقم
        SiteSetting::set('whatsapp_platform_number', $validated['whatsapp_platform_number'], 'whatsapp');
        SiteSetting::set('whatsapp_number', $validated['whatsapp_platform_number'], 'whatsapp');
        SiteSetting::set('whatsapp_gateway_provider', $validated['whatsapp_gateway_provider'], 'whatsapp');
        SiteSetting::set('whatsapp_instance_id', $validated['whatsapp_instance_id'] ?? '', 'whatsapp');
        SiteSetting::set('whatsapp_api_token', $validated['whatsapp_api_token'] ?? '', 'whatsapp');
        SiteSetting::set('whatsapp_api_url', $validated['whatsapp_api_url'] ?? '', 'whatsapp');
        SiteSetting::set('whatsapp_auto_send_enabled', $request->has('whatsapp_auto_send_enabled') ? '1' : '0', 'whatsapp');

        // مصفوفة العمليات التشغيلية التلقائية المستقلة
        SiteSetting::set('whatsapp_notify_receipt', $request->has('whatsapp_notify_receipt') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_payment', $request->has('whatsapp_notify_payment') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_entitlement', $request->has('whatsapp_notify_entitlement') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_exam_result', $request->has('whatsapp_notify_exam_result') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_evaluation', $request->has('whatsapp_notify_evaluation') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_completion', $request->has('whatsapp_notify_completion') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_welcome', $request->has('whatsapp_notify_welcome') ? '1' : '0', 'whatsapp_matrix');
        SiteSetting::set('whatsapp_notify_reminder', $request->has('whatsapp_notify_reminder') ? '1' : '0', 'whatsapp_matrix');

        SystemAuditLog::record(
            'whatsapp.settings_update',
            "تحديث إعدادات وضوابط ومصفوفة عمليات واتساب المنصة برقم ({$validated['whatsapp_platform_number']})",
            null,
            $validated
        );

        return back()->with('success', 'تم حفظ وتطبيق كافة إعدادات ومصفوفة عمليات الواتساب بنجاح.');
    }

    /**
     * التبديل السريع للمفتاح الرئيسي للواتساب كلياً
     */
    public function toggleMasterSwitch(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل حالة الواتساب العامة.');
        }

        $current = (bool) SiteSetting::get('whatsapp_master_switch', true);
        $newStatus = !$current;
        SiteSetting::set('whatsapp_master_switch', $newStatus ? '1' : '0', 'whatsapp');

        $text = $newStatus ? 'تم تفعيل نظام الواتساب التلقائي بالكامل بنجاح 🟢' : 'تم إيقاف نظام الواتساب كلياً بنجاح (وضع الإيقاف الشامل) 🔴';

        SystemAuditLog::record(
            'whatsapp.master_toggle',
            $text,
            null,
            ['master_switch' => $newStatus]
        );

        return back()->with('success', $text);
    }

    /**
     * تبديل استثناء المستخدم من استلام إشعارات الواتساب التلقائية
     */
    public function toggleUser(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك'], 403);
            }
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل استثناءات المستخدمين.');
        }

        $newStatus = WhatsAppService::toggleUserNotifications((int)$id);
        $user = User::findOrFail($id);

        $statusText = $newStatus ? 'تم تفعيل استلام إشعارات الواتساب التلقائية بنجاح' : 'تم استبعاد وحظر استلام إشعارات الواتساب التلقائية';

        SystemAuditLog::record(
            'whatsapp.user_toggle',
            "تغيير حالة إشعارات الواتساب للمستخدم ({$user->name}) إلى: " . ($newStatus ? 'مفعل للإشعارات' : 'مستثنى ومحظور'),
            $user->id,
            ['enabled' => $newStatus]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'enabled' => $newStatus,
                'message' => "{$statusText} للمستخدم ({$user->name})."
            ]);
        }

        return back()->with('success', "{$statusText} للمستخدم ({$user->name}).");
    }

    /**
     * تطبيق إجراء جماعي على استثناءات المستخدمين
     */
    public function bulkToggleUsers(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل استثناءات المستخدمين.');
        }

        $action = $request->input('bulk_action'); // enable_all, exclude_all, enable_role, exclude_role
        $role = $request->input('target_role');

        $query = User::query();
        if (in_array($action, ['enable_role', 'exclude_role']) && $role) {
            $query->where('role', $role);
        }

        $enable = in_array($action, ['enable_all', 'enable_role']);
        $affected = $query->update(['whatsapp_notifications_enabled' => $enable]);

        $actionText = $enable ? 'تفعيل استلام الإشعارات التلقائية' : 'استبعاد وحظر استلام الإشعارات التلقائية';
        SystemAuditLog::record(
            'whatsapp.bulk_user_toggle',
            "تم {$actionText} لعدد ({$affected}) مستخدم",
            null,
            ['action' => $action, 'role' => $role, 'count' => $affected]
        );

        return back()->with('success', "تم بنجاح {$actionText} لعدد {$affected} مستخدم.");
    }

    /**
     * اختبار الاتصال بالبوابة وإرسال رسالة تجريبية
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
        ]);

        $res = WhatsAppService::testConnection($request->test_phone);

        if ($res['success']) {
            if ($res['status'] === 'sent') {
                return back()->with('success', "تم إرسال الرسالة التجريبية بنجاح إلى الرقم ({$res['formatted_phone']}) عبر البوابة الآلية!");
            } else {
                return back()->with('info', "تم تجهيز الرسالة التجريبية وتوثيقها. يمكنك إرسالها فوراً عبر واتساب ويب من خلال الرابط: " . $res['wa_url']);
            }
        }

        return back()->with('error', "فشل اختبار الاتصال: " . ($res['message'] ?? 'تحقق من صحة مفاتيح الـ API ورقم الهاتف.'));
    }

    /**
     * تحديث نص وحالة قالب رسالة واتساب تلقائي
     */
    public function updateTemplate(Request $request, $key)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل قوالب الرسائل.');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
            'is_enabled' => 'nullable',
        ]);

        SiteSetting::set("wa_template_{$key}_enabled", $request->has('is_enabled') ? '1' : '0', 'whatsapp_templates');
        SiteSetting::set("wa_template_{$key}_body", trim($request->body), 'whatsapp_templates');

        return back()->with('success', "تم تحديث قالب الرسالة بنجاح.");
    }

    /**
     * إرسال حملة رسائل جماعية أو موجهة
     */
    public function sendCampaign(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('manage_whatsapp')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إرسال حملات الواتساب.');
        }

        $validated = $request->validate([
            'target' => 'required|in:all_users,all_students,teachers_and_supervisors,parents,specific_group,specific_course,custom_user,custom_phone',
            'message_type' => 'required|string',
            'message' => 'required|string|max:3000',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'course_id' => 'nullable|exists:courses,id',
            'custom_user_id' => 'nullable|exists:users,id',
            'custom_phone' => 'nullable|string',
        ]);

        $res = WhatsAppService::sendCampaign($validated);

        SystemAuditLog::record(
            'whatsapp.campaign_dispatch',
            "إرسال حملة واتساب استهدفت ({$res['total_target']}) مستلماً بنوع ({$validated['message_type']})",
            null,
            [
                'target' => $validated['target'],
                'total' => $res['total_target'],
                'sent' => $res['sent_count'],
                'logged' => $res['logged_count'],
                'failed' => $res['failed_count'],
            ]
        );

        $msg = "تمت معالجة حملة الواتساب بنجاح! الإجمالي المستهدف: {$res['total_target']}، أُرسل آلياً: {$res['sent_count']}، سُجلت بروابط جاهزة: {$res['logged_count']}.";

        return back()->with('success', $msg);
    }

    /**
     * حذف سجل رسالة من الأرشيف
     */
    public function destroyLog($id)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'عذراً، حذف السجلات مخصص للإدارة العليا.');
        }

        $log = WhatsAppLog::findOrFail($id);
        $log->delete();

        return back()->with('success', 'تم حذف السجل بنجاح.');
    }

    /**
     * تفريغ الأرشيف
     */
    public function clearLogs()
    {
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'عذراً، تفريغ السجل مخصص للإدارة العليا فقط.');
        }

        WhatsAppLog::truncate();

        return back()->with('success', 'تم تفريغ أرشيف رسائل الواتساب بالكامل.');
    }
}
