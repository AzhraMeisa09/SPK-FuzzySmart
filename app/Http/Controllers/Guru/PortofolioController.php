<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use App\Models\PortofolioImage;
use App\Models\Siswa;
use App\Models\MingguPenilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PortofolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $kelasIds = $user->kelas()->pluck('kelas.id_kelas');

        $query = Portofolio::whereIn('siswa_id', function($q) use ($kelasIds) {
                $q->select('id_siswa')->from('siswa')->whereIn('kelas_id', $kelasIds);
            })
            ->with(['siswa.kelas', 'minggu', 'images'])
            ->latest();

        // Filter Siswa
        if ($request->siswa_id) {
            $query->where('siswa_id', $request->siswa_id);
        }

        // Filter Minggu
        if ($request->minggu_id) {
            $query->where('minggu_id', $request->minggu_id);
        }

        $portofolio = $query->paginate(12);

        $siswa = Siswa::with('kelas')->whereIn('kelas_id', $kelasIds)->orderBy('kelas_id')->orderBy('name')->get();
        $minggu = MingguPenilaian::whereHas('periode', function($q) {
                $q->where('is_aktif', true);
            })->get();

        return view('guru.portofolio', compact('portofolio', 'siswa', 'minggu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('guru.portofolio.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id_siswa',
            'minggu_id' => 'required|exists:minggu_penilaian,id_minggu',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $minggu = MingguPenilaian::with('periode')->findOrFail($request->minggu_id);

        // LOCK SYSTEM
        if ($minggu->periode->status === 'final') {
            return back()->with('error', 'Periode ini sudah final. Data tidak dapat ditambah.');
        }

        // CEK DUPLIKASI
        $existing = Portofolio::where('siswa_id', $request->siswa_id)
            ->where('minggu_id', $request->minggu_id)
            ->exists();
        if ($existing) {
            $msg = 'Siswa tersebut sudah memiliki portofolio di minggu ini.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        try {
            // Store images first (outside transaction to avoid long locks)
            $paths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('portofolio', 'public');
                }
            }

            DB::beginTransaction();
            $portofolio = Portofolio::create([
                'siswa_id' => $request->siswa_id,
                'guru_id' => Auth::id(),
                'minggu_id' => $request->minggu_id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
            ]);

            foreach ($paths as $path) {
                PortofolioImage::create([
                    'portofolio_id' => $portofolio->id_portofolio,
                    'file_path' => $path
                ]);
            }
            DB::commit();
            $count = $request->hasFile('images') ? count($request->file('images')) : 0;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Portofolio berhasil disimpan ($count foto).",
                    'redirect' => route('guru.portofolio.index')
                ]);
            }

            return redirect()->route('guru.portofolio.index')->with('success', "Portofolio berhasil disimpan ($count foto).");
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $portofolio = Portofolio::with(['siswa', 'minggu.periode', 'images'])->findOrFail($id);
        
        $user = Auth::user();
        $kelasIds = $user->kelas()->pluck('kelas.id_kelas');
        $siswa = Siswa::with('kelas')->whereIn('kelas_id', $kelasIds)->orderBy('kelas_id')->orderBy('name')->get();
        $minggu = MingguPenilaian::whereHas('periode', function($q) {
                $q->where('is_aktif', true);
            })->get();

        return view('guru.portofolio_detail', compact('portofolio', 'siswa', 'minggu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return redirect()->route('guru.portofolio.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id_siswa',
            'minggu_id' => 'required|exists:minggu_penilaian,id_minggu',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $portofolio = Portofolio::with('minggu.periode')->findOrFail($id);

        // LOCK SYSTEM
        if ($portofolio->minggu->periode->status === 'final') {
            return back()->with('error', 'Periode ini sudah final. Data tidak dapat diubah.');
        }

        // CEK DUPLIKASI
        $existing = Portofolio::where('siswa_id', $request->siswa_id)
            ->where('minggu_id', $request->minggu_id)
            ->where('id_portofolio', '!=', $id)
            ->exists();
        if ($existing) {
            $msg = 'Siswa tersebut sudah memiliki portofolio di minggu ini.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        try {
            // Store images first (outside transaction)
            $paths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('portofolio', 'public');
                }
            }

            DB::beginTransaction();
            $portofolio->update([
                'siswa_id' => $request->siswa_id,
                'minggu_id' => $request->minggu_id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
            ]);

            foreach ($paths as $path) {
                PortofolioImage::create([
                    'portofolio_id' => $portofolio->id_portofolio,
                    'file_path' => $path
                ]);
            }
            DB::commit();
            $count = $request->hasFile('images') ? count($request->file('images')) : 0;

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Portofolio berhasil diperbarui (+ $count foto baru).",
                    'redirect' => route('guru.portofolio.index')
                ]);
            }

            return redirect()->route('guru.portofolio.index')->with('success', "Portofolio berhasil diperbarui (+ $count foto baru).");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $portofolio = Portofolio::with(['images', 'minggu.periode'])->findOrFail($id);

        // LOCK SYSTEM
        if ($portofolio->minggu->periode->status === 'final') {
            return back()->with('error', 'Periode ini sudah final. Data tidak dapat dihapus.');
        }

        try {
            DB::beginTransaction();

            // Hapus file fisik
            foreach ($portofolio->images as $img) {
                Storage::disk('public')->delete($img->file_path);
            }

            $portofolio->delete();

            DB::commit();
            return redirect()->route('guru.portofolio.index')->with('success', 'Portofolio berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus satu gambar portofolio
     */
    public function destroyImage($id)
    {
        $img = PortofolioImage::findOrFail($id);
        $portofolio = Portofolio::with('minggu.periode')->findOrFail($img->portofolio_id);

        if ($portofolio->minggu->periode->status === 'final') {
            return response()->json(['success' => false, 'message' => 'Data terkunci'], 403);
        }

        Storage::disk('public')->delete($img->file_path);
        $img->delete();

        return response()->json(['success' => true]);
    }
}
