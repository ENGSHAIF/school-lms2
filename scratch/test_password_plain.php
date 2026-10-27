<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

$candidates = ['123456', '12345678', '123456789', 'admin', 'admin123', 'password', 'password123', 'shaif', 'shaif123', '775342886', '735999101', 'secret', 'demo123', 'asots', 'asots123'];

$users = User::whereIn('id', [1, 6, 10, 20, 21])->get();
foreach ($users as $u) {
    $found = 'UNKNOWN';
    foreach ($candidates as $cand) {
        if (Hash::check($cand, $u->password)) {
            $found = $cand;
            break;
        }
    }
    echo "User: {$u->name} ({$u->email}) -> Plain password is: [{$found}]\n";
}
