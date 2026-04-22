@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        nama: '{{ old('nama_lengkap', session('edit_data.nama_lengkap')) }}',
        username: '{{ old('username', session('edit_data.username')) }}',
        email: '{{ old('email', session('edit_data.email')) }}',
        role: '{{ old('role', session('edit_data.role')) }}',
        is_active: '{{ old('is_active', session('edit_data.is_active')) }}',
        no_hp: '{{ old('no_hp', session('edit_data.no_hp')) }}',
        alamat: '{{ old('alamat', session('edit_data.alamat')) }}'
    },
    deleteData: {},
    openEdit(u) { 
        this.editData = {
            id: u.id,
            nama: u.nama_lengkap,
            username: u.username,
            email: u.email,
            role: u.role,
            is_active: u.is_active ? '1' : '0',
            no_hp: u.no_hp || '',
            alamat: u.alamat || ''
        }; 
        this.showEdit = true; 
    },
    openDelete(u) { this.deleteData = u; this.showDelete = true; }
}" class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Manajemen User</h1>
            <p class="text-xs text-gray-500">{{ $users->total() }} akun terdaftar</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Tambah User
        </button>
    </div>

    <form action="{{ route('admin.user.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Ketik kata kunci (nama, username, atau email)...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
            <select name="filter_role" onchange="this.form.submit()" class="form-select w-full md:w-48 py-2.5 bg-gray-50 border-gray-200">
                <option value="" {{ empty(request('filter_role')) || strtolower(request('filter_role')) == 'semua role' ? 'selected' : '' }}>Semua Role</option>
                <option value="admin" {{ strtolower(request('filter_role')) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ strtolower(request('filter_role')) == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="kepala_sekolah" {{ strtolower(request('filter_role')) == 'kepala_sekolah' ? 'selected' : '' }}>Kepsek</option>
                <option value="wali_murid" {{ strtolower(request('filter_role')) == 'wali_murid' ? 'selected' : '' }}>Wali</option>
            </select>
        </div>
    </form>

    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center text-[11px] font-black text-green-700">{{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}</div>
                                <span class="font-semibold text-gray-800">{{ $u->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $u->username }}</code></td>
                        <td class="text-xs text-gray-500">{{ $u->email }}</td>
                        <td>
                            <span class="badge {{ $u->role == 'admin' ? 'bg-purple-100 text-purple-700' : ($u->role == 'guru' ? 'badge-blue' : ($u->role == 'kepala_sekolah' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600')) }}">
                                {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.user.toggle-status', $u->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $u->is_active ? 'badge-aktif' : 'badge-nonaktif' }} transition-opacity hover:opacity-75">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.user.show', $u->id) }}" class="btn btn-xs outline-gray text-gray-600 px-2 group hover:text-green-600 hover:border-green-200" title="Detail Profil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                </a>
                                <button @click="openEdit({{ json_encode($u) }})"
                                        class="btn btn-xs btn-blue" title="Edit Data">Edit</button>
                                <button @click="openDelete({ id: {{ $u->id }}, nama: '{{ addslashes($u->nama_lengkap) }}', username: '{{ $u->username }}' })"
                                        class="btn btn-xs btn-gray text-red-500" title="Hapus Data">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">Data user tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Tambah User</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input @error('nama_lengkap') border-red-500 @enderror" placeholder="Nama lengkap pengguna">
                        @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" value="{{ old('username') }}" class="form-input @error('username') border-red-500 @enderror" placeholder="username_unik">
                            @error('username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role <span class="text-red-500">*</span></label>
                            <select name="role" class="form-select @error('role') border-red-500 @enderror">
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepsek</option>
                                <option value="wali_murid" {{ old('role') == 'wali_murid' ? 'selected' : '' }}>Wali</option>
                            </select>
                            @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-input @error('email') border-red-500 @enderror" placeholder="email@sekolah.sch.id">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-input @error('no_hp') border-red-500 @enderror" placeholder="08xxxxxxxxxx">
                            @error('no_hp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat / Domisili</label>
                        <textarea name="alamat" class="form-input h-20 @error('alamat') border-red-500 @enderror" placeholder="Alamat pengguna...">{{ old('alamat') }}</textarea>
                        @error('alamat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror" placeholder="Minimal 6 karakter">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.user.index') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit User</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" x-model="editData.nama" class="form-input @error('nama_lengkap') border-red-500 @enderror">
                        @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group"><label class="form-label">Username</label><input type="text" name="username" x-model="editData.username" class="form-input bg-gray-50 border-gray-200" readonly></div>
                        <div class="form-group">
                            <label class="form-label">Role <span class="text-red-500">*</span></label>
                            <select name="role" x-model="editData.role" class="form-select @error('role') border-red-500 @enderror">
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="kepala_sekolah">Kepsek</option>
                                <option value="wali_murid">Wali</option>
                            </select>
                            @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" x-model="editData.email" class="form-input @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="no_hp" x-model="editData.no_hp" class="form-input @error('no_hp') border-red-500 @enderror">
                            @error('no_hp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat / Domisili</label>
                        <textarea name="alamat" x-model="editData.alamat" class="form-input h-20 @error('alamat') border-red-500 @enderror"></textarea>
                        @error('alamat') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror" placeholder="Biarkan kosong jika tidak diubah">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showEdit = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.user.index') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-5 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 text-base mb-2">Hapus User?</h3>
                    <p class="text-sm font-bold text-gray-800 mb-1" x-text="deleteData.nama"></p>
                    <p class="text-xs text-gray-500 mb-4" x-text="'@' + deleteData.username"></p>
                    <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg p-2">⚠️ Aksi ini tidak dapat dibatalkan!</p>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center">Hapus User</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
