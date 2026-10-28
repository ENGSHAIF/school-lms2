<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
foreach ($tables as $t) {
    if (preg_match('/(account|voucher|journal|cheque|cost|budget|period|portfoli|note)/i', $t->name)) {
        echo $t->name . "\n";
    }
}
