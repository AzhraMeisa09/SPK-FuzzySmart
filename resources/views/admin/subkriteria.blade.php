@extends('layouts.app')
@section('title', 'Manajemen Subkriteria')
@section('page-title', 'Subkriteria')

@section('content')

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showImport: false,
    showDelete: false,
    editData: {
        id_subkriteria: '{{ old('id_subkriteria', session('edit_data.id_subkriteria')) }}',
        nama_subkriteria: '{{ old('nama_subkriteria', session('edit_data.nama_subkriteria')) }}',
        rubrik_mb: '{{ old('rubrik_mb', session('edit_data.rubrik_mb')) }}',
        rubrik_bsh: '{{ old('rubrik_bsh', session('edit_data.rubrik_bsh')) }}',
        rubrik_bsb: '{{ old('rubrik_bsb', session('edit_data.rubrik_bsb')) }}'
    },
    deleteData: {},
    openEdit(s) { 
        this.editData = {
            id_subkriteria: s.id_subkriteria,
            nama_subkriteria: s.nama_subkriteria,
            rubrik_mb: s.rubrik_mb,
            rubrik_bsh: s.rubrik_bsh,
            rubrik_bsb: s.rubrik_bsb
        }; 
        this.showEdit = true; 
    },
    openDelete(s) { this.deleteData = s; this.showDelete = true; }
}" class="space-y-5">

    <!-- Header + breadcrumb -->
    <div class="card p-5">
        <div class="flex flex-col md:flex-row items-center gap-4">
            <a href="{{ route('admin.kriteria.index') }}"
               class="p-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Manajemen Subkriteria</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">
                    Kriteria Induk: <span class="font-semibold text-gray-700">{{ $selectedKriteria ? $selectedKriteria->id_kriteria . ' — ' . $selectedKriteria->nama_kriteria : 'Pilih Kriteria' }}</span>
                </p>
            </div>
            @if($selectedKriteria)
            <div class="flex gap-2">
                <button @click="showImport = true" class="px-5 py-2 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Word
                </button>
                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2 rounded-xl flex items-center gap-2 font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Subkriteria
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Kriteria Switcher Banner -->
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex flex-col md:flex-row items-start md:items-center gap-4">
        @if($selectedKriteria)
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
            {{ $selectedKriteria->id_kriteria }}
        </div>
        <div class="flex-1">
            <p class="font-semibold text-blue-900 leading-tight">{{ $selectedKriteria->nama_kriteria }}</p>
            <p class="text-[10px] text-blue-600 mt-0.5 font-medium">{{ $subkriterias->count() }} subkriteria terdaftar • Bobot: {{ number_format($selectedKriteria->bobot * 100, 0) }}%</p>
        </div>
        @else
        <div class="flex-1"><p class="text-sm font-medium text-blue-800">Silakan pilih kriteria untuk mengelola subkriteria.</p></div>
        @endif
        
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[10px] font-bold text-blue-300 mr-1 hidden md:inline">Pilih Kriteria:</span>
            @foreach($allKriteria as $k)
                <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $k->id_kriteria]) }}"
                   class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold transition-all shadow-sm relative
                          {{ $selectedKriteria && $k->id_kriteria == $selectedKriteria->id_kriteria ? 'bg-blue-600 text-white shadow-blue-200' : ($k->is_aktif ? 'bg-white text-gray-500 border border-gray-200 hover:border-blue-300 hover:text-blue-600' : 'bg-gray-100 text-gray-400 border border-gray-200 italic') }}"
                   title="{{ $k->nama_kriteria }} {{ $k->is_aktif ? '' : '(Nonaktif)' }}">
                    {{ $k->id_kriteria }}
                    @if(!$k->is_aktif)
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <div class="card overflow-hidden">
        @if($selectedKriteria)
            <form action="{{ route('admin.subkriteria.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 bg-white p-4 border-b border-gray-100">
                <input type="hidden" name="kriteria_id" value="{{ $selectedKriteria->id_kriteria }}">
                <div class="relative flex-1 w-full text-var(--text-3)">
                    <svg class="absolute left-4.5 top-1/2 -translate-y-1/2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full bg-var(--bg) border-var(--border) rounded-xl" style="padding-left: 52px;" placeholder="Cari subkriteria...">
                </div>
                <div class="flex gap-2 w-full md:w-auto shrink-0 md:pl-2">
                    <button type="submit" class="btn btn-blue py-2 px-6 rounded-xl font-bold text-sm">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.subkriteria.index', ['kriteria_id' => $selectedKriteria->id_kriteria]) }}" class="btn btn-gray py-2 px-4 rounded-xl font-bold text-sm">Reset</a>
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
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subkriterias as $s)
                            <tr class="hover:bg-var(--bg) transition-colors">
                                <td><span class="badge badge-blue font-mono text-[10px]">{{ $s->id_subkriteria }}</span></td>
                                <td><span class="font-semibold text-gray-800 leading-snug block tracking-tight">{{ $s->nama_subkriteria }}</span></td>
                                <td><div class="text-[10px] text-red-700 leading-relaxed max-w-xs font-medium italic">{{ $s->rubrik_mb }}</div></td>
                                <td><div class="text-[10px] text-amber-700 leading-relaxed max-w-xs font-medium italic">{{ $s->rubrik_bsh }}</div></td>
                                <td><div class="text-[10px] text-green-700 leading-relaxed max-w-xs font-medium italic">{{ $s->rubrik_bsb }}</div></td>
                                <td class="py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEdit({{ json_encode($s) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="openDelete({{ json_encode($s) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-500 mb-1">Belum ada subkriteria</p>
                <p class="text-xs text-gray-400 mb-5">Kriteria {{ $selectedKriteria->nama_kriteria }} belum memiliki detail penilaian.</p>
                <button @click="showAdd = true" class="btn btn-green px-6 py-2 rounded-xl font-bold">Tambah Subkriteria Pertama</button>
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
                <input type="hidden" name="kriteria_id" value="{{ $selectedKriteria->id_kriteria }}">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Tambah Subkriteria</h3>
                        <p class="text-[10px] text-blue-600 font-bold">Induk: {{ $selectedKriteria->id_kriteria }} — {{ $selectedKriteria->nama_kriteria }}</p>
                    </div>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-4">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Subkriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_subkriteria" value="{{ old('nama_subkriteria') }}" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm" placeholder="Contoh: Berdoa dengan tertib">
                        @error('nama_subkriteria') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-3">
                        <div class="p-4 rounded-xl bg-red-50/50 border border-red-100">
                            <label class="form-label text-[10px] font-bold text-red-700 mb-2 block">Rubrik MB — Mulai Berkembang <span class="text-red-400">*</span></label>
                            <textarea name="rubrik_mb" class="form-input rounded-xl bg-white border-red-100 font-medium text-xs h-20" placeholder="Siswa masih perlu bimbingan penuh...">{{ old('rubrik_mb') }}</textarea>
                            @error('rubrik_mb') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100">
                            <label class="form-label text-[10px] font-bold text-amber-700 mb-2 block">Rubrik BSH — Berkembang Sesuai Harapan <span class="text-amber-400">*</span></label>
                            <textarea name="rubrik_bsh" class="form-input rounded-xl bg-white border-amber-100 font-medium text-xs h-20" placeholder="Siswa sudah bisa melakukan secara mandiri...">{{ old('rubrik_bsh') }}</textarea>
                            @error('rubrik_bsh') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="p-4 rounded-xl bg-green-50/50 border border-green-100">
                            <label class="form-label text-[10px] font-bold text-green-700 mb-2 block">Rubrik BSB — Berkembang Sangat Baik <span class="text-green-400">*</span></label>
                            <textarea name="rubrik_bsb" class="form-input rounded-xl bg-white border-green-100 font-medium text-xs h-20" placeholder="Siswa sudah mahir dan bisa membantu teman...">{{ old('rubrik_bsb') }}</textarea>
                            @error('rubrik_bsb') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-100">Simpan Subkriteria</button>
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
            <form :action="'{{ url('admin/subkriteria') }}/' + editData.id_subkriteria" method="POST">
                @csrf
                @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Edit Subkriteria</h3>
                        <p class="text-[10px] text-blue-600 font-bold" x-text="editData.id_subkriteria"></p>
                    </div>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-4">
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Nama Subkriteria <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_subkriteria" x-model="editData.nama_subkriteria" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-bold text-sm">
                    </div>
                    <div class="space-y-3">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-red-700 block mb-1.5">Rubrik MB</label>
                            <textarea name="rubrik_mb" x-model="editData.rubrik_mb" class="form-input rounded-xl bg-white border-red-100 font-medium text-xs h-20"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-amber-700 block mb-1.5">Rubrik BSH</label>
                            <textarea name="rubrik_bsh" x-model="editData.rubrik_bsh" class="form-input rounded-xl bg-white border-amber-100 font-medium text-xs h-20"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-green-700 block mb-1.5">Rubrik BSB</label>
                            <textarea name="rubrik_bsb" x-model="editData.rubrik_bsb" class="form-input rounded-xl bg-white border-green-100 font-medium text-xs h-20"></textarea>
                        </div>
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

    {{-- ========= MODAL HAPUS ========= --}}
    <template x-teleport="body">
    <div x-show="showDelete" x-transition.opacity @keydown.escape.window="showDelete = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-sm" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/subkriteria') }}/' + deleteData.id_subkriteria" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Subkriteria?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6 tracking-tight" x-text="deleteData.nama_subkriteria"></p>
                    <p class="text-[10px] text-red-600 bg-red-50 border border-red-100 rounded-xl p-4 leading-relaxed italic">⚠️ Data penilaian terkait subkriteria ini mungkin akan terganggu atau hilang.</p>
                </div>
                <div class="px-8 pb-8 flex gap-3">
                    <button type="button" @click="showDelete = false" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-var(--text-3) bg-gray-100 hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition-all shadow-lg shadow-red-100">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL IMPORT WORD --}}
    <template x-teleport="body">
    <div x-show="showImport" x-transition.opacity @keydown.escape.window="showImport = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-md" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.subkriteria.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kriteria_id" value="{{ $selectedKriteria->id_kriteria ?? '' }}">
                
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-blue-50/50">
                    <div>
                        <h3 class="text-base font-bold text-blue-800">Import dari Microsoft Word</h3>
                        <p class="text-[10px] text-blue-600 font-bold">Format: .docx (Tabel 5 Kolom)</p>
                    </div>
                    <button type="button" @click="showImport = false" class="p-2 rounded-xl hover:bg-blue-100 text-blue-400 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="px-8 py-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800 mb-2">Pilih File Dokumentasi (.docx)</h4>
                        <p class="text-[10px] text-gray-500 leading-relaxed italic px-4">
                            Pastikan file berisi tabel dengan urutan: <br>
                            <b>No &rarr; Subkriteria &rarr; MB &rarr; BSH &rarr; BSB</b>
                        </p>
                    </div>

                    <div class="form-group">
                        <input type="file" name="file" accept=".docx" required
                               class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-50 p-2 rounded-2xl border-2 border-dashed border-gray-200">
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-100 flex gap-3 bg-gray-50/50">
                    <button type="button" @click="showImport = false" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
