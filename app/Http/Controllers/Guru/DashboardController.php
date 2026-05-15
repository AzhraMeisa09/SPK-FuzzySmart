<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MingguPenilaian;
use App\Models\PenilaianMingguan;
use App\Models\PeriodePenilaian;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Evaluasi;
use App\Models\KategoriNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function matchKategori($nilaiDecimal)
    {
        $nilaiPersen = $nilaiDecimal * 100;
        $kategori = KategoriNilai::findByNilai($nilaiPersen);
        return $kategori ? $kategori->nama : "MB";
    }

    public function index()
    {
        $user = Auth::user();
        $kelas = $user->kelas;
        $allKelasIds = $kelas->pluck('id_kelas')->toArray();
        $allSiswa = Siswa::whereIn('kelas_id', $allKelasIds)->get();
        $totalSiswa = $allSiswa->count();

        $kriteriaList = Kriteria::orderBy('id_kriteria', 'asc')->get();

        $periode = PeriodePenilaian::where('is_aktif', true)->first();
        $mingguAktif = null; $semuaMinggu = collect(); $mingguSelesaiCount = 0;

        if ($periode) {
            $semuaMinggu = MingguPenilaian::where('periode_id', $periode->id_periode)->orderBy('minggu_ke')->get();
            $mingguAktif = $semuaMinggu->where('status', 'aktif')->first();
            $mingguSelesaiCount = $semuaMinggu->where('status', 'selesai')->count();
        }

        $terlayaniCount = 0; $progresPerSiswa = []; $totalProgresPercent = 0;

        if ($mingguAktif) {
            $jadwalIds = $mingguAktif->jadwalSubkriteria->pluck('id');
            $totalHarusDinilai = $jadwalIds->count() * $totalSiswa;
            $penilaianMingguIni = PenilaianMingguan::whereIn('jadwal_sub_id', $jadwalIds)->whereIn('siswa_id', $allSiswa->pluck('id_siswa'))->get();
            $terlayaniCount = $penilaianMingguIni->where('status', 'final')->pluck('siswa_id')->unique()->count();
            $totalProgresPercent = $totalHarusDinilai > 0 ? round(($penilaianMingguIni->count() / $totalHarusDinilai) * 100) : 0;

            /**
             * 🟢 DYNAMIC NORMALIZATION PREVIEW
             */
            $coutMap = []; 
            foreach ($allSiswa as $s) {
                $pSiswa = $penilaianMingguIni->where('siswa_id', $s->id_siswa);
                $pGrouped = $pSiswa->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria->kriteria_id);
                foreach ($kriteriaList as $krit) {
                    $items = $pGrouped->get($krit->id_kriteria);
                    $cout = ($items && $items->count() > 0) ? $items->avg('nilai_crisp') : 0.0;
                    $coutMap[$krit->id_kriteria][$s->id_siswa] = $cout;
                }
            }

            $minMaxMap = [];
            foreach ($coutMap as $kId => $values) {
                $minMaxMap[$kId] = [
                    'min' => count($values) > 0 ? min($values) : 0.0,
                    'max' => count($values) > 0 ? max($values) : 100.0
                ];
            }

            foreach ($allSiswa->take(6) as $s) {
                $p = $penilaianMingguIni->where('siswa_id', $s->id_siswa);
                $status = 'Belum'; $kategori = '-';

                if ($p->count() > 0) {
                    $isFinal = $p->where('status', 'final')->count() == $jadwalIds->count();
                    $status = $isFinal ? 'Final' : 'Draft';
                    if ($isFinal) {
                        $weightedSum = 0;
                        foreach ($kriteriaList as $krit) {
                            $wi = (double)$krit->bobot; 
                            $cout = $coutMap[$krit->id_kriteria][$s->id_siswa] ?? 0.0;
                            $pConf = $minMaxMap[$krit->id_kriteria] ?? ['min' => 0, 'max' => 100];
                            
                            if ($pConf['max'] == $pConf['min']) {
                                $ui = 1.0;
                            } else {
                                $ui = ($cout - $pConf['min']) / ($pConf['max'] - $pConf['min']);
                            }
                            $ui = max(0, min(1, $ui));
                            $weightedSum += ($wi * $ui);
                        }
                        $weightedSum = max(0, min(1, $weightedSum));
                        $kategori = $this->matchKategori($weightedSum);
                    }
                }
                $progresPerSiswa[] = ['nama' => $s->name, 'status' => $status, 'kategori' => $kategori];
            }
        }

        $notifikasi = [];
        if (!$periode) $notifikasi[] = ['type' => 'warning', 'pesan' => 'Belum ada periode aktif.'];
        elseif (!$mingguAktif) $notifikasi[] = ['type' => 'info', 'pesan' => 'Tidak ada minggu aktif saat ini.'];

        $distribusi = [
            'BSB' => ['nama' => 'BSB', 'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id_siswa'))->where('nilai_crisp', '>=', 85)->count(), 'badge' => 'badge-emerald', 'progress' => 'progress-green'],
            'BSH' => ['nama' => 'BSH', 'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id_siswa'))->where('nilai_crisp', '>=', 70)->where('nilai_crisp', '<', 85)->count(), 'badge' => 'badge-amber', 'progress' => 'progress-yellow'],
            'MB'  => ['nama' => 'MB', 'count' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id_siswa'))->where('nilai_crisp', '<', 70)->where('nilai_crisp', '>', 0)->count(), 'badge' => 'badge-rose', 'progress' => 'progress-red'],
        ];

        $totalEntri = array_sum(array_column($distribusi, 'count'));
        foreach ($distribusi as $key => $val) {
            $distribusi[$key]['percent'] = $totalEntri > 0 ? round(($val['count'] / $totalEntri) * 100) : 0;
        }

        $latestEvaluasi = collect();
        $finalizedPeriode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        if ($finalizedPeriode) {
            $latestEvaluasi = Evaluasi::with('siswa')
                ->where('periode_id', $finalizedPeriode->id_periode)
                ->whereIn('siswa_id', $allSiswa->pluck('id_siswa'))
                ->orderBy('nilai_akhir', 'desc')
                ->take(5)
                ->get();
        }

        return view('guru.dashboard', compact('user', 'kelas', 'totalSiswa', 'periode', 'mingguAktif', 'terlayaniCount', 'progresPerSiswa', 'distribusi', 'totalProgresPercent', 'semuaMinggu', 'mingguSelesaiCount', 'notifikasi', 'latestEvaluasi', 'finalizedPeriode'));
    }
}
