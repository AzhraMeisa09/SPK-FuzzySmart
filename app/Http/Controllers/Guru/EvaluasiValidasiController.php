<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Evaluasi;
use App\Models\PeriodePenilaian;
use App\Models\DetailEvaluasi;
use App\Models\Siswa;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluasiValidasiController extends Controller
{
    /**
     * Daftar evaluasi yang menunggu / sudah divalidasi guru
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $kelasIds = $user->kelas->pluck('id_kelas')->toArray();

        // Cari periode yang sedang dalam status 'proses'
        $periodeList = PeriodePenilaian::where('status', PeriodePenilaian::STATUS_PROSES)
            ->whereHas('kelas', fn($q) => $q->whereIn('kelas.id_kelas', $kelasIds))
            ->orderBy('created_at', 'desc')
            ->get();

        // Juga ambil periode final agar guru bisa lihat riwayat validasi
        $periodeFinalList = PeriodePenilaian::where('status', PeriodePenilaian::STATUS_FINAL)
            ->whereHas('kelas', fn($q) => $q->whereIn('kelas.id_kelas', $kelasIds))
            ->orderBy('finalized_at', 'desc')
            ->take(5)->get();

        $selectedPeriodeId = $request->get('periode_id');
        $allPeriode        = $periodeList->merge($periodeFinalList);
        $periode           = $selectedPeriodeId
            ? $allPeriode->firstWhere('id_periode', $selectedPeriodeId)
            : $periodeList->first() ?? $periodeFinalList->first();

        $evaluasiList = collect();
        $progress     = ['total' => 0, 'done' => 0, 'pending' => 0];

        if ($periode) {
            $allSiswa    = Siswa::whereIn('kelas_id', $kelasIds)->pluck('id_siswa');
            $evaluasiList = Evaluasi::with(['siswa.kelas', 'guruValidator'])
                ->where('periode_id', $periode->id_periode)
                ->whereIn('siswa_id', $allSiswa)
                ->orderBy('status_validasi', 'asc') // menunggu_review dulu
                ->orderBy('nilai_akhir', 'desc')
                ->get();

            $total   = $evaluasiList->count();
            $done    = $evaluasiList->where('status_validasi', Evaluasi::STATUS_DISETUJUI_GURU)->count();
            $progress = ['total' => $total, 'done' => $done, 'pending' => $total - $done];
        }

        return view('guru.validasi_evaluasi', compact(
            'evaluasiList', 'periode', 'allPeriode', 'progress', 'selectedPeriodeId'
        ));
    }

    /**
     * Form review dan validasi satu evaluasi siswa
     */
    public function review(Request $request, $evaluasi_id)
    {
        $user     = Auth::user();
        $kelasIds = $user->kelas->pluck('id_kelas')->toArray();

        $evaluasi = Evaluasi::with(['siswa.kelas', 'periode', 'detail.subkriteria.kriteria', 'guruValidator'])
            ->findOrFail($evaluasi_id);

        // Pastikan guru hanya bisa review siswa dari kelasnya sendiri
        if (!in_array($evaluasi->siswa->kelas_id, $kelasIds)) {
            abort(403, 'Anda tidak berwenang untuk memvalidasi evaluasi ini.');
        }

        // Hanya bisa divalidasi jika periode berstatus 'proses' atau 'final'
        if (!in_array($evaluasi->periode->status, [PeriodePenilaian::STATUS_PROSES, PeriodePenilaian::STATUS_FINAL])) {
            return back()->with('error', 'Evaluasi ini belum dapat divalidasi. Periode harus dalam status Proses terlebih dahulu.');
        }

        $details = $evaluasi->detail->groupBy(
            fn($d) => $d->subkriteria->kriteria->nama_kriteria ?? 'Lainnya'
        );

        $portofolio = Portofolio::with('images', 'minggu')
            ->where('siswa_id', $evaluasi->siswa_id)
            ->whereHas('minggu', fn($q) => $q->where('periode_id', $evaluasi->periode_id))
            ->get();

        return view('guru.validasi_evaluasi_form', compact('evaluasi', 'details', 'portofolio'));
    }

    /**
     * Simpan keputusan validasi guru
     */
    public function submit(Request $request, $evaluasi_id)
    {
        $request->validate([
            'kategori_keputusan_guru' => 'required|in:BSB,BSH,MB',
            'catatan_guru'            => 'required|string|min:10|max:5000',
        ], [
            'kategori_keputusan_guru.required' => 'Kategori keputusan wajib dipilih.',
            'catatan_guru.required'            => 'Catatan evaluasi wajib diisi.',
            'catatan_guru.min'                 => 'Catatan minimal 10 karakter.',
        ]);

        $user     = Auth::user();
        $kelasIds = $user->kelas->pluck('id_kelas')->toArray();

        $evaluasi = Evaluasi::with(['siswa', 'periode'])->findOrFail($evaluasi_id);

        // Cek hak akses
        if (!in_array($evaluasi->siswa->kelas_id, $kelasIds)) {
            abort(403);
        }

        // Periode harus 'proses' agar bisa divalidasi
        if ($evaluasi->periode->status !== PeriodePenilaian::STATUS_PROSES) {
            return back()->with('error', 'Periode tidak dalam status Proses. Validasi tidak dapat dilakukan.');
        }

        // Jika sudah final (dipublikasikan), tidak bisa diubah lagi
        if ($evaluasi->is_final) {
            return back()->with('error', 'Evaluasi ini sudah dipublikasikan dan tidak dapat diubah.');
        }

        try {
            $kategoriGuru = $request->kategori_keputusan_guru;
            $isDiubah     = $kategoriGuru !== $evaluasi->kategori_rekomendasi_sistem;

            $evaluasi->update([
                'kategori_keputusan_guru' => $kategoriGuru,
                'catatan_guru'            => $request->catatan_guru,
                'status_validasi'         => Evaluasi::STATUS_DISETUJUI_GURU,
                'tanggal_validasi'        => now(),
                'id_guru_validator'       => $user->id_user,
            ]);

            $msg = $isDiubah
                ? '✅ Validasi disimpan. Kategori diubah dari rekomendasi sistem berdasarkan observasi langsung guru.'
                : '✅ Validasi disimpan. Kategori sesuai dengan rekomendasi sistem.';

            return redirect()->route('guru.validasi.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
