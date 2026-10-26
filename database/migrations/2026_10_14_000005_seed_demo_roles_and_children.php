<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $password = Hash::make('password123');

        // 1. حساب ولي الأمر التجريبي
        $parentId = DB::table('users')->insertGetId([
            'name' => 'ولي الأمر التجريبي',
            'email' => 'parent@school.com',
            'phone_whatsapp' => '+967770000005',
            'role' => 'parent',
            'status' => 'active',
            'student_code' => 'PAR-2026-0001',
            'password' => $password,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. حساب المشرف الأكاديمي التجريبي
        DB::table('users')->insert([
            'name' => 'المشرف الأكاديمي التجريبي',
            'email' => 'supervisor@school.com',
            'phone_whatsapp' => '+967770000006',
            'role' => 'supervisor',
            'status' => 'active',
            'student_code' => 'SUP-2026-0001',
            'password' => $password,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. حساب الطفل التجريبي 1 (ابتدائي)
        $child1Id = DB::table('users')->insertGetId([
            'name' => 'الطفل التجريبي 1',
            'email' => 'child1@school.com',
            'phone_whatsapp' => '+967770000005',
            'role' => 'student',
            'status' => 'active',
            'student_code' => 'STU-2026-0101',
            'password' => $password,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_profiles')->insert([
            'user_id' => $child1Id,
            'parent_id' => $parentId,
            'stage' => 'primary',
            'grade_substage' => 'الصف الثالث الابتدائي',
            'learning_goals' => json_encode(['الطلاقة والمحادثة 1-on-1', 'نطق الحروف والأصوات الصحيحة']),
            'preferred_time' => 'مسائي (5:00 م - 9:00 م)',
            'preferred_frequency' => '3 حصص أسبوعياً',
            'parent_consent' => true,
            'is_trial_booked' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_wallets')->insert([
            'user_id' => $child1Id,
            'total_lessons' => 12,
            'used_lessons' => 3,
            'remaining_lessons' => 9,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('financial_accounts')->insert([
            'account_code' => '210101',
            'account_name' => 'حساب الطالب: الطفل التجريبي 1',
            'account_type' => 'student',
            'user_id' => $child1Id,
            'currency' => 'USD',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. حساب الطفل التجريبي 2 (أساسي / متوسط)
        $child2Id = DB::table('users')->insertGetId([
            'name' => 'الطفل التجريبي 2',
            'email' => 'child2@school.com',
            'phone_whatsapp' => '+967770000005',
            'role' => 'student',
            'status' => 'pending_approval',
            'student_code' => 'STU-2026-0102',
            'password' => $password,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_profiles')->insert([
            'user_id' => $child2Id,
            'parent_id' => $parentId,
            'stage' => 'middle_school',
            'grade_substage' => 'الصف الثاني المتوسط',
            'learning_goals' => json_encode(['تقوية القراءة وفهم النصوص', 'الاستعداد لاختبار التوفل']),
            'preferred_time' => 'مسائي (5:00 م - 9:00 م)',
            'preferred_frequency' => 'حصتان أسبوعياً',
            'parent_consent' => true,
            'is_trial_booked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('student_wallets')->insert([
            'user_id' => $child2Id,
            'total_lessons' => 1,
            'used_lessons' => 0,
            'remaining_lessons' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('financial_accounts')->insert([
            'account_code' => '210102',
            'account_name' => 'حساب الطالب: الطفل التجريبي 2',
            'account_type' => 'student',
            'user_id' => $child2Id,
            'currency' => 'USD',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
