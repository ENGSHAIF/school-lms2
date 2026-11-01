<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_sessions', function (Blueprint $table) {
            $table->text('lesson_objectives')->nullable()->after('lesson_topic');
            $table->text('curriculum_objectives')->nullable()->after('lesson_objectives');
        });
    }

    public function down(): void
    {
        Schema::table('group_sessions', function (Blueprint $table) {
            $table->dropColumn(['lesson_objectives', 'curriculum_objectives']);
        });
    }
};
