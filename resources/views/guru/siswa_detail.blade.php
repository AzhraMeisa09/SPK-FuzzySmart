@extends('layouts.app')
@section('title', 'Detail Siswa: ' . $siswa->name)
@section('page-title', 'Profil Siswa')

@section('content')
<div class="space-y-5 fade-in">

    {{-- ── HEADER CARD ── --}}
    <div class="card p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="w-14 h-14 rounded-xl flex items-center justify-center font-black text-2xl flex-shrink-0 overflow-hidden" style="background: var(--accent-lt); color: var(--accent);">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($siswa->name, 0, 1)) }}
                    @endif
                </div>
                {{-- Name & Meta --}}
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="badge badge-blue text-[9px] uppercase tracking-widest">{{ $siswa->kelas->nama_kelas ?? 'Belum ditempatkan' }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">NISN: {{ $siswa->kode ?: $siswa->id_siswa ?: '—' }}</span>
                    </div>
                    <h1 class="text-lg font-semibold leading-tight" style="color: var(--text-1);">{{ $siswa->name }}</h1>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guru.siswa.index') }}" class="btn btn-gray btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <a href="{{ route('guru.riwayat.detail', $siswa->id_siswa) }}" class="btn btn-green btn-sm">
                    Riwayat nilai
                </a>
                <a href="{{ route('guru.portofolio.index', ['siswa_id' => $siswa->id_siswa]) }}" class="btn btn-purple btn-sm">
                    Portofolio
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- ── MAIN COLUMN ── --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- 1. BIODATA --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Biodata & keluarga</h3>
                        <p class="text-[10px]" style="color: var(--text-3);">Informasi dasar siswa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Tempat, tanggal lahir</p>
                        <p class="text-sm font-medium" style="color: var(--text-1);">{{ $siswa->tempat_lahir ?? '-' }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Orang tua / wali</p>
                        <p class="text-sm font-medium" style="color: var(--text-1);">{{ $siswa->wali->first()->nama_lengkap ?? $siswa->nama_orang_tua ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2 p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Alamat</p>
                        <p class="text-sm font-medium italic" style="color: var(--text-2);">{{ $siswa->wali->count() > 0 ? ($siswa->wali->first()->alamat ?? $siswa->alamat ?? 'Belum diisi.') : ($siswa->alamat ?? 'Belum diisi.') }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. HASIL SPK (JIKA FINAL) --}}
            @if($evaluasi && $evaluasi->is_final)
            <div class="card p-5">
                <div class="flex items-center justify-between mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold" style="color: var(--text-1);">Hasil evaluasi akhir (SPK)</h3>
                            <p class="text-[10px]" style="color: var(--text-3);">Perhitungan Fuzzy SMART</p>
                        </div>
                    </div>
                    <span class="badge badge-final">Finalisasi</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl flex items-center justify-between" style="background: var(--bg); border: 1px solid var(--border);">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Nilai akhir (Va)</p>
                            <p class="text-2xl font-bold" style="color: var(--text-1);">{{ number_format($evaluasi->nilai_akhir, 3) }}</p>
                        </div>
                        <span class="badge {{ $evaluasi->kategori_akhir == 'BSB' ? 'badge-bsb' : ($evaluasi->kategori_akhir == 'BSH' ? 'badge-bsh' : 'badge-mb') }} px-4 py-1.5 text-xs">
                            {{ $evaluasi->kategori_akhir }}
                        </span>
                    </div>
                    <div class="p-4 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-2" style="color: var(--text-3);">Komentar guru</p>
                        <p class="text-xs leading-relaxed italic" style="color: var(--text-2);">
                            "{{ $evaluasi->catatan_guru ?: 'Belum ada catatan tambahan dari guru.' }}"
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- 3. PORTOFOLIO --}}
            <div class="card p-5">
                <div class="flex items-center justify-between mb-5 pb-4" style="border-bottom: 1px solid var(--border);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold" style="color: var(--text-1);">Portofolio perkembangan</h3>
                            <p class="text-[10px]" style="color: var(--text-3);">Dokumentasi karya & kegiatan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">{{ $portofolios->count() }} entri</span>
                </div>

                @if($portofolios->count() > 0)
                    <div class="space-y-8">
                        @foreach($portofolios->groupBy(fn($p) => $p->minggu->minggu_ke) as $mingguKe => $items)
                            <div class="relative pl-7">
                                <div class="absolute left-0 top-0 bottom-0 w-0.5 rounded-full" style="background: var(--border);"></div>
                                <div class="absolute left-[-4px] top-1 w-2.5 h-2.5 rounded-full" style="background: var(--accent);"></div>
                                <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4" style="color: var(--accent);">Minggu ke-{{ $mingguKe }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($items as $porto)
                                        <div class="rounded-xl overflow-hidden" style="border: 1px solid var(--border); background: var(--bg);">
                                            @if($porto->images->count() > 0)
                                                <div class="aspect-video relative overflow-hidden">
                                                    <img src="{{ asset('storage/' . $porto->images->first()->file_path) }}" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <div class="p-4">
                                                <h5 class="text-xs font-semibold mb-1" style="color: var(--text-1);">{{ $porto->judul }}</h5>
                                                <p class="text-[10px] italic leading-relaxed line-clamp-2" style="color: var(--text-3);">{{ $porto->deskripsi }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-16 text-center rounded-xl" style="background: var(--bg); border: 1px dashed var(--border);">
                        <svg class="w-8 h-8 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-xs font-medium italic" style="color: var(--text-3);">Belum ada portofolio terinput</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── SIDEBAR COLUMN ── --}}
        <div class="lg:col-span-4 space-y-5">

            {{-- Ringkasan Aktivitas --}}
            <div class="card p-5">
                <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4" style="color: var(--text-3);">Ringkasan aktivitas</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2.5" style="border-bottom: 1px solid var(--border);">
                        <span class="text-xs" style="color: var(--text-2);">Total portofolio</span>
                        <span class="text-xs font-semibold" style="color: var(--text-1);">{{ $portofolios->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5" style="border-bottom: 1px solid var(--border);">
                        <span class="text-xs" style="color: var(--text-2);">Minggu terdata</span>
                        <span class="text-xs font-semibold" style="color: var(--text-1);">{{ $portofolios->unique('minggu_id')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-xs" style="color: var(--text-2);">Periode aktif</span>
                        <span class="badge badge-aktif text-[9px]">{{ $periodeAktif->nama_periode ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Kontak Orang Tua --}}
            <div class="card p-5">
                <h4 class="text-[10px] font-bold uppercase tracking-widest mb-4" style="color: var(--text-3);">Kontak orang tua</h4>
                <div class="space-y-4">
                    <div class="p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Nama wali</p>
                        <p class="text-sm font-medium" style="color: var(--text-1);">{{ $siswa->wali->first()->nama_lengkap ?? $siswa->nama_orang_tua ?? '-' }}</p>
                    </div>
                    <div class="p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">No. WhatsApp</p>
                        <p class="text-sm font-medium" style="color: var(--accent);">{{ $siswa->wali->first()->no_hp ?? $siswa->no_hp_orang_tua ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
