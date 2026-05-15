<?php

namespace App\Services;

use App\Models\PeriodePenilaian;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use App\Models\PenilaianMingguan;
use App\Models\Evaluasi;
use App\Models\DetailEvaluasi;
use App\Models\TemplateRekomendasi;
use App\Models\TemplateRekomendasiUmum;
use App\Models\KategoriNilai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SpkService
{
    /**
     * IMPLEMENTASI FUZZY SMART - VERSI FINAL (KONSISTENSI DATA & INTEGRITAS HISTORIS)
     * 🎯 TUJUAN: Menghitung hasil evaluasi akhir dengan menjamin validitas data input.
     */
    public function hitungPeriode(PeriodePenilaian $periode)
    {
        /**
         * 🛡️ VALIDASI INTEGRITAS DATA (POIN KRUSIAL SIDANG)
         * Sistem hanya memproses data yang sudah lengkap dan final.
         */
        if ($periode->minggu()->where('status', '!=', 'selesai')->exists()) {
            throw new Exception("🔒 GAGAL: Masih ada minggu yang belum selesai. Semua minggu harus berstatus 'selesai' agar data valid.");
        }

        $siswaList = $periode->kelas->flatMap->siswa;
        $mingguIds = $periode->minggu->pluck('id_minggu');
        
        $totalJadwal = DB::table('jadwal_subkriteria')->whereIn('minggu_id', $mingguIds)->count();
        if ($totalJadwal == 0) throw new Exception("🔒 GAGAL: Jadwal subkriteria kosong.");

        foreach ($siswaList as $siswa) {
            $countPenilaian = PenilaianMingguan::where('siswa_id', $siswa->id_siswa)
                ->whereIn('jadwal_sub_id', DB::table('jadwal_subkriteria')->whereIn('minggu_id', $mingguIds)->pluck('id_jadwal_sub'))
                ->where('status', 'final')
                ->count();
            
            if ($countPenilaian < $totalJadwal) {
                throw new Exception("🔒 GAGAL: Penilaian siswa {$siswa->name} belum lengkap atau masih ada status 'draft'. Pastikan seluruh input guru sudah 'final'.");
            }
        }

        return DB::transaction(function () use ($periode, $siswaList, $mingguIds) {
            // Mengambil Bobot Dinamis dari database untuk fleksibilitas sistem
            $kriteriaList = Kriteria::orderBy('id_kriteria', 'asc')->get();
            
            /**
             * ⚙️ PARAMETER NORMALISASI SMART
             * Cmin dan Cmax saat ini disimpan sebagai konstanta berdasarkan standar penelitian,
             * namun arsitektur ini siap dikembangkan menjadi dinamis melalui database di masa depan.
             */
            /**
             * 🟢 PHASE 1 — AGREGASI DATA (HITUNG SEMUA COUT DULU)
             * Mengumpulkan nilai crisp rata-rata per kriteria untuk semua siswa dalam periode ini.
             */
            $coutMap = []; // [kriteria_id => [siswa_id => Cout]]
            $performanceData = []; // [siswa_id => [kriteria_id => ['Cout' => x, 'details' => [...], 'wi' => y]]]

            foreach ($siswaList as $siswa) {
                foreach ($kriteriaList as $kriteria) {
                    $subkriterias = $kriteria->subkriteria;
                    $totalCrisp = 0.0;
                    $subCount = $subkriterias->count();
                    $tempDetails = [];

                    foreach ($subkriterias as $sub) {
                        $penilaian = PenilaianMingguan::where('siswa_id', $siswa->id_siswa)
                            ->where('status', 'final')
                            ->whereHas('jadwalSubkriteria', function ($q) use ($mingguIds, $sub) {
                                $q->whereIn('minggu_id', $mingguIds)->where('subkriteria_id', $sub->id_subkriteria);
                            })->get();

                        $sumSubCrisp = $penilaian->sum('nilai_crisp');
                        $avgCrispSub = $penilaian->count() > 0 ? $sumSubCrisp / $penilaian->count() : 0.0;
                        $totalCrisp += $avgCrispSub;

                        // Ambil kategori langsung dari input terakhir guru (jangan dihitung ulang dari rata-rata nilai)
                        $latestPenilaian = $penilaian->sortByDesc(fn($p) => $p->minggu()->tanggal_mulai ?? $p->created_at)->first();
                        $katSub = $latestPenilaian?->kategori?->nama ?? 'MB';

                        $tempDetails[] = [
                            'sub_id' => $sub->id_subkriteria,
                            'crisp'  => round($avgCrispSub, 4),
                            'kat'    => $katSub,
                            'rek'    => $this->generateRekomendasiDetail($sub->id_subkriteria, $katSub, $siswa->name, $avgCrispSub)
                        ];
                    }

                    $Cout = $subCount > 0 ? $totalCrisp / $subCount : 0.0;
                    $coutMap[$kriteria->id_kriteria][$siswa->id_siswa] = $Cout;
                    
                    $performanceData[$siswa->id_siswa][$kriteria->id_kriteria] = [
                        'Cout' => $Cout,
                        'details' => $tempDetails,
                        'wi' => (double)($kriteria->bobot_kriteria ?? 0)
                    ];
                }
            }

            /**
             * 🟢 PHASE 2 — HITUNG Cmin & Cmax PER KRITERIA (DATA-DRIVEN)
             * Menentukan batas bawah dan atas berdasarkan distribusi nilai aktual siswa.
             */
            $minMaxMap = [];
            foreach ($coutMap as $kId => $values) {
                $minMaxMap[$kId] = [
                    'min' => count($values) > 0 ? min($values) : 0.0,
                    'max' => count($values) > 0 ? max($values) : 100.0,
                ];
                
                Log::info("SMART Dynamic Normalization - Kriteria: {$kId}", [
                    'Cmin' => $minMaxMap[$kId]['min'],
                    'Cmax' => $minMaxMap[$kId]['max']
                ]);
            }

            /**
             * 🟢 PHASE 3 — NORMALISASI & PERHITUNGAN AKHIR
             * Menghitung ui dan Va serta menyimpan hasil ke database.
             */
            foreach ($siswaList as $siswa) {
                $V_a = 0.0;

                $evaluasi = Evaluasi::updateOrCreate(
                    ['periode_id' => $periode->id_periode, 'siswa_id' => $siswa->id_siswa],
                    ['is_final' => true, 'nilai_akhir' => 0, 'kategori_akhir' => 'MB', 'rekomendasi' => '-']
                );
                DetailEvaluasi::where('evaluasi_id', $evaluasi->id_evaluasi)->delete();

                foreach ($kriteriaList as $kriteria) {
                    $data = $performanceData[$siswa->id_siswa][$kriteria->id_kriteria];
                    $Cout = $data['Cout'];
                    $Cmin = $minMaxMap[$kriteria->id_kriteria]['min'];
                    $Cmax = $minMaxMap[$kriteria->id_kriteria]['max'];
                    $wi = $data['wi'];

                    // Rumus Normalisasi SMART: ui = (Cout - Cmin) / (Cmax - Cmin)
                    if ($Cmax == $Cmin) {
                        $ui = 1.0; // Jika semua nilai sama, dianggap maksimal
                    } else {
                        $ui = ($Cout - $Cmin) / ($Cmax - $Cmin);
                    }
                    
                    $ui = max(0, min(1, $ui)); // Safety clamp boundary 0-1

                    foreach ($data['details'] as $td) {
                        DetailEvaluasi::create([
                            'evaluasi_id'        => $evaluasi->id_evaluasi,
                            'subkriteria_id'     => $td['sub_id'],
                            'nilai_crisp'        => $td['crisp'],
                            'nilai_normalisasi'  => (double)$ui,
                            'bobot_snapshot'     => $wi,
                            'kategori'           => $td['kat'],
                            'rekomendasi_detail' => $td['rek'],
                        ]);
                    }

                    // Akumulasi Va = Σ (wi × ui)
                    $V_a += ($wi * (double)$ui);
                }

                $V_a = max(0, min(1, $V_a));
                
                $katObj = KategoriNilai::findByNilai($V_a * 100);
                $finalKat = $katObj ? $katObj->nama : 'MB';

                $rekUmum = $this->generateRekomendasiUmum($evaluasi->id_evaluasi, $finalKat);

                $evaluasi->update([
                    'nilai_akhir'    => round($V_a, 4),
                    'kategori_akhir' => $finalKat,
                    'rekomendasi'    => $rekUmum,
                ]);
            }

            return true;
        });
    }

    /**
     * 🤖 GENERATE REKOMENDASI OTOMATIS (SISTEM PAKAR)
     * Memberikan saran berdasarkan aspek dengan nilai di bawah threshold (75).
     */
    private function generateRekomendasiDetail($subId, $kat, $namaSiswa, $crisp)
    {
        $template = TemplateRekomendasi::where('subkriteria_id', $subId)
            ->where('kategori', $kat)
            ->first();

        if (!$template) {
            // Fallback jika tidak ada template spesifik
            $sub = Subkriteria::find($subId);
            $aspek = $sub ? $sub->nama_subkriteria : 'aspek ini';
            
            if ($kat === 'BSB') {
                return "Ananda {$namaSiswa} menunjukkan perkembangan yang sangat baik dalam {$aspek}.";
            }
            if ($kat === 'BSH') {
                return "Cukup baik dalam {$aspek}.";
            }
            return "Perlu bimbingan dalam {$aspek}";
        }

        $isi = $template->isi;

        // Replace placeholder {{nama_siswa}} jika ada
        $isi = str_replace('{{nama_siswa}}', $namaSiswa, $isi);
        
        return trim($isi);
    }

    private function generateRekomendasiUmum($evaluasiId, $kat)
    {
        $details = DetailEvaluasi::where('evaluasi_id', $evaluasiId)->with('subkriteria')->get();
        $template = TemplateRekomendasiUmum::where('kategori', $kat)->first();

        // Ambil semua aspek (subkriteria) yang mendapatkan kategori 'MB' (Mulai Berkembang)
        $lemah = $details->filter(fn($d) => $d->kategori === 'MB')
            ->pluck('subkriteria.nama_subkriteria')
            ->filter()
            ->unique()
            ->toArray();

        if (!empty($lemah)) {
            $aspekStr = implode(', ', $lemah);
        } else {
            $aspekStr = 'seluruh aspek';
        }

        if (!$template) {
            if ($kat === 'BSB') {
                return "Ananda menunjukkan perkembangan yang sangat luar biasa dan konsisten di seluruh aspek perkembangan.";
            }
            if ($kat === 'BSH') {
                return "Ananda menunjukkan perkembangan yang baik dan stabil pada seluruh indikator penilaian.";
            }
            return "Hasil evaluasi {$kat}. Perlu perhatian lebih pada aspek: {$aspekStr}.";
        }

        // Replace placeholder {{aspek}} dengan daftar subkriteria yang MB
        $isi = str_replace('{{aspek}}', $aspekStr, $template->isi);
        
        // Juga pastikan {{nama_siswa}} bisa diganti jika ada di template umum
        $siswa = Evaluasi::find($evaluasiId)->siswa;
        if ($siswa) {
            $isi = str_replace('{{nama_siswa}}', $siswa->name, $isi);
        }

        return trim($isi);
    }

    /**
     * 🏷️ GET KATEGORI BY NILAI SPK
     * Mengambil kode kategori (MB, BSH, BSB) secara dinamis dari database.
     */
    private function getKategoriByNilai($V_a)
    {
        $nilaiPersen = $V_a * 100;
        $kategori = KategoriNilai::findByNilai($nilaiPersen);
        return $kategori ? $kategori->nama : 'MB';
    }
}
