<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_sessions', function (Blueprint $table) {
            $table->string('lesson_summary_file')->nullable()->after('lesson_summary');
            $table->string('lesson_summary_file_name')->nullable()->after('lesson_summary_file');
            $table->string('homework_file')->nullable()->after('homework_assigned');
            $table->string('homework_file_name')->nullable()->after('homework_file');
        });
    }

    public function down(): void
    {
        Schema::table('group_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'lesson_summary_file',
                'lesson_summary_file_name',
                'homework_file',
                'homework_file_name',
            ]);
        });
    }
};
