<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswas = App\Models\Siswa::whereNotNull('wali_murid_id')->get();
$count = 0;
foreach($siswas as $s) {
    if ($s->wali_murid_id) {
        $s->wali()->syncWithoutDetaching([$s->wali_murid_id]);
        $count++;
    }
}
echo "Synced {$count} records.\n";
