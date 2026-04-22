@extends('layouts.app')
@section('title', 'Perkembangan Anak')
@section('page-title', 'Perkembangan')

@section('content')
<div class="space-y-5">

    @if($selectedAnak)
    <!-- Child Profile Header -->
    <div class="card p-6 flex flex-wrap items-center gap-6 shadow-sm border-none bg-white">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-2xl flex-shrink-0 shadow-inner">
            {{ strtoupper(substr($selectedAnak->nama, 0, 1)) }}
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $selectedAnak->nama }}</h2>
            <p class="text-xs text-slate-400 font-medium mt-1 uppercase tracking-wider">
                NISN: {{ $selectedAnak->kode }} • {{ $selectedAnak->kelas->nama_kelas ?? 'Tanpa Kelas' }} • {{ $periodeAktif ? $periodeAktif->nama_periode : '-' }}
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            @if($anak->count() > 1)
            <div class="flex items-center gap-2 mr-4">
                <form action="{{ route('wali.perkembangan') }}" method="GET" id="childSwitchForm" class="flex gap-2">
                    <select name="siswa_id" onchange="this.form.submit()" class="form-select text-xs py-1.5 px-3 rounded-lg border-slate-200 font-bold text-slate-600 focus:ring-indigo-500">
                        @foreach($anak as $a)
                            <option value="{{ $a->id }}" {{ $selectedAnak->id == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Timeline Perkembangan -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest pl-1">Timeline Perkembangan Mingguan</h3>
            <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Urutan Terbaru</span>
        </div>
        
        <div class="relative">
            <!-- Vertical line -->
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-indigo-200 via-slate-100 to-transparent"></div>

            <div class="space-y-6 pl-14 pb-10">
                @forelse($mingguSelesai as $m)
                    <div class="relative">
                        <!-- Dot -->
                        <div class="absolute -left-9 top-4 w-4 h-4 rounded-full border-2 border-white shadow-md bg-indigo-500 z-10"></div>

                        <div class="card overflow-hidden border-none shadow-sm group hover:shadow-md transition-all duration-300">
                            <!-- Header -->
                            <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-white">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 text-slate-700 flex items-center justify-center font-black text-sm flex-shrink-0">
                                        M{{ $m->minggu_ke }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-800 text-sm tracking-tight leading-none mb-1.5">{{ $m->tema }}</h4>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[11px] text-slate-400 font-bold">{{ $m->tanggal_mulai->format('d M') }} - {{ $m->tanggal_selesai->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-final text-[10px] py-1 px-3">Sudah Dinilai</span>
                                </div>
                            </div>

                            <!-- Subkriteria results -->
                            <div class="px-6 py-5 bg-white">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                    @foreach($m->jadwalSubkriteria as $jadwal)
                                        @php 
                                            $nil = $jadwal->penilaian->first(); // filtered by student in controller
                                            if(!$nil) continue;
                                            $catClass = strtolower($nil->kategori->nama);
                                            $bgClass = $catClass == 'bsb' ? 'bg-green-50 border-green-100' : ($catClass == 'bsh' ? 'bg-blue-50 border-blue-100' : 'bg-amber-50 border-amber-100');
                                            $txtClass = $catClass == 'bsb' ? 'text-green-700' : ($catClass == 'bsh' ? 'text-blue-700' : 'text-amber-700');
                                        @endphp
                                        <div class="flex items-start justify-between gap-3 p-4 rounded-2xl border {{ $bgClass }} transition-colors">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-black {{ $txtClass }} uppercase tracking-wide mb-1">{{ $jadwal->subkriteria->nama }}</p>
                                                @if($nil->catatan)
                                                    <p class="text-[11px] text-slate-500 leading-relaxed italic mt-2 opacity-80">"{{ $nil->catatan }}"</p>
                                                @endif
                                            </div>
                                            <span class="badge badge-{{ $catClass }} flex-shrink-0 font-black">{{ $nil->kategori->nama }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Summary Catatan if any --}}
                                @php 
                                    $allCatatan = $m->jadwalSubkriteria->map->penilaian->flatten()->whereNotNull('catatan')->pluck('catatan')->join('. ');
                                    $waliKelas = $selectedAnak->kelas && $selectedAnak->kelas->guru->first() ? $selectedAnak->kelas->guru->first()->nama_lengkap : 'Wali Kelas';
                                @endphp
                                
                                <div class="flex items-start gap-4 p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-400 shadow-sm flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Info & Saran {{ $waliKelas }}</p>
                                        <p class="text-[11px] text-slate-500-700 italic leading-relaxed">
                                            {{ $allCatatan ?: 'Tidak ada catatan khusus untuk minggu ini. Aditya mengikuti pembelajaran dengan baik.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Belum Ada Riwayat</h3>
                        <p class="text-sm text-slate-500 mt-2 max-w-sm">Data penilaian mingguan akan muncul di sini setelah Admin memfinalisasi laporan setiap minggunya.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @else
        <div class="card p-20 flex flex-col items-center text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Akun Belum Terhubung</h3>
            <p class="text-sm text-slate-500 mt-2 max-w-sm">Hubungi pihak sekolah untuk menghubungkan akun wali murid Anda.</p>
        </div>
    @endif
</div>
@endsection
