@extends('layouts.app')
@section('title', 'Hasil Template')
@section('page-title', 'Pustaka Rekomendasi')

@section('content')

<div x-data="{ 
    editId: null, 
    openEdit(id) { this.editId = id; }, 
    closeEdit() { this.editId = null; } 
}" class="space-y-6">

    {{-- ── BREADCRUMB & HEADER ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    <a href="{{ route('admin.template-rekomendasi.index') }}" class="hover:text-var(--accent) transition-colors">Template Rekomendasi</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    <span class="text-var(--accent)">Hasil Generate</span>
                </div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Hasil Template Tersimpan</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">{{ $templates->count() }} narasi rekomendasi tersedia. Gunakan fitur edit inline untuk penyesuaian cepat.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @php
                    $firstTemplate = $templates->first();
                    $backUrl = $firstTemplate && $firstTemplate->subkriteria 
                        ? route('admin.template-rekomendasi.subkriteria', $firstTemplate->subkriteria->kriteria_id) 
                        : route('admin.template-rekomendasi.index');
                @endphp
                <a href="{{ $backUrl }}" class="px-8 py-3 rounded-xl bg-var(--accent) text-black border border-gray-200 text-xs font-bold shadow-lg shadow-green-100 hover:scale-105 active:scale-95 transition-all">
                    Selesai
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50/50 border border-green-100 text-green-700 rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── TABLE CARD ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Subkriteria</th>
                    <th class="w-24 text-center">Capaian</th>
                    <th>Isi Rekomendasi</th>
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
                        $prioClass = match($t->prioritas) {
                            'tinggi'   => 'bg-red-50 text-red-600 border-red-100',
                            'sedang','menengah' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'rendah'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            default    => 'bg-gray-50 text-gray-500 border-gray-100'
                        };
                    @endphp

                    {{-- View Row --}}
                    <tr x-show="editId !== '{{ $t->id_template }}'" class="hover:bg-var(--bg) transition-colors group">
                        <td class="align-top py-6">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-var(--accent) tracking-wide mb-1">{{ $t->subkriteria?->id_subkriteria }}</span>
                                <span class="text-sm font-semibold text-var(--text-1) tracking-tight">{{ $t->subkriteria?->nama_subkriteria ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="align-top text-center py-6">
                            <span class="badge {{ $badgeClass }} border border-current/10">{{ $t->kategori }}</span>
                        </td>
                        <td class="align-top py-6">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 group-hover:bg-white transition-colors">
                                <p class="text-xs text-var(--text-2) leading-relaxed font-medium italic">"{{ $t->isi }}"</p>
                            </div>
                        </td>
                        <td class="align-top text-center py-6">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-bold border shadow-sm {{ $prioClass }}">
                                {{ ucfirst($t->prioritas) }}
                            </span>
                        </td>
                        <td class="align-top text-center py-6">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit('{{ $t->id_template }}')" class="p-2 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('admin.template-rekomendasi.destroy', $t->id_template) }}" method="POST"
                                      onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Edit Inline Row --}}
                    <tr x-show="editId === '{{ $t->id_template }}'" x-transition.opacity class="bg-blue-50/30">
                        <td class="align-top py-6">
                            <span class="text-sm font-semibold text-var(--text-1) tracking-tight">{{ $t->subkriteria?->nama_subkriteria ?? '-' }}</span>
                        </td>
                        <td class="align-top text-center py-6">
                            <span class="badge {{ $badgeClass }} border border-current/10">{{ $t->kategori }}</span>
                        </td>
                        <td class="align-top py-6" colspan="2">
                            <form action="{{ route('admin.template-rekomendasi.update', $t->id_template) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <textarea name="isi" rows="3" required
                                              class="form-input rounded-2xl bg-white border-blue-100 font-medium text-xs p-4 resize-none shadow-md">{{ $t->isi }}</textarea>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <select name="prioritas" required class="form-select rounded-xl bg-white border-blue-100 font-bold text-[10px] px-4 py-3 shadow-sm">
                                                <option value="tinggi"  {{ $t->prioritas === 'tinggi'  ? 'selected' : '' }}>Tinggi</option>
                                                <option value="sedang"  {{ $t->prioritas === 'sedang'  ? 'selected' : '' }}>Sedang</option>
                                                <option value="rendah"  {{ $t->prioritas === 'rendah'  ? 'selected' : '' }}>Rendah</option>
                                            </select>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 text-white text-[10px] font-bold shadow-lg shadow-blue-100 hover:scale-105 active:scale-95 transition-all">Simpan</button>
                                            <button type="button" @click="closeEdit()" class="px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-500 text-[10px] font-bold hover:bg-gray-100 transition-all shadow-sm">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Tidak ada data template narasi hasil generate.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
