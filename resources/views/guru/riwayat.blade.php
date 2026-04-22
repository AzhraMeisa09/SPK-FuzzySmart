@extends('layouts.app')
@section('title', 'Riwayat Penilaian')
@section('page-title', 'Riwayat Penilaian')

@section('content')
<div class="space-y-5" x-data="{ openSiswa: null }">

    {{-- ── HEADER ────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Penilaian</h1>
            <p class="text-sm text-gray-400 mt-0.5">Lihat kembali seluruh catatan penilaian perkembangan siswa Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-black uppercase tracking-wide border border-blue-100">
                {{ auth()->user()->nama }}
            </span>
        </div>
    </div>

    {{-- ── FILTER BAR ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form action="{{ route('guru.riwayat') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Nama Siswa --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Siswa</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="nama" value="{{ request('nama') }}"
                            class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-700 placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:border-green-400 focus:bg-white transition"
                            placeholder="Cari nama siswa...">
                    </div>
                </div>

                {{-- Minggu --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Minggu</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <select name="minggu" class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-green-400 focus:border-green-400 focus:bg-white transition appearance-none">
                            <option value="">Semua Minggu</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}" {{ request('minggu') == $i ? 'selected' : '' }}>Minggu {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        <select name="status" class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-green-400 focus:border-green-400 focus:bg-white transition appearance-none">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="final" {{ request('status') == 'final' ? 'selected' : '' }}>Final</option>
                        </select>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-2 items-end">
                    <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-black rounded-xl transition-all duration-200 active:scale-95 shadow-sm shadow-green-900/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('guru.riwayat') }}"
                        class="flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── MAIN CONTENT ────────────────────────────── --}}
    @if($records->count() > 0)

        {{-- LEGEND (above cards) --}}
        <div class="flex items-center gap-4 text-[11px] text-gray-400 px-1">
            <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span> BSB ≥85</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span> BSH 70–84</span>
            <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-rose-400"></span> MB &lt;70</span>
        </div>

        {{-- ── STUDENT CARD GRID ──────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($records as $s)
                @php
                    $latestRiwayat = collect($s['riwayat'])->last();
                    $latestHasil = $latestRiwayat ? $latestRiwayat['hasil'] : null;
                    $latestKat = '-'; $latestCol = 'text-gray-300';
                    if ($latestHasil) {
                        if ($latestHasil >= 85)      { $latestKat = 'BSB'; $latestCol = 'emerald'; }
                        elseif ($latestHasil >= 70)  { $latestKat = 'BSH'; $latestCol = 'amber'; }
                        else                         { $latestKat = 'MB';  $latestCol = 'rose'; }
                    }
                @endphp
                <div>
                    {{-- Card Header --}}
                    <button type="button"
                        @click="openSiswa = (openSiswa === {{ $s['siswa_id'] }} ? null : {{ $s['siswa_id'] }})"
                        class="w-full text-left bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:scale-[1.01] transition-all duration-200 active:scale-[0.99]"
                        :class="openSiswa === {{ $s['siswa_id'] }} ? 'ring-2 ring-green-400 border-transparent shadow-md' : ''">
                        <div class="flex items-center gap-4">
                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center text-xl font-black flex-shrink-0 shadow-md">
                                {{ strtoupper(substr($s['nama'], 0, 1)) }}
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-black text-gray-900 truncate leading-tight">{{ $s['nama'] }}</h3>
                                <p class="text-[11px] text-blue-500 font-bold uppercase tracking-wider mt-0.5">{{ $s['kelas'] }}</p>
                            </div>
                            {{-- Last result badge --}}
                            <div class="flex flex-col items-end gap-1">
                                @if($latestKat !== '-')
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase
                                        {{ $latestCol === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                                           ($latestCol === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-100' :
                                           'bg-rose-50 text-rose-700 border border-rose-100') }}">
                                        {{ $latestKat }}
                                    </span>
                                @endif
                                <span class="text-[10px] text-gray-400 font-medium">{{ $s['riwayat']->count() }} entri</span>
                            </div>
                            {{-- Arrow --}}
                            <svg class="w-5 h-5 text-gray-300 flex-shrink-0 transition-transform duration-300"
                                :class="openSiswa === {{ $s['siswa_id'] }} ? 'rotate-180 text-green-500' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        {{-- Mini progress bar --}}
                        @php
                            $allHasil = collect($s['riwayat'])->pluck('hasil')->filter();
                            $avgHasil = $allHasil->count() > 0 ? $allHasil->avg() : 0;
                        @endphp
                        @if($avgHasil > 0)
                            <div class="mt-4 flex items-center gap-3">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700
                                        {{ $avgHasil >= 85 ? 'bg-emerald-500' : ($avgHasil >= 70 ? 'bg-amber-400' : 'bg-rose-400') }}"
                                        style="width: {{ min($avgHasil, 100) }}%"></div>
                                </div>
                                <span class="text-[11px] font-black text-gray-500">{{ number_format($avgHasil, 0) }}%</span>
                            </div>
                        @endif
                    </button>

                    {{-- Expanded Detail Table --}}
                    <template x-if="openSiswa === {{ $s['siswa_id'] }}">
                        <div class="mt-2 bg-white rounded-2xl border border-green-100 shadow-lg overflow-hidden animate-fade-up">
                            {{-- Detail Header --}}
                            <div class="flex items-center justify-between px-5 py-3 bg-green-500 text-white">
                                <h4 class="font-black text-sm">Riwayat Mingguan — {{ $s['nama'] }}</h4>
                                <button @click="openSiswa = null" class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 transition active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Desktop: Table --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                            <th class="px-5 py-3 text-center w-20">Minggu</th>
                                            <th class="px-5 py-3 text-left">Tema</th>
                                            <th class="px-5 py-3 text-center">Status</th>
                                            <th class="px-5 py-3 text-center">Nilai</th>
                                            <th class="px-5 py-3 text-center">Kategori</th>
                                            <th class="px-5 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($s['riwayat'] as $r)
                                            @php
                                                $kHasil = $r['hasil'];
                                                $kKat = '-'; $kCol = 'gray';
                                                if ($kHasil) {
                                                    if ($kHasil >= 85)      { $kKat = 'BSB'; $kCol = 'emerald'; }
                                                    elseif ($kHasil >= 70)  { $kKat = 'BSH'; $kCol = 'amber'; }
                                                    else                    { $kKat = 'MB';  $kCol = 'rose'; }
                                                }
                                                $mingguParts = explode(' - ', $r['minggu']);
                                            @endphp
                                            <tr class="hover:bg-green-50/40 transition-colors odd:bg-white even:bg-gray-50/30 group">
                                                <td class="px-5 py-4 text-center">
                                                    <span class="inline-flex items-center justify-center w-10 h-7 bg-blue-50 text-blue-700 text-[11px] font-black rounded-xl border border-blue-100">
                                                        {{ $mingguParts[0] }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 font-semibold text-gray-800 text-sm">
                                                    {{ $mingguParts[1] ?? '—' }}
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    @if($r['status'] == 'final')
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            Final
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/></svg>
                                                            Draft
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4 text-center font-mono font-black text-gray-900">
                                                    {{ $kHasil ? number_format($kHasil, 2) : '—' }}
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="inline-block px-4 py-1 rounded-xl text-[10px] font-black uppercase italic border
                                                        {{ $kKat === 'BSB' ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                         : ($kKat === 'BSH' ? 'bg-amber-50 text-amber-700 border-amber-100'
                                                         : ($kKat === 'MB' ? 'bg-rose-50 text-rose-700 border-rose-100'
                                                         : 'bg-gray-100 text-gray-400 border-transparent')) }}">
                                                        {{ $kKat }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-center">
                                                    <a href="{{ route('guru.laporan') }}?siswa_id={{ $s['siswa_id'] }}"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition active:scale-90"
                                                        title="Lihat Laporan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile: Card List per minggu --}}
                            <div class="sm:hidden divide-y divide-gray-50">
                                @foreach($s['riwayat'] as $r)
                                    @php
                                        $mHasil = $r['hasil'];
                                        $mKat = '-'; $mCol = 'gray';
                                        if ($mHasil) {
                                            if ($mHasil >= 85)      { $mKat = 'BSB'; $mCol = 'emerald'; }
                                            elseif ($mHasil >= 70)  { $mKat = 'BSH'; $mCol = 'amber'; }
                                            else                    { $mKat = 'MB';  $mCol = 'rose'; }
                                        }
                                        $mParts = explode(' - ', $r['minggu']);
                                    @endphp
                                    <div class="px-5 py-4 hover:bg-green-50/30 transition">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-0.5 px-2 py-0.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black flex-shrink-0 border border-blue-100">{{ $mParts[0] }}</span>
                                                <div>
                                                    <p class="font-semibold text-gray-900 text-sm">{{ $mParts[1] ?? '—' }}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        @if($r['status'] == 'final')
                                                            <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-lg font-black">✓ Final</span>
                                                        @else
                                                            <span class="text-[10px] text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-lg font-black">Draft</span>
                                                        @endif
                                                        @if($mHasil)
                                                            <span class="text-[10px] text-gray-500 font-bold">{{ number_format($mHasil, 2) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-black px-3 py-1 rounded-xl border
                                                {{ $mKat === 'BSB' ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                 : ($mKat === 'BSH' ? 'bg-amber-50 text-amber-700 border-amber-100'
                                                 : ($mKat === 'MB' ? 'bg-rose-50 text-rose-700 border-rose-100'
                                                 : 'bg-gray-100 text-gray-400 border-transparent')) }}">
                                                {{ $mKat }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>

    @else
        {{-- ── EMPTY STATE ─────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 py-20 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <h3 class="font-black text-gray-400 text-base">Belum ada data penilaian</h3>
            <p class="text-gray-300 text-sm mt-1.5 max-w-xs mx-auto">
                @if(request()->hasAny(['nama','minggu','status']))
                    Tidak ada hasil yang cocok dengan filter Anda. Coba ubah kriteria pencarian.
                @else
                    Silakan lakukan penilaian di menu <strong>Input Penilaian</strong> terlebih dahulu.
                @endif
            </p>
            @if(request()->hasAny(['nama','minggu','status']))
                <a href="{{ route('guru.riwayat') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset Filter
                </a>
            @endif
        </div>
    @endif

</div>
@endsection
