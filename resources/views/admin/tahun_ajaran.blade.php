@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        nama: '{{ old('nama', session('edit_data.nama')) }}',
        mulai: '{{ old('tanggal_mulai', session('edit_data.tanggal_mulai')) }}',
        selesai: '{{ old('tanggal_selesai', session('edit_data.tanggal_selesai')) }}',
        is_aktif: '{{ old('is_aktif', session('edit_data.is_aktif')) }}'
    },
    deleteData: {},
    openEdit(t) { 
        this.editData = {
            id: t.id_tahun_ajaran,
            nama: t.nama,
            mulai: t.tanggal_mulai.split('T')[0],
            selesai: t.tanggal_selesai.split('T')[0],
            is_aktif: t.is_aktif
        }; 
        this.showEdit = true; 
    },
    openDelete(t) { this.deleteData = t; this.showDelete = true; }
}" class="space-y-6">

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Manajemen Tahun Ajaran</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Kelola konfigurasi periode akademik aktif untuk sistem penilaian.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.tahun_ajaran.index') }}" method="GET" class="w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari tahun ajaran...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Tahun Ajaran
                </button>
            </div>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-20 text-center">No</th>
                    <th>Tahun Ajaran</th>
                    <th>Periode</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tahuns as $i => $t)
                    <tr class="hover:bg-var(--bg) transition-colors group">
                        <td class="text-center py-4 text-var(--text-3) text-xs font-bold">{{ $tahuns->firstItem() + $i }}</td>
                        <td class="py-4">
                            <span class="font-semibold text-var(--text-1) tracking-tight">{{ $t->nama }}</span>
                        </td>
                        <td class="py-4">
                            <div class="flex flex-col gap-1 text-[11px] font-bold">
                                <div class="flex items-center gap-2 text-var(--text-2)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ \Carbon\Carbon::parse($t->tanggal_mulai)->translatedFormat('d M Y') }}
                                </div>
                                <div class="flex items-center gap-2 text-var(--text-3)">
                                    <svg class="w-3 h-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ \Carbon\Carbon::parse($t->tanggal_selesai)->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <form action="{{ route('admin.tahun_ajaran.toggle', $t) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $t->is_aktif ? 'badge-aktif shadow-[0_0_8px_rgba(34,197,94,0.2)]' : 'badge-nonaktif' }} transition-all hover:scale-105 active:scale-95">
                                    {{ $t->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit({{ json_encode($t) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({ id: '{{ $t->id_tahun_ajaran }}', nama: '{{ addslashes($t->nama) }}' })" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data tahun ajaran yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($tahuns->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $tahuns->links() }}
        </div>
        @endif
    </div>

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Tahun Ajaran</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('nama') border-red-500 @enderror" placeholder="Contoh: 2024/2025">
                        @error('nama') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('tanggal_mulai') border-red-500 @enderror">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('tanggal_selesai') border-red-500 @enderror">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif') ? 'checked' : '' }} class="w-5 h-5 rounded-lg text-var(--accent) focus:ring-var(--accent) border-gray-300">
                        <span class="text-xs font-bold text-gray-700">Aktifkan Tahun Ajaran Ini Secara Otomatis</span>
                    </label>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-100">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.tahun_ajaran.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="id" :value="editData.id">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Edit Tahun Ajaran</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" x-model="editData.nama" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" x-model="editData.mulai" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" x-model="editData.selesai" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="is_aktif" value="1" x-model="editData.is_aktif" class="w-5 h-5 rounded-lg text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-xs font-bold text-gray-700">Tahun Ajaran Aktif</span>
                    </label>
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
            <form :action="'{{ route('admin.tahun_ajaran.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Tahun Ajaran?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <p class="text-[10px] text-red-600 bg-red-50 border border-red-100 rounded-xl p-4 leading-relaxed italic">⚠️ Data ini hanya bisa dihapus jika belum digunakan pada data Kelas atau Periode Penilaian.</p>
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
