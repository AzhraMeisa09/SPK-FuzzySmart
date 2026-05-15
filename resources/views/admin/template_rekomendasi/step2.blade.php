@extends('layouts.app')
@section('title', 'Pilih Subkriteria')
@section('page-title', 'Pustaka Rekomendasi')

@section('content')

<div class="space-y-6">

    {{-- ── HEADER CARD ── --}}
    <div class="card p-6 shadow-xl border-none">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                    <a href="{{ route('admin.template-rekomendasi.index') }}" class="hover:text-var(--accent) transition-colors">Template Rekomendasi</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    <span class="text-var(--accent)">{{ $kriteria->nama_kriteria }}</span>
                </div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">{{ $kriteria->id_kriteria }} — {{ $kriteria->nama_kriteria }}</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Klik subkriteria untuk melakukan kustomisasi atau generate narasi otomatis.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.template-rekomendasi.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-black text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                    Kembali
                </a>
                
                @php
                    $pendingSubIds = $kriteria->subkriteria->filter(function($s) {
                        return \App\Models\TemplateRekomendasi::where('subkriteria_id', $s->id_subkriteria)->count() < 3;
                    })->pluck('id_subkriteria')->toArray();
                @endphp

                @if(!empty($pendingSubIds))
                    <form action="{{ route('admin.template-rekomendasi.generate') }}" method="POST">
                        @csrf
                        @foreach($pendingSubIds as $id)
                            <input type="hidden" name="subkriteria_ids[]" value="{{ $id }}">
                        @endforeach
                        <button type="submit" class="btn btn-green px-5 py-2.5 rounded-xl flex items-center gap-2 font-bold text-xs shadow-lg shadow-green-100 !text-black border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Generate Semua
                        </button>
                    </form>
                @endif
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
                    <th class="w-48 text-center">Status Kelengkapan</th>
                    <th class="w-40 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kriteria->subkriteria as $s)
                    @php
                        $existingCount = \App\Models\TemplateRekomendasi::where('subkriteria_id', $s->id_subkriteria)->count();
                        $complete = $existingCount === 3;
                    @endphp
                    <tr class="hover:bg-var(--bg) transition-colors group">
                        <td class="py-5">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-var(--accent) tracking-wide mb-1">{{ $s->id_subkriteria }}</span>
                                <span class="text-sm font-semibold text-var(--text-1) tracking-tight">{{ $s->nama_subkriteria }}</span>
                            </div>
                        </td>
                        <td class="text-center py-5">
                            <div class="flex items-center justify-center gap-1.5">
                                @php
                                    $existingKats = \App\Models\TemplateRekomendasi::where('subkriteria_id', $s->id_subkriteria)->pluck('kategori')->toArray();
                                @endphp
                                @foreach(['MB','BSH','BSB'] as $kat)
                                    @php
                                        $has = in_array($kat, $existingKats);
                                        $colorClass = 'bg-gray-50 text-gray-300 border-gray-100'; // Default gray
                                        
                                        if ($has) {
                                            if ($kat === 'MB') $colorClass = 'bg-red-50 text-red-600 border-red-100';
                                            elseif ($kat === 'BSH') $colorClass = 'bg-amber-50 text-amber-600 border-amber-100';
                                            elseif ($kat === 'BSB') $colorClass = 'bg-green-50 text-green-600 border-green-100';
                                        }
                                    @endphp
                                    <span class="text-[9px] font-bold px-2 py-1 rounded-lg border {{ $colorClass }} tracking-tighter shadow-sm">
                                        {{ $kat }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center py-5">
                            <form action="{{ route('admin.template-rekomendasi.generate') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="subkriteria_ids[]" value="{{ $s->id_subkriteria }}">
                                @if($complete)
                                    <button type="submit" class="flex items-center gap-2 mx-auto px-4 py-2 rounded-xl bg-blue-50 text-black border border-gray-200 text-[10px] font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Lihat / Edit
                                    </button>
                                @else
                                    <button type="submit" class="flex items-center gap-2 mx-auto px-4 py-2 rounded-xl bg-var(--accent) text-black border border-gray-200 text-[10px] font-bold hover:scale-105 transition-all shadow-lg shadow-green-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Generate
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data subkriteria untuk kriteria ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
