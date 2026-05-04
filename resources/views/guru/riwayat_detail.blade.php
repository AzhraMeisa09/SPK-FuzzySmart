@extends('layouts.app')
@section('title', 'Detail Riwayat — ' . $siswa->nama)
@section('page-title', 'Analisis riwayat penilaian')

@section('content')
<div class="space-y-5 fade-in">

    {{-- ── HEADER ── --}}
    <div class="card p-5 no-print">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Info Siswa --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-xl flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="badge badge-blue text-[9px]">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">NISN: {{ $siswa->kode ?: '—' }}</span>
                    </div>
                    <h1 class="text-base font-semibold leading-tight" style="color: var(--text-1);">{{ $siswa->nama }}</h1>
                </div>
            </div>
            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guru.riwayat') }}" class="btn btn-gray btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <a href="{{ route('guru.laporan', $siswa->id) }}" class="btn btn-green btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan lengkap
                </a>
                <button onclick="window.print()" class="btn btn-gray btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    @php
        $statData = [
            ['label' => 'Minggu analisis',  'val' => $divisor,  'sub' => $totalMingguInPeriode > 0 ? 'total periode' : 'total record'],
            ['label' => 'Rata-rata capaian','val' => ($avgTotal ? number_format($avgTotal, 1) : '—').'%', 'sub' => 'global score'],
            ['label' => 'Kategori global',  'val' => $finalKat, 'sub' => 'status akhir',
             'badge' => $finalKat === 'BSB' ? 'badge-bsb' : ($finalKat === 'BSH' ? 'badge-bsh' : 'badge-mb')],
            ['label' => 'Data terinput',    'val' => ($bsbCount+$bshCount+$mbCount), 'sub' => 'total indikator'],
        ];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statData as $s)
            <div class="card p-5">
                <p class="text-[9px] font-bold uppercase tracking-widest mb-2" style="color: var(--text-3);">{{ $s['label'] }}</p>
                @if(isset($s['badge']))
                    <span class="badge {{ $s['badge'] }} px-3 py-1 text-sm font-bold">{{ $s['val'] }}</span>
                @else
                    <p class="text-2xl font-bold leading-tight" style="color: var(--text-1);">{{ $s['val'] }}</p>
                @endif
                <p class="text-[10px] font-medium uppercase tracking-wider mt-2" style="color: var(--text-3);">{{ $s['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── TIMELINE ── --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Timeline perkembangan mingguan</h3>
            <span class="text-[10px] font-medium" style="color: var(--text-3);">{{ $mingguGrouped->count() }} entri ditemukan</span>
        </div>

        @if($mingguGrouped->count() > 0)
            <div class="space-y-3" x-data="{ openMinggu: null }">
                @foreach($mingguGrouped as $index => $m)
                    @php
                        $mBadge = match($m['col']) { 'emerald' => 'badge-bsb', 'amber' => 'badge-bsh', 'rose' => 'badge-mb', default => 'badge-nonaktif' };
                    @endphp
                    <div class="card overflow-hidden transition-all duration-300">
                        {{-- Accordion Header --}}
                        <button @click="openMinggu = (openMinggu === {{ $index }} ? null : {{ $index }})"
                                class="w-full flex items-center gap-4 px-5 py-4 text-left transition-colors hover:bg-gray-50">
                            {{-- Week Badge --}}
                            <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center flex-shrink-0 font-bold text-sm"
                                 style="background: var(--accent-lt); color: var(--accent);">
                                <span class="text-[8px] font-bold uppercase leading-none opacity-70">Mgg</span>
                                <span class="text-lg font-bold leading-none mt-0.5">{{ $m['minggu_ke'] }}</span>
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm truncate" style="color: var(--text-1);">{{ $m['tema'] }}</h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-medium" style="color: var(--text-3);">{{ $m['tanggal'] }}</span>
                                    <span class="w-1 h-1 rounded-full" style="background: var(--border);"></span>
                                    <span class="text-[10px] font-medium uppercase" style="color: var(--text-3);">{{ $m['status'] }}</span>
                                </div>
                            </div>
                            {{-- Score & Category --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-lg font-bold leading-none" style="color: var(--text-1);">{{ number_format($m['avg'], 1) }}%</p>
                                <span class="inline-block mt-1 badge {{ $mBadge }} text-[9px]">{{ $m['kategori'] }}</span>
                            </div>
                            {{-- Chevron --}}
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-transform duration-300"
                                 :class="openMinggu === {{ $index }} ? 'rotate-180' : ''"
                                 style="background: var(--bg); color: var(--text-3);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        {{-- Accordion Body --}}
                        <div x-show="openMinggu === {{ $index }}" x-collapse style="display:none">
                            <div class="p-4" style="border-top: 1px solid var(--border); background: var(--bg);">
                                <div class="card overflow-hidden">
                                    <table class="tbl">
                                        <thead>
                                            <tr>
                                                <th class="w-16 text-center">Kode</th>
                                                <th>Aspek & indikator</th>
                                                <th class="text-center w-28">Kategori</th>
                                                <th class="text-center w-24">Skor (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($m['details'] as $d)
                                                @php $dBadge = match($d['col']) { 'emerald' => 'badge-bsb', 'amber' => 'badge-bsh', 'rose' => 'badge-mb', default => 'badge-nonaktif' }; @endphp
                                                <tr>
                                                    <td class="text-center font-mono text-[10px]" style="color: var(--text-3);">{{ $d['kode'] }}</td>
                                                    <td class="py-3">
                                                        <p class="font-medium text-sm leading-tight" style="color: var(--text-1);">{{ $d['subkriteria'] }}</p>
                                                        <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5" style="color: var(--text-3);">{{ $d['kriteria'] }}</p>
                                                        @if($d['catatan'])
                                                            <div class="mt-2 p-2.5 rounded-lg flex gap-2" style="background: #FDF8ED; border: 1px solid #E8D890;">
                                                                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #92700A;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                                <p class="text-xs italic leading-relaxed" style="color: #92700A;">&ldquo;{{ $d['catatan'] }}&rdquo;</p>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $dBadge }} text-[9px]">{{ $d['kategori'] }}</span>
                                                    </td>
                                                    <td class="text-center font-mono font-semibold text-sm" style="color: var(--text-1);">{{ $d['capaian'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-20 text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: var(--bg); border: 1px solid var(--border);">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-semibold text-sm" style="color: var(--text-2);">Belum ada riwayat</h3>
                <p class="text-xs mt-2 max-w-xs mx-auto" style="color: var(--text-3);">Siswa ini belum memiliki data penilaian mingguan yang tersimpan.</p>
            </div>
        @endif
    </div>

</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    }
</style>
@endsection
