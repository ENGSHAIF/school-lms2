<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\PasswordResetRequest;
use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required_without:email|nullable|string',
            'email' => 'required_without:login|nullable|string',
            'password' => 'required|string',
        ], [
            'login.required_without' => 'يرجى إدخال البريد الإلكتروني أو الرقم الفريد للمستخدم.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        $loginInput = trim($request->input('login') ?? $request->input('email') ?? '');

        // البحث عن المستخدم بواسطة البريد الإلكتروني أو الرقم الفريد (user_code أو student_code)
        $user = User::where('email', $loginInput)
            ->orWhere('user_code', $loginInput)
            ->orWhere('student_code', $loginInput)
            ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // التحقق من أن الحساب غير معطل
            if ($user->status === 'suspended') {
                SystemAuditLog::record(
                    'auth.login_suspended',
                    "محاولة تسجيل دخول مرفوضة لحساب معطل: ({$user->name}) [{$user->user_code}]",
                    $user,
                    ['user_id' => $user->id, 'status' => $user->status]
                );

                return back()->withErrors([
                    'login' => 'تم إيقاف هذا الحساب مؤقتاً. يرجى التواصل مع إدارة النظام.',
                ])->withInput();
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // توثيق تسجيل الدخول الناجح في سجل تدقيق النظام
            SystemAuditLog::record(
                'auth.login',
                "تسجيل دخول ناجح إلى النظام للمستخدم ({$user->name}) [{$user->user_code}]",
                $user,
                ['user_id' => $user->id, 'role' => $user->role, 'email' => $user->email]
            );

            // في حال تم تغيير كلمة المرور من قبل الإدارة، يجبر النظام المستخدم على تغييرها فوراً
            if ($user->must_change_password) {
                return redirect()->route('password.force_change')->with('warning', 'عزيزي المستخدم: تم تغيير كلمة المرور لحسابك من قبل الإدارة. حرصاً على خصوصيتك وأمان حسابك، يجب عليك تعيين كلمة مرور جديدة خاصة بك الآن للمتابعة.');
            }

            return $this->redirectBasedOnRole($user);
        }

        // توثيق محاولة تسجيل الدخول الفاشلة
        SystemAuditLog::record(
            'auth.login_failed',
            "محاولة تسجيل دخول فاشلة إلى النظام للمعرف أو البريد: [{$loginInput}]",
            null,
            ['attempted_identifier' => $loginInput]
        );

        return back()->withErrors([
            'login' => 'بيانات الدخول غير صحيحة. تحقق من البريد أو الرقم الفريد وكلمة المرور.',
        ])->withInput();
    }

    /**
     * توجيه المستخدم حسب دوره في النظام
     */
    protected function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isSupervisor()) {
            return redirect()->intended(route('supervisor.dashboard'));
        } elseif ($user->isAccountant()) {
            return redirect()->intended(route('accounting.dashboard'));
        } elseif ($user->isParent()) {
            return redirect()->intended(route('parent.dashboard'));
        } elseif ($user->isTeacher()) {
            return redirect()->intended(route('teacher.dashboard'));
        } else {
            // الطالب
            if ($user->status === 'pending_test') {
                return redirect()->route('placement.test');
            } elseif ($user->status === 'pending_approval') {
                return redirect()->route('student.status');
            } else {
                return redirect()->route('student.portal');
            }
        }
    }

    /**
     * صفحة إجبار المستخدم على تغيير كلمة المرور عند أول تسجيل دخول
     */
    public function showForceChangePassword()
    {
        $user = Auth::user();
        return view('auth.force_change_password', compact('user'));
    }

    /**
     * معالجة تحديث كلمة المرور الإلزامية
     */
    public function updateForceChangePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 6 أحرف أو أرقام.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        // تحديث أي طلبات استرجاع معلقة لهذا المستخدم إن وجدت
        PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'admin_notes' => 'قام المستخدم بتعيين كلمة مرور جديدة بنجاح.',
                'handled_at' => now(),
            ]);

        return $this->redirectBasedOnRole($user)->with('success', 'تهانينا! تم تعيين كلمة المرور الجديدة بنجاح وتفعيل حسابك بالكامل.');
    }

    /**
     * تقديم طلب استعادة أو تغيير كلمة المرور للإدارة عند نسيانها
     */
    public function storePasswordResetRequest(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'user_notes' => 'nullable|string|max:500',
        ], [
            'identifier.required' => 'يرجى إدخال البريد الإلكتروني أو الرقم الفريد للمستخدم.',
        ]);

        $identifier = trim($validated['identifier']);

        // محاولة مطابقة المستخدم في النظام
        $user = User::where('email', $identifier)
            ->orWhere('user_code', $identifier)
            ->orWhere('student_code', $identifier)
            ->orWhere('phone_whatsapp', $identifier)
            ->first();

        PasswordResetRequest::create([
            'user_id' => $user?->id,
            'name' => $validated['name'] ?? $user?->name ?? 'مستخدم غير مسجل',
            'identifier' => $identifier,
            'phone' => $validated['phone'] ?? $user?->phone_whatsapp,
            'user_notes' => $validated['user_notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success_reset_request', 'تم إرسال طلب استعادة الحساب وتغيير كلمة المرور إلى إدارة النظام بنجاح! سيتم فحص طلبك والتواصل معك عبر الواتساب أو الهاتف لتزويدك ببيانات الدخول.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_whatsapp' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'gender.required' => 'يرجى تحديد الجنس (ذكر أو أنثى).',
            'gender.in' => 'قيمة الجنس المحددة غير صحيحة.',
        ]);

        // توليد كود الطالب التلقائي ورقم المستخدم الفريد
        $latestId = User::max('id') ?? 0;
        $studentCode = 'STU-' . date('Y') . '-' . str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'status' => 'pending_test', // يبدأ بالاختبار الإلزامي
            'student_code' => $studentCode,
            'user_code' => $studentCode,
            'must_change_password' => false,
        ]);

        // فتح حساب مالي للطالب (وفق المعايير المحاسبية المعتمدة)
        FinancialAccount::create([
            'account_code' => '21' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'account_name' => 'حساب الطالب: ' . $user->name,
            'account_type' => 'student',
            'user_id' => $user->id,
            'currency' => 'YER',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
        ]);

        Auth::login($user);

        // إرسال رسالة ترحيبية آلية عبر الواتساب للطالب الجديد
        if (!empty($user->phone_whatsapp)) {
            $welcomeText = \App\Services\WhatsAppService::renderTemplate(
                \App\Models\SiteSetting::get('wa_template_welcome_body', "أهلاً وسهلاً بك عزيزنا {name} في {school_name}! 🎓✨\n\nيسرنا انضمامك إلينا لمواصلة رحلتك التعليمية المتميزة. يمكنك الآن تسجيل الدخول لمنصتك التعليمية ومتابعة دروسك واختباراتك بكل سهولة.\n\n🔗 كود الدخول الخاص بك: {student_code}\n📱 هاتف الدعم والاستفسار: {platform_phone}\n\nنتمنى لك تجربة ممتعة وتفوقاً مستمراً!"),
                [
                    '{name}' => $user->name,
                    '{student_code}' => $user->student_code ?? $user->user_code,
                    '{email}' => $user->email,
                ]
            );

            \App\Services\WhatsAppService::send(
                $user->phone_whatsapp,
                $welcomeText,
                null,
                $user->id,
                'welcome'
            );
        }

        // توجيهه فوراً لاختبار تحديد المستوى
        return redirect()->route('placement.test')->with('success', 'أهلاً بك! رقمك الفريد للدخول هو: [' . $user->user_code . ']. يرجى أداء اختبار تحديد المستوى للبدء.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            SystemAuditLog::record(
                'auth.logout',
                "تسجيل خروج المستخدم ({$user->name}) [{$user->user_code}] من النظام.",
                $user,
                ['user_id' => $user->id, 'role' => $user->role]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

