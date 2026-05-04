@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── WELCOME BANNER ── --}}
    <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-5" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
        <div class="max-w-xl">
            <p class="text-xs mb-2" style="color: rgba(255,255,255,.7);">Selamat datang kembali</p>
            <h1 class="text-xl font-bold text-white leading-tight">{{ $user->nama_lengkap }}</h1>
            <p class="text-sm mt-2 leading-relaxed" style="color: rgba(255,255,255,.85);">
                Wali kelas: <span class="font-bold" style="color: #F1F4E9;">{{ $kelas->pluck('nama_kelas')->implode(', ') ?: '—' }}</span>.
                Pantau perkembangan siswa secara berkala.
            </p>
        </div>
        <a href="{{ route('guru.penilaian') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium flex-shrink-0 transition-opacity hover:opacity-80"
           style="background: #84934A; color: #fff;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Mulai penilaian
        </a>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Periode aktif',   'value' => $periode ? $periode->nama_periode : '—', 'sub' => $periode ? $periode->status : 'Nonaktif',
                 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Minggu aktif',    'value' => $mingguAktif ? 'Minggu '.$mingguAktif->minggu_ke : '—', 'sub' => $mingguAktif ? $mingguAktif->tema : 'Belum ada',
                 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Jumlah siswa',    'value' => $totalSiswa . ' siswa', 'sub' => 'Selesai: ' . $terlayaniCount . '/' . $totalSiswa,
                 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Progress minggu', 'value' => $mingguSelesaiCount . ' / ' . $semuaMinggu->count() . ' selesai',
                 'sub' => ($semuaMinggu->count() > 0 ? round(($mingguSelesaiCount / $semuaMinggu->count()) * 100) : 0) . '% total',
                 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card p-5" style="border-left: 3px solid #84934A;">
            <div class="mb-4">
                <div class="stat-icon inline-flex" style="background: #F0F3E8; color: #84934A;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $s['icon'] }}"/></svg>
                </div>
            </div>
            <p class="text-xs mb-1" style="color: var(--text-3);">{{ $s['label'] }}</p>
            <p class="text-sm font-medium truncate" style="color: var(--text-1);">{{ $s['value'] }}</p>
            <div class="mt-3 pt-3 flex items-center justify-between" style="border-top: 1px solid var(--border);">
                <span class="text-xs" style="color: var(--text-3);">{{ $s['sub'] }}</span>
                @if($loop->last)
                    @php $weekPct = $semuaMinggu->count() > 0 ? round(($mingguSelesaiCount / $semuaMinggu->count()) * 100) : 0; @endphp
                    <div class="w-16 progress-track">
                        <div class="progress-fill progress-green" style="width: {{ $weekPct }}%"></div>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── NOTIFIKASI ── --}}
    @if(!empty($notifikasi))
    <div class="space-y-2">
        @foreach($notifikasi as $notif)
        @php
            $notifStyle = match($notif['type']) {
                'success' => 'background:#F0F3E8; color:#4A5E2A; border-color:#C8D4A8;',
                'warning' => 'background:#FDF8ED; color:#92700A; border-color:#E8D890;',
                default   => 'background:#EEF4FF; color:#3B4FB0; border-color:#C0CEFF;',
            };
        @endphp
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg border text-sm" style="{{ $notifStyle }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $notif['pesan'] }}
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── MAIN GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Progres Penilaian --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                <div>
                    <h3 class="text-sm font-medium" style="color: var(--text-1);">Status penilaian minggu ini</h3>
                    <p class="text-xs mt-0.5" style="color: var(--text-3);">Daftar progres siswa di kelas Anda</p>
                </div>
                @if($mingguAktif)
                    <a href="{{ route('guru.penilaian') }}" class="btn btn-green btn-sm">Input nilai</a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($progresPerSiswa as $s)
                    <div class="flex items-center gap-3 p-4 rounded-lg" style="background: var(--bg); border: 1px solid var(--border);">
                        <div class="w-9 h-9 rounded-lg text-white flex items-center justify-center text-sm font-medium flex-shrink-0" style="background: #84934A;">
                            {{ strtoupper(substr($s['nama'], 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate leading-tight" style="color: var(--text-1);">{{ $s['nama'] }}</p>
                            <p class="text-xs mt-0.5
                                {{ $s['status'] === 'Final' ? 'text-green-600' : ($s['status'] === 'Draft' ? 'text-amber-600' : '') }}"
                               style="{{ $s['status'] === 'Belum' ? 'color: var(--text-3);' : '' }}">
                                {{ $s['status'] }}
                            </p>
                        </div>
                        @if($s['kategori'] !== '-')
                            <span class="badge {{ $s['kategori'] === 'BSB' ? 'badge-bsb' : ($s['kategori'] === 'BSH' ? 'badge-bsh' : 'badge-mb') }}">{{ $s['kategori'] }}</span>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center">
                        <p class="text-sm" style="color: var(--text-3);">
                            @if(!$mingguAktif) Tidak ada minggu aktif saat ini.
                            @else Belum ada data penilaian untuk minggu ini. @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Hasil Evaluasi Terakhir --}}
            @if($latestEvaluasi->isNotEmpty())
            <div class="mt-6 pt-5" style="border-top: 1px solid var(--border);">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-medium" style="color: var(--text-1);">Peringkat capaian akhir (SPK)</h3>
                        <p class="text-xs mt-0.5" style="color: var(--text-3);">Periode: {{ $finalizedPeriode->nama_periode }}</p>
                    </div>
                    <a href="{{ route('guru.hasil-evaluasi') }}" class="text-xs font-medium" style="color: var(--accent);">Lihat semua →</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($latestEvaluasi as $eval)
                        <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--bg); border: 1px solid var(--border);">
                            <div class="flex items-center gap-3">
                                <span class="text-xs w-5 text-center" style="color: var(--text-3);">{{ $loop->iteration }}</span>
                                <span class="text-xs font-medium truncate max-w-[120px]" style="color: var(--text-1);">{{ $eval->siswa->nama }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs" style="color: var(--text-3);">{{ round($eval->nilai_akhir * 100, 1) }}%</span>
                                <span class="badge {{ $eval->kategori_akhir === 'BSB' ? 'badge-bsb' : ($eval->kategori_akhir === 'BSH' ? 'badge-bsh' : 'badge-mb') }}">{{ $eval->kategori_akhir }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar Right --}}
        <div class="space-y-5">
            {{-- Distribusi Nilai --}}
            <div class="card p-5">
                <h4 class="text-xs mb-5" style="color: var(--text-3);">Distribusi nilai global</h4>
                <div class="space-y-4">
                    @forelse($distribusi as $d)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="badge {{ $d['badge'] }}">{{ $d['nama'] }}</span>
                                <span class="text-xs" style="color: var(--text-2);">{{ $d['count'] }} siswa</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill {{ $d['progress'] }}" style="width: {{ $d['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-center py-4" style="color: var(--text-3);">Belum ada data nilai.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Access --}}
            <div class="card p-5">
                <h4 class="text-xs mb-4" style="color: var(--text-3);">Akses cepat</h4>
                <div class="space-y-1">
                    @foreach([
                        ['Input penilaian', route('guru.penilaian'), 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['Riwayat nilai', route('guru.riwayat'), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['Hasil evaluasi (SPK)', route('guru.hasil-evaluasi'), 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138'],
                    ] as $link)
                        <a href="{{ $link[1] }}" class="flex items-center gap-3 px-2 py-2.5 rounded-lg transition-colors"
                           onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background='transparent'">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $link[2] }}"/></svg>
                            </div>
                            <p class="text-sm" style="color: var(--text-1);">{{ $link[0] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
