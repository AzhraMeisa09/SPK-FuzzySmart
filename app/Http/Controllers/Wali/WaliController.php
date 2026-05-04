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
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with(['kelas.guru'])->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $selectedAnak = $anak->where('id', $siswaId)->first();
        
        $periodeAktif = PeriodePenilaian::where('is_aktif', true)->first();
        
        $penilaianTerbaru = collect();
        $evaluasiTerakhir = null;

        if ($selectedAnak) {
            $penilaianTerbaru = PenilaianMingguan::where('siswa_id', $selectedAnak->id)
                ->where('status', 'final')
                ->with(['jadwalSubkriteria.subkriteria', 'jadwalSubkriteria.minggu', 'kategori'])
                ->latest()
                ->take(5)
                ->get();

            // 1. Ambil evaluasi terakhir yang sudah final (dari periode mana saja)
            $evaluasiTerakhir = Evaluasi::where('siswa_id', $selectedAnak->id)
                ->whereHas('periode', fn($q) => $q->where('status', 'final'))
                ->latest()
                ->first();

            // 2. Jika belum ada evaluasi final, hitung data "Live" dari penilaian mingguan
            if (!$evaluasiTerakhir) {
                $allFinalPenilaian = PenilaianMingguan::where('siswa_id', $selectedAnak->id)
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
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $siswa = $anak->where('id', $siswaId)->first();
        $selectedAnak = $siswa; // for backward compatibility in layout if needed

        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'kategori', 'jadwalSubkriteria.minggu'])
            ->where('siswa_id', $siswa->id)
            ->where('status', 'final')
            ->get();

        $portofolios = \App\Models\Portofolio::with('images', 'minggu')
            ->where('siswa_id', $siswa->id)
            ->get();

        // Ambil semua ID minggu yang terlibat (baik dari penilaian maupun portofolio)
        $allMingguIds = $penilaian->pluck('jadwalSubkriteria.minggu_id')
            ->merge($portofolios->pluck('minggu_id'))
            ->unique()
            ->filter();

        $mingguList = MingguPenilaian::whereIn('id', $allMingguIds)
            ->orderBy('minggu_ke', 'asc')
            ->get();

        $penilaianGrouped = $penilaian->groupBy('jadwalSubkriteria.minggu_id');
        $portofolioGrouped = $portofolios->groupBy('minggu_id');

        $mingguGrouped = $mingguList->map(function ($minggu) use ($penilaianGrouped, $portofolioGrouped) {
            $items = $penilaianGrouped->get($minggu->id, collect());
            $weekPortos = $portofolioGrouped->get($minggu->id, collect());
            
            $avgCrisp = $items->count() > 0 ? $items->sum('nilai_crisp') / $items->count() : 0;
            
            $kategori = '-';
            if ($avgCrisp >= 80) $kategori = 'BSB';
            elseif ($avgCrisp >= 60) $kategori = 'BSH';
            elseif ($avgCrisp > 0) $kategori = 'MB';
            
            $subDetails = $items->map(function ($item) {
                $sub = $item->jadwalSubkriteria->subkriteria;
                return [
                    'kode' => $sub->kode,
                    'subkriteria' => $sub->nama,
                    'kriteria' => $sub->kriteria->nama ?? '-',
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
            'siswa', 'selectedAnak', 'anak', 'mingguGrouped', 'divisor', 'avgTotal', 'finalKat', 'bsbCount', 'bshCount', 'mbCount'
        ));
    }

    /**
     * Portofolio Anak
     */
    public function portofolio(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $selectedAnak = $anak->where('id', $siswaId)->first();

        $mingguId = $request->get('minggu_id');
        $mingguList = collect();
        $portofolio_list = collect();

        if ($selectedAnak) {
            // Selalu tampilkan portofolio terlepas dari status periode, agar sejarah tetap ada
            $query = \App\Models\Portofolio::with(['images', 'minggu.periode'])
                ->where('siswa_id', $selectedAnak->id);

            if ($mingguId) {
                $query->where('minggu_id', $mingguId);
            }

            $portofolio_list = $query->latest()->get();

            // Populate filter minggu dari periode terakhir/aktif
            $currentPeriode = PeriodePenilaian::whereHas('kelas', function($q) use ($selectedAnak) {
                    $q->where('kelas.id', $selectedAnak->kelas_id);
                })
                ->whereIn('status', [PeriodePenilaian::STATUS_AKTIF, PeriodePenilaian::STATUS_FINAL])
                ->latest('id')
                ->first();

            if ($currentPeriode) {
                $mingguList = MingguPenilaian::where('periode_id', $currentPeriode->id)
                    ->where('status', 'selesai')
                    ->orderBy('minggu_ke', 'desc')
                    ->get();
            }
        }

        return view('wali.portofolio', compact('anak', 'selectedAnak', 'portofolio_list', 'mingguList', 'mingguId'));
    }

    public function evaluasi(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $siswa = $anak->where('id', $siswaId)->first();
        $selectedAnak = $siswa; // fallback

        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        $evaluasi = null;
        $details = collect();
        $portofolio_list = collect();
        $ranking = 0;
        $totalSiswa = 0;

        if ($periode) {
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id)->where('periode_id', $periode->id)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')->where('siswa_id', $siswa->id)->get();
                // Ambil semua hasil evaluasi dalam periode ini, tapi filter hanya yang sekelas dengan siswa ini
                $allResults = Evaluasi::where('periode_id', $periode->id)
                    ->whereHas('siswa', function($q) use ($siswa) {
                        $q->where('kelas_id', $siswa->kelas_id);
                    })
                    ->orderBy('nilai_akhir', 'desc')
                    ->pluck('siswa_id')
                    ->toArray();
                
                $ranking = array_search($siswa->id, $allResults) + 1;
                $totalSiswa = count($allResults);
            }
        }

        return view('wali.evaluasi', compact('evaluasi', 'siswa', 'selectedAnak', 'anak', 'periode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    public function laporan(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id);
        $siswa = $anak->where('id', $siswaId)->first();
        
        if (!$siswa) {
            return redirect()->route('wali.dashboard')->with('error', 'Data anak tidak ditemukan.');
        }

        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        $evaluasi = null;
        $details = collect();
        $portofolio_list = collect();
        $ranking = 0;
        $totalSiswa = 0;

        if ($periode) {
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id)->where('periode_id', $periode->id)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')->where('siswa_id', $siswa->id)->get();
                // Perbaikan: Ranking harus sekelas
                $allResults = Evaluasi::where('periode_id', $periode->id)
                    ->whereHas('siswa', function($q) use ($siswa) {
                        $q->where('kelas_id', $siswa->kelas_id);
                    })
                    ->orderBy('nilai_akhir', 'desc')
                    ->pluck('siswa_id')
                    ->toArray();
                
                $ranking = array_search($siswa->id, $allResults) + 1;
                $totalSiswa = count($allResults);
            }
        }

        return view('wali.laporan', compact('evaluasi', 'siswa', 'anak', 'periode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    private function getReportData($selectedSiswaId)
    {
        $user = Auth::user();
        $activeSiswa = Siswa::with(['kelas.tahunAjaran'])->find($selectedSiswaId);
        
        // Cek apakah ini benar anak dari wali murid
        if (!$activeSiswa || !$user->siswaWali()->where('siswa.id', $selectedSiswaId)->exists()) {
            return null;
        }

        $kriteriaList = \App\Models\Kriteria::orderBy('id', 'asc')->get();
        $evaluasi = Evaluasi::with('periode')->where('siswa_id', $selectedSiswaId)->whereHas('periode', fn($q) => $q->where('status', 'final'))->latest('id')->first();
        
        $activePeriode = PeriodePenilaian::whereHas('kelas', fn($q) => $q->where('kelas.id', $activeSiswa->kelas_id))->where('is_aktif', true)->first();

        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])->where('siswa_id', $selectedSiswaId)->get();
        
        // Penentuan Kategori Crisp local helper
        $matchKategoriCrisp = function($nilai) {
            $kategori = \App\Models\KategoriNilai::where('nilai_l', '<=', (float)$nilai)->where('nilai_u', '>=', (float)$nilai)->first();
            return $kategori ? $kategori->nama : "MB";
        };

        $kriteriaScores = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria->kriteria_id)->map(function ($items) use ($matchKategoriCrisp) {
            $first = $items->first()->jadwalSubkriteria->subkriteria->kriteria; $avg = $items->avg('nilai_crisp');
            return ['kode' => $first->kode, 'nama' => $first->nama, 'avg' => round($avg, 2), 'kategori' => $matchKategoriCrisp($avg)];
        })->values();
        
        $subDetails = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria_id)->map(function ($items) use ($matchKategoriCrisp) {
            $first = $items->first(); $sub = $first->jadwalSubkriteria->subkriteria; $avg = $items->avg('nilai_crisp');
            return ['kode' => $sub->kode, 'nama' => $sub->nama, 'nilai' => $matchKategoriCrisp($avg), 'avg' => round($avg, 2), 'catatan' => $items->whereNotNull('catatan')->pluck('catatan')->filter()->first() ?? '-'];
        })->values();
        
        $detailEvaluasi = $evaluasi ? \App\Models\DetailEvaluasi::with('subkriteria.kriteria')->where('evaluasi_id', $evaluasi->id)->get() : collect();
        
        if ($detailEvaluasi->isEmpty() && $subDetails->isNotEmpty()) {
            $detailEvaluasi = $subDetails->map(function($s) {
                return (object)['subkriteria' => (object)['kode' => $s['kode'], 'nama' => $s['nama']], 'kategori' => $s['nilai'], 'rekomendasi_detail' => $s['catatan']];
            });
        }

        $detailEvaluasiGrouped = $detailEvaluasi->groupBy(fn($item) => isset($item->subkriteria->kriteria) ? $item->subkriteria->kriteria->nama : 'Umum');
        $portofolioList = \App\Models\Portofolio::with(['images', 'minggu'])->where('siswa_id', $selectedSiswaId)->get();
        
        $totalAvg = $evaluasi ? (double)$evaluasi->nilai_akhir * 100 : 0; 
        $totalKat = $evaluasi ? $evaluasi->kategori_akhir : 'MB'; 

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
            'is_spk_final' => ($evaluasi !== null)
        ];
    }

    /**
     * Generate Laporan Word untuk Wali Murid
     */
    public function generateWordReport(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $reportData = $this->getReportData($request->siswa_id);
        if (!$reportData) return back()->with('error', 'Siswa tidak ditemukan atau bukan anak Anda.');

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

            $fileName = 'Laporan_Wali_' . str_replace(' ', '_', $reportData['siswa']->nama) . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $templateProcessor->saveAs($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate Word: ' . $e->getMessage());
        }
    }

}
