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
            id: t.id,
            nama: t.nama,
            mulai: t.tanggal_mulai.split('T')[0],
            selesai: t.tanggal_selesai.split('T')[0],
            is_aktif: t.is_aktif
        }; 
        this.showEdit = true; 
    },
    openDelete(t) { this.deleteData = t; this.showDelete = true; }
}" class="space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Tahun Ajaran</h1>
            <p class="text-xs text-gray-500">Manajemen periode akademik aktif</p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Tahun Ajaran
        </button>
    </div>

    <form action="{{ route('admin.tahun_ajaran.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari tahun ajaran...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
        </div>
    </form>

    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun Ajaran</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tahuns as $i => $t)
                    <tr>
                        <td class="text-gray-400 text-xs">{{ $tahuns->firstItem() + $i }}</td>
                        <td><span class="font-black text-gray-800">{{ $t->nama }}</span></td>
                        <td class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($t->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($t->tanggal_selesai)->format('d-m-Y') }}</td>
                        <td>
                            <form action="{{ route('admin.tahun_ajaran.toggle', $t->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $t->is_aktif ? 'badge-aktif' : 'badge-nonaktif' }} transition-opacity hover:opacity-75">
                                    {{ $t->is_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <button @click="openEdit({{ json_encode($t) }})" class="btn btn-xs btn-blue">Edit</button>
                                <button @click="openDelete({ id: {{ $t->id }}, nama: '{{ $t->nama }}' })" class="btn btn-xs btn-gray text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">Belum ada data tahun ajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($tahuns->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $tahuns->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form action="{{ route('admin.tahun_ajaran.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Tambah Tahun Ajaran</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input @error('nama') border-red-500 @enderror" placeholder="Contoh: 2024/2025">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="form-input @error('tanggal_mulai') border-red-500 @enderror">
                        @error('tanggal_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="form-input @error('tanggal_selesai') border-red-500 @enderror">
                        @error('tanggal_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif') ? 'checked' : '' }} class="w-4 h-4 rounded text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Jadikan Tahun Ajaran Aktif</span>
                    </label>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Tahun Ajaran</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <form :action="'{{ route('admin.tahun_ajaran.index') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit Tahun Ajaran</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" x-model="editData.nama" class="form-input @error('nama') border-red-500 @enderror">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" x-model="editData.mulai" class="form-input @error('tanggal_mulai') border-red-500 @enderror">
                        @error('tanggal_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" x-model="editData.selesai" class="form-input @error('tanggal_selesai') border-red-500 @enderror">
                        @error('tanggal_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" x-model="editData.is_aktif" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Jadikan Tahun Ajaran Aktif</span>
                    </label>
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
            <form :action="'{{ route('admin.tahun_ajaran.index') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-5 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                    <h3 class="font-black text-gray-900 text-base mb-2">Hapus Tahun Ajaran?</h3>
                    <p class="text-sm font-bold text-gray-800 mb-3" x-text="deleteData.nama"></p>
                    <p class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg p-2">⚠️ Data ini hanya bisa dihapus jika belum digunakan pada data Kelas atau Periode Penilaian.</p>
                </div>
                <div class="px-6 pb-5 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
