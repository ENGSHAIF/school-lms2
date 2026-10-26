<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('id', 3)->update([
            'name' => 'المعلم التجريبي'
        ]);
        DB::table('users')->where('id', 4)->update([
            'name' => 'الطالب التجريبي'
        ]);
    }

    public function down(): void
    {
        //
    }
};
