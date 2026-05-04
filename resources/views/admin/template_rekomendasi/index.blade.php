@extends('layouts.app')
@section('title', 'Template Rekomendasi')
@section('page-title', 'Pustaka Rekomendasi')

@section('content')

@php
    $totalKriteria = $kriterias->count();
    $totalSub = \App\Models\Subkriteria::count();
    $totalTemplates = \App\Models\TemplateRekomendasi::count();
    $coveredSub = \App\Models\TemplateRekomendasi::distinct('subkriteria_id')->count('subkriteria_id');
@endphp

<div class="space-y-6">

    {{-- ── SUMMARY STATS ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @php
            $stats = [
                ['label' => 'Total Kriteria', 'value' => $totalKriteria, 'color' => '#84934A'],
                ['label' => 'Total Subkriteria', 'value' => $totalSub, 'color' => '#64748b'],
                ['label' => 'Total Narasi', 'value' => $totalTemplates, 'color' => '#3b82f6'],
                ['label' => 'Cakupan (%)', 'value' => ($totalSub > 0 ? round(($coveredSub/$totalSub)*100) : 0) . '%', 'color' => '#f59e0b'],
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
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Template Rekomendasi</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Pilih kriteria untuk mengelola narasi rekomendasi otomatis per subkriteria.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.template-rekomendasi-umum.index') }}" class="px-5 py-2.5 rounded-xl border border-blue-100 bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                    Kelola Narasi Umum
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50/50 border border-green-100 text-green-700 rounded-2xl text-[10px] font-bold flex items-center animate-fade-in shadow-sm">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── KRITERIA TABLE ── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="w-24">Kode</th>
                    <th>Nama Kriteria</th>
                    <th class="text-center">Jumlah Subkriteria</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kriterias as $k)
                    <tr class="hover:bg-var(--bg) transition-colors group">
                        <td class="py-5">
                            <span class="inline-flex items-center justify-center w-12 h-12 bg-var(--accent-lt) text-var(--accent) rounded-2xl font-bold text-sm border border-var(--accent)/10 shadow-sm transition-all group-hover:scale-110 group-hover:rotate-3">
                                {{ $k->kode }}
                            </span>
                        </td>
                        <td class="py-5">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-gray-400 tracking-wide mb-1">Mata Evaluasi</span>
                                <span class="text-sm font-semibold text-var(--text-1) tracking-tight">{{ $k->nama }}</span>
                            </div>
                        </td>
                        <td class="text-center py-5">
                            <span class="badge badge-blue shadow-[0_0_8px_rgba(59,130,246,0.15)]">{{ $k->subkriteria_count }} subkriteria</span>
                        </td>
                        <td class="text-center py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.template-rekomendasi.subkriteria', $k->id) }}"
                                   class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group" title="Kelola Template">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-24 text-var(--text-3) font-medium italic text-sm">Belum ada data kriteria utama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
