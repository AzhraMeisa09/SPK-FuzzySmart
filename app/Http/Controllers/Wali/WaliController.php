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

        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'kategori', 'jadwalSubkriteria.minggu'])
            ->where('siswa_id', $siswa->id_siswa)
            ->where('status', 'final')
            ->get();

        $portofolios = \App\Models\Portofolio::with('images', 'minggu')
            ->where('siswa_id', $siswa->id_siswa)
            ->get();

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
        
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $selectedAnak = $anak->where('id_siswa', $siswaId)->first();

        $mingguId = $request->get('minggu_id');
        $mingguList = collect();
        $portofolio_list = collect();

        if ($selectedAnak) {
            // Selalu tampilkan portofolio terlepas dari status periode, agar sejarah tetap ada
            $query = \App\Models\Portofolio::with(['images', 'minggu.periode'])
                ->where('siswa_id', $selectedAnak->id_siswa);

            if ($mingguId) {
                $query->where('minggu_id', $mingguId);
            }

            $portofolio_list = $query->latest()->get();

            // Populate filter minggu dari periode terakhir/aktif
            $currentPeriode = PeriodePenilaian::whereHas('kelas', function($q) use ($selectedAnak) {
                    $q->where('kelas.id_kelas', $selectedAnak->kelas_id);
                })
                ->whereIn('status', [PeriodePenilaian::STATUS_AKTIF, PeriodePenilaian::STATUS_FINAL])
                ->latest('id_periode')
                ->first();

            if ($currentPeriode) {
                $mingguList = MingguPenilaian::where('periode_id', $currentPeriode->id_periode)
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
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $siswa = $anak->where('id_siswa', $siswaId)->first();
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
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id_siswa)->where('periode_id', $periode->id_periode)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama_kriteria ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')->where('siswa_id', $siswa->id_siswa)->get();
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

        return view('wali.evaluasi', compact('evaluasi', 'siswa', 'selectedAnak', 'anak', 'periode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    public function laporan(Request $request)
    {
        $user = Auth::user();
        $anak = $user->siswaWali()->with('kelas')->get();
        $siswaId = $request->get('siswa_id', $anak->first()?->id_siswa);
        $siswa = $anak->where('id_siswa', $siswaId)->first();
        
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
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa->id_siswa)->where('periode_id', $periode->id_periode)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama_kriteria ?? 'Lainnya');
                $portofolio_list = \App\Models\Portofolio::with('images', 'minggu')->where('siswa_id', $siswa->id_siswa)->get();
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

        return view('wali.laporan', compact('evaluasi', 'siswa', 'anak', 'periode', 'details', 'portofolio_list', 'ranking', 'totalSiswa'));
    }

    private function getReportData($selectedSiswaId)
    {
        $user = Auth::user();
        $activeSiswa = Siswa::with(['kelas.tahunAjaran'])->find($selectedSiswaId);
        
        // Cek apakah ini benar anak dari wali murid
        if (!$activeSiswa || !$user->siswaWali()->where('siswa.id_siswa', $selectedSiswaId)->exists()) {
            return null;
        }

        $kriteriaList = \App\Models\Kriteria::orderBy('id_kriteria', 'asc')->get();
        $evaluasi = Evaluasi::with('periode')->where('siswa_id', $selectedSiswaId)->whereHas('periode', fn($q) => $q->where('status', 'final'))->latest('id_evaluasi')->first();
        
        $activePeriode = PeriodePenilaian::whereHas('kelas', fn($q) => $q->where('kelas.id_kelas', $activeSiswa->kelas_id))->where('is_aktif', true)->first();

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
            return ['kode' => $sub->id_subkriteria, 'nama' => $sub->nama_subkriteria, 'nilai' => $matchKategoriCrisp($avg), 'avg' => round($avg, 2), 'catatan' => $items->whereNotNull('catatan')->pluck('catatan')->filter()->first() ?? '-'];
        })->values();
        
        $detailEvaluasi = $evaluasi ? \App\Models\DetailEvaluasi::with('subkriteria.kriteria')->where('evaluasi_id', $evaluasi->id_evaluasi)->get() : collect();
        
        if ($detailEvaluasi->isEmpty() && $subDetails->isNotEmpty()) {
            $detailEvaluasi = $subDetails->map(function($s) {
                return (object)['subkriteria' => (object)['kode' => $s['kode'], 'nama' => $s['nama']], 'kategori' => $s['nilai'], 'rekomendasi_detail' => $s['catatan']];
            });
        }

        $detailEvaluasiGrouped = $detailEvaluasi->groupBy(fn($item) => isset($item->subkriteria->kriteria) ? $item->subkriteria->kriteria->nama_kriteria : 'Umum');
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
            'siswa_id' => 'required|exists:siswa,id_siswa',
        ]);

        $reportData = $this->getReportData($request->siswa_id);
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
                    $templateProcessor->setValue("SUB_KODE#$i", $det->subkriteria->id_subkriteria);
                    $templateProcessor->setValue("SUB_NAMA#$i", $det->subkriteria->nama_subkriteria);
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
