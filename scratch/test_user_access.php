<?php

use App\Http\Controllers\AdminController;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('user_code', 'SUP-2026-0001')->first();
Auth::login($user);

echo 'Logged in as: '.Auth::user()->name.' ('.Auth::user()->role.")\n";

// Test courses index
$controller = app(AdminController::class);
try {
    $response = $controller->courses();
    echo 'Courses controller response: '.get_class($response)." (SUCCESS)\n";
} catch (Exception $e) {
    echo 'Courses controller EXCEPTION: '.$e->getMessage()."\n";
}

// Test students index
try {
    $request = new Request;
    $response = $controller->students($request);
    echo 'Students controller response: '.get_class($response)." (SUCCESS)\n";
} catch (Exception $e) {
    echo 'Students controller EXCEPTION: '.$e->getMessage()."\n";
}
