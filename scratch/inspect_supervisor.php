<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$cols = Schema::getColumnListing('permission_groups');
echo 'Columns: '.implode(', ', $cols)."\n\n";

$groups = DB::table('permission_groups')->get();
foreach ($groups as $g) {
    print_r($g);
}

$user = User::where('user_code', 'SUP-2026-0001')->first();
echo "\n=== USER SUP-2026-0001 ===\n";
print_r($user->toArray());
