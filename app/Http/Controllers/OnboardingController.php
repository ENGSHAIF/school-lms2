<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\StudentProfile;
use App\Models\StudentWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    // عرض مسار التسجيل الموجه متعدد الخطوات (5 خطوات)
    public function show()
    {
        return view('auth.onboarding');
    }

    // معالجة وحفظ بيانات التسجيل وإنشاء الملف التعليمي المخصص
    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_whatsapp' => 'required|string|max:30',
            'password' => 'required|string|min:6',
            'country' => 'nullable|string|max:100',
            'stage' => 'required|string|in:kindergarten,primary,middle_school,high_school,university,adult,parent,other',
            'grade_substage' => 'nullable|string|max:100',
            'learning_goals' => 'nullable|array',
            'prior_english_exposure' => 'nullable|string|max:100',
            'preferred_time' => 'nullable|string|max:100',
            'preferred_frequency' => 'nullable|string|max:100',
            'parent_consent' => 'nullable|boolean',
        ]);

        // 1. تحديد الدور بناءً على المرحلة
        $role = ($validated['stage'] === 'parent') ? 'parent' : 'student';

        // 2. توليد كود الطالب التلقائي
        $latestId = User::max('id') ?? 0;
        $codePrefix = ($role === 'parent') ? 'PAR-' : 'STU-';
        $studentCode = $codePrefix . date('Y') . '-' . str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);

        // 3. إنشاء حساب المستخدم
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_whatsapp' => $validated['phone_whatsapp'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'status' => 'pending_test',
            'student_code' => $studentCode,
            'theme_preference' => session('theme', 'professional_navy'),
            'locale' => session('locale', 'ar'),
        ]);

        // 4. إنشاء الملف التعليمي والخيارات الشرطية المحددة
        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'stage' => $validated['stage'],
            'grade_substage' => $validated['grade_substage'] ?? null,
            'learning_goals' => $validated['learning_goals'] ?? [],
            'prior_english_exposure' => $validated['prior_english_exposure'] ?? null,
            'preferred_time' => $validated['preferred_time'] ?? 'مسائي (5:00 م - 9:00 م)',
            'preferred_frequency' => $validated['preferred_frequency'] ?? 'حصتان إلى 3 حصص أسبوعياً',
            'parent_consent' => (bool) ($request->input('parent_consent', false)),
        ]);

        // 5. منح حصة تجريبية مجانية 1-on-1 فوراً في محفظة الطالب
        StudentWallet::create([
            'user_id' => $user->id,
            'total_lessons' => 1, // حصة تجريبية مجانية (Free Trial)
            'used_lessons' => 0,
            'remaining_lessons' => 1,
        ]);

        // 6. فتح الحساب المالي (المعايير المحاسبية المعتمدة)
        FinancialAccount::create([
            'account_code' => '21' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'account_name' => 'حساب الطالب: ' . $user->name,
            'account_type' => 'student',
            'user_id' => $user->id,
            'currency' => 'YER',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
        ]);

        // 7. تسجيل الدخول فورياً
        Auth::login($user);

        // 8. إرجاع النتيجة والتوصية (الخطوة 5: Personalized Result)
        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'code' => $user->student_code,
                'role' => $user->role,
            ],
            'profile' => [
                'stage' => $profile->stage,
                'stage_label' => $this->getStageLabel($profile->stage),
                'grade_substage' => $profile->grade_substage,
                'goals' => $profile->learning_goals,
            ],
            'trial_lessons' => 1,
            'redirect_placement' => route('placement.test'),
            'redirect_booking' => route('student.booking'),
            'redirect_packages' => route('packages.index'),
            'message' => 'تم إنشاء حسابك وتخصيص مسارك التعليمي بنجاح، ومُنحت حصة تجريبية مجانية 1-on-1!',
        ]);
    }

    private function getStageLabel($stage)
    {
        $labels = [
            'kindergarten' => 'طفل روضة (3 - 5 سنوات)',
            'primary' => 'طالب ابتدائي (6 - 11 سنة)',
            'middle_school' => 'طالب أساسي / متوسط (12 - 14 سنة)',
            'high_school' => 'طالب ثانوي (15 - 17 سنة)',
            'university' => 'طالب جامعي',
            'adult' => 'موظف أو متعلم بالغ',
            'parent' => 'ولي أمر يسجل لأبنائه',
            'other' => 'مسار خاص',
        ];

        return $labels[$stage] ?? $stage;
    }
}
