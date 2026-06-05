<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi wali murid.
     */
    public function register(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:50|unique:users,username',
            'email'        => 'required|string|email|max:50|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'nisn'         => 'required|string|max:10',
            'kode_registrasi' => 'required|string|max:10',
        ], [
            'username.unique'          => 'Username sudah digunakan.',
            'email.unique'             => 'Email sudah digunakan.',
            'password.min'             => 'Password minimal 8 karakter.',
            'password.confirmed'       => 'Konfirmasi password tidak cocok.',
            'nisn.required'            => 'NISN siswa wajib diisi.',
            'kode_registrasi.required' => 'Kode registrasi wajib diisi.',
        ]);

        // 2. Validasi NISN + Kode Registrasi (keduanya harus cocok)
        $siswa = Siswa::where(function($q) use ($request) {
                        $q->where('kode', $request->nisn)
                          ->orWhere('id_siswa', $request->nisn);
                    })
                    ->where('kode_registrasi', strtoupper(trim($request->kode_registrasi)))
                    ->first();

        if (!$siswa) {
            return back()->withErrors(['nisn' => 'NISN atau kode registrasi tidak valid.'])->withInput();
        }

        if ($siswa->wali_murid_id !== null) {
            return back()->withErrors(['nisn' => 'Siswa dengan NISN tersebut sudah terhubung dengan akun wali murid lain.'])->withInput();
        }

        // 3. Simpan User Wali Murid & Relasi di dalam transaksi
        DB::beginTransaction();
        try {
            // Buat akun wali murid
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'username'     => $request->username,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'role'         => 'wali_murid',
                'no_hp'        => $request->no_hp,
                'alamat'       => $request->alamat,
                'is_active'    => true,
            ]);

            // Update wali_murid_id di tabel siswa
            $siswa->update([
                'wali_murid_id' => $user->id_user
            ]);

            // Sinkronisasi tabel pivot wali_siswa (opsional tapi baik untuk konsistensi dengan yang ada di sistem)
            $siswa->wali()->syncWithoutDetaching([$user->id_user]);

            DB::commit();

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda telah terhubung dengan data anak. Silakan login.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem saat registrasi. Silakan coba lagi.'])->withInput();
        }
    }
}
