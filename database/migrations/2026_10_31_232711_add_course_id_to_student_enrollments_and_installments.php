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
        if (Schema::hasTable('student_enrollments') && ! Schema::hasColumn('student_enrollments', 'course_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('level_id')->constrained('courses')->nullOnDelete();
            });
        }

        if (Schema::hasTable('student_installments') && ! Schema::hasColumn('student_installments', 'course_id')) {
            Schema::table('student_installments', function (Blueprint $table) {
                $table->foreignId('course_id')->nullable()->after('level_id')->constrained('courses')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_enrollments') && Schema::hasColumn('student_enrollments', 'course_id')) {
            Schema::table('student_enrollments', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }

        if (Schema::hasTable('student_installments') && Schema::hasColumn('student_installments', 'course_id')) {
            Schema::table('student_installments', function (Blueprint $table) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            });
        }
    }
};
