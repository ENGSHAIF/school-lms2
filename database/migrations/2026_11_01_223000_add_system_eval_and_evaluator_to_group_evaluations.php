<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة هوية المقيم (معلم/مشرف/إدارة) لجدول جلسات وحصص المجموعات
        if (Schema::hasTable('group_sessions')) {
            Schema::table('group_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('group_sessions', 'evaluated_by')) {
                    $table->foreignId('evaluated_by')->nullable()->after('teacher_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('group_sessions', 'evaluator_name')) {
                    $table->string('evaluator_name')->nullable()->after('evaluated_by');
                }
                if (! Schema::hasColumn('group_sessions', 'evaluator_role')) {
                    $table->string('evaluator_role')->nullable()->after('evaluator_name'); // 'معلم', 'مشرف', 'مدير نظام'
                }
            });
        }

        // 2. إضافة تقييم النظام التلقائي، وتقييم المعلم المنفصل، وهوية المقيم لجدول تقييمات الطلاب
        if (Schema::hasTable('group_student_evaluations')) {
            Schema::table('group_student_evaluations', function (Blueprint $table) {
                if (! Schema::hasColumn('group_student_evaluations', 'evaluator_id')) {
                    $table->foreignId('evaluator_id')->nullable()->after('student_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('group_student_evaluations', 'evaluator_name')) {
                    $table->string('evaluator_name')->nullable()->after('evaluator_id');
                }
                if (! Schema::hasColumn('group_student_evaluations', 'evaluator_role')) {
                    $table->string('evaluator_role')->nullable()->after('evaluator_name');
                }

                // درجات تقييم النظام التلقائي (Automatic System Evaluation)
                if (! Schema::hasColumn('group_student_evaluations', 'system_attendance_score')) {
                    $table->decimal('system_attendance_score', 5, 2)->default(25.00)->after('homework_score'); // حضور/غياب من 25
                }
                if (! Schema::hasColumn('group_student_evaluations', 'system_voice_score')) {
                    $table->decimal('system_voice_score', 5, 2)->default(0.00)->after('system_attendance_score'); // تفاعل صوتي من 25
                }
                if (! Schema::hasColumn('group_student_evaluations', 'system_chat_score')) {
                    $table->decimal('system_chat_score', 5, 2)->default(0.00)->after('system_voice_score'); // مشاركة كتابية من 25
                }
                if (! Schema::hasColumn('group_student_evaluations', 'system_task_score')) {
                    $table->decimal('system_task_score', 5, 2)->default(0.00)->after('system_chat_score'); // تسليم أوراق وأنشطة من 25
                }
                if (! Schema::hasColumn('group_student_evaluations', 'system_total_score')) {
                    $table->decimal('system_total_score', 5, 2)->default(0.00)->after('system_task_score'); // مجموع تقييم النظام التلقائي من 100
                }

                // درجات تقييم المعلم المنفصل (Teacher Evaluation)
                if (! Schema::hasColumn('group_student_evaluations', 'teacher_total_score')) {
                    $table->decimal('teacher_total_score', 5, 2)->default(100.00)->after('system_total_score'); // مجموع تقييم المعلم المستقل من 100
                }

                if (! Schema::hasColumn('group_student_evaluations', 'system_notes')) {
                    $table->text('system_notes')->nullable()->after('teacher_notes'); // ملاحظات وتقرير النظام الآلي
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('group_student_evaluations')) {
            Schema::table('group_student_evaluations', function (Blueprint $table) {
                $table->dropColumn([
                    'evaluator_id',
                    'evaluator_name',
                    'evaluator_role',
                    'system_attendance_score',
                    'system_voice_score',
                    'system_chat_score',
                    'system_task_score',
                    'system_total_score',
                    'teacher_total_score',
                    'system_notes',
                ]);
            });
        }

        if (Schema::hasTable('group_sessions')) {
            Schema::table('group_sessions', function (Blueprint $table) {
                $table->dropColumn(['evaluated_by', 'evaluator_name', 'evaluator_role']);
            });
        }
    }
};
