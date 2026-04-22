@extends('layouts.app')
@section('title', 'Dashboard Wali Murid')
@section('page-title', 'Portal Wali Murid')

@section('content')
<div class="space-y-5">

    @if($selectedAnak)
    <!-- Child info hero -->
    <div class="rounded-xl overflow-hidden relative shadow-lg" style="background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 100%);">
        <div class="px-6 py-8 relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-white font-black text-2xl flex-shrink-0 shadow-inner">
                {{ strtoupper(substr($selectedAnak->nama, 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-1 opacity-80">Data Siswa</p>
                <h1 class="text-white text-2xl font-black tracking-tight">{{ $selectedAnak->nama }}</h1>
                <p class="text-blue-100 text-sm font-medium mt-1">
                    {{ $selectedAnak->kelas->nama_kelas ?? 'Tanpa Kelas' }} • NISN: {{ $selectedAnak->kode }}
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                @php
                    $catAkhir = $evaluasiTerakhir ? $evaluasiTerakhir->kategori_akhir : '-';
                    $nilaiAkhir = $evaluasiTerakhir ? number_format($evaluasiTerakhir->nilai_akhir, 0) . '%' : '-';
                    $statusPeriode = $periodeAktif ? $periodeAktif->nama_periode : 'Tidak Aktif';
                @endphp
                <div class="px-4 py-2 bg-white/10 rounded-xl border border-white/20 text-center backdrop-blur-sm">
                    <p class="text-blue-200 text-[10px] font-bold uppercase tracking-wider mb-0.5">Nilai Akhir</p>
                    <p class="text-white font-black text-sm">{{ $nilaiAkhir }}</p>
                </div>
                <div class="px-4 py-2 bg-white/10 rounded-xl border border-white/20 text-center backdrop-blur-sm">
                    <p class="text-blue-200 text-[10px] font-bold uppercase tracking-wider mb-0.5">Kategori</p>
                    <p class="text-white font-black text-sm">{{ $catAkhir }}</p>
                </div>
            </div>
        </div>
        <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    </div>

    <!-- Stats Summary if Finalized -->
    @if($evaluasiTerakhir && $evaluasiTerakhir->nilaiPerKriteria()->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($evaluasiTerakhir->nilaiPerKriteria() as $det)
            <div class="card p-4 text-center card-hover border-none shadow-sm bg-white">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 truncate">{{ $det->kriteria->nama }}</p>
                @php
                    $score = $det->nilai_akhir * 100;
                    $colorClass = $score >= 80 ? 'bg-green-50 text-green-600' : ($score >= 60 ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600');
                    $badgeClass = $score >= 80 ? 'bsb' : ($score >= 60 ? 'bsh' : 'mb');
                    $kategori = $score >= 80 ? 'BSB' : ($score >= 60 ? 'BSH' : 'MB');
                @endphp
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $colorClass }} mx-auto mb-3 font-black text-sm shadow-sm">
                    {{ round($score) }}%
                </div>
                <span class="badge badge-{{ $badgeClass }}">{{ $kategori }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Last Assessment Row -->
        <div class="lg:col-span-2 space-y-5">
            <div class="card p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-black text-gray-800 tracking-tight">Aktivitas Terakhir</h3>
                        <p class="text-xs text-gray-400 font-medium">Berdasarkan penilaian guru yang sudah difinalisasi</p>
                    </div>
                </div>
                
                @if($penilaianTerbaru->count() > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($penilaianTerbaru as $pt)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:border-blue-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-1.5 h-8 rounded-full {{ $pt->status == 'final' ? 'bg-green-400' : 'bg-amber-400' }}"></div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700">{{ $pt->jadwalSubkriteria->subkriteria->nama }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $pt->jadwalSubkriteria->minggu->tema }} • M{{ $pt->jadwalSubkriteria->minggu->minggu_ke }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="badge badge-{{ strtolower($pt->kategori->nama) }}">{{ $pt->kategori->nama }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @php $firstWithCatatan = $penilaianTerbaru->whereNotNull('catatan')->first(); @endphp
                    @if($firstWithCatatan)
                    <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100 flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-500 shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Catatan Guru (M{{ $firstWithCatatan->jadwalSubkriteria->minggu->minggu_ke }})</p>
                            <p class="text-sm text-blue-900/70 italic leading-relaxed">"{{ $firstWithCatatan->catatan }}"</p>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="py-12 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium italic">Belum ada penilaian mingguan yang difinalisasi.</p>
                    </div>
                @endif
                
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('wali.perkembangan') }}" class="flex-1 btn btn-blue justify-center text-xs py-3 rounded-xl shadow-md">Timeline Perkembangan</a>
                    <a href="{{ route('wali.evaluasi') }}" class="flex-1 btn btn-white justify-center text-xs py-3 border border-slate-200 rounded-xl shadow-sm">Hasil SPK</a>
                </div>
            </div>
        </div>

        <!-- Right side info -->
        <div class="space-y-5">
            @if($anak->count() > 1)
            <div class="card p-5 shadow-sm border-none bg-indigo-50/30">
                <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4">Pilih Data Anak</h4>
                <div class="space-y-2">
                    @foreach($anak as $a)
                        <a href="?siswa_id={{ $a->id }}" class="flex items-center gap-3 p-3 rounded-xl border {{ $selectedAnak->id == $a->id ? 'bg-indigo-600 text-white border-indigo-700 shadow-lg shadow-indigo-200' : 'bg-white text-gray-700 border-slate-100 hover:border-indigo-200' }} transition-all">
                             <div class="w-8 h-8 rounded-lg {{ $selectedAnak->id == $a->id ? 'bg-white/20' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($a->nama, 0, 1)) }}
                             </div>
                             <span class="text-xs font-bold truncate">{{ $a->nama }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card p-5 shadow-sm border-none bg-white">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Periode</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-800">{{ $periodeAktif ? $periodeAktif->nama_periode : 'Periode Tidak Aktif' }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">{{ $periodeAktif ? $periodeAktif->tahunAjaran->nama : '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-800">Wali Kelas</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">
                                {{ $selectedAnak->kelas && $selectedAnak->kelas->guru->first() ? $selectedAnak->kelas->guru->first()->nama_lengkap : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-5 bg-green-700 border-none shadow-lg shadow-green-200 relative overflow-hidden" style="background: linear-gradient(135deg, #15803d, #16a34a);">
                <div class="relative z-10">
                    <p class="text-white/70 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Pelaporan Akhir</p>
                    <p class="text-white font-black text-sm mb-4 leading-tight">Laporan Hasil Belajar (Rapor) Digital</p>
                    <a href="{{ route('wali.laporan') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-white text-green-700 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-green-50 transition-colors shadow-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Rapor
                    </a>
                </div>
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full"></div>
            </div>
        </div>

    </div>
    @else
        <div class="card p-20 flex flex-col items-center text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Akun Belum Terhubung</h3>
            <p class="text-sm text-slate-500 mt-2 max-w-sm">Mohon maaf, akun Anda belum terhubung dengan data siswa manapun. Harap hubungi Admin Sekolah untuk sinkronisasi data Wali Murid.</p>
        </div>
    @endif
</div>
@endsection
