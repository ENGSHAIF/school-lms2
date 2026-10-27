<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

$users = User::all();
foreach ($users as $u) {
    $isPass123 = Hash::check('password123', $u->password);
    echo "ID: {$u->id} | Email: {$u->email} | Pass is password123? ".($isPass123 ? 'YES' : 'NO')."\n";
}
