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
}" class="space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-black text-gray-900">Kriteria Penilaian</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Total bobot: <span class="font-bold {{ abs($totalBobot - 1.0) < 0.0001 ? 'text-green-600' : 'text-red-500' }}">{{ number_format($totalBobot * 100, 0) }}%</span> &bull; {{ $kriterias->count() }} kriteria terdaftar
            </p>
        </div>
        <button @click="showAdd = true" class="btn btn-green">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Kriteria
        </button>
    </div>

    <!-- Bobot validation banner -->
    @if(abs($totalBobot - 1.0) < 0.0001)
    <div class="p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-green-800 font-medium">Validasi bobot: Total bobot semua kriteria sudah mencapai 100% ✓</p>
    </div>
    @else
    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-amber-800 font-medium">Validasi bobot: Total bobot saat ini {{ number_format($totalBobot * 100, 0) }}%. Harap sesuaikan agar mencapai 100%.</p>
    </div>
    @endif

    <form action="{{ route('admin.kriteria.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm mt-5 mb-4">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari kriteria...">
        </div>
        <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
            <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
        </div>
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Bobot (Desimal)</th>
                    <th>Bobot (%)</th>
                    <th>Subkriteria</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kriterias as $k)
                    <tr>
                        <td><span class="badge badge-blue font-mono">{{ $k->kode }}</span></td>
                        <td><span class="font-semibold text-gray-800">{{ $k->nama }}</span></td>
                        <td><span class="font-black text-gray-700">{{ number_format($k->bobot, 2) }}</span></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-24 progress-track">
                                    <div class="progress-fill progress-green" style="width: {{ $k->bobot * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 font-medium">{{ number_format($k->bobot * 100, 0) }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="font-bold text-gray-600 text-xs">{{ $k->subkriteria_count }} item</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $k->id]) }}"
                                   class="btn btn-xs btn-green" title="Kelola Subkriteria">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    Kelola
                                </a>
                                <button @click="openEdit({{ json_encode($k) }})" class="btn btn-xs btn-blue">Edit</button>
                                <button @click="openDelete({{ json_encode($k) }})" class="btn btn-xs btn-gray text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">Belum ada data kriteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ========= MODAL TAMBAH ========= --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.kriteria.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Tambah Kriteria</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Kriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input @error('nama') border-red-500 @enderror" placeholder="Contoh: Nilai Agama dan Moral">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bobot <span class="text-red-500">*</span></label>
                        <input type="number" name="bobot" value="{{ old('bobot') }}" step="0.01" min="0" max="1" class="form-input @error('bobot') border-red-500 @enderror" placeholder="0.00 – 1.00">
                        <p class="text-[11px] text-gray-400 mt-1">Gunakan desimal (contoh: 0.25 untuk 25%). Total harus 1.00.</p>
                        @error('bobot') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Kriteria</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- ========= MODAL EDIT ========= --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/kriteria') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit Kriteria</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Kode Kriteria</label>
                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 font-mono text-sm" x-text="editData.kode"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Kriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" x-model="editData.nama" class="form-input @error('nama') border-red-500 @enderror">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bobot <span class="text-red-500">*</span></label>
                        <input type="number" name="bobot" x-model="editData.bobot" step="0.01" min="0" max="1" class="form-input @error('bobot') border-red-500 @enderror">
                        @error('bobot') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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

    {{-- ========= MODAL HAPUS ========= --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/kriteria') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="font-black text-gray-900 text-lg mb-2">Hapus Kriteria?</h3>
                    <p class="text-sm text-gray-500 mb-1">Menghapus kriteria akan menghapus semua data terkait.</p>
                    <div class="py-2 px-3 bg-gray-50 rounded-lg mt-3">
                        <p class="text-sm font-bold text-gray-800" x-text="`${deleteData.kode} — ${deleteData.nama}`"></p>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center font-bold">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center font-bold shadow-sm shadow-red-200">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
