@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-5 fade-in">

    {{-- ── WELCOME BANNER (Hijau = warna sidebar) ────── --}}
    <div class="relative overflow-hidden rounded-2xl p-6 text-white"
         style="background: linear-gradient(135deg, #15803d 0%, #16a34a 60%, #22c55e 100%);">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-green-100/80 text-xs font-semibold uppercase tracking-widest mb-1">Selamat datang kembali,</p>
                <h1 class="text-2xl font-black tracking-tight">{{ $user->nama }}</h1>
                <p class="text-green-100/70 text-sm mt-1 font-medium">
                    Wali Kelas: <span class="text-white font-bold">{{ $kelas->pluck('nama')->implode(', ') ?: '—' }}</span>
                </p>
            </div>
            <a href="{{ route('guru.penilaian') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-green-700 font-black text-sm rounded-xl hover:bg-green-50 transition-all active:scale-95 shadow-md flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Mulai Penilaian
            </a>
        </div>
        {{-- Decorative circles --}}
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="absolute right-20 -bottom-10 w-24 h-24 rounded-full bg-white/5"></div>
    </div>

    {{-- ── STATS GRID (4 kartu, serasi sidebar hijau) ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Siswa --}}
        <div class="card card-hover p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Siswa</p>
            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalSiswa }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Di kelas Anda</p>
        </div>

        {{-- Minggu Aktif --}}
        <div class="card card-hover p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon {{ $mingguAktif ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @if($mingguAktif)
                    <span class="badge badge-aktif text-[9px]">Aktif</span>
                @endif
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minggu Aktif</p>
            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ $mingguAktif ? 'M-'.$mingguAktif->minggu_ke : '—' }}</p>
            <p class="text-[11px] text-gray-400 mt-1 truncate">{{ $mingguAktif ? $mingguAktif->tema : 'Belum ada jadwal' }}</p>
        </div>

        {{-- Progres Input --}}
        <div class="card card-hover p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon bg-blue-50 text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sudah Dinilai</p>
            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ $terlayaniCount }} <span class="text-base font-semibold text-gray-300">/ {{ $totalSiswa }}</span></p>
            <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-green-500 transition-all duration-700"
                     style="width: {{ $totalSiswa > 0 ? ($terlayaniCount/$totalSiswa)*100 : 0 }}%"></div>
            </div>
        </div>

        {{-- Belum Dinilai --}}
        <div class="card card-hover p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon bg-red-50 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Belum Dinilai</p>
            <p class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalSiswa - $terlayaniCount }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Perlu tindak lanjut</p>
        </div>
    </div>

    {{-- ── MAIN GRID ───────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Status Siswa (kiri, 2 col) --}}
        <div class="lg:col-span-2 card p-5">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50">
                <div>
                    <h3 class="font-black text-gray-900">Status Penilaian Siswa</h3>
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-widest mt-0.5">Minggu aktif berjalan</p>
                </div>
                <a href="{{ route('guru.penilaian') }}" class="text-[11px] text-green-600 font-bold hover:underline">+ Input Nilai</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($progresPerSiswa as $s)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50/60 border border-gray-100 hover:bg-green-50/30 hover:border-green-100 transition-all duration-150">
                        <div class="w-9 h-9 rounded-xl bg-green-600 text-white flex items-center justify-center text-sm font-black flex-shrink-0">
                            {{ strtoupper(substr($s['nama'], 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate leading-tight">{{ $s['nama'] }}</p>
                            <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $s['status'] }}</p>
                        </div>
                        @if($s['kategori'] !== '-')
                            <span class="badge {{ $s['kategori'] === 'BSB' ? 'badge-bsb' : ($s['kategori'] === 'BSH' ? 'badge-bsh' : 'badge-mb') }} text-[9px]">
                                {{ $s['kategori'] }}
                            </span>
                        @else
                            <span class="badge badge-nonaktif text-[9px]">—</span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 py-10 text-center text-gray-300 italic text-sm">Tidak ada siswa ditemukan.</div>
                @endforelse
            </div>

            <div class="mt-4 pt-4 border-t border-gray-50 flex gap-3">
                <a href="{{ route('guru.penilaian') }}" class="flex-1 btn btn-green justify-center text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Input Nilai
                </a>
                <a href="{{ route('guru.riwayat') }}" class="flex-1 btn btn-gray justify-center text-xs">
                    Lihat Riwayat
                </a>
            </div>
        </div>

        {{-- Sidebar Right --}}
        <div class="space-y-4">
            {{-- Distribusi Nilai --}}
            <div class="card p-5">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Distribusi Nilai</h4>
                <div class="space-y-4">
                    @forelse($distribusi as $d)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="badge {{ $d['badge'] }} text-[9px]">{{ $d['nama'] }}</span>
                                <span class="text-[11px] font-black text-gray-600">{{ $d['count'] }} ({{ $d['percent'] }}%)</span>
                            </div>
                            <div class="progress-track h-1.5">
                                <div class="progress-fill h-1.5 {{ $d['progress'] }}" style="width: {{ $d['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic text-center py-4">Belum ada data nilai.</p>
                    @endforelse
                </div>
            </div>

            {{-- Status Sistem --}}
            <div class="card p-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="stat-icon bg-green-100 text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-[10px] font-black text-green-800 uppercase tracking-widest">Status Sistem</h4>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-green-700/60 font-semibold">Periode Aktif</span>
                        <span class="px-2 py-0.5 {{ $mingguAktif ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }} rounded font-black uppercase text-[9px]">
                            {{ $mingguAktif ? 'Valid' : 'None' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-green-700/60 font-semibold">Jadwal Minggu</span>
                        <span class="px-2 py-0.5 {{ $mingguAktif ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} rounded font-black uppercase text-[9px]">
                            {{ $mingguAktif ? 'M-'.$mingguAktif->minggu_ke : 'Belum' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-green-700/60 font-semibold">Total Kelas</span>
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded font-black uppercase text-[9px]">
                            {{ $kelas->count() }} Kelas
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Nav --}}
            <div class="card p-4">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Akses Cepat</h4>
                <div class="space-y-1">
                    @foreach([
                        ['Input Penilaian', route('guru.penilaian'), 'Nilai siswa minggu ini', 'text-green-600'],
                        ['Riwayat Nilai', route('guru.riwayat'), 'Lihat semua penilaian', 'text-blue-500'],
                        ['Rekap Nilai', route('guru.rekap'), 'Statistik & distribusi', 'text-violet-500'],
                        ['Laporan', route('guru.laporan'), 'Cetak laporan siswa', 'text-amber-500'],
                    ] as $link)
                        <a href="{{ $link[1] }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                            <div class="w-1.5 h-1.5 rounded-full {{ $link[3] }} bg-current flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $link[0] }}</p>
                                <p class="text-[10px] text-gray-400">{{ $link[2] }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
