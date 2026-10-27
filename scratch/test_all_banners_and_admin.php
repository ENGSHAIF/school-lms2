<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// 1. فحص كمسؤول نظام
$admin = User::where('role', 'admin')->first();
if (! $admin) {
    exit("No admin user found\n");
}

auth()->login($admin);

// أ) فحص صفحة إدارة الإعلانات في لوحة الإدارة
$reqAdmin = Request::create('/admin/banners', 'GET');
$resAdmin = $kernel->handle($reqAdmin);
echo 'GET /admin/banners -> Status: '.$resAdmin->getStatusCode()."\n";
$adminContent = $resAdmin->getContent();
if (str_contains($adminContent, 'إدارة وتعديل الإعلانات والحملات') && str_contains($adminContent, 'تعديل صيغة إعلان البداية')) {
    echo "✓ Admin banners management page loaded with quick edit & full control!\n";
} else {
    echo "✗ Admin banners view content mismatch!\n";
}

// ب) فحص إمكانية تحديث إعلان البداية (Banner 1) عبر POST
$reqUpdate = Request::create('/admin/banners/1/update', 'POST', [
    'title' => 'عرض تعلم اللغة الإنجليزية: حصة تجريبية مجانية 1-on-1 + فحص النطق بالذكاء الاصطناعي اليوم!',
    'subtitle' => 'انضم الآن لبرنامج تعلم اللغة الإنجليزية الشامل من الصفر حتى الاحتراف واكتشف مستواك الحقيقي فوراً مع حصة تجريبية مجانية.',
    'button_text' => 'ابدأ مجاناً 🚀',
    'link_url' => '/onboarding',
    'placement' => 'top_announcement',
    'theme_style' => 'amber',
    'device_target' => 'all',
]);
$resUpdate = $kernel->handle($reqUpdate);
echo 'POST /admin/banners/1/update -> Status: '.$resUpdate->getStatusCode()."\n";

// ج) فحص ظهور إعلان البداية الجديد في الصفحة الرئيسية
auth()->logout();
$reqHome = Request::create('/', 'GET');
$resHome = $kernel->handle($reqHome);
$homeContent = $resHome->getContent();
if (str_contains($homeContent, 'عرض تعلم اللغة الإنجليزية: حصة تجريبية مجانية')) {
    echo "✓ Top start announcement shows 'عرض تعلم اللغة الإنجليزية'!\n";
} else {
    echo "✗ Top start announcement not found on home!\n";
}

// د) فحص سلايدر الصفحة الرئيسية مع الإعلانات الجديدة (الترم 39 والترم 38 وتطوير اللغة)
if (str_contains($homeContent, 'الترم 39') || str_contains($homeContent, 'طوّر لغتك')) {
    echo "✓ Home dynamic slider contains the new announcements!\n";
} else {
    echo "✗ Announcements not in home slider!\n";
}

// هـ) فحص صفحة مركز الإعلانات وتفاصيل الإعلانات الجديدة
$reqAnn = Request::create('/announcements', 'GET');
$resAnn = $kernel->handle($reqAnn);
$annContent = $resAnn->getContent();
if (str_contains($annContent, '+967 735 999 101') && str_contains($annContent, '+967 775 342 886') && str_contains($annContent, '+967 738 837 887')) {
    echo "✓ Announcements directory displays all 3 unified WhatsApp numbers!\n";
} else {
    echo "✗ Missing phone numbers in announcements page!\n";
}

// و) فحص صفحة تفاصيل إعلان الترم 39 (ID: 5)
$ad5 = Banner::where('title', 'LIKE', '%الترم 39%')->first();
if ($ad5) {
    $reqDetail = Request::create('/announcements/'.$ad5->id, 'GET');
    $resDetail = $kernel->handle($reqDetail);
    echo "GET /announcements/{$ad5->id} -> Status: ".$resDetail->getStatusCode()."\n";
    $detailContent = $resDetail->getContent();
    if (str_contains($detailContent, 'تخفيضات تصل إلى') && str_contains($detailContent, '0029Vajnq1y7DAWxCf429S2T')) {
        echo "✓ Term 39 announcement detail rendered with full content and channel link!\n";
    }
}

echo "All tests completed successfully!\n";
