<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c1 = \App\Models\Kriteria::where('nama', 'like', '%Agama%')->orWhere('nama', 'like', '%NAB%')->first();
if ($c1) { $c1->kode = 'C1'; $c1->save(); echo "C1 updated\n"; }

$c2 = \App\Models\Kriteria::where('nama', 'like', '%Jati%')->first();
if ($c2) { $c2->kode = 'C2'; $c2->save(); echo "C2 updated\n"; }

$c3 = \App\Models\Kriteria::where('nama', 'like', '%Litera%')->first();
if ($c3) { $c3->kode = 'C3'; $c3->save(); echo "C3 updated\n"; }
echo "Done\n";
