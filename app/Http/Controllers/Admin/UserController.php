<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_role')) {
            $roleFilter = strtolower($request->filter_role);
            if ($roleFilter !== 'semua role' && $roleFilter !== '') {
                $query->where('role', $roleFilter);
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.user', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $plainPassword = $data['password']; // Simpan plain password untuk email
        $data['password'] = Hash::make($plainPassword);
        $data['is_active'] = true;

        $user = User::create($data);

        // Kirim email notifikasi ke user baru
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $plainPassword));
        } catch (\Exception $e) {
            // Jika email gagal, user tetap tersimpan tapi beri peringatan minor
            return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan, namun email notifikasi gagal terkirim.');
        }

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan dan email notifikasi telah dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.detail_user', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id_user) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        try {
            $user->delete();
            return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user. Pastikan user tidak memiliki data terkait (seperti penilaian atau portofolio).');
        }
    }

    /**
     * Toggle the active status of the user.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id_user) {
            return redirect()->back()->with('error', 'Tidak bisa menonaktifkan akun sendiri');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'Status user berhasil diubah');
    }
}
