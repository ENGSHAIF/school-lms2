<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('indigo');
            $table->string('icon')->default('🛡️');
            $table->boolean('is_system')->default(false); // إذا كانت مجموعة نظام أساسية كالأدمن لا تحذف ولا تلغى صلاحياتها
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('permission_group_id')->nullable()->after('role')->constrained('permission_groups')->nullOnDelete();
        });

        // إنشاء المجموعات القياسية الأولية للنظام
        $now = now();
        $adminId = DB::table('permission_groups')->insertGetId([
            'name' => 'مديرو النظام (Super Admins)',
            'name_en' => 'Super Admins',
            'slug' => 'admin',
            'description' => 'مجموعة الإدارة العليا ومدير النظام - تمتلك جميع الصلاحيات تلقائياً ودائماً على كافة الشاشات والعمليات والتقارير.',
            'color' => 'rose',
            'icon' => '👑',
            'is_system' => true,
            'permissions' => json_encode(['*']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $supervisorId = DB::table('permission_groups')->insertGetId([
            'name' => 'المشرفون الأكاديميون',
            'name_en' => 'Academic Supervisors',
            'slug' => 'supervisors',
            'description' => 'إدارة الطلاب واعتماد المستويات، إدارة المجموعات، بنك المناهج والموارد، رصد التقييمات، وبنك الأسئلة والكويزات.',
            'color' => 'purple',
            'icon' => '🎓',
            'is_system' => false,
            'permissions' => json_encode([
                'students.view', 'students.create', 'students.edit', 'students.approve', 'students.statement',
                'teachers.view', 'teachers.schedule',
                'curriculum.view', 'curriculum.edit', 'curriculum.resources_manage', 'curriculum.resources_share',
                'groups.view', 'groups.create', 'groups.edit', 'groups.live', 'groups.activities',
                'evaluations.view', 'evaluations.create', 'evaluations.edit', 'evaluations.print_daily', 'evaluations.print_final',
                'exams.view', 'exams.create', 'exams.questions_manage', 'exams.publish', 'exams.grade',
                'reports.academic', 'reports.terms_comparison', 'reports.print_all',
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $teacherId = DB::table('permission_groups')->insertGetId([
            'name' => 'المعلمون والمحاضرون',
            'name_en' => 'Teachers & Lecturers',
            'slug' => 'teachers',
            'description' => 'إدارة الفصول التفاعلية الحية (Air Class)، رصد الحضور والتقييم اليومي والنجوم، مشاركة مواد المستوى للمجموعات، وإجراء الكويزات.',
            'color' => 'blue',
            'icon' => '👨‍🏫',
            'is_system' => false,
            'permissions' => json_encode([
                'students.view',
                'curriculum.view', 'curriculum.resources_share',
                'groups.view', 'groups.live', 'groups.activities',
                'evaluations.view', 'evaluations.create', 'evaluations.print_daily',
                'exams.view', 'exams.publish', 'exams.grade',
                'reports.academic',
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $accountantId = DB::table('permission_groups')->insertGetId([
            'name' => 'قسم المحاسبة والمالية',
            'name_en' => 'Accounting & Finance',
            'slug' => 'accountants',
            'description' => 'إصدار سندات القبض والصرف، إدارة الصناديق ودفتر اليومية، كشوف الحسابات، تسعيرة الباقات والمستويات، والتقارير المالية.',
            'color' => 'emerald',
            'icon' => '💼',
            'is_system' => false,
            'permissions' => json_encode([
                'students.view', 'students.statement',
                'teachers.view', 'teachers.statement',
                'curriculum.view', 'curriculum.pricing', 'curriculum.packages',
                'finance.view', 'finance.receipt_create', 'finance.payment_create', 'finance.voucher_edit', 'finance.voucher_delete', 'finance.statements',
                'reports.financial', 'reports.print_all',
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $admissionsId = DB::table('permission_groups')->insertGetId([
            'name' => 'القبول والتسجيل وخدمة العملاء',
            'name_en' => 'Admissions & Customer Support',
            'slug' => 'admissions',
            'description' => 'تسجيل واستقبال الطلاب الجدد، متابعة جداول المواعيد وحجوزات المعلمين، واستعراض برامج المنصة والباقات.',
            'color' => 'amber',
            'icon' => '📋',
            'is_system' => false,
            'permissions' => json_encode([
                'students.view', 'students.create', 'students.edit',
                'teachers.view', 'teachers.schedule',
                'curriculum.view',
                'groups.view',
                'reports.academic',
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ربط المستخدمين الحاليين تلقائياً بمجموعات الصلاحيات المناسبة حسب أدوارهم
        DB::table('users')->where('role', 'admin')->update(['permission_group_id' => $adminId]);
        DB::table('users')->where('role', 'supervisor')->update(['permission_group_id' => $supervisorId]);
        DB::table('users')->where('role', 'teacher')->update(['permission_group_id' => $teacherId]);
        DB::table('users')->where('role', 'accountant')->update(['permission_group_id' => $accountantId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['permission_group_id']);
            $table->dropColumn('permission_group_id');
        });

        Schema::dropIfExists('permission_groups');
    }
};
