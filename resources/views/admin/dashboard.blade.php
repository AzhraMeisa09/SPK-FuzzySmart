@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="space-y-5">
    <!-- Welcome + Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['Total Siswa', $totalSiswa, 'bg-blue-50 text-blue-600', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'Siswa terdaftar'],
                ['Total Kelas', $totalKelas, 'bg-green-50 text-green-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'Ruang kelas aktif'],
                ['Total Guru', $totalGuru, 'bg-amber-50 text-amber-600', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'Pendidik aktif'],
                ['Periode Aktif', $periodeAktif ? $periodeAktif->tahunAjaran->nama . ' ' . $periodeAktif->semester : 'None', 'bg-purple-50 text-purple-600', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', $mingguAktif ? 'Minggu ke-' . $mingguAktif->minggu_ke : 'Belum ada minggu aktif'],
            ];
        @endphp
        @foreach($stats as $s)
            <div class="card p-4 card-hover">
                <div class="flex justify-between items-start mb-3">
                    <div class="stat-icon {{ $s[2] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $s[3] }}"/></svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 font-medium">{{ $s[0] }}</p>
                <p class="text-2xl font-black text-gray-900 leading-tight">{{ $s[1] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5 font-bold uppercase tracking-tight">{{ $s[4] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Left: Progress per kelas -->
        <div class="lg:col-span-2 card p-5">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50">
                <div>
                    <h3 class="font-black text-gray-800 tracking-tight">Progres Penilaian per Kelas</h3>
                    <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Pantau kelengkapan nilai guru setiap minggunya</p>
                </div>
                <div class="text-right">
                    @if($mingguAktif)
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase border border-indigo-100 italic">
                            M-{{ $mingguAktif->minggu_ke }} Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-50 text-red-500 rounded-lg text-[10px] font-black uppercase border border-red-100">
                            Tidak Ada Minggu Aktif
                        </span>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                @forelse($progresPerKelas as $c)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-sm font-bold text-gray-700">{{ $c['nama'] }}</span>
                            <div class="flex items-center gap-2 text-[10px] font-black uppercase">
                                <span class="text-gray-400">{{ $c['terlayani'] }}/{{ $c['total'] }} siswa</span>
                                <span class="{{ $c['persen'] >= 80 ? 'text-green-600' : ($c['persen'] >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $c['persen'] }}%
                                </span>
                            </div>
                        </div>
                        <div class="progress-track bg-gray-100 h-2">
                            <div class="progress-fill h-2 {{ $c['persen'] >= 80 ? 'progress-green' : ($c['persen'] >= 40 ? 'progress-yellow' : 'progress-red') }}" 
                                 style="width: {{ $c['persen'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium italic">Aktifkan minggu penilaian untuk melihat progres.</p>
                    </div>
                @endforelse
            </div>

            <!-- Recent activity Placeholder -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Aktivitas Terbaru</h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 py-3 px-4 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                        <p class="text-[11px] font-medium text-slate-500 italic">Sistem siap digunakan. Silakan mulai melakukan penilaian.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Quick summary -->
        <div class="space-y-4">
            <!-- Distribusi -->
            <div class="card p-5">
                <h4 class="font-black text-gray-800 mb-4 text-xs uppercase tracking-widest">Distribusi Nilai</h4>
                @forelse($distribusi as $d)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="badge {{ $d['badge'] }} text-[10px] font-black">{{ $d['nama'] }}</span>
                            <span class="text-[10px] text-gray-600 font-black">{{ $d['count'] }} entri ({{ $d['percent'] }}%)</span>
                        </div>
                        <div class="progress-track bg-gray-100 h-1.5">
                            <div class="progress-fill h-1.5 {{ $d['progress'] }}" style="width: {{ $d['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Belum ada data nilai.</p>
                @endforelse
            </div>

            <!-- Info card -->
            <div class="card p-5" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0;">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-xs font-black text-green-800 uppercase tracking-widest">Status Sistem</h4>
                </div>
                <div class="space-y-2.5">
                    @php
                        $statusItems = [
                            ['Active Period', $periodeAktif ? 'Valid' : 'Invalid', $periodeAktif ? 'green' : 'red'],
                            ['Active Week', $mingguAktif ? 'M-' . $mingguAktif->minggu_ke : 'None', $mingguAktif ? 'green' : 'amber'],
                            ['Total Guru', $totalGuru . ' Terdaftar', 'green'],
                        ];
                    @endphp
                    @foreach($statusItems as $st)
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-green-700/70 font-bold tracking-tight">{{ $st[0] }}</span>
                            <span class="px-2 py-0.5 bg-{{ $st[2] }}-100 text-{{ $st[2] }}-700 rounded font-black uppercase text-[9px]">{{ $st[1] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
