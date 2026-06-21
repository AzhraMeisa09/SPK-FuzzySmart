<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\PeriodePenilaian;
use App\Models\MingguPenilaian;
use App\Models\PenilaianMingguan;
use App\Models\Evaluasi;
use App\Models\KategoriNilai;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use App\Models\DetailEvaluasi;
use App\Models\TemplateRekomendasiUmum;
use App\Models\Portofolio;
use Illuminate\Support\Facades\Auth;

class WaliController extends Controller
{
    /**
     * Tambah Anak dari Dashboard Wali
     */
    public function tambahAnak(Request $request)
    {
        $request->validate([
            'nisn'            => 'required|string|max:10',
            'kode_registrasi' => 'required|string|max:10',
        ], [
            'nisn.required'            => 'NISN wajib diisi.',
            'kode_registrasi.required' => 'Kode registrasi wajib diisi.',
        ]);

        // Cari siswa berdasarkan NISN DAN kode registrasi (keduanya harus cocok)
        $siswa = Siswa::where(function($q) use ($request) {
                        $q->where('kode', $request->nisn)
                          ->orWhere('id_siswa', $request->nisn);
                    })
                    ->where('kode_registrasi', strtoupper(trim($request->kode_registrasi)))
                    ->first();

        if (!$siswa) {
            return back()->with('error', 'NISN atau kode registrasi tidak valid. Pastikan keduanya sudah benar.');
        }

        if ($siswa->wali_murid_id !== null) {
            return back()->with('error', 'Siswa tersebut sudah terhubung dengan akun wali murid lain.');
        }

        $user = Auth::user();

        // Update relasi
        $siswa->update([
            'wali_murid_id' => $user->id_user
        ]);

        $siswa->wali()->syncWithoutDetaching([$user->id_user]);

        return back()->with('success', 'Berhasil menambahkan data anak: ' . $siswa->name . '.');
    }


    /**
     * Dashboard Wali Murid
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with(['kelas.guru'])->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $selectedAnak = $anak->where('id_siswa', $siswaId)->first();
        
        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->first();
        
        $penilaianTerbaru = collect();
        $evaluasiTerakhir = null;

        if ($selectedAnak) {
            $penilaianTerbaru = PenilaianMingguan::where('siswa_id', $selectedAnak->id_siswa)
                ->where('status', 'final')
                ->with(['jadwalSubkriteria.subkriteria', 'jadwalSubkriteria.minggu', 'kategori'])
                ->latest()
                ->take(5)
                ->get();

            // 1. Ambil evaluasi terakhir yang sudah final (dari periode mana saja)
            $evaluasiTerakhir = Evaluasi::where('siswa_id', $selectedAnak->id_siswa)
                ->whereHas('periode', fn($q) => $q->where('status', 'final'))
                ->latest()
                ->first();

            // 2. Jika belum ada evaluasi final, hitung data "Live" dari penilaian mingguan
            if (!$evaluasiTerakhir) {
                $allFinalPenilaian = PenilaianMingguan::where('siswa_id', $selectedAnak->id_siswa)
                    ->where('status', 'final')
                    ->get();
                
                if ($allFinalPenilaian->isNotEmpty()) {
                    $avgLive = $allFinalPenilaian->avg('nilai_crisp');
                    $katLive = $avgLive >= 85 ? 'BSB' : ($avgLive >= 70 ? 'BSH' : 'MB');
                    
                    // Mock object evaluasi untuk tampilan dashboard
                    $evaluasiTerakhir = (object)[
                        'nilai_akhir' => $avgLive / 100, // normalize to 0.0-1.0
                        'kategori_akhir' => $katLive,
                        'is_live' => true
                    ];
                }
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
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $siswa = $anak->where('id_siswa', $siswaId)->first();
        $selectedAnak = $siswa; // for backward compatibility in layout if needed

        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        // Fetch all periods related to the student's class (active, proses, or final)
        $listPeriode = PeriodePenilaian::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id_kelas', $siswa->kelas_id);
            })
            ->whereIn('status', [PeriodePenilaian::STATUS_AKTIF, PeriodePenilaian::STATUS_PROSES, PeriodePenilaian::STATUS_FINAL])
            ->orderBy('created_at', 'desc')
            ->get();

        $periodeId = $request->get('periode_id');
        $periode = null;

        if ($periodeId) {
            $periode = PeriodePenilaian::find($periodeId);
        }

        if (!$periode) {
            // Default to active period, or the first/latest period
            $periode = $listPeriode->where('status', PeriodePenilaian::STATUS_AKTIF)->first() ?? $listPeriode->first();
        }

        $penilaian = collect();
        $portofolios = collect();

        if ($periode) {
            $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'kategori', 'jadwalSubkriteria.minggu'])
                ->where('siswa_id', $siswa->id_siswa)
                ->where('status', 'final')
                ->whereHas('jadwalSubkriteria.minggu', function($q) use ($periode) {
                    $q->where('periode_id', $periode->id_periode);
                })
                ->get();

            $portofolios = \App\Models\Portofolio::with('images', 'minggu')
                ->where('siswa_id', $siswa->id_siswa)
                ->whereHas('minggu', function($q) use ($periode) {
                    $q->where('periode_id', $periode->id_periode);
                })
                ->get();
        }

        // Ambil semua ID minggu yang terlibat (baik dari penilaian maupun portofolio)
        $allMingguIds = $penilaian->pluck('jadwalSubkriteria.minggu_id')
            ->merge($portofolios->pluck('minggu_id'))
            ->unique()
            ->filter();

        $mingguList = MingguPenilaian::whereIn('id_minggu', $allMingguIds)
            ->orderBy('minggu_ke', 'asc')
            ->get();

        $penilaianGrouped = $penilaian->groupBy('jadwalSubkriteria.minggu_id');
        $portofolioGrouped = $portofolios->groupBy('minggu_id');

        $mingguGrouped = $mingguList->map(function ($minggu) use ($penilaianGrouped, $portofolioGrouped) {
            $items = $penilaianGrouped->get($minggu->id_minggu, collect());
            $weekPortos = $portofolioGrouped->get($minggu->id_minggu, collect());
            
            $avgCrisp = $items->count() > 0 ? $items->sum('nilai_crisp') / $items->count() : 0;
            
            $kategori = '-';
            if ($avgCrisp >= 80) $kategori = 'BSB';
            elseif ($avgCrisp >= 60) $kategori = 'BSH';
            elseif ($avgCrisp > 0) $kategori = 'MB';
            
            $subDetails = $items->map(function ($item) {
                $sub = $item->jadwalSubkriteria->subkriteria;
                return [
                    'id_subkriteria' => $sub->id_subkriteria,
                    'subkriteria' => $sub->nama_subkriteria,
                    'kriteria' => $sub->kriteria->nama_kriteria ?? '-',
                    'kategori' => $item->kategori->nama ?? '-',
                    'capaian' => round($item->nilai_crisp, 1) . '%',
                    'catatan' => $item->catatan,
                    'col' => ($item->kategori->nama ?? '') === 'BSB' ? 'emerald' : (($item->kategori->nama ?? '') === 'BSH' ? 'amber' : 'rose')
                ];
            });
            
            return [
                'minggu_ke' => $minggu->minggu_ke,
                'tema' => $minggu->tema ?? '-',
                'tanggal' => $minggu->updated_at->format('d M Y'),
                'avg' => round($avgCrisp, 2),
                'kategori' => $kategori,
                'status' => $minggu->status,
                'col' => $kategori === 'BSB' ? 'emerald' : ($kategori === 'BSH' ? 'amber' : 'rose'),
                'details' => $subDetails,
                'portofolios' => $weekPortos
            ];
        })->values();

        // Hitung statistik global
        $divisor = $mingguGrouped->count();
        $sumAvg = $mingguGrouped->sum('avg');
        $avgTotal = $divisor > 0 ? $sumAvg / $divisor : 0;
        
        $finalKat = '-';
        if ($avgTotal >= 80) $finalKat = 'BSB';
        elseif ($avgTotal >= 60) $finalKat = 'BSH';
        elseif ($avgTotal > 0) $finalKat = 'MB';
        
        $bsbCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'BSB')->count();
        $bshCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'BSH')->count();
        $mbCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'MB')->count();

        return view('wali.perkembangan', compact(
            'siswa', 'selectedAnak', 'anak', 'mingguGrouped', 'divisor', 'avgTotal', 'finalKat', 'bsbCount', 'bshCount', 'mbCount', 'periode', 'listPeriode'
        ));
    }

    /**
     * Portofolio Anak
     */
    public function portofolio(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $selectedAnak = $anak->where('id_siswa', $siswaId)->first();

        $listPeriode = collect();
        $periode = null;
        $mingguId = $request->get('minggu_id');
        $mingguList = collect();
        $portofolio_list = collect();

        if ($selectedAnak) {
            // Fetch all periods related to the student's class (active, proses, or final)
            $listPeriode = PeriodePenilaian::whereHas('kelas', function($q) use ($selectedAnak) {
                    $q->where('kelas.id_kelas', $selectedAnak->kelas_id);
                })
                ->whereIn('status', [PeriodePenilaian::STATUS_AKTIF, PeriodePenilaian::STATUS_PROSES, PeriodePenilaian::STATUS_FINAL])
                ->orderBy('created_at', 'desc')
                ->get();

            $periodeId = $request->get('periode_id');
            if ($periodeId) {
                $periode = PeriodePenilaian::find($periodeId);
            }

            if (!$periode) {
                // Default to active period, or the first/latest period
                $periode = $listPeriode->where('status', PeriodePenilaian::STATUS_AKTIF)->first() ?? $listPeriode->first();
            }

            if ($periode) {
                // Populate filter minggu dari periode terpilih
                $mingguList = MingguPenilaian::where('periode_id', $periode->id_periode)
                    ->where('status', 'selesai')
                    ->orderBy('minggu_ke', 'desc')
                    ->get();

                if ($mingguId && !$mingguList->contains('id_minggu', $mingguId)) {
                    $mingguId = null;
                }

                $query = \App\Models\Portofolio::with(['images', 'minggu.periode'])
                    ->where('siswa_id', $selectedAnak->id_siswa)
                    ->whereHas('minggu', function($q) use ($periode) {
                        $q->where('periode_id', $periode->id_periode);
                    });

                if ($mingguId) {
                    $query->where('minggu_id', $mingguId);
                }

                $portofolio_list = $query->latest()->get();
            }
        }

        return view('wali.portofolio', compact('anak', 'selectedAnak', 'portofolio_list', 'mingguList', 'mingguId', 'periode', 'listPeriode'));
    }

    public function evaluasi(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $siswa = $anak->where('id_siswa', $siswaId)->first();
        $selectedAnak = $siswa; // fallback

        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $listPeriode = PeriodePenilaian::where('status', 'final')
            ->whereHas('evaluasi', fn($q) => $q->where('siswa_id', $siswa->id_siswa))
            ->orderBy('finalized_at', 'desc')
            ->get();

        $periodeId = $request->get('periode_id');
        $periode = null;

        if ($periodeId) {
            $periode = PeriodePenilaian::where('status', 'final')->find($periodeId);
        }

        if (!$periode) {
            $periode = $listPeriode->first();
        }

        $evaluasi = null;
        $details = collect();
        $portofolio_list = collect();
        $ranking = 0;
        $totalSiswa = 0;

        if ($periode) {
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id_siswa)->where('periode_id', $periode->id_periode)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama_kriteria ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')
                    ->where('siswa_id', $siswa->id_siswa)
                    ->whereHas('minggu', function($q) use ($periode) {
                        $q->where('periode_id', $periode->id_periode);
                    })
                    ->get();
                // Ambil semua hasil evaluasi dalam periode ini, tapi filter hanya yang sekelas dengan siswa ini
                $allResults = Evaluasi::where('periode_id', $periode->id_periode)
                    ->whereHas('siswa', function($q) use ($siswa) {
                        $q->where('kelas_id', $siswa->kelas_id);
                    })
                    ->orderBy('nilai_akhir', 'desc')
                    ->pluck('siswa_id')
                    ->toArray();
                
                $ranking = array_search($siswa->id_siswa, $allResults) + 1;
                $totalSiswa = count($allResults);
            }
        }

        return view('wali.evaluasi', compact('evaluasi', 'siswa', 'selectedAnak', 'anak', 'periode', 'listPeriode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    public function laporan(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $siswa = $anak->where('id_siswa', $siswaId)->first();
        $selectedAnak = $siswa; // fallback
        
        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $listPeriode = PeriodePenilaian::where('status', 'final')
            ->whereHas('evaluasi', fn($q) => $q->where('siswa_id', $siswa->id_siswa))
            ->orderBy('finalized_at', 'desc')
            ->get();

        $periodeId = $request->get('periode_id');
        $periode = null;

        if ($periodeId) {
            $periode = PeriodePenilaian::where('status', 'final')->find($periodeId);
        }

        if (!$periode) {
            $periode = $listPeriode->first();
        }

        $evaluasi = null;
        $details = collect();
        $portofolio_list = collect();
        $ranking = 0;
        $totalSiswa = 0;

        if ($periode) {
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id_siswa)->where('periode_id', $periode->id_periode)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama_kriteria ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')
                    ->where('siswa_id', $siswa->id_siswa)
                    ->whereHas('minggu', function($q) use ($periode) {
                        $q->where('periode_id', $periode->id_periode);
                    })
                    ->get();
                // Perbaikan: Ranking harus sekelas
                $allResults = Evaluasi::where('periode_id', $periode->id_periode)
                    ->whereHas('siswa', function($q) use ($siswa) {
                        $q->where('kelas_id', $siswa->kelas_id);
                    })
                    ->orderBy('nilai_akhir', 'desc')
                    ->pluck('siswa_id')
                    ->toArray();
                
                $ranking = array_search($siswa->id_siswa, $allResults) + 1;
                $totalSiswa = count($allResults);
            }
        }

        return view('wali.laporan', compact('evaluasi', 'siswa', 'selectedAnak', 'anak', 'periode', 'listPeriode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    /**
     * Penentuan Kategori - Versi Sidang (Hardcoded)
     */
    private function matchKategori($nilaiDecimal)
    {
        $nilaiPersen = $nilaiDecimal * 100;
        $kategori = KategoriNilai::findByNilai($nilaiPersen);
        return $kategori ? $kategori->nama : "MB";
    }

    /**
     * Penentuan Kategori Crisp (0-100)
     */
    private function matchKategoriCrisp($nilai)
    {
        $kategori = KategoriNilai::findByNilai((float)$nilai);
        return $kategori ? $kategori->nama : "MB";
    }

    private function getReportData($selectedSiswaId, $periodeId = null)
    {
        $user = Auth::user();
        $activeSiswa = Siswa::with(['kelas.tahunAjaran'])->find($selectedSiswaId);
        
        // Cek apakah ini benar anak dari wali murid
        if (!$activeSiswa || !$user->siswaWali()->where('siswa.id_siswa', $selectedSiswaId)->exists()) {
            return null;
        }

        $kriteriaList = Kriteria::orderBy('id_kriteria', 'asc')->get();
        
        // 1. Tentukan Evaluasi
        if ($periodeId) {
            $evaluasi = Evaluasi::with('periode')
                ->where('siswa_id', $selectedSiswaId)
                ->where('periode_id', $periodeId)
                ->first();
        } else {
            $evaluasi = Evaluasi::with('periode')
                ->where('siswa_id', $selectedSiswaId)
                ->whereHas('periode', fn($q) => $q->where('status', 'final'))
                ->latest('id_evaluasi')
                ->first();
        }
        
        // 2. Tentukan Periode (jika evaluasi tidak ada)
        if ($periodeId) {
            $activePeriode = PeriodePenilaian::find($periodeId);
        } else {
            $activePeriode = $evaluasi ? $evaluasi->periode : PeriodePenilaian::whereHas('kelas', fn($q) => $q->where('kelas.id_kelas', $activeSiswa->kelas_id))
                ->where('is_aktif', true)
                ->first();
        }

        $targetPeriodeId = $evaluasi ? $evaluasi->periode_id : ($activePeriode->id_periode ?? null);

        // 3. Ambil data Nilai (Kriteria & Subkriteria/Indikator) secara konsisten
        if ($evaluasi && isset($evaluasi->id_evaluasi)) {
            // Jika sudah difinalisasi, ambil detail penilaian langsung dari record database
            $detailEvaluasi = DetailEvaluasi::with('subkriteria.kriteria')->where('evaluasi_id', $evaluasi->id_evaluasi)->orderBy('subkriteria_id', 'asc')->get();
            
            // Hitung rata-rata kriteria berdasarkan subkriteria secara konsisten
            $kriteriaScores = $detailEvaluasi->groupBy(fn($item) => $item->subkriteria->kriteria_id)->map(function ($items) {
                $first = $items->first()->subkriteria->kriteria; 
                $avg = $items->avg('nilai_crisp');
                return [
                    'kode' => $first->id_kriteria, 
                    'nama' => $first->nama_kriteria, 
                    'avg' => round($avg, 2), 
                    'kategori' => $this->matchKategoriCrisp($avg)
                ];
            })->values();

            // Sinergikan detail subkriteria dari database
            $subDetails = $detailEvaluasi->map(function ($d) {
                return [
                    'id' => $d->subkriteria->id_subkriteria,
                    'kode' => $d->subkriteria->id_subkriteria,
                    'nama' => $d->subkriteria->nama_subkriteria,
                    'nilai' => $d->kategori,
                    'avg' => round($d->nilai_crisp, 2),
                    'catatan' => $d->rekomendasi_detail ?? '-'
                ];
            })->sortBy('id')->values();
        } else {
            // Jika belum difinalisasi, hitung pratinjau dinamis dari PenilaianMingguan
            $penilaianQuery = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])
                ->where('siswa_id', $selectedSiswaId);
            
            if ($targetPeriodeId) {
                $penilaianQuery->whereHas('jadwalSubkriteria.minggu', function($q) use ($targetPeriodeId) {
                    $q->where('periode_id', $targetPeriodeId);
                });
            }
            $penilaian = $penilaianQuery->get();

            // Hitung rata-rata subkriteria/indikator dulu
            $subDetails = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria_id)->map(function ($items) {
                $first = $items->first(); 
                $sub = $first->jadwalSubkriteria->subkriteria; 
                $avg = $items->avg('nilai_crisp');
                return [
                    'id' => $sub->id_subkriteria,
                    'kode' => $sub->id_subkriteria, 
                    'nama' => $sub->nama_subkriteria, 
                    'nilai' => $this->matchKategoriCrisp($avg), 
                    'avg' => round($avg, 2), 
                    'catatan' => $items->whereNotNull('catatan')->pluck('catatan')->filter()->first() ?? '-'
                ];
            })->sortBy('id')->values();

            // Menjamin setiap indikator memiliki bobot yang setara (rata-rata subkriteria dirata-ratakan per kriteria)
            $kriteriaScores = collect();
            foreach ($kriteriaList as $krit) {
                $kritSubs = $subDetails->filter(function($s) use ($krit) {
                    $subModel = Subkriteria::find($s['id']);
                    return $subModel && $subModel->kriteria_id === $krit->id_kriteria;
                });
                
                $avg = $kritSubs->count() > 0 ? $kritSubs->avg('avg') : 0.0;
                $kriteriaScores->push([
                    'kode' => $krit->id_kriteria,
                    'nama' => $krit->nama_kriteria,
                    'avg' => round($avg, 2),
                    'kategori' => $this->matchKategoriCrisp($avg)
                ]);
            }

            // Samakan data $detailEvaluasi untuk visualisasi di sisa template view
            $detailEvaluasi = $subDetails->map(function($s) {
                $subModel = Subkriteria::find($s['id']);
                return (object)[
                    'subkriteria' => (object)['kode' => $s['kode'], 'nama' => $s['nama'], 'kriteria' => $subModel ? $subModel->kriteria : null],
                    'kategori' => $s['nilai'],
                    'rekomendasi_detail' => $s['catatan']
                ];
            });
        }

        $detailEvaluasiGrouped = $detailEvaluasi->groupBy(fn($item) => isset($item->subkriteria->kriteria) ? $item->subkriteria->kriteria->nama_kriteria : 'Umum');
        
        // 4. Filter Portofolio berdasarkan periode terpilih
        $portofolioQuery = Portofolio::with(['images', 'minggu'])->where('siswa_id', $selectedSiswaId);
        if ($targetPeriodeId) {
            $portofolioQuery->whereHas('minggu', function($q) use ($targetPeriodeId) {
                $q->where('periode_id', $targetPeriodeId);
            });
        }
        $portofolioList = $portofolioQuery->get();
        
        $isSpkFinal = ($evaluasi !== null);
        
        if (!$evaluasi) {
            $weightedSum = 0;
            $params = ['C1' => ['min' => 50.00, 'max' => 95.00], 'C2' => ['min' => 58.33, 'max' => 95.00], 'C3' => ['min' => 66.67, 'max' => 95.00]];
            foreach ($kriteriaList as $krit) {
                $wi = (double)$krit->bobot_kriteria; 
                $scoreObj = $kriteriaScores->where('kode', $krit->id_kriteria)->first(); 
                $cout = $scoreObj ? $scoreObj['avg'] : 0;
                $p = $params[$krit->id_kriteria] ?? ['min' => 0, 'max' => 100];
                $ui = ($cout <= $p['min']) ? 0.0 : (($cout >= $p['max']) ? 1.0 : ($cout - $p['min']) / ($p['max'] - $p['min']));
                $weightedSum += ($wi * $ui);
            }
            $totalAvg = $weightedSum * 100; 
            $totalKat = $this->matchKategori($weightedSum);

            // Fetch recommendation from general template for unfinalized reports
            $tempRek = TemplateRekomendasiUmum::where('kategori', $totalKat)->orderBy('prioritas', 'desc')->first();
            $evaluasi = (object)[
                'rekomendasi' => $tempRek ? $tempRek->isi : null,
                'catatan_guru' => null,
                'nilai_akhir' => $weightedSum,
                'kategori_akhir' => $totalKat,
                'periode' => $activePeriode,
                'periode_id' => $activePeriode->id_periode ?? null
            ];
        } else {
            $totalAvg = (double)$evaluasi->nilai_akhir * 100; 
            $totalKat = $evaluasi->kategori_akhir; 
        }

        // Hitung Ranking (berdasarkan Nilai Akhir sekelas di periode ini)
        $periodeId = $evaluasi->periode_id ?? ($activePeriode->id_periode ?? null);
        $ranking = 0;
        $totalSiswa = 0;
        
        if ($periodeId) {
            $allSiswaInKelas = Siswa::where('kelas_id', $activeSiswa->kelas_id)->pluck('id_siswa')->toArray();
            $totalSiswa = count($allSiswaInKelas);
            
            // Ambil semua evaluasi di periode ini untuk kelas tersebut
            $allEvaluations = Evaluasi::where('periode_id', $periodeId)
                ->whereIn('siswa_id', $allSiswaInKelas)
                ->orderBy('nilai_akhir', 'desc')
                ->pluck('siswa_id')
                ->toArray();
                
            $rankIdx = array_search($selectedSiswaId, $allEvaluations);
            $ranking = ($rankIdx !== false) ? $rankIdx + 1 : '-';
        }

        return [
            'siswa' => $activeSiswa, 
            'kriteria' => $kriteriaScores, 
            'subkriteria' => $subDetails, 
            'detail_evaluasi' => $detailEvaluasi,
            'detail_evaluasi_grouped' => $detailEvaluasiGrouped, 
            'portofolio_list' => $portofolioList, 
            'evaluasi' => $evaluasi, 
            'active_periode' => $activePeriode,
            'final_score' => round($totalAvg, 2), 
            'final_kategori' => $totalKat, 
            'is_spk_final' => $isSpkFinal,
            'ranking' => $ranking,
            'total_siswa' => $totalSiswa
        ];
    }

    /**
     * Generate Laporan Word untuk Wali Murid
     */
    public function generateWordReport(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id_siswa',
            'periode_id' => 'nullable|exists:periode_penilaian,id_periode',
        ]);

        $reportData = $this->getReportData($request->siswa_id, $request->periode_id);
        if (!$reportData) return back()->with('error', 'Siswa tidak ditemukan atau bukan anak Anda.');

        try {
            $templatePath = storage_path('app/public/templates/template_laporan.docx');
            if (!file_exists($templatePath)) return back()->with('error', 'Template default tidak ditemukan.');

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            $semester = $reportData['evaluasi']->periode->semester ?? $reportData['active_periode']->semester ?? '—';
            $guruName = $reportData['siswa']->kelas->guru->first()->nama_lengkap ?? '—';

            $rekomendasiText = $reportData['evaluasi']->rekomendasi ?? '—';
            $rekomendasiText = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', htmlspecialchars($rekomendasiText, ENT_XML1, 'UTF-8'));
            $catatanText = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', htmlspecialchars($reportData['evaluasi']->catatan_guru ?? '—', ENT_XML1, 'UTF-8'));

            $templateProcessor->setValues([
                'NAMA_SISWA'     => $reportData['siswa']->name,
                'NISN'           => $reportData['siswa']->id_siswa ?: '—',
                'KELAS'          => $reportData['siswa']->kelas->nama_kelas ?? '—',
                'SEMESTER'       => $semester,
                'NILAI_AKHIR'    => $reportData['final_score'],
                'KATEGORI_AKHIR' => $reportData['final_kategori'],
                'REKOMENDASI'    => $rekomendasiText,
                'CATATAN_GURU'   => $catatanText,
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
                    $subKode = isset($det->subkriteria->id_subkriteria) ? $det->subkriteria->id_subkriteria : ($det->subkriteria->kode ?? '-');
                    $subNama = isset($det->subkriteria->nama_subkriteria) ? $det->subkriteria->nama_subkriteria : ($det->subkriteria->nama ?? '-');
                    $templateProcessor->setValue("SUB_KODE#$i", $subKode);
                    $templateProcessor->setValue("SUB_NAMA#$i", $subNama);
                    $templateProcessor->setValue("SUB_KAT#$i",  $det->kategori ?? '—');
                    $templateProcessor->setValue("SUB_CAT#$i",  $det->rekomendasi_detail ?? '—');
                }
            } else {
                $templateProcessor->setValues(['SUB_KODE' => '-', 'SUB_NAMA' => '(Belum ada data)', 'SUB_KAT' => 'MB', 'SUB_CAT' => '—']);
            }

            $allEntries = [];
            $tempMergedImages = []; // To keep track of temp files to delete later

            foreach ($reportData['portofolio_list'] as $porto) {
                $validImages = [];
                foreach ($porto->images as $img) {
                    $fullPath = storage_path('app/public/' . $img->file_path);
                    if (file_exists($fullPath) && !in_array(strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)), ['mp4', 'mov', 'webm'])) {
                        $validImages[] = $fullPath;
                    }
                }

                $finalImagePath = null;

                if (count($validImages) == 1 || (!extension_loaded('gd') || !function_exists('imagecreatetruecolor'))) {
                    // Jika hanya 1 gambar, atau ekstensi GD PHP tidak aktif, gunakan gambar pertama saja
                    $finalImagePath = $validImages[0] ?? null;
                } elseif (count($validImages) > 1) {
                    // Gabungkan gambar secara horizontal (dalam 1 baris)
                    $targetHeight = 300;
                    $totalWidth = 0;
                    $gdImages = [];
                    
                    foreach ($validImages as $path) {
                        $info = @getimagesize($path);
                        if (!$info) continue;
                        
                        $img = null;
                        switch ($info[2]) {
                            case IMAGETYPE_JPEG: $img = @imagecreatefromjpeg($path); break;
                            case IMAGETYPE_PNG:  $img = @imagecreatefrompng($path); break;
                            case IMAGETYPE_WEBP: $img = @imagecreatefromwebp($path); break;
                        }
                        
                        if ($img) {
                            $w = round(($info[0] / $info[1]) * $targetHeight);
                            $resized = imagecreatetruecolor($w, $targetHeight);
                            $white = imagecolorallocate($resized, 255, 255, 255);
                            imagefill($resized, 0, 0, $white);
                            
                            imagecopyresampled($resized, $img, 0, 0, 0, 0, $w, $targetHeight, $info[0], $info[1]);
                            imagedestroy($img);
                            
                            $gdImages[] = ['res' => $resized, 'w' => $w];
                            $totalWidth += $w + 10;
                        }
                    }
                    
                    if (count($gdImages) > 0) {
                        $totalWidth -= 10;
                        $canvas = imagecreatetruecolor($totalWidth, $targetHeight);
                        $white = imagecolorallocate($canvas, 255, 255, 255);
                        imagefill($canvas, 0, 0, $white);
                        
                        $currentX = 0;
                        foreach ($gdImages as $g) {
                            imagecopy($canvas, $g['res'], $currentX, 0, 0, 0, $g['w'], $targetHeight);
                            imagedestroy($g['res']);
                            $currentX += $g['w'] + 10;
                        }
                        
                        $tempImg = tempnam(sys_get_temp_dir(), 'porto_merge_wali_') . '.jpg';
                        imagejpeg($canvas, $tempImg, 90);
                        imagedestroy($canvas);
                        
                        $finalImagePath = $tempImg;
                        $tempMergedImages[] = $tempImg;
                    } else {
                        // Fallback jika proses GD gagal
                        $finalImagePath = $validImages[0] ?? null;
                    }
                }

                $allEntries[] = [
                    'minggu'  => $porto->minggu ? "Minggu Ke-".$porto->minggu->minggu_ke : '—',
                    'judul'   => $porto->judul,
                    'deskripsi' => $porto->deskripsi,
                    'path'    => $finalImagePath,
                ];
            }

            if (count($allEntries) > 0) {
                $templateProcessor->cloneRow('PORTO_MINGGU', count($allEntries));
                foreach ($allEntries as $index => $data) {
                    $i = $index + 1;
                    $templateProcessor->setValue("PORTO_MINGGU#$i", $data['minggu']);
                    $templateProcessor->setValue("PORTO_JUDUL#$i", $data['judul']);
                    $templateProcessor->setValue("PORTO_DESKRIPSI#$i", $data['deskripsi']);
                    if ($data['path'] && file_exists($data['path'])) {
                        $templateProcessor->setImageValue("PORTO_IMAGE#$i", [
                            'path' => $data['path'], 
                            'width' => 400, 
                            'height' => 150, 
                            'ratio' => true
                        ]);
                    } else {
                        $templateProcessor->setValue("PORTO_IMAGE#$i", '(Tanpa Foto/Media Video)');
                    }
                }
            } else {
                $templateProcessor->setValues(['PORTO_MINGGU' => '-', 'PORTO_JUDUL' => '(Kosong)', 'PORTO_DESKRIPSI' => '-', 'PORTO_IMAGE' => '-']);
            }

            $fileName = 'Laporan_Wali_' . str_replace(' ', '_', $reportData['siswa']->name) . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $templateProcessor->saveAs($tempFile);

            // Clean up temporary merged images
            foreach ($tempMergedImages as $tmpImg) {
                if (file_exists($tmpImg)) @unlink($tmpImg);
            }

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate Word: ' . $e->getMessage());
        }
    }

}
