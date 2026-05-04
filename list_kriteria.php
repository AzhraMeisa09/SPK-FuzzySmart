<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$k = \App\Models\Kriteria::all();
foreach ($k as $kr) {
    echo $kr->id . " : " . $kr->nama . "\n";
}
