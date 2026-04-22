@extends('layouts.app')
@section('title', 'Manajemen Subkriteria')
@section('page-title', 'Subkriteria')

@section('content')
@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        kode: '{{ old('kode', session('edit_data.kode')) }}',
        nama: '{{ old('nama', session('edit_data.nama')) }}',
        rubrik_mb: '{{ old('rubrik_mb', session('edit_data.rubrik_mb')) }}',
        rubrik_bsh: '{{ old('rubrik_bsh', session('edit_data.rubrik_bsh')) }}',
        rubrik_bsb: '{{ old('rubrik_bsb', session('edit_data.rubrik_bsb')) }}'
    },
    deleteData: {},
    openEdit(s) { 
        this.editData = {
            id: s.id,
            kode: s.kode,
            nama: s.nama,
            rubrik_mb: s.rubrik_mb,
            rubrik_bsh: s.rubrik_bsh,
            rubrik_bsb: s.rubrik_bsb
        }; 
        this.showEdit = true; 
    },
    openDelete(s) { this.deleteData = s; this.showDelete = true; }
}" class="space-y-5">

    <!-- Header + breadcrumb -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kriteria.index') }}"
           class="p-2 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-green-600 hover:border-green-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-base font-black text-gray-900">Manajemen Subkriteria</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Kriteria Induk: <span class="font-bold text-green-700">{{ $selectedKriteria ? $selectedKriteria->kode . ' — ' . $selectedKriteria->nama : 'Pilih Kriteria' }}</span>
            </p>
        </div>
        @if($selectedKriteria)
        <button @click="showAdd = true" class="btn btn-green shadow-sm shadow-green-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Subkriteria
        </button>
        @endif
    </div>

    <!-- Kriteria Switcher Banner -->
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex flex-col md:flex-row items-start md:items-center gap-4">
        @if($selectedKriteria)
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-black text-sm flex items-center justify-center flex-shrink-0">
            {{ $selectedKriteria->kode }}
        </div>
        <div class="flex-1">
            <p class="font-bold text-blue-900 leading-tight">{{ $selectedKriteria->nama }}</p>
            <p class="text-xs text-blue-600 mt-0.5">{{ $subkriterias->count() }} subkriteria terdaftar • Bobot kriteria induk: {{ number_format($selectedKriteria->bobot * 100, 0) }}%</p>
        </div>
        @else
        <div class="flex-1"><p class="text-sm font-medium text-blue-800">Silakan pilih kriteria untuk mengelola subkriteria.</p></div>
        @endif
        
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[10px] font-black uppercase text-blue-300 mr-1 hidden md:inline">Pilih Kriteria:</span>
            @foreach($allKriteria as $k)
                <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $k->id]) }}"
                   class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all
                          {{ $selectedKriteria && $k->id == $selectedKriteria->id ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white text-gray-500 border border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
                    {{ $k->kode }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card overflow-hidden">
        @if($selectedKriteria)
            <form action="{{ route('admin.subkriteria.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 border-b border-gray-100">
                <input type="hidden" name="kriteria_id" value="{{ $selectedKriteria->id }}">
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full py-2.5" style="padding-left: 42px;" placeholder="Cari subkriteria...">
                </div>
                <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
                    <button type="submit" class="btn btn-blue py-2.5 px-6 shadow-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $selectedKriteria->id]) }}" class="btn btn-gray py-2.5 px-4 outline-none border-none">Reset</a>
                    @endif
                </div>
            </form>
        @endif

        @if($selectedKriteria && $subkriterias->count() > 0)
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th width="80">Kode</th>
                            <th width="250">Nama Subkriteria</th>
                            <th class="text-red-600">MB</th>
                            <th class="text-amber-600">BSH</th>
                            <th class="text-green-600">BSB</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subkriterias as $s)
                            <tr>
                                <td><span class="badge badge-blue font-mono text-xs">{{ $s->kode }}</span></td>
                                <td><span class="font-semibold text-gray-800 leading-snug block">{{ $s->nama }}</span></td>
                                <td><div class="text-[11px] text-red-700 leading-relaxed max-w-xs">{{ $s->rubrik_mb }}</div></td>
                                <td><div class="text-[11px] text-amber-700 leading-relaxed max-w-xs">{{ $s->rubrik_bsh }}</div></td>
                                <td><div class="text-[11px] text-green-700 leading-relaxed max-w-xs">{{ $s->rubrik_bsb }}</div></td>
                                <td>
                                    <div class="flex gap-1">
                                        <button @click="openEdit({{ json_encode($s) }})" class="btn btn-xs btn-blue">Edit</button>
                                        <button @click="openDelete({{ json_encode($s) }})" class="btn btn-xs btn-gray text-red-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($selectedKriteria)
            <div class="py-20 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-500 mb-1">Belum ada subkriteria</p>
                <p class="text-xs text-gray-400 mb-5">Kriteria {{ $selectedKriteria->nama }} belum memiliki detail penilaian.</p>
                <button @click="showAdd = true" class="btn btn-green">Tambah Subkriteria Pertama</button>
            </div>
        @else
            <div class="py-20 text-center text-gray-400"><p class="text-sm italic">Pilih kriteria untuk melihat data</p></div>
        @endif
    </div>

    {{-- ========= MODAL TAMBAH ========= --}}
    @if($selectedKriteria)
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-xl" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.subkriteria.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kriteria_id" value="{{ $selectedKriteria->id }}">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-gray-900">Tambah Subkriteria</h3>
                        <p class="text-xs text-gray-400">Ke Induk: {{ $selectedKriteria->kode }} — {{ $selectedKriteria->nama }}</p>
                    </div>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Subkriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" placeholder="Contoh: Berdoa dengan tertib">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-3">
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100">
                            <label class="form-label text-red-700 font-bold mb-2">Rubrik MB — Mulai Berkembang <span class="text-red-400">*</span></label>
                            <textarea name="rubrik_mb" class="form-textarea h-20 bg-white" placeholder="Siswa masih perlu bimbingan penuh...">{{ old('rubrik_mb') }}</textarea>
                            @error('rubrik_mb') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
                            <label class="form-label text-amber-700 font-bold mb-2">Rubrik BSH — Berkembang Sesuai Harapan <span class="text-amber-400">*</span></label>
                            <textarea name="rubrik_bsh" class="form-textarea h-20 bg-white" placeholder="Siswa sudah bisa melakukan secara mandiri...">{{ old('rubrik_bsh') }}</textarea>
                            @error('rubrik_bsh') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                            <label class="form-label text-green-700 font-bold mb-2">Rubrik BSB — Berkembang Sangat Baik <span class="text-green-400">*</span></label>
                            <textarea name="rubrik_bsb" class="form-textarea h-20 bg-white" placeholder="Siswa sudah mahir dan bisa membantu teman...">{{ old('rubrik_bsb') }}</textarea>
                            @error('rubrik_bsb') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showAdd = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-green">Simpan Subkriteria</button>
                </div>
            </form>
        </div>
    </div>
    </template>
    @endif

    {{-- ========= MODAL EDIT ========= --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-xl" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/subkriteria') }}/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-gray-900">Edit Subkriteria</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="form-group">
                        <label class="form-label">Kode Subkriteria</label>
                        <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 font-mono text-sm" x-text="editData.kode"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Subkriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" x-model="editData.nama" class="form-input">
                        @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-3">
                        <div class="form-group">
                            <label class="form-label text-red-700 font-bold">Rubrik MB</label>
                            <textarea name="rubrik_mb" x-model="editData.rubrik_mb" class="form-textarea h-20"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-amber-700 font-bold">Rubrik BSH</label>
                            <textarea name="rubrik_bsh" x-model="editData.rubrik_bsh" class="form-textarea h-20"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-green-700 font-bold">Rubrik BSB</label>
                            <textarea name="rubrik_bsb" x-model="editData.rubrik_bsb" class="form-textarea h-20"></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                    <button type="button" @click="showEdit = false" class="btn btn-gray">Batal</button>
                    <button type="submit" class="btn btn-blue shadow-sm shadow-blue-200">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- ========= MODAL HAPUS ========= --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/subkriteria') }}/' + deleteData.id" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="font-black text-gray-900 text-lg mb-2">Hapus Subkriteria?</h3>
                    <p class="text-sm text-gray-500 mb-1" x-text="deleteData.nama"></p>
                    <p class="text-[10px] text-red-600 font-bold bg-red-50 border border-red-100 rounded p-1.5 mt-4">⚠️ Data penilaian terkait subkriteria ini mungkin akan terganggu.</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 btn btn-gray justify-center font-bold">Batal</button>
                    <button type="submit" class="flex-1 btn btn-red justify-center font-bold shadow-sm shadow-red-200">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection


</div>
@endsection
