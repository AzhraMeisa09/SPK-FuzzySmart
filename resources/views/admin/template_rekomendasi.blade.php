@extends('layouts.app')
@section('title', 'Template Rekomendasi')
@section('page-title', 'Pustaka Narasi')

@section('content')

@php
    $totalTemplates = $templates->total();
    $totalMB = \App\Models\TemplateRekomendasi::where('kategori', 'MB')->count();
    $totalBSH = \App\Models\TemplateRekomendasi::where('kategori', 'BSH')->count();
    $totalBSB = \App\Models\TemplateRekomendasi::where('kategori', 'BSB')->count();
    $subkriteriaCount = \App\Models\Subkriteria::count();
    $coveredSubkriteria = \App\Models\TemplateRekomendasi::distinct('subkriteria_id')->count('subkriteria_id');
@endphp

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: {{ session('edit_id') ? 'true' : 'false' }},
    showDelete: false,
    editData: {
        id: '{{ old('id', session('edit_data.id')) }}',
        subkriteria_id: '{{ old('subkriteria_id', session('edit_data.subkriteria_id')) }}',
        kategori: '{{ old('kategori', session('edit_data.kategori')) }}',
        prioritas: '{{ old('prioritas', session('edit_data.prioritas')) }}',
        isi: '{{ old('isi', session('edit_data.isi')) }}'
    },
    deleteData: {},
    openEdit(t) { 
        this.editData = {
            id: t.id,
            subkriteria_id: t.subkriteria_id,
            kategori: t.kategori,
            prioritas: t.prioritas,
            isi: t.isi
        }; 
        this.showEdit = true; 
    },
    openDelete(t) { this.deleteData = t; this.showDelete = true; }
}" class="space-y-6">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        @php
            $stats = [
                ['label' => 'Total Narasi', 'value' => $totalTemplates, 'color' => '#64748b'],
                ['label' => 'Capaian MB',   'value' => $totalMB,        'color' => '#ef4444'],
                ['label' => 'Capaian BSH',  'value' => $totalBSH,       'color' => '#f59e0b'],
                ['label' => 'Capaian BSB',  'value' => $totalBSB,       'color' => '#22c55e'],
                ['label' => 'Cakupan',      'value' => $coveredSubkriteria . '/' . $subkriteriaCount, 'color' => '#3b82f6'],
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
                <h2 class="text-xl font-bold text-var(--text-1) tracking-tight">Pustaka Narasi Otomatis</h2>
                <p class="text-xs mt-1 text-var(--text-3) font-medium">Kustomisasi output rekomendasi berdasarkan capaian indikator subkriteria.</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <form action="{{ route('admin.template-rekomendasi.generate') }}" method="POST" onsubmit="return confirm('Generate template default?')">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl border border-blue-100 bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        Auto-Generate
                    </button>
                </form>
                <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                    Tambah Template
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
                    <th class="w-64">Indikator Subkriteria</th>
                    <th class="text-center w-24">Capaian</th>
                    <th>Narasi Rekomendasi</th>
                    <th class="text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($templates as $t)
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="align-top py-5">
                            @if($t->subkriteria)
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-var(--accent) mb-1">{{ $t->subkriteria->kriteria->nama }}</span>
                                    <span class="text-xs font-bold text-var(--text-1) leading-tight tracking-tight">{{ $t->subkriteria->nama }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-gray-400 italic">Berlaku Umum</span>
                            @endif
                        </td>
                        <td class="text-center align-top py-5">
                            @php
                                $badgeClass = match($t->kategori) {
                                    'BSB' => 'badge-bsb shadow-[0_0_8px_rgba(132,147,74,0.3)]',
                                    'BSH' => 'badge-bsh shadow-[0_0_8px_rgba(245,158,11,0.2)]',
                                    'MB'  => 'badge-mb shadow-[0_0_8px_rgba(239,68,68,0.2)]',
                                    default => 'bg-gray-100 text-gray-600'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} border border-current/10">{{ $t->kategori }}</span>
                        </td>
                        <td class="align-top py-5">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 relative group/narasi">
                                <p class="text-xs text-var(--text-2) leading-relaxed font-medium italic">
                                    "{{ $t->isi }}"
                                </p>
                            </div>
                        </td>
                        <td class="text-center align-top py-5">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit({{ json_encode($t) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({{ json_encode($t) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Pustaka template narasi masih kosong.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
    <div class="mt-4">
        {{ $templates->links() }}
    </div>
    @endif

    {{-- ── MODALS ── --}}
    
    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
    <div x-show="showAdd" x-transition.opacity @keydown.escape.window="showAdd = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form action="{{ route('admin.template-rekomendasi.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-var(--text-1) tracking-tight">Tambah Template</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Subkriteria <span class="text-red-500">*</span></label>
                            <select name="subkriteria_id" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" required>
                                <option value="">Pilih Subkriteria</option>
                                @foreach($subkriteria->groupBy('kriteria.nama') as $kriteriaNama => $items)
                                    <optgroup label="{{ $kriteriaNama }}">
                                        @foreach($items as $s)
                                            <option value="{{ $s->id_subkriteria }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Capaian <span class="text-red-500">*</span></label>
                            <select name="kategori" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Urutan / Prioritas</label>
                        <select name="prioritas" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            @foreach($prioritasList as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Narasi Rekomendasi <span class="text-red-500">*</span></label>
                        <textarea name="isi" rows="4" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-medium text-xs p-4 resize-none" placeholder="Cth: @{{ nama_siswa }} telah mampu..." required></textarea>
                        <p class="mt-2 text-[9px] text-blue-600 font-bold bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 inline-block">Variabel: <b>@{{ nama_siswa }}</b></p>
                    </div>
                </div>
                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn btn-green px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ url('admin/template-rekomendasi') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-var(--text-1) tracking-tight">Edit Template</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-8 py-6 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Subkriteria</label>
                            <select name="subkriteria_id" x-model="editData.subkriteria_id" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="">Pilih Subkriteria</option>
                                @foreach($subkriteria->groupBy('kriteria.nama') as $kriteriaNama => $items)
                                    <optgroup label="{{ $kriteriaNama }}">
                                        @foreach($items as $s)
                                            <option value="{{ $s->id_subkriteria }}">{{ $s->nama }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold">Capaian</label>
                            <select name="kategori" x-model="editData.kategori" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                @foreach($kategoriList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Urutan / Prioritas</label>
                        <select name="prioritas" x-model="editData.prioritas" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                            @foreach($prioritasList as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold">Narasi Rekomendasi</label>
                        <textarea name="isi" x-model="editData.isi" rows="4" class="form-input rounded-xl bg-var(--bg) border-var(--border) font-medium text-xs p-4 resize-none"></textarea>
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
            <form :action="'{{ url('admin/template-rekomendasi') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Template?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6">Aksi ini bersifat permanen.</p>
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
