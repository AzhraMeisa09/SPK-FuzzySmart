@extends('layouts.app')
@section('title', 'Laporan Perkembangan — ' . $siswa->name)
@section('page-title', 'Perkembangan')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── HERO BANNER ── --}}
    <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100 no-print" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform overflow-hidden">
                @if($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover" alt="{{ $siswa->name }}">
                @else
                    📈
                @endif
            </div>
            <div class="text-center md:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Analisis Riwayat Mingguan</p>
                <h1 class="text-2xl font-black tracking-tight text-white">{{ $siswa->name }}</h1>
                <p class="text-[11px] mt-2 font-medium text-white/80">Pantau grafik dan detail perkembangan anak setiap minggunya.</p>
            </div>
        </div>
        <div class="flex flex-wrap justify-center md:justify-end gap-3">
            <a href="{{ route('wali.evaluasi', ['siswa_id' => $siswa->id_siswa]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white/10 hover:bg-white/20 text-white transition-all backdrop-blur-sm">
                Hasil Evaluasi Final
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white text-[#84934A] hover:bg-[#F1F4E9] transition-all shadow-lg shadow-black/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak
            </button>
        </div>
    </div>

    {{-- ── STUDENT SWITCHER ── --}}
    @if(count($anak) > 1)
        <div class="flex flex-wrap items-center gap-3 no-print">
            @foreach($anak as $a)
                <a href="{{ route('wali.perkembangan', ['siswa_id' => $a->id_siswa]) }}" 
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

    {{-- ── ANALYTIC STATS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        @php
            $statData = [
                ['label' => 'Total Minggu', 'val' => $divisor, 'sub' => 'Data Terinput', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue'],
                ['label' => 'Rata-rata Skor', 'val' => ($avgTotal ? number_format($avgTotal, 1) : '—').'%', 'sub' => 'Capaian Global', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'indigo'],
                ['label' => 'Status Akhir', 'val' => $finalKat, 'sub' => 'Kategori Global', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z', 'color' => 'emerald'],
                ['label' => 'Total Indikator', 'val' => ($bsbCount+$bshCount+$mbCount), 'sub' => 'Poin Penilaian', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'amber'],
            ];
        @endphp
        @foreach($statData as $s)
            <div class="card p-5 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm" 
                         style="background: var(--bg); border: 1px solid var(--border); color: var(--accent);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $s['icon'] }}"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-3);">{{ $s['label'] }}</p>
                        <p class="text-sm font-black truncate {{ $s['val'] === 'BSB' ? 'text-green-600' : ($s['val'] === 'BSH' ? 'text-amber-500' : ($s['val'] === 'MB' ? 'text-rose-500' : '')) }}" style="color: var(--text-1);">
                            {{ $s['val'] }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 pt-4 flex items-center justify-between" style="border-top: 1px solid var(--border);">
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $s['sub'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TIMELINE ── --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Timeline Perkembangan</h3>
            <span class="badge badge-gray px-3 py-1 text-[9px] font-black uppercase">{{ $mingguGrouped->count() }} Minggu</span>
        </div>

        @if($mingguGrouped->count() > 0)
            <div class="space-y-4" x-data="{ openMinggu: null }">
                @foreach($mingguGrouped as $index => $m)
                    @php
                        $mBadge = match($m['col']) { 'emerald'=>'badge-bsb shadow-green-100', 'amber'=>'badge-bsh shadow-amber-100', 'rose'=>'badge-mb shadow-red-100', default=>'badge-gray shadow-gray-100' };
                    @endphp
                    <div class="card overflow-hidden transition-all duration-300 border-none shadow-xl" :class="openMinggu === {{ $index }} ? 'ring-2 ring-var(--accent) bg-gray-50/30' : ''">
                        <button @click="openMinggu = (openMinggu === {{ $index }} ? null : {{ $index }})"
                                class="w-full flex items-center gap-6 px-8 py-5 text-left hover:bg-gray-50/50 transition-colors relative group">
                            <div class="absolute left-0 top-0 bottom-0 w-1 transition-all group-hover:w-1.5 {{ $m['col'] === 'emerald' ? 'bg-green-500' : ($m['col'] === 'amber' ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                            
                            <div class="w-12 h-12 rounded-2xl flex flex-col items-center justify-center flex-shrink-0 shadow-inner bg-white border border-gray-100">
                                <span class="text-[8px] font-black uppercase leading-none opacity-40">MG</span>
                                <span class="text-xl font-black leading-none mt-1" style="color: var(--text-1);">{{ $m['minggu_ke'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-black text-gray-800 truncate tracking-tight">{{ $m['tema'] }}</h4>
                                <div class="flex items-center gap-3 mt-1.5 text-[9px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                                    <span>{{ $m['tanggal'] }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                    <span>{{ $m['status'] }}</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 flex flex-col items-end">
                                <p class="text-sm font-black text-gray-900 leading-none">{{ number_format($m['avg'], 1) }}%</p>
                                <span class="badge {{ $mBadge }} mt-2.5 px-3 py-1 font-bold text-[9px]">{{ $m['kategori'] }}</span>
                            </div>
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-300 border border-transparent" :class="openMinggu === {{ $index }} ? 'rotate-180 bg-var(--accent-lt) text-var(--accent) border-var(--accent)/10' : 'text-gray-300'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <div x-show="openMinggu === {{ $index }}" x-collapse style="display:none">
                            <div class="px-8 py-8 space-y-8 bg-white border-t border-gray-100">
                                {{-- ASSESSMENT TABLE --}}
                                @if($m['details']->isNotEmpty())
                                    <div class="space-y-4">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Rincian Capaian Indikator</p>
                                        <div class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm bg-white">
                                            <table class="tbl">
                                                <thead>
                                                    <tr class="bg-gray-50/50">
                                                        <th class="w-20 text-center">ID</th>
                                                        <th>Indikator Capaian</th>
                                                        <th class="text-center w-36">Kategori</th>
                                                        <th class="text-right w-24">Skor</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50">
                                                    @foreach($m['details'] as $idx => $d)
                                                        @php $dBadge = match($d['col']) { 'emerald'=>'badge-bsb', 'amber'=>'badge-bsh', 'rose'=>'badge-mb', default=>'badge-gray' }; @endphp
                                                        <tr class="hover:bg-var(--bg) transition-colors">
                                                            <td class="text-center font-mono text-[10px] font-bold text-gray-400">{{ $d['id_subkriteria'] }}</td>
                                                            <td class="py-4">
                                                                <p class="font-bold text-gray-800 text-sm leading-tight tracking-tight">{{ $d['subkriteria'] }}</p>
                                                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">{{ $d['kriteria'] }}</p>
                                                                @if($d['catatan'])
                                                                    <div class="mt-3 p-4 rounded-2xl bg-amber-50/50 border border-amber-100 flex gap-3 relative overflow-hidden group/note">
                                                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400"></div>
                                                                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                                        <p class="text-xs text-amber-900 leading-relaxed font-medium italic" style="white-space: pre-wrap;">"{{ $d['catatan'] }}"</p>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge {{ $dBadge }} px-3 py-1 font-bold">{{ $d['kategori'] }}</span>
                                                            </td>
                                                            <td class="text-right font-mono font-black text-gray-900 text-xs">{{ $d['capaian'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                {{-- PORTOFOLIO SECTION --}}
                                @if($m['portofolios']->isNotEmpty())
                                    <div class="space-y-5">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest px-1">Dokumentasi Portofolio</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            @foreach($m['portofolios'] as $p)
                                                <div class="p-5 rounded-2xl bg-gray-50/50 border border-gray-100 shadow-sm flex flex-col gap-4 group/p">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white shadow-sm border border-gray-100 group-hover/p:scale-110 transition-transform" style="color: var(--accent);">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        </div>
                                                        <h6 class="text-xs font-black text-gray-800 tracking-tight">{{ $p->judul }}</h6>
                                                    </div>
                                                    <p class="text-[11px] text-gray-500 font-medium leading-relaxed italic border-l-2 border-gray-200 pl-3" style="white-space: pre-wrap;">"{{ $p->deskripsi }}"</p>
                                                    <div class="grid grid-cols-3 gap-3">
                                                        @foreach($p->images as $img)
                                                                <div class="aspect-square rounded-xl overflow-hidden border-2 border-white shadow-sm group/img relative group-hover/p:rotate-1 transition-transform cursor-pointer"
                                                                     @click="$dispatch('open-lightbox', '{{ asset('storage/'.$img->file_path) }}')">
                                                                    <img src="{{ asset('storage/'.$img->file_path) }}" class="w-full h-full object-cover">
                                                                    <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/20 transition-all flex items-center justify-center">
                                                                        <div class="w-8 h-8 rounded-full bg-white shadow-lg flex items-center justify-center scale-0 group-hover/img:scale-100 transition-transform">
                                                                            <svg class="w-4 h-4 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-24 text-center border-none shadow-xl">
                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-200 border border-gray-100 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-[11px] font-black uppercase tracking-widest text-gray-400">Belum ada riwayat perkembangan</p>
            </div>
        @endif
    </div>

</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card { border: 1px solid #eee !important; box-shadow: none !important; }
        .badge { border: 1px solid #eee !important; }
    }
</style>

{{-- ── LIGHTBOX MODAL ── --}}
<div x-data="{ isOpen: false, imgUrl: '' }" 
     @open-lightbox.window="isOpen = true; imgUrl = $event.detail"
     x-show="isOpen" 
     x-cloak
     class="fixed inset-0 z-[10000] flex items-center justify-center p-6 no-print"
     style="background: rgba(9, 9, 11, 0.72); backdrop-filter: blur(8px);"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <button @click="isOpen = false" class="absolute top-8 right-8 w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all border border-white/10">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <div class="relative max-w-5xl w-full max-h-[85vh] flex items-center justify-center">
        <img :src="imgUrl" @click.away="isOpen = false" class="max-w-full max-h-full object-contain rounded-3xl shadow-[0_0_50px_rgba(0,0,0,0.5)] border-4 border-white/10">
    </div>
</div>
@endsection
