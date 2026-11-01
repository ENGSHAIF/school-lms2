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
        // 1. جدول تهيئة العملات (مطابق لنظام المتكامل بلاس ERP)
        if (! Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('currency_code', 10)->unique(); // YER, SAR, USD, AED, etc.
                $table->string('name'); // اسم العملة بدون ال التعريف (ريال يمني، ريال سعودي)
                $table->string('name_en')->nullable(); // Yemeni Rial, Saudi Riyal
                $table->string('symbol', 10)->default('ر.ي'); // ر.ي, ر.س, $
                $table->string('fraction_name', 50)->default('فلس'); // فلس, هللة, سنت
                $table->boolean('is_local')->default(false); // عملة محلية رئيسية للمنشأة (واحدة فقط)
                $table->decimal('exchange_rate', 14, 4)->default(1.0000); // سعر التحويل مقارنة بالعملة المحلية
                $table->decimal('max_exchange_rate', 14, 4)->nullable(); // أعلى سعر تحويل
                $table->decimal('min_exchange_rate', 14, 4)->nullable(); // أدنى سعر تحويل
                $table->decimal('valuation_rate', 14, 4)->nullable(); // سعر التقييم (متوسط أسعار الصرف)
                $table->decimal('buy_rate', 14, 4)->nullable(); // سعر الشراء
                $table->decimal('max_buy_rate', 14, 4)->nullable(); // السعر الأعلى للشراء
                $table->decimal('sell_rate', 14, 4)->nullable(); // سعر البيع
                $table->decimal('min_sell_rate', 14, 4)->nullable(); // سعر البيع الأدنى
                $table->boolean('is_default_transfer')->default(false); // عملة افتراضية للحوالات
                $table->boolean('is_active')->default(true); // مستخدمة في النظام
                $table->boolean('is_stopped')->default(false); // إيقاف العملة
                $table->json('denominations')->nullable(); // فئات العملة النقدية
                $table->text('notes')->nullable(); // ملاحظات
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });

            // بذر العملات الافتراضية الأولية (الريال اليمني كعملة محلية، والريال السعودي والدولار كعملات أجنبية)
            DB::table('currencies')->insert([
                [
                    'currency_code' => 'YER',
                    'name' => 'ريال يمني',
                    'name_en' => 'Yemeni Rial',
                    'symbol' => 'ر.ي',
                    'fraction_name' => 'فلس',
                    'is_local' => true,
                    'exchange_rate' => 1.0000,
                    'max_exchange_rate' => 1.0000,
                    'min_exchange_rate' => 1.0000,
                    'valuation_rate' => 1.0000,
                    'buy_rate' => 1.0000,
                    'max_buy_rate' => 1.0000,
                    'sell_rate' => 1.0000,
                    'min_sell_rate' => 1.0000,
                    'is_default_transfer' => true,
                    'is_active' => true,
                    'is_stopped' => false,
                    'denominations' => json_encode([1000, 500, 250, 200, 100]),
                    'notes' => 'العملة المحلية الأساسية للمنشأة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'currency_code' => 'SAR',
                    'name' => 'ريال سعودي',
                    'name_en' => 'Saudi Riyal',
                    'symbol' => 'ر.س',
                    'fraction_name' => 'هللة',
                    'is_local' => false,
                    'exchange_rate' => 142.0000,
                    'max_exchange_rate' => 146.0000,
                    'min_exchange_rate' => 138.0000,
                    'valuation_rate' => 142.0000,
                    'buy_rate' => 141.5000,
                    'max_buy_rate' => 144.0000,
                    'sell_rate' => 142.5000,
                    'min_sell_rate' => 140.0000,
                    'is_default_transfer' => false,
                    'is_active' => true,
                    'is_stopped' => false,
                    'denominations' => json_encode([500, 200, 100, 50, 20, 10, 5, 1]),
                    'notes' => 'عملة أجنبية معتمدة في تسعير الدورات وسندات القبض',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'currency_code' => 'USD',
                    'name' => 'دولار أمريكي',
                    'name_en' => 'US Dollar',
                    'symbol' => '$',
                    'fraction_name' => 'سنت',
                    'is_local' => false,
                    'exchange_rate' => 535.0000,
                    'max_exchange_rate' => 545.0000,
                    'min_exchange_rate' => 525.0000,
                    'valuation_rate' => 535.0000,
                    'buy_rate' => 533.0000,
                    'max_buy_rate' => 540.0000,
                    'sell_rate' => 537.0000,
                    'min_sell_rate' => 530.0000,
                    'is_default_transfer' => false,
                    'is_active' => true,
                    'is_stopped' => false,
                    'denominations' => json_encode([100, 50, 20, 10, 5, 1]),
                    'notes' => 'الدولار الأمريكي المعتمد في التحويلات الدولية',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. إضافة حقول أهداف المنهج والدورة والأساسيات إلى جدول courses
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'course_objectives')) {
                $table->text('course_objectives')->nullable()->after('description');
            }
            if (! Schema::hasColumn('courses', 'course_syllabus')) {
                $table->text('course_syllabus')->nullable()->after('course_objectives');
            }
            if (! Schema::hasColumn('courses', 'course_prerequisites')) {
                $table->text('course_prerequisites')->nullable()->after('course_syllabus');
            }
        });

        // 3. إضافة حقل الموارد العامة والجمهور المستهدف (خاص بالأستاذ / خاص بالطالب / للكل) إلى level_resources
        Schema::table('level_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('level_resources', 'is_general')) {
                $table->boolean('is_general')->default(false)->after('course_section_id');
            }
            if (! Schema::hasColumn('level_resources', 'target_audience')) {
                $table->string('target_audience', 20)->default('all')->after('is_general'); // 'all', 'teacher', 'student'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'course_objectives')) {
                $table->dropColumn('course_objectives');
            }
            if (Schema::hasColumn('courses', 'course_syllabus')) {
                $table->dropColumn('course_syllabus');
            }
            if (Schema::hasColumn('courses', 'course_prerequisites')) {
                $table->dropColumn('course_prerequisites');
            }
        });

        Schema::table('level_resources', function (Blueprint $table) {
            if (Schema::hasColumn('level_resources', 'is_general')) {
                $table->dropColumn('is_general');
            }
            if (Schema::hasColumn('level_resources', 'target_audience')) {
                $table->dropColumn('target_audience');
            }
        });
    }
};
