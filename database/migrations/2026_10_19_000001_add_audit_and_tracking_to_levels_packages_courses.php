<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة أعمدة التتبع (المنشئ والمعدل) لجدول المستويات والرسوم الدراسية
        if (Schema::hasTable('levels')) {
            Schema::table('levels', function (Blueprint $table) {
                if (!Schema::hasColumn('levels', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('levels', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }
            });
        }

        // 2. إضافة أعمدة التتبع لجدول الدورات
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('courses', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }
            });
        }

        // 3. إضافة أعمدة التتبع لجدول الباقات والتسعيرة
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (!Schema::hasColumn('packages', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('is_active')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('packages', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }
            });
        }

        // 4. إنشاء جدول سجل تدقيق العمليات والأنشطة الإدارية والمالية (System Audit Logs)
        if (!Schema::hasTable('system_audit_logs')) {
            Schema::create('system_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 60)->index(); // رمز العملية (pricing_update, level_create, voucher_create, etc.)
                $table->string('model_type', 100)->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('description'); // نص تفصيلي للعملية بالعربية
                $table->json('details')->nullable(); // بيانات التعديل والأسعار السابقة والجديدة
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index(['user_id', 'action']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_audit_logs');

        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (Schema::hasColumn('packages', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
                if (Schema::hasColumn('packages', 'created_by')) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                }
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
                if (Schema::hasColumn('courses', 'created_by')) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                }
            });
        }

        if (Schema::hasTable('levels')) {
            Schema::table('levels', function (Blueprint $table) {
                if (Schema::hasColumn('levels', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn('updated_by');
                }
                if (Schema::hasColumn('levels', 'created_by')) {
                    $table->dropForeign(['created_by']);
                    $table->dropColumn('created_by');
                }
            });
        }
    }
};
