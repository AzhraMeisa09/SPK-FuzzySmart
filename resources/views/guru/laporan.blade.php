@extends('layouts.app')
@section('title', 'Laporan Perkembangan Siswa')
@section('page-title', 'Laporan')

@section('content')
<div class="space-y-6 fade-in pb-12">

    {{-- ── HERO BANNER ── --}}
    @if($selectedSiswaId && !empty($reportData))
        @php
            $siswa = $reportData['siswa'];
            $evaluasi = $reportData['evaluasi'];
            $details = $reportData['detail_evaluasi_grouped'];
            $portofolio_list = $reportData['portofolio_list'];
            $ranking = $reportData['ranking'];
            $totalSiswa = $reportData['total_siswa'];
            $anak = $allSiswa;
        @endphp

        <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100 no-print" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform overflow-hidden">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover" alt="{{ $siswa->name }}">
                    @else
                        📄
                    @endif
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Pratinjau Laporan Akhir</p>
                    <h1 class="text-2xl font-black tracking-tight text-white">{{ $siswa->name }}</h1>
                    <p class="text-[11px] mt-2 font-medium text-white/80">Laporan ini menggabungkan hasil evaluasi SPK dan portofolio kegiatan.</p>
                </div>
            </div>
            <div class="flex flex-wrap justify-center md:justify-end gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white/10 hover:bg-white/20 text-white transition-all backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print PDF
                </button>
                <form action="{{ route('guru.laporan.generate-word') }}" method="POST">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswa->id_siswa }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold bg-white text-[#84934A] hover:bg-[#F1F4E9] transition-all shadow-lg shadow-black/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Cetak Word
                    </button>
                </form>
            </div>
        </div>

        {{-- ── STUDENT SWITCHER ── --}}
        @if(count($anak) > 1)
            <div class="flex flex-wrap items-center gap-3 no-print">
                @foreach($anak as $a)
                    <a href="{{ route('guru.laporan', ['siswa_id' => $a->id_siswa]) }}" 
                       class="group flex items-center gap-3 px-4 py-2.5 rounded-2xl transition-all border {{ $siswa->id_siswa == $a->id_siswa ? 'bg-white border-[#84934A] shadow-md ring-4 ring-[#84934A]/10' : 'bg-white border-gray-100 opacity-60 hover:opacity-100 hover:border-gray-200 shadow-sm' }}">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black transition-transform group-hover:scale-110 {{ $siswa->id_siswa == $a->id_siswa ? 'bg-[#84934A] text-white shadow-lg shadow-[#84934A]/20' : 'bg-gray-100 text-gray-400' }} overflow-hidden">
                            @if($a->foto)
                                <img src="{{ asset('storage/' . $a->foto) }}" class="w-full h-full object-cover" alt="{{ $a->name }}">
                            @else
                                {{ strtoupper(substr($a->name, 0, 1)) }}
                            @endif
                        </div>
                        <span class="text-[11px] font-bold {{ $siswa->id_siswa == $a->id_siswa ? 'text-gray-900' : 'text-gray-500' }}">{{ $a->name }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── REPORT PREVIEW (Paper Style) ── --}}
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100 print:shadow-none print:border-none print:rounded-none">
            
            {{-- REPORT HEADER (KOP) --}}
            <div class="relative p-10 md:p-14 overflow-hidden bg-white border-b-8 border-double border-gray-100">
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                    <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                        <div class="w-24 h-24 rounded-[2rem] bg-gray-900 p-0.5 shadow-xl shadow-gray-900/10">
                            <div class="w-full h-full rounded-[1.9rem] bg-white flex items-center justify-center text-3xl font-black text-gray-900">
                                TK
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight uppercase leading-none">TK NEGERI PEMBINA KOTA PADANG PANJANG</h2>
                            <p class="text-[11px] font-bold text-gray-400 mt-2 uppercase tracking-wider">Sistem Penilaian Perkembangan & SPK Fuzzy SMART</p>
                            <div class="flex items-center justify-center md:justify-start gap-4 mt-4 text-[10px] font-bold text-gray-400 italic">
                                <span>Jl. Rasuna Said RT VIII, Kelurahan Kampung Manggis, Kecamatan Padang Panjang Barat, Kota Padang Panjang, Sumatera Barat</span>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block text-right">
                        <div class="inline-block p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">ID Dokumen</p>
                            <p class="text-xs font-mono font-bold text-gray-700">RPT-{{ date('Ymd') }}-{{ $siswa->id_siswa }}</p>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- STUDENT PROFILE BANNER --}}
            <div class="px-10 md:px-14 py-10 bg-gray-50/50 border-b border-gray-100">
                <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-white border-4 border-white shadow-xl overflow-hidden flex-shrink-0 ring-1 ring-gray-100">
                            @if($siswa->foto)
                                <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover" alt="{{ $siswa->name }}">
                            @else
                                <div class="w-full h-full bg-[#84934A]/10 flex items-center justify-center text-3xl font-black text-[#84934A]">
                                    {{ strtoupper(substr($siswa->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 tracking-tight">{{ $siswa->name }}</h3>
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <span class="badge badge-blue text-[9px] px-3 font-bold">{{ $siswa->kelas->nama_kelas ?? '—' }}</span>
                                <span class="text-[10px] font-bold text-gray-400">ID: {{ $siswa->id_siswa }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                <span class="text-[9px] font-bold text-gray-900 uppercase">Laporan Perkembangan</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-stretch gap-3 w-full md:w-auto">
                        <div class="flex-1 md:w-32 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Skor Akhir (V)</p>
                            <p class="text-2xl font-black text-blue-600 leading-none">
                                {{ number_format($evaluasi->nilai_akhir, 3) }}
                            </p>
                        </div>
                        <div class="flex-1 md:w-32 p-4 rounded-2xl shadow-sm border text-center
                             {{ $evaluasi->kategori_akhir === 'BSB' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : ($evaluasi->kategori_akhir === 'BSH' ? 'border-amber-100 bg-amber-50 text-amber-700' : 'border-rose-100 bg-rose-50 text-rose-700') }}">
                            <p class="text-[8px] font-bold opacity-60 uppercase tracking-wider mb-1">Kategori Akhir</p>
                            <p class="text-xl font-bold leading-none">{{ $evaluasi->kategori_akhir }}</p>
                        </div>
                        <div class="flex-1 md:w-32 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 text-center">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Ranking</p>
                            <p class="text-2xl font-black text-gray-900 leading-none">#{{ $ranking }}</p>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- MAIN REPORT CONTENT --}}
            <div class="p-10 md:p-14 space-y-12">
                
                {{-- REKOMENDASI UTAMA --}}
                <div class="p-8 bg-gray-50 rounded-[2rem] border-2 border-gray-100 relative overflow-hidden">
                    <div class="relative z-10">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 mb-4">Ringkasan Rekomendasi Pengembangan</h4>
                        <p class="text-lg font-medium leading-relaxed italic text-gray-900 text-justify" style="white-space: pre-wrap;">"{{ $evaluasi->rekomendasi ?? 'Belum ada rekomendasi final untuk periode ini.' }}"</p>
                    </div>
                </div>
    
                {{-- I. CAPAIAN KRITERIA --}}
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-lg text-white text-sm font-semibold flex items-center justify-center flex-shrink-0" style="background: #84934A;">I</span>
                        <h4 class="text-base font-semibold text-gray-900">Capaian Per Aspek Perkembangan</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($details as $kriteriaName => $items)
                            @php 
                                $avg = $items->avg('nilai_crisp');
                                $katObj = \App\Models\KategoriNilai::findByNilai($avg ?? 0);
                                $kat = $katObj ? $katObj->nama : 'MB';
                                $kColor = $kat === 'BSB' ? 'emerald' : ($kat === 'BSH' ? 'amber' : 'rose'); 
                            @endphp
                            <div class="p-6 rounded-3xl bg-gray-50 border border-gray-100 hover:shadow-lg transition-all group">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="badge badge-blue px-2 font-mono text-[9px]">{{ strtoupper(substr($kriteriaName, 0, 3)) }}</span>
                                    <span class="badge {{ 'badge-'.$kColor }} px-3 py-1 text-[9px]">{{ $kat }}</span>
                                </div>
                                <h5 class="text-sm font-black text-gray-800 leading-tight mb-4 min-h-[40px]">{{ $kriteriaName }}</h5>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-[10px] font-black">
                                        <span class="text-gray-400 uppercase tracking-widest">Skor Indeks</span>
                                        <span class="text-gray-900">{{ number_format($avg, 1) }}%</span>
                                    </div>
                                    <div class="progress-track bg-gray-200 h-2">
                                        <div class="progress-fill h-2 {{ $kColor === 'emerald' ? 'bg-emerald-500' : ($kColor === 'amber' ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $avg }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
    
                {{-- II. DETAIL INDIKATOR --}}
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-lg text-white text-sm font-semibold flex items-center justify-center flex-shrink-0" style="background: #84934A;">II</span>
                        <h4 class="text-base font-semibold text-gray-900">Daftar Capaian Indikator</h4>
                    </div>
                    <div class="card overflow-hidden border-gray-100">
                        <table class="tbl">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="w-20 text-center">Kode</th>
                                    <th>Indikator Capaian</th>
                                    <th class="text-center w-28">Kategori</th>
                                    <th class="text-right w-28">Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['subkriteria'] as $sub)
                                    @php
                                        $rubrikKey = 'rubrik_' . strtolower($sub['nilai']);
                                        // Cari model Subkriteria untuk ambil rubrik (agak boros tapi paling gampang di view)
                                        $subModel = \App\Models\Subkriteria::find($sub['id']);
                                        $rubrikText = $subModel ? $subModel->$rubrikKey : null;
                                        if($rubrikText) {
                                            $rubrikText = str_replace('{{nama_siswa}}', $siswa->name, $rubrikText);
                                        }
                                    @endphp
                                    <tr class="hover:bg-blue-50/20 transition-colors">
                                        <td class="text-center"><span class="badge badge-blue font-mono text-[9px]">{{ $sub['id'] }}</span></td>
                                        <td class="py-5">
                                            <p class="text-sm font-bold text-gray-800 leading-snug">{{ $sub['nama'] }}</p>
                                            @if($rubrikText)
                                                <p class="text-[11px] text-gray-500 italic mt-1.5 leading-relaxed">&ldquo;{{ $rubrikText }}&rdquo;</p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php $sColor = match($sub['nilai']) { 'BSB'=>'badge-bsb', 'BSH'=>'badge-bsh', 'MB'=>'badge-mb', default=>'badge-gray' }; @endphp
                                            <span class="badge {{ $sColor }} text-[9px] font-black">{{ $sub['nilai'] ?: '—' }}</span>
                                        </td>
                                        <td class="text-right font-mono text-xs font-bold text-gray-600">
                                            {{ number_format($sub['avg'], 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
    
                {{-- III. REKOMENDASI STRATEGIS --}}
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-lg text-white text-sm font-semibold flex items-center justify-center flex-shrink-0" style="background: #84934A;">III</span>
                        <h4 class="text-base font-semibold text-gray-900">Rekomendasi Strategis per Aspek</h4>
                    </div>
                    <div class="space-y-6">
                        @php $hasRecommendation = false; @endphp
                        @foreach($details as $kriteriaName => $items)
                            @php
                                $rekList = $items->filter(fn($item) => $item->rekomendasi_detail && !str_contains(strtolower($item->rekomendasi_detail), 'sangat baik'));
                            @endphp
                            @if($rekList->isNotEmpty())
                                @php $hasRecommendation = true; @endphp
                                <div class="p-8 rounded-[2rem] bg-gray-50 border border-gray-100">
                                    <h5 class="text-xs font-black text-[#84934A] uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full bg-[#84934A]"></span>
                                        {{ $kriteriaName }}
                                    </h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($rekList as $item)
                                            <div class="p-5 rounded-2xl bg-white border border-gray-100 shadow-sm">
                                                <div class="flex items-start gap-3 mb-2">
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $item->subkriteria->id_subkriteria }}</span>
                                                    <p class="text-[10px] font-black text-gray-700 uppercase tracking-tight">{{ $item->subkriteria->nama_subkriteria }}</p>
                                                </div>
                                                <p class="text-xs font-bold text-gray-600 leading-relaxed italic">&ldquo;{{ $item->rekomendasi_detail }}&rdquo;</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(!$hasRecommendation)
                            <div class="p-10 text-center rounded-[2rem] bg-gray-50 border border-dashed border-gray-200">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Belum ada rekomendasi strategis spesifik untuk periode ini.</p>
                            </div>
                        @endif
                    </div>
                </section>
    
                {{-- IV. PORTOFOLIO --}}
                @if($portofolio_list->count() > 0)
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-lg text-white text-sm font-semibold flex items-center justify-center flex-shrink-0" style="background: #84934A;">IV</span>
                        <h4 class="text-base font-semibold text-gray-900">Portofolio & Dokumentasi</h4>
                    </div>
                    <div class="card overflow-hidden border-gray-100">
                        <table class="tbl">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="w-24 text-center">Minggu</th>
                                    <th>Judul & Deskripsi</th>
                                    <th class="w-48 text-center">Dokumentasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($portofolio_list as $port)
                                <tr class="hover:bg-violet-50/10 transition-colors">
                                    <td class="text-center font-black text-[10px] text-gray-400">Minggu {{ $port->minggu->minggu_ke ?? '—' }}</td>
                                    <td class="py-5">
                                        <p class="text-sm font-black text-gray-800 mb-1">{{ $port->judul }}</p>
                                        <p class="text-[11px] text-gray-500 italic leading-relaxed" style="white-space: pre-wrap;">&ldquo;{{ $port->deskripsi }}&rdquo;</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="grid grid-cols-2 gap-2">
                                            @forelse($port->images as $img)
                                                @php $isVideo = in_array(pathinfo($img->file_path, PATHINFO_EXTENSION), ['mp4', 'mov', 'webm']); @endphp
                                                <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-50 cursor-pointer group/img"
                                                     @click="$dispatch('open-lightbox', '{{ Storage::url($img->file_path) }}')">
                                                     @if($isVideo)
                                                         <video src="{{ Storage::url($img->file_path) }}" class="w-full h-full object-cover"></video>
                                                     @else
                                                         <img src="{{ Storage::url($img->file_path) }}" class="w-full h-full object-cover transition-transform group-hover/img:scale-110">
                                                     @endif
                                                     <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/20 transition-all flex items-center justify-center">
                                                         <div class="w-8 h-8 rounded-full bg-white shadow-lg flex items-center justify-center scale-0 group-hover/img:scale-100 transition-transform">
                                                             <svg class="w-4 h-4 text-[#84934A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                         </div>
                                                     </div>
                                                 </div>
                                            @empty
                                                <div class="col-span-2 py-2 text-center">
                                                    <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest italic">Tanpa Foto</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif
    
                {{-- V. CATATAN GURU UMUM --}}
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-8 rounded-lg text-white text-sm font-semibold flex items-center justify-center flex-shrink-0" style="background: #84934A;">V</span>
                        <h4 class="text-base font-semibold text-gray-900">Kesimpulan Guru</h4>
                    </div>
                    <div class="p-8 rounded-[2rem] bg-amber-50 border-2 border-dashed border-amber-200">
                        <div class="flex gap-6">
                            <div class="stat-icon bg-white text-amber-600 shadow-sm flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                                <p class="text-base font-bold text-amber-900/80 leading-relaxed italic" style="white-space: pre-wrap;">&ldquo;{{ $evaluasi->catatan_guru ?? 'Belum ada catatan kesimpulan guru.' }}&rdquo;</p>
                        </div>
                    </div>
                </section>
    
                {{-- SIGNATURE AREA --}}
                <div class="pt-16 mt-16 border-t-2 border-gray-100">
                    <div class="grid grid-cols-3 gap-12 text-center">
                        @foreach(['Orang Tua / Wali Siswa', 'Wali Kelas (Pengajar)', 'Kepala Sekolah TK Pembina'] as $sign)
                            <div class="space-y-16">
                                <p class="text-xs font-black text-gray-800 uppercase tracking-widest">{{ $sign }}</p>
                                <div class="space-y-2">
                                    <div class="h-px bg-gray-300 w-4/5 mx-auto"></div>
                                    <p class="text-[10px] text-gray-400 font-bold italic">Tanda Tangan & Nama Terang</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    
            {{-- REPORT FOOTER --}}
            <div class="px-10 py-6 flex flex-col md:flex-row items-center justify-between gap-3" style="background: #1B211A;">
                <p class="text-xs" style="color: rgba(255,255,255,.45);">Dicetak otomatis oleh Sistem SPK Fuzzy SMART &bull; {{ date('d M Y H:i') }}</p>
                <p class="text-xs" style="color: rgba(255,255,255,.45);">&copy; {{ date('Y') }} TK Pembina</p>
            </div>
        </div>

    @else
        {{-- ── FILTER CARD ── --}}
        <div class="card p-6 no-print shadow-sm border-gray-100">
            <form action="{{ route('guru.laporan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Pilih siswa untuk melihat pratinjau laporan</label>
                    <div class="relative">
                        <select name="siswa_id" class="w-full py-2.5 pl-11 pr-4 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 shadow-sm hover:border-blue-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none outline-none">
                            <option value="">-- Cari nama siswa --</option>
                            @foreach($allSiswa as $s)
                                <option value="{{ $s->id_siswa }}" {{ $selectedSiswaId == $s->id_siswa ? 'selected' : '' }}>
                                    {{ $s->name }} (Kelas: {{ $s->kelas->nama_kelas ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-green px-5 py-2.5 rounded-lg font-bold text-xs shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Tampilkan
                </button>
            </form>
        </div>

        {{-- ── STUDENT LIST TABLE ── --}}
        <div class="card overflow-hidden">
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-bottom: 1px solid var(--border);">
                <div>
                    <h3 class="text-base font-semibold" style="color: var(--text-1);">Daftar Laporan Siswa</h3>
                    <p class="text-xs mt-0.5" style="color: var(--text-3);">Klik tombol lihat untuk mempratinjau laporan perkembangan siswa.</p>
                </div>
                <span class="badge badge-blue">{{ $allSiswa->count() }} siswa</span>
            </div>

            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="py-4 px-6 text-left" style="width: 80px;">No</th>
                            <th class="py-4 px-6 text-left" style="width: 120px;">Foto</th>
                            <th class="py-4 px-6 text-left">Nama Lengkap</th>
                            <th class="py-4 px-6 text-left" style="width: 180px;">NISN</th>
                            <th class="py-4 px-6 text-left" style="width: 150px;">Kelas</th>
                            <th class="py-4 px-6 text-left" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allSiswa as $index => $siswa)
                            <tr class="hover:bg-blue-50/10 transition-colors border-b border-gray-50 last:border-0">
                                <td class="py-4 px-6 text-left font-mono text-xs text-gray-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6 text-left">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden flex items-center justify-center">
                                        @if($siswa->foto)
                                            <img src="{{ asset('storage/'.$siswa->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-black text-blue-300">{{ strtoupper(substr($siswa->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-left">
                                    <p class="text-sm font-bold text-gray-800 tracking-tight">{{ $siswa->name }}</p>
                                </td>
                                <td class="py-4 px-6 text-left">
                                    <span class="font-mono text-xs text-gray-500">{{ $siswa->id_siswa }}</span>
                                </td>
                                <td class="py-4 px-6 text-left">
                                    <span class="badge badge-blue text-[9px] px-3">{{ $siswa->kelas->nama_kelas ?? '—' }}</span>
                                </td>
                                <td class="py-4 px-6 text-left">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('guru.laporan', ['siswa_id' => $siswa->id_siswa]) }}" 
                                           class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Detail Laporan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <form action="{{ route('guru.laporan.generate-word') }}" method="POST" class="m-0 p-0 inline">
                                            @csrf
                                            <input type="hidden" name="siswa_id" value="{{ $siswa->id_siswa }}">
                                            <button type="submit" class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Download Word">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    @media print {
        .no-print, aside, nav, header, [x-cloak] { display: none !important; }
        html, body { 
            height: auto !important; 
            overflow: visible !important; 
            background: white !important; 
            margin: 0 !important; 
            padding: 0 !important; 
            -webkit-print-color-adjust: exact;
        }
        
        /* Specific layout overrides */
        div.flex.h-screen { display: block !important; height: auto !important; overflow: visible !important; }
        div.flex.flex-col.flex-1 { display: block !important; height: auto !important; overflow: visible !important; }
        
        main { 
            display: block !important; 
            position: static !important; 
            overflow: visible !important; 
            width: 100% !important; 
            margin: 0 !important; 
            padding: 0 !important;
        }
        
        .max-w-7xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; page-break-inside: avoid; }
        .fade-in { animation: none !important; transform: none !important; opacity: 1 !important; }
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
