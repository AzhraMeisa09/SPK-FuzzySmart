@extends('layouts.app')
@section('title', 'Detail Evaluasi SPK — ' . $siswa->nama)
@section('page-title', 'Detail hasil evaluasi')

@section('content')
<div class="space-y-5 pb-10 fade-in">

    {{-- ── TOP NAVIGATION ── --}}
    <div class="flex items-center justify-between no-print mb-2">
        <a href="{{ route('guru.hasil-evaluasi') }}" class="group flex items-center gap-2" style="color: var(--text-2);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all group-hover:bg-gray-100" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <span class="text-xs font-semibold uppercase tracking-widest group-hover:text-gray-900">Kembali ke daftar</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('guru.hasil-evaluasi.cetak', $siswa->id) }}" target="_blank" class="btn btn-green btn-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak laporan
            </a>
        </div>
    </div>

    @if(!$evaluasi)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Hasil belum tersedia</h3>
            <p class="text-xs mt-2 max-w-sm mx-auto" style="color: var(--text-3);">
                Hasil evaluasi SPK untuk siswa ini belum tersedia atau periode penilaian belum difinalisasi oleh Admin.
            </p>
            <div class="mt-8">
                <a href="{{ route('guru.hasil-evaluasi') }}" class="btn btn-gray py-2.5 px-8 text-xs font-bold">Kembali</a>
            </div>
        </div>
    @else
        {{-- ── ANALYTIC HERO SECTION ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            {{-- Profile Card --}}
            <div class="lg:col-span-8">
                <div class="card h-full flex flex-col overflow-hidden">
                    <div class="p-5 flex flex-col md:flex-row items-center gap-5" style="border-bottom: 1px solid var(--border); background: var(--bg);">
                        <div class="w-20 h-20 rounded-xl flex items-center justify-center text-2xl font-black shadow-inner" style="background: var(--accent-lt); color: var(--accent);">
                            {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-1.5">
                                <span class="badge badge-final text-[9px]">Final Report</span>
                                @if($periode)
                                    <span class="badge badge-blue text-[9px]">{{ $periode->nama_periode }}</span>
                                @endif
                            </div>
                            <h1 class="text-lg font-bold tracking-tight leading-tight" style="color: var(--text-1);">{{ $siswa->nama }}</h1>
                            <p class="text-[10px] font-bold uppercase tracking-widest mt-1" style="color: var(--text-3);">Kelas: {{ $siswa->kelas->nama_kelas ?? '—' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2">
                        <div class="p-5" style="border-right: 1px solid var(--border);">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color: var(--text-3);">Rekomendasi utama (SPK)</h4>
                                    <p class="text-sm font-semibold leading-relaxed italic" style="color: var(--text-1);">&ldquo;{{ $evaluasi->rekomendasi ?? '—' }}&rdquo;</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-5">
                            <form action="{{ route('guru.hasil-eval-catatan.update', $evaluasi->id) }}" method="POST" class="no-print space-y-3">
                                @csrf
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Catatan guru (umum)</h4>
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-widest hover:underline" style="color: var(--accent);">Simpan catatan</button>
                                </div>
                                <textarea name="catatan_guru" rows="3" class="form-input text-xs italic resize-y min-h-[60px]" placeholder="Tambahkan perspektif guru di sini...">{{ $evaluasi->catatan_guru }}</textarea>
                            </form>
                            <div class="hidden print:block space-y-2">
                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Catatan guru (umum)</h4>
                                <p class="text-xs font-semibold leading-relaxed italic text-gray-800">&ldquo;{{ $evaluasi->catatan_guru ?: 'Tidak ada catatan tambahan.' }}&rdquo;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Score Matrix --}}
            <div class="lg:col-span-4">
                <div class="card h-full p-6 flex flex-col justify-center">
                    <div class="text-center mb-6">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color: var(--text-3);">Indeks capaian akhir (V)</p>
                        <div class="inline-flex items-center justify-center p-4 rounded-xl shadow-inner" style="background: var(--bg); border: 1px solid var(--border);">
                            <span class="text-3xl font-bold tracking-tight" style="color: var(--text-1);">{{ number_format($evaluasi->nilai_akhir, 3) }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--bg); border: 1px solid var(--border);">
                            <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Kategori akhir</span>
                            <span class="badge {{ $evaluasi->kategori_akhir === 'BSB' ? 'badge-bsb' : ($evaluasi->kategori_akhir === 'BSH' ? 'badge-bsh' : 'badge-mb') }} px-3 py-1 text-[10px]">
                                {{ $evaluasi->kategori_akhir }}
                            </span>
                        </div>
                        <div class="space-y-1.5 px-1">
                            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">
                                <span>Percentile ranking</span>
                                <span style="color: var(--text-1);">{{ number_format($evaluasi->nilai_akhir * 100, 1) }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill {{ $evaluasi->kategori_akhir === 'BSB' ? 'progress-green' : ($evaluasi->kategori_akhir === 'BSH' ? 'progress-yellow' : 'progress-red') }}" 
                                     style="width: {{ $evaluasi->nilai_akhir * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- ── CONTENT GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <div class="lg:col-span-8 space-y-5">
            
            {{-- 🔹 3. RINCIAN KRITERIA --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs" style="background: var(--accent-lt); color: var(--accent);">III</div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-1);">Analisis capaian per aspek</h4>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($details as $kriteriaName => $items)
                        @php
                            $first = $items->first();
                            $cout = $items->avg('nilai_crisp');
                            $ui = $items->avg('nilai_normalisasi');
                            $wi = $items->first()->bobot_snapshot;
                            $kontribusi = $ui * $wi;
                            $kBadge = $cout >= 85 ? 'badge-bsb' : ($cout >= 70 ? 'badge-bsh' : 'badge-mb');
                        @endphp
                        <div class="p-4 rounded-xl flex flex-col" style="background: var(--bg); border: 1px solid var(--border);">
                            <div class="flex items-center justify-between mb-3">
                                <span class="badge badge-blue font-mono text-[9px]">{{ $first->subkriteria->kriteria->kode }}</span>
                                <span class="badge {{ $kBadge }} font-bold text-[9px]">{{ number_format($cout, 1) }}%</span>
                            </div>
                            <h5 class="text-[10px] font-bold uppercase tracking-wider mb-3 flex-1" style="color: var(--text-1);">{{ $kriteriaName }}</h5>
                            <div class="space-y-1.5 pt-3 mt-auto" style="border-top: 1px solid var(--border);">
                                <div class="flex justify-between text-[9px] font-bold">
                                    <span class="uppercase" style="color: var(--text-3);">Utilitas (ui)</span>
                                    <span style="color: var(--text-2);">{{ number_format($ui, 4) }}</span>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold">
                                    <span class="uppercase" style="color: var(--text-3);">Bobot (wi)</span>
                                    <span style="color: var(--text-2);">{{ number_format($wi, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-[9px] font-bold pt-1.5 mt-1.5" style="border-top: 1px dashed var(--border); color: var(--accent);">
                                    <span class="uppercase">Kontribusi</span>
                                    <span>{{ number_format($kontribusi, 4) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 🔹 4. RINCIAN SUBKRITERIA --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs" style="background: var(--accent-lt); color: var(--accent);">IV</div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-1);">Rincian capaian indikator (Subkriteria)</h4>
                </div>
                
                <div class="overflow-hidden rounded-xl" style="border: 1px solid var(--border);">
                    <table class="tbl w-full text-left">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">ID</th>
                                <th>Indikator perkembangan</th>
                                <th class="text-center w-16">Cout</th>
                                <th class="text-center w-24">Capaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluasi->detail as $d)
                                @php
                                    $rubrikKey = 'rubrik_' . strtolower($d->kategori);
                                    $rubrikText = $d->subkriteria->$rubrikKey ?? null;
                                    if($rubrikText) {
                                        $rubrikText = str_replace('{{nama_siswa}}', $siswa->nama, $rubrikText);
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center font-mono text-[9px]" style="color: var(--text-3);">{{ $d->subkriteria->kode }}</td>
                                    <td class="py-3">
                                        <p class="text-xs font-semibold leading-snug" style="color: var(--text-1);">{{ $d->subkriteria->nama }}</p>
                                        @if($rubrikText)
                                            <p class="text-[10px] text-gray-500 italic mb-1.5 mt-0.5">&ldquo;{{ $rubrikText }}&rdquo;</p>
                                        @endif
                                        <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5" style="color: var(--text-3);">{{ $d->subkriteria->kriteria->nama }}</p>
                                    </td>
                                    <td class="text-center font-mono text-xs font-bold" style="color: var(--text-2);">{{ number_format($d->nilai_crisp, 1) }}</td>
                                    <td class="text-center">
                                        @php $sBadge = $d->kategori === 'BSB' ? 'badge-bsb' : ($d->kategori === 'BSH' ? 'badge-bsh' : 'badge-mb'); @endphp
                                        <span class="badge {{ $sBadge }} text-[9px]">{{ $d->kategori }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔹 5. REKOMENDASI PER SUBKRITERIA --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Rekomendasi strategis per aspek</h3>
                        <p class="text-[10px] uppercase tracking-widest font-bold" style="color: var(--text-3);">Deep analysis by fuzzy smart system</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach($details as $kriteriaName => $items)
                        @php
                            $rekList = $items->filter(fn($item) => $item->rekomendasi_detail && !str_contains($item->rekomendasi_detail, 'sangat baik'));
                        @endphp
                        @if($rekList->isNotEmpty())
                            <div class="relative pl-5">
                                <div class="absolute left-0 top-0 bottom-0 w-0.5 rounded-full" style="background: var(--border);"></div>
                                <h5 class="text-[10px] font-bold uppercase tracking-widest mb-3 flex items-center gap-2" style="color: var(--accent);">
                                    <span class="w-1.5 h-1.5 rounded-full absolute -left-[2px]" style="background: var(--accent);"></span>
                                    {{ $kriteriaName }}
                                </h5>
                                <div class="space-y-2">
                                    @foreach($rekList as $item)
                                        <div class="p-3.5 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                                            <p class="text-xs font-medium leading-relaxed italic" style="color: var(--text-2);">&ldquo;{{ $item->rekomendasi_detail }}&rdquo;</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- 🔹 6. 📷 PORTOFOLIO --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Portofolio & dokumentasi</h3>
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Evidence-based progress records</p>
                    </div>
                </div>

                @forelse($portofolio_list->groupBy('minggu_id') as $mingguId => $items)
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="badge badge-blue text-[9px]">Minggu {{ $items->first()->minggu->minggu_ke }}</span>
                            <div class="h-px flex-1" style="background: var(--border);"></div>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($items as $p)
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 rounded-xl" style="border: 1px solid var(--border); background: var(--bg);">
                                    <div class="md:col-span-1 grid grid-cols-2 gap-1">
                                        @foreach($p->images as $img)
                                            <div class="aspect-square rounded-lg overflow-hidden border border-gray-100">
                                                <img src="{{ asset('storage/'.$img->file_path) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="md:col-span-3 space-y-1.5">
                                        <h4 class="text-xs font-semibold" style="color: var(--text-1);">{{ $p->judul }}</h4>
                                        <p class="text-[11px] leading-relaxed text-gray-600 font-medium" style="white-space: pre-wrap;">{{ $p->deskripsi }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center rounded-xl" style="background: var(--bg); border: 1px dashed var(--border);">
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Tidak ada portofolio pada periode ini</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-5">
            {{-- Category Breakdown --}}
            <div class="card p-5">
                <h3 class="text-[10px] font-bold uppercase tracking-widest mb-5 text-center" style="color: var(--text-3);">Distribusi pencapaian</h3>
                
                @php
                    $counts = ['BSB' => 0, 'BSH' => 0, 'MB' => 0];
                    foreach($details as $items) {
                        foreach($items as $item) {
                            if(isset($counts[$item->kategori])) $counts[$item->kategori]++;
                        }
                    }
                    $totalCount = array_sum($counts);
                @endphp

                <div class="space-y-4">
                    @foreach(['BSB' => 'Sangat Baik', 'BSH' => 'Sesuai Harapan', 'MB' => 'Mulai Berkembang'] as $kat => $label)
                        @php 
                            $val = $counts[$kat];
                            $pct = $totalCount > 0 ? ($val/$totalCount)*100 : 0;
                            $colorClass = $kat === 'BSB' ? 'progress-green' : ($kat === 'BSH' ? 'progress-yellow' : 'progress-red');
                            $textColor = $kat === 'BSB' ? 'var(--green)' : ($kat === 'BSH' ? 'var(--yellow)' : 'var(--red)');
                        @endphp
                        <div class="space-y-2">
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color: {{ $textColor }};">{{ $kat }}</p>
                                    <p class="text-[9px] font-medium" style="color: var(--text-3);">{{ $label }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold leading-none" style="color: var(--text-1);">{{ $val }}</p>
                                    <p class="text-[8px] font-bold uppercase tracking-widest mt-1" style="color: var(--text-3);">Indikator</p>
                                </div>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill {{ $colorClass }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card p-5" style="border: 1px solid var(--accent); background: var(--accent-lt);">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--accent);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h4 class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--accent);">Keamanan data</h4>
                </div>
                <p class="text-[10px] leading-relaxed font-medium mt-2" style="color: var(--text-2);">
                    Hasil evaluasi ini telah difinalisasi secara digital dan tidak dapat diubah tanpa otoritas Administrator.
                </p>
            </div>
        </div>
    </div>

    @endif
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    }
</style>
@endsection
