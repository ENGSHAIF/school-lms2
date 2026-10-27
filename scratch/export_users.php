<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

$users = User::all(['id', 'name', 'email', 'phone_whatsapp', 'role', 'status', 'user_code', 'student_code', 'password']);
file_put_contents(__DIR__.'/users_export.json', json_encode($users->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo 'Exported '.$users->count()." users\n";
