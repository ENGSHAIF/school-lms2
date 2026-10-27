<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\StudentApplicationController;
use App\Models\StudentApplication;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// 1. اختبار الحفظ عبر الـ Controller
$controller = new StudentApplicationController;

$testData = [
    'name_ar' => 'سالم عبد الله العولقي',
    'name_en' => 'Salem Abdullah Al-Awlaqi',
    'birth_place' => 'صنعاء',
    'birth_date' => '2001-05-15',
    'educational_level' => 'طالب جامعي (بكالوريوس)',
    'study_phone_whatsapp' => '+967 735 999 101',
    'parent_phone' => '+967 775 342 886',
    'needs_placement_test' => '1',
    'course_interest' => 'دبلوم اللغة الإنجليزية الشامل',
];

$request = Request::create('/student-registration', 'POST', $testData);
$app->instance('request', $request);
$session = $app['session']->driver();
$request->setLaravelSession($session);

$response = $controller->store($request);

echo 'Controller store response -> Status: '.$response->getStatusCode()."\n";

$saved = StudentApplication::where('name_ar', 'سالم عبد الله العولقي')->first();
if ($saved) {
    echo "✓ Application successfully saved to DB with ID: {$saved->id} and Term: {$saved->term}!\n";
    echo "✓ WhatsApp Clean Study Phone: {$saved->clean_study_phone}\n";

    // 2. فحص صفحة الطباعة
    $printView = $controller->print($saved->id);
    $renderedHtml = $printView->render();
    if (str_contains($renderedHtml, 'Salem Abdullah Al-Awlaqi') && str_contains($renderedHtml, 'Term 40')) {
        echo "✓ Print view rendered perfectly with official ASOTS header and student details!\n";
    } else {
        echo '✗ Print view check failed: '.substr(strip_tags($renderedHtml), 0, 150)."\n";
    }
} else {
    echo "✗ Application was not saved!\n";
}
