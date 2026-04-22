<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MingguPenilaian;
use App\Models\PenilaianMingguan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilan Dashboard Guru yang selaras dengan Admin Pro
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil kelas yang diajar
        $kelas = $user->kelas;
        $allKelasIds = $kelas->pluck('id')->toArray();
        
        // Ambil semua siswa di kelas tersebut
        $allSiswa = Siswa::whereIn('kelas_id', $allKelasIds)->get();
        $totalSiswa = $allSiswa->count();
        
        // Periode & Minggu Aktif
        $mingguAktif = MingguPenilaian::where('status', 'aktif')->first();
        
        // Progres Penilaian Minggu Ini
        $terlayaniCount = 0;
        $progresPerSiswa = [];
        
        if ($mingguAktif) {
            $penilaianMingguIni = PenilaianMingguan::where('jadwal_sub_id', '!=', null)
                ->whereHas('jadwalSubkriteria', function($q) use ($mingguAktif) {
                    $q->where('minggu_id', $mingguAktif->id);
                })
                ->whereIn('siswa_id', $allSiswa->pluck('id'))
                ->get();

            $siswaSudahDinilaiIds = $penilaianMingguIni->pluck('siswa_id')->unique();
            $terlayaniCount = $siswaSudahDinilaiIds->count();

            // Ambil sample 6 siswa untuk progres di dashboard
            foreach ($allSiswa->take(6) as $s) {
                $p = $penilaianMingguIni->where('siswa_id', $s->id);
                $status = 'Belum';
                $kategori = '-';
                
                if ($p->count() > 0) {
                    $isFinal = $p->where('status', 'final')->count() > 0;
                    $status = $isFinal ? 'Final' : 'Draft';
                    
                    if ($isFinal) {
                        $avg = $p->avg('nilai_crisp');
                        if ($avg >= 85) $kategori = 'BSB';
                        elseif ($avg >= 70) $kategori = 'BSH';
                        else $kategori = 'MB';
                    }
                }

                $progresPerSiswa[] = [
                    'nama' => $s->nama,
                    'status' => $status,
                    'kategori' => $kategori
                ];
            }
        }

        // Distribusi Nilai (Global untuk Guru ini)
        $distribusi = [
            'BSB' => [
                'nama' => 'BSB',
                'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '>=', 85)->count(),
                'badge' => 'badge-emerald',
                'progress' => 'progress-green'
            ],
            'BSH' => [
                'nama' => 'BSH',
                'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '>=', 70)->where('nilai_crisp', '<', 85)->count(),
                'badge' => 'badge-amber',
                'progress' => 'progress-yellow'
            ],
            'MB' => [
                'nama' => 'MB',
                'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '<', 70)->where('nilai_crisp', '>', 0)->count(),
                'badge' => 'badge-rose',
                'progress' => 'progress-red'
            ],
        ];

        // Hitung persentase distribusi
        $totalEntri = array_sum(array_column($distribusi, 'count'));
        foreach ($distribusi as $key => $val) {
            $distribusi[$key]['percent'] = $totalEntri > 0 ? round(($val['count'] / $totalEntri) * 100) : 0;
        }

        return view('guru.dashboard', compact(
            'user', 'kelas', 'totalSiswa', 'mingguAktif', 
            'terlayaniCount', 'progresPerSiswa', 'distribusi'
        ));
    }
}
