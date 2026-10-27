<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

$users = User::whereIn('id', [1, 2, 3, 4, 5, 6, 10, 18, 20, 21])->get();
foreach ($users as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | Role: {$u->role} | Password Hash: {$u->password}\n";
}
