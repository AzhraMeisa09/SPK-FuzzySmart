<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelas::query()->with(['tahunAjaran', 'guru', 'siswa']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama_kelas', 'like', "%{$search}%");
        }

        $kelas = $query->latest()->paginate(10)->withQueryString();
        $tahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $guru = User::where('role', 'guru')->where('is_active', true)->get();

        return view('admin.kelas', compact('kelas', 'tahunAjaran', 'guru'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id_user',
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.',
            'tahun_ajaran_id.required' => 'Tahun Ajaran wajib dipilih.',
            'guru_ids.*.exists' => 'Data guru tidak valid.',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        if ($request->has('guru_ids')) {
            $kelas->guru()->sync($request->guru_ids);
        }

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil ditambahkan beserta relasi guru.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id_user',
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi.',
            'tahun_ajaran_id.required' => 'Tahun Ajaran wajib dipilih.',
            'guru_ids.*.exists' => 'Data guru tidak valid.',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]);

        if ($request->has('guru_ids')) {
            $kelas->guru()->sync($request->guru_ids);
        } else {
            $kelas->guru()->sync([]);
        }

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        // Optional: protect if there are students in the class
        if ($kelas->siswa()->exists()) {
            return redirect()->back()->with('error', 'Tidak bisa dihapus, Kelas ini sudah memiliki siswa.');
        }

        // pivot table data (kelas_guru) will be cascading deleted depending on DB schema
        // but we can be explicit just in case
        $kelas->guru()->detach();
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil dihapus.');
    }
}
