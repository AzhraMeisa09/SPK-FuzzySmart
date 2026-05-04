@extends('layouts.app')
@section('title', 'Kriteria Penilaian')
@section('page-title', 'Kriteria Penilaian')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        kode: '{{ old('kode', session('edit_data.kode')) }}',
        nama: '{{ old('nama', session('edit_data.nama')) }}',
        bobot: '{{ old('bobot', session('edit_data.bobot')) }}'
    },
    deleteData: {},
    openEdit(k) { 
        this.editData = {
            id: k.id,
            kode: k.kode,
            nama: k.nama,
            bobot: k.bobot
        }; 
        this.showEdit = true; 
    },
    openDelete(k) { this.deleteData = k; this.showDelete = true; }
}" class="space-y-6">

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Kriteria Penilaian</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">
                    Total Bobot: <span class="font-bold {{ abs($totalBobot - 1.0) < 0.0001 ? 'text-green-600' : 'text-red-500' }}">{{ number_format($totalBobot * 100, 0) }}%</span> &bull; {{ $kriterias->count() }} Kriteria Terdaftar
                </p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.kriteria.index') }}" method="GET" class="w-full lg:w-auto">
                    <div class="search-box lg:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari kriteria...">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>

                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Kriteria
                </button>
            </div>
        </div>
    </div>

    {{-- ── VALIDATION BANNER ── --}}
    @if(abs($totalBobot - 1.0) < 0.0001)
    <div class="card p-4 shadow-xl border-none bg-green-50 border border-green-100 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shadow-sm border border-green-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm text-green-800 font-bold tracking-tight">Status Bobot Valid</p>
            <p class="text-[11px] text-green-600 font-medium">Total bobot seluruh kriteria sudah mencapai 100%. Sistem siap digunakan.</p>
        </div>
    </div>
    @else
    <div class="card p-4 shadow-xl border-none bg-amber-50 border border-amber-100 flex items-center gap-4 animate-pulse">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shadow-sm border border-amber-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-sm text-amber-800 font-bold tracking-tight">Total Bobot Belum Sesuai</p>
            <p class="text-[11px] text-amber-600 font-medium">Total bobot saat ini {{ number_format($totalBobot * 100, 0) }}%. Harap sesuaikan agar total menjadi tepat 100%.</p>
        </div>
    </div>
    @endif

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-24">Kode</th>
                    <th>Kriteria Penilaian</th>
                    <th class="w-32">Bobot Desimal</th>
                    <th class="w-48">Progress Bobot</th>
                    <th class="w-32 text-center">Subkriteria</th>
                    <th class="w-36 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kriterias as $k)
                    <tr class="hover:bg-var(--bg) transition-colors group">
                        <td class="py-6">
                            <span class="badge badge-blue font-mono text-[10px]">{{ $k->kode }}</span>
                        </td>
                        <td class="py-6">
                            <span class="font-semibold text-var(--text-1) tracking-tight">{{ $k->nama }}</span>
                        </td>
                        <td class="py-6">
                            <span class="font-bold text-var(--text-2) font-mono">{{ number_format($k->bobot, 2) }}</span>
                        </td>
                        <td class="py-6">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                                    <div class="h-full bg-green-500 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(34,197,94,0.3)]" style="width: {{ $k->bobot * 100 }}%"></div>
                                </div>
                                <span class="text-[10px] text-var(--text-3) font-bold">{{ number_format($k->bobot * 100, 0) }}%</span>
                            </div>
                        </td>
                        <td class="text-center py-6">
                            <span class="px-3 py-1 rounded-lg bg-gray-100 text-var(--text-2) text-[10px] font-bold border border-gray-200">{{ $k->subkriteria_count }} Item</span>
                        </td>
                        <td class="text-center py-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $k->id]) }}"
                                   class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group" title="Kelola Subkriteria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                </a>
                                <button @click="openEdit({{ json_encode($k) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({{ json_encode($k) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data kriteria penilaian yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.kriteria.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Kriteria</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Kriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('nama') border-red-500 @enderror" placeholder="Contoh: Nilai Agama dan Moral">
                        @error('nama') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Bobot Kriteria (Desimal) <span class="text-red-500">*</span></label>
                        <input type="number" name="bobot" value="{{ old('bobot') }}" step="0.01" min="0" max="1" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm @error('bobot') border-red-500 @enderror" placeholder="0.00 – 1.00">
                        <p class="text-[10px] text-gray-400 mt-2 italic font-medium">Gunakan nilai desimal (contoh: 0.25 untuk 25%). Total bobot seluruh kriteria harus tepat 1.00.</p>
                        @error('bobot') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-100">Simpan Kriteria</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/kriteria') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Edit Kriteria</h3>
                        <p class="text-[10px] text-var(--accent) font-bold" x-text="editData.kode"></p>
                    </div>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Kriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" x-model="editData.nama" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Bobot Kriteria <span class="text-red-500">*</span></label>
                        <input type="number" name="bobot" x-model="editData.bobot" step="0.01" min="0" max="1" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
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
            <form :action="'{{ url('admin/kriteria') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Kriteria?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama"></p>
                    <p class="text-[10px] text-red-600 bg-red-50 border border-red-100 rounded-xl p-4 leading-relaxed italic">⚠️ Menghapus kriteria akan secara otomatis menghapus semua subkriteria dan data penilaian terkait.</p>
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
