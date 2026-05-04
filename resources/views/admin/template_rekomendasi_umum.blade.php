@extends('layouts.app')
@section('title', 'Narasi Global')
@section('page-title', 'Narasi Rekomendasi Global')

@section('content')

@php
    $totalTemplates = $templates->count();
    $totalMB = \App\Models\TemplateRekomendasiUmum::where('kategori', 'MB')->count();
    $totalBSH = \App\Models\TemplateRekomendasiUmum::where('kategori', 'BSH')->count();
    $totalBSB = \App\Models\TemplateRekomendasiUmum::where('kategori', 'BSB')->count();
    $totalUtama = \App\Models\TemplateRekomendasiUmum::where('prioritas', 'utama')->count();
@endphp

<div x-data="{
    showAdd: {{ $errors->any() && !session('edit_id') ? 'true' : 'false' }},
    showEdit: false,
    showDelete: false,
    categoriesWithUtama: @js($categoriesWithUtama),
    editData: {
        id: '',
        kategori: 'MB',
        isi: '',
        prioritas: 'alternatif'
    },
    addData: {
        kategori: 'MB',
        prioritas: 'alternatif'
    },
    deleteData: {
        id: '',
        kategori: ''
    },
    openEdit(t) { 
        this.editData = {
            id: t.id,
            kategori: t.kategori,
            isi: t.isi,
            prioritas: t.prioritas
        }; 
        this.showEdit = true; 
    },
    openDelete(t) { 
        this.deleteData = {
            id: t.id,
            kategori: t.kategori
        }; 
        this.showDelete = true; 
    },
    isUtamaDisabled(cat, currentId = null) {
        if (this.categoriesWithUtama.includes(cat)) {
            if (currentId) {
                const original = @js($templates).find(t => t.id == currentId);
                if (original && original.kategori == cat && original.prioritas == 'utama') {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}" class="space-y-6">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        @php
            $stats = [
                ['label' => 'Total Narasi', 'value' => $totalTemplates, 'color' => '#64748b'],
                ['label' => 'Capaian MB',   'value' => $totalMB,        'color' => '#ef4444'],
                ['label' => 'Capaian BSH',  'value' => $totalBSH,       'color' => '#f59e0b'],
                ['label' => 'Capaian BSB',  'value' => $totalBSB,       'color' => '#22c55e'],
                ['label' => 'Narasi Utama', 'value' => $totalUtama . '/3', 'color' => '#3b82f6'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card p-5 shadow-xl border-none flex flex-col items-center justify-center text-center group hover:translate-y-[-2px] transition-all duration-300">
            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-2 group-hover:text-gray-500 transition-colors">{{ $s['label'] }}</span>
            <span class="text-2xl font-bold tracking-tighter" style="color: {{ $s['color'] }}">{{ $s['value'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Pustaka Narasi Global</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Manajemen kalimat pembuka dan penutup otomatis untuk laporan perkembangan siswa.</p>
            </div>
            <button @click="showAdd = true" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                Tambah Narasi
            </button>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-32 text-center">Capaian</th>
                    <th>Isi Narasi Global</th>
                    <th class="w-32 text-center">Prioritas</th>
                    <th class="w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($templates as $t)
                    @php
                        $badgeClass = match($t->kategori) {
                            'BSB' => 'badge-bsb shadow-[0_0_8px_rgba(132,147,74,0.2)]',
                            'BSH' => 'badge-bsh shadow-[0_0_8px_rgba(245,158,11,0.15)]',
                            'MB'  => 'badge-mb shadow-[0_0_8px_rgba(239,68,68,0.15)]',
                            default => 'bg-gray-50 text-gray-400'
                        };
                    @endphp
                    <tr class="hover:bg-var(--bg) transition-colors">
                        <td class="text-center py-6">
                            <span class="badge {{ $badgeClass }}">{{ $t->kategori }}</span>
                        </td>
                        <td class="py-6">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 italic text-xs text-var(--text-2) leading-relaxed">
                                "{{ $t->isi }}"
                            </div>
                        </td>
                        <td class="text-center py-6">
                            <span class="px-3 py-1 rounded-xl text-[10px] font-bold {{ $t->prioritas == 'utama' ? 'bg-blue-50 text-blue-600 border border-blue-100 shadow-sm' : 'bg-gray-100 text-gray-400' }}">
                                {{ ucfirst($t->prioritas) }}
                            </span>
                        </td>
                        <td class="text-center py-6">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit({{ Js::from($t) }})" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDelete({{ Js::from($t) }})" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data narasi global.</td>
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
            <form action="{{ route('admin.template-rekomendasi-umum.store') }}" method="POST">
                @csrf
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Tambah Narasi Global</h3>
                    <button type="button" @click="showAdd = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Capaian Nilai</label>
                            <select name="kategori" x-model="addData.kategori" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="MB">MB (Mulai Berkembang)</option>
                                <option value="BSH">BSH (Sesuai Harapan)</option>
                                <option value="BSB">BSB (Sangat Baik)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Prioritas</label>
                            <select name="prioritas" x-model="addData.prioritas" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="utama" :disabled="isUtamaDisabled(addData.kategori)">Narasi Utama</option>
                                <option value="alternatif">Narasi Alternatif</option>
                            </select>
                            <p x-show="isUtamaDisabled(addData.kategori)" class="text-[8px] text-amber-600 mt-1">* Narasi utama kategori ini sudah ada</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Konten Narasi</label>
                        <textarea name="isi" required class="form-input rounded-2xl bg-var(--bg) border-var(--border) font-medium text-xs h-32 resize-none p-4" placeholder="Tuliskan kalimat narasi global..."></textarea>
                    </div>
                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex gap-3 justify-end bg-gray-50/50">
                    <button type="button" @click="showAdd = false" class="px-6 py-2 rounded-xl text-sm font-bold text-var(--text-3) hover:bg-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="px-8 py-2.5 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all shadow-lg shadow-green-100">Simpan Narasi</button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL EDIT --}}
    <template x-teleport="body">
    <div x-show="showEdit" x-transition.opacity @keydown.escape.window="showEdit = false" class="modal-overlay" x-cloak>
        <div class="modal-box w-full max-w-lg" @click.stop x-transition.scale.95>
            <form :action="'{{ route('admin.template-rekomendasi-umum.index') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-800">Edit Narasi Global</h3>
                    <button type="button" @click="showEdit = false" class="p-2 rounded-xl hover:bg-gray-200 text-var(--text-3) transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="px-8 py-6 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Capaian Nilai</label>
                            <select name="kategori" x-model="editData.kategori" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="MB">MB</option>
                                <option value="BSH">BSH</option>
                                <option value="BSB">BSB</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Prioritas</label>
                            <select name="prioritas" x-model="editData.prioritas" class="form-select rounded-xl bg-var(--bg) border-var(--border) font-bold text-xs">
                                <option value="utama" :disabled="isUtamaDisabled(editData.kategori, editData.id)">Narasi Utama</option>
                                <option value="alternatif">Narasi Alternatif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label text-[10px] font-bold text-gray-500 mb-1.5 block">Konten Narasi</label>
                        <textarea name="isi" x-model="editData.isi" required class="form-input rounded-2xl bg-var(--bg) border-var(--border) font-medium text-xs h-32 resize-none p-4"></textarea>
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
            <form :action="'{{ route('admin.template-rekomendasi-umum.index') }}/' + deleteData.id" method="POST">
                @csrf @method('DELETE')
                <div class="px-8 py-10 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-100">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-var(--text-1) tracking-tight mb-2">Hapus Narasi?</h3>
                    <p class="text-sm text-var(--text-3) font-medium mb-6" x-text="'Kategori: ' + deleteData.kategori"></p>
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
