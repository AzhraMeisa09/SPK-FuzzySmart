@extends('layouts.app')
@section('title', 'Riwayat Penilaian')
@section('page-title', 'Riwayat Penilaian')

@section('content')
<div class="space-y-6">

    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Riwayat Penilaian</h2>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                {{-- Filter PERIODE --}}
                @if(isset($listPeriode) && $listPeriode->count() > 0)
                    <form action="{{ route('guru.riwayat') }}" method="GET" id="periodeForm" class="relative">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="periode_id" class="form-select text-gray-900" style="padding-left: 36px; color: #000;" onchange="document.getElementById('periodeForm').submit()">
                            @foreach($listPeriode as $p)
                                <option value="{{ $p->id_periode }}" class="text-gray-900" style="color: #000;"
                                    {{ $currentPeriode && $currentPeriode->id_periode == $p->id_periode ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}
                                    @if($p->status === 'aktif') (Aktif) @elseif($p->status === 'final') (Final) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </form>
                @endif

                {{-- SEARCH FORM --}}
                <div class="w-full lg:w-[320px]">
                    <form action="{{ route('guru.riwayat') }}" method="GET" class="flex gap-2 items-center">
                        @if($currentPeriode)
                            <input type="hidden" name="periode_id" value="{{ $currentPeriode->id_periode }}">
                        @endif
                        <div class="search-box flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama siswa...">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <button type="submit" class="btn btn-blue btn-sm whitespace-nowrap">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('guru.riwayat', array_filter(['periode_id' => $currentPeriode?->id_periode])) }}" class="btn btn-gray btn-sm flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    @if($records->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($records as $s)
                @php
                    $latestRiwayat = collect($s['riwayat'])->last();
                    $latestHasil = $latestRiwayat ? $latestRiwayat['hasil'] : null;
                    $latestKat = '-'; $latestBadge = 'badge-nonaktif';
                    if ($latestHasil) {
                        if ($latestHasil >= 85)      { $latestKat = 'BSB'; $latestBadge = 'badge-bsb'; }
                        elseif ($latestHasil >= 70)  { $latestKat = 'BSH'; $latestBadge = 'badge-bsh'; }
                        else                         { $latestKat = 'MB';  $latestBadge = 'badge-mb'; }
                    }
                    $avgHasil = $s['avg_hasil'] ?? 0;
                @endphp

                <div class="card p-5 card-hover relative group overflow-hidden" style="border-left: 3px solid var(--accent);">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-lg" style="background: var(--accent-lt); color: var(--accent);">
                                {{ strtoupper(substr($s['nama'], 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold truncate leading-tight" style="color: var(--text-1);">{{ $s['nama'] }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--accent);">{{ $s['kelas'] }}</p>
                                    <span class="w-1 h-1 rounded-full" style="background: var(--border);"></span>
                                    <p class="text-[10px] font-bold tracking-widest uppercase" style="color: var(--text-3);">{{ $s['nisn'] ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($s['has_portofolio'])
                                <span title="Memiliki Portofolio">📷</span>
                            @endif
                            <span class="badge {{ $latestBadge }} text-[10px] font-black">{{ $latestKat }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Stats --}}
                        <div class="flex items-center justify-between py-2 border-y border-gray-50">
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Total Penilaian</span>
                            <span class="text-xs font-black text-gray-700">{{ $s['riwayat']->count() }} Minggu</span>
                        </div>

                        {{-- SPK RESULT (NEW) --}}
                        @if($s['evaluasi'])
                            <div class="p-3 rounded-xl flex items-center justify-between" style="background: var(--accent-lt); border: 1px solid var(--border);">
                                <div>
                                    <p class="text-[8px] font-bold uppercase tracking-widest leading-none mb-1.5" style="color: var(--accent);">Nilai akhir (Va)</p>
                                    <p class="text-sm font-bold leading-none" style="color: var(--text-1);">{{ number_format($s['evaluasi']->nilai_akhir, 3) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[8px] font-bold uppercase tracking-widest leading-none mb-1.5" style="color: var(--accent);">Kategori</p>
                                    <span class="badge {{ $s['evaluasi']->kategori_akhir === 'BSB' ? 'badge-bsb' : ($s['evaluasi']->kategori_akhir === 'BSH' ? 'badge-bsh' : 'badge-mb') }} px-2.5 py-0.5 text-[9px] font-black">
                                        {{ $s['evaluasi']->kategori_akhir }}
                                    </span>
                                </div>
                            </div>
                        @else
                            {{-- Progress --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--text-3);">Progress mingguan</span>
                                        <span class="text-[9px] font-bold uppercase tracking-tighter italic" style="color: var(--text-3);">Dibagi {{ $totalMingguInPeriode }} minggu</span>
                                    </div>
                                    <span class="text-sm font-bold px-2 py-0.5 rounded-md" style="color: var(--accent); background: var(--accent-lt);">{{ number_format($avgHasil, 1) }}%</span>
                                </div>
                                <div class="w-full progress-track bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="progress-fill h-full {{ $avgHasil >= 85 ? 'progress-green' : ($avgHasil >= 70 ? 'progress-yellow' : 'progress-red') }}"
                                         style="width: {{ min($avgHasil, 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 flex gap-2" style="border-top: 1px solid var(--border);">
                        <a href="{{ route('guru.riwayat.detail', [$s['siswa_id'], 'periode_id' => $currentPeriode?->id_periode]) }}"
                           class="flex-1 btn btn-gray btn-sm justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail
                        </a>
                        @if($s['has_portofolio'])
                            <a href="{{ route('guru.portofolio.index', ['siswa_id' => $s['siswa_id']]) }}"
                               class="btn btn-purple btn-sm px-3" title="Lihat Portofolio">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Tidak ada data</h3>
            <p class="text-xs mt-1.5 max-w-xs mx-auto" style="color: var(--text-3);">Coba gunakan filter lain atau mulai penilaian baru.</p>
        </div>
    @endif

</div>
@endsection
