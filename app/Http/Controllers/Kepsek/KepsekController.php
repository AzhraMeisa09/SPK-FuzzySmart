<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\PeriodePenilaian;
use App\Models\Evaluasi;
use App\Models\PenilaianMingguan;
use App\Models\MingguPenilaian;
use App\Models\Kriteria;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KepsekController extends Controller
{

    private function getDefaultPeriodeId()
    {
        return PeriodePenilaian::where('status', 'final')->latest('finalized_at')->value('id')
            ?? PeriodePenilaian::where('is_aktif', true)->value('id')
            ?? PeriodePenilaian::latest('id')->value('id');
    }

    public function evaluasi(Request $request)
    {
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $periodeAktif = PeriodePenilaian::find($selectedPeriodeId);

        $query = Evaluasi::with(['siswa.kelas', 'detail.subkriteria.kriteria'])
            ->where('periode_id', $selectedPeriodeId);

        if ($request->filled('search')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_akhir', $request->kategori);
        }

        $evaluasi = $query->orderBy('nilai_akhir', 'desc')->paginate(15)->withQueryString();
        
        // Stats for the header
        $allEvaluasi = Evaluasi::where('periode_id', $selectedPeriodeId)->get();
        $stats = [
            'bsb' => $allEvaluasi->where('kategori_akhir', 'BSB')->count(),
            'bsh' => $allEvaluasi->where('kategori_akhir', 'BSH')->count(),
            'mb' => $allEvaluasi->where('kategori_akhir', 'MB')->count(),
            'total' => $allEvaluasi->count()
        ];

        $kelasList = Kelas::all();
        $kriteriaList = Kriteria::all();

        return view('kepsek.evaluasi', compact('evaluasi', 'kelasList', 'periodeAktif', 'stats', 'kriteriaList', 'periodeList', 'selectedPeriodeId'));
    }

    public function siswa(Request $request)
    {
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $periodeAktif = PeriodePenilaian::find($selectedPeriodeId);
        
        $query = Siswa::with(['kelas.guru', 'evaluasi' => function($q) use ($selectedPeriodeId) {
            $q->where('periode_id', $selectedPeriodeId);
        }]);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('kategori')) {
            $query->whereHas('evaluasi', function($q) use ($request, $selectedPeriodeId) {
                $q->where('periode_id', $selectedPeriodeId);
                $q->where('kategori_akhir', $request->kategori);
            });
        }

        $siswa = $query->paginate(15)->withQueryString();
        $kelasList = Kelas::all();

        return view('kepsek.siswa', compact('siswa', 'kelasList', 'periodeAktif', 'periodeList', 'selectedPeriodeId'));
    }

    public function siswaDetail(Request $request, $id)
    {
        $siswa = Siswa::with('kelas.guru')->findOrFail($id);
        
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $periodeAktif = PeriodePenilaian::find($selectedPeriodeId);
        
        $evaluasi = null;
        $kriteriaScores = collect();
        
        if ($selectedPeriodeId) {
            $evaluasi = Evaluasi::with('detail.subkriteria.kriteria')
                ->where('siswa_id', $id)
                ->where('periode_id', $selectedPeriodeId)
                ->first();
                
            if ($evaluasi) {
                $kriteriaScores = $evaluasi->detail->groupBy(fn($item) => $item->subkriteria->kriteria->nama)
                    ->map(fn($items) => round($items->avg('nilai_crisp'), 1));
            }
        }

        if ($kriteriaScores->isEmpty() && $selectedPeriodeId) {
            $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])
                ->where('siswa_id', $id)
                ->whereHas('jadwalSubkriteria.minggu', fn($q) => $q->where('periode_id', $selectedPeriodeId))
                ->get();

            $kriteriaScores = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria->kriteria->nama)
                ->map(fn($items) => round($items->avg('nilai_crisp'), 1));
        }

        // Hitung Rata-rata Sekolah untuk Perbandingan (Radar Chart)
        $schoolAverages = collect();
        if ($selectedPeriodeId) {
            $schoolAverages = DB::table('detail_evaluasi')
                ->join('subkriteria', 'detail_evaluasi.subkriteria_id', '=', 'subkriteria.id')
                ->join('kriteria', 'subkriteria.kriteria_id', '=', 'kriteria.id')
                ->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')
                ->where('evaluasi.periode_id', $selectedPeriodeId)
                ->groupBy('kriteria.nama')
                ->select('kriteria.nama', DB::raw('AVG(nilai_crisp) as avg'))
                ->pluck('avg', 'nama')
                ->map(fn($val) => round($val, 1));
                
            if ($schoolAverages->isEmpty()) {
                $schoolAverages = DB::table('penilaian_mingguan')
                    ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
                    ->join('subkriteria', 'jadwal_subkriteria.subkriteria_id', '=', 'subkriteria.id')
                    ->join('kriteria', 'subkriteria.kriteria_id', '=', 'kriteria.id')
                    ->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')
                    ->where('minggu_penilaian.periode_id', $selectedPeriodeId)
                    ->groupBy('kriteria.nama')
                    ->select('kriteria.nama', DB::raw('AVG(nilai_crisp) as avg'))
                    ->pluck('avg', 'nama')
                    ->map(fn($val) => round($val, 1));
            }
        }

        $portofolio = \App\Models\Portofolio::with('images', 'minggu')
            ->where('siswa_id', $id)
            ->whereHas('minggu', fn($q) => $q->where('periode_id', $selectedPeriodeId))
            ->latest()
            ->get();

        return view('kepsek.siswa_detail', compact('siswa', 'evaluasi', 'kriteriaScores', 'schoolAverages', 'portofolio', 'periodeAktif', 'periodeList', 'selectedPeriodeId'));
    }

    public function perkembangan()
    {
        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->first() 
                      ?? PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first()
                      ?? PeriodePenilaian::whereHas('minggu.jadwalSubkriteria.penilaian')->latest('id')->first()
                      ?? PeriodePenilaian::latest()->first();
                      
        $kriteriaList = Kriteria::all();
        
        $siswaData = [];
        $trendData = [];
        $trendLabels = [];
        
        if ($periodeAktif) {
            $evaluasiList = Evaluasi::with(['siswa.kelas', 'detail.subkriteria.kriteria'])
                ->where('periode_id', $periodeAktif->id)
                ->get();

            foreach ($evaluasiList as $eval) {
                $row = [
                    'nama' => $eval->siswa->nama,
                    'kelas' => $eval->siswa->kelas->nama_kelas ?? '—',
                    'nilai_akhir' => $eval->nilai_akhir * 100,
                    'kategori' => $eval->kategori_akhir,
                    'kriteria' => []
                ];

                foreach ($kriteriaList as $k) {
                    $avg = $eval->detail->filter(fn($d) => $d->subkriteria->kriteria_id == $k->id)->avg('nilai_crisp');
                    $row['kriteria'][$k->nama] = $avg ? round($avg, 1) : 0;
                }
                $siswaData[] = $row;
            }

            // Data Tren Mingguan (Real)
            $mingguList = MingguPenilaian::where('periode_id', $periodeAktif->id)
                ->orderBy('minggu_ke', 'asc')
                ->get();

            foreach ($mingguList as $m) {
                $avg = DB::table('penilaian_mingguan')
                    ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
                    ->where('jadwal_subkriteria.minggu_id', $m->id)
                    ->avg('nilai_crisp');
                
                if ($avg !== null) {
                    $trendLabels[] = "Minggu " . $m->minggu_ke;
                    $trendData[] = round($avg, 1);
                }
            }

            // Matrix Nilai Mingguan per Siswa
            $penilaianAll = PenilaianMingguan::with('jadwalSubkriteria.minggu')
                ->whereHas('jadwalSubkriteria.minggu', fn($q) => $q->where('periode_id', $periodeAktif->id))
                ->get();

            $weeklyMatrix = [];
            foreach ($penilaianAll as $p) {
                $siswaId = $p->siswa_id;
                $mingguKe = $p->jadwalSubkriteria->minggu->minggu_ke;
                if (!isset($weeklyMatrix[$siswaId][$mingguKe])) $weeklyMatrix[$siswaId][$mingguKe] = [];
                $weeklyMatrix[$siswaId][$mingguKe][] = $p->nilai_crisp;
            }

            // Average weekly scores per student
            foreach ($weeklyMatrix as $sId => $weeks) {
                foreach ($weeks as $mKe => $scores) {
                    $weeklyMatrix[$sId][$mKe] = round(collect($scores)->avg(), 1);
                }
            }

            // Map matrix back to siswaData
            foreach ($siswaData as $index => $row) {
                $sId = $evaluasiList[$index]->siswa_id;
                $siswaData[$index]['mingguan'] = $weeklyMatrix[$sId] ?? [];
            }
        }

        return view('kepsek.perkembangan', compact('siswaData', 'kriteriaList', 'periodeAktif', 'trendData', 'trendLabels'));
    }    public function dashboard(Request $request)
    {
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $periodeAktif = PeriodePenilaian::find($selectedPeriodeId);

        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        
        $distribusiKategori = ['BSB' => 0, 'BSH' => 0, 'MB' => 0, 'Belum Dinilai' => $totalSiswa];
        $rataRataSekolah = 0;
        $kriteriaAverages = [];
        $insights = [];
        
        if ($periodeAktif) {
            // Ambil data evaluasi final
            $evaluasi = Evaluasi::where('periode_id', $selectedPeriodeId)->get();
            $siswaIdsDinilai = $evaluasi->pluck('siswa_id')->toArray();
            
            // Ambil data mingguan sebagai fallback
            $mingguanFallback = DB::table('penilaian_mingguan')
                ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
                ->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')
                ->where('minggu_penilaian.periode_id', $selectedPeriodeId)
                ->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)
                ->select('penilaian_mingguan.siswa_id', DB::raw('AVG(nilai_crisp) as avg_score'))
                ->groupBy('penilaian_mingguan.siswa_id')
                ->get();

            foreach ($evaluasi as $e) {
                $distribusiKategori[$e->kategori_akhir]++;
            }
            foreach ($mingguanFallback as $m) {
                $kat = \App\Models\KategoriNilai::findByNilai((float)$m->avg_score);
                $distribusiKategori[$kat ? $kat->nama : 'MB']++;
            }
            
            $totalTerdata = $evaluasi->count() + $mingguanFallback->count();
            $distribusiKategori['Belum Dinilai'] = max(0, $totalSiswa - $totalTerdata);
            
            $totalScore = $evaluasi->sum(fn($e) => $e->nilai_akhir * 100) + $mingguanFallback->sum('avg_score');
            $rataRataSekolah = $totalTerdata > 0 ? $totalScore / $totalTerdata : 0;

            $kriteriaList = Kriteria::all();
            foreach ($kriteriaList as $k) {
                $sumFinal = DB::table('detail_evaluasi')
                    ->join('subkriteria', 'detail_evaluasi.subkriteria_id', '=', 'subkriteria.id')
                    ->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')
                    ->where('evaluasi.periode_id', $selectedPeriodeId)
                    ->where('subkriteria.kriteria_id', $k->id)
                    ->sum('nilai_crisp');
                $countFinal = DB::table('detail_evaluasi')
                    ->join('subkriteria', 'detail_evaluasi.subkriteria_id', '=', 'subkriteria.id')
                    ->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')
                    ->where('evaluasi.periode_id', $selectedPeriodeId)
                    ->where('subkriteria.kriteria_id', $k->id)
                    ->count();

                $sumLive = DB::table('penilaian_mingguan')
                    ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
                    ->join('subkriteria', 'jadwal_subkriteria.subkriteria_id', '=', 'subkriteria.id')
                    ->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')
                    ->where('minggu_penilaian.periode_id', $selectedPeriodeId)
                    ->where('subkriteria.kriteria_id', $k->id)
                    ->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)
                    ->sum('nilai_crisp');
                $countLive = DB::table('penilaian_mingguan')
                    ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
                    ->join('subkriteria', 'jadwal_subkriteria.subkriteria_id', '=', 'subkriteria.id')
                    ->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')
                    ->where('minggu_penilaian.periode_id', $selectedPeriodeId)
                    ->where('subkriteria.kriteria_id', $k->id)
                    ->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)
                    ->count();

                $totalSum = $sumFinal + $sumLive;
                $totalCount = $countFinal + $countLive;
                
                if ($totalCount > 0) {
                    $kriteriaAverages[$k->nama] = round($totalSum / $totalCount, 1);
                }
            }

            $validStats = collect($distribusiKategori)->except('Belum Dinilai');
            if ($validStats->sum() > 0) {
                $dominantKat = $validStats->sortDesc()->keys()->first();
                $insights[] = "Mayoritas siswa (" . round(($validStats[$dominantKat] / $validStats->sum()) * 100) . "%) berada pada kategori $dominantKat.";
            }
            
            if (!empty($kriteriaAverages)) {
                asort($kriteriaAverages);
                $lowestKrit = array_key_first($kriteriaAverages);
                $insights[] = "Aspek $lowestKrit memerlukan perhatian lebih dengan rata-rata " . $kriteriaAverages[$lowestKrit] . "%.";
                arsort($kriteriaAverages);
                $highestKrit = array_key_first($kriteriaAverages);
                $insights[] = "Kekuatan utama sekolah saat ini ada pada aspek $highestKrit (" . $kriteriaAverages[$highestKrit] . "%).";
            }
        }

        return view('kepsek.dashboard', compact(
            'totalSiswa', 'totalKelas', 'distribusiKategori', 
            'rataRataSekolah', 'periodeAktif', 'kriteriaAverages', 'insights', 'periodeList', 'selectedPeriodeId'
        ));
    }

    public function analisis(Request $request)
    {
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $periodeAktif = PeriodePenilaian::find($selectedPeriodeId);
                       
        if (!$periodeAktif) return view('kepsek.analisis', [
            'periodeAktif' => null,
            'topSiswa' => collect(),
            'bottomSiswa' => collect(),
            'kriteriaStats' => [],
            'subkriteriaStats' => collect(),
            'kelasStats' => collect(),
            'insights' => [],
            'periodeList' => $periodeList,
            'selectedPeriodeId' => $selectedPeriodeId
        ]);

        $evaluasi = Evaluasi::where('periode_id', $selectedPeriodeId)->get();
        $siswaIdsDinilai = $evaluasi->pluck('siswa_id')->toArray();
        
        $mingguanData = DB::table('penilaian_mingguan')
            ->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')
            ->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')
            ->where('minggu_penilaian.periode_id', $selectedPeriodeId)
            ->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)
            ->select('penilaian_mingguan.siswa_id', DB::raw('AVG(nilai_crisp) as avg_score'))
            ->groupBy('penilaian_mingguan.siswa_id')
            ->get();

        $allPerformance = collect();
        foreach ($evaluasi as $e) {
            $allPerformance->push((object)['siswa_id' => $e->siswa_id, 'nilai_akhir' => (float)$e->nilai_akhir, 'is_final' => true]);
        }
        foreach ($mingguanData as $m) {
            $allPerformance->push((object)['siswa_id' => $m->siswa_id, 'nilai_akhir' => (float)$m->avg_score / 100, 'is_final' => false]);
        }

        $allPerformance = $allPerformance->sortByDesc('nilai_akhir');
        
        $topSiswa = $allPerformance->take(10)->map(function($p) {
            return (object)['nilai_akhir' => $p->nilai_akhir, 'siswa' => Siswa::with('kelas')->find($p->siswa_id)];
        })->filter(fn($i) => $i->siswa != null);

        $bottomSiswa = $allPerformance->reverse()->take(10)->map(function($p) {
            return (object)['nilai_akhir' => $p->nilai_akhir, 'siswa' => Siswa::with('kelas')->find($p->siswa_id)];
        })->filter(fn($i) => $i->siswa != null);

        $kriteriaList = Kriteria::all();
        $kriteriaStats = [];
        foreach ($kriteriaList as $k) {
            $sumFinal = DB::table('detail_evaluasi')->join('subkriteria', 'detail_evaluasi.subkriteria_id', '=', 'subkriteria.id')->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')->where('evaluasi.periode_id', $selectedPeriodeId)->where('subkriteria.kriteria_id', $k->id)->sum('nilai_crisp');
            $countFinal = DB::table('detail_evaluasi')->join('subkriteria', 'detail_evaluasi.subkriteria_id', '=', 'subkriteria.id')->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')->where('evaluasi.periode_id', $selectedPeriodeId)->where('subkriteria.kriteria_id', $k->id)->count();

            $sumLive = DB::table('penilaian_mingguan')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('subkriteria', 'jadwal_subkriteria.subkriteria_id', '=', 'subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('subkriteria.kriteria_id', $k->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->sum('nilai_crisp');
            $countLive = DB::table('penilaian_mingguan')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('subkriteria', 'jadwal_subkriteria.subkriteria_id', '=', 'subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('subkriteria.kriteria_id', $k->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->count();

            $totalCount = $countFinal + $countLive;
            $kriteriaStats[$k->nama] = $totalCount > 0 ? round(($sumFinal + $sumLive) / $totalCount, 1) : 0;
        }

        $subkriteriaStats = collect();
        $allSub = DB::table('subkriteria')->select('id', 'nama')->get();
        foreach ($allSub as $s) {
            $sumFinal = DB::table('detail_evaluasi')->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')->where('evaluasi.periode_id', $selectedPeriodeId)->where('detail_evaluasi.subkriteria_id', $s->id)->sum('nilai_crisp');
            $countFinal = DB::table('detail_evaluasi')->join('evaluasi', 'detail_evaluasi.evaluasi_id', '=', 'evaluasi.id')->where('evaluasi.periode_id', $selectedPeriodeId)->where('detail_evaluasi.subkriteria_id', $s->id)->count();

            $sumLive = DB::table('penilaian_mingguan')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('jadwal_subkriteria.subkriteria_id', $s->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->sum('nilai_crisp');
            $countLive = DB::table('penilaian_mingguan')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('jadwal_subkriteria.subkriteria_id', $s->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->count();

            $totalCount = $countFinal + $countLive;
            if ($totalCount > 0) {
                $subkriteriaStats->push((object)['nama' => $s->nama, 'avg' => round(($sumFinal + $sumLive) / $totalCount, 1)]);
            }
        }
        $subkriteriaStats = $subkriteriaStats->sortBy('avg');

        $kelasStats = Kelas::all()->map(function($kelas) use ($selectedPeriodeId, $siswaIdsDinilai) {
            $sumFinal = Evaluasi::where('periode_id', $selectedPeriodeId)->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelas->id))->sum('nilai_akhir');
            $countFinal = Evaluasi::where('periode_id', $selectedPeriodeId)->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelas->id))->count();

            $sumLive = DB::table('penilaian_mingguan')->join('siswa', 'penilaian_mingguan.siswa_id', '=', 'siswa.id')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('siswa.kelas_id', $kelas->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->avg('nilai_crisp');
            $countLive = DB::table('penilaian_mingguan')->join('siswa', 'penilaian_mingguan.siswa_id', '=', 'siswa.id')->join('jadwal_subkriteria', 'penilaian_mingguan.jadwal_sub_id', '=', 'jadwal_subkriteria.id')->join('minggu_penilaian', 'jadwal_subkriteria.minggu_id', '=', 'minggu_penilaian.id')->where('minggu_penilaian.periode_id', $selectedPeriodeId)->where('siswa.kelas_id', $kelas->id)->whereNotIn('penilaian_mingguan.siswa_id', $siswaIdsDinilai)->distinct('penilaian_mingguan.siswa_id')->count('penilaian_mingguan.siswa_id');

            $totalCount = $countFinal + $countLive;
            $avg = $totalCount > 0 ? (($sumFinal * 100) + ($sumLive * $countLive)) / $totalCount : 0;
            return ['nama' => $kelas->nama_kelas, 'avg' => round($avg, 1)];
        })->sortByDesc('avg');

        $insights = [];
        if (!empty($kriteriaStats)) {
            $filteredKrit = array_filter($kriteriaStats, fn($v) => $v > 0);
            if (!empty($filteredKrit)) {
                asort($filteredKrit);
                $lowestKrit = array_key_first($filteredKrit);
                $insights[] = "Secara agregat, indikator $lowestKrit memerlukan penguatan di tingkat sekolah.";
            }
        }
        
        if ($kelasStats->isNotEmpty()) {
            $bestKelas = $kelasStats->first();
            $insights[] = "Kelas " . $bestKelas['nama'] . " menunjukkan konsistensi performa terbaik saat ini.";
        }

        return view('kepsek.analisis', compact('topSiswa', 'bottomSiswa', 'kriteriaStats', 'subkriteriaStats', 'kelasStats', 'insights', 'periodeAktif', 'periodeList', 'selectedPeriodeId'));
    }


    public function laporan(Request $request)
    {
        $periodeList = PeriodePenilaian::orderBy('id', 'desc')->get();
        $kelasList = Kelas::all();
        
        $selectedPeriodeId = $request->get('periode_id');
        
        if (!$selectedPeriodeId) {
            $selectedPeriodeId = $this->getDefaultPeriodeId();
        }

        $selectedKelasId = $request->get('kelas_id');

        // Ambil daftar siswa yang relevan
        $siswaQuery = Siswa::with('kelas');
        if ($selectedKelasId) {
            $siswaQuery->where('kelas_id', $selectedKelasId);
        } else if ($selectedPeriodeId) {
            $periode = PeriodePenilaian::with('kelas')->find($selectedPeriodeId);
            if ($periode && $periode->kelas->isNotEmpty()) {
                $siswaQuery->whereIn('kelas_id', $periode->kelas->pluck('id'));
            }
        }
        $siswaList = $siswaQuery->get();

        // Ambil data evaluasi final yang sudah ada
        $evaluasiFinal = Evaluasi::with(['siswa.kelas', 'detail.subkriteria.kriteria'])
            ->where('periode_id', $selectedPeriodeId)
            ->get()
            ->keyBy('siswa_id');

        // Bangun list evaluasi gabungan (Final + Live Fallback)
        $evaluasi = $siswaList->map(function($s) use ($selectedPeriodeId, $evaluasiFinal) {
            if ($evaluasiFinal->has($s->id)) {
                return $evaluasiFinal->get($s->id);
            }

            // Fallback ke live assessment
            $avg = \App\Models\PenilaianMingguan::where('siswa_id', $s->id)
                ->whereHas('jadwalSubkriteria.minggu', function($q) use ($selectedPeriodeId) {
                    $q->where('periode_id', $selectedPeriodeId);
                })
                ->avg('nilai_crisp');
            
            $kat = \App\Models\KategoriNilai::findByNilai((float)$avg);
            
            return (object)[
                'id' => null,
                'siswa_id' => $s->id,
                'siswa' => $s,
                'nilai_akhir' => $avg ? (float)$avg / 100 : 0,
                'kategori_akhir' => $kat ? $kat->nama : 'MB',
                'is_draft' => true
            ];
        });

        // Urutkan berdasarkan nilai akhir
        $evaluasi = $evaluasi->sortByDesc('nilai_akhir');

        return view('kepsek.laporan', compact('periodeList', 'kelasList', 'evaluasi', 'selectedPeriodeId', 'selectedKelasId'));
    }

    public function generateWordReport(Request $request)
    {
        // This remains largely the same but ensures consistency
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'periode_id' => 'required|exists:periode_penilaian,id',
        ]);

        $reportData = $this->getReportData($request->siswa_id);
        if (!$reportData) return back()->with('error', 'Siswa tidak ditemukan.');

        try {
            $templatePath = storage_path('app/public/templates/template_laporan.docx');
            if (!file_exists($templatePath)) return back()->with('error', 'Template default tidak ditemukan.');

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            $semester = $reportData['evaluasi']->periode->semester ?? $reportData['active_periode']->semester ?? '—';
            $guruName = $reportData['siswa']->kelas->guru->first()->nama_lengkap ?? '—';

            $templateProcessor->setValues([
                'NAMA_SISWA'     => $reportData['siswa']->nama,
                'NISN'           => $reportData['siswa']->kode ?: '—',
                'KELAS'          => $reportData['siswa']->kelas->nama_kelas ?? '—',
                'SEMESTER'       => $semester,
                'NILAI_AKHIR'    => $reportData['final_score'],
                'KATEGORI_AKHIR' => $reportData['final_kategori'],
                'REKOMENDASI'    => $reportData['evaluasi']->rekomendasi ?? '—',
                'CATATAN_GURU'   => $reportData['evaluasi']->catatan_guru ?? '—',
                'GURU_NAME'      => $guruName, 
                'TANGGAL'        => now()->translatedFormat('d F Y'),
                'TAHUN_AJARAN'   => $reportData['siswa']->kelas->tahunAjaran->nama ?? '—',
            ]);

            if ($reportData['siswa']->foto && file_exists(storage_path('app/public/' . $reportData['siswa']->foto))) {
                $templateProcessor->setImageValue('FOTO', ['path' => storage_path('app/public/' . $reportData['siswa']->foto), 'width' => 100, 'height' => 120, 'ratio' => true]);
            } else {
                $templateProcessor->setValue('FOTO', '(Belum ada foto)');
            }

            if (count($reportData['kriteria']) > 0) {
                $templateProcessor->cloneRow('KRIT_KODE', count($reportData['kriteria']));
                foreach ($reportData['kriteria'] as $index => $k) {
                    $i = $index + 1;
                    $templateProcessor->setValue("KRIT_KODE#$i", $k['kode']);
                    $templateProcessor->setValue("KRIT_NAMA#$i", $k['nama']);
                    $templateProcessor->setValue("KRIT_SKOR#$i", $k['avg'] . '%');
                    $templateProcessor->setValue("KRIT_KAT#$i",  $k['kategori']);
                }
            } else {
                $templateProcessor->setValues(['KRIT_KODE' => '-', 'KRIT_NAMA' => '(Belum ada penilaian)', 'KRIT_SKOR' => '0%', 'KRIT_KAT' => 'MB']);
            }

            if ($reportData['detail_evaluasi']->count() > 0) {
                $templateProcessor->cloneRow('SUB_KODE', count($reportData['detail_evaluasi']));
                foreach ($reportData['detail_evaluasi'] as $index => $det) {
                    $i = $index + 1;
                    $templateProcessor->setValue("SUB_KODE#$i", $det->subkriteria->kode);
                    $templateProcessor->setValue("SUB_NAMA#$i", $det->subkriteria->nama);
                    $templateProcessor->setValue("SUB_KAT#$i",  $det->kategori ?? '—');
                    $templateProcessor->setValue("SUB_CAT#$i",  $det->rekomendasi_detail ?? '—');
                }
            } else {
                $templateProcessor->setValues(['SUB_KODE' => '-', 'SUB_NAMA' => '(Belum ada data)', 'SUB_KAT' => 'MB', 'SUB_CAT' => '—']);
            }

            $allEntries = [];
            foreach ($reportData['portofolio_list'] as $porto) {
                if ($porto->images->isEmpty()) {
                    $allEntries[] = ['minggu' => $porto->minggu ? "Minggu Ke-".$porto->minggu->minggu_ke : '—', 'judul' => $porto->judul, 'deskripsi' => $porto->deskripsi, 'path' => null];
                } else {
                    foreach ($porto->images as $img) {
                        $allEntries[] = ['minggu' => $porto->minggu ? "Minggu Ke-".$porto->minggu->minggu_ke : '—', 'judul' => $porto->judul, 'deskripsi' => $porto->deskripsi, 'path' => $img->file_path];
                    }
                }
            }

            if (count($allEntries) > 0) {
                $templateProcessor->cloneRow('PORTO_MINGGU', count($allEntries));
                foreach ($allEntries as $index => $data) {
                    $i = $index + 1;
                    $templateProcessor->setValue("PORTO_MINGGU#$i", $data['minggu']);
                    $templateProcessor->setValue("PORTO_JUDUL#$i", $data['judul']);
                    $templateProcessor->setValue("PORTO_DESKRIPSI#$i", $data['deskripsi']);
                    if ($data['path']) {
                        $fullPath = storage_path('app/public/' . $data['path']);
                        if (file_exists($fullPath) && !in_array(strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)), ['mp4', 'mov', 'webm'])) {
                            $templateProcessor->setImageValue("PORTO_IMAGE#$i", ['path' => $fullPath, 'width' => 150, 'height' => 150, 'ratio' => true]);
                        } else {
                            $templateProcessor->setValue("PORTO_IMAGE#$i", '(Media Video/Missing)');
                        }
                    } else {
                        $templateProcessor->setValue("PORTO_IMAGE#$i", '(Tanpa Foto)');
                    }
                }
            } else {
                $templateProcessor->setValues(['PORTO_MINGGU' => '-', 'PORTO_JUDUL' => '(Kosong)', 'PORTO_DESKRIPSI' => '-', 'PORTO_IMAGE' => '-']);
            }

            $fileName = 'Laporan_Kepsek_' . str_replace(' ', '_', $reportData['siswa']->nama) . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $templateProcessor->saveAs($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate Word: ' . $e->getMessage());
        }
    }

    private function getReportData($selectedSiswaId)
    {
        $activeSiswa = Siswa::with(['kelas.tahunAjaran'])->find($selectedSiswaId);
        if (!$activeSiswa) return null;

        $evaluasi = Evaluasi::with('periode')->where('siswa_id', $selectedSiswaId)->where('is_final', true)->latest('id')->first();
        $activePeriode = PeriodePenilaian::where('is_aktif', true)->first();
        
        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])
            ->where('siswa_id', $selectedSiswaId)
            ->get();
        
        // Helper untuk penentuan kategori
        $matchKategori = function($nilai) {
            $k = \App\Models\KategoriNilai::where('nilai_l', '<=', (float)$nilai)->where('nilai_u', '>=', (float)$nilai)->first();
            return $k ? $k->nama : ($nilai >= 85 ? 'BSB' : ($nilai >= 70 ? 'BSH' : 'MB'));
        };

        $kriteriaScores = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria->kriteria_id)->map(function ($items) use ($matchKategori) {
            $first = $items->first()->jadwalSubkriteria->subkriteria->kriteria;
            $avg = $items->avg('nilai_crisp');
            return ['kode' => $first->kode, 'nama' => $first->nama, 'avg' => round($avg, 1), 'kategori' => $matchKategori($avg)];
        })->values();

        $subDetailsFallback = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria_id)->map(function ($items) use ($matchKategori) {
            $first = $items->first();
            $sub = $first->jadwalSubkriteria->subkriteria;
            $avg = $items->avg('nilai_crisp');
            return [
                'subkriteria' => (object)['kode' => $sub->kode, 'nama' => $sub->nama],
                'kategori' => $matchKategori($avg),
                'rekomendasi_detail' => $items->whereNotNull('catatan')->pluck('catatan')->filter()->first() ?? '-'
            ];
        })->values();
        
        $detailEvaluasi = $evaluasi ? \App\Models\DetailEvaluasi::with('subkriteria.kriteria')->where('evaluasi_id', $evaluasi->id)->get() : collect();
        
        // Jika belum ada detail evaluasi SPK, gunakan fallback dari penilaian mingguan
        if ($detailEvaluasi->isEmpty() && $subDetailsFallback->isNotEmpty()) {
            $detailEvaluasi = $subDetailsFallback->map(fn($s) => (object)$s);
        }

        $detailEvaluasiGrouped = $detailEvaluasi->groupBy(fn($item) => $item->subkriteria->kriteria->nama ?? 'Lainnya');
        $portofolioList = \App\Models\Portofolio::with(['images', 'minggu'])->where('siswa_id', $selectedSiswaId)->get();
        
        return [
            'siswa' => $activeSiswa, 
            'kriteria' => $kriteriaScores, 
            'detail_evaluasi' => $detailEvaluasi,
            'detail_evaluasi_grouped' => $detailEvaluasiGrouped, 
            'portofolio_list' => $portofolioList, 
            'evaluasi' => $evaluasi, 
            'active_periode' => $activePeriode,
            'final_score' => $evaluasi ? round($evaluasi->nilai_akhir * 100, 1) : round($kriteriaScores->avg('avg'), 1), 
            'final_kategori' => $evaluasi ? $evaluasi->kategori_akhir : $matchKategori($kriteriaScores->avg('avg')), 
        ];
    }
}
