<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use App\Models\CostCenter;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * تشغيل بذور الدليل المحاسبي الشجري القياسي (5 رتب صارمة)
     * - الرتب (1، 2، 3، 4) حسابات رئيسية تجميعية (is_parent = true)
     * - الرتبة (5) حسابات فرعية حركية تقبل القيود والسندات (is_parent = false)
     */
    public function run(): void
    {
        DB::transaction(function () {
            // دالة مساعدة
            $syncAcc = function ($code, $name, $type, $category, $nature, $level, $isParent, $parentId = null, $allowCC = false) {
                $acc = FinancialAccount::where('account_code', $code)->first();
                if (!$acc) {
                    $acc = new FinancialAccount();
                    $acc->account_code = $code;
                }
                $acc->account_name = $name;
                $acc->account_type = $type;
                $acc->account_category = $category;
                $acc->nature = $nature;
                $acc->level = $level;
                $acc->is_parent = $isParent;
                $acc->parent_id = $parentId;
                $acc->allow_cost_center = $allowCC;
                $acc->is_active = true;
                $acc->save();
                return $acc;
            };

            // --- الرتبة 1: الحسابات الرئيسية الكبرى ---
            $r1 = $syncAcc('1', 'الأصول (Assets)', 'revenue', 'asset', 'debit', 1, true);
            $r2 = $syncAcc('2', 'الخصوم والالتزامات (Liabilities)', 'expense', 'liability', 'credit', 1, true);
            $r3 = $syncAcc('3', 'حقوق الملكية (Equity)', 'revenue', 'equity', 'credit', 1, true);
            $r4 = $syncAcc('4', 'الإيرادات (Revenues)', 'revenue', 'revenue', 'credit', 1, true);
            $r5 = $syncAcc('5', 'المصروفات (Expenses)', 'expense', 'expense', 'debit', 1, true);

            // --- الرتبة 2: المجموعات العامة ---
            $r11 = $syncAcc('11', 'الأصول المتداولة', 'cash_box', 'asset', 'debit', 2, true, $r1->id);
            $r12 = $syncAcc('12', 'الأصول الثابتة غير المتداولة', 'cash_box', 'asset', 'debit', 2, true, $r1->id);
            $r21 = $syncAcc('21', 'الخصوم المتداولة (قصيرة الأجل)', 'teacher', 'liability', 'credit', 2, true, $r2->id);
            $r22 = $syncAcc('22', 'الخصوم غير المتداولة (طويلة الأجل)', 'teacher', 'liability', 'credit', 2, true, $r2->id);
            $r31 = $syncAcc('31', 'رأس المال والاحتياطيات', 'revenue', 'equity', 'credit', 2, true, $r3->id);
            $r32 = $syncAcc('32', 'الأرباح (الخسائر) المدورة', 'revenue', 'equity', 'credit', 2, true, $r3->id);
            $r41 = $syncAcc('41', 'إيرادات النشاط التعليمي والتدريبي', 'revenue', 'revenue', 'credit', 2, true, $r4->id);
            $r42 = $syncAcc('42', 'إيرادات خدمات الترجمة والاستشارات', 'revenue', 'revenue', 'credit', 2, true, $r4->id);
            $r49 = $syncAcc('49', 'إيرادات أخرى متنوعة', 'revenue', 'revenue', 'credit', 2, true, $r4->id);
            $r51 = $syncAcc('51', 'التكاليف التدريبية المباشرة', 'expense', 'expense', 'debit', 2, true, $r5->id);
            $r52 = $syncAcc('52', 'المصروفات الإدارية والعمومية', 'expense', 'expense', 'debit', 2, true, $r5->id);
            $r53 = $syncAcc('53', 'المصروفات التسويقية والترويجية', 'expense', 'expense', 'debit', 2, true, $r5->id);

            // --- الرتبة 3: الحسابات الوسيطة ---
            $r111 = $syncAcc('111', 'النقدية والبنوك وما في حكمها', 'cash_box', 'asset', 'debit', 3, true, $r11->id);
            $r112 = $syncAcc('112', 'المدينون والعملاء (الذمم المدينة)', 'student', 'asset', 'debit', 3, true, $r11->id);
            $r113 = $syncAcc('113', 'الأرصدة المدينة الأخرى والأمانات', 'cash_box', 'asset', 'debit', 3, true, $r11->id);
            $r121 = $syncAcc('121', 'الأصول الثابتة الملموسة', 'cash_box', 'asset', 'debit', 3, true, $r12->id);

            $r211 = $syncAcc('211', 'الدائنون والتجاريون', 'teacher', 'liability', 'credit', 3, true, $r21->id);
            $r212 = $syncAcc('212', 'الأرصدة الدائنة ومستحقات العاملين', 'teacher', 'liability', 'credit', 3, true, $r21->id);

            $r311 = $syncAcc('311', 'رأس المال الأساسي', 'revenue', 'equity', 'credit', 3, true, $r31->id);
            $r321 = $syncAcc('321', 'الأرباح المدورة والفائض', 'revenue', 'equity', 'credit', 3, true, $r32->id);

            $r411 = $syncAcc('411', 'إيرادات البرامج والدورات التدريبية', 'revenue', 'revenue', 'credit', 3, true, $r41->id);
            $r412 = $syncAcc('412', 'إيرادات الاختبارات والشهائد الأكاديمية', 'revenue', 'revenue', 'credit', 3, true, $r41->id);
            $r421 = $syncAcc('421', 'إيرادات خدمات الترجمة المعتمدة', 'revenue', 'revenue', 'credit', 3, true, $r42->id);
            $r422 = $syncAcc('422', 'إيرادات الاستشارات وتطوير الأعمال', 'revenue', 'revenue', 'credit', 3, true, $r42->id);
            $r491 = $syncAcc('491', 'إيرادات متنوعة وأرباح فروق عملة', 'revenue', 'revenue', 'credit', 3, true, $r49->id);

            $r511 = $syncAcc('511', 'أجور وتكاليف التدريس والتدريب المباشرة', 'expense', 'expense', 'debit', 3, true, $r51->id);
            $r512 = $syncAcc('512', 'تكاليف المناهج والكتب والمواد التدريبية', 'expense', 'expense', 'debit', 3, true, $r51->id);
            $r521 = $syncAcc('521', 'مصروفات المقرات والمرافق الأساسية', 'expense', 'expense', 'debit', 3, true, $r52->id);
            $r522 = $syncAcc('522', 'مصروفات التشغيل والخدمات (كهرباء، ماء، اتصالات)', 'expense', 'expense', 'debit', 3, true, $r52->id);
            $r523 = $syncAcc('523', 'المصروفات الإدارية المتنوعة والقرطاسية', 'expense', 'expense', 'debit', 3, true, $r52->id);
            $r531 = $syncAcc('531', 'مصروفات الدعاية والإعلان والتسويق', 'expense', 'expense', 'debit', 3, true, $r53->id);

            // --- الرتبة 4: الحسابات التجميعية الرقابية ---
            $r1111 = $syncAcc('1111', 'الصناديق النقدية والخزن', 'cash_box', 'asset', 'debit', 4, true, $r111->id);
            $r1112 = $syncAcc('1112', 'الحسابات البنكية والمحافظ الإلكترونية', 'bank', 'asset', 'debit', 4, true, $r111->id);
            $r1121 = $syncAcc('1121', 'ذمم رسوم الطلاب والدارسين', 'student', 'asset', 'debit', 4, true, $r112->id);
            $r1122 = $syncAcc('1122', 'ذمم الشركات والجهات المتعاقدة', 'student', 'asset', 'debit', 4, true, $r112->id);
            $r1131 = $syncAcc('1131', 'العُهد المالية المؤقتة لموظفي الإدارة', 'cash_box', 'asset', 'debit', 4, true, $r113->id);
            $r1211 = $syncAcc('1211', 'أجهزة الكمبيوتر والسيرفرات والمعدات التقنية', 'cash_box', 'asset', 'debit', 4, true, $r121->id);
            $r1212 = $syncAcc('1212', 'الأثاث والتجهيزات المكتبية والقاعات', 'cash_box', 'asset', 'debit', 4, true, $r121->id);

            $r2111 = $syncAcc('2111', 'موردو الكتب والمستلزمات الخارجية', 'teacher', 'liability', 'credit', 4, true, $r211->id);
            $r2121 = $syncAcc('2121', 'مستحقات وأجور المعلمين والمدربين', 'teacher', 'liability', 'credit', 4, true, $r212->id);
            $r2122 = $syncAcc('2122', 'أمانات ورسوم دورات مقبوضة مقدماً', 'teacher', 'liability', 'credit', 4, true, $r212->id);

            $r3111 = $syncAcc('3111', 'رأس المال التأسيسي وحصص الشركاء', 'revenue', 'equity', 'credit', 4, true, $r311->id);
            $r3211 = $syncAcc('3211', 'أرباح (خسائر) مرحلة من سنوات سابقة', 'revenue', 'equity', 'credit', 4, true, $r321->id);

            $r4111 = $syncAcc('4111', 'إيرادات دورات اللغة الإنجليزية', 'revenue', 'revenue', 'credit', 4, true, $r411->id);
            $r4112 = $syncAcc('4112', 'إيرادات دورات الحاسوب والمهارات', 'revenue', 'revenue', 'credit', 4, true, $r411->id);
            $r4121 = $syncAcc('4121', 'إيرادات اختبارات تحديد المستوى والشهائد', 'revenue', 'revenue', 'credit', 4, true, $r412->id);
            $r4211 = $syncAcc('4211', 'إيرادات ترجمة الوثائق المعتمدة', 'revenue', 'revenue', 'credit', 4, true, $r421->id);
            $r4221 = $syncAcc('4221', 'إيرادات الاستشارات وتطوير الأعمال', 'revenue', 'revenue', 'credit', 4, true, $r422->id);
            $r4911 = $syncAcc('4911', 'أرباح فروق صرف العملات الأجنبية', 'revenue', 'revenue', 'credit', 4, true, $r491->id);

            $r5111 = $syncAcc('5111', 'أتعاب ورواتب المعلمين والمدربين المباشرة', 'expense', 'expense', 'debit', 4, true, $r511->id);
            $r5121 = $syncAcc('5121', 'تكاليف طباعة وشراء المناهج والكتب التدريبية', 'expense', 'expense', 'debit', 4, true, $r512->id);
            $r5211 = $syncAcc('5211', 'إيجار المقرات والقاعات التدريبية', 'expense', 'expense', 'debit', 4, true, $r521->id);
            $r5221 = $syncAcc('5221', 'فواتير الكهرباء والمياه والطاقة', 'expense', 'expense', 'debit', 4, true, $r522->id);
            $r5222 = $syncAcc('5222', 'اشتراكات الإنترنت والاتصالات والهاتف', 'expense', 'expense', 'debit', 4, true, $r522->id);
            $r5231 = $syncAcc('5231', 'القرطاسية والمطبوعات والأدوات المكتبية', 'expense', 'expense', 'debit', 4, true, $r523->id);
            $r5232 = $syncAcc('5232', 'الضيافة والاستقبال ومستلزمات النظافة', 'expense', 'expense', 'debit', 4, true, $r523->id);
            $r5233 = $syncAcc('5233', 'صيانة المبنى والآلات والتجهيزات', 'expense', 'expense', 'debit', 4, true, $r523->id);
            $r5311 = $syncAcc('5311', 'الدعاية والتسويق والإعلانات الممولة', 'expense', 'expense', 'debit', 4, true, $r531->id);

            // --- الرتبة 5: الحسابات الفرعية الحركية (is_parent = false) ---
            FinancialAccount::whereIn('account_code', ['1101', '1101-01', '1101-02'])->update([
                'parent_id' => $r1111->id,
                'level' => 5,
                'is_parent' => false,
            ]);

            FinancialAccount::where('account_code', '1102')->update([
                'parent_id' => $r1112->id,
                'level' => 5,
                'is_parent' => false,
            ]);

            FinancialAccount::where('account_type', 'student')
                ->whereNotIn('account_code', ['112', '1121', '1122'])
                ->update([
                    'parent_id' => $r1121->id,
                    'level' => 5,
                    'is_parent' => false,
                ]);

            FinancialAccount::where('account_type', 'teacher')
                ->whereNotIn('account_code', ['21', '211', '212', '2111', '2121', '2122'])
                ->update([
                    'parent_id' => $r2121->id,
                    'level' => 5,
                    'is_parent' => false,
                ]);

            FinancialAccount::whereIn('level', [1, 2, 3, 4])->update(['is_parent' => true]);
            FinancialAccount::where('level', 5)->update(['is_parent' => false]);
        });
    }
}
