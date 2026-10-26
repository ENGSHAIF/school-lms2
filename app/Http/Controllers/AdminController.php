<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use App\Models\Lesson;  
use App\Models\Package;
use App\Models\User;
use App\Models\ExamAttempt;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\FinancialAccount;
use App\Models\PasswordResetRequest;
use App\Models\PermissionGroup;
use App\Models\StudentProfile;
use App\Models\SystemAuditLog;
use App\Models\Voucher;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = User::where('role', 'student')->count();
        $pendingStudents = User::where('role', 'student')->whereIn('status', ['pending_test', 'pending_approval'])->count();
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();
        $totalTeachers = User::whereIn('role', ['teacher', 'supervisor'])->count();
        $coursesCount = Course::count();
        $groupsCount = \App\Models\StudyGroup::count();
        $examsCount = \App\Models\Exam::count();
        $vouchersCount = Voucher::count();
        $todayReceipts = Voucher::where('voucher_type', 'receipt')->whereDate('created_at', today())->sum('amount');
        $pendingPasswordRequests = PasswordResetRequest::where('status', 'pending')->count();
        $whatsappMasterOn = (bool) \App\Models\SiteSetting::get('whatsapp_master_switch', true);

        return view('admin.dashboard', compact(
            'totalStudents',
            'pendingStudents',
            'activeStudents',
            'totalTeachers',
            'coursesCount',
            'groupsCount',
            'examsCount',
            'vouchersCount',
            'todayReceipts',
            'pendingPasswordRequests',
            'whatsappMasterOn'
        ));
    }

    /**
     * شاشة إدارة الطلاب والقبول والتسجيل المستقلة
     */
    public function students(Request $request)
    {
        $query = User::where('role', 'student')
            ->with([
                'examAttempts' => fn($q) => $q->with('answers')->latest()->limit(1),
                'enrollments.level.course'
            ])
            ->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('student_code', 'like', "%{$s}%")
                  ->orWhere('user_code', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone_whatsapp', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(15)->withQueryString();
        $levels = Level::with('course')->get();

        $totalStudents = User::where('role', 'student')->count();
        $pendingApproval = User::where('role', 'student')->where('status', 'pending_approval')->count();
        $pendingTest = User::where('role', 'student')->where('status', 'pending_test')->count();
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();

        return view('admin.students.index', compact(
            'students',
            'levels',
            'totalStudents',
            'pendingApproval',
            'pendingTest',
            'activeStudents'
        ));
    }

    public function assignLevel(Request $request, $id)
    {
        $request->validate([
            'level_id' => 'required|exists:levels,id',
        ]);

        $student = User::findOrFail($id);
        $level = Level::findOrFail($request->level_id);

        // 1. تسجيل الطالب في المستوى
        StudentEnrollment::create([
            'user_id' => $student->id,
            'level_id' => $level->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // 2. جدولة القسط الدراسي المالي
        StudentInstallment::create([
            'user_id' => $student->id,
            'level_id' => $level->id,
            'installment_number' => 1,
            'amount' => $level->fee_amount,
            'currency' => $level->currency,
            'due_date' => now()->addDays(7),
            'status' => 'unpaid',
        ]);

        // 3. تحديث حساب الطالب المالي (مدين بالرسوم الدراسية - كشف حساب قياسي)
        $studentAccount = FinancialAccount::where('user_id', $student->id)->first();
        if ($studentAccount) {
            $studentAccount->increment('current_balance', $level->fee_amount);
        }

        // 4. تفعيل حساب الطالب بالكامل
        $student->update([
            'status' => 'active'
        ]);

        // 5. إرسال إشعار الواتساب التلقائي المعتمد
        $schoolName = \App\Models\SiteSetting::get('school_name', 'منصة ASOTS');
        $msgBody = "مرحباً {$student->name}، تهانينا! 🎓\n\nتم اعتماد مستواك الأكاديمي رسمياً: [{$level->name}] في {$schoolName}.\nيمكنك الآن تسجيل الدخول لمنصتك ومتابعة محاضراتك ودروسك فوراً.\n\nنتمنى لك دوام التفوق والنجاح!";
        
        \App\Services\WhatsAppService::send(
            $student->phone_whatsapp ?: '',
            $msgBody,
            null,
            $student->id,
            'announcement'
        );

        return back()->with('success', "تم اعتماد الطالب وتخصيص المستوى [{$level->name}] بنجاح، وتفعيل حسابه المالي والأكاديمي وإشعاره عبر الواتساب.");
    }

    // 3. عرض وإدارة المستخدمين مع تبويبات منفصلة لكل فئة (إدارة النظام، المعلمون، الطلاب، أولياء الأمور، وطلبات استعادة كلمة المرور)
    public function users(Request $request)
    {
        $tab = $request->query('tab', 'staff');
        $search = $request->query('search');

        $pendingResetCount = PasswordResetRequest::where('status', 'pending')->count();
        $passwordRequests = null;

        $selectedGroupId = $request->query('permission_group_id');

        if ($tab === 'requests') {
            $reqQuery = PasswordResetRequest::with(['user', 'handler'])->latest();
            if ($search) {
                $reqQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identifier', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            $passwordRequests = $reqQuery->paginate(15)->withQueryString();
            $users = User::whereRaw('1 = 0')->paginate(15); // مصفوفة فارغة لتجنب أخطاء العرض
        } else {
            $query = User::query()->with('permissionGroup')->latest();

            if ($tab === 'staff') {
                $query->whereIn('role', ['admin', 'supervisor', 'accountant']);
            } elseif ($tab === 'teachers') {
                $query->where('role', 'teacher')->with(['financialAccount', 'permissionGroup']);
            } elseif ($tab === 'students') {
                $query->where('role', 'student')->with(['financialAccount', 'enrollments.level', 'permissionGroup']);
            } elseif ($tab === 'parents') {
                $query->where('role', 'parent')->with([
                    'permissionGroup',
                    'childrenProfiles.user.enrollments.level',
                    'childrenProfiles.user.studyGroups',
                    'children.enrollments.level',
                    'children.studyGroups'
                ]);
            }

            if ($selectedGroupId) {
                $query->where('permission_group_id', $selectedGroupId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_whatsapp', 'like', "%{$search}%")
                      ->orWhere('user_code', 'like', "%{$search}%")
                      ->orWhere('student_code', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate(15)->withQueryString();
            $users->getCollection()->transform(function ($u) {
                $u->integrity_reasons = $u->checkDeletionIntegrity();
                $u->can_be_deleted = empty($u->integrity_reasons);
                return $u;
            });
        }

        $allStudents = User::where('role', 'student')
            ->select('id', 'name', 'name_en', 'user_code', 'student_code', 'phone_whatsapp')
            ->orderBy('name')
            ->get();


        $roleCounts = [
            'staff' => User::whereIn('role', ['admin', 'supervisor', 'accountant'])->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'parents' => User::where('role', 'parent')->count(),
            'requests' => $pendingResetCount,
            'all' => User::count(),
        ];

        $availablePermissions = User::availablePermissions();
        $permissionGroups = PermissionGroup::withCount('users')->latest('is_system')->oldest('id')->get();

        return view('admin.users.index', compact('users', 'passwordRequests', 'pendingResetCount', 'roleCounts', 'tab', 'search', 'availablePermissions', 'permissionGroups', 'selectedGroupId', 'allStudents'));
    }


    // 4. إضافة مستخدم أو موظف جديد للنظام مع تحديد صلاحياته
    public function storeUser(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة مستخدمين للنظام.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_whatsapp' => 'required|string|max:30',
            'role' => 'required|string|in:teacher,student,supervisor,accountant,parent,admin',
            'permission_group_id' => 'nullable|exists:permission_groups,id',
            'password' => 'required|string|min:6',
            'status' => 'nullable|string|in:active,pending_approval,suspended,pending_test',
            'must_change_password' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ], [
            'gender.required' => 'يرجى تحديد الجنس (ذكر أو أنثى).',
        ]);

        $status = $validated['status'] ?? 'active';

        // تحديد مجموعة الصلاحيات
        $permissionGroupId = $validated['permission_group_id'] ?? null;
        if (!$permissionGroupId) {
            $defaultSlug = match ($validated['role']) {
                'admin' => 'admin',
                'supervisor' => 'supervisors',
                'teacher' => 'teachers',
                'accountant' => 'accountants',
                'parent' => 'parents',
                default => null,
            };
            if ($defaultSlug) {
                $permissionGroupId = PermissionGroup::where('slug', $defaultSlug)->value('id');
            }
        }

        // إسناد الصلاحيات: إذا أرسلها النموذج تؤخذ، وإلا تؤخذ الافتراضية للدور
        $permissions = $validated['permissions'] ?? User::defaultPermissionsForRole($validated['role']);

        $user = User::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? 'male',
            'email' => $validated['email'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'permission_group_id' => $permissionGroupId,
            'permissions' => $permissions,
            'status' => $status,
            'must_change_password' => $request->has('must_change_password') ? $request->boolean('must_change_password') : false,
            'theme_preference' => 'professional_navy',
            'locale' => 'ar',
        ]);

        // إذا كان طالباً أو معلماً، ننشئ له حساباً مالياً في دليل الحسابات
        if ($validated['role'] === 'student') {
            FinancialAccount::create([
                'user_id' => $user->id,
                'account_code' => '21' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'account_name' => 'حساب الطالب: ' . $user->name,
                'account_type' => 'student',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'currency' => 'YER',
            ]);
        } elseif ($validated['role'] === 'teacher') {
            FinancialAccount::create([
                'user_id' => $user->id,
                'account_code' => '22' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'account_name' => 'حساب المعلم: ' . $user->name,
                'account_type' => 'teacher',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'currency' => 'YER',
            ]);
        } elseif (in_array($validated['role'], ['supervisor', 'accountant', 'staff', 'admin'])) {
            FinancialAccount::create([
                'user_id' => $user->id,
                'account_code' => '22' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'account_name' => 'حساب الكادر: ' . $user->name,
                'account_type' => 'teacher',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'currency' => 'YER',
            ]);
        }

        // إرسال رسالة ترحيبية فورية عبر الواتساب إذا كانت مفعلة
        if (!empty($user->phone_whatsapp) && \App\Models\SiteSetting::get('whatsapp_auto_welcome_enabled', true) && \App\Models\SiteSetting::get('wa_template_welcome_enabled', true)) {
            $welcomeText = \App\Services\WhatsAppService::renderTemplate(
                \App\Models\SiteSetting::get('wa_template_welcome_body', "أهلاً وسهلاً بك {name} في {school_name}! 🎓✨"),
                ['{name}' => $user->name, '{phone}' => $user->phone_whatsapp]
            );
            \App\Services\WhatsAppService::send($user->phone_whatsapp, $welcomeText, null, $user->id, 'welcome');
        }

        $roleLabels = [
            'teacher' => 'معلم',
            'student' => 'طالب',
            'supervisor' => 'مشرف أكاديمي',
            'accountant' => 'محاسب مالي',
            'parent' => 'ولي أمر',
            'admin' => 'مدير نظام',
        ];
        $roleName = $roleLabels[$validated['role']] ?? $validated['role'];

        return back()->with('success', "تمت إضافة المستخدم ({$user->name}) بدور [{$roleName}] ورقم فريد [{$user->user_code}] بنجاح إلى النظام وتفعيل حسابه! 🎉");
    }

    // 5. تعديل بيانات وصلاحيات المستخدم
    public function updateUser(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل المستخدمين والصلاحيات.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_whatsapp' => 'required|string|max:30',
            'role' => 'required|string|in:teacher,student,supervisor,accountant,parent,admin',
            'permission_group_id' => 'nullable|exists:permission_groups,id',
            'status' => 'required|string|in:active,pending_approval,suspended,pending_test',
            'password' => 'nullable|string|min:6',
            'must_change_password' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ], [
            'gender.required' => 'يرجى تحديد الجنس (ذكر أو أنثى).',
        ]);

        $permissionGroupId = $validated['permission_group_id'] ?? null;
        if ($validated['role'] === 'admin') {
            $adminGroupId = PermissionGroup::where('slug', 'admin')->value('id');
            if ($adminGroupId) {
                $permissionGroupId = $adminGroupId;
            }
        } elseif ($validated['role'] === 'parent' && empty($permissionGroupId)) {
            $parentGroupId = PermissionGroup::where('slug', 'parents')->value('id');
            if ($parentGroupId) {
                $permissionGroupId = $parentGroupId;
            }
        }

        $data = [
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? 'male',
            'email' => $validated['email'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'role' => $validated['role'],
            'permission_group_id' => $permissionGroupId,
            'status' => $validated['status'],
        ];

        if ($request->has('must_change_password')) {
            $data['must_change_password'] = $request->boolean('must_change_password');
        }

        if (!empty($validated['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
            // عند تغيير كلمة المرور من قبل الإدارة، يتم إجباره على تغييرها عند تسجيل الدخول إلا إذا حدد خلاف ذلك
            $data['must_change_password'] = $request->has('must_change_password') ? $request->boolean('must_change_password') : true;
        }

        // تحديث الصلاحيات: إذا أرسلت في النموذج يتم تخزينها، إذا لم تُرسل (فارغة للموظفين) نحدد مصفوفة فارغة
        if (in_array($validated['role'], ['admin', 'supervisor', 'accountant'])) {
            $data['permissions'] = $validated['permissions'] ?? [];
        }

        $user->update($data);

        return back()->with('success', "تم تحديث بيانات وصلاحيات المستخدم ({$user->name}) بنجاح.");
    }

    // 5.1 تغيير كلمة مرور المستخدم مباشرة من قبل الإدارة مع إجبار التغيير عند أول تسجيل دخول
    public function changeUserPassword(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل كلمات المرور.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'force_change' => 'nullable|boolean',
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user = User::findOrFail($id);
        $force = $request->has('force_change') ? $request->boolean('force_change') : true;

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'must_change_password' => $force,
        ]);

        // إغلاق أي طلبات استرجاع معلقة لهذا المستخدم
        PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'admin_notes' => 'تم تغيير كلمة المرور مباشرة بواسطة الإدارة.',
                'handled_by' => auth()->id(),
                'handled_at' => now(),
            ]);

        $forceMsg = $force ? 'مع إلزامه بتغيير كلمة المرور فور أول تسجيل دخول لحسابه.' : 'دون إلزامه بتغييرها.';
        return back()->with('success', "تم تغيير كلمة المرور للمستخدم ({$user->name}) بنجاح {$forceMsg} 🔑");
    }

    // 5.2 معالجة طلب استرجاع كلمة المرور المقدم من المستخدم (قبول وإعادة تعيين / أو رفض)
    public function handlePasswordResetRequest(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية معالجة طلبات كلمات المرور.');
        }

        $resetReq = PasswordResetRequest::findOrFail($id);
        $action = $request->input('action', 'reset');

        if ($action === 'reject') {
            $resetReq->update([
                'status' => 'rejected',
                'admin_notes' => $request->input('admin_notes', 'تم رفض الطلب لعدم تطابق البيانات أو عدم الحاجة.'),
                'handled_by' => auth()->id(),
                'handled_at' => now(),
            ]);

            return back()->with('success', "تم رفض طلب استرجاع الحساب رقم #{$resetReq->id} بنجاح.");
        }

        $request->validate([
            'new_password' => 'required|string|min:6',
            'admin_notes' => 'nullable|string|max:500',
        ], [
            'new_password.required' => 'يرجى إدخال كلمة المرور المؤقتة.',
            'new_password.min' => 'كلمة المرور المؤقتة يجب أن لا تقل عن 6 أحرف.',
        ]);

        $user = $resetReq->user;
        if (!$user) {
            $user = User::where('email', $resetReq->identifier)
                ->orWhere('user_code', $resetReq->identifier)
                ->orWhere('student_code', $resetReq->identifier)
                ->orWhere('phone_whatsapp', $resetReq->identifier)
                ->first();
        }

        if (!$user) {
            return back()->with('error', 'تعذر العثور على حساب مستخدم مطابق لهذا المعرف. يرجى التحقق من البريد أو الرقم الفريد.');
        }

        // تحديث كلمة مرور المستخدم وإجباره على التغيير عند أول تسجيل دخول
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
            'must_change_password' => true,
        ]);

        $resetReq->update([
            'user_id' => $user->id,
            'status' => 'completed',
            'admin_notes' => $request->input('admin_notes', 'تم تعيين كلمة مرور مؤقتة وتفعيل إجبار التغيير عند الدخول.'),
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return back()->with('success', "تم تعيين كلمة المرور المؤقتة بنجاح للمستخدم ({$user->name}) وإجباره على تغييرها عند أول تسجيل دخول.");
    }


    // 6. تبديل حالة الحساب (تنشيط / إيقاف)
    public function toggleUserStatus($id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل حالة المستخدمين.');
        }

        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'لا يمكنك تعطيل أو إيقاف حسابك الحالي المسجل به الدخول.');
        }

        $oldStatus = $user->status;
        $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $statusLabel = ($newStatus === 'active') ? 'تنشيط' : 'إيقاف وتعطيل';

        // توثيق تبديل الحالة في سجل تدقيق النظام
        SystemAuditLog::record(
            'user.status_toggle',
            "تم {$statusLabel} حساب المستخدم ({$user->name}) [{$user->user_code}] بنجاح.",
            $user,
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );

        return back()->with('success', "تم {$statusLabel} حساب ({$user->name}) بنجاح.");
    }

    // 7. حذف حساب مستخدم مع التحقق الصارم من قيود النزاهة المحاسبية وسجل العمليات
    public function deleteUser($id)
    {
        // التحقق من الصلاحيات العامة وصلاحيات الأدوار
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('system.users_delete') && !auth()->user()->hasPermission('manage_users')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف المستخدمين من النظام.');
        }

        $user = User::with(['financialAccount'])->findOrFail($id);

        // التحقق من صلاحيات حذف الطلاب أو المعلمين إن وجدت
        if ($user->role === 'student' && !auth()->user()->isAdmin() && !auth()->user()->hasPermission('students.delete') && !auth()->user()->hasPermission('system.users_delete')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف حسابات الطلاب.');
        }
        if ($user->role === 'teacher' && !auth()->user()->isAdmin() && !auth()->user()->hasPermission('teachers.delete') && !auth()->user()->hasPermission('system.users_delete')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف حسابات المعلمين.');
        }

        // منع المستخدم من حذف حسابه الشخصي
        if (auth()->id() === $user->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الشخصي المسجل به حالياً.');
        }

        // حماية حساب الإدارة الرئيسي للنظام من الحذف
        if ($user->id === 1 || ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1)) {
            return back()->with('error', 'محظور تماماً: لا يمكن حذف الحساب الإداري الرئيسي للنظام.');
        }

        // فحص قيود وسجل العمليات المحاسبية والأكاديمية والإدارية
        $integrityReasons = $user->checkDeletionIntegrity();

        if (!empty($integrityReasons)) {
            // تسجيل محاولة الحذف المحظورة في سجل تدقيق النظام
            SystemAuditLog::record(
                'user.delete_blocked',
                "تم منع محاولة حذف المستخدم ({$user->name}) [{$user->user_code}] نظراً لوجود قيود وعمليات مسجلة باسمه في النظام.",
                $user,
                ['reasons' => $integrityReasons]
            );

            $summaryList = implode('، ', $integrityReasons);

            return back()->with('error_with_actions', [
                'title' => "تعذر حذف المستخدم ({$user->name}) لوجود قيود وعمليات سابقة",
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_code' => $user->user_code,
                'user_role' => $user->role,
                'user_status' => $user->status,
                'reasons' => $integrityReasons,
                'message' => "حفاظاً على نزاهة البيانات المحاسبية وسلامة القيود والتقارير، يمنع النظام حذف هذا المستخدم لوجود عمليات مرتبطة به ({$summaryList}). يرجى إيقاف الحساب بدلاً من حذفه لمنع دخوله مع بقاء السجلات."
            ])->with('error', "لا يمكن حذف المستخدم ({$user->name}) نظراً لارتباطه بعمليات وسجلات في النظام ({$summaryList}). يوصى بإيقاف وتعطيل الحساب بدلاً من الحذف.");
        }

        $userName = $user->name;
        $userCode = $user->user_code;

        // توثيق الحذف في سجل تدقيق النظام للمستخدم النظيف غير المقيد بعمليات
        SystemAuditLog::record(
            'user.deleted',
            "تم حذف حساب المستخدم ({$userName}) [{$userCode}] نهائياً لعدم وجود أي عمليات أو قيود سابقة مرتبطة به.",
            null,
            [
                'old_values' => [
                    'اسم المستخدم المحذوف' => $userName,
                    'الرقم الفريد (الكود)' => $userCode,
                    'البريد الإلكتروني' => $user->email,
                    'الدور / الرتبة' => $user->role,
                    'رقم الهاتف / الواتساب' => $user->phone_whatsapp ?? 'غير مسجل',
                    'تاريخ التسجيل' => $user->created_at?->format('Y-m-d'),
                ],
                'new_values' => [
                    'الحالة' => 'محذوف نهائياً من قاعدة البيانات 🗑️',
                ],
                'deleted_user_id' => $user->id,
                'deleted_user_name' => $userName,
                'deleted_user_code' => $userCode,
            ]
        );

        $user->delete();

        return back()->with('success', "تم حذف حساب المستخدم ({$userName}) بنجاح لعدم ارتباطه بأي عمليات أو قيود سابقة.");
    }

    // 8. شاشة إدارة المناهج والدورات وأسعار الباقات والمستويات والحصص
    // 8. شاشة إدارة المناهج والدورات وأسعار الباقات والمستويات والحصص
    public function courses(?Request $request = null)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses') && !auth()->user()->hasPermission('manage_packages')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إدارة المناهج والأسعار.');
        }

        $levels = Level::with(['courses' => function ($q) {
            $q->withCount('resources')->orderBy('course_order');
        }])->orderBy('level_order')->get();

        $allLevels = Level::where('is_active', true)->orderBy('level_order')->get();
        $packages = Package::all();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.courses.index', compact('levels', 'allLevels', 'packages', 'teachers'));
    }

    public function storeCourse(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة الدورات.');
        }

        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'hours' => 'required|integer|min:1|max:500',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $nameEn = trim($request->name_en);
        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : null;
        $fullName = $nameAr ? "{$nameEn} ({$nameAr})" : $nameEn;

        $nextOrder = (Course::where('level_id', $validated['level_id'])->max('course_order') ?? 0) + 1;

        $validated['name_en'] = $nameEn;
        $validated['name_ar'] = $nameAr;
        $validated['name'] = $fullName;
        $validated['course_order'] = $nextOrder;
        $validated['created_by'] = auth()->id();

        $course = Course::create($validated);

        SystemAuditLog::record(
            'course.create',
            "إضافة دورة تعليمية جديدة: [{$fullName}] للمستوى [{$course->level?->name_en}] بواقع {$validated['hours']} ساعة ورسم {$validated['fee_amount']} {$validated['currency']}",
            $course,
            ['hours' => $validated['hours'], 'fee_amount' => $validated['fee_amount'], 'currency' => $validated['currency'], 'level_id' => $validated['level_id']]
        );

        return back()->with('success', "تمت إضافة الدورة التعليمية [{$fullName}] بنجاح للمستوى المختار.");
    }

    public function updateCourse(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل الدورات.');
        }

        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'hours' => 'required|integer|min:1|max:500',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $nameEn = trim($request->name_en);
        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : null;
        $fullName = $nameAr ? "{$nameEn} ({$nameAr})" : $nameEn;

        $oldFee = (float)$course->fee_amount;
        $newFee = (float)$validated['fee_amount'];
        $oldHours = (int)$course->hours;
        $newHours = (int)$validated['hours'];
        $oldCurrency = $course->currency;
        $newCurrency = $validated['currency'];

        $validated['name_en'] = $nameEn;
        $validated['name_ar'] = $nameAr;
        $validated['name'] = $fullName;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        $validated['updated_by'] = auth()->id();

        $course->update($validated);

        if ($oldFee !== $newFee || $oldCurrency !== $newCurrency || $oldHours !== $newHours) {
            $feeDiff = $newFee - $oldFee;
            SystemAuditLog::record(
                'course.pricing_update',
                "تعديل تسعيرة وساعات الدورة [{$fullName}] من ({$oldHours} ساعة - " . number_format($oldFee, 2) . " {$oldCurrency}) إلى ({$newHours} ساعة - " . number_format($newFee, 2) . " {$newCurrency})",
                $course,
                [
                    'old_values' => [
                        'الدورة' => $course->name,
                        'الساعات السابقة' => "{$oldHours} ساعة",
                        'الرسوم السابقة' => number_format($oldFee, 2) . " {$oldCurrency}",
                    ],
                    'new_values' => [
                        'الدورة' => $fullName,
                        'الساعات الجديدة' => "{$newHours} ساعة",
                        'الرسوم الجديدة' => number_format($newFee, 2) . " {$newCurrency}",
                    ],
                    'diff' => [
                        'فارق الرسوم' => ($feeDiff > 0 ? '+' : '') . number_format($feeDiff, 2) . " {$newCurrency}",
                    ],
                    'old_fee' => $oldFee,
                    'new_fee' => $newFee,
                    'old_hours' => $oldHours,
                    'new_hours' => $newHours,
                    'old_currency' => $oldCurrency,
                    'new_currency' => $newCurrency,
                ]
            );
        } else {
            SystemAuditLog::record('course.update', "تحديث وتعديل بيانات دورة تعليمية: [{$course->name}]", $course, ['changes' => $validated]);
        }

        return back()->with('success', "تم تحديث بيانات وتسعيرة الدورة [{$course->name}] بنجاح.");
    }

    public function storeLevel(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة المستويات.');
        }

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'level_order' => 'nullable|integer',
        ]);

        $nameEn = trim($request->name_en);
        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : null;
        $fullName = $nameAr ? "{$nameAr} ({$nameEn})" : $nameEn;

        $nextOrder = $validated['level_order'] ?? ((Level::max('level_order') ?? 0) + 1);

        $validated['name_ar'] = $nameAr;
        $validated['name_en'] = $nameEn;
        $validated['name'] = $fullName;
        $validated['level_order'] = $nextOrder;
        $validated['created_by'] = auth()->id();

        $level = Level::create($validated);

        SystemAuditLog::record(
            'level.create',
            "إضافة مستوى أكاديمي جديد: [{$fullName}] برسم قدره {$validated['fee_amount']} {$validated['currency']}",
            $level,
            ['fee_amount' => $validated['fee_amount'], 'currency' => $validated['currency']]
        );

        return back()->with('success', "تمت إضافة المستوى الأكاديمي [{$fullName}] بنجاح.");
    }

    public function updateLevel(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل أسعار ومستويات الدورات.');
        }

        $level = Level::findOrFail($id);
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fee_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
        ]);

        $nameEn = trim($request->name_en);
        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : null;
        $fullName = $nameAr ? "{$nameAr} ({$nameEn})" : $nameEn;

        $oldFee = (float)$level->fee_amount;
        $newFee = (float)$validated['fee_amount'];
        $oldCurrency = $level->currency;
        $newCurrency = $validated['currency'];

        $validated['name_ar'] = $nameAr;
        $validated['name_en'] = $nameEn;
        $validated['name'] = $fullName;
        $validated['updated_by'] = auth()->id();

        $level->update($validated);

        if ($oldFee !== $newFee || $oldCurrency !== $newCurrency) {
            $feeDiff = $newFee - $oldFee;
            SystemAuditLog::record(
                'level.pricing_update',
                "تعديل تسعيرة ورسوم المستوى الأكاديمي [{$fullName}] من (" . number_format($oldFee, 2) . " {$oldCurrency}) إلى (" . number_format($newFee, 2) . " {$newCurrency})",
                $level,
                [
                    'old_values' => [
                        'المستوى' => $level->name,
                        'الرسوم السابقة' => number_format($oldFee, 2) . " {$oldCurrency}",
                    ],
                    'new_values' => [
                        'المستوى' => $fullName,
                        'الرسوم الجديدة' => number_format($newFee, 2) . " {$newCurrency}",
                    ],
                    'diff' => [
                        'فارق الرسوم' => ($feeDiff > 0 ? '+' : '') . number_format($feeDiff, 2) . " {$newCurrency}",
                    ],
                    'old_fee' => $oldFee,
                    'new_fee' => $newFee,
                    'old_currency' => $oldCurrency,
                    'new_currency' => $newCurrency,
                ]
            );
        } else {
            SystemAuditLog::record('level.update', "تحديث وتعديل بيانات المستوى الأكاديمي [{$fullName}]", $level, ['changes' => $validated]);
        }

        return back()->with('success', "تم تحديث رسوم وبيانات المستوى [{$fullName}] بنجاح.");
    }

    public function storeLesson(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة الحصص والدروس.');
        }

        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'teacher_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Lesson::create($validated);
        return back()->with('success', "تمت إضافة الدرس والحصة التعليمية [{$validated['title']}] بنجاح.");
    }

    public function updatePackage(Request $request, $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_packages')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل أسعار الباقات.');
        }

        $package = Package::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'lessons_count' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $oldPrice = (float)$package->price;
        $newPrice = (float)$validated['price'];
        $oldCurrency = $package->currency;
        $newCurrency = $validated['currency'];

        $validated['updated_by'] = auth()->id();
        $package->update($validated);

        if ($oldPrice !== $newPrice || $oldCurrency !== $newCurrency) {
            $priceDiff = $newPrice - $oldPrice;
            SystemAuditLog::record(
                'package.pricing_update',
                "تعديل تسعيرة الباقة التدريبية [{$package->title}] من (" . number_format($oldPrice, 2) . " {$oldCurrency}) إلى (" . number_format($newPrice, 2) . " {$newCurrency})",
                $package,
                [
                    'old_values' => [
                        'الباقة' => $package->title,
                        'السعر السابق' => number_format($oldPrice, 2) . " {$oldCurrency}",
                        'عدد الحصص' => $package->lessons_count . ' حصة',
                    ],
                    'new_values' => [
                        'الباقة' => $validated['title'],
                        'السعر الجديد' => number_format($newPrice, 2) . " {$newCurrency}",
                        'عدد الحصص' => $validated['lessons_count'] . ' حصة',
                    ],
                    'diff' => [
                        'فارق السعر' => ($priceDiff > 0 ? '+' : '') . number_format($priceDiff, 2) . " {$newCurrency}",
                    ],
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'old_currency' => $oldCurrency,
                    'new_currency' => $newCurrency,
                ]
            );
        } else {
            SystemAuditLog::record('package.update', "تحديث وتعديل بيانات الباقة [{$package->title}]", $package, ['changes' => $validated]);
        }

        return back()->with('success', "تم تحديث سعر وبيانات الباقة [{$package->title}] بنجاح.");
    }

    public function storePackage(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_packages')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة باقات جديدة.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'lessons_count' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $package = Package::create($validated);

        SystemAuditLog::record(
            'package.create',
            "إضافة باقة تدريبية جديدة: [{$package->title}] بسعر {$package->price} {$package->currency}",
            $package,
            ['price' => $package->price, 'currency' => $package->currency]
        );

        return back()->with('success', "تمت إضافة الباقة التدريبية [{$validated['title']}] بنجاح.");
    }

    // ربط طالب بحساب ولي أمر
    public function linkStudentToParent(Request $request, $parentId)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users') && !auth()->user()->hasPermission('students.edit') && !auth()->user()->hasPermission('system.users_manage')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية ربط الطلاب بأولياء الأمور.');
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
        ], [
            'student_id.required' => 'يرجى اختيار الطالب المراد ربطه بولي الأمر.',
            'student_id.exists' => 'الطالب المختار غير موجود في النظام.',
        ]);

        $parent = User::where('id', $parentId)->where('role', 'parent')->firstOrFail();
        $student = User::where('id', $request->student_id)->where('role', 'student')->firstOrFail();

        $profile = StudentProfile::firstOrCreate(['user_id' => $student->id]);
        $oldParentId = $profile->parent_id;
        $profile->parent_id = $parent->id;
        $profile->save();

        SystemAuditLog::record(
            'parent_student_link',
            "تم ربط الطالب [{$student->name}] (كود: {$student->user_code}) بحساب ولي الأمر [{$parent->name}] (كود: {$parent->user_code})",
            $parent,
            [
                'parent_id' => $parent->id,
                'student_id' => $student->id,
                'previous_parent_id' => $oldParentId,
            ]
        );

        return redirect()->route('admin.users.index', ['tab' => 'parents'])->with('success', "تم ربط الطالب [{$student->name}] بحساب ولي الأمر [{$parent->name}] بنجاح! 👨‍👧‍👦");
    }

    // إلغاء ربط طالب من حساب ولي أمر
    public function unlinkStudentFromParent(Request $request, $parentId, $studentId)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage_users') && !auth()->user()->hasPermission('students.edit') && !auth()->user()->hasPermission('system.users_manage')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إلغاء ربط الطلاب بأولياء الأمور.');
        }

        $parent = User::where('id', $parentId)->where('role', 'parent')->firstOrFail();
        $student = User::where('id', $studentId)->where('role', 'student')->firstOrFail();

        $profile = StudentProfile::where('user_id', $student->id)->where('parent_id', $parent->id)->first();
        if ($profile) {
            $profile->parent_id = null;
            $profile->save();

            SystemAuditLog::record(
                'parent_student_unlink',
                "تم إلغاء ربط الطالب [{$student->name}] من حساب ولي الأمر [{$parent->name}]",
                $parent,
                [
                    'parent_id' => $parent->id,
                    'student_id' => $student->id,
                ]
            );

            return redirect()->route('admin.users.index', ['tab' => 'parents'])->with('success', "تم إلغاء ربط الطالب [{$student->name}] من حساب ولي الأمر [{$parent->name}] بنجاح.");
        }

        return redirect()->route('admin.users.index', ['tab' => 'parents'])->with('info', "هذا الطالب غير مرتبط بهذا الحساب حالياً.");
    }
}
