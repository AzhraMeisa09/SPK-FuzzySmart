@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Overview Sistem')

@section('content')
<div class="space-y-6 pb-12">
    
    {{-- ── WELCOME BANNER ── --}}
    <div class="relative overflow-hidden rounded-[2.5rem] p-8 md:p-12 shadow-2xl border-none" style="background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-black opacity-10 rounded-full blur-2xl"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-300 shadow-[0_0_8px_rgba(134,239,172,0.8)]"></span>
                    <span class="text-[9px] font-bold text-white/90">Sistem Pusat Kendali Admin</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight tracking-tighter">Selamat Datang, Administrator</h1>
                <p class="text-sm md:text-base mt-3 leading-relaxed text-white/80 font-medium">
                    Panel kendali utama untuk mengelola data master, sinkronisasi periode akademik, dan pemantauan real-time progres penilaian guru melalui metodologi Fuzzy SMART.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('admin.siswa.index') }}" class="px-6 py-3 rounded-2xl bg-white text-var(--accent) text-xs font-bold shadow-xl shadow-black/10 hover:scale-105 transition-all">Manajemen Siswa</a>
                    <a href="{{ route('admin.periode.index') }}" class="px-6 py-3 rounded-2xl bg-white/10 border border-white/20 text-white text-xs font-bold backdrop-blur-md hover:bg-white/20 transition-all">Konfigurasi Periode</a>
                </div>
            </div>
            
            <div class="hidden xl:block">
                <div class="w-48 h-48 bg-white/10 rounded-[3rem] border border-white/20 backdrop-blur-sm flex items-center justify-center rotate-12 shadow-2xl">
                    <svg class="w-24 h-24 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['label' => 'Total Siswa',   'value' => $totalSiswa, 'unit' => 'Siswa', 'sub' => 'Terdaftar Aktif', 'color' => '#84934A',
                 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Total Kelas',   'value' => $totalKelas, 'unit' => 'Kelas', 'sub' => 'Ruang Aktif', 'color' => '#3B82F6',
                 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'Total Guru',    'value' => $totalGuru,  'unit' => 'Pendidik', 'sub' => 'Akses Terverifikasi', 'color' => '#8B5CF6',
                 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Agenda Aktif', 'value' => $mingguAktif ? 'M-'.$mingguAktif->minggu_ke : 'OFF', 'unit' => '', 'sub' => $periodeAktif ? $periodeAktif->nama_periode : 'Periode Tidak Aktif', 'color' => '#F59E0B',
                 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card group p-6 shadow-xl border-none hover:translate-y-[-4px] transition-all duration-300">
            <div class="flex items-start justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-colors shadow-sm" style="background: {{ $s['color'] }}10; color: {{ $s['color'] }};">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $s['icon'] }}"/></svg>
                </div>
                <div class="text-[9px] font-bold px-2 py-1 rounded-lg bg-gray-50 text-gray-400 group-hover:bg-var(--accent-lt) group-hover:text-var(--accent) transition-colors">Real-time</div>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] font-bold text-var(--text-3)">{{ $s['label'] }}</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-bold text-var(--text-1) tracking-tight">{{ $s['value'] }}</span>
                    <span class="text-xs font-bold text-var(--text-3)">{{ $s['unit'] }}</span>
                </div>
            </div>
            <div class="mt-5 pt-5 border-t border-gray-50">
                <p class="text-[10px] font-bold text-var(--text-3) flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $s['color'] }};"></span>
                    {{ $s['sub'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Progress per kelas -->
        <div class="lg:col-span-2 card p-8 shadow-xl border-none">
            <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
                <div>
                    <h3 class="text-lg font-bold text-var(--text-1) tracking-tight">Monitoring Progres Penilaian</h3>
                    <p class="text-xs mt-1 text-var(--text-3) font-medium">Visualisasi kelengkapan penginputan nilai guru per kelas.</p>
                </div>
                @if($mingguAktif)
                    <span class="px-4 py-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 text-[10px] font-bold shadow-sm shadow-blue-50">
                        Minggu Ke-{{ $mingguAktif->minggu_ke }} Aktif
                    </span>
                @else
                    <span class="px-4 py-2 rounded-xl bg-gray-50 text-gray-400 border border-gray-100 text-[10px] font-bold">
                        Tidak Ada Minggu Aktif
                    </span>
                @endif
            </div>

            <div class="space-y-8">
                @forelse($progresPerKelas as $c)
                    <div class="group">
                        <div class="flex items-center justify-between mb-2.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-var(--bg) border border-var(--border) flex items-center justify-center font-bold text-[10px] text-var(--text-2) group-hover:bg-var(--accent-lt) group-hover:text-var(--accent) group-hover:border-var(--accent)/20 transition-all">
                                    {{ substr($c['nama'], 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-var(--text-1) tracking-tight">{{ $c['nama'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-bold text-var(--text-3)">{{ $c['terlayani'] }}/{{ $c['total'] }} Siswa</span>
                                <span class="text-xs font-bold {{ $c['persen'] >= 80 ? 'text-green-600' : ($c['persen'] >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $c['persen'] }}%
                                </span>
                            </div>
                        </div>
                        <div class="h-3 bg-gray-50 rounded-full border border-gray-100 overflow-hidden p-0.5">
                            <div class="h-full rounded-full transition-all duration-1000 shadow-sm {{ $c['persen'] >= 80 ? 'bg-green-500' : ($c['persen'] >= 40 ? 'bg-amber-400' : 'bg-red-500') }}" 
                                 style="width: {{ $c['persen'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <div class="w-24 h-24 bg-var(--bg) rounded-full flex items-center justify-center mx-auto mb-6 text-var(--border)">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-var(--text-2)">Belum Ada Progres</h4>
                        <p class="text-xs text-var(--text-3) mt-1 font-medium">Aktifkan minggu penilaian untuk memulai pemantauan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Recent Log / Info -->
        <div class="card p-8 shadow-xl border-none relative overflow-hidden bg-white">
            <div class="relative">
                <h3 class="text-lg font-bold text-var(--text-1) tracking-tight mb-6 border-b border-gray-50 pb-4">Info Periode</h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-var(--accent-lt) text-var(--accent) flex flex-shrink-0 items-center justify-center font-bold">TA</div>
                        <div>
                            <p class="text-[9px] font-bold text-var(--text-3) mb-1">Tahun Ajaran Aktif</p>
                            <p class="text-xs font-bold text-var(--text-1)">{{ $periodeAktif ? $periodeAktif->tahunAjaran->nama : 'Belum Diatur' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex flex-shrink-0 items-center justify-center font-bold">SM</div>
                        <div>
                            <p class="text-[9px] font-bold text-var(--text-3) mb-1">Semester</p>
                            <p class="text-xs font-bold text-var(--text-1)">{{ $periodeAktif ? $periodeAktif->semester : '—' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex flex-shrink-0 items-center justify-center font-bold">PK</div>
                        <div>
                            <p class="text-[9px] font-bold text-var(--text-3) mb-1">Label Periode</p>
                            <p class="text-xs font-bold text-var(--text-1)">{{ $periodeAktif ? $periodeAktif->nama_periode : '—' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 p-6 rounded-[2rem] bg-var(--accent-lt) border border-var(--accent)/10 relative">
                    <p class="text-[10px] font-bold text-var(--accent) mb-2">Pemberitahuan</p>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed font-medium italic">
                        "Pastikan total bobot kriteria selalu berjumlah 1.0 (100%) agar perhitungan Fuzzy SMART akurat."
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
