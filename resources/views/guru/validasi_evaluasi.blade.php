@extends('layouts.app')
@section('title', 'Validasi Evaluasi')
@section('page-title', 'Validasi Evaluasi')

@section('content')
<div class="space-y-6 fade-in pb-12">

    {{-- ── HEADER ── --}}
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--accent-lt); color: var(--accent);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold" style="color: var(--text-1);">Validasi Evaluasi Siswa</h2>
                    <p class="text-xs mt-0.5" style="color: var(--text-3);">
                        Tinjau hasil SPK dan berikan keputusan akhir perkembangan anak.
                    </p>
                </div>
            </div>

            {{-- Pilih Periode --}}
            @if($allPeriode->count() > 1)
                <form action="{{ route('guru.validasi.index') }}" method="GET" id="periodeForm" class="relative">
                    <select name="periode_id" class="form-select" style="padding-left:36px;" onchange="document.getElementById('periodeForm').submit()">
                        @foreach($allPeriode as $p)
                            <option value="{{ $p->id_periode }}" {{ $periode && $periode->id_periode == $p->id_periode ? 'selected' : '' }}>
                                {{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}
                                ({{ strtoupper($p->status) }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </form>
            @endif
        </div>

        {{-- Progress Bar --}}
        @if($periode && $progress['total'] > 0)
            <div class="mt-5 pt-5" style="border-top: 1px solid var(--border);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-3);">Progress Validasi</span>
                    <span class="text-xs font-bold" style="color: var(--text-1);">{{ $progress['done'] }} / {{ $progress['total'] }} siswa</span>
                </div>
                <div class="progress-track h-2">
                    <div class="progress-fill h-2 {{ $progress['done'] == $progress['total'] ? 'progress-green' : 'progress-yellow' }}"
                         style="width: {{ $progress['total'] > 0 ? ($progress['done'] / $progress['total']) * 100 : 0 }}%"></div>
                </div>
                @if($progress['pending'] === 0)
                    <p class="text-[10px] font-bold text-green-600 mt-1.5">✅ Semua evaluasi sudah divalidasi. Admin dapat mempublikasikan hasilnya.</p>
                @else
                    <p class="text-[10px] text-amber-600 font-bold mt-1.5">⏳ Masih {{ $progress['pending'] }} evaluasi menunggu validasi Anda.</p>
                @endif
            </div>
        @endif
    </div>

    {{-- ── GLOSARIUM BADGE ── --}}
    <div class="flex flex-wrap gap-3">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-widest bg-amber-50 border border-amber-200 text-amber-700">
            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Menunggu Review
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-widest bg-green-50 border border-green-200 text-green-700">
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Disetujui Guru
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-widest bg-rose-50 border border-rose-200 text-rose-700">
            <span class="w-2 h-2 rounded-full bg-rose-400 inline-block"></span> Kategori Diubah dari Sistem
        </div>
    </div>

    @if(!$periode)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5" style="background: var(--bg); border: 1px solid var(--border);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-sm" style="color: var(--text-2);">Tidak ada periode yang perlu divalidasi</h3>
            <p class="text-xs mt-2" style="color: var(--text-3);">Admin perlu menjalankan proses evaluasi terlebih dahulu.</p>
        </div>
    @elseif($evaluasiList->isEmpty())
        <div class="card p-16 text-center">
            <p class="text-sm font-medium" style="color: var(--text-3);">Tidak ada data evaluasi untuk kelas yang Anda ampu pada periode ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($evaluasiList as $eval)
                @php
                    $isValidated  = $eval->isValidatedByGuru();
                    $isDiubah     = $eval->isKategoriDiubahGuru();
                    $kat          = $eval->kategori_akhir;
                    $colorClass   = match($kat) { 'BSB' => 'bsb', 'BSH' => 'bsh', 'MB' => 'mb', default => 'nonaktif' };
                    $borderColor  = $isValidated ? 'border-green-400' : 'border-amber-400';
                @endphp

                <div class="card p-5 flex flex-col gap-4 relative overflow-hidden" style="border-left: 4px solid {{ $isValidated ? '#4ade80' : '#fbbf24' }};">
                    {{-- Badge Status Validasi --}}
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg font-black" style="background: var(--accent-lt); color: var(--accent);">
                                {{ strtoupper(substr($eval->siswa->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold leading-tight" style="color: var(--text-1);">{{ $eval->siswa->name }}</p>
                                <p class="text-[9px] font-bold uppercase tracking-wider mt-0.5" style="color: var(--text-3);">
                                    {{ $eval->siswa->kelas->nama_kelas ?? '—' }}
                                    &bull; NISN: {{ $eval->siswa->kode ?: $eval->siswa->id_siswa ?: '—' }}
                                </p>
                            </div>
                        </div>
                        @if($isValidated)
                            <span class="px-2 py-1 text-[9px] font-bold uppercase rounded-lg bg-green-100 text-green-700 border border-green-200 shrink-0">✓ Divalidasi</span>
                        @else
                            <span class="px-2 py-1 text-[9px] font-bold uppercase rounded-lg bg-amber-100 text-amber-700 border border-amber-200 shrink-0">Menunggu</span>
                        @endif
                    </div>

                    {{-- Nilai & Kategori --}}
                    <div class="flex items-center justify-between p-3 rounded-xl" style="background: var(--bg); border: 1px solid var(--border);">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Rekomendasi Sistem</p>
                            <span class="badge badge-{{ $colorClass }} text-[9px] font-bold">
                                {{ $eval->kategori_rekomendasi_sistem ?? $kat }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold uppercase tracking-widest mb-1" style="color: var(--text-3);">Nilai SPK</p>
                            <p class="text-lg font-black" style="color: var(--text-1);">{{ number_format($eval->nilai_akhir, 3) }}</p>
                        </div>
                    </div>

                    {{-- Keputusan Guru (jika sudah divalidasi) --}}
                    @if($isValidated)
                        <div class="p-3 rounded-xl bg-green-50 border border-green-200">
                            <p class="text-[9px] font-bold uppercase tracking-widest text-green-700 mb-1.5">Keputusan Guru</p>
                            <div class="flex items-center gap-2">
                                @php $katGuru = $eval->kategori_keputusan_guru; @endphp
                                <span class="badge badge-{{ match($katGuru) { 'BSB' => 'bsb', 'BSH' => 'bsh', 'MB' => 'mb', default => 'nonaktif' } }} text-[9px] font-bold">
                                    {{ $katGuru }}
                                </span>
                                @if($isDiubah)
                                    <span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-lg">Diubah dari sistem</span>
                                @endif
                            </div>
                            @if($eval->guruValidator)
                                <p class="text-[9px] text-green-600 mt-1.5">Oleh: {{ $eval->guruValidator->nama_lengkap }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Actions --}}
                    <a href="{{ route('guru.validasi.review', $eval->id_evaluasi) }}"
                       class="btn {{ $isValidated ? 'btn-gray' : 'btn-green' }} w-full justify-center text-xs py-2.5 mt-auto">
                        {{ $isValidated ? 'Lihat / Ubah Keputusan' : '✎ Validasi Sekarang' }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
