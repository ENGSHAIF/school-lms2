<?php

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SupervisorController;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

$user = User::where('user_code', 'SUP-2026-0001')->first();
if (! $user) {
    echo "Supervisor SUP-2026-0001 not found!\n";
    exit(1);
}

Auth::login($user);
echo 'Logged in as: '.Auth::user()->name.' (Role: '.Auth::user()->role.', Code: '.Auth::user()->user_code.")\n";

// Test 1: Rendering supervisor dashboard
echo "\n--- Test 1: Rendering Supervisor Dashboard ---\n";
try {
    $controller = app(SupervisorController::class);
    $view = $controller->dashboard();
    $html = $view->render();
    echo 'Supervisor Dashboard rendered successfully! Length: '.strlen($html)." bytes\n";

    // Check if courses link appears
    if (strpos($html, route('admin.courses.index')) !== false) {
        echo "SUCCESS: Link to admin.courses.index found in rendered HTML!\n";
    } else {
        echo "FAIL: Link to admin.courses.index NOT found in rendered HTML!\n";
    }

    if (strpos($html, 'إدارة المناهج والأسعار') !== false) {
        echo "SUCCESS: Text 'إدارة المناهج والأسعار' found in rendered HTML!\n";
    } else {
        echo "FAIL: Text 'إدارة المناهج والأسعار' NOT found!\n";
    }
} catch (Throwable $e) {
    echo 'ERROR in Supervisor Dashboard: '.$e->getMessage()."\n".$e->getTraceAsString()."\n";
}

// Test 2: Rendering Admin Courses page
echo "\n--- Test 2: Rendering Admin Courses Page ---\n";
try {
    $adminController = app(AdminController::class);
    $view2 = $adminController->courses();
    $html2 = $view2->render();
    echo 'Admin Courses rendered successfully! Length: '.strlen($html2)." bytes\n";
    if (strpos($html2, 'إضافة دورة لمستوى') !== false) {
        echo "SUCCESS: 'إضافة دورة لمستوى' button found!\n";
    }
    if (strpos($html2, 'إضافة مستوى جديد') !== false) {
        echo "SUCCESS: 'إضافة مستوى جديد' button found!\n";
    }
} catch (Throwable $e) {
    echo 'ERROR in Admin Courses: '.$e->getMessage()."\n".$e->getTraceAsString()."\n";
}

// Test 3: Rendering Admin Students page
echo "\n--- Test 3: Rendering Admin Students Page ---\n";
try {
    $view3 = $adminController->students(request());
    $html3 = $view3->render();
    echo 'Admin Students rendered successfully! Length: '.strlen($html3)." bytes\n";
} catch (Throwable $e) {
    echo 'ERROR in Admin Students: '.$e->getMessage()."\n".$e->getTraceAsString()."\n";
}
