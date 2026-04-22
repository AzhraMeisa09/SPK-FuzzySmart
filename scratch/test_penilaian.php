<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\KategoriNilai;
use App\Models\PenilaianMingguan;

$kat = KategoriNilai::find(3); // BSB (L:50, M:100, U:100)
echo "Kategori: " . $kat->nama . "\n";

$p = PenilaianMingguan::updateOrCreate(
    ['siswa_id' => 2, 'jadwal_sub_id' => 1, 'guru_id' => 3],
    [
        'kategori_id' => $kat->id,
        'nilai_l' => $kat->nilai_l,
        'nilai_m' => $kat->nilai_m,
        'nilai_u' => $kat->nilai_u,
        'nilai_crisp' => $kat->nilai_crisp,
        'status' => 'final'
    ]
);

echo "Data tersimpan di penilaian_mingguan:\n";
print_r($p->fresh()->toArray());
