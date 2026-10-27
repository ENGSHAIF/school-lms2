<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;
use Illuminate\Contracts\Console\Kernel;

$banners = Banner::orderBy('id')->get();
echo 'TOTAL BANNERS: '.$banners->count()."\n";
foreach ($banners as $b) {
    echo "ID: {$b->id}\n";
    echo "Title: {$b->title}\n";
    echo "Category: {$b->category}\n";
    echo "Placement: {$b->placement}\n";
    echo "Image: {$b->image_path}\n";
    echo 'Active: '.($b->is_active ? 'YES' : 'NO')."\n";
    echo "Sort: {$b->sort_order}\n";
    echo "----------------------------------------\n";
}
