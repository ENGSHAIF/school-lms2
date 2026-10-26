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
        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('financial_accounts')->nullOnDelete();
            $table->string('box_nature', 20)->default('both')->after('account_type'); // both (قبض وصرف), receipt (قبض فقط), payment (صرف فقط), pos (نقطة بيع)
            $table->string('box_category', 30)->default('main_box')->after('box_nature'); // main_box, sub_box, general_revenue, pos, bank, electronic_wallet
            $table->foreignId('custodian_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete(); // أمين/مسؤول الصندوق
            $table->string('bank_name')->nullable()->after('currency'); // اسم البنك أو جهة المحفظة
            $table->string('account_number')->nullable()->after('bank_name'); // رقم الحساب البنكي أو رقم المحفظة
            $table->text('description')->nullable()->after('current_balance'); // وصف وملاحظات الحساب
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['custodian_id']);
            $table->dropColumn([
                'parent_id',
                'box_nature',
                'box_category',
                'custodian_id',
                'bank_name',
                'account_number',
                'description',
            ]);
        });
    }
};
