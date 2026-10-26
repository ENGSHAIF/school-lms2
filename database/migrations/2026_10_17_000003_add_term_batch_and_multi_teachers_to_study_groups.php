<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة حقول الترم والدفعة والدبلوم والمدة لجدول المجموعات الدراسية
        Schema::table('study_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('study_groups', 'term')) {
                $table->string('term')->nullable()->after('level_id')->comment('الترم الدراسي (مثال: الترم الأول 2026)');
            }
            if (!Schema::hasColumn('study_groups', 'batch')) {
                $table->string('batch')->nullable()->after('term')->comment('الدفعة (مثال: الدفعة الأولى A)');
            }
            if (!Schema::hasColumn('study_groups', 'program_type')) {
                $table->string('program_type')->nullable()->after('batch')->comment('نوع البرنامج أو الدبلوم (مثال: دبلوم اللغة الإنجليزية العام)');
            }
            if (!Schema::hasColumn('study_groups', 'duration_months')) {
                $table->integer('duration_months')->nullable()->default(3)->after('program_type')->comment('مدة الدبلوم أو الدورة بالأشهر');
            }
            if (!Schema::hasColumn('study_groups', 'start_date')) {
                $table->date('start_date')->nullable()->after('duration_months')->comment('تاريخ بدء المجموعة');
            }
            if (!Schema::hasColumn('study_groups', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date')->comment('تاريخ نهاية المجموعة أو التخرج');
            }
        });

        // 2. إنشاء جدول المعلمين المتعددين للمجموعة الدراسية (المعلم الرئيسي والمعلمون المساعدون)
        if (!Schema::hasTable('study_group_teachers')) {
            Schema::create('study_group_teachers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('study_group_id')->constrained('study_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->boolean('is_primary')->default(false)->comment('هل هو المعلم الرئيسي');
                $table->string('role_title')->nullable()->comment('المسمى التدريبي: معلم رئيسي، معلم محادثة، معلم استماع، معلم مساعد');
                $table->timestamps();

                $table->unique(['study_group_id', 'user_id']);
            });
        }

        // 3. إنشاء جدول المشرفين المتعددين للمجموعة الدراسية
        if (!Schema::hasTable('study_group_supervisors')) {
            Schema::create('study_group_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('study_group_id')->constrained('study_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->boolean('is_primary')->default(false)->comment('هل هو المشرف الرئيسي');
                $table->string('notes')->nullable()->comment('ملاحظات الإشراف');
                $table->timestamps();

                $table->unique(['study_group_id', 'user_id']);
            });
        }

        // 4. إضافة حقول الخصم لجدول الأقساط إذا لم تكن موجودة
        if (Schema::hasTable('student_installments')) {
            Schema::table('student_installments', function (Blueprint $table) {
                if (!Schema::hasColumn('student_installments', 'discount_amount')) {
                    $table->decimal('discount_amount', 12, 2)->default(0.00)->after('amount')->comment('قيمة الخصم الممنوح للطالب');
                }
                if (!Schema::hasColumn('student_installments', 'discount_reason')) {
                    $table->string('discount_reason')->nullable()->after('discount_amount')->comment('سبب أو مبرر الخصم (منحة، تفوق، قرابة)');
                }
            });
        }

        // 5. مزامنة وتحديث بيانات المجموعة الحالية (المجموعة 1) وتغذية الجداول الوسيطة
        $firstGroup = DB::table('study_groups')->find(1);
        if ($firstGroup) {
            DB::table('study_groups')->where('id', 1)->update([
                'term' => 'الترم الأول 2026',
                'batch' => 'الدفعة الأولى A',
                'program_type' => 'دبلوم اللغة الإنجليزية العام',
                'duration_months' => 4,
                'start_date' => '2026-10-01',
                'end_date' => '2027-01-31',
            ]);

            if ($firstGroup->teacher_id) {
                DB::table('study_group_teachers')->updateOrInsert(
                    ['study_group_id' => 1, 'user_id' => $firstGroup->teacher_id],
                    ['is_primary' => true, 'role_title' => 'معلم رئيسي', 'created_at' => now(), 'updated_at' => now()]
                );
            }

            if ($firstGroup->supervisor_id) {
                DB::table('study_group_supervisors')->updateOrInsert(
                    ['study_group_id' => 1, 'user_id' => $firstGroup->supervisor_id],
                    ['is_primary' => true, 'notes' => 'المشرف الأكاديمي الرئيسي للمجموعة', 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('study_group_supervisors');
        Schema::dropIfExists('study_group_teachers');

        if (Schema::hasTable('study_groups')) {
            Schema::table('study_groups', function (Blueprint $table) {
                $table->dropColumn(['term', 'batch', 'program_type', 'duration_months', 'start_date', 'end_date']);
            });
        }

        if (Schema::hasTable('student_installments')) {
            Schema::table('student_installments', function (Blueprint $table) {
                $table->dropColumn(['discount_amount', 'discount_reason']);
            });
        }
    }
};
