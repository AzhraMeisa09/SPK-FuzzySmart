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
        $kelasIds = $user->kelas()->pluck('kelas.id');

        $siswa = Siswa::whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'wali'])
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.siswa', compact('siswa'));
    }

    /**
     * Tampilkan detail siswa
     */
    public function show($id)
    {
        $user = Auth::user();
        $kelasIds = $user->kelas()->pluck('kelas.id');

        $siswa = Siswa::whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'wali'])
            ->findOrFail($id);

        // Ambil data tambahan (Hasil SPK & Portofolio)
        $periodeAktif = \App\Models\PeriodePenilaian::where('is_aktif', true)->first();
        $evaluasi = null;
        if ($periodeAktif) {
            $evaluasi = \App\Models\Evaluasi::where('siswa_id', $siswa->id)
                ->where('periode_id', $periodeAktif->id)
                ->first();
        }

        $portofolios = \App\Models\Portofolio::where('siswa_id', $siswa->id)
            ->with(['minggu', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.siswa_detail', compact('siswa', 'evaluasi', 'portofolios', 'periodeAktif'));
    }
}
