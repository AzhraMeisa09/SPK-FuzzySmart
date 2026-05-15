@extends('layouts.app')
@section('title', 'Detail Evaluasi SPK — ' . $siswa->name)
@section('page-title', 'Evaluasi Final')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── STUDENT SWITCHER ── --}}
    @if($anak->count() > 1)
        <div class="flex flex-wrap items-center gap-3 no-print">
            @foreach($anak as $a)
                <a href="{{ route('wali.evaluasi', ['siswa_id' => $a->id_siswa]) }}" 
                   class="group flex items-center gap-3 px-4 py-2.5 rounded-2xl transition-all border {{ $siswa->id_siswa == $a->id_siswa ? 'bg-white border-var(--accent) shadow-md ring-4 ring-var(--accent-lt)' : 'bg-white border-gray-100 opacity-60 hover:opacity-100 hover:border-gray-200 shadow-sm' }}">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-transform group-hover:scale-110 {{ $siswa->id_siswa == $a->id_siswa ? 'bg-var(--accent) text-white shadow-lg shadow-var(--accent-lt)' : 'bg-gray-100 text-gray-400' }} overflow-hidden">
                        @if($a->foto)
                            <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover" alt="{{ $a->name }}">
                        @else
                            {{ strtoupper(substr($a->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="text-[11px] font-bold {{ $siswa->id_siswa == $a->id_siswa ? 'text-var(--text-1)' : 'text-gray-500' }}">{{ $a->name }}</span>
                </a>
            @endforeach
        </div>
    @endif

    @if(!$evaluasi)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Laporan Belum Tersedia</h3>
            <p class="text-xs mt-2 max-w-sm mx-auto" style="color: var(--text-3);">
                Laporan evaluasi final untuk {{ $siswa->name }} belum diterbitkan oleh pihak sekolah atau periode penilaian belum berakhir.
            </p>
            <div class="mt-8">
                <a href="{{ route('wali.dashboard') }}" class="btn btn-gray py-2.5 px-8 text-xs font-bold">Kembali ke Dashboard</a>
            </div>
        </div>
    @else
        {{-- ── HERO BANNER ── --}}
        <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100 no-print" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform overflow-hidden">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover" alt="{{ $siswa->name }}">
                    @else
                        🎓
                    @endif
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Laporan Hasil Evaluasi SPK</p>
                    <h1 class="text-2xl font-black tracking-tight text-white">{{ $siswa->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-3 text-[10px] font-bold">
                        @if($periode)
                            <span class="px-3 py-1 rounded-lg bg-white/20 text-white backdrop-blur-sm uppercase tracking-widest">{{ $periode->nama_periode }}</span>
                        @endif
                        <span class="text-white/40 uppercase">Kelas: {{ $siswa->kelas->nama_kelas ?? '—' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap justify-center md:justify-end gap-3">
                <a href="{{ route('wali.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white text-[#84934A] hover:bg-[#F1F4E9] transition-all shadow-lg shadow-black/5">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
        {{-- ── ANALYTIC HERO SECTION ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Summary Info --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="card p-8 group hover:border-var(--accent)/20 transition-all duration-300">
                <div class="space-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-5 flex items-center gap-3">
                            <span class="w-8 h-px bg-gray-200"></span>
                            Rekomendasi Utama (SPK)
                        </h4>
                        <div class="p-6 rounded-2xl bg-var(--bg) border border-var(--border) relative overflow-hidden group/recom">
                            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover/recom:scale-110 transition-transform">
                                <svg class="w-16 h-16 text-var(--accent)" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.5 3c1.557 0 3.046.716 3.945 2.031C12.344 3.715 13.833 3 15.39 3 18.286 3 20.75 5.322 20.75 8.25c0 3.924-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001z"/></svg>
                            </div>
                            <p class="text-sm font-bold text-gray-800 leading-relaxed italic relative z-10 text-justify">
                                &ldquo;{{ $evaluasi->rekomendasi }}&rdquo;
                            </p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-5 flex items-center gap-3">
                            <span class="w-8 h-px bg-gray-200"></span>
                            Catatan Perkembangan Guru
                        </h4>
                        <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                {{ $evaluasi->catatan_guru ?: 'Belum ada catatan tambahan dari guru untuk laporan periode ini.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Final Score Card --}}
        <div class="lg:col-span-4">
            <div class="card p-8 flex flex-col items-center justify-center text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-var(--accent-lt)/30 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8 relative z-10">Indeks Capaian Akhir</p>
                
                <div class="relative mb-8 flex justify-center">
                    <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle -->
                        <circle cx="50" cy="50" r="44" stroke="currentColor" stroke-width="6" fill="transparent" class="text-gray-100"/>
                        <!-- Progress Circle -->
                        @php 
                            $circumference = 2 * pi() * 44;
                            $offset = $circumference - ($evaluasi->nilai_akhir * $circumference);
                            $strokeColor = $evaluasi->kategori_akhir === 'BSB' ? '#10b981' : ($evaluasi->kategori_akhir === 'BSH' ? '#f59e0b' : '#f43f5e');
                        @endphp
                        <circle cx="50" cy="50" r="44" stroke="{{ $strokeColor }}" stroke-width="8" fill="transparent" 
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="{{ $offset }}"
                                stroke-linecap="round"
                                class="transition-all duration-1000 shadow-lg"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-gray-900 leading-none">{{ number_format($evaluasi->nilai_akhir, 3) }}</span>
                        <span class="text-[9px] font-black text-gray-400 mt-2 uppercase tracking-[0.2em]">Skor (V)</span>
                    </div>
                </div>

                <div class="relative z-10 w-full space-y-6">
                    @php $color = $evaluasi->kategori_akhir === 'BSB' ? 'emerald' : ($evaluasi->kategori_akhir === 'BSH' ? 'amber' : 'rose'); @endphp
                    <span class="badge {{ 'badge-'.$color }} px-8 py-2 text-[11px] font-black shadow-lg uppercase">{{ $evaluasi->kategori_akhir }}</span>
                    
                    <div class="grid grid-cols-2 gap-4 pt-8 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Ranking</p>
                            <p class="text-lg font-black text-gray-900 tracking-tighter">#{{ $ranking }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Siswa</p>
                            <p class="text-lg font-black text-gray-900 tracking-tighter">{{ $totalSiswa ?? 0 }} Orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CRITERIA ANALYSIS ── --}}
    <div class="space-y-5">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-2">Analisis Capaian Aspek</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($details as $kriteriaName => $items)
                @php 
                    $avg = $items->avg('nilai_crisp');
                    $katObj = \App\Models\KategoriNilai::findByNilai($avg ?? 0);
                    $kat = $katObj ? $katObj->nama : 'MB';
                    $color = $kat === 'BSB' ? 'emerald' : ($kat === 'BSH' ? 'amber' : 'rose');
                    $progressBarColor = $kat === 'BSB' ? 'bg-green-500 shadow-green-100' : ($kat === 'BSH' ? 'bg-amber-500 shadow-amber-100' : 'bg-red-500 shadow-red-100');
                @endphp
                <div class="card p-6 flex flex-col group hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-[10px] font-black text-gray-800 uppercase tracking-wider truncate mr-3">{{ $kriteriaName }}</h4>
                        <span class="badge {{ 'badge-'.$color }} px-3 py-1 font-bold text-[9px]">{{ $kat }}</span>
                    </div>
                    
                    <div class="flex items-end gap-2 mb-4">
                        <span class="text-3xl font-black text-gray-900 leading-none tracking-tighter">{{ number_format($avg, 1) }}</span>
                        <span class="text-[10px] font-black text-gray-400 mb-1">%</span>
                    </div>

                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden mb-6 shadow-inner">
                        <div class="h-full {{ $progressBarColor }} transition-all duration-1000 shadow-lg" style="width: {{ $avg }}%"></div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Indikator</span>
                        <span class="text-[10px] font-black text-gray-900">{{ $items->count() }} Poin</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── INDICATOR DETAILS ── --}}
    <div class="space-y-5">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-2">Rincian Capaian per Indikator</h3>

        <div class="card overflow-hidden border-none shadow-xl">
            <table class="tbl">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="w-20 text-center">ID</th>
                        <th>Indikator Capaian</th>
                        <th class="text-center w-40">Kategori</th>
                        <th class="text-right w-40">Skor Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($evaluasi->detail as $d)
                        @php
                            $rubrikKey = 'rubrik_' . strtolower($d->kategori);
                            $rubrikText = $d->subkriteria->$rubrikKey ?? null;
                            if($rubrikText) {
                                $rubrikText = str_replace('{{nama_siswa}}', $siswa->name, $rubrikText);
                            }
                        @endphp
                        <tr class="hover:bg-var(--bg) transition-colors">
                            <td class="text-center font-mono text-[10px] font-bold text-gray-400">{{ $d->subkriteria->id_subkriteria }}</td>
                            <td class="py-5">
                                <p class="text-sm font-black text-gray-800 tracking-tight leading-tight mb-1.5">{{ $d->subkriteria->nama_subkriteria }}</p>
                                @if($rubrikText)
                                    <p class="text-[11px] text-gray-500 italic mb-2 leading-relaxed">&ldquo;{{ $rubrikText }}&rdquo;</p>
                                @endif
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $d->subkriteria->kriteria->nama_kriteria }}</p>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $d->kategori === 'BSB' ? 'badge-bsb' : ($d->kategori === 'BSH' ? 'badge-bsh' : 'badge-mb') }} px-4 py-1 font-bold">{{ $d->kategori }}</span>
                            </td>
                            <td class="text-right">
                                <span class="text-sm font-black text-gray-900 font-mono tracking-tighter">{{ number_format($d->nilai_crisp, 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── RECOMMENDATION TABLE (AS GURU PAGE) ── --}}
    <div class="space-y-5">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-var(--accent-lt) text-var(--accent)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Rekomendasi Strategis per Aspek</h3>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Analisis mendalam sistem pakar fuzzy smart</p>
            </div>
        </div>

        <div class="card p-8">
            <div class="space-y-10">
                @foreach($details as $kriteriaName => $items)
                    @php
                        $rekList = $items->filter(fn($item) => $item->rekomendasi_detail && !str_contains(strtolower($item->rekomendasi_detail), 'sangat baik'));
                    @endphp
                    @if($rekList->isNotEmpty())
                        <div class="relative pl-8">
                            <div class="absolute left-0 top-0 bottom-0 w-px bg-gray-100"></div>
                            <div class="absolute left-0 top-0 w-px h-8 bg-var(--accent)"></div>
                            
                            <h5 class="text-[11px] font-black text-var(--accent) uppercase tracking-[0.15em] mb-4 flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-var(--accent) -ml-[4.5px] border-4 border-white shadow-sm"></span>
                                {{ $kriteriaName }}
                            </h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($rekList as $item)
                                    <div class="p-5 rounded-2xl bg-var(--bg) border border-var(--border) hover:border-var(--accent)/20 transition-colors group/rek">
                                        <div class="flex items-start gap-3 mb-2">
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item->subkriteria->id_subkriteria }}</span>
                                            <p class="text-[10px] font-black text-gray-700 uppercase tracking-tight">{{ $item->subkriteria->nama_subkriteria }}</p>
                                        </div>
                                        <p class="text-xs font-bold text-gray-600 leading-relaxed italic">&ldquo;{{ $item->rekomendasi_detail }}&rdquo;</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── PORTOFOLIO SECTION ── --}}
    <div class="space-y-5">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-var(--accent-lt) text-var(--accent)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider">Portofolio & Dokumentasi</h3>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Evidence-based progress records</p>
            </div>
        </div>

        <div class="card p-8">
            @forelse($portofolio_list->groupBy('minggu_id') as $mingguId => $items)
                <div class="mb-10 last:mb-0">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="px-4 py-1.5 rounded-xl bg-var(--accent-lt) text-var(--accent) text-[10px] font-black uppercase tracking-widest border border-var(--accent)/10 shadow-sm">Minggu {{ $items->first()->minggu->minggu_ke }}</span>
                        <div class="h-px flex-1 bg-gray-50"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($items as $p)
                            <div class="p-6 rounded-[2rem] bg-var(--bg) border border-var(--border) hover:border-var(--accent)/20 transition-all group/port shadow-sm">
                                <div class="flex flex-col gap-5">
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach($p->images as $img)
                                            <div class="aspect-square rounded-2xl overflow-hidden border-2 border-white shadow-sm hover:scale-105 transition-transform">
                                                <img src="{{ asset('storage/'.$img->file_path) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="space-y-3">
                                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-tight group-hover:text-var(--accent) transition-colors">{{ $p->judul }}</h4>
                                        <p class="text-[11px] leading-relaxed text-gray-600 font-medium" style="white-space: pre-wrap;">{{ $p->deskripsi }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-20 text-center rounded-[2.5rem] bg-var(--bg) border border-dashed border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Tidak ada portofolio pada periode ini</p>
                </div>
            @endforelse
        </div>
    </div>


    @endif
</div>
@endsection
