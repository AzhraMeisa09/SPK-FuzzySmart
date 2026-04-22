<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\PeriodePenilaian;
use App\Models\MingguPenilaian;
use App\Models\PenilaianMingguan;
use App\Models\KategoriNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalGuru = User::where('role', 'guru')->count();
        
        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->with('tahunAjaran')->first();
        $mingguAktif = null;
        
        if ($periodeAktif) {
            $mingguAktif = MingguPenilaian::where('periode_id', $periodeAktif->id)
                ->where('status', 'aktif')
                ->first();
        }

        // 2. Progres Penilaian per Kelas (Jika ada minggu aktif)
        $progresPerKelas = [];
        if ($mingguAktif) {
            $classes = Kelas::all();
            
            // Ambil ID siswa yang sudah dinilai di minggu ini (minimal 1 subkriteria)
            $jadwalIds = $mingguAktif->subkriteria()->pluck('jadwal_subkriteria.id');
            
            $assessedSiswaIds = PenilaianMingguan::whereIn('jadwal_sub_id', $jadwalIds)
                ->distinct('siswa_id')
                ->pluck('siswa_id')
                ->toArray();

            foreach ($classes as $kelas) {
                $totalSiswaKelas = $kelas->siswa()->count();
                if ($totalSiswaKelas > 0) {
                    $assessedInKelas = $kelas->siswa()->whereIn('id', $assessedSiswaIds)->count();
                    $percentage = round(($assessedInKelas / $totalSiswaKelas) * 100);
                    
                    $progresPerKelas[] = [
                        'nama' => $kelas->nama_kelas,
                        'total' => $totalSiswaKelas,
                        'terlayani' => $assessedInKelas,
                        'persen' => $percentage
                    ];
                }
            }
        }

        // 3. Distribusi Nilai (Tally dari PenilaianMingguan)
        // Kita hitung distribusi nilai BSB, BSH, MB secara global (atau per minggu aktif)
        $distribusi = [];
        $kategori = KategoriNilai::orderBy('nilai_crisp', 'desc')->get(); // Diurutkan dari nilai tertinggi (BSB)
        
        $totalPenilaian = PenilaianMingguan::count();
        
        foreach ($kategori as $k) {
            $count = PenilaianMingguan::where('kategori_id', $k->id)->count();
            $percent = $totalPenilaian > 0 ? round(($count / $totalPenilaian) * 100) : 0;
            
            // Map kategori ke badge style
            $badge = 'badge-mb';
            $progress = 'progress-red';
            if (str_contains(strtoupper($k->nama), 'BSB')) { $badge = 'badge-bsb'; $progress = 'progress-green'; }
            if (str_contains(strtoupper($k->nama), 'BSH')) { $badge = 'badge-bsh'; $progress = 'progress-yellow'; }

            $distribusi[] = [
                'nama' => $k->nama,
                'count' => $count,
                'percent' => $percent,
                'progress' => $progress,
                'badge' => $badge
            ];
        }

        return view('admin.dashboard', compact(
            'totalSiswa', 
            'totalKelas', 
            'totalGuru', 
            'periodeAktif', 
            'mingguAktif',
            'progresPerKelas',
            'distribusi'
        ));
    }
}
