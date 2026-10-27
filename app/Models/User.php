<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'name_en',
        'email',
        'password',
        'phone_whatsapp',
        'whatsapp_notifications_enabled',
        'birth_date',
        'gender',
        'role',
        'permission_group_id',
        'permissions',
        'status',
        'student_code',
        'user_code',
        'must_change_password',
        'stars_balance',
        'avatar',
        'theme_preference',
        'locale',
        'approved_by',
        'approved_at',
        'revoked_by',
        'revoked_at',
        'revoke_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'birth_date' => 'date',
            'must_change_password' => 'boolean',
            'whatsapp_notifications_enabled' => 'boolean',
            'approved_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->user_code)) {
                $prefix = match ($user->role) {
                    'admin' => 'ADM',
                    'teacher' => 'TEA',
                    'supervisor' => 'SUP',
                    'accountant' => 'ACC',
                    'parent' => 'PAR',
                    default => 'STU',
                };
                $latestId = static::max('id') ?? 0;
                $code = $prefix.'-'.date('Y').'-'.str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);
                $user->user_code = $code;
                if (empty($user->student_code)) {
                    $user->student_code = $code;
                }
            }
            if (empty($user->gender)) {
                $user->gender = 'male';
            }
        });
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'female' ? 'أنثى' : 'ذكر';
    }

    public function getGenderIconAttribute(): string
    {
        return $this->gender === 'female' ? '👩' : '👨';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function passwordResetRequests()
    {
        return $this->hasMany(PasswordResetRequest::class, 'user_id');
    }

    public function groupEvaluations()
    {
        return $this->hasMany(GroupStudentEvaluation::class, 'student_id');
    }

    public function studentSurveys()
    {
        return $this->hasMany(StudentSurvey::class, 'student_id');
    }

    public function teacherSurveys()
    {
        return $this->hasMany(StudentSurvey::class, 'teacher_id');
    }

    public function taughtSessions()
    {
        return $this->hasMany(GroupSession::class, 'teacher_id');
    }

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }

    public static function availablePermissions(): array
    {
        return PermissionGroup::allSystemPermissions();
    }

    public function hasPermission(string $permission): bool
    {
        // 1. مدير النظام الأساسي يملك كافة الصلاحيات تلقائياً ودائماً بدون استثناء
        if ($this->role === 'admin') {
            return true;
        }

        // 2. إذا كان المستخدم ينتمي لمجموعة الإدارة العليا (Admin)
        if ($this->permissionGroup && $this->permissionGroup->isAdminGroup()) {
            return true;
        }

        // 3. التحقق من صلاحيات مجموعة الصلاحيات المرتبط بها المستخدم
        if ($this->permissionGroup && $this->permissionGroup->hasPermission($permission)) {
            return true;
        }

        // 4. التحقق من الصلاحيات المخصصة الفردية للمستخدم (كاستثناء خاص)
        $perms = $this->permissions;
        if (is_array($perms)) {
            if (in_array('*', $perms, true) || ! empty($perms['*'])) {
                return true;
            }
            if (! empty($perms[$permission]) || in_array($permission, $perms, true)) {
                return true;
            }

            // فحص التوافقية العكسية مع الصلاحيات القديمة
            $aliases = PermissionGroup::legacyAliases();
            if (isset($aliases[$permission])) {
                foreach ($aliases[$permission] as $alias) {
                    if (! empty($perms[$alias]) || in_array($alias, $perms, true)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function defaultPermissionsForRole(string $role): array
    {
        $groupSlug = match ($role) {
            'admin' => 'admin',
            'supervisor' => 'supervisors',
            'teacher' => 'teachers',
            'accountant' => 'accountants',
            'parent' => 'parents',
            default => null,
        };

        if ($groupSlug) {
            $group = PermissionGroup::where('slug', $groupSlug)->first();
            if ($group && is_array($group->permissions)) {
                return $group->permissions;
            }
        }

        return match ($role) {
            'admin' => ['*'],
            'supervisor' => [
                'students.view', 'students.create', 'students.edit', 'students.approve', 'students.revoke_approval', 'students.statement',
                'curriculum.view', 'curriculum.edit', 'curriculum.resources_manage', 'curriculum.resources_share',
                'groups.view', 'groups.create', 'groups.edit', 'groups.live', 'groups.activities',
                'evaluations.view', 'evaluations.create', 'evaluations.print_daily', 'evaluations.print_final',
                'exams.view', 'exams.create', 'exams.questions_manage', 'exams.publish', 'exams.grade',
                'reports.academic', 'reports.terms_comparison', 'reports.print_all',
            ],
            'teacher' => [
                'students.view', 'curriculum.view', 'curriculum.resources_share',
                'groups.view', 'groups.live', 'groups.activities',
                'evaluations.view', 'evaluations.create', 'evaluations.print_daily',
                'exams.view', 'exams.publish', 'exams.grade',
                'reports.academic',
            ],
            'accountant' => [
                'students.view', 'students.statement', 'teachers.view', 'teachers.statement',
                'curriculum.view', 'curriculum.pricing', 'curriculum.packages',
                'finance.view', 'finance.receipt_create', 'finance.payment_create', 'finance.voucher_edit', 'finance.voucher_delete', 'finance.statements',
                'reports.financial', 'reports.print_all',
            ],
            'parent' => [
                'parent.dashboard',
                'parent.profile_view',
                'parent.attendance_view',
                'parent.evaluations_view',
                'parent.activities_view',
                'parent.exams_view',
                'parent.finance_view',
                'parent.transcript_view',
                'parent.live_schedule',
            ],
            default => [],
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isReviewer(): bool
    {
        if ($this->email === 'demo.reviewer@asots.com' || $this->user_code === 'REV-2026-0001') {
            return true;
        }
        $perms = $this->permissions;
        if (is_array($perms) && (! empty($perms['is_reviewer']) || in_array('is_reviewer', $perms, true))) {
            return true;
        }

        return false;
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    // علاقة الحساب المالي للطالب أو المعلم
    public function financialAccount()
    {
        return $this->hasOne(FinancialAccount::class, 'user_id');
    }

    // علاقة التسجيل في المستويات الدراسية
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'user_id');
    }

    // علاقة المستخدم الذي اعتمد الطالب
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // علاقة المستخدم الذي ألغى اعتماد الطالب
    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * فحص هل بدأ الطالب دراسة أي دورات أو حضور حصص أو رُصدت له درجات
     * (إذا كان طالباً جديداً لم يدرس، يمكن إلغاء اعتماده وتسكينه بحرية)
     */
    public function hasStudiedAnyCourses(): bool
    {
        // 1. حضور أو تقييمات يومية في المجموعات
        if (GroupStudentEvaluation::where('student_id', $this->id)->exists()) {
            return true;
        }

        // 2. تقارير حصص فردية أو ملاحظات معلمين
        if (LessonReport::where('student_id', $this->id)->exists()) {
            return true;
        }

        // 3. حجوزات حصص مكتملة
        if (Booking::where('student_id', $this->id)->where('status', 'completed')->exists()) {
            return true;
        }

        // 4. درجات دبلوم أو مقررات
        if (StudentCourseGrade::where('student_id', $this->id)->exists()) {
            return true;
        }

        // 5. شهائد أكاديمية صادرة
        if (Certificate::where('student_id', $this->id)->exists()) {
            return true;
        }

        // 6. اختبارات دورات أكاديمية (غير اختبار تحديد المستوى)
        $hasCourseExams = ExamAttempt::where('user_id', $this->id)
            ->whereHas('exam', function ($q) {
                $q->where('type', '!=', 'placement');
            })->exists();

        if ($hasCourseExams) {
            return true;
        }

        return false;
    }

    // محاولات الاختبارات
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class, 'user_id');
    }

    // الأقساط المجدولة
    public function installments()
    {
        return $this->hasMany(StudentInstallment::class, 'user_id');
    }

    // الملف التعليمي والمرحلة ومسار التسجيل
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class, 'user_id');
    }

    // أبناء ولي الأمر المرتبطين بحسابه
    public function childrenProfiles()
    {
        return $this->hasMany(StudentProfile::class, 'parent_id');
    }

    // علاقة مباشرة للطلاب الأبناء المرتبطين بولي الأمر
    public function children()
    {
        return $this->hasManyThrough(
            User::class,
            StudentProfile::class,
            'parent_id', // المفتاح الأجنبي في student_profiles
            'id',        // المفتاح الأساسي في users
            'id',        // المفتاح الأساسي في users لولي الأمر
            'user_id'    // المفتاح المحلي في student_profiles
        );
    }

    // علاقة ولي الأمر للطالب
    public function parentUser()
    {
        return $this->hasOneThrough(
            User::class,
            StudentProfile::class,
            'user_id',   // المفتاح الأجنبي في student_profiles
            'id',        // المفتاح الأساسي في users
            'id',        // المفتاح الأساسي في users للطالب
            'parent_id'  // المفتاح المحلي في student_profiles
        );
    }

    // المجموعات الدراسية المسكن فيها الطالب
    public function studyGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_students', 'student_id', 'study_group_id')
            ->withPivot('status', 'enrolled_at', 'notes')
            ->withTimestamps();
    }

    // المجموعات التي يقوم المعلم بتدريسها (كمعلم رئيسي)
    public function teachingGroups()
    {
        return $this->hasMany(StudyGroup::class, 'teacher_id');
    }

    // جميع المجموعات التي يدرّس فيها المعلم (رئيسي أو مساعد)
    public function assignedTeachingGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_teachers', 'user_id', 'study_group_id')
            ->withPivot('is_primary', 'role_title')
            ->withTimestamps();
    }

    // المجموعات التي يشرف عليها المشرف الأكاديمي (كمشرف رئيسي)
    public function supervisedGroups()
    {
        return $this->hasMany(StudyGroup::class, 'supervisor_id');
    }

    // جميع المجموعات التي يشرف عليها المشرف (رئيسي أو مساعد)
    public function assignedSupervisedGroups()
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_supervisors', 'user_id', 'study_group_id')
            ->withPivot('is_primary', 'notes')
            ->withTimestamps();
    }

    // العمليات وسجل التدقيق المرتبط بالمستخدم
    public function auditLogs()
    {
        return $this->hasMany(SystemAuditLog::class, 'user_id');
    }

    public function createdVouchers()
    {
        return $this->hasMany(Voucher::class, 'created_by');
    }

    public function createdLevels()
    {
        return $this->hasMany(Level::class, 'created_by');
    }

    public function updatedLevels()
    {
        return $this->hasMany(Level::class, 'updated_by');
    }

    public function createdCourses()
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function updatedCourses()
    {
        return $this->hasMany(Course::class, 'updated_by');
    }

    public function createdPackages()
    {
        return $this->hasMany(Package::class, 'created_by');
    }

    public function updatedPackages()
    {
        return $this->hasMany(Package::class, 'updated_by');
    }

    public function uploadedResources()
    {
        return $this->hasMany(LevelResource::class, 'created_by');
    }

    public function createdExams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    /**
     * فحص شامل لسلامة القيود المحاسبية وسجل العمليات قبل محاولة حذف المستخدم.
     * يُعيد مصفوفة توضح كافة العمليات والبيانات المرتبطة بالحساب لمنع الحذف والحفاظ على نزاهة النظام.
     */
    public function checkDeletionIntegrity(): array
    {
        $reasons = [];

        // 1. سندات مالية مدخلة ومعتمدة بواسطة المستخدم
        $vouchersCreated = Voucher::where('created_by', $this->id)->count();
        if ($vouchersCreated > 0) {
            $reasons['vouchers_created'] = "قام بإنشاء واعتماد {$vouchersCreated} سند مالي (قبض/صرف)";
        }

        // 2. حساب مالي وسندات مسجلة على حسابه
        if ($this->financialAccount) {
            $accId = $this->financialAccount->id;
            $accountVouchers = Voucher::where('account_id', $accId)
                ->orWhere('cash_or_bank_account_id', $accId)
                ->count();
            if ($accountVouchers > 0) {
                $reasons['account_vouchers'] = "مرتبط بـ {$accountVouchers} حركة وقيد مالي في شجرة الحسابات";
            }
            if (abs((float) $this->financialAccount->current_balance) > 0.001) {
                $curr = $this->financialAccount->currency ?? 'YER';
                $bal = number_format($this->financialAccount->current_balance, 2);
                $reasons['account_balance'] = "حسابه المالي لديه رصيد حالي غير صفري ({$bal} {$curr})";
            }
        }

        // 3. مستويات أكاديمية وتعديل رسوم وتسعيرة
        $levelsCreated = Level::where('created_by', $this->id)->count();
        if ($levelsCreated > 0) {
            $reasons['levels_created'] = "قام بإضافة وإنشاء {$levelsCreated} مستوى دراسي";
        }
        $levelsUpdated = Level::where('updated_by', $this->id)->count();
        if ($levelsUpdated > 0) {
            $reasons['levels_updated'] = "قام بتعديل بيانات أو تسعيرة {$levelsUpdated} مستوى دراسي";
        }

        // 4. دورات تعليمية
        $coursesCreated = Course::where('created_by', $this->id)->count();
        if ($coursesCreated > 0) {
            $reasons['courses_created'] = "قام بإضافة {$coursesCreated} دورة تعليمية";
        }
        $coursesUpdated = Course::where('updated_by', $this->id)->count();
        if ($coursesUpdated > 0) {
            $reasons['courses_updated'] = "قام بتحديث وتعديل {$coursesUpdated} دورة تعليمية";
        }

        // 5. باقات التدريب وتسعيرتها
        $packagesCreated = Package::where('created_by', $this->id)->count();
        if ($packagesCreated > 0) {
            $reasons['packages_created'] = "قام بإضافة {$packagesCreated} باقة تدريبية";
        }
        $packagesUpdated = Package::where('updated_by', $this->id)->count();
        if ($packagesUpdated > 0) {
            $reasons['packages_updated'] = "قام بتعديل تسعيرة {$packagesUpdated} باقة تدريبية";
        }

        // 6. موارد ومناهج تعليمية مرفوعة
        $resourcesUploaded = LevelResource::where('created_by', $this->id)->count();
        if ($resourcesUploaded > 0) {
            $reasons['resources_uploaded'] = "قام برفع وإضافة {$resourcesUploaded} ملف/كتاب في بنك المناهج";
        }

        // 7. اختبارات تم تأليفها أو مراجعتها
        $examsCreated = Exam::where('created_by', $this->id)->count();
        if ($examsCreated > 0) {
            $reasons['exams_created'] = "قام بإنشاء وتأليف {$examsCreated} اختبار وكويز";
        }
        $examsReviewed = ExamAttempt::where('reviewed_by', $this->id)->count();
        if ($examsReviewed > 0) {
            $reasons['exams_reviewed'] = "قام بمراجعة واعتماد نتيحة {$examsReviewed} اختبار";
        }

        // 8. محاولات ونتائج اختبارات الطلاب
        $attemptsCount = ExamAttempt::where('user_id', $this->id)->count();
        if ($attemptsCount > 0) {
            $reasons['exam_attempts'] = "لديه {$attemptsCount} محاولة ونتيجة اختبار معتمدة";
        }

        // 9. تدريس أو إشراف أو قيد في المجموعات الدراسية
        $teachingCount = StudyGroup::where('teacher_id', $this->id)->count()
            + DB::table('study_group_teachers')->where('user_id', $this->id)->count();
        if ($teachingCount > 0) {
            $reasons['teaching_groups'] = "معلم ومسند إليه التدريس في {$teachingCount} مجموعة دراسية";
        }

        $supervisingCount = StudyGroup::where('supervisor_id', $this->id)->count()
            + DB::table('study_group_supervisors')->where('user_id', $this->id)->count();
        if ($supervisingCount > 0) {
            $reasons['supervising_groups'] = "مشرف أكاديمي على {$supervisingCount} مجموعة دراسية";
        }

        $enrolledCount = DB::table('study_group_students')->where('student_id', $this->id)->count();
        if ($enrolledCount > 0) {
            $reasons['enrolled_groups'] = "مسكن ومقيد في {$enrolledCount} مجموعة دراسية";
        }

        // 10. حصص وجلسات تدريسية مسجلة
        $sessionsCount = GroupSession::where('teacher_id', $this->id)->count();
        if ($sessionsCount > 0) {
            $reasons['sessions_taught'] = "قام بتدريس {$sessionsCount} حصة وجلسة تعليمية مسجلة";
        }

        // 11. تقييمات يومية ورصد حضور ومشاركات
        $evalsCount = GroupStudentEvaluation::where('student_id', $this->id)->count();
        if ($evalsCount > 0) {
            $reasons['evaluations_received'] = "مسجل له {$evalsCount} سجل تقييم يومي وحضور ومشاركات";
        }

        // 12. أنشطة وتكليفات منشورة
        $activitiesCount = GroupActivity::where('user_id', $this->id)->count();
        if ($activitiesCount > 0) {
            $reasons['activities_posted'] = "قام بنشر {$activitiesCount} نشاط/واجب للمجموعات";
        }

        // 13. أقساط دراسية
        $installmentsCount = StudentInstallment::where('user_id', $this->id)->count();
        if ($installmentsCount > 0) {
            $reasons['student_installments'] = "لديه جدول أقساط دراسية ({$installmentsCount} قسط)";
        }

        // 14. تسجيلات المستويات الأكاديمية
        $enrollmentsCount = StudentEnrollment::where('user_id', $this->id)->count();
        if ($enrollmentsCount > 0) {
            $reasons['student_enrollments'] = "لديه قيد رسمي في {$enrollmentsCount} مستوى أكاديمي";
        }

        // 15. عقود معلمين
        $contractsCount = TeacherContract::where('teacher_id', $this->id)->count();
        if ($contractsCount > 0) {
            $reasons['teacher_contracts'] = 'مسجل له عقد مالي وإداري كمعلم';
        }

        // 16. حجوزات حصص خاصة
        $bookingsCount = Booking::where('student_id', $this->id)->orWhere('teacher_id', $this->id)->count();
        if ($bookingsCount > 0) {
            $reasons['bookings'] = "لديه {$bookingsCount} حجز حصة فردية";
        }

        // 17. سجل تدقيق النظام
        $auditLogsCount = SystemAuditLog::where('user_id', $this->id)->count();
        if ($auditLogsCount > 0) {
            $reasons['audit_logs'] = "مسجل له {$auditLogsCount} عملية موثقة في سجل النظام";
        }

        return $reasons;
    }

    /**
     * التحقق السريع هل الحساب لديه عمليات سابقة تمنع حذفه
     */
    public function hasSystemOperations(): bool
    {
        return ! empty($this->checkDeletionIntegrity());
    }

    public function courseGrades()
    {
        return $this->hasMany(StudentCourseGrade::class, 'student_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    public function teacherContract()
    {
        return $this->hasOne(TeacherContract::class, 'teacher_id')->where('is_active', true);
    }

    public function teacherContracts()
    {
        return $this->hasMany(TeacherContract::class, 'teacher_id');
    }

    public function teacherEntitlements()
    {
        return $this->hasMany(TeacherEntitlement::class, 'teacher_id');
    }
}
