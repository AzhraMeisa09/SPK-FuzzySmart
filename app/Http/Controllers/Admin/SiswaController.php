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
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_kelas')) {
            $query->where('kelas_id', $request->filter_kelas);
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
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:10|unique:siswa,kode',
            'kelas_id' => 'required|exists:kelas,id',
            'wali_murid_id' => 'nullable|exists:users,id',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kode.unique' => 'NISN sudah terdaftar.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->all();

        if ($request->filled('wali_murid_id')) {
            $userWali = User::find($request->wali_murid_id);
            if ($userWali) {
                $data['nama_orang_tua'] = $userWali->nama_lengkap;
                $data['no_hp_orang_tua'] = $userWali->no_hp;
            }
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

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
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:10|unique:siswa,kode,' . $id,
            'kelas_id' => 'required|exists:kelas,id',
            'wali_murid_id' => 'nullable|exists:users,id',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_orang_tua' => 'nullable|string|max:255',
            'no_hp_orang_tua' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'kode.unique' => 'NISN sudah terdaftar.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->all();

        if ($request->filled('wali_murid_id')) {
            $userWali = User::find($request->wali_murid_id);
            if ($userWali) {
                $data['nama_orang_tua'] = $userWali->nama_lengkap;
                $data['no_hp_orang_tua'] = $userWali->no_hp;
            }
        }

        if ($request->hasFile('foto')) {
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);

        if ($request->filled('wali_murid_id')) {
            $siswa->wali()->sync([$request->wali_murid_id]);
        } else {
            $siswa->wali()->detach();
        }

        return redirect()->route('admin.siswa.index')
                         ->with('success', 'Data siswa berhasil diperbarui.');
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
