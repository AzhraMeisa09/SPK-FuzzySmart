@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')

@php
    $totalUsers = \App\Models\User::count();
    $totalAdmin = \App\Models\User::where('role', 'admin')->count();
    $totalGuru  = \App\Models\User::where('role', 'guru')->count();
    $totalKepsek = \App\Models\User::where('role', 'kepala_sekolah')->count();
    $totalWali  = \App\Models\User::where('role', 'wali_murid')->count();
@endphp

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
}" class="space-y-6">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        @php
            $stats = [
                ['label' => 'Total Akun', 'value' => $totalUsers, 'color' => '#64748b'],
                ['label' => 'Admin',      'value' => $totalAdmin, 'color' => '#8b5cf6'],
                ['label' => 'Guru',       'value' => $totalGuru,  'color' => '#3b82f6'],
                ['label' => 'Kepsek',     'value' => $totalKepsek,'color' => '#f59e0b'],
                ['label' => 'Wali',       'value' => $totalWali,  'color' => '#84934A'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card p-5 shadow-xl border-none flex flex-col items-center justify-center text-center group hover:translate-y-[-2px] transition-all duration-300">
            <span class="text-[9px] font-bold text-gray-400 mb-2 group-hover:text-gray-500 transition-colors">{{ $s['label'] }}</span>
            <span class="text-2xl font-bold tracking-tighter" style="color: {{ $s['color'] }}">{{ $s['value'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Manajemen User</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Kelola hak akses administrator, pendidik, dan wali murid dalam sistem.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.user.index') }}" method="GET" class="flex flex-wrap gap-2 items-center w-full lg:w-auto">
                    <div class="search-box">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama atau email...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div class="relative">
                        <select name="filter_role" onchange="this.form.submit()" class="form-select bg-var(--bg) border-var(--border) rounded-xl text-[13px] font-bold h-[42px] min-w-[140px]" style="padding-left: 16px;">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('filter_role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="guru" {{ request('filter_role') == 'guru' ? 'selected' : '' }}>Guru</option>
                            <option value="kepala_sekolah" {{ request('filter_role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepsek</option>
                            <option value="wali_murid" {{ request('filter_role') == 'wali_murid' ? 'selected' : '' }}>Wali</option>
                        </select>
                    </div>
                </form>

        <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah User
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50/50 border border-green-100 text-green-700 rounded-2xl text-[10px] font-bold flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th>Identitas Pengguna</th>
                    <th>Kontak & Email</th>
                    <th>Otoritas</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $i => $u)
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="text-var(--text-3) text-[11px] font-bold">{{ $users->firstItem() + $i }}</td>
                        <td class="py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-var(--accent-lt) flex items-center justify-center text-xs font-bold text-var(--accent) border border-var(--accent)/10 shadow-sm">{{ strtoupper(substr($u->nama_lengkap, 0, 1)) }}</div>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-var(--text-1) leading-tight">{{ $u->nama_lengkap }}</span>
                                    <span class="text-[10px] text-var(--text-3) font-medium mt-0.5">@ {{ $u->username }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-xs text-var(--text-2)">
                                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $u->email }}
                                </div>
                                @if($u->no_hp)
                                <div class="flex items-center gap-2 text-[10px] text-var(--text-3) font-medium">
                                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $u->no_hp }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 text-center">
                            <span class="badge {{ $u->role == 'admin' ? 'bg-purple-50 text-purple-700 border-purple-100' : ($u->role == 'guru' ? 'badge-blue shadow-[0_0_8px_rgba(59,130,246,0.1)]' : ($u->role == 'kepala_sekolah' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-gray-100 text-gray-600')) }}">
                                {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                            </span>
                        </td>
                        <td class="text-center py-4">
                            <form action="{{ route('admin.user.toggle', $u->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $u->is_active ? 'badge-aktif shadow-[0_0_8px_rgba(132,147,74,0.15)]' : 'badge-nonaktif' }} transition-all hover:scale-105 active:scale-95" {{ auth()->id() == $u->id ? 'disabled' : '' }}>
                                    {{ $u->is_active ? 'Aktif' : 'Terblokir' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.user.show', $u->id) }}" class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group" title="Detail Profil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                </a>
                                <button @click="openEdit({{ Js::from($u) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({{ Js::from($u) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" {{ auth()->id() == $u->id ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Tidak ada data pengguna yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Akun Baru</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Contoh: Ahmad Subardjo, S.Pd">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="ahmad_subardjo">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Email Aktif <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="ahmad@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Otoritas / Role <span class="text-red-500">*</span></label>
                        <select name="role" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            <option value="guru">Guru / Pendidik</option>
                            <option value="admin">Administrator</option>
                            <option value="kepala_sekolah">Kepala Sekolah</option>
                            <option value="wali_murid">Wali Murid</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Ulangi password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="08xxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Alamat Tinggal</label>
                        <textarea name="alamat" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs h-24 resize-none p-4" placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.user.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Edit Profil Pengguna</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" x-model="editData.nama" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Username</label>
                            <input type="text" name="username" x-model="editData.username" required class="form-input rounded-xl bg-gray-50 border-var(--border) font-bold text-xs" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Email</label>
                            <input type="email" name="email" x-model="editData.email" required class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Role</label>
                            <select name="role" x-model="editData.role" required class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="guru">Guru</option>
                                <option value="admin">Admin</option>
                                <option value="kepala_sekolah">Kepsek</option>
                                <option value="wali_murid">Wali Murid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Status Akses</label>
                            <select name="is_active" x-model="editData.is_active" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="1">Aktif</option>
                                <option value="0">Blokir</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Update Password <span class="text-gray-400 font-normal lowercase">(opsional)</span></label>
                        <input type="password" name="password" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" placeholder="Biarkan kosong jika tidak ingin mengubah">
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showEdit = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.user.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Pengguna?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6" x-text="deleteData.nama_lengkap"></p>
                    <div class="p-4 rounded-xl bg-red-50/50 border border-red-100 text-[10px] font-bold text-red-700">
                        ⚠️ Seluruh data akses untuk akun ini akan dihapus permanen!
                    </div>
                </div>
                <div class="px-8 pb-8 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-var(--text-3) bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition-all shadow-lg shadow-red-100">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
