<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => 'database/database.sqlite']);
DB::reconnect();

use App\Models\Kriteria;
use App\Models\PeriodePenilaian;
use App\Models\Evaluasi;
use App\Models\DetailEvaluasi;

$kriteria = Kriteria::all();
echo "ID | Nama | Bobot\n";
echo "---|------|------\n";
foreach ($kriteria as $k) {
    echo "{$k->id_kriteria} | {$k->nama_kriteria} | {$k->bobot_kriteria}\n";
}

$periode = PeriodePenilaian::latest()->first();
if ($periode) {
    echo "\nPeriode: {$periode->nama_periode}\n";
    $evaluasis = Evaluasi::where('periode_id', $periode->id_periode)
        ->orderBy('nilai_akhir', 'desc')
        ->get();
    
    echo "\nOriginal Ranking:\n";
    foreach ($evaluasis as $index => $e) {
        echo ($index + 1) . ". {$e->siswa->name} - {$e->nilai_akhir}\n";
    }
}
