<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePenilaian;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodePenilaian::query()->with(['tahunAjaran', 'kelas', 'minggu']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama_periode', 'like', "%{$search}%");
        }

        $periode = $query->latest()->paginate(10)->withQueryString();
        $tahun_ajaran = TahunAjaran::orderBy('nama', 'desc')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('admin.periode', compact('periode', 'tahun_ajaran', 'kelas'));
    }

    public function show(PeriodePenilaian $periode)
    {
        return redirect()->route('admin.periode.index')->with('info', 'Detail periode dapat dilihat langsung pada tabel di bawah.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id'
        ]);

        // 1. Validasi Bisnis (Diluar Transaksi agar tidak mengunci DB jika gagal)
        foreach ($request->kelas_ids as $kelas_id) {
            // Cek Duplikat Semester
            $exists = PeriodePenilaian::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->whereHas('kelas', function ($q) use ($kelas_id) {
                    $q->where('kelas.id', $kelas_id);
                })->exists();

            if ($exists) {
                $nama_kelas = Kelas::find($kelas_id)->nama_kelas;
                return back()->with('error', "Kelas $nama_kelas sudah memiliki periode untuk semester ini.")->withInput();
            }

            // Cek Overlap Tanggal
            $overlap = PeriodePenilaian::whereHas('kelas', function ($q) use ($kelas_id) {
                    $q->where('kelas.id', $kelas_id);
                })
                ->where(function ($q) use ($request) {
                    $q->where('tanggal_mulai', '<', $request->tanggal_selesai)
                      ->where('tanggal_selesai', '>', $request->tanggal_mulai);
                })->exists();

            if ($overlap) {
                $nama_kelas = Kelas::find($kelas_id)->nama_kelas;
                return back()->with('error', "Tanggal periode bentrok (overlap) untuk kelas $nama_kelas.")->withInput();
            }
        }

        // 2. Simpan Data (Dalam Transaksi)
        try {
            DB::beginTransaction();

            $periode = PeriodePenilaian::create([
                'nama_periode' => $request->nama_periode,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'semester' => $request->semester,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif' => false,
                'status' => PeriodePenilaian::STATUS_DRAFT
            ]);

            $periode->kelas()->sync($request->kelas_ids);

            DB::commit();
            return back()->with('success', 'Periode berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function update(Request $request, PeriodePenilaian $periode)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id'
        ]);

        // 1. Validasi Bisnis (Diluar Transaksi)
        foreach ($request->kelas_ids as $kelas_id) {
            $exists = PeriodePenilaian::where('id', '!=', $periode->id)
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->whereHas('kelas', function ($q) use ($kelas_id) {
                    $q->where('kelas.id', $kelas_id);
                })->exists();

            if ($exists) {
                $nama_kelas = Kelas::find($kelas_id)->nama_kelas;
                return back()->with('error', "Kelas $nama_kelas sudah memiliki periode untuk semester ini.")->withInput();
            }

            $overlap = PeriodePenilaian::where('id', '!=', $periode->id)
                ->whereHas('kelas', function ($q) use ($kelas_id) {
                    $q->where('kelas.id', $kelas_id);
                })
                ->where(function ($q) use ($request) {
                    $q->where('tanggal_mulai', '<', $request->tanggal_selesai)
                      ->where('tanggal_selesai', '>', $request->tanggal_mulai);
                })->exists();

            if ($overlap) {
                $nama_kelas = Kelas::find($kelas_id)->nama_kelas;
                return back()->with('error', "Tanggal periode bentrok (overlap) untuk kelas $nama_kelas.")->withInput();
            }
        }

        // 2. Update (Dalam Transaksi)
        try {
            DB::beginTransaction();

            $periode->update([
                'nama_periode' => $request->nama_periode,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'semester' => $request->semester,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ]);

            $periode->kelas()->sync($request->kelas_ids);

            DB::commit();
            return back()->with('success', 'Periode berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function toggle(PeriodePenilaian $periode)
    {
        try {
            if ($periode->isFinal()) {
                return back()->with('error', 'Periode yang sudah final tidak dapat diubah status aktifnya.');
            }

            $periode->is_aktif = !$periode->is_aktif;
            
            // Sync status
            if ($periode->is_aktif) {
                $periode->status = PeriodePenilaian::STATUS_AKTIF;
            } else {
                $periode->status = PeriodePenilaian::STATUS_DRAFT;
            }

            $periode->save();
            
            $msg = $periode->is_aktif ? 'Periode berhasil diaktifkan.' : 'Periode berhasil dinonaktifkan (kembali ke Draft).';
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function finalize($id)
    {
        $periode = PeriodePenilaian::with('minggu')->findOrFail($id);

        // 🔒 VALIDASI 1: semua minggu harus selesai
        if ($periode->minggu()->where('status', '!=', 'selesai')->exists()) {
            return back()->with('error', 'Masih ada minggu yang belum selesai');
        }

        // 🔒 VALIDASI 2: tidak boleh ada nilai draft
        if (\App\Models\PenilaianMingguan::whereHas('jadwalSubkriteria.minggu', function ($q) use ($id) {
            $q->where('periode_id', $id);
        })->where('status', 'draft')->exists()) {
            return back()->with('error', 'Masih ada nilai draft');
        }

        // 🔒 VALIDASI 3: pastikan setiap kelas sudah melakukan pengisian
        $kelasIds = $periode->kelas->pluck('id');
        $kelasKosong = [];

        foreach ($kelasIds as $kelasId) {
            $siswaIds = \App\Models\Siswa::where('kelas_id', $kelasId)->pluck('id');
            $namaKelas = \App\Models\Kelas::find($kelasId)->nama_kelas;

            if ($siswaIds->isEmpty()) {
                $kelasKosong[] = $namaKelas . " (Kosong/Tidak ada siswa)";
                continue;
            }

            $hasPengisian = \App\Models\PenilaianMingguan::whereIn('siswa_id', $siswaIds)
                ->whereHas('jadwalSubkriteria.minggu', function ($q) use ($id) {
                    $q->where('periode_id', $id);
                })->exists();

            if (!$hasPengisian) {
                $kelasKosong[] = $namaKelas;
            }
        }

        if (!empty($kelasKosong)) {
            $namaKelasDigabung = implode(', ', $kelasKosong);
            return back()->with('error', "Gagal Finalisasi. Terdapat kelas yang belum melakukan pengisian penilaian sama sekali: Kelas {$namaKelasDigabung}");
        }

        try {
            DB::beginTransaction();

            // 🚀 JALANKAN SPK
            app(\App\Services\SpkService::class)->hitungPeriode($periode);

            // ✅ UPDATE STATUS
            $periode->update([
                'is_aktif' => false,
                'status' => PeriodePenilaian::STATUS_FINAL,
                'finalized_at' => now()
            ]);

            DB::commit();
            return back()->with('success', 'Periode berhasil difinalisasi & SPK dihitung');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(PeriodePenilaian $periode)
    {
        // Delete Protection
        if ($periode->minggu()->exists() || $periode->hasPenilaian()) {
            return back()->with('error', 'Periode tidak bisa dihapus karena sudah memiliki data penilaian.');
        }

        try {
            $periode->delete();
            return back()->with('success', 'Periode berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus periode: ' . $e->getMessage());
        }
    }
}
