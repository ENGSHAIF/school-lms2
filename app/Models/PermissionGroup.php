<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'description',
        'color',
        'icon',
        'is_system',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * المستخدمون المنتمون لمجموعة الصلاحيات هذه
     */
    public function users()
    {
        return $this->hasMany(User::class, 'permission_group_id');
    }

    /**
     * هل هذه مجموعة مدير النظام الفائقة؟
     */
    public function isAdminGroup(): bool
    {
        return $this->slug === 'admin' || ($this->is_system && $this->slug === 'admin');
    }

    /**
     * فحص هل تملك المجموعة صلاحية محددة
     */
    public function hasPermission(string $permission): bool
    {
        // مدير النظام يملك كافة الصلاحيات تلقائياً ودائماً بدون استثناء
        if ($this->isAdminGroup()) {
            return true;
        }

        $perms = $this->permissions ?? [];

        // التحقق من النجمة الشاملة
        if (in_array('*', $perms, true)) {
            return true;
        }

        // التحقق المباشر من الصلاحية
        if (in_array($permission, $perms, true) || ! empty($perms[$permission])) {
            return true;
        }

        // التحقق من صلاحية الوحدة الشاملة (مثل finance.*)
        $parts = explode('.', $permission);
        if (count($parts) > 1 && in_array($parts[0].'.*', $perms, true)) {
            return true;
        }

        // التحقق من التوافقية العكسية مع الصلاحيات القديمة (Legacy aliases)
        $aliases = static::legacyAliases();
        if (isset($aliases[$permission])) {
            foreach ($aliases[$permission] as $alias) {
                if (in_array($alias, $perms, true) || ! empty($perms[$alias])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * خريطة التوافقية مع المفاتيح القديمة
     */
    public static function legacyAliases(): array
    {
        return [
            'manage_courses' => ['curriculum.view', 'curriculum.edit', 'curriculum.create', 'curriculum.pricing'],
            'manage_packages' => ['curriculum.packages', 'curriculum.pricing'],
            'upload_level_resources' => ['curriculum.resources_manage'],
            'share_group_resources' => ['curriculum.resources_share'],
            'view_reports' => ['reports.academic', 'reports.financial', 'reports.terms_comparison', 'reports.print_all'],
            'manage_users' => ['system.users_manage', 'system.users_view', 'system.users_delete', 'system.groups_manage'],
            'delete_user' => ['system.users_delete'],
            'create_receipt_voucher' => ['finance.receipt_create', 'finance.view'],
            'create_payment_voucher' => ['finance.payment_create', 'finance.view'],
            'print_statement' => ['finance.statements', 'students.statement'],
            'edit_voucher' => ['finance.voucher_edit'],
            'delete_voucher' => ['finance.voucher_delete'],
            'approve_student' => ['students.approve'],
            'assign_course' => ['students.approve', 'curriculum.view'],
            'groups.create' => ['groups.create'],
            'groups.edit' => ['groups.edit', 'groups.assign_teachers', 'groups.enroll_students', 'groups.toggle_status'],
            'groups.live' => ['groups.live'],
            'groups.activities' => ['groups.activities'],
            'groups.delete' => ['groups.delete'],
        ];
    }

    /**
     * تبويبات النظام الرئيسية لتنظيم الشاشات
     */
    public static function systemCategories(): array
    {
        return [
            'academic' => [
                'title' => 'الشاشات الأكاديمية والتعليمية',
                'title_en' => 'Academic & Educational Screens',
                'icon' => '🎓',
                'color' => 'indigo',
            ],
            'financial' => [
                'title' => 'شاشات الحسابات والمالية والمخازن',
                'title_en' => 'Accounting, Finance & Inventory',
                'icon' => '💼',
                'color' => 'emerald',
            ],
            'reports' => [
                'title' => 'مركز التقارير والإحصائيات الشاملة',
                'title_en' => 'Reports & Comparative Analytics',
                'icon' => '📊',
                'color' => 'amber',
            ],
            'system' => [
                'title' => 'إدارة النظام والمستخدمين والأمان',
                'title_en' => 'System Administration & Security',
                'icon' => '⚙️',
                'color' => 'slate',
            ],
            'portal' => [
                'title' => 'بوابات أولياء الأمور والطلاب والمتابعة',
                'title_en' => 'Parent Portal & Student Follow-up',
                'icon' => '👨‍👧‍👦',
                'color' => 'purple',
            ],
        ];
    }

    /**
     * قاموس الصلاحيات الشامل الممنهج لكافة شاشات وعمليات وتقارير النظام
     */
    public static function allSystemPermissions(): array
    {
        return [
            // ================= 1. الشاشات الأكاديمية والتعليمية =================
            'groups' => [
                'category' => 'academic',
                'title' => 'شاشة المجموعات الدراسية والفصول التفاعلية',
                'title_en' => 'Study Groups & Live Classroom',
                'icon' => '👥',
                'desc' => 'إدارة الشعب الدراسية، تسكين الطلاب، البث المباشر Air Class، رصد التقييمات، وتعيين الكوادر.',
                'permissions' => [
                    'groups.view' => [
                        'label' => 'عرض واستعراض قائمة المجموعات والشعب',
                        'desc' => 'الاطلاع على الشعب المسكنة والطلاب الملتحقين ومواعيد الحصص',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'groups.create' => [
                        'label' => 'إضافة وإنشاء مجموعة دراسية جديدة',
                        'desc' => 'تكوين فصول جديدة وتحديد الترم والدفعة والمعلم الرئيسي والدبلوم',
                        'badge' => 'إضافة ➕',
                        'type' => 'create',
                    ],
                    'groups.edit' => [
                        'label' => 'تعديل بيانات المجموعة وتوقيتها والترم والاسم',
                        'desc' => 'تعديل اسم المجموعة (عربي/إنجليزي)، المواعيد، تاريخ البداية والدبلوم',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'groups.assign_teachers' => [
                        'label' => 'تعيين وتعديل المعلمين والمشرفين للشعبة',
                        'desc' => 'تحديد المعلم الرئيسي والمعلمين المساعدين والمشرفين الأكاديميين',
                        'badge' => 'كوادر 👨‍🏫',
                        'type' => 'assign',
                    ],
                    'groups.enroll_students' => [
                        'label' => 'تسكين الطلاب ونقلهم بين المجموعات',
                        'desc' => 'تسكين الطلاب المعتمدين في المجموعة ونقلهم لشعب أخرى وحفظ الأرشيف',
                        'badge' => 'تسكين 🎓',
                        'type' => 'enroll',
                    ],
                    'groups.live' => [
                        'label' => 'فتح وبدء البث التفاعلي المباشر (Air Class)',
                        'desc' => 'بدء البث الحي الجماعي، تشغيل الكاميرات، إدارة المايكروفونات والسبورة',
                        'badge' => 'بث حي 📡',
                        'type' => 'live',
                    ],
                    'groups.evaluations' => [
                        'label' => 'رصد الحضور والتقييم اليومي ومنح النجوم ⭐',
                        'desc' => 'فتح كشف تحضير وتقييم الجلسة اليومية ورصد التفاعل ومنح النجوم للطلاب',
                        'badge' => 'رصد ⭐',
                        'type' => 'eval',
                    ],
                    'groups.activities' => [
                        'label' => 'استدعاء ومشاركة كتب المستوى والأنشطة والمحادثة',
                        'desc' => 'استدعاء مناهج المستوى وإرسال التكليفات والملاحظات الصوتية واستلام أوراق الإجابات',
                        'badge' => 'أنشطة 📚',
                        'type' => 'activity',
                    ],
                    'groups.toggle_status' => [
                        'label' => 'إيقاف / تنشيط الشعبة الدراسية',
                        'desc' => 'إمكانية إيقاف المجموعة مؤقتاً أو إعادة تنشيطها دون فقدان البيانات',
                        'badge' => 'تشغيل ⚡',
                        'type' => 'toggle',
                    ],
                    'groups.delete' => [
                        'label' => 'حذف المجموعة (محمية: يمنع الحذف إذا بدأت الدراسة أو وجد طلاب)',
                        'desc' => 'صلاحية حذف المجموعة نهائياً فقط عند عدم وجود طلاب أو جلسات دراسية فيها',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                ],
            ],

            'students' => [
                'category' => 'academic',
                'title' => 'شاشة شؤون الطلاب والقبول والتسجيل',
                'title_en' => 'Students Affairs & Admissions',
                'icon' => '🎓',
                'desc' => 'إدارة ملفات الطلاب، التسجيل الجديد، فحص القبول، اعتماد المستويات، وكشوف الحركة.',
                'permissions' => [
                    'students.view' => [
                        'label' => 'استعراض سجلات وبيانات الطلاب',
                        'desc' => 'عرض قائمة الطلاب والملف التعليمي والحالة الأكاديمية والبحث في السجلات',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'students.create' => [
                        'label' => 'تسجيل وإضافة طالب جديد',
                        'desc' => 'إضافة طالب جديد للنظام وتوليد كود الدخول وفتح ملف دراسي',
                        'badge' => 'إضافة ➕',
                        'type' => 'create',
                    ],
                    'students.edit' => [
                        'label' => 'تعديل بيانات وحالة الطالب',
                        'desc' => 'تعديل الاسم ورقم الهاتف والبريد وحالة التفعيل أو التعليق',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'students.delete' => [
                        'label' => 'حذف / أرشفة حساب الطالب',
                        'desc' => 'إمكانية إيقاف أو حذف حساب الطالب نهائياً من المنصة',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                    'students.approve' => [
                        'label' => 'اعتماد الطالب وتخصيص المستوى الأكاديمي',
                        'desc' => 'اعتماد نتائج فحص المستوى وتسكين الطالب في المستوى المناسب',
                        'badge' => 'اعتماد ✅',
                        'type' => 'approve',
                    ],
                    'students.revoke_approval' => [
                        'label' => 'إلغاء اعتماد وتسكين المستويات للطلاب الجدد',
                        'desc' => 'إلغاء الاعتماد للطالب وإعادته لحالة بانتظار الاعتماد ما دام طالباً جديداً لم يبدأ دراسة دورات أو حضور حصص',
                        'badge' => 'إلغاء اعتماد ↩️',
                        'type' => 'revoke',
                    ],
                    'students.statement' => [
                        'label' => 'استعراض وطباعة كشف حساب وحركة الطالب الكاملة',
                        'desc' => 'طباعة حركة الطالب الأكاديمية وسجل الرسوم والمدفوعات والمستندات بجميع تواريخها',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                ],
            ],

            'teachers' => [
                'category' => 'academic',
                'title' => 'شاشة الكادر التعليمي والمعلمين',
                'title_en' => 'Teachers & Faculty Management',
                'icon' => '👨‍🏫',
                'desc' => 'إدارة المعلمين والمحاضرين، العقود والنسب، جداول المواعيد والحصص الفردية، وكشوف الحسابات.',
                'permissions' => [
                    'teachers.view' => [
                        'label' => 'استعراض قائمة المعلمين والملفات',
                        'desc' => 'عرض ملفات المعلمين والاطلاع على التخصصات والحسابات المرتبطة',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'teachers.create' => [
                        'label' => 'إضافة وتعيين معلم جديد',
                        'desc' => 'تسجيل معلم جديد وتحديد لغة التدريس وتوليد كود الدخول',
                        'badge' => 'إضافة ➕',
                        'type' => 'create',
                    ],
                    'teachers.edit' => [
                        'label' => 'تعديل بيانات ومستحقات المعلم',
                        'desc' => 'تعديل بيانات التواصل ونسبة المعلم أو أجر الحصة',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'teachers.delete' => [
                        'label' => 'تعطيل / حذف حساب المعلم',
                        'desc' => 'إيقاف حساب المعلم أو سحب صلاحيات دخوله للنظام',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                    'teachers.schedule' => [
                        'label' => 'إدارة جدول المواعيد والحجوزات الحية 1-on-1',
                        'desc' => 'تحديد أوقات التوفر وحجز الحصص الفردية التجريبية والمكثفة مع الطلاب',
                        'badge' => 'مواعيد ⏱️',
                        'type' => 'schedule',
                    ],
                    'teachers.statement' => [
                        'label' => 'استعراض وطباعة كشف حساب ومستحقات المعلم',
                        'desc' => 'معاينة كشف الحركة المالية للحصص المنفذة وسندات الصرف',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                ],
            ],

            'curriculum' => [
                'category' => 'academic',
                'title' => 'شاشة المناهج والمستويات وبنك الموارد والتسعيرة',
                'title_en' => 'Curriculum, Courseware & Pricing',
                'icon' => '📚',
                'desc' => 'إدارة خطط CEFR، أسماء المستويات عربي/إنجليزي، تسعيرة الدورات والعملات، وبنك الكتب والمكتبة الدائمة.',
                'permissions' => [
                    'curriculum.view' => [
                        'label' => 'استعراض المناهج والدورات والمستويات',
                        'desc' => 'الاطلاع على خارطة المناهج وخطة المستويات المعتمدة',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'curriculum.create' => [
                        'label' => 'إضافة دورات ومستويات تعليمية جديدة',
                        'desc' => 'إنشاء مستويات جديدة ودورات تخصصية في النظام',
                        'badge' => 'إضافة ➕',
                        'type' => 'create',
                    ],
                    'curriculum.edit' => [
                        'label' => 'تعديل بيانات وأسماء المستويات (عربي / إنجليزي)',
                        'desc' => 'تعديل المسميات والمحاور التعليمية وشروط الانتقال',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'curriculum.pricing' => [
                        'label' => 'تعديل تسعيرة المستويات والدورات والعملات',
                        'desc' => 'تحديد وتعديل أسعار التسجيل بكل مستوى بمختلف العملات (ريال يمني، سعودي، دولار)',
                        'badge' => 'تسعيرة 💰',
                        'type' => 'pricing',
                    ],
                    'curriculum.resources_manage' => [
                        'label' => 'رفع وإدارة بنك موارد المستوى الدائم (كتب، صوتيات، فيديوهات، أنشطة)',
                        'desc' => 'رفع الكتب، التسجيلات الصوتية، فيديوهات الشرح، والأنشطة الدائمة بكل مستوى',
                        'badge' => 'موارد 📁',
                        'type' => 'resources',
                    ],
                    'curriculum.resources_share' => [
                        'label' => 'مشاركة موارد المستوى للمجموعات والطلاب يومياً',
                        'desc' => 'استدعاء ونشر كتب وصوتيات وفيديوهات المستوى لداخل المجموعات',
                        'badge' => 'مشاركة 📤',
                        'type' => 'share',
                    ],
                    'curriculum.packages' => [
                        'label' => 'إدارة باقات الحصص وأسعارها وعروض التوفير',
                        'desc' => 'تحديث أسعار باقات الـ 25 دقيقة وحزم الحصص الفردية',
                        'badge' => 'باقات 💳',
                        'type' => 'packages',
                    ],
                ],
            ],

            'evaluations' => [
                'category' => 'academic',
                'title' => 'شاشة التقييم الدوري والحضور وسجل درجات الدبلوم',
                'title_en' => 'Evaluations, Attendance & Diploma Grading',
                'icon' => '📋',
                'desc' => 'رصد الحضور اليومي، التقييم الأسبوعي والشهري، احتساب درجات نهاية الدبلوم 100%، والطباعة الرسمية.',
                'permissions' => [
                    'evaluations.view' => [
                        'label' => 'استعراض سجلات التقييم وجلسات الحضور',
                        'desc' => 'الاطلاع على تقارير الحضور والغياب اليومي والأسبوعي والشهري للمجموعات',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'evaluations.create' => [
                        'label' => 'رصد الحضور والتفاعل اليومي ومنح النجوم ⭐',
                        'desc' => 'رصد حضور وغياب الجلسة، تفاعل الصوت والمحادثة، ومنح النجوم للطلاب',
                        'badge' => 'رصد ⭐',
                        'type' => 'create',
                    ],
                    'evaluations.edit' => [
                        'label' => 'تعديل بيانات الجلسة وسجل التقييم',
                        'desc' => 'تعديل درجات الجلسة المنعقدة وتحديث ملخص الدرس والواجبات',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'evaluations.delete' => [
                        'label' => 'حذف جلسة تقييم وإلغاء رصيد نجومها',
                        'desc' => 'إلغاء جلسة مسجلة مع التراجع التلقائي عن نجوم الطلاب المرتبطة بها',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                    'evaluations.print_daily' => [
                        'label' => 'طباعة كشف التحضير والتقييم اليومي الرسمي',
                        'desc' => 'تصدير وطباعة كشف تحضير الجلسة مع ترويسة المدرسة وتواقيع الاعتماد',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                    'evaluations.print_final' => [
                        'label' => 'طباعة كشف درجات الدبلوم النهائي المعتمد (100%)',
                        'desc' => 'طباعة كشف درجات التخرج بنسب الحضور والمشاركات والواجبات والكويزات',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                ],
            ],

            'exams' => [
                'category' => 'academic',
                'title' => 'شاشة الكويزات والاختبارات وبنك الأسئلة',
                'title_en' => 'Quizzes, Exams & Question Bank',
                'icon' => '🎯',
                'desc' => 'تصميم الكويزات، بنك الأسئلة المتعدد، إدخال يدوي/Word/Excel، الترتيب العشوائي، والتصحيح.',
                'permissions' => [
                    'exams.view' => [
                        'label' => 'استعراض الاختبارات والكويزات ومحاولات الطلاب',
                        'desc' => 'معاينة بنك الاختبارات ونتائج درجات الطلاب ونسب النجاح',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'exams.create' => [
                        'label' => 'إنشاء وتصميم اختبار أو كويز جديد',
                        'desc' => 'تصميم كويز أسبوعي أو امتحان نصفي أو نهائي للدورة والمستوى',
                        'badge' => 'إضافة ➕',
                        'type' => 'create',
                    ],
                    'exams.questions_manage' => [
                        'label' => 'إدارة بنك الأسئلة (يدوي، Word، Excel، خيارات)',
                        'desc' => 'إضافة وتعديل الأسئلة والخيارات وتحديد الإجابة الصحيحة ودرجة كل سؤال',
                        'badge' => 'أسئلة ❓',
                        'type' => 'questions',
                    ],
                    'exams.publish' => [
                        'label' => 'مشاركة ونشر الاختبار للمجموعة وتحديد وقت النتيجة',
                        'desc' => 'إرسال الاختبار للشعبة مع خيار إظهار النتيجة فوراً أو بعد المراجعة وتغيير ترتيب الأسئلة عشوائياً',
                        'badge' => 'نشر 🚀',
                        'type' => 'publish',
                    ],
                    'exams.grade' => [
                        'label' => 'مراجعة وتصحيح إجابات الطلاب وإصدار النتائج',
                        'desc' => 'الاطلاع على إجابات الطلاب التفصيلية وتعديل الدرجات واعتماد النتيجة الرسمية',
                        'badge' => 'تصحيح ✍️',
                        'type' => 'grade',
                    ],
                ],
            ],

            'exams_management' => [
                'category' => 'academic',
                'title' => 'شاشة إدارة الاختبارات الشاملة والشهائد الأكاديمية',
                'title_en' => 'Comprehensive Exam Management & Certificates',
                'icon' => '🎓',
                'desc' => 'إدارة الاختبارات وتوزيعها للدورات والمجموعات، ضبط سلالم الدرجات (100%)، رصد وتعديل النتائج، طباعة الكشوفات التفصيلية والإجمالية، وتصميم وإصدار الشهائد الملكية دفعة واحدة.',
                'permissions' => [
                    'exams_management.view' => [
                        'label' => 'استعراض تبويب الاختبارات الشاملة والشهائد',
                        'desc' => 'الاطلاع على قائمة الاختبارات وسلالم الدرجات وكشوفات الطلاب والشهائد الصادرة',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'exams_management.scales' => [
                        'label' => 'تحديد وضبط أوزان درجات الدورة والدبلوم (نظري، عملي، أنشطة، مشاركة، نصفي، نهائي)',
                        'desc' => 'إنشاء وتعديل توزيع الدرجات ونسبة النجاح للدورات والمجموعات التخصصية',
                        'badge' => 'أوزان ⚖️',
                        'type' => 'scales',
                    ],
                    'exams_management.assign' => [
                        'label' => 'إسناد وتخصيص الاختبارات لمستوى أو دورة أو مجموعة أو طلاب محددين',
                        'desc' => 'توجيه الاختبارات وتحديد الدرجة الموزونة لكل كويز أو امتحان',
                        'badge' => 'إسناد 🎯',
                        'type' => 'assign',
                    ],
                    'exams_management.grading' => [
                        'label' => 'رصد ومراجعة وتعديل درجات الطلاب واعتمادها',
                        'desc' => 'إدخال درجات الطلاب للمجموعات، تعديل النتائج، والمراجعة الأكاديمية',
                        'badge' => 'رصد 📝',
                        'type' => 'grading',
                    ],
                    'exams_management.print_sheets' => [
                        'label' => 'تجهيز وطباعة كشوفات درجات الطلاب (إجمالي وتفصيلي)',
                        'desc' => 'تصدير وطباعة كشوفات الدرجات الرسمية للمجموعات مع توقيع الإدارة الأكاديمية',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                    'exams_management.certificates_issue' => [
                        'label' => 'تصميم وإصدار شهائد التخرج للدورات والدبلوم دفعة واحدة',
                        'desc' => 'توليد الشهائد الأكاديمية لكافة الطلاب الناجحين بضغطة زر وتوليد أرقام التحقق والباركود',
                        'badge' => 'إصدار 📜',
                        'type' => 'issue',
                    ],
                    'exams_management.certificates_print' => [
                        'label' => 'طباعة الشهائد الملكية الفاخرة فردياً أو دفعة واحدة',
                        'desc' => 'طباعة الشهائد المعتمدة بجودة عالية مع الإطار الملكي والباركود الرقمي',
                        'badge' => 'طباعة 🏆',
                        'type' => 'print',
                    ],
                ],
            ],

            // ================= 2. شاشات الحسابات والمالية والمخازن =================
            'finance_vouchers' => [
                'category' => 'financial',
                'title' => 'شاشة سندات القبض والصرف الرسمية',
                'title_en' => 'Receipt & Payment Vouchers',
                'icon' => '🧾',
                'desc' => 'تحصيل الرسوم وإصدار سندات القبض الآلية، صرف أتعاب المعلمين والمصروفات التشغيلية.',
                'permissions' => [
                    'finance.view' => [
                        'label' => 'استعراض دفتر اليومية وسجل السندات الصادرة',
                        'desc' => 'الاطلاع على جميع سندات القبض والصرف وحركات اليومية',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'finance.receipt_create' => [
                        'label' => 'إصدار سند قبض للطالب وحركات التحصيل',
                        'desc' => 'تحصيل الرسوم الدراسية وإصدار سندات القبض الآلية مع إشعار الواتساب',
                        'badge' => 'قبض 📥',
                        'type' => 'create',
                    ],
                    'finance.payment_create' => [
                        'label' => 'إصدار سند صرف للمعلم / المصروفات التشغيلية',
                        'desc' => 'صرف مستحقات المعلمين والنفقات والمشتريات وإثباتها محاسبياً',
                        'badge' => 'صرف 📤',
                        'type' => 'create',
                    ],
                    'finance.voucher_edit' => [
                        'label' => 'تعديل السندات المالية والدفترية',
                        'desc' => 'تعديل بيانات السند والمبلغ والبيان والرقم الدفتري قبل الترحيل',
                        'badge' => 'تعديل ✏️',
                        'type' => 'edit',
                    ],
                    'finance.voucher_delete' => [
                        'label' => 'حذف / إلغاء السند المالي وضبط الأرصدة',
                        'desc' => 'إلغاء السند المالي مع إعادة ضبط أرصدة الحسابات والصناديق تلقائياً',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                ],
            ],

            'finance_accounts' => [
                'category' => 'financial',
                'title' => 'شاشة قيود اليومية والحسابات والصناديق والمخازن',
                'title_en' => 'Journal, Accounts, Funds & Inventory',
                'icon' => '💼',
                'desc' => 'إدارة الصناديق النقدية، الحسابات البنكية، قيود اليومية، دليل الحسابات، والمخازن والمطبوعات.',
                'permissions' => [
                    'finance.journal_entry' => [
                        'label' => 'تسجيل قيود اليومية والتسويات المحاسبية',
                        'desc' => 'إنشاء قيود تسوية يدوية وقيود إقفال الفترات المالية',
                        'badge' => 'قيود 📑',
                        'type' => 'journal',
                    ],
                    'finance.funds_banks' => [
                        'label' => 'إدارة الصناديق النقدية والحسابات البنكية',
                        'desc' => 'متابعة أرصدة الصناديق في الفروع والتحويلات بين الصناديق والبنوك',
                        'badge' => 'صناديق 🏦',
                        'type' => 'funds',
                    ],
                    'finance.accounts_chart' => [
                        'label' => 'إدارة شجرة الحسابات والدليل المحاسبي',
                        'desc' => 'إضافة وتعديل الحسابات العامة والفرعية في دليل الحسابات الموحد',
                        'badge' => 'شجرة 🌳',
                        'type' => 'chart',
                    ],
                    'finance.statements' => [
                        'label' => 'استعراض وطباعة كشوف الحسابات وميزان المراجعة',
                        'desc' => 'طباعة كشف حساب تحليلي أو إجمالي لأي طالب أو معلم أو صندوق مع الأرصدة',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                    'finance.inventory' => [
                        'label' => 'إدارة المخازن والكتب المطبوعة والمستودع',
                        'desc' => 'متابعة مخزون الكتب الدراسية والمطبوعات والشهادات وسندات الإدخال والإخراج',
                        'badge' => 'مخازن 📦',
                        'type' => 'inventory',
                    ],
                ],
            ],

            // ================= 3. مركز التقارير والإحصائيات الشاملة =================
            'reports' => [
                'category' => 'reports',
                'title' => 'شاشة مركز التقارير والإحصائيات الشاملة والمقارنات',
                'title_en' => 'Reports & Comparative Analytics',
                'icon' => '📊',
                'desc' => 'تقارير أداء الطلاب، حركة الإيرادات والمصروفات، مقارنة الأترام والدفعات، وتصدير PDF.',
                'permissions' => [
                    'reports.academic' => [
                        'label' => 'تقارير أداء الطلاب والمجموعات الأكاديمية',
                        'desc' => 'تقارير نسب الحضور، نسب الإنجاز، وأداء المعلمين والمشرفين',
                        'badge' => 'أكاديمي 📈',
                        'type' => 'view',
                    ],
                    'reports.financial' => [
                        'label' => 'التقارير المالية والإيرادات والمصروفات',
                        'desc' => 'تقارير الدخل، المدفوعات، الأقساط المتبقية، وحركة الصناديق',
                        'badge' => 'مالي 💵',
                        'type' => 'view',
                    ],
                    'reports.terms_comparison' => [
                        'label' => 'تقارير مقارنة الأترام والدفعات والإحصائيات',
                        'desc' => 'مقارنة أداء الأترام المتعاقبة، أعداد الطلاب، ومعدل التفوق والتحصيل',
                        'badge' => 'مقارنات ⚖️',
                        'type' => 'compare',
                    ],
                    'reports.print_all' => [
                        'label' => 'تصدير وطباعة كافة التقارير الرسمية (PDF / Print)',
                        'desc' => 'صلاحية طباعة وتصدير جميع الكشوفات الرسمية من مركز التقارير',
                        'badge' => 'طباعة 🖨️',
                        'type' => 'print',
                    ],
                ],
            ],

            // ================= 4. إدارة النظام والمستخدمين والأمان =================
            'system_users' => [
                'category' => 'system',
                'title' => 'شاشة إدارة المستخدمين والموظفين والأمان',
                'title_en' => 'Users & Staff Management',
                'icon' => '👥',
                'desc' => 'إدارة حسابات الموظفين، تغيير كلمات المرور، معالجة طلبات الاستعادة، وتحديد الحالات.',
                'permissions' => [
                    'system.users_view' => [
                        'label' => 'استعراض قائمة المستخدمين والموظفين وطلبات الدخول',
                        'desc' => 'الاطلاع على جميع حسابات النظام وطلبات استعادة كلمة المرور',
                        'badge' => 'عرض 👁️',
                        'type' => 'view',
                    ],
                    'system.users_manage' => [
                        'label' => 'إضافة وتعديل المستخدمين وتسكينهم بالمجموعات',
                        'desc' => 'إنشاء مستخدم جديد، تعديل البيانات، وتعيين مجموعة الصلاحيات المناسبة',
                        'badge' => 'إدارة 👤',
                        'type' => 'manage',
                    ],
                    'system.passwords' => [
                        'label' => 'تغيير وتعيين كلمات مرور المستخدمين وإجبار التغيير',
                        'desc' => 'تعديل كلمات المرور ومعالجة طلبات الاسترجاع وإجبار المستخدم على التغيير',
                        'badge' => 'أمان 🔑',
                        'type' => 'security',
                    ],
                    'system.users_delete' => [
                        'label' => 'حذف المستخدمين المشروط (غير المقيدين بعمليات)',
                        'desc' => 'حذف الحسابات الجديدة أو التجريبية التي لا ترتبط بأي قيود أو عمليات وسندات سابقة في النظام',
                        'badge' => 'حذف 🗑️',
                        'type' => 'delete',
                    ],
                ],
            ],

            'system_settings' => [
                'category' => 'system',
                'title' => 'شاشة مجموعات الصلاحيات والهوية وإعدادات النظام',
                'title_en' => 'Permission Groups, Branding & Settings',
                'icon' => '⚙️',
                'desc' => 'إنشاء وتعديل مجموعات الصلاحيات، تخصيص الهوية المرئية والخلفيات، وبيانات المدرسة.',
                'permissions' => [
                    'system.groups_manage' => [
                        'label' => 'إنشاء وإدارة وتعديل مجموعات الصلاحيات والأدوار',
                        'desc' => 'إنشاء مجموعات جديدة، تخصيص الصلاحيات للشاشات، ونقل المستخدمين بين المجموعات',
                        'badge' => 'صلاحيات 🛡️',
                        'type' => 'groups',
                    ],
                    'system.branding' => [
                        'label' => 'إدارة الهوية المرئية والخلفيات والإعلانات',
                        'desc' => 'تخصيص الشعار، خلفيات الموقع، والشريط الإعلاني الترويجي العلوي',
                        'badge' => 'هوية 🖼️',
                        'type' => 'branding',
                    ],
                    'system.settings' => [
                        'label' => 'إعدادات النظام العامة وبيانات المدرسة',
                        'desc' => 'تعديل اسم المدرسة، أرقام التواصل، وبيانات الاتصال والاعتماد',
                        'badge' => 'إعدادات ⚙️',
                        'type' => 'settings',
                    ],
                    'system.licenses_view' => [
                        'label' => 'استعراض التراخيص والاعتمادات الرسمية للمنصة',
                        'desc' => 'صلاحية النقر على زر استعراض صور وتراخيص الموقع في العارض الآمن المحمي',
                        'badge' => 'تراخيص 📜',
                        'type' => 'licenses',
                    ],
                ],
            ],

            // ================= 5. شاشات بوابات أولياء الأمور والمتابعة =================
            'parent_portal' => [
                'category' => 'portal',
                'title' => 'شاشة بوابة أولياء الأمور ومتابعة الأبناء الشاملة',
                'title_en' => 'Parent Portal & Children Follow-up',
                'icon' => '👨‍👧‍👦',
                'desc' => 'تحديد ما يحق لأولياء الأمور استعراضه ومتابعته لأبنائهم في كافة المستويات من الرسوم، الحضور، التقييمات، السجلات 360.',
                'permissions' => [
                    'parent.dashboard' => [
                        'label' => 'دخول لوحة ولي الأمر والتبديل السلس بين الأبناء',
                        'desc' => 'الوصول للوحة التحكم الرئيسية الخاصة بولي الأمر والتنقل بين بطاقات أبنائه',
                        'badge' => 'رئيسية 🏠',
                        'type' => 'view',
                    ],
                    'parent.profile_view' => [
                        'label' => 'الاطلاع على بيانات الطالب والمرحلة والمستوى الأكاديمي',
                        'desc' => 'استعراض المرحلة التعليمية، المستوى الحالي المقيد به، والدورة التعليمية',
                        'badge' => 'الملف 🎓',
                        'type' => 'view',
                    ],
                    'parent.attendance_view' => [
                        'label' => 'متابعة سجل الحضور والغياب والتأخير اليومي ونسب الحضور',
                        'desc' => 'استعراض نسبة حضور الطالب، سجل الغياب، الحضور، والأعذار المقبولة في الحصص',
                        'badge' => 'حضور 📅',
                        'type' => 'view',
                    ],
                    'parent.evaluations_view' => [
                        'label' => 'الاطلاع على التقييمات اليومية ورصيد النجوم المكافأة ⭐',
                        'desc' => 'متابعة تقييم المعلم اليومي، التفاعل الصوتي والكتابي، والنجوم التشجيعية المحصودة',
                        'badge' => 'تقييم ⭐',
                        'type' => 'eval',
                    ],
                    'parent.activities_view' => [
                        'label' => 'متابعة التكليفات والواجبات المدرسية وأوراق العمل',
                        'desc' => 'الاطلاع على الواجبات المنشورة من المعلم، أوراق الإجابات، وملاحظات المعلم عليها',
                        'badge' => 'واجبات 📝',
                        'type' => 'activity',
                    ],
                    'parent.exams_view' => [
                        'label' => 'استعراض نتائج الاختبارات والكويزات الدورية وتفاصيل الدرجات',
                        'desc' => 'الاطلاع على نتائج الكويزات الأسبوعية، الاختبارات النصفية والنهائية، وملاحظات التصحيح',
                        'badge' => 'اختبارات 🏆',
                        'type' => 'view',
                    ],
                    'parent.finance_view' => [
                        'label' => 'متابعة الرسوم المالية والأقساط وسندات القبض وكشف الحساب',
                        'desc' => 'استعراض الرسوم المستحقة، الأقساط المسددة والمتبقية، وتنزيل إيصالات وسندات القبض المعتمدة',
                        'badge' => 'مالية 🧾',
                        'type' => 'finance',
                    ],
                    'parent.transcript_view' => [
                        'label' => 'استعراض وطباعة السجل الأكاديمي والمالي الشامل 360 (من الدخول حتى التخرج)',
                        'desc' => 'استعراض التقرير التراكمي الشامل لكافة مراحل ومستويات وشهادات الطالب من أول يوم حتى التخرج',
                        'badge' => 'سجل 360 📜',
                        'type' => 'report',
                    ],
                    'parent.live_schedule' => [
                        'label' => 'متابعة جدول الحصص ومواعيد البث المباشر التفاعلي',
                        'desc' => 'الاطلاع على أيام ومواعيد جلسات المجموعة، وتوقيت البث الحي Air Class',
                        'badge' => 'مواعيد ⏰',
                        'type' => 'view',
                    ],
                ],
            ],
        ];
    }

    /**
     * ألوان Tailwind لبادجات المجموعات
     */
    public function getBadgeClass(): string
    {
        return match ($this->color) {
            'rose', 'red' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-700',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700',
            'blue' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
            'emerald', 'green' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700',
            'amber', 'yellow' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700',
            'teal' => 'bg-teal-100 text-teal-800 border-teal-300 dark:bg-teal-900/30 dark:text-teal-300 dark:border-teal-700',
            'slate', 'gray' => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            default => 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700',
        };
    }
}
