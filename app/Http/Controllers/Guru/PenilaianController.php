<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\PeriodePenilaian;
use App\Models\MingguPenilaian;
use App\Models\JadwalSubkriteria;
use App\Models\KategoriNilai;
use App\Models\PenilaianMingguan;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Menampilkan halaman input nilai
     */
    public function index()
    {
        $guru = Auth::user();

        // 1. Ambil kelas yang diajar guru
        $kelasIds = $guru->kelas->pluck('id')->toArray();

        // 2. Ambil siswa dari kelas tersebut (Paginated)
        $siswa = Siswa::whereIn('kelas_id', $kelasIds)
            ->with('kelas')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // 3. Ambil periode aktif
        $periode = PeriodePenilaian::where('is_aktif', true)->first();

        // 4. Ambil minggu & jadwal
        $mingguAktif = null;
        $semuaMinggu = collect();
        $jadwalPerMinggu = []; // Simpan jadwal per minggu_id

        if ($periode) {
            $semuaMinggu = MingguPenilaian::where('periode_id', $periode->id)->orderBy('minggu_ke')->get();

            $mingguAktif = $semuaMinggu->where('status', 'aktif')->first();

            // Ambil SEMUA jadwal subkriteria untuk periode ini agar modal bisa render semua minggu
            $allJadwal = JadwalSubkriteria::with(['subkriteria.kriteria'])
                ->whereIn('minggu_id', $semuaMinggu->pluck('id'))
                ->orderBy('urutan')
                ->get();
            
            $jadwalPerMinggu = $allJadwal->groupBy('minggu_id');
        }

        // 6. Ambil kategori nilai
        $kategoriNilai = KategoriNilai::orderBy('nilai_crisp', 'asc')->get();

        // 7. Ambil semua penilaian existing untuk guru ini pada periode ini
        $penilaianExisting = collect();
        if ($periode) {
            $penilaianExisting = PenilaianMingguan::whereHas('jadwalSubkriteria.minggu', function($q) use ($periode) {
                    $q->where('periode_id', $periode->id);
                })->get();
        }

        // Generate status grid: statusGrid[siswa_id][minggu_id] = 'draft' | 'final' | null
        $statusGrid = [];
        foreach ($siswa as $s) {
            $statusGrid[$s->id] = [];
            foreach ($semuaMinggu as $m) {
                $jadwalIds = $m->jadwalSubkriteria->pluck('id')->toArray();
                $penilaianM = $penilaianExisting->where('siswa_id', $s->id)->whereIn('jadwal_sub_id', $jadwalIds);
                
                if ($penilaianM->isEmpty()) {
                    $statusGrid[$s->id][$m->id] = null;
                } else {
                    $jumlahDiisi = $penilaianM->count();
                    $jumlahJadwal = count($jadwalIds); 
                    $hasDraft = $penilaianM->where('status', 'draft')->isNotEmpty();

                    if ($hasDraft || $jumlahDiisi < $jumlahJadwal) {
                        $statusGrid[$s->id][$m->id] = 'draft';
                    } else {
                        $statusGrid[$s->id][$m->id] = 'final';
                    }
                }
            }
        }

        return view('guru.penilaian', compact(
            'siswa',
            'periode',
            'mingguAktif',
            'jadwalPerMinggu',
            'kategoriNilai',
            'semuaMinggu',
            'statusGrid',
            'penilaianExisting'
        ));
    }

    /**
     * Menyimpan atau mengupdate penilaian (Snapshot Logic)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nilai' => 'nullable|array',
            'status' => 'required|in:draft,final',
            'catatan' => 'nullable|array'
        ]);

        $guru = Auth::user();
        $kelasIds = $guru->kelas->pluck('id')->toArray();

        // 🔹 Identifikasi semua Siswa dan Jadwal yang mendapatkan input
        $inputSiswaIds = array_unique(array_merge(
            array_keys($request->nilai ?? []),
            array_keys($request->catatan ?? [])
        ));

        foreach ($inputSiswaIds as $siswaId) {
            // 1. Validasi Akses Guru
            $siswa = Siswa::find($siswaId);
            if (!$siswa || !in_array($siswa->kelas_id, $kelasIds)) continue;

            $inputJadwalIds = array_unique(array_merge(
                array_keys($request->nilai[$siswaId] ?? []),
                array_keys($request->catatan[$siswaId] ?? [])
            ));

            foreach ($inputJadwalIds as $jadwalSubId) {
                $jadwal = JadwalSubkriteria::with('minggu')->find($jadwalSubId);
                if (!$jadwal) continue;

                $minggu = $jadwal->minggu;
                
                // 🔹 LOCK BERLAPIS: Validasi wajib sesuai blueprint
                if ($minggu->status === 'selesai') {
                    abort(403, 'Tidak bisa mengubah nilai, minggu sudah final');
                }

                if (!$minggu->periode || !$minggu->periode->is_aktif) continue;

                // 2. Cek apakah sudah ada data sebelumnya
                $existing = PenilaianMingguan::where([
                    'jadwal_sub_id' => $jadwalSubId,
                    'siswa_id' => $siswaId,
                ])->first();

                // Cek status final baris (Gak boleh diubah jika sudah final per baris)
                // KECUALI jika nilai_crisp nilainya NULL (kita izinkan update untuk penyembuhan data lama)
                if ($existing && $existing->status === 'final' && $existing->nilai_crisp !== null) {
                    continue; 
                }

                // 3. Persiapan Data Snapshot
                $kategoriId = $request->nilai[$siswaId][$jadwalSubId] ?? ($existing ? $existing->kategori_id : null);
                $catatan = $request->catatan[$siswaId][$jadwalSubId] ?? ($existing ? $existing->catatan : null);

                $dataSave = [
                    'guru_id' => $guru->id,
                    'catatan' => $catatan,
                    'status' => $request->status
                ];

                // 4. Hitung Nilai Fisik jika kategori dipilih
                if ($kategoriId) {
                    $kategori = KategoriNilai::find($kategoriId);
                    if ($kategori) {
                        $l = $kategori->nilai_l;
                        $m = $kategori->nilai_m;
                        $u = $kategori->nilai_u;
                        $crisp = ($l + $m + $u) / 3;

                        $dataSave['kategori_id'] = $kategori->id;
                        $dataSave['nilai_l'] = $l;
                        $dataSave['nilai_m'] = $m;
                        $dataSave['nilai_u'] = $u;
                        $dataSave['nilai_crisp'] = $crisp;

                        // 5. Eksekusi Update atau Create HANYA jika kategori ditemukan
                        PenilaianMingguan::updateOrCreate(
                            [
                                'jadwal_sub_id' => $jadwalSubId,
                                'siswa_id' => $siswaId,
                            ],
                            $dataSave
                        );
                    }
                }
            }
        }

        $message = $request->status === 'final' ? 'Nilai berhasil difinalisasi.' : 'Draft nilai berhasil disimpan.';
        return redirect()->route('guru.penilaian')->with('success', $message);
    }

    /**
     * Menampilkan riwayat penilaian per siswa per minggu
     */
    public function riwayat(Request $request)
    {
        $user = auth()->user();

        $query = PenilaianMingguan::with([
            'siswa.kelas',
            'jadwalSubkriteria.minggu',
            'kategori'
        ])
        ->where('guru_id', $user->id);

        // 🔍 FILTER NAMA SISWA
        if ($request->nama) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        // 🔍 FILTER MINGGU
        if ($request->minggu) {
            $query->whereHas('jadwalSubkriteria.minggu', function ($q) use ($request) {
                $q->where('minggu_ke', $request->minggu);
            });
        }

        // 🔍 FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Ambil data
        $rawData = $query->get();
        
        // Grouping: Siswa -> Minggu
        $records = $rawData->groupBy('siswa_id')->map(function ($siswaItems) {
            $firstSiswa = $siswaItems->first()->siswa;
            
            $mingguGrouped = $siswaItems->groupBy(function($item) {
                return $item->jadwalSubkriteria->minggu_id;
            })->map(function ($items) {
                $first = $items->first();
                return [
                    'minggu' => 'Minggu ' . $first->jadwalSubkriteria->minggu->minggu_ke 
                                . ' - ' . $first->jadwalSubkriteria->minggu->tema,
                    'status' => $first->status,
                    'hasil' => $first->status === 'final' ? $items->avg('nilai_crisp') : null,
                    'tanggal' => $first->created_at->format('d M Y'),
                    'minggu_id' => $first->jadwalSubkriteria->minggu_id,
                ];
            })->values();

            return [
                'siswa_id' => $firstSiswa->id,
                'nama' => $firstSiswa->nama,
                'kelas' => $firstSiswa->kelas->nama ?? '-',
                'riwayat' => $mingguGrouped
            ];
        })->values();

        return view('guru.riwayat', compact('records'));
    }

    /**
     * Rekapitulasi Nilai Siswa per-Subkriteria (Dynamic)
     */
    public function rekap(Request $request)
    {
        $user = auth()->user();
        $kelasIds = $user->kelas->pluck('id')->toArray();
        $allSiswa = Siswa::whereIn('kelas_id', $kelasIds)->get();

        $statistics = [
            'BSB' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '>=', 85)->count(),
            'BSH' => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '>=', 70)->where('nilai_crisp', '<', 85)->count(),
            'MB'  => PenilaianMingguan::whereIn('siswa_id', $allSiswa->pluck('id'))->where('nilai_crisp', '<', 70)->where('nilai_crisp', '>', 0)->count(),
        ];

        // Fetch student scores summary
        $records = $allSiswa->map(function($s) {
            $p = PenilaianMingguan::where('siswa_id', $s->id)->get();
            $avg = $p->count() > 0 ? $p->avg('nilai_crisp') : 0;
            
            $kategori = '-';
            if ($avg >= 85) $kategori = 'BSB';
            elseif ($avg >= 70) $kategori = 'BSH';
            elseif ($avg > 0) $kategori = 'MB';

            return [
                'nama' => $s->nama,
                'avg' => round($avg, 2),
                'kategori' => $kategori,
                'total_entri' => $p->count()
            ];
        });

        return view('guru.rekap', compact('statistics', 'records'));
    }

    /**
     * Laporan Detail per Siswa (Dynamic)
     */
    public function laporan(Request $request)
    {
        $user = auth()->user();
        $kelasIds = $user->kelas->pluck('id')->toArray();
        $allSiswa = Siswa::whereIn('kelas_id', $kelasIds)->get();

        $selectedSiswaId = $request->siswa_id;
        $activeSiswa = null;
        $reportData = [];

        if ($selectedSiswaId) {
            $activeSiswa = Siswa::with('kelas')->find($selectedSiswaId);
            if ($activeSiswa && in_array($activeSiswa->kelas_id, $kelasIds)) {
                
                $penilaian = PenilaianMingguan::with(['jadwalSubkriteria.subkriteria.kriteria', 'kategori'])
                    ->where('siswa_id', $selectedSiswaId)
                    ->get();

                // Grouping per Kriteria
                $kriteriaScores = $penilaian->groupBy(function($item) {
                    return $item->jadwalSubkriteria->subkriteria->kriteria_id;
                })->map(function($items) {
                    $first = $items->first()->jadwalSubkriteria->subkriteria->kriteria;
                    $avg = $items->avg('nilai_crisp');
                    
                    $kat = 'MB';
                    if ($avg >= 85) $kat = 'BSB';
                    elseif ($avg >= 70) $kat = 'BSH';

                    return [
                        'kode' => $first->kode ?? 'K?',
                        'nama' => $first->nama,
                        'avg' => round($avg, 2),
                        'kategori' => $kat
                    ];
                })->values();

                // Detail Subkriteria (Unique entries)
                $subDetails = $penilaian->unique('jadwal_sub_id')->map(function($item) {
                    return [
                        'kode' => $item->jadwalSubkriteria->subkriteria->kode ?? 'S?',
                        'nama' => $item->jadwalSubkriteria->subkriteria->nama,
                        'nilai' => $item->kategori->nama ?? '-',
                        'catatan' => $item->catatan ?? '-'
                    ];
                });

                $totalAvg = $kriteriaScores->avg('avg');
                $totalKat = 'MB';
                if ($totalAvg >= 85) $totalKat = 'BSB';
                elseif ($totalAvg >= 70) $totalKat = 'BSH';

                $reportData = [
                    'siswa' => $activeSiswa,
                    'kriteria' => $kriteriaScores,
                    'subkriteria' => $subDetails,
                    'final_score' => round($totalAvg, 2),
                    'final_kategori' => $totalKat,
                ];
            }
        }

        return view('guru.laporan', compact('allSiswa', 'reportData', 'selectedSiswaId'));
    }
}
