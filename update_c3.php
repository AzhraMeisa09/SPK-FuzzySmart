<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c3 = \App\Models\Kriteria::where('nama', 'like', '%STEAM%')->first();
if ($c3) { $c3->kode = 'C3'; $c3->save(); echo "C3 updated\n"; }
echo "Done\n";
