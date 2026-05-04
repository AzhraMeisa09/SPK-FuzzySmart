@extends('layouts.app')
@section('title', 'Hasil Evaluasi SPK')
@section('page-title', 'Hasil Evaluasi SPK (Fuzzy SMART)')

@section('content')
<div class="space-y-6 pb-12">

    {{-- ── ERROR/INFO MESSAGES ────────────────────────── --}}
    @if(isset($error))
        <div class="card p-12 text-center shadow-xl border-none">
            <div class="w-24 h-24 bg-amber-50 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-amber-100">
                <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-var(--text-1) tracking-tight mb-2">{{ $error }}</h3>
            <p class="text-var(--text-3) text-sm font-medium">Sistem belum menemukan data kalkulasi evaluasi yang bersifat final.</p>
        </div>
    @else

    {{-- ── HEADER ────────────────────────────── --}}
    <div class="card p-6 flex flex-col sm:flex-row justify-between items-center gap-6 shadow-xl border-none relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-var(--accent)/5 rounded-full"></div>
        <div class="relative">
            <h2 class="text-xl font-bold text-var(--text-1) tracking-tight">Rekapitulasi Evaluasi SPK</h2>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="px-2.5 py-1 rounded-lg bg-var(--accent-lt) text-var(--accent) text-[10px] font-bold border border-var(--accent)/10">
                    {{ $periode->nama_periode }}
                </span>
                <p class="text-xs text-var(--text-3) font-bold">Semester {{ ucfirst($periode->semester) }} • {{ $periode->tahunAjaran->nama }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto relative">
            <button onclick="window.print()" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm no-print">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Export Laporan PDF
            </button>
        </div>
    </div>

    {{-- ── DAFTAR KELAS (ACCORDION) ───────────────────────── --}}
    <div class="space-y-4">
        @forelse($groupedData as $group)
            <div x-data="{ expanded: false }" class="card border-none shadow-xl overflow-hidden print-uncollapse transition-all duration-300" :class="expanded ? 'ring-2 ring-var(--accent)/20' : ''">
                
                {{-- HEADER ACCORDION --}}
                <div @click="expanded = !expanded" class="px-8 py-6 cursor-pointer flex items-center justify-between bg-white hover:bg-var(--bg) transition-colors">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl border border-blue-100 shadow-sm">
                            {{ substr($group->kelas->nama_kelas, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-var(--text-1) tracking-tight">Kelas {{ $group->kelas->nama_kelas }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                                <p class="text-xs text-var(--text-3) font-bold">{{ count($group->data) }} Siswa Dievaluasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 no-print">
                        <div class="hidden lg:flex -space-x-3">
                            @foreach($group->data->take(4) as $top)
                                <div class="w-9 h-9 rounded-xl border-2 border-white bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-600 shadow-sm" title="{{ $top->siswa->nama }}">
                                    {{ substr($top->siswa->nama, 0, 1) }}
                                </div>
                            @endforeach
                            @if(count($group->data) > 4)
                                <div class="w-9 h-9 rounded-xl border-2 border-white bg-var(--bg) flex items-center justify-center text-[10px] font-bold text-var(--text-3) shadow-sm">
                                    +{{ count($group->data) - 4 }}
                                </div>
                            @endif
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-var(--bg) border border-var(--border) flex items-center justify-center text-var(--text-3) transition-transform duration-300 shadow-sm" :class="expanded ? 'rotate-180 text-var(--accent) border-var(--accent)/20 bg-var(--accent-lt)' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- KONTEN TABEL --}}
                <div x-show="expanded" x-collapse x-cloak class="border-t border-gray-50 bg-var(--bg)/30">
                    <div class="overflow-x-auto">
                        <table class="tbl bg-transparent">
                            <thead>
                                <tr>
                                    <th class="w-20 text-center">Rank</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kategori Hasil</th>
                                    <th>Nilai Akhir (SPK)</th>
                                    <th class="w-72 hidden xl:table-cell">Intisari Rekomendasi</th>
                                    <th class="text-center w-24 no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($group->data as $item)
                                    <tr class="hover:bg-white transition-colors group">
                                        <td class="text-center">
                                            @if($item->ranking == 1)
                                                <div class="w-9 h-9 mx-auto rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-600 text-white flex items-center justify-center font-bold shadow-lg shadow-yellow-100 border-2 border-white" title="Juara 1">1</div>
                                            @elseif($item->ranking == 2)
                                                <div class="w-9 h-9 mx-auto rounded-xl bg-gradient-to-br from-gray-300 to-gray-400 text-white flex items-center justify-center font-bold shadow-lg shadow-gray-100 border-2 border-white" title="Juara 2">2</div>
                                            @elseif($item->ranking == 3)
                                                <div class="w-9 h-9 mx-auto rounded-xl bg-gradient-to-br from-amber-600 to-amber-800 text-white flex items-center justify-center font-bold shadow-lg shadow-amber-100 border-2 border-white" title="Juara 3">3</div>
                                            @else
                                                <span class="text-xs font-bold text-var(--text-3)">{{ $item->ranking }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-var(--text-1) leading-tight">{{ $item->siswa->nama }}</span>
                                                <span class="text-[10px] text-var(--text-3) font-bold mt-0.5 tracking-wider">NISN: {{ $item->siswa->kode ?? '—' }}</span>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @if($item->kategori_akhir == 'BSB')
                                                <span class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-100 rounded-lg text-[10px] font-bold">BSB</span>
                                            @elseif($item->kategori_akhir == 'BSH')
                                                <span class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-[10px] font-bold">BSH</span>
                                            @else
                                                <span class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-100 rounded-lg text-[10px] font-bold">MB</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="w-48">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-bold text-var(--text-1)">{{ number_format($item->nilai_akhir, 3) }}</span>
                                                    <span class="text-[9px] font-bold text-var(--text-3)">Skor</span>
                                                </div>
                                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden border border-gray-200">
                                                    <div class="{{ $item->kategori_akhir == 'BSB' ? 'bg-green-500' : ($item->kategori_akhir == 'BSH' ? 'bg-amber-400' : 'bg-red-400') }} h-full transition-all duration-1000 shadow-[0_0_8px_rgba(34,197,94,0.3)]" 
                                                         style="width: {{ min($item->nilai_akhir * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="hidden xl:table-cell">
                                            <p class="text-[11px] text-var(--text-2) leading-relaxed font-medium line-clamp-2" title="{{ $item->rekomendasi }}">
                                                {{ $item->rekomendasi ?? 'Data rekomendasi sistem belum tersedia.' }}
                                            </p>
                                        </td>

                                        <td class="text-center no-print">
                                            <a href="{{ route('admin.hasil_evaluasi.show', $item->id) }}" 
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group-hover:scale-105" title="Lihat Analisis Detail">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-20 text-center border-none shadow-xl">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.244l-.28.14a2 2 0 01-2.983-1.882V4.67a2 2 0 012.104-1.997l2.91.145a2 2 0 001.103-.216l.23-.115a6 6 0 014.89 0l.23.115a2 2 0 001.103.216l2.91-.145a2 2 0 012.104 1.997v8.523a2 2 0 01-2.127 1.99l-1.503-.125z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-var(--text-1) tracking-tight">Data Evaluasi Kosong</h3>
                <p class="text-xs text-var(--text-3) mt-1 font-medium">Belum ada rekaman hasil evaluasi SPK untuk periode ini.</p>
            </div>
        @endforelse
    </div>

    @endif
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background-color: white !important; font-size: 10pt; }
    .card { box-shadow: none !important; border: 1px solid #eee !important; margin-bottom: 20px; page-break-inside: avoid; }
    .bg-white { background-color: white !important; }
    @page { margin: 1.5cm; size: landscape; }
    table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #ddd !important; }
    th, td { border: 1px solid #ddd !important; padding: 10px !important; }
    .print-uncollapse [x-show] { display: block !important; height: auto !important; opacity: 1 !important; visibility: visible !important; }
    .w-14, .w-12 { width: 40px !important; height: 40px !important; }
}
</style>
@endsection
