<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cloud:sync-export', function () {
    $this->info('بدء توليد ملف تهجير المزامنة السحابية الشاملة...');

    $banners = DB::table('banners')->get();
    $timestamp = date('Y_m_d_His');
    $fileName = "database/migrations/{$timestamp}_sync_cloud_production_data.php";

    $bannersArray = var_export(json_decode(json_encode($banners), true), true);

    $code = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Support\Facades\DB;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        \$banners = {$bannersArray};\n        foreach (\$banners as \$b) {\n            DB::table('banners')->updateOrInsert(['id' => \$b['id']], \$b);\n        }\n    }\n\n    public function down(): void {}\n};\n";

    file_put_contents(base_path($fileName), $code);

    $this->info("تم بنجاح إنشاء ملف المزامنة: {$fileName}");
    $this->comment('الآن يمكنك رفع التحديثات إلى السحاب بالأوامر:');
    $this->line('git add .');
    $this->line('git commit -m "sync data to cloud"');
    $this->line('git push origin main');
})->purpose('تصدير بيانات المنصة المحلية إلى migration جاهزة للرفع إلى Laravel Cloud');
