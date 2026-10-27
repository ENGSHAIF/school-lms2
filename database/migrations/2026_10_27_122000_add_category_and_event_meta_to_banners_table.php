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
        Schema::table('banners', function (Blueprint $table) {
            if (! Schema::hasColumn('banners', 'category')) {
                $table->string('category', 50)->default('ad')->after('placement')->index();
            }
            if (! Schema::hasColumn('banners', 'event_date')) {
                $table->date('event_date')->nullable()->after('category');
            }
            if (! Schema::hasColumn('banners', 'event_location')) {
                $table->string('event_location')->nullable()->after('event_date');
            }
            if (! Schema::hasColumn('banners', 'batch_name')) {
                $table->string('batch_name', 100)->nullable()->after('event_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('banners', 'category')) {
                $cols[] = 'category';
            }
            if (Schema::hasColumn('banners', 'event_date')) {
                $cols[] = 'event_date';
            }
            if (Schema::hasColumn('banners', 'event_location')) {
                $cols[] = 'event_location';
            }
            if (Schema::hasColumn('banners', 'batch_name')) {
                $cols[] = 'batch_name';
            }

            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
