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
        $listPeriode = PeriodePenilaian::with('tahunAjaran')->orderBy('created_at', 'desc')->get();

        $periodeId = $request->input('periode_id');
        $periode = null;

        if ($periodeId) {
            $periode = PeriodePenilaian::with('tahunAjaran')->find($periodeId);
        }

        if (!$periode) {
            $periode = PeriodePenilaian::with('tahunAjaran')->where('is_aktif', true)->first();
        }

        if (!$periode) {
            $periode = PeriodePenilaian::with('tahunAjaran')->where('status', 'final')->latest('finalized_at')->first();
        }

        if (!$periode) {
            return view('admin.hasil_evaluasi', [
                'groupedData' => collect(),
                'periode' => null,
                'kelas' => Kelas::all(),
                'selectedKelas' => null,
                'isFinalized' => false,
                'progresPengisian' => collect(),
                'listPeriode' => $listPeriode,
                'error' => 'Belum ada hasil evaluasi. Silakan aktifkan atau finalisasi periode terlebih dahulu.'
            ]);
        }

        $isFinalized = ($periode->status === 'final');
        $progresPengisian = collect();

        if (!$isFinalized) {
            // Periode aktif tapi belum final: Hitung progres pengisian
            // Ambil semua minggu di periode ini
            $weeks = $periode->minggu;
            $jadwalIds = \App\Models\JadwalSubkriteria::whereIn('minggu_id', $weeks->pluck('id_minggu'))->pluck('id_jadwal_sub');
            
            $totalJadwalSub = $jadwalIds->count();
            $kelasInPeriode = $periode->kelas()->orderBy('nama_kelas')->get();

            foreach ($kelasInPeriode as $k) {
                $siswaIds = $k->siswa()->pluck('id_siswa');
                $totalSiswa = $siswaIds->count();
                $totalHarusDinilai = $totalSiswa * $totalJadwalSub;

                $totalDraft = 0;
                $totalFinal = 0;
                $persen = 0;

                if ($totalHarusDinilai > 0) {
                    $totalDraft = \App\Models\PenilaianMingguan::whereIn('jadwal_sub_id', $jadwalIds)
                        ->whereIn('siswa_id', $siswaIds)
                        ->where('status', 'draft')
                        ->count();

                    $totalFinal = \App\Models\PenilaianMingguan::whereIn('jadwal_sub_id', $jadwalIds)
                        ->whereIn('siswa_id', $siswaIds)
                        ->where('status', 'final')
                        ->count();

                    $totalIsi = $totalDraft + $totalFinal;
                    $persen = round(($totalIsi / $totalHarusDinilai) * 100);
                }

                $progresPengisian->push((object)[
                    'kelas' => $k,
                    'total_siswa' => $totalSiswa,
                    'total_harus' => $totalHarusDinilai,
                    'total_draft' => $totalDraft,
                    'total_final' => $totalFinal,
                    'total_isi' => $totalDraft + $totalFinal,
                    'persen' => $persen
                ]);
            }
        }

        $query = Evaluasi::with(['siswa.kelas'])
            ->where('periode_id', $periode->id_periode)
            ->where('is_final', true); // Hanya ambil yang sudah final

        $evaluasiList = $query->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $groupedData = collect();

        foreach ($kelas as $k) {
            $siswaKelas = $evaluasiList->filter(function($item) use ($k) {
                return $item->siswa && $item->siswa->kelas_id == $k->id_kelas;
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

        return view('admin.hasil_evaluasi', compact('groupedData', 'periode', 'isFinalized', 'progresPengisian', 'listPeriode'));
    }

    public function show(Evaluasi $evaluasi)
    {
        $evaluasi->load(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria']);
        return view('admin.hasil_evaluasi_detail', compact('evaluasi'));
    }
}
