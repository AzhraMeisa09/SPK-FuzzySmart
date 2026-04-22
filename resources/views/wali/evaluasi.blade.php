@extends('layouts.app')
@section('title', 'Evaluasi Akhir Anak')
@section('page-title', 'Evaluasi')

@section('content')
<div class="space-y-5">

    @if($selectedAnak)
    <!-- Child Profile Card -->
    <div class="card p-6 flex flex-wrap items-center gap-6 shadow-sm border-none bg-white">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-700 font-black text-2xl flex-shrink-0">
            {{ strtoupper(substr($selectedAnak->nama, 0, 1)) }}
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $selectedAnak->nama }}</h2>
            <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                {{ $selectedAnak->kelas->nama_kelas ?? 'Tanpa Kelas' }} • {{ $periode ? $periode->nama_periode : 'Periode Tidak Ditemukan' }}
            </p>
        </div>
        
        @if($evaluasi)
        <div class="flex items-center gap-3">
            <div class="text-center px-6 py-3 rounded-2xl bg-indigo-50 border border-indigo-100 shadow-sm">
                <p class="text-[10px] text-indigo-600 font-black uppercase tracking-widest mb-1">Nilai Akhir</p>
                <p class="text-3xl font-black text-indigo-700 tracking-tighter">{{ number_format($evaluasi->nilai_akhir, 0) }}%</p>
                <div class="mt-1">
                    <span class="badge badge-{{ strtolower($evaluasi->kategori_akhir) }} text-xs">{{ $evaluasi->kategori_akhir }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($evaluasi)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Score Visual (Circular SVG) -->
        <div class="card p-8 text-center shadow-sm border-none bg-white">
            <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest mb-6">Skor Total SPK</h3>
            <div class="relative w-40 h-40 mx-auto mb-6">
                <svg class="w-full h-full transform -rotate-90 filter drop-shadow-sm" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="42" stroke="#f1f5f9" stroke-width="8" fill="none"/>
                    <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="8" fill="none"
                            class="text-indigo-600"
                            stroke-dasharray="{{ 263.8 * ($evaluasi->nilai_akhir / 100) }} 263.8"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-800 tracking-tighter">{{ round($evaluasi->nilai_akhir) }}%</span>
                    <span class="text-[11px] font-black {{ $evaluasi->nilai_akhir >= 80 ? 'text-green-600' : 'text-indigo-600' }} uppercase mt-0.5 tracking-widest">
                        {{ $evaluasi->kategori_akhir }}
                    </span>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Metode Fuzzy SMART</p>
        </div>

        <!-- Per kriteria progress -->
        <div class="md:col-span-2 card p-6 shadow-sm border-none bg-white">
            <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest mb-6">Capaian Per Aspek Utama</h3>
            <div class="space-y-6">
                @foreach($evaluasi->detail as $det)
                    @php
                        $score = $det->nilai_akhir * 100;
                        $kategori = $score >= 80 ? 'BSB' : ($score >= 60 ? 'BSH' : 'MB');
                        $pClass = $score >= 80 ? 'progress-green' : ($score >= 60 ? 'progress-blue' : 'progress-yellow');
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-wide">{{ $det->kriteria->nama }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black text-slate-800">{{ round($score) }}%</span>
                                <span class="badge badge-{{ strtolower($kategori) }} text-[10px]">{{ $kategori }}</span>
                            </div>
                        </div>
                        <div class="progress-track h-2 bg-slate-50">
                            <div class="progress-fill h-full rounded-full {{ $pClass }} transition-all duration-1000" style="width: {{ $score }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recommendations Section -->
    @if($evaluasi->rekomendasi->count() > 0)
    <div>
        <div class="flex items-center gap-2 mb-4 pl-1">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest leading-none mt-0.5">Rekomendasi Stimulasi di Rumah</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($evaluasi->rekomendasi as $idx => $rec)
                @php
                    $colors = [
                        ['bg-green-50 border-green-100 text-green-700 hover:bg-green-100', 'bg-green-200'],
                        ['bg-blue-50 border-blue-100 text-blue-700 hover:bg-blue-100', 'bg-blue-200'],
                        ['bg-purple-50 border-purple-100 text-purple-700 hover:bg-purple-100', 'bg-purple-200'],
                        ['bg-amber-50 border-amber-100 text-amber-700 hover:bg-amber-100', 'bg-amber-200'],
                    ];
                    $c = $colors[$idx % 4];
                @endphp
                <div class="card p-5 {{ $c[0] }} border transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl {{ $c[1] }} flex items-center justify-center font-bold shadow-sm flex-shrink-0">
                            {{ $idx + 1 }}
                        </div>
                        <div>
                            <h4 class="font-black {{ $c[0] == 'bg-green-50' ? 'text-green-800' : 'text-slate-800' }} mb-2 tracking-tight">Rekomendasi #{{ $idx + 1 }}</h4>
                            <p class="text-sm opacity-80 leading-relaxed">{{ $rec->rekomendasi }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
        <div class="card p-10 bg-slate-50 border-none flex flex-col items-center text-center">
             <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 mb-4">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             </div>
             <p class="text-sm text-slate-400 font-medium italic">Sistem belum mengenerate rekomendasi otomatis untuk periode ini.</p>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex flex-wrap gap-4 pt-4 pb-10">
        <a href="{{ route('wali.laporan') }}" class="btn btn-green px-8 py-3.5 shadow-lg shadow-green-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Rapor Lengkap
        </a>
        <a href="{{ route('wali.perkembangan') }}" class="btn btn-white border-slate-200 px-8 py-3.5 shadow-sm">Lihat Timeline Detail</a>
    </div>

    @else
        {{-- Empty State: No Finalized Evaluation found --}}
        <div class="card p-20 flex flex-col items-center text-center shadow-sm border-none bg-white">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Evaluasi Belum Siap</h3>
            <p class="text-sm text-slate-500 mt-3 max-w-sm">Mohon maaf, hasil evaluasi akhir periode <strong>{{ $periode ? $periode->nama_periode : 'sedang berjalan' }}</strong> belum difinalisasi oleh Admin. Silakan cek menu Perkembangan untuk melihat laporan mingguan yang tersedia.</p>
            <div class="mt-8">
                <a href="{{ route('wali.perkembangan') }}" class="btn btn-blue px-10 py-3 shadow-lg shadow-blue-200 rounded-xl">Lihat Laporan Mingguan</a>
            </div>
        </div>
    @endif

    @else
        {{-- Akun belum terhubung --}}
        <div class="card p-20 flex flex-col items-center text-center shadow-sm border-none">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Akun Belum Terhubung</h3>
            <p class="text-sm text-slate-500 mt-2 max-w-sm">Segera hubungi Admin Sekolah untuk mendaftarkan data siswa ke akun Anda.</p>
        </div>
    @endif
</div>
@endsection
