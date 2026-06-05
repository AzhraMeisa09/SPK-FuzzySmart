@extends('layouts.app')
@section('title', 'Portofolio Karya — ' . $selectedAnak->name)
@section('page-title', 'Karya Anak')

@section('content')
<div class="space-y-8 pb-20 fade-in">

    {{-- ── HERO BANNER ── --}}
    <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100 no-print" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform overflow-hidden">
                @if($selectedAnak->foto)
                    <img src="{{ asset('storage/' . $selectedAnak->foto) }}" class="w-full h-full object-cover" alt="{{ $selectedAnak->name }}">
                @else
                    🎨
                @endif
            </div>
            <div class="text-center md:text-left">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Dokumentasi Portofolio</p>
                <h1 class="text-2xl font-black tracking-tight text-white">{{ $selectedAnak->name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="px-3 py-1 rounded-lg bg-white/20 text-white text-[10px] font-black backdrop-blur-sm uppercase tracking-widest">Kelas: {{ $selectedAnak->kelas->nama_kelas ?? '—' }}</span>
                    @if($periode)
                        <span class="px-3 py-1 rounded-lg bg-white/20 text-white text-[10px] font-black backdrop-blur-sm uppercase tracking-widest">{{ $periode->nama_periode }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-wrap justify-center md:justify-end gap-3 items-center">
            @if(isset($listPeriode) && $listPeriode->count() > 1)
                <form action="{{ route('wali.portofolio') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="siswa_id" value="{{ $selectedAnak->id_siswa }}">
                    <span class="text-xs font-bold text-white/80 whitespace-nowrap">Pilih Periode:</span>
                    <select name="periode_id" onchange="this.form.submit()" class="bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-black rounded-xl px-4 py-2.5 focus:ring-0 focus:border-white/40 cursor-pointer appearance-none outline-none" style="padding-right: 32px;">
                        @foreach($listPeriode as $p)
                            <option value="{{ $p->id_periode }}" class="text-gray-900" {{ $periode && $periode->id_periode == $p->id_periode ? 'selected' : '' }}>
                                {{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
            <p class="text-[11px] font-medium text-white/70">Kumpulan karya dan aktivitas terbaik pilihan guru.</p>
        </div>
    </div>

    {{-- ── STUDENT SWITCHER ── --}}
    @if($anak->count() > 1)
        <div class="flex flex-wrap items-center gap-4 no-print">
            @foreach($anak as $a)
                <a href="{{ route('wali.portofolio', array_filter(['siswa_id' => $a->id_siswa, 'periode_id' => $periode?->id_periode])) }}" 
                   class="group flex items-center gap-4 px-6 py-3.5 rounded-[2rem] transition-all duration-500 border {{ $selectedAnak->id_siswa == $a->id_siswa ? 'bg-white border-var(--accent) shadow-2xl ring-4 ring-var(--accent-lt)' : 'bg-white border-gray-100 opacity-60 hover:opacity-100 hover:border-gray-200 shadow-sm' }}">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black transition-all duration-500 group-hover:rotate-6 {{ $selectedAnak->id_siswa == $a->id_siswa ? 'bg-var(--accent) text-white shadow-lg shadow-var(--accent-lt)' : 'bg-gray-100 text-gray-400' }} overflow-hidden">
                        @if($a->foto)
                            <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover" alt="{{ $a->name }}">
                        @else
                            {{ strtoupper(substr($a->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black tracking-tight {{ $selectedAnak->id_siswa == $a->id_siswa ? 'text-gray-900' : 'text-gray-500' }}">{{ $a->name }}</span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $a->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- ── PORTOFOLIO GRID ── --}}
    @if($portofolio_list->count() > 0)
        {{-- Section header --}}
        <div class="flex items-center justify-between px-2">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Karya & Portofolio</h3>
            <span class="badge badge-gray px-3 py-1 text-[9px] font-black uppercase">{{ $portofolio_list->count() }} Karya</span>
        </div>

        @php
            $groupedByMinggu = $portofolio_list->groupBy(fn($p) => $p->minggu->minggu_ke ?? 0);
        @endphp

        @foreach($groupedByMinggu as $mingguKe => $items)
            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shadow-sm" style="background: var(--accent-lt); border: 1px solid var(--border); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-widest">Minggu {{ $mingguKe }}</h4>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $items->count() }} Karya</p>
                    </div>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $port)
                        <div class="card overflow-hidden flex flex-col group hover:-translate-y-1 transition-all duration-300 border-none shadow-xl">
                            <div class="p-6 flex-1">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 bg-var(--accent-lt) text-var(--accent) text-[9px] font-black rounded-lg uppercase tracking-widest border border-var(--accent)/10 shadow-sm">Minggu {{ $port->minggu->minggu_ke }}</span>
                                    @php
                                        $pm = \App\Models\PenilaianMingguan::where('siswa_id', $port->siswa_id)
                                            ->whereHas('jadwalSubkriteria', function($q) use ($port) {
                                                $q->where('minggu_id', $port->minggu_id);
                                            })->avg('nilai_crisp');
                                        
                                        $katObj = \App\Models\KategoriNilai::findByNilai($pm ?? 0);
                                        $kat = $katObj ? $katObj->nama : 'MB';
                                        $color = $kat === 'BSB' ? 'emerald' : ($kat === 'BSH' ? 'amber' : 'rose');
                                    @endphp
                                    <span class="badge {{ 'badge-'.$color }} px-3 py-1 font-bold text-[9px]">{{ $kat }}</span>
                                </div>
                                <h3 class="text-sm font-black text-gray-800 leading-tight mb-3 tracking-tight group-hover:text-var(--accent) transition-colors">{{ $port->judul }}</h3>
                                <div class="relative">
                                    <p class="text-[11px] text-gray-500 leading-relaxed italic font-medium pl-3 border-l-2 border-gray-100" style="white-space: pre-wrap;">"{{ $port->deskripsi }}"</p>
                                </div>
                            </div>
                            
                            @if($port->images->count() > 0)
                                <div class="p-4 bg-gray-50/50 border-t border-gray-50">
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach($port->images as $img)
                                            <div class="aspect-square rounded-xl overflow-hidden border-2 border-white cursor-pointer relative group/img shadow-sm" 
                                                 x-data x-on:click="$dispatch('open-lightbox', '{{ asset('storage/' . $img->file_path) }}')">
                                                <img src="{{ asset('storage/' . $img->file_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-125">
                                                <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/30 transition-all flex items-center justify-center">
                                                    <div class="w-8 h-8 rounded-full bg-white shadow-lg flex items-center justify-center scale-0 group-hover/img:scale-100 transition-transform">
                                                        <svg class="w-4 h-4 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="card p-24 text-center border-none shadow-xl">
            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-gray-200 border border-gray-100 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400">Belum ada karya yang diunggah</p>
        </div>
    @endif
</div>

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
