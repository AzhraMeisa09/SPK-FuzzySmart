<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TahunAjaran::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        $tahuns = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();
        return view('admin.tahun_ajaran', compact('tahuns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'regex:/^\d{4}\/\d{4}$/', 'unique:tahun_ajaran,nama'],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'nama.regex' => 'Format Nama Tahun Ajaran harus YYYY/YYYY (Contoh: 2024/2025).',
            'nama.unique' => 'Tahun Ajaran ini sudah ada dalam sistem.',
            'tanggal_selesai.after' => 'Tanggal Selesai harus lebih besar dari Tanggal Mulai.',
        ]);

        $data = $request->except('_token');
        $data['is_aktif'] = $request->has('is_aktif');

        TahunAjaran::create($data);

        return redirect()->route('admin.tahun_ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'regex:/^\d{4}\/\d{4}$/', 'unique:tahun_ajaran,nama,' . $id],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'nama.regex' => 'Format Nama Tahun Ajaran harus YYYY/YYYY (Contoh: 2024/2025).',
            'nama.unique' => 'Tahun Ajaran ini sudah ada dalam sistem.',
            'tanggal_selesai.after' => 'Tanggal Selesai harus lebih besar dari Tanggal Mulai.',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['is_aktif'] = $request->has('is_aktif');

        $tahunAjaran->update($data);

        return redirect()->route('admin.tahun_ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Proteksi Hapus: Cek Relasi
        if ($tahunAjaran->kelas()->exists() || $tahunAjaran->periodePenilaian()->exists()) {
            return redirect()->back()->with('error', 'Tidak bisa dihapus, Tahun Ajaran sudah digunakan pada data Kelas atau Periode.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun_ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        $tahunAjaran->is_aktif = !$tahunAjaran->is_aktif;
        $tahunAjaran->save();

        return redirect()->route('admin.tahun_ajaran.index')
                         ->with('success', 'Status tahun ajaran berhasil diubah');
    }
}
