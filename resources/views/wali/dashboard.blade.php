@extends('layouts.app')
@section('title', 'Dashboard Wali Murid')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 fade-in">

    @if($selectedAnak)
    {{-- ── WELCOME BANNER ── --}}
    <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform overflow-hidden">
                @if($selectedAnak->foto)
                    <img src="{{ asset('storage/' . $selectedAnak->foto) }}" class="w-full h-full object-cover" alt="{{ $selectedAnak->name }}">
                @else
                    {{ strtoupper(substr($selectedAnak->name, 0, 1)) }}
                @endif
            </div>
            <div class="text-center md:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Informasi Siswa Aktif</p>
                <h1 class="text-2xl font-black tracking-tight text-white">{{ $selectedAnak->name }}</h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-3">
                    <span class="px-3 py-1 text-[10px] font-bold rounded-lg bg-white/20 text-white backdrop-blur-sm">
                        {{ $selectedAnak->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                    </span>
                    <span class="text-[10px] font-mono font-bold text-white/60">ID: {{ $selectedAnak->id_siswa }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 justify-center md:justify-end">
            @if($anak->count() > 1)
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold bg-white/10 hover:bg-white/20 text-white transition-all backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Ganti Anak
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white border border-gray-100 z-50 p-2" x-cloak>
                        @foreach($anak as $a)
                            <a href="?siswa_id={{ $a->id_siswa }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[11px] font-bold transition-all {{ $selectedAnak->id_siswa == $a->id_siswa ? 'bg-var(--accent-lt) text-var(--accent)' : 'text-gray-600 hover:bg-gray-50' }}">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ $selectedAnak->id_siswa == $a->id_siswa ? 'bg-var(--accent) text-white' : 'bg-gray-100 text-gray-400' }} overflow-hidden">
                                    @if($a->foto)
                                        <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover" alt="{{ $a->name }}">
                                    @else
                                        {{ strtoupper(substr($a->name, 0, 1)) }}
                                    @endif
                                </div>
                                {{ $a->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            <a href="{{ route('wali.laporan', ['siswa_id' => $selectedAnak->id_siswa]) }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white text-[#84934A] hover:bg-[#F1F4E9] transition-all shadow-lg shadow-black/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Rapor
            </a>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @php
            $valAkhir = $evaluasiTerakhir ? number_format($evaluasiTerakhir->nilai_akhir * 100, 1) . '%' : '—';
            $katAkhir = $evaluasiTerakhir ? $evaluasiTerakhir->kategori_akhir : '—';
            $isLive = isset($evaluasiTerakhir->is_live);
            
            $stats = [
                ['label' => 'Periode Aktif', 'value' => $periodeAktif ? $periodeAktif->nama_periode : '—', 'sub' => $periodeAktif ? $periodeAktif->tahunAjaran->nama : 'Nonaktif', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'blue'],
                ['label' => 'Indeks Capaian', 'value' => $valAkhir, 'sub' => $isLive ? 'Real-time Progress' : 'Hasil Evaluasi SPK', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'indigo'],
                ['label' => 'Kategori Akhir', 'value' => $katAkhir, 'sub' => $isLive ? 'Estimasi Sementara' : 'Status Penilaian', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z', 'color' => 'emerald'],
                ['label' => 'Wali Kelas', 'value' => $selectedAnak->kelas && $selectedAnak->kelas->guru->isNotEmpty() ? $selectedAnak->kelas->guru->first()->nama_lengkap : '—', 'sub' => 'Guru Pendamping', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'amber'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="card p-5 group hover:border-var(--accent) transition-all duration-300" style="border-left: 4px solid #84934A;">
            <div class="flex items-center gap-4 mb-4">
                <div class="stat-icon flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $s['icon'] }}"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-3);">{{ $s['label'] }}</p>
                    <p class="text-sm font-black truncate leading-tight" style="color: var(--text-1);">{{ $s['value'] }}</p>
                </div>
            </div>
            <div class="pt-3 flex items-center justify-between" style="border-top: 1px solid var(--border);">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ $s['sub'] }}</span>
                <svg class="w-3 h-3 text-gray-300 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── MAIN CONTENT GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left: Penilaian Terbaru --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card overflow-hidden">
                <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 bg-gray-50/30">
                    <div>
                        <h3 class="text-sm font-bold" style="color: var(--text-1);">Aktivitas & Penilaian Terbaru</h3>
                        <p class="text-[10px] mt-0.5 font-medium" style="color: var(--text-3);">Data capaian mingguan yang telah diverifikasi</p>
                    </div>
                    <a href="{{ route('wali.perkembangan', ['siswa_id' => $selectedAnak->id_siswa]) }}" class="text-[11px] font-bold text-var(--accent) hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="p-6 space-y-3">
                    @forelse($penilaianTerbaru as $pt)
                        <div class="group flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-var(--accent)/20 hover:bg-var(--bg) transition-all duration-300">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-[10px] shadow-sm flex-shrink-0 {{ $pt->kategori->nama === 'BSB' ? 'bg-green-50 text-green-600 border border-green-100' : ($pt->kategori->nama === 'BSH' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                {{ $pt->kategori->nama }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold truncate leading-tight" style="color: var(--text-1);">{{ $pt->jadwalSubkriteria->subkriteria->nama_subkriteria }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $pt->jadwalSubkriteria->minggu->tema }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Minggu {{ $pt->jadwalSubkriteria->minggu->minggu_ke }}</span>
                                </div>
                            </div>
                            <div class="text-right hidden sm:flex flex-col items-end flex-shrink-0">
                                <span class="text-[10px] font-bold" style="color: var(--text-1);">{{ $pt->updated_at->diffForHumans() }}</span>
                                <div class="flex items-center gap-1 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-1 h-1 rounded-full bg-var(--accent)"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-var(--accent)">Verified</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-inner">
                                <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-300">Belum ada penilaian baru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pesan dari Guru --}}
            @php $latestNote = $penilaianTerbaru->whereNotNull('catatan')->first(); @endphp
            @if($latestNote)
            <div class="card p-6 bg-amber-50/30 border-amber-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-6 opacity-[0.03] group-hover:rotate-12 transition-transform">
                    <svg class="w-32 h-32 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 7.55228 14.017 7V5C14.017 4.44772 14.4647 4 15.017 4H19.017C20.6738 4 22.017 5.34315 22.017 7V15C22.017 18.3137 19.3307 21 16.017 21H14.017ZM2.01697 21L2.01697 18C2.01697 16.8954 2.9124 16 4.01697 16H7.01697C7.56925 16 8.01697 15.5523 8.01697 15V9C8.01697 8.44772 7.56925 8 7.01697 8H3.01697C2.46468 8 2.01697 7.55228 2.01697 7V5C2.01697 4.44772 2.46468 4 3.01697 4H7.01697C8.67383 4 10.017 5.34315 10.017 7V15C10.017 18.3137 7.33069 21 4.01697 21H2.01697Z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-amber-800">Catatan Evaluasi Guru</h4>
                    </div>
                    <div class="relative pl-6">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-200 rounded-full"></div>
                        <p class="text-sm text-amber-900 leading-relaxed font-bold italic tracking-tight" style="white-space: pre-wrap;">"{{ $latestNote->catatan }}"</p>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center text-[8px] font-black text-amber-600">M{{ $latestNote->jadwalSubkriteria->minggu->minggu_ke }}</div>
                        <span class="text-[9px] font-bold text-amber-600/60 uppercase tracking-widest">Minggu Aktif {{ $latestNote->jadwalSubkriteria->minggu->minggu_ke }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Navigasi Cepat --}}
            <div class="card p-6">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-6 flex items-center gap-2" style="color: var(--text-3);">
                    Akses Cepat
                    <span class="h-px flex-1 bg-gray-100"></span>
                </h4>
                <div class="grid grid-cols-1 gap-2.5">
                    @foreach([
                        ['Laporan Perkembangan', route('wali.perkembangan', ['siswa_id' => $selectedAnak->id_siswa]), 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'blue'],
                        ['Portofolio Karya', route('wali.portofolio', ['siswa_id' => $selectedAnak->id_siswa]), 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'purple'],
                        ['Hasil Evaluasi (SPK)', route('wali.evaluasi', ['siswa_id' => $selectedAnak->id_siswa]), 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'indigo'],
                    ] as $link)
                        <a href="{{ $link[1] }}" class="group flex items-center gap-4 p-3 rounded-2xl hover:bg-var(--bg) transition-all duration-300 border border-transparent hover:border-gray-100">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-white border border-gray-100" style="color: var(--accent);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $link[2] }}"/></svg>
                            </div>
                            <span class="text-xs font-bold group-hover:text-var(--accent) transition-colors" style="color: var(--text-2);">{{ $link[0] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Info Alert --}}
            <div class="card p-7 border-none shadow-xl text-white relative overflow-hidden group" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-blue-300 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em]">Pemberitahuan</h4>
                    </div>
                    <p class="text-xs leading-relaxed text-blue-100/70 font-bold tracking-tight">
                        Sistem Penilaian (SPK) akan melakukan perhitungan otomatis pada setiap akhir periode. Pastikan memantau riwayat mingguan secara berkala.
                    </p>
                    <div class="mt-6 pt-5 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[9px] font-black text-blue-300/40 uppercase tracking-widest">Update Otomatis</span>
                        <div class="flex gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 opacity-40"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
        <div class="card p-24 text-center border-none shadow-xl">
            <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-gray-200 border border-gray-100 shadow-inner">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Akun Belum Terhubung</h3>
            <p class="text-sm text-gray-400 mt-4 max-w-sm mx-auto leading-relaxed font-bold">
                Data siswa tidak ditemukan untuk akun ini. Mohon hubungi pihak sekolah untuk sinkronisasi data wali murid.
            </p>
            <button onclick="location.reload()" class="btn btn-gray mt-8 px-8 font-black text-[10px]">Coba Lagi</button>
        </div>
    @endif
</div>
@endsection
