<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة تسعيرة تدريس المعلم وأجر الساعة في جدول الدورات
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'teacher_fee')) {
                $table->decimal('teacher_fee', 12, 2)->default(0.00)->after('fee_amount');
            }
            if (!Schema::hasColumn('courses', 'teacher_hourly_rate')) {
                $table->decimal('teacher_hourly_rate', 10, 2)->default(0.00)->after('teacher_fee');
            }
        });

        // 2. جدول الخدمات والمهام التعليمية الإضافية وأسعارها (إعداد اختبارات، تصحيح، إشراف، أنشطة)
        if (!Schema::hasTable('educational_services')) {
            Schema::create('educational_services', function (Blueprint $table) {
                $table->id();
                $table->string('service_code')->unique();
                $table->string('name');
                $table->string('service_type')->default('other'); // exam_prep, grading, supervision, activity, tutoring, other
                $table->decimal('default_fee', 12, 2)->default(0.00);
                $table->string('currency', 10)->default('YER');
                $table->string('unit_type')->default('fixed'); // per_course, per_exam, per_hour, per_group, per_student, fixed
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 3. تحديث جدول عقود المعلمين وأنماط التوظيف (موظف براتب ثابت، متعاقد بالدورة، بالساعة، نسبة)
        Schema::table('teacher_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_contracts', 'contract_type')) {
                $table->string('contract_type')->default('per_course')->after('teacher_id');
            }
            if (!Schema::hasColumn('teacher_contracts', 'monthly_salary')) {
                $table->decimal('monthly_salary', 12, 2)->default(0.00)->after('rate');
            }
            if (!Schema::hasColumn('teacher_contracts', 'default_hourly_rate')) {
                $table->decimal('default_hourly_rate', 10, 2)->default(0.00)->after('monthly_salary');
            }
        });

        // 4. جدول إثبات مستحقات المعلمين (Teacher Entitlements / Accruals Posting)
        if (!Schema::hasTable('teacher_entitlements')) {
            Schema::create('teacher_entitlements', function (Blueprint $table) {
                $table->id();
                $table->string('entitlement_no')->unique(); // ENT-2026-00001
                $table->string('manual_no')->nullable(); // رقم المرجع الدفتري
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('study_group_id')->nullable()->constrained('study_groups')->nullOnDelete();
                $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
                $table->foreignId('educational_service_id')->nullable()->constrained('educational_services')->nullOnDelete();
                $table->string('entitlement_type'); // course_teaching, fixed_salary, exam_prep, grading, supervision, activity, custom_service, bonus, other
                $table->string('title');
                $table->decimal('amount', 14, 2);
                $table->string('currency', 10)->default('YER');
                $table->date('due_date');
                $table->string('period')->nullable(); // 2026-10 أو الترم الأول 2026
                $table->text('statement')->nullable();
                $table->enum('status', ['approved', 'partially_paid', 'paid', 'cancelled'])->default('approved');
                $table->decimal('paid_amount', 14, 2)->default(0.00);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_entitlements');
        Schema::dropIfExists('educational_services');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['teacher_fee', 'teacher_hourly_rate']);
        });
        Schema::table('teacher_contracts', function (Blueprint $table) {
            $table->dropColumn(['contract_type', 'monthly_salary', 'default_hourly_rate']);
        });
    }
};
