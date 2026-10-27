<?php

use App\Models\Banner;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// 1. اختبار الصفحة الرئيسية
$responseHome = $kernel->handle(
    $requestHome = Request::create('/', 'GET')
);
echo 'GET / -> Status: '.$responseHome->getStatusCode()."\n";
$homeContent = $responseHome->getContent();
if (str_contains($homeContent, 'افتتاح قناة ASOTS')) {
    echo "✓ Home contains seeded announcement title!\n";
} else {
    echo "✗ Home does not contain seeded announcement title!\n";
}
if (str_contains($homeContent, 'برنامج تعليم اللغة الإنجليزية من الصفر حتى الاحتراف')) {
    echo "✓ Home contains primary pillar: English from scratch to proficiency!\n";
} else {
    echo "✗ Home does not contain primary pillar!\n";
}
if (str_contains($homeContent, 'مدة الحصة')) {
    echo "✓ Home contains session duration indicators!\n";
}

// 2. اختبار صفحة الإعلانات
$responseAnn = $kernel->handle(
    $requestAnn = Request::create('/announcements', 'GET')
);
echo 'GET /announcements -> Status: '.$responseAnn->getStatusCode()."\n";
$annContent = $responseAnn->getContent();
if (str_contains($annContent, 'مركز الإعلانات والفرص الأكاديمية والتدريبية') && str_contains($annContent, 'افتتاح قناة ASOTS')) {
    echo "✓ Announcements directory rendered perfectly with seeded announcement!\n";
} else {
    echo "✗ Announcements directory issue!\n";
}

// 3. اختبار صفحة تفاصيل الإعلان
$banner = Banner::where('title', 'LIKE', '%قناة ASOTS%')->first();
if ($banner) {
    $responseDetail = $kernel->handle(
        $requestDetail = Request::create('/announcements/'.$banner->id, 'GET')
    );
    echo "GET /announcements/{$banner->id} -> Status: ".$responseDetail->getStatusCode()."\n";
    $detailContent = $responseDetail->getContent();
    if (str_contains($detailContent, 'برنامج دبلوم اللغة الإنجليزية الشامل') && str_contains($detailContent, '735 999 101')) {
        echo "✓ Announcement detail rendered perfectly with full text and WhatsApp contacts!\n";
    } else {
        echo "✗ Announcement detail issue!\n";
    }
}
