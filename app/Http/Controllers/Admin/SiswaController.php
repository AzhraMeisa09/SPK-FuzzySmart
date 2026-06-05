<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Siswa::query()->with(['kelas', 'wali']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_kelas')) {
            $query->where('id_kelas', $request->filter_kelas);
        }

        $siswa = $query->latest()->paginate(10)->withQueryString();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        // Hanya ambil user dengan role wali_murid
        $waliMurid = User::where('role', 'wali_murid')->where('is_active', true)->get();

        return view('admin.siswa', compact('siswa', 'kelas', 'waliMurid'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_siswa' => 'nullable|string|max:10|unique:siswa,id_siswa',
            'kode' => 'nullable|string|max:10|unique:siswa,kode',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'wali_murid_id' => 'nullable|exists:users,id_user',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kode.unique' => 'NISN sudah terdaftar.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->except(['kode_registrasi']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        // Auto-generate kode registrasi unik
        $data['kode_registrasi'] = Siswa::generateKodeRegistrasi();

        $siswa = Siswa::create($data);

        if ($request->filled('wali_murid_id')) {
            $siswa->wali()->sync([$request->wali_murid_id]);
        }

        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $siswa = Siswa::with(['kelas', 'wali'])->findOrFail($id);
        return view('admin.detail_siswa', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'id_siswa' => 'nullable|string|max:10|unique:siswa,id_siswa,' . $id . ',id_siswa',
            'kode' => 'nullable|string|max:10|unique:siswa,kode,' . $id . ',id_siswa',
            'kelas_id' => 'required|exists:kelas,id_kelas',
            'wali_murid_id' => 'nullable|exists:users,id_user',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kode.unique' => 'NISN sudah terdaftar.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Jangan overwrite kode_registrasi saat update biasa
        $data = $request->except(['kode_registrasi']);

        if ($request->hasFile('foto')) {
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);

        if ($request->filled('wali_murid_id')) {
            $siswa->wali()->syncWithoutDetaching([$request->wali_murid_id]);
        } else {
            // Only detach if the admin explicitly chose "-- Tidak ditautkan --" 
            // and we know it's a single guardian sync (to avoid removing guardians added by parents)
            // But for simplicity, we'll just not detach if it's empty, 
            // so parents can keep their linked children even if admin edits the student.
            if ($request->has('wali_murid_id') && empty($request->wali_murid_id)) {
                 $siswa->wali()->detach();
            }
        }

        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Generate ulang kode registrasi siswa.
     */
    public function regenerateKode($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->kode_registrasi = Siswa::generateKodeRegistrasi();
        $siswa->save();

        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Kode registrasi ' . $siswa->name . ' berhasil diperbarui: ' . $siswa->kode_registrasi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();

        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil dihapus.');
    }
}
