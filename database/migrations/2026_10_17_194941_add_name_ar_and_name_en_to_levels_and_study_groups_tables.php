<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('course_id');
            $table->string('name_en')->nullable()->after('name_ar');
        });

        Schema::table('study_groups', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('id');
            $table->string('name_en')->nullable()->after('name_ar');
        });

        // ترحيل الأسماء الحالية وفصل العربي عن الإنجليزي تلقائياً للمستويات
        $levels = DB::table('levels')->get();
        foreach ($levels as $lvl) {
            $raw = $lvl->name;
            $ar = $raw;
            $en = null;

            if (preg_match('/^(.*?)\s*\((.*?)\)$/u', $raw, $matches)) {
                $ar = trim($matches[1]);
                $en = trim($matches[2]);
            }

            DB::table('levels')->where('id', $lvl->id)->update([
                'name_ar' => $ar,
                'name_en' => $en,
            ]);
        }

        // ترحيل الأسماء الحالية للمجموعات
        $groups = DB::table('study_groups')->get();
        foreach ($groups as $grp) {
            DB::table('study_groups')->where('id', $grp->id)->update([
                'name_ar' => $grp->name,
                'name_en' => null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en']);
        });

        Schema::table('study_groups', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en']);
        });
    }
};
