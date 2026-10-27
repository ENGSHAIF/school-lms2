<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة حقول تتبع الاعتماد وإلغاء الاعتماد لجدول المستخدمين (users)
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (! Schema::hasColumn('users', 'revoked_by')) {
                $table->foreignId('revoked_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('revoked_by');
            }
            if (! Schema::hasColumn('users', 'revoke_reason')) {
                $table->text('revoke_reason')->nullable()->after('revoked_at');
            }
        });

        // 2. تحديث وتوسيع جدول تسجيل المستويات (student_enrollments)
        Schema::table('student_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('student_enrollments', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('student_enrollments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (! Schema::hasColumn('student_enrollments', 'revoked_by')) {
                $table->foreignId('revoked_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('student_enrollments', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('revoked_by');
            }
            if (! Schema::hasColumn('student_enrollments', 'revoke_reason')) {
                $table->text('revoke_reason')->nullable()->after('revoked_at');
            }
        });

        // تعديل عمود status في student_enrollments لدعم 'revoked'
        try {
            DB::statement("ALTER TABLE student_enrollments MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'active'");
        } catch (Throwable $e) {
            // تجاهل إن لم يكن مدعوماً
        }

        // تحديث البيانات القديمة للطلاب المقبولين الحاليين لربط تاريخ اعتمادهم المبدئي
        try {
            DB::statement("
                UPDATE users u
                INNER JOIN student_enrollments se ON se.user_id = u.id AND se.status = 'active'
                SET u.approved_at = COALESCE(se.enrolled_at, u.created_at),
                    u.approved_by = 1,
                    se.approved_at = COALESCE(se.enrolled_at, u.created_at),
                    se.approved_by = 1
                WHERE u.role = 'student' AND u.status = 'active' AND u.approved_at IS NULL
            ");
        } catch (Throwable $e) {
            // تجاهل
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['revoked_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'revoked_by', 'revoked_at', 'revoke_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['revoked_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'revoked_by', 'revoked_at', 'revoke_reason']);
        });
    }
};
