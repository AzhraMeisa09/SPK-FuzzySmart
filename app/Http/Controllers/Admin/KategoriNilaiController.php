<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriNilai;
use Illuminate\Http\Request;

class KategoriNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = KategoriNilai::orderBy('rentang_min')->get();
        return view('admin.kategori_nilai', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Batasi jumlah data (Max 3: MB, BSH, BSB)
        if (KategoriNilai::count() >= 3) {
            return back()->with('error', 'Kategori maksimal hanya 3 (MB, BSH, BSB)');
        }

        // Pre-process decimal format (koma ke titik)
        $request->merge([
            'nilai_l' => str_replace(',', '.', $request->nilai_l),
            'nilai_m' => str_replace(',', '.', $request->nilai_m),
            'nilai_u' => str_replace(',', '.', $request->nilai_u),
        ]);

        // 2. Validasi Lengkap
        $request->validate([
            'nama' => 'required|in:MB,BSH,BSB|unique:kategori_nilai,nama',
            'nilai_l' => 'required|numeric|min:0|max:100',
            'nilai_m' => 'required|numeric|min:0|max:100',
            'nilai_u' => 'required|numeric|min:0|max:100',
            'rentang_min' => 'required|numeric|min:0|max:100',
            'rentang_max' => 'required|numeric|min:0|max:100|gte:rentang_min',
        ], [
            'nama.unique' => 'Kategori sudah ada',
        ]);

        // 3. Validasi Urutan Fuzzy (L <= M <= U)
        if ($request->nilai_l > $request->nilai_m || $request->nilai_m > $request->nilai_u) {
            return back()->withErrors(['nilai_l' => 'Nilai harus berurutan: L ≤ M ≤ U'])->withInput();
        }

        // 4. Anti-Overlap Logic
        $overlap = KategoriNilai::where(function ($query) use ($request) {
            $query->where('rentang_min', '<', $request->rentang_max)
                  ->where('rentang_max', '>', $request->rentang_min);
        })->exists();

        if ($overlap) {
            return back()->with('error', 'Rentang nilai tidak boleh overlap dengan kategori lain')->withInput();
        }

        KategoriNilai::create($request->all());

        return redirect()->back()->with('success', 'Kategori nilai berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriNilai $kategoriNilai)
    {
        // Pre-process decimal format (koma ke titik)
        $request->merge([
            'nilai_l' => str_replace(',', '.', $request->nilai_l),
            'nilai_m' => str_replace(',', '.', $request->nilai_m),
            'nilai_u' => str_replace(',', '.', $request->nilai_u),
        ]);

        $request->validate([
            'nama' => 'required|in:MB,BSH,BSB|unique:kategori_nilai,nama,' . $kategoriNilai->id_kategori . ',id_kategori',
            'nilai_l' => 'required|numeric|min:0|max:100',
            'nilai_m' => 'required|numeric|min:0|max:100',
            'nilai_u' => 'required|numeric|min:0|max:100',
            'rentang_min' => 'required|numeric|min:0|max:100',
            'rentang_max' => 'required|numeric|min:0|max:100|gte:rentang_min',
        ], [
            'nama.unique' => 'Kategori sudah ada',
        ]);

        // Validasi Urutan Fuzzy
        if ($request->nilai_l > $request->nilai_m || $request->nilai_m > $request->nilai_u) {
            return back()->withErrors(['nilai_l' => 'Nilai harus berurutan: L ≤ M ≤ U'])->withInput();
        }

        // Anti-Overlap Logic (Exclude Current ID)
        $overlap = KategoriNilai::where('id_kategori', '!=', $kategoriNilai->id_kategori)
            ->where(function ($query) use ($request) {
                $query->where('rentang_min', '<', $request->rentang_max)
                      ->where('rentang_max', '>', $request->rentang_min);
            })->exists();

        if ($overlap) {
            return back()->with('error', 'Rentang nilai tidak boleh overlap dengan kategori lain')->withInput();
        }

        $kategoriNilai->update($request->all());

        return redirect()->back()->with('success', 'Kategori nilai berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriNilai $kategoriNilai)
    {
        // Delete Protection
        if ($kategoriNilai->penilaian()->exists()) {
            return back()->with('error', 'Kategori sudah digunakan dalam penilaian dan tidak bisa dihapus');
        }

        $kategoriNilai->delete();

        return redirect()->back()->with('success', 'Kategori nilai berhasil dihapus');
    }
}
