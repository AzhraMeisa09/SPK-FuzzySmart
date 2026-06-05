@extends('layouts.app')
@section('title', 'Hasil Evaluasi SPK')
@section('page-title', 'Hasil Evaluasi SPK')

@section('content')
<div class="space-y-8 fade-in pb-12">

    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--accent-lt); color: var(--accent);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold" style="color: var(--text-1);">Hasil evaluasi akhir (SPK)</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($periode)
                            <span class="badge badge-blue text-[9px] px-2.5 py-0.5 uppercase tracking-wider">{{ $periode->nama_periode }}</span>
                            <span class="badge badge-nonaktif text-[9px] px-2.5 py-0.5 uppercase tracking-wider">Final</span>
                        @endif
                        <p class="text-xs" style="color: var(--text-3);">Hasil perhitungan menggunakan metode <span class="font-semibold" style="color: var(--text-1);">Fuzzy SMART</span>.</p>
                    </div>
                </div>
            </div>

            {{-- Pilih Periode --}}
            @if(isset($listPeriode) && $listPeriode->count() > 1)
                <form action="{{ route('guru.hasil-evaluasi') }}" method="GET" id="periodeFilterForm" class="relative">
                    <select name="periode_id" class="form-select" style="padding-left: 36px;" onchange="document.getElementById('periodeFilterForm').submit()">
                        @foreach($listPeriode as $p)
                            <option value="{{ $p->id_periode }}" class="text-gray-900"
                                {{ $periode && $periode->id_periode == $p->id_periode ? 'selected' : '' }}>
                                {{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }} (Final)
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </form>
            @endif
        </div>
    </div>


    @if(!$isFinalized)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Menunggu finalisasi data</h3>
            <p class="text-xs mt-2 max-w-sm mx-auto" style="color: var(--text-3);">
                Administrator sedang melakukan perhitungan akhir. Hasil evaluasi akan tampil di sini setelah periode ditutup.
            </p>
        </div>
    @else
        {{-- ── STATISTIK RINGKASAN ── --}}
        @if($results->count() > 0)
            @php
                $bsbCount = $results->where('kategori_akhir', 'BSB')->count();
                $bshCount = $results->where('kategori_akhir', 'BSH')->count();
                $mbCount  = $results->where('kategori_akhir', 'MB')->count();
                $total    = $results->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="card p-5 flex items-center gap-4" style="border-left: 4px solid #10b981;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #d1fae5; color: #059669;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Berkembang Sangat Baik</p>
                        <p class="text-2xl font-black text-gray-900 leading-tight">{{ $bsbCount }} <span class="text-sm font-bold text-gray-400">siswa</span></p>
                        <span class="badge badge-bsb text-[8px] mt-0.5">BSB</span>
                    </div>
                </div>
                <div class="card p-5 flex items-center gap-4" style="border-left: 4px solid #f59e0b;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #fef3c7; color: #d97706;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Berkembang Sesuai Harapan</p>
                        <p class="text-2xl font-black text-gray-900 leading-tight">{{ $bshCount }} <span class="text-sm font-bold text-gray-400">siswa</span></p>
                        <span class="badge badge-bsh text-[8px] mt-0.5">BSH</span>
                    </div>
                </div>
                <div class="card p-5 flex items-center gap-4" style="border-left: 4px solid #f43f5e;">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ffe4e6; color: #e11d48;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Mulai Berkembang</p>
                        <p class="text-2xl font-black text-gray-900 leading-tight">{{ $mbCount }} <span class="text-sm font-bold text-gray-400">siswa</span></p>
                        <span class="badge badge-mb text-[8px] mt-0.5">MB</span>
                    </div>
                </div>
            </div>

            {{-- ── TABEL PERANGKINGAN ── --}}
            <div class="card overflow-hidden">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-bottom: 1px solid var(--border);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold" style="color: var(--text-1);">Tabel Perangkingan Siswa</h3>
                            <p class="text-[10px]" style="color: var(--text-3);">Diurutkan berdasarkan skor keputusan Fuzzy SMART (nilai tertinggi = peringkat 1)</p>
                        </div>
                    </div>
                    <span class="badge badge-blue">{{ $total }} siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="tbl w-full">
                        <thead>
                            <tr style="background: var(--bg);">
                                <th class="text-center" style="width: 70px;">Peringkat</th>
                                <th style="min-width: 200px;">Nama Siswa</th>
                                <th class="text-center" style="width: 120px;">NISN</th>
                                <th class="text-center" style="width: 100px;">Kelas</th>
                                <th class="text-center" style="width: 180px;">Skor Keputusan (V)</th>
                                <th class="text-center" style="width: 160px;">Predikat</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $res)
                                @php
                                    $rank = $loop->iteration;
                                    $kat  = $res->kategori_akhir;
                                    $progColor = match($kat) {
                                        'BSB' => '#10b981',
                                        'BSH' => '#f59e0b',
                                        'MB'  => '#f43f5e',
                                        default => '#94a3b8'
                                    };
                                    $badgeClass = match($kat) {
                                        'BSB' => 'badge-bsb',
                                        'BSH' => 'badge-bsh',
                                        'MB'  => 'badge-mb',
                                        default => ''
                                    };
                                    $katLabel = match($kat) {
                                        'BSB' => 'Berkembang Sangat Baik',
                                        'BSH' => 'Berkembang Sesuai Harapan',
                                        'MB'  => 'Mulai Berkembang',
                                        default => $kat
                                    };
                                    $rowBg = $rank === 1 ? 'rgba(251,191,36,0.04)' : ($rank === 2 ? 'rgba(156,163,175,0.04)' : ($rank === 3 ? 'rgba(180,83,9,0.04)' : 'transparent'));
                                @endphp
                                <tr class="transition-colors hover:bg-blue-50/10" style="background: {{ $rowBg }};">
                                    {{-- Kolom Peringkat --}}
                                    <td class="text-center py-4">
                                        @if($rank === 1)
                                            <div class="inline-flex flex-col items-center gap-0.5">
                                                <span class="text-xl">🥇</span>
                                                <span class="text-[9px] font-black text-amber-500 uppercase tracking-wider">1st</span>
                                            </div>
                                        @elseif($rank === 2)
                                            <div class="inline-flex flex-col items-center gap-0.5">
                                                <span class="text-xl">🥈</span>
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">2nd</span>
                                            </div>
                                        @elseif($rank === 3)
                                            <div class="inline-flex flex-col items-center gap-0.5">
                                                <span class="text-xl">🥉</span>
                                                <span class="text-[9px] font-black text-amber-700 uppercase tracking-wider">3rd</span>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center mx-auto font-black text-xs" style="background: var(--bg); border: 1px solid var(--border); color: var(--text-3);">
                                                {{ $rank }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Kolom Nama --}}
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                                                @if($res->siswa->foto)
                                                    <img src="{{ asset('storage/' . $res->siswa->foto) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $res->siswa->name }}">
                                                @else
                                                    {{ strtoupper(substr($res->siswa->name, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold truncate" style="color: var(--text-1);">{{ $res->siswa->name }}</p>
                                                @if($rank <= 3)
                                                    <p class="text-[9px] font-bold uppercase tracking-widest" style="color: var(--accent);">Top {{ $rank }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- NISN --}}
                                    <td class="text-center py-4">
                                        <span class="font-mono text-xs" style="color: var(--text-3);">{{ $res->siswa->kode ?: $res->siswa->id_siswa ?: '—' }}</span>
                                    </td>

                                    {{-- Kelas --}}
                                    <td class="text-center py-4">
                                        <span class="badge badge-blue text-[9px]">{{ $res->siswa->kelas->nama_kelas ?? '—' }}</span>
                                    </td>

                                    {{-- Skor --}}
                                    <td class="text-center py-4 px-4">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span class="text-base font-black" style="color: var(--text-1);">{{ number_format($res->nilai_akhir, 3) }}</span>
                                            <div class="w-full max-w-[100px] h-1.5 rounded-full overflow-hidden" style="background: var(--border);">
                                                <div class="h-full rounded-full transition-all duration-700" style="width: {{ $res->nilai_akhir * 100 }}%; background: {{ $progColor }};"></div>
                                            </div>
                                            <span class="text-[8px] font-bold uppercase tracking-widest" style="color: var(--text-3);">{{ number_format($res->nilai_akhir * 100, 1) }}%</span>
                                        </div>
                                    </td>

                                    {{-- Predikat --}}
                                    <td class="text-center py-4">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="badge {{ $badgeClass }} text-[8px] px-3 font-black">{{ $kat }}</span>
                                            <span class="text-[9px] font-medium leading-tight text-center max-w-[130px]" style="color: var(--text-3);">{{ $katLabel }}</span>
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="text-center py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('guru.hasil-evaluasi.detail', $res->siswa_id) }}{{ $periode ? '?periode_id=' . $periode->id_periode : '' }}"
                                               class="w-9 h-9 rounded-xl flex items-center justify-center transition-all shadow-sm"
                                               style="background: var(--accent-lt); color: var(--accent);"
                                               title="Detail SPK"
                                               onmouseover="this.style.background='var(--accent)'; this.style.color='white';"
                                               onmouseout="this.style.background='var(--accent-lt)'; this.style.color='var(--accent)';">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            </a>
                                            <a href="{{ route('guru.laporan', $res->siswa_id) }}"
                                               class="w-9 h-9 rounded-xl flex items-center justify-center transition-all shadow-sm"
                                               style="background: #eff6ff; color: #3b82f6;"
                                               title="Laporan lengkap"
                                               onmouseover="this.style.background='#3b82f6'; this.style.color='white';"
                                               onmouseout="this.style.background='#eff6ff'; this.style.color='#3b82f6';">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer Tabel --}}
                <div class="px-5 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border); background: var(--bg);">
                    <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">
                        Total {{ $total }} siswa &bull; Periode: {{ $periode->nama_periode ?? '—' }}
                    </p>
                    <p class="text-[10px]" style="color: var(--text-3);">
                        Dihasilkan oleh sistem SPK Fuzzy SMART
                    </p>
                </div>
            </div>
        @else
            <div class="card p-20 text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: var(--bg); border: 1px solid var(--border);">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-semibold text-sm" style="color: var(--text-2);">Data tidak tersedia</h3>
                <p class="text-xs mt-2 max-w-xs mx-auto" style="color: var(--text-3);">Siswa Anda belum terdaftar dalam sistem hasil evaluasi periode aktif.</p>
            </div>
        @endif
    @endif

</div>
@endsection
