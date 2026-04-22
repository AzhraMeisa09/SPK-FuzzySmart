<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\PeriodePenilaian;
use App\Models\MingguPenilaian;
use App\Models\PenilaianMingguan;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Auth;

class WaliController extends Controller
{
    /**
     * Dashboard Wali Murid
     */
    public function dashboard()
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->first();
        
        // Data ringkasan untuk anak pertama (sebagai default)
        $selectedAnak = $anak->first();
        $penilaianTerbaru = collect();
        $evaluasiTerakhir = null;

        if ($selectedAnak) {
            $penilaianTerbaru = PenilaianMingguan::where('siswa_id', $selectedAnak->id)
                ->whereHas('jadwalSubkriteria.minggu', function($q) {
                    $q->where('status', 'selesai'); // Hanya yang sudah difinalisasi admin
                })
                ->with(['jadwalSubkriteria.subkriteria', 'kategori'])
                ->latest()
                ->take(5)
                ->get();

            if ($periodeAktif) {
                $evaluasiTerakhir = Evaluasi::where('siswa_id', $selectedAnak->id)
                    ->where('periode_id', $periodeAktif->id)
                    ->first();
            }
        }

        return view('wali.dashboard', compact('anak', 'periodeAktif', 'selectedAnak', 'penilaianTerbaru', 'evaluasiTerakhir'));
    }

    /**
     * Laporan Perkembangan Mingguan
     */
    public function perkembangan(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        // Pilihan anak (jika punya lebih dari satu)
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $selectedAnak = $anak->where('id', $siswaId)->first();

        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->first();
        $mingguSelesai = collect();

        if ($periodeAktif && $selectedAnak) {
            // Ambil semua minggu yang sudah SELESAI (Final Minggu)
            $mingguSelesai = MingguPenilaian::where('periode_id', $periodeAktif->id)
                ->where('status', 'selesai')
                ->with(['jadwalSubkriteria.penilaian' => function($q) use ($siswaId) {
                    $q->where('siswa_id', $siswaId)->with('kategori');
                }, 'jadwalSubkriteria.subkriteria'])
                ->orderBy('minggu_ke', 'desc')
                ->get();
        }

        return view('wali.perkembangan', compact('anak', 'selectedAnak', 'periodeAktif', 'mingguSelesai'));
    }

    /**
     * Laporan Evaluasi Akhir (SPK Result)
     */
    public function evaluasi(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $selectedAnak = $anak->where('id', $siswaId)->first();

        // Cari periode yang sudah FINAL atau sednag aktif
        $periode = PeriodePenilaian::whereIn('status', ['aktif', 'final'])
            ->orderBy('id', 'desc')
            ->first();

        $evaluasi = null;
        if ($selectedAnak && $periode) {
            $evaluasi = Evaluasi::where('siswa_id', $selectedAnak->id)
                ->where('periode_id', $periode->id)
                ->with(['detail.kriteria', 'rekomendasi'])
                ->first();
        }

        return view('wali.evaluasi', compact('anak', 'selectedAnak', 'periode', 'evaluasi'));
    }

    /**
     * Rapor Digital / Laporan Akhir
     */
    public function laporan()
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        // Logika rapor (biasanya pdf atau ringkasan sangat lengkap)
        return view('wali.laporan', compact('anak'));
    }
}
