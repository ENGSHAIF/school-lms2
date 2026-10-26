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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('phone_whatsapp');
            $table->string('gender', 10)->default('male')->after('birth_date');
            $table->string('user_code', 50)->nullable()->unique()->after('student_code');
            $table->boolean('must_change_password')->default(false)->after('password');
        });

        // ترقية وتوليد أكواد تسلسلية فريدة لجميع المستخدمين الموجودين حالياً
        $users = DB::table('users')->get();
        foreach ($users as $u) {
            $code = $u->student_code;
            if (empty($code)) {
                $prefix = match ($u->role) {
                    'admin' => 'ADM',
                    'teacher' => 'TEA',
                    'supervisor' => 'SUP',
                    'accountant' => 'ACC',
                    'parent' => 'PAR',
                    default => 'STU',
                };
                $code = $prefix . '-' . date('Y') . '-' . str_pad($u->id, 4, '0', STR_PAD_LEFT);
            }

            DB::table('users')->where('id', $u->id)->update([
                'user_code' => $code,
                'student_code' => $code,
                'gender' => $u->gender ?? 'male',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name_en',
                'birth_date',
                'gender',
                'user_code',
                'must_change_password',
            ]);
        });
    }
};
