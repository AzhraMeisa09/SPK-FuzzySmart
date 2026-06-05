<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
    /**
     * Tampilkan daftar siswa yang diampu guru
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil kelas yang diampu guru
        $kelasIds = $user->kelas()->pluck('kelas.id_kelas')->toArray();

        // Hanya kelas yang terdaftar pada periode aktif
        $periodeAktif = \App\Models\PeriodePenilaian::where('is_aktif', true)->first();
        if ($periodeAktif) {
            $kelasIdsPeriode = $periodeAktif->kelas()->pluck('kelas.id_kelas')->toArray();
            $kelasIds = array_intersect($kelasIds, $kelasIdsPeriode);
        }

        $siswa = Siswa::whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'wali'])
            ->orderBy('name', 'asc')
            ->get();

        return view('guru.siswa', compact('siswa'));
    }

    /**
     * Tampilkan detail siswa
     */
    public function show($id)
    {
        $user = Auth::user();
        $kelasIds = $user->kelas()->pluck('kelas.id_kelas')->toArray();

        $periodeAktif = \App\Models\PeriodePenilaian::where('is_aktif', true)->first();
        if ($periodeAktif) {
            $kelasIdsPeriode = $periodeAktif->kelas()->pluck('kelas.id_kelas')->toArray();
            $kelasIds = array_intersect($kelasIds, $kelasIdsPeriode);
        }

        $siswa = Siswa::whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'wali'])
            ->findOrFail($id);

        // Ambil data tambahan (Hasil SPK & Portofolio)
        $periodeAktif = \App\Models\PeriodePenilaian::where('is_aktif', true)->first();
        $evaluasi = null;
        if ($periodeAktif) {
            $evaluasi = \App\Models\Evaluasi::where('siswa_id', $siswa->id_siswa)
                ->where('periode_id', $periodeAktif->id_periode)
                ->first();
        }

        $portofolios = \App\Models\Portofolio::where('siswa_id', $siswa->id_siswa)
            ->with(['minggu', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.siswa_detail', compact('siswa', 'evaluasi', 'portofolios', 'periodeAktif'));
    }
}
