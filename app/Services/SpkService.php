<?php

namespace App\Services;

use App\Models\PeriodePenilaian;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\PenilaianMingguan;
use App\Models\Evaluasi;
use App\Models\DetailEvaluasi;
use Illuminate\Support\Facades\DB;

class SpkService
{
    /**
     * Hitung SPK Fuzzy SMART untuk satu periode
     */
    public function hitungPeriode(PeriodePenilaian $periode)
    {
        return DB::transaction(function () use ($periode) {
            $siswaList = $periode->kelas->flatMap->siswa;
            $kriteriaList = Kriteria::with('subkriteria')->get();
            $mingguIds = $periode->minggu->pluck('id');

            foreach ($siswaList as $siswa) {
                $nilaiAkhir = 0;
                
                // 1. Buat atau update header evaluasi
                $evaluasi = Evaluasi::updateOrCreate(
                    [
                        'periode_id' => $periode->id,
                        'siswa_id' => $siswa->id,
                    ],
                    [
                        'is_final' => true,
                    ]
                );

                foreach ($kriteriaList as $kriteria) {
                    // 2. Ambil semua penilaian_mingguan untuk siswa ini, kriteria ini, di periode ini
                    // Caranya: cari jadwal_subkriteria yang merupakan bagian dari kriteria ini dan minggu di periode ini
                    $subIds = $kriteria->subkriteria->pluck('id');
                    
                    $penilaian = PenilaianMingguan::where('siswa_id', $siswa->id)
                        ->whereHas('jadwalSubkriteria', function ($q) use ($mingguIds, $subIds) {
                            $q->whereIn('minggu_id', $mingguIds)
                              ->whereIn('subkriteria_id', $subIds);
                        })
                        ->with('kategori')
                        ->get();

                    if ($penilaian->isEmpty()) {
                        $rataNilai = 0;
                    } else {
                        // 3. Hitung rata-rata nilai crisp langsung dari kolom fisik (Historical Integrity)
                        $rataNilai = $penilaian->avg(function ($p) {
                            return $p->nilai_crisp ?? 0;
                        });
                    }

                    // 4. Simpan detail evaluasi (Criteria Level Score)
                    DetailEvaluasi::updateOrCreate(
                        [
                            'evaluasi_id' => $evaluasi->id,
                            'kriteria_id' => $kriteria->id,
                        ],
                        [
                            'nilai' => $rataNilai,
                            'bobot_snapshot' => $kriteria->bobot,
                        ]
                    );

                    // 5. Akumulasi nilai akhir (SMART: Sum of utility * weight)
                    $nilaiAkhir += ($rataNilai * $kriteria->bobot);
                }

                // 6. Update nilai akhir dan kategori
                $evaluasi->nilai_akhir = $nilaiAkhir;
                $evaluasi->kategori_akhir = $evaluasi->tentukanKategori();
                $evaluasi->save();
            }

            return true;
        });
    }
}
