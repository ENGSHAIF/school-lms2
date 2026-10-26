<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. فك قيد المفتاح الأجنبي السابق على جدول levels (course_id) وجعله اختياري nullable
        // لأن المستويات أصبحت الحاوية الرئيسية والدورات تابعة لها
        if (Schema::hasColumn('levels', 'course_id')) {
            try {
                Schema::table('levels', function (Blueprint $table) {
                    $table->dropForeign(['course_id']);
                });
            } catch (\Exception $e) {
                // في حال كان اسم القيد مختلفاً
            }

            Schema::table('levels', function (Blueprint $table) {
                $table->unsignedBigInteger('course_id')->nullable()->change();
            });
        }

        // 2. تحديث جدول الدورات courses لتتبع المستويات مع الساعات والتسعيرة الفردية
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'level_id')) {
                $table->foreignId('level_id')->nullable()->after('id')->constrained('levels')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('courses', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('courses', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('courses', 'hours')) {
                $table->integer('hours')->default(40)->after('name_en'); // عدد الساعات المعتمدة للدورة
            }
            if (!Schema::hasColumn('courses', 'fee_amount')) {
                $table->decimal('fee_amount', 12, 2)->default(0.00)->after('hours'); // تسعيرة الدورة الفردية
            }
            if (!Schema::hasColumn('courses', 'currency')) {
                $table->string('currency', 10)->default('YER')->after('fee_amount'); // عملة الدورة
            }
            if (!Schema::hasColumn('courses', 'course_order')) {
                $table->integer('course_order')->default(1)->after('currency'); // ترتيب الدورة داخل المستوى
            }
        });

        // 3. تحديث جدول بنك الموارد والمناهج level_resources ليرتبط بالدورات مباشرة
        Schema::table('level_resources', function (Blueprint $table) {
            if (!Schema::hasColumn('level_resources', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->cascadeOnDelete();
            }
            $table->unsignedBigInteger('level_id')->nullable()->change();
        });

        // 4. حذف الموارد السابقة بناءً على طلب المستخدم الصريح:
        // "احذف الموارد الموجودة حاليا للمستويات"
        DB::table('level_resources')->delete();

        // 5. تحديث جدول الامتحانات exams ليقبل course_id اختياري
        if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'course_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('level_id')->constrained('courses')->nullOnDelete();
            });
        }

        // 6. تحديث جدول الدروس والحصص lessons ليقبل course_id اختياري
        if (Schema::hasTable('lessons') && !Schema::hasColumn('lessons', 'course_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('level_id')->constrained('courses')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lessons', 'course_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasColumn('exams', 'course_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasColumn('level_resources', 'course_id')) {
            Schema::table('level_resources', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasColumn('courses', 'level_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['level_id']);
                $table->dropColumn(['level_id', 'name_ar', 'name_en', 'hours', 'fee_amount', 'currency', 'course_order']);
            });
        }
    }
};
