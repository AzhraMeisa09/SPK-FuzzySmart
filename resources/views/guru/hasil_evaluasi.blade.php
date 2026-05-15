@extends('layouts.app')
@section('title', 'Hasil Evaluasi SPK')
@section('page-title', 'Hasil Evaluasi SPK')

@section('content')
<div class="space-y-8 fade-in pb-12">

    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Hasil evaluasi akhir (SPK)</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Hasil perhitungan menggunakan metode <span class="font-semibold" style="color: var(--text-1);">Fuzzy SMART</span> untuk menentukan tingkat perkembangan siswa.</p>
            </div>
            @if($periode)
            <div class="px-4 py-2.5 rounded-xl" style="background: var(--accent-lt); border: 1px solid var(--border);">
                <span class="text-[9px] font-bold uppercase tracking-wider" style="color: var(--text-3);">Periode aktif</span>
                <p class="text-sm font-semibold mt-0.5" style="color: var(--text-1);">{{ $periode->nama_periode }}</p>
            </div>
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
        {{-- ── DATA GRID ── --}}
        @if($results->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results as $res)
                    @php
                        $kat = $res->kategori_akhir;
                        $colorClass = match($kat) {
                            'BSB' => 'emerald',
                            'BSH' => 'amber',
                            'MB'  => 'rose',
                            default => 'gray'
                        };
                        $progColor = match($kat) {
                            'BSB' => 'progress-green',
                            'BSH' => 'progress-yellow',
                            'MB'  => 'progress-red',
                            default => ''
                        };
                    @endphp
                    <div class="card p-5 card-hover relative group flex flex-col" style="border-top: 3px solid var(--accent);">
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg" style="background: var(--accent-lt); color: var(--accent);">
                                    {{ strtoupper(substr($res->siswa->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background: var(--bg); color: var(--text-3);">#{{ $loop->iteration }}</span>
                                        <h3 class="font-semibold truncate" style="color: var(--text-1);">{{ $res->siswa->name }}</h3>
                                    </div>
                                    <p class="text-[9px] font-bold uppercase tracking-wider" style="color: var(--text-3);">{{ $res->siswa->kelas->nama ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="badge {{ 'badge-'.$colorClass }} text-[9px] px-3 font-bold">{{ $kat }}</span>
                        </div>

                        <div class="flex-1 space-y-4">
                            {{-- Score Indicator --}}
                            <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[9px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Skor keputusan</span>
                                    <span class="text-lg font-bold" style="color: var(--text-1);">{{ number_format($res->nilai_akhir, 3) }}</span>
                                </div>
                                <div class="progress-track h-2">
                                    <div class="progress-fill h-2 {{ $progColor }}" style="width: {{ $res->nilai_akhir * 100 }}%"></div>
                                </div>
                            </div>
                            
                            {{-- Recommendation Snippet --}}
                            <div class="px-1">
                                <p class="text-[9px] font-bold uppercase tracking-widest mb-1.5" style="color: var(--text-3);">Rekomendasi utama</p>
                                <p class="text-xs leading-relaxed italic" style="color: var(--text-2);">
                                    &ldquo;{{ $res->rekomendasi ?? 'Analisis rekomendasi belum tersedia.' }}&rdquo;
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-6 pt-4 flex gap-3" style="border-top: 1px solid var(--border);">
                            <a href="{{ route('guru.hasil-evaluasi.detail', $res->siswa_id) }}" 
                               class="flex-1 btn btn-gray justify-center text-xs py-2.5 text-gray-900">
                                Detail SPK
                            </a>
                            <a href="{{ route('guru.laporan', $res->siswa_id) }}" 
                               class="btn btn-gray px-4 py-2.5" title="Laporan lengkap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
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
