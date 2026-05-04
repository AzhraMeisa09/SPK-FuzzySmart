<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Evaluasi;
use App\Models\PeriodePenilaian;
use App\Models\Kelas;

class HasilEvaluasiController extends Controller
{
    public function index(Request $request)
    {
        // Prioritaskan periode yang sudah final, jika tidak ada cari yang aktif
        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        
        if (!$periode) {
            $periode = PeriodePenilaian::where('is_aktif', true)->first();
        }

        if (!$periode) {
            return view('admin.hasil_evaluasi', [
                'data' => collect(),
                'periode' => null,
                'kelas' => Kelas::all(),
                'selectedKelas' => null,
                'error' => 'Belum ada hasil evaluasi. Silakan aktifkan atau finalisasi periode terlebih dahulu.'
            ]);
        }

        $query = Evaluasi::with(['siswa.kelas'])
            ->where('periode_id', $periode->id)
            ->where('is_final', true); // Hanya ambil yang sudah final

        $evaluasiList = $query->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $groupedData = collect();

        foreach ($kelas as $k) {
            $siswaKelas = $evaluasiList->filter(function($item) use ($k) {
                return $item->siswa && $item->siswa->kelas_id == $k->id;
            })->sortByDesc('nilai_akhir')->values();

            if ($siswaKelas->isNotEmpty()) {
                // Tambahkan ranking per kelas
                $siswaKelas = $siswaKelas->map(function ($item, $index) {
                    $item->ranking = $index + 1;
                    return $item;
                });
                
                $groupedData->push((object)[
                    'kelas' => $k,
                    'data'  => $siswaKelas
                ]);
            }
        }

        return view('admin.hasil_evaluasi', compact('groupedData', 'periode'));
    }

    public function show(Evaluasi $evaluasi)
    {
        $evaluasi->load(['siswa.kelas', 'periode', 'detail.kriteria']);
        return view('admin.hasil_evaluasi_detail', compact('evaluasi'));
    }
}
