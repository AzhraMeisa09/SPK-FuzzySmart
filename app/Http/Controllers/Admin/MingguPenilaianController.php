<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MingguPenilaian;
use App\Models\PeriodePenilaian;
use App\Models\Subkriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MingguPenilaianController extends Controller
{
    public function index(Request $request)
    {
        $query = MingguPenilaian::query()->with(['periode.tahunAjaran', 'subkriteria']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('tema', 'like', "%{$search}%");
        }

        $minggu = $query->latest()->paginate(10)->withQueryString();
        $periode = PeriodePenilaian::where('is_aktif', true)->with('tahunAjaran')->get();
        $subkriteria = Subkriteria::with('kriteria')->orderBy('kriteria_id')->get();
        
        // Data sederhana untuk auto-suggest AlpineJS (tanpa relasi berat)
        $existingWeeks = MingguPenilaian::select('periode_id', 'minggu_ke')->get();

        return view('admin.minggu', compact('minggu', 'periode', 'subkriteria', 'existingWeeks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'minggu_ke' => 'required|integer|min:1',
            'tema' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'subkriteria_ids' => 'required|array|min:1',
            'subkriteria_ids.*' => 'exists:subkriteria,id',
        ]);

        $periode = PeriodePenilaian::find($request->periode_id);
        
        if (!$periode || !$periode->is_aktif) {
            return back()->with('error', 'Minggu penilaian hanya dapat ditambahkan pada periode yang aktif.')->withInput();
        }
        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_selesai);

        // 1. Tanggal harus dalam rentang periode
        if ($start->lt($periode->tanggal_mulai) || $end->gt($periode->tanggal_selesai)) {
            return back()->with('error', "Tanggal harus berada dalam rentang Periode: " . $periode->tanggal_mulai->format('d/m/Y') . " - " . $periode->tanggal_selesai->format('d/m/Y'))->withInput();
        }

        // 2. Minggu ke harus unik dalam periode
        $existsMinggu = MingguPenilaian::where('periode_id', $request->periode_id)
            ->where('minggu_ke', $request->minggu_ke)
            ->exists();
        if ($existsMinggu) {
            return back()->with('error', "Nomor Minggu ke-{$request->minggu_ke} sudah digunakan dalam periode ini.")->withInput();
        }

        // 3. Tidak boleh overlap dengan minggu lain di periode yang sama
        $overlap = MingguPenilaian::where('periode_id', $request->periode_id)
            ->where(function ($q) use ($start, $end) {
                $q->where('tanggal_mulai', '<=', $end->toDateString())
                  ->where('tanggal_selesai', '>=', $start->toDateString());
            })->exists();

        if ($overlap) {
            return back()->with('error', "Rentang tanggal tumpang tindih dengan jadwal minggu lain ($request->tanggal_mulai s/d $request->tanggal_selesai).")->withInput();
        }

        try {
            DB::beginTransaction();

            // Explicitly set status to draft and filter only fillable fields
            $minggu = MingguPenilaian::create([
                'periode_id' => $request->periode_id,
                'minggu_ke' => $request->minggu_ke,
                'tema' => $request->tema,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => 'draft',
            ]);

            if ($request->has('subkriteria_ids')) {
                $minggu->subkriteria()->sync($request->subkriteria_ids);
            }

            DB::commit();
            return back()->with('success', 'Berhasil menambahkan minggu penilaian.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error saving week: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan ke database: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, MingguPenilaian $minggu)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_penilaian,id',
            'minggu_ke' => 'required|integer|min:1',
            'tema' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'subkriteria_ids' => 'required|array|min:1',
            'subkriteria_ids.*' => 'exists:subkriteria,id',
        ]);

        if ($minggu->status !== 'draft') {
            return back()->with('error', 'Tidak dapat mengubah minggu yang sudah Aktif/Final.');
        }

        $periode = PeriodePenilaian::find($request->periode_id);
        
        if (!$periode || !$periode->is_aktif) {
            return back()->with('error', 'Periode sasaran harus dalam status Aktif.')->withInput();
        }
        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_selesai);

        if ($start->lt($periode->tanggal_mulai) || $end->gt($periode->tanggal_selesai)) {
            return back()->with('error', "Tanggal di luar rentang periode.")->withInput();
        }

        $existsMinggu = MingguPenilaian::where('periode_id', $request->periode_id)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('id', '!=', $minggu->id)
            ->exists();
        if ($existsMinggu) {
            return back()->with('error', "Nomor Minggu sudah digunakan.")->withInput();
        }

        $overlap = MingguPenilaian::where('periode_id', $request->periode_id)
            ->where('id', '!=', $minggu->id)
            ->where(function ($q) use ($start, $end) {
                $q->where('tanggal_mulai', '<=', $end->toDateString())
                  ->where('tanggal_selesai', '>=', $start->toDateString());
            })->exists();

        if ($overlap) {
            return back()->with('error', "Rentang tanggal tumpang tindih.");
        }

        try {
            DB::beginTransaction();
            $minggu->update($request->all());
            $minggu->subkriteria()->sync($request->subkriteria_ids);
            DB::commit();
            return back()->with('success', 'Berhasil memperbarui minggu.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $minggu = MingguPenilaian::findOrFail($id);
        $newStatus = $request->status;

        // Draft -> Aktif
        if ($minggu->status === 'draft' && $newStatus === 'aktif') {
            if (!$minggu->bolehDiisi()) {
                return back()->with('error', 'Minggu sebelumnya harus diselesaikan (Selesai) terlebih dahulu.');
            }
            $minggu->update(['status' => 'aktif']);
            return back()->with('success', 'Minggu berhasil diaktifkan.');
        }

        // Aktif -> Selesai
        if ($minggu->status === 'aktif' && $newStatus === 'selesai') {
            if (!$minggu->sudahDinilai()) {
                return back()->with('error', 'Gagal finalisasi. Pastikan seluruh guru telah memfinalisasi (bukan draf) penilaian untuk semua siswa dan subkriteria minggu ini.');
            }
            $minggu->update(['status' => 'selesai']);
            return back()->with('success', 'Minggu berhasil difinalisasi (Final Minggu).');
        }

        return back()->with('error', 'Alur perubahan status tidak valid.');
    }

    public function destroy(MingguPenilaian $minggu)
    {
        if ($minggu->status === 'selesai') {
            return back()->with('error', 'Minggu yang sudah selesai tidak dapat dihapus.');
        }

        // Cek data penilaian
        if ($minggu->jadwalSubkriteria()->whereHas('penilaian')->exists()) {
            return back()->with('error', 'Minggu tidak dapat dihapus karena sudah memiliki data penilaian.');
        }

        try {
            $minggu->delete();
            return back()->with('success', 'Minggu berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
