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
        if (Schema::hasTable('levels') && ! Schema::hasColumn('levels', 'original_fee')) {
            Schema::table('levels', function (Blueprint $table) {
                $table->decimal('original_fee', 10, 2)->nullable()->default(500.00)->after('fee_amount');
            });
        }

        if (Schema::hasTable('courses') && ! Schema::hasColumn('courses', 'original_fee')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->decimal('original_fee', 10, 2)->nullable()->default(500.00)->after('fee_amount');
            });
        }

        // تحديث رسوم كافة المستويات الأكاديمية إلى 150 ريال سعودي وسعر قبل التخفيض 500 ريال سعودي
        DB::table('levels')->update([
            'fee_amount' => 150.00,
            'currency' => 'SAR',
            'original_fee' => 500.00,
        ]);

        // تحديث رسوم كافة الدورات التدريبية إلى 150 ريال سعودي وسعر قبل التخفيض 500 ريال سعودي
        DB::table('courses')->update([
            'fee_amount' => 150.00,
            'currency' => 'SAR',
            'original_fee' => 500.00,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('levels') && Schema::hasColumn('levels', 'original_fee')) {
            Schema::table('levels', function (Blueprint $table) {
                $table->dropColumn('original_fee');
            });
        }

        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'original_fee')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('original_fee');
            });
        }
    }
};
