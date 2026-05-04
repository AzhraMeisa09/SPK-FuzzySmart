<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\PeriodePenilaian;
use App\Models\MingguPenilaian;
use App\Models\JadwalSubkriteria;
use App\Models\PenilaianMingguan;
use App\Models\KategoriNilai;
use App\Models\Kriteria;
use App\Models\Portofolio;
use App\Models\PortofolioImage;
use App\Models\Evaluasi;
use App\Models\DetailEvaluasi;
use App\Models\TemplateRekomendasiUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class PenilaianController extends Controller
{
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $kelasIds = $user->kelas->pluck('id')->toArray();
        $periode = PeriodePenilaian::where('is_aktif', true)->first();
        $mingguAktif = null;
        if ($periode) $mingguAktif = MingguPenilaian::where('periode_id', $periode->id)->where('status', 'aktif')->first();
        
        $search = $request->input('search');
        $siswaQuery = Siswa::whereIn('kelas_id', $kelasIds);
        
        if ($search) {
            $siswaQuery->where('nama', 'like', "%{$search}%");
        }
        
        $siswa = $siswaQuery->orderBy('nama')->paginate(15)->withQueryString();
        $penilaianExisting = collect(); $statusGrid = []; $jadwalPerMinggu = []; $semuaMinggu = collect();
        if ($periode) {
            $semuaMinggu = MingguPenilaian::where('periode_id', $periode->id)->orderBy('minggu_ke', 'asc')->get();
            $mingguAktif = $semuaMinggu->where('status', 'aktif')->first();
            
            $jadwalIds = JadwalSubkriteria::whereIn('minggu_id', $semuaMinggu->pluck('id'))->pluck('id');
            $penilaianExisting = PenilaianMingguan::whereIn('siswa_id', $siswa->pluck('id'))->whereIn('jadwal_sub_id', $jadwalIds)->get();
            
            foreach ($siswa as $s) {
                foreach ($semuaMinggu as $m) {
                    $mJadwalIds = JadwalSubkriteria::where('minggu_id', $m->id)->pluck('id');
                    $pSiswa = $penilaianExisting->where('siswa_id', $s->id)->whereIn('jadwal_sub_id', $mJadwalIds);
                    if ($pSiswa->count() > 0) {
                        $isFinal = $pSiswa->where('status', 'final')->count() == $mJadwalIds->count() && $mJadwalIds->count() > 0;
                        $statusGrid[$s->id][$m->id] = $isFinal ? 'final' : 'draft';
                    } else {
                        $statusGrid[$s->id][$m->id] = null;
                    }
                }
            }
            foreach ($semuaMinggu as $m) {
                $jadwalPerMinggu[$m->id] = JadwalSubkriteria::with('subkriteria.kriteria')->where('minggu_id', $m->id)->get();
            }
        }
        $kategoriNilai = KategoriNilai::orderBy('nilai_l', 'asc')->get();
        return view('guru.penilaian', compact('periode', 'mingguAktif', 'semuaMinggu', 'siswa', 'statusGrid', 'jadwalPerMinggu', 'kategoriNilai', 'penilaianExisting'));
    }

    public function store(Request $request)
    {
        $request->validate(['siswa_id' => 'required|integer', 'minggu_id' => 'required|integer']);
        $guru = Auth::user(); $siswaId = $request->siswa_id; $mingguId = $request->minggu_id; $status = $request->is_final ? 'final' : 'draft';
        $minggu = MingguPenilaian::with('periode')->findOrFail($mingguId);

        // LOCK SYSTEM
        if ($minggu->periode->status === 'final') {
            return back()->with('error', 'Periode ini sudah final. Penilaian tidak dapat diubah.');
        }

        $jadwalList = JadwalSubkriteria::where('minggu_id', $mingguId)->get();
        $nilaiInput = $request->nilai[$siswaId] ?? [];
        try {
            DB::beginTransaction();
            foreach ($jadwalList as $jadwal) {
                $kategoriId = $nilaiInput[$jadwal->id] ?? null;
                $dataSave = ['guru_id' => $guru->id, 'catatan' => $request->catatan[$jadwal->id] ?? null, 'status' => $status];
                if ($kategoriId) {
                    $kategori = KategoriNilai::find($kategoriId);
                    if ($kategori) {
                        /**
                         * 📉 DEFUZZIFIKASI SNAPSHOT (INTEGRITAS HISTORIS)
                         * Sistem menyimpan nilai crisp secara permanen pada saat input.
                         * Ini menjamin konsistensi data historis meskipun di masa depan 
                         * standar kategori_nilai (fuzzy) diubah oleh administrator.
                         */
                        $dataSave['kategori_id'] = $kategori->id;
                        $dataSave['nilai_l'] = $kategori->nilai_l; 
                        $dataSave['nilai_m'] = $kategori->nilai_m; 
                        $dataSave['nilai_u'] = $kategori->nilai_u;
                        $dataSave['nilai_crisp'] = ($kategori->nilai_l + $kategori->nilai_m + $kategori->nilai_u) / 3.0;
                    }
                }
                PenilaianMingguan::updateOrCreate(['jadwal_sub_id' => $jadwal->id, 'siswa_id' => $siswaId], $dataSave);
            }
            if ($request->hasFile('portofolio')) {
                $portofolio = Portofolio::firstOrCreate(['siswa_id' => $siswaId, 'minggu_id' => $mingguId]);
                foreach ($request->file('portofolio') as $file) {
                    $path = $file->store('portofolio', 'public');
                    PortofolioImage::create(['portofolio_id' => $portofolio->id, 'path' => $path, 'caption' => $file->getClientOriginalName()]);
                }
            }
            DB::commit(); return redirect()->route('guru.penilaian')->with('success', 'Penilaian disimpan.');
        } catch (\Exception $e) { DB::rollBack(); return back()->with('error', $e->getMessage()); }
    }

    public function rekap()
    {
        $user = auth()->user(); 
        $kelasIds = $user->kelas->pluck('id')->toArray();
        $allSiswa = Siswa::whereIn('kelas_id', $kelasIds)->with(['kelas', 'penilaian.jadwalSubkriteria.minggu'])->get();
        
        $periode = PeriodePenilaian::where('is_aktif', true)->first();
        $totalMingguInPeriode = $periode ? $periode->minggu()->count() : 0;
        
        $records = $allSiswa->map(function($s) use ($totalMingguInPeriode) {
            // Group penilaian by week
            $mingguGrouped = $s->penilaian->where('status', 'final')->groupBy('jadwalSubkriteria.minggu_id')->map(function ($items) {
                // Average for this week: sum of scores / number of sub-criteria
                return $items->count() > 0 ? $items->sum('nilai_crisp') / $items->count() : 0;
            });

            $sumWeeklyAvg = $mingguGrouped->sum();
            
            // Fallback Divisor
            $divisor = $totalMingguInPeriode ?: $mingguGrouped->count();
            $avgFinal = $divisor > 0 ? ($sumWeeklyAvg / $divisor) : 0;
            
            return [
                'siswa_id' => $s->id, 
                'nama' => $s->nama, 
                'nisn' => $s->kode, 
                'kelas' => $s->kelas->nama_kelas ?? '—', 
                'avg' => round($avgFinal, 2), 
                'kategori' => $this->matchKategoriCrisp($avgFinal), 
                'total_minggu_data' => $mingguGrouped->count(),
                'total_minggu_period' => $totalMingguInPeriode,
                'divisor' => $divisor
            ];
        });

        $statistics = [
            'total_siswa' => $allSiswa->count(), 
            'bsb' => $records->where('kategori', 'BSB')->count(), 
            'bsh' => $records->where('kategori', 'BSH')->count(), 
            'mb' => $records->where('kategori', 'MB')->count()
        ];

        return view('guru.rekap', compact('records', 'totalMingguInPeriode', 'statistics'));
    }

    public function laporan(Request $request)
    {
        $user = auth()->user(); 
        $kelasIds = $user->kelas->pluck('id')->toArray();
        $allSiswa = Siswa::whereIn('kelas_id', $kelasIds)->with('kelas')->get();
        $selectedSiswaId = $request->siswa_id; 
        $reportData = [];
        
        if ($selectedSiswaId) {
            $reportData = $this->getReportData($selectedSiswaId);
        }

        return view('guru.laporan', compact('allSiswa', 'reportData', 'selectedSiswaId'));
    }

    private function getReportData($selectedSiswaId)
    {
        $user = auth()->user(); $kelasIds = $user->kelas->pluck('id')->toArray();
        $activeSiswa = Siswa::with(['kelas.tahunAjaran'])->find($selectedSiswaId);
        
        if (!$activeSiswa || !in_array($activeSiswa->kelas_id, $kelasIds)) return null;

        $kriteriaList = Kriteria::orderBy('id', 'asc')->get();
        // Ambil evaluasi final jika ada
        $evaluasi = Evaluasi::with('periode')->where('siswa_id', $selectedSiswaId)->whereHas('periode', fn($q) => $q->where('status', 'final'))->latest('id')->first();
        
        // Jika tidak ada evaluasi final, ambil semester dari periode aktif kelas tersebut
        $activePeriode = PeriodePenilaian::whereHas('kelas', fn($q) => $q->where('kelas.id', $activeSiswa->kelas_id))
            ->where('is_aktif', true)
            ->first();

        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])->where('siswa_id', $selectedSiswaId)->get();
        
        $kriteriaScores = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria->kriteria_id)->map(function ($items) {
            $first = $items->first()->jadwalSubkriteria->subkriteria->kriteria; $avg = $items->avg('nilai_crisp');
            return ['kode' => $first->kode, 'nama' => $first->nama, 'avg' => round($avg, 2), 'kategori' => $this->matchKategoriCrisp($avg)];
        })->values();
        
        $subDetails = $penilaian->groupBy(fn($item) => $item->jadwalSubkriteria->subkriteria_id)->map(function ($items) {
            $first = $items->first(); 
            $sub = $first->jadwalSubkriteria->subkriteria; 
            $avg = $items->avg('nilai_crisp');
            return [
                'id' => $sub->id,
                'kode' => $sub->kode, 
                'nama' => $sub->nama, 
                'nilai' => $this->matchKategoriCrisp($avg), 
                'avg' => round($avg, 2), 
                'catatan' => $items->whereNotNull('catatan')->pluck('catatan')->filter()->first() ?? '-'
            ];
        })->sortBy('id')->values();
        
        $detailEvaluasi = $evaluasi ? DetailEvaluasi::with('subkriteria.kriteria')->where('evaluasi_id', $evaluasi->id)->orderBy('subkriteria_id', 'asc')->get() : collect();
        
        // Fallback untuk DetailEvaluasi jika belum ada (gunakan data dari PenilaianMingguan)
        if ($detailEvaluasi->isEmpty() && $subDetails->isNotEmpty()) {
            $detailEvaluasi = $subDetails->map(function($s) {
                return (object)[
                    'subkriteria' => (object)['kode' => $s['kode'], 'nama' => $s['nama']],
                    'kategori' => $s['nilai'],
                    'rekomendasi_detail' => $s['catatan']
                ];
            });
        }

        $detailEvaluasiGrouped = $detailEvaluasi->groupBy(fn($item) => isset($item->subkriteria->kriteria) ? $item->subkriteria->kriteria->nama : 'Umum');
        $portofolioList = Portofolio::with(['images', 'minggu'])->where('siswa_id', $selectedSiswaId)->get();
        
        $isSpkFinal = ($evaluasi !== null);
        
        if (!$evaluasi) {
            $weightedSum = 0;
            $params = ['C1' => ['min' => 50.00, 'max' => 95.00], 'C2' => ['min' => 58.33, 'max' => 95.00], 'C3' => ['min' => 66.67, 'max' => 95.00]];
            foreach ($kriteriaList as $krit) {
                $wi = (double)$krit->bobot; 
                $scoreObj = $kriteriaScores->where('kode', $krit->kode)->first(); 
                $cout = $scoreObj ? $scoreObj['avg'] : 0;
                $p = $params[$krit->kode] ?? ['min' => 0, 'max' => 100];
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
                'periode' => $activePeriode
            ];
        } else {
            $totalAvg = (double)$evaluasi->nilai_akhir * 100; 
            $totalKat = $evaluasi->kategori_akhir; 
        }

        // Hitung Ranking (berdasarkan Nilai Akhir sekelas di periode ini)
        $periodeId = $evaluasi->periode_id ?? ($activePeriode->id ?? null);
        $ranking = 0;
        $totalSiswa = 0;
        
        if ($periodeId) {
            $allSiswaInKelas = Siswa::where('kelas_id', $activeSiswa->kelas_id)->pluck('id')->toArray();
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

    public function generateWordReport(Request $request)
    {
        // 1. Validasi & Ambil Data Laporan
        $request->validate([
            'siswa_id' => 'required',
            'template' => 'nullable|file|mimes:docx'
        ]);

        $reportData = $this->getReportData($request->siswa_id);
        if (!$reportData) return back()->with('error', 'Siswa tidak ditemukan.');

        try {
            // 2. Load Template Word
            $templatePath = $request->hasFile('template')
                ? $request->file('template')->getRealPath()
                : storage_path('app/public/templates/template_laporan.docx');
            
            if (!file_exists($templatePath)) {
                return back()->with('error', 'Template default tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // 3. Isi Variabel Dasar (Text)
            $semester = $reportData['evaluasi']->periode->semester ?? $reportData['active_periode']->semester ?? '—';
            $templateProcessor->setValues([
                'NAMA_SISWA'     => $reportData['siswa']->nama,
                'NISN'           => $reportData['siswa']->kode ?: '—',
                'KELAS'          => $reportData['siswa']->kelas->nama_kelas ?? '—',
                'SEMESTER'       => $semester,
                'NILAI_AKHIR'    => $reportData['final_score'],
                'KATEGORI_AKHIR' => $reportData['final_kategori'],
                'REKOMENDASI'    => $reportData['evaluasi']->rekomendasi ?? '—',
                'CATATAN_GURU'   => $reportData['evaluasi']->catatan_guru ?? '—',
                'GURU_NAME'      => auth()->user()->nama_lengkap, 
                'TANGGAL'        => now()->translatedFormat('d F Y'),
                'TAHUN_AJARAN'   => $reportData['siswa']->kelas->tahunAjaran->nama ?? '—',
            ]);

            // 4. Masukkan Foto Profil Siswa
            if ($reportData['siswa']->foto && file_exists(storage_path('app/public/' . $reportData['siswa']->foto))) {
                $templateProcessor->setImageValue('FOTO', [
                    'path'   => storage_path('app/public/' . $reportData['siswa']->foto),
                    'width'  => 100,
                    'height' => 120,
                    'ratio'  => true,
                ]);
            } else {
                $templateProcessor->setValue('FOTO', '(Belum ada foto)');
            }

            // 5. Tabel I – Capaian Per Aspek (cloneRow berdasarkan KRIT_KODE)
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
                $templateProcessor->setValues([
                    'KRIT_KODE' => '-', 'KRIT_NAMA' => '(Belum ada penilaian)', 'KRIT_SKOR' => '0%', 'KRIT_KAT' => 'MB'
                ]);
            }

            // 6. Tabel II – Detail Indikator Per Subkriteria
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
                $templateProcessor->setValues([
                    'SUB_KODE' => '-', 'SUB_NAMA' => '(Belum ada data)', 'SUB_KAT' => 'MB', 'SUB_CAT' => '—'
                ]);
            }

            // 7. Tabel III – Portofolio Anak (dengan gambar & minggu)
            $allEntries = [];
            foreach ($reportData['portofolio_list'] as $porto) {
                if ($porto->images->isEmpty()) {
                    $allEntries[] = [
                        'minggu'  => $porto->minggu ? "Minggu Ke-".$porto->minggu->minggu_ke : '—',
                        'judul'   => $porto->judul,
                        'deskripsi' => $porto->deskripsi,
                        'path'    => null,
                    ];
                } else {
                    foreach ($porto->images as $img) {
                        $allEntries[] = [
                            'minggu'  => $porto->minggu ? "Minggu Ke-".$porto->minggu->minggu_ke : '—',
                            'judul'   => $porto->judul,
                            'deskripsi' => $porto->deskripsi,
                            'path'    => $img->file_path,
                        ];
                    }
                }
            }

            if (count($allEntries) > 0) {
                $templateProcessor->cloneRow('PORTO_MINGGU', count($allEntries));
                foreach ($allEntries as $index => $data) {
                    $i = $index + 1;
                    $templateProcessor->setValue("PORTO_MINGGU#$i",    $data['minggu']);
                    $templateProcessor->setValue("PORTO_JUDUL#$i",     $data['judul']);
                    $templateProcessor->setValue("PORTO_DESKRIPSI#$i", $data['deskripsi']);
                    
                    if ($data['path']) {
                        $fullPath = storage_path('app/public/' . $data['path']);
                        if (file_exists($fullPath) && !in_array(strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)), ['mp4', 'mov', 'webm'])) {
                            $templateProcessor->setImageValue("PORTO_IMAGE#$i", [
                                'path'   => $fullPath,
                                'width'  => 150, 'height' => 150, 'ratio' => true
                            ]);
                        } else {
                            $templateProcessor->setValue("PORTO_IMAGE#$i", '(Media Video/Missing)');
                        }
                    } else {
                        $templateProcessor->setValue("PORTO_IMAGE#$i", '(Tanpa Foto)');
                    }
                }
            } else {
                $templateProcessor->setValues([
                    'PORTO_MINGGU' => '-', 'PORTO_JUDUL' => '(Kosong)', 'PORTO_DESKRIPSI' => '-', 'PORTO_IMAGE' => '-'
                ]);
            }

            // 9. Simpan & Download
            $fileName = 'Laporan_' . str_replace(' ', '_', $reportData['siswa']->nama) . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
            $templateProcessor->saveAs($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate Word: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        $user = auth()->user(); $kelasIds = $user->kelas->pluck('id')->toArray();
        
        // Query Siswa agar kita punya akses ke seluruh riwayatnya untuk statistik di card
        $siswaQuery = Siswa::whereIn('kelas_id', $kelasIds)->with(['kelas', 'penilaian.jadwalSubkriteria.minggu', 'portofolio']);
        
        $search = $request->input('search');
        if ($search) {
            $siswaQuery->where('nama', 'like', '%' . $search . '%');
        }
        
        $allSiswa = $siswaQuery->get();
        
        $periode = PeriodePenilaian::where('is_aktif', true)->first();
        $totalMingguInPeriode = $periode ? $periode->minggu()->count() : 0;
        
        $records = $allSiswa->map(function ($s) use ($totalMingguInPeriode, $periode) {
            $mingguGrouped = $s->penilaian->groupBy('jadwalSubkriteria.minggu_id')->map(function ($items) {
                $first = $items->first(); 
                $finalItems = $items->where('status', 'final');
                $avg = $finalItems->count() > 0 ? $finalItems->sum('nilai_crisp') / $finalItems->count() : 0;
                
                return [
                    'minggu' => 'Minggu ' . $first->jadwalSubkriteria->minggu->minggu_ke, 
                    'status' => $first->status, 
                    'hasil' => round((double)$avg, 2), 
                    'tanggal' => $first->created_at->format('d M Y'), 
                    'minggu_id' => $first->jadwalSubkriteria->minggu_id
                ];
            })->values();
            
            // Fallback Divisor: Jika periode aktif tidak ditemukan (0), gunakan jumlah minggu yang ada datanya
            $sumHasil = $mingguGrouped->sum('hasil');
            $divisor = $totalMingguInPeriode ?: $mingguGrouped->count();
            $avgKeseluruhan = $divisor > 0 ? $sumHasil / $divisor : 0;

            // Load Evaluasi (SPK Result)
            $evaluasi = $periode ? Evaluasi::where('siswa_id', $s->id)->where('periode_id', $periode->id)->first() : null;

            return [
                'siswa_id' => $s->id, 
                'nama' => $s->nama, 
                'nisn' => $s->kode, 
                'kelas' => $s->kelas->nama_kelas ?? '-', 
                'riwayat' => $mingguGrouped,
                'avg_hasil' => round($avgKeseluruhan, 2),
                'total_minggu_count' => $mingguGrouped->count(),
                'total_minggu_period' => $totalMingguInPeriode,
                'divisor' => $divisor,
                'has_portofolio' => $s->portofolio->count() > 0,
                'evaluasi' => $evaluasi
            ];
        })->values();

        return view('guru.riwayat', compact('records', 'totalMingguInPeriode'));
    }

    public function riwayatDetail(Siswa $siswa)
    {
        $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'jadwalSubkriteria.minggu', 'kategori'])->where('siswa_id', $siswa->id)->get();
        
        $mingguGrouped = $penilaian->groupBy('jadwalSubkriteria.minggu_id')->map(function ($items) {
            $first = $items->first(); 
            $minggu = $first->jadwalSubkriteria->minggu; 
            
            $finalItems = $items->where('status', 'final');
            $avgCrisp = $finalItems->count() > 0 ? $finalItems->sum('nilai_crisp') / $finalItems->count() : 0;
            $kategori = $avgCrisp ? $this->matchKategoriCrisp($avgCrisp) : '-';
            
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
                'tanggal' => $first->created_at->format('d M Y'),
                'avg' => round($avgCrisp, 2),
                'kategori' => $kategori,
                'status' => $first->status,
                'col' => $kategori === 'BSB' ? 'emerald' : ($kategori === 'BSH' ? 'amber' : 'rose'),
                'details' => $subDetails
            ];
        })->sortBy('minggu_ke')->values();

        $periode = PeriodePenilaian::where('is_aktif', true)->first();
        $totalMingguInPeriode = $periode ? $periode->minggu()->count() : 0;
        
        // Fallback: Jika periode aktif tidak ditemukan, gunakan jumlah minggu yang ada di data
        $divisor = $totalMingguInPeriode ?: $mingguGrouped->count();
        
        $sumAvg = $mingguGrouped->sum('avg');
        $avgTotal = $divisor > 0 ? $sumAvg / $divisor : 0;
        $finalKat = $this->matchKategoriCrisp($avgTotal);
        
        // Menghitung distribusi capaian dari seluruh indikator yang terinput
        $bsbCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'BSB')->count();
        $bshCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'BSH')->count();
        $mbCount = $penilaian->filter(fn($p) => ($p->kategori->nama ?? '') === 'MB')->count();

        return view('guru.riwayat_detail', compact(
            'siswa', 'mingguGrouped', 'totalMingguInPeriode', 'divisor', 'avgTotal', 'finalKat', 'bsbCount', 'bshCount', 'mbCount'
        ));
    }

    public function finalizeWeek($id)
    {
        $minggu = MingguPenilaian::with('periode')->findOrFail($id);

        // LOCK SYSTEM
        if ($minggu->periode->status === 'final') {
            return back()->with('error', 'Periode ini sudah final.');
        }

        $user = Auth::user(); $kelasIds = $user->kelas->pluck('id')->toArray();
        try {
            DB::beginTransaction();
            PenilaianMingguan::whereHas('jadwalSubkriteria', fn($q) => $q->where('minggu_id', $id))->whereHas('siswa', fn($q) => $q->whereIn('kelas_id', $kelasIds))->update(['status' => 'final']);
            DB::commit(); return back()->with('success', 'Minggu difinalisasi.');
        } catch (\Exception $e) { DB::rollBack(); return back()->with('error', $e->getMessage()); }
    }

    public function hasilEvaluasi(Request $request)
    {
        $user = Auth::user(); $kelasIds = $user->kelas->pluck('id')->toArray(); $allSiswa = Siswa::whereIn('kelas_id', $kelasIds)->get();
        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        $results = collect(); $isFinalized = false;
        if ($periode) {
            $isFinalized = true;
            $results = Evaluasi::with('siswa.kelas')->where('periode_id', $periode->id)->whereIn('siswa_id', $allSiswa->pluck('id'))->orderBy('nilai_akhir', 'desc')->get();
        }
        return view('guru.hasil_evaluasi', compact('results', 'periode', 'isFinalized'));
    }

    public function hasilEvaluasiDetail($siswa_id)
    {
        $user = Auth::user(); $siswa = Siswa::with('kelas')->findOrFail($siswa_id);
        $kelasIds = $user->kelas->pluck('id')->toArray();
        if (!in_array($siswa->kelas_id, $kelasIds)) abort(403);
        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        $evaluasi = null;
        $details = collect();
        $portofolio_list = collect();
        
        if ($periode) {
            $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa_id)->where('periode_id', $periode->id)->first();
            if ($evaluasi) {
                $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama ?? 'Lainnya');
                $portofolio_list = Portofolio::with('images', 'minggu')->where('siswa_id', $siswa_id)->get();
            }
        }
        
        return view('guru.hasil_evaluasi_detail', compact('evaluasi', 'siswa', 'periode', 'details', 'portofolio_list'));
    }

    public function cetakLaporan($siswa_id)
    {
        $user = Auth::user(); $siswa = Siswa::with('kelas')->findOrFail($siswa_id);
        $kelasIds = $user->kelas->pluck('id')->toArray();
        if (!in_array($siswa->kelas_id, $kelasIds)) abort(403);
        
        $periode = PeriodePenilaian::where('status', 'final')->latest('finalized_at')->first();
        if (!$periode) return back()->with('error', 'Belum ada periode final.');
        
        $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria'])->where('siswa_id', $siswa_id)->where('periode_id', $periode->id)->first();
        if (!$evaluasi) return back()->with('error', 'Hasil tidak ditemukan.');
        
        $details = $evaluasi->detail->groupBy(fn($d) => $d->subkriteria->kriteria->nama ?? 'Lainnya');
        $portofolio_list = Portofolio::with('images', 'minggu')->where('siswa_id', $siswa_id)->get();
        
        // Ambil ranking
        $allResults = Evaluasi::where('periode_id', $periode->id)->orderBy('nilai_akhir', 'desc')->pluck('siswa_id')->toArray();
        $ranking = array_search($siswa_id, $allResults) + 1;

        // Ambil semua siswa sekelas untuk switcher
        $anak = Siswa::where('kelas_id', $siswa->kelas_id)->orderBy('nama')->get();

        return view('guru.laporan_cetak', compact('evaluasi', 'siswa', 'periode', 'details', 'portofolio_list', 'ranking', 'anak'));
    }

    public function updateCatatanEvaluasi(Request $request, $evaluasi_id)
    {
        $request->validate(['catatan_guru' => 'required|string|max:5000']);
        $evaluasi = Evaluasi::with('periode')->findOrFail($evaluasi_id);

        try {
            $evaluasi->update(['catatan_guru' => $request->catatan_guru]);
            return back()->with('success', 'Catatan diperbarui.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }
}
