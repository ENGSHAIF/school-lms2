<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;
use Illuminate\Contracts\Console\Kernel;

$banners = Banner::where('is_active', true)->get();
file_put_contents(__DIR__.'/banners_utf8.json', json_encode($banners->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo 'SAVED '.$banners->count()." BANNERS\n";
