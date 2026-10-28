<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use App\Models\AccountingPeriod;
use App\Models\CostCenterGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YemenSoftAccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء الفترات المحاسبية الـ 12 لعام 2026
        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create(2026, $m, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create(2026, $m, 1)->endOfMonth()->format('Y-m-d');
            $monthName = $arabicMonths[$m].' 2026';

            AccountingPeriod::updateOrCreate(
                [
                    'fiscal_year' => 2026,
                    'period_number' => $m,
                ],
                [
                    'name' => $monthName,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'open',
                    'notes' => "فترة شهر {$arabicMonths[$m]} المالية للعام 2026",
                ]
            );
        }

        // 2. مجموعات الحسابات (Account Groups)
        $accountGroups = [
            ['code' => 'CASH_BANKS', 'name' => 'مجموعة الصناديق النقدية والحسابات البنكية', 'description' => 'حسابات الصناديق بالعملات المختلفة والبنوك التجارية'],
            ['code' => 'STUDENTS', 'name' => 'مجموعة ذمم الطلاب والعملاء المدينين', 'description' => 'المدينون من رسوم الدورات والتأهيل'],
            ['code' => 'TEACHERS', 'name' => 'مجموعة مستحقات المدربين والموردين الدائنين', 'description' => 'المستحقات الشهرية وعقود التدريس'],
            ['code' => 'REVENUES', 'name' => 'مجموعة إيرادات الدورات والخدمات التعليمية', 'description' => 'إيرادات دبلومات اللغات والشهادات المعتمدة'],
            ['code' => 'EXPENSES', 'name' => 'مجموعة المصروفات الإدارية والتشغيلية', 'description' => 'الإيجارات والرواتب والاتصالات والإنترنت'],
        ];

        foreach ($accountGroups as $grp) {
            AccountGroup::updateOrCreate(
                ['code' => $grp['code']],
                ['name' => $grp['name'], 'description' => $grp['description'], 'is_active' => true]
            );
        }

        // 3. مجموعات مراكز التكلفة (Cost Center Groups)
        $costCenterGroups = [
            ['code' => 'CC_COURSES', 'name' => 'مجموعة دورات اللغات والتأهيل الأكاديمي', 'description' => 'مراكز تكلفة الدورات التدريبية المباشرة والأونلاين'],
            ['code' => 'CC_ADMIN', 'name' => 'مجموعة الإدارة العامة والخدمات المشتركة', 'description' => 'تكاليف المقر الرئيسي والإدارة والتسويق'],
        ];

        foreach ($costCenterGroups as $ccg) {
            CostCenterGroup::updateOrCreate(
                ['code' => $ccg['code']],
                ['name' => $ccg['name'], 'description' => $ccg['description'], 'is_active' => true]
            );
        }

        // 4. خيارات نظام المتكامل بلاس (Accounting Options)
        $settings = [
            'accounting.prevent_overdraft' => '1', // منع الصرف في حال عدم كفاية رصيد الصندوق
            'accounting.allow_suspended_entries' => '1', // السماح بالسندات والقيود المعلقة
            'accounting.strict_period_lock' => '1', // منع العمليات في الفترات المقفلة أو الموقوفة
            'accounting.allow_daily_portfolios' => '1', // تفعيل حوافظ الصرف والقبض اليومية
            'accounting.f9_statement_history' => '1', // تفعيل استعلام كشف الحساب السريع F9
        ];

        foreach ($settings as $key => $val) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $val,
                    'group' => 'accounting',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
