<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة عمود الوصف للمستويات الأكاديمية (إن لم يكن موجوداً)
        if (Schema::hasTable('levels') && !Schema::hasColumn('levels', 'description')) {
            Schema::table('levels', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }

        // 2. جدول بنك المناهج والموارد التعليمية المركزي لكل مستوى (Level Resources)
        // (كتب رقمية PDF، تسجيلات صوتية MP3، فيديوهات ومحاضرات، أوراق عمل وأنشطة، كويزات واختبارات)
        Schema::create('level_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['book', 'audio', 'video', 'worksheet', 'quiz'])->default('book');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('external_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->integer('order_index')->default(1);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. ربط أنشطة المجموعات بمورد المستوى عند المشاركة
        if (Schema::hasTable('group_activities') && !Schema::hasColumn('group_activities', 'level_resource_id')) {
            Schema::table('group_activities', function (Blueprint $table) {
                $table->foreignId('level_resource_id')->nullable()->after('study_group_id')->constrained('level_resources')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('group_activities') && Schema::hasColumn('group_activities', 'level_resource_id')) {
            Schema::table('group_activities', function (Blueprint $table) {
                $table->dropForeign(['level_resource_id']);
                $table->dropColumn('level_resource_id');
            });
        }

        Schema::dropIfExists('level_resources');

        if (Schema::hasTable('levels') && Schema::hasColumn('levels', 'description')) {
            Schema::table('levels', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
