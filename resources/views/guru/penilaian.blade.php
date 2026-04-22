@extends('layouts.app')
@section('title', 'Input Penilaian Siswa')
@section('page-title', 'Input Penilaian')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="mb-5 flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-semibold animate-fade-up">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-5 flex items-center gap-3 px-5 py-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm font-semibold animate-fade-up">
        <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
@endif

<div x-data="{
    showModal: false,
    activeStudentId: null,
    activeMingguId: null,
    studentName: '',
    mingguName: '',
    openModal(studentId, studentName, mingguId, mingguName) {
        this.activeStudentId = studentId;
        this.studentName = studentName;
        this.activeMingguId = mingguId;
        this.mingguName = mingguName;
        document.querySelectorAll('.penilaian-form-container').forEach(el => el.classList.add('hidden'));
        let targetForm = document.getElementById('form-' + studentId + '-' + mingguId);
        if(targetForm) targetForm.classList.remove('hidden');
        this.showModal = true;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.showModal = false;
        document.body.style.overflow = '';
    }
}" class="space-y-5">

    {{-- ── STATUS BAR ──────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Periode --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode Aktif</p>
                <p class="text-sm font-black text-gray-900 truncate mt-0.5">{{ $periode ? $periode->nama_periode : '— Tidak ada —' }}</p>
            </div>
        </div>

        {{-- Minggu Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
            <div class="w-11 h-11 rounded-xl {{ $mingguAktif ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-500' }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minggu Aktif</p>
                <p class="text-sm font-black {{ $mingguAktif ? 'text-emerald-700' : 'text-amber-600' }} truncate mt-0.5">
                    {{ $mingguAktif ? 'Minggu ' . $mingguAktif->minggu_ke . ' — ' . $mingguAktif->tema : 'Belum ditetapkan' }}
                </p>
            </div>
        </div>

        {{-- Kelas --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kelas Anda</p>
                <p class="text-sm font-black text-gray-900 truncate mt-0.5">{{ auth()->user()->kelas->pluck('nama')->implode(', ') ?: '— Belum ditugaskan —' }}</p>
            </div>
        </div>
    </div>

    {{-- ── KONDISI KOSONG ──────────────────────────── --}}
    @if(!$periode)
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-16 text-center">
            <div class="w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-black text-gray-700">Tidak Ada Periode Aktif</h3>
            <p class="text-gray-400 text-sm mt-2 max-w-xs mx-auto leading-relaxed">Silakan hubungi Admin untuk mengaktifkan periode penilaian saat ini.</p>
        </div>
    @elseif($siswa->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-16 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-lg font-black text-gray-700">Data Siswa Kosong</h3>
            <p class="text-gray-400 text-sm mt-2 max-w-xs mx-auto leading-relaxed">Anda belum ditugaskan ke kelas atau kelas belum memiliki siswa terdaftar.</p>
        </div>
    @else

        {{-- ── TABEL DESKTOP ─────────────────────────── --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <div>
                    <h3 class="font-black text-gray-900 text-sm">Daftar Penilaian Siswa</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Klik sel minggu aktif untuk mulai menilai</p>
                </div>
                <div class="flex items-center gap-4 text-[11px] text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-400 inline-block"></span> Final</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-400 inline-block"></span> Draft</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-gray-200 inline-block border border-dashed border-gray-300"></span> Belum</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="sticky left-0 bg-gray-50 z-10 px-4 py-3 text-center w-12">No</th>
                            <th class="sticky left-12 bg-gray-50 z-10 px-4 py-3 text-left min-w-[200px]">Nama Siswa</th>
                            @foreach($semuaMinggu as $m)
                                <th class="px-3 py-3 text-center w-20 border-l border-gray-100
                                    {{ $mingguAktif && $m->id === $mingguAktif->id ? 'text-blue-600 bg-blue-50/50' : '' }}">
                                    M-{{ $m->minggu_ke }}
                                    @if($mingguAktif && $m->id === $mingguAktif->id)
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mx-auto mt-1"></div>
                                    @endif
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center border-l border-gray-100">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($siswa as $i => $s)
                            @php
                                $grid = $statusGrid[$s->id] ?? [];
                                $doneCount = collect($grid)->filter(fn($v) => $v === 'final')->count();
                                $totalM = $semuaMinggu->count();
                                $pct = $totalM > 0 ? ($doneCount/$totalM)*100 : 0;
                            @endphp
                            <tr class="hover:bg-green-50/40 transition-colors duration-150 group odd:bg-white even:bg-gray-50/30">
                                <td class="sticky left-0 bg-white group-hover:bg-green-50/40 z-10 px-4 py-3.5 text-center text-gray-400 text-xs font-mono transition-colors">{{ $i + 1 }}</td>
                                <td class="sticky left-12 bg-white group-hover:bg-green-50/40 z-10 px-4 py-3.5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center text-[11px] font-black flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($s->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 leading-tight">{{ $s->nama }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $s->kode }}</p>
                                        </div>
                                    </div>
                                </td>

                                @foreach($semuaMinggu as $m)
                                    @php
                                        $st = $grid[$m->id] ?? null;
                                        $isAct = $mingguAktif && $m->id === $mingguAktif->id;
                                    @endphp
                                    <td class="px-3 py-3.5 text-center border-l border-gray-50 {{ $isAct ? 'bg-blue-50/20' : '' }}">
                                        <button
                                            @if($isAct) @click="openModal({{ $s->id }}, '{{ addslashes($s->nama) }}', {{ $m->id }}, '{{ $m->minggu_ke }}')" @else onclick="alert('Hanya minggu aktif yang dapat dinilai')" @endif
                                            class="w-9 h-9 mx-auto rounded-xl flex items-center justify-center transition-all duration-200 active:scale-90
                                                {{ $st === 'final'
                                                    ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 hover:scale-105'
                                                    : ($st === 'draft'
                                                        ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 hover:scale-105'
                                                        : ($isAct ? 'bg-white border-2 border-dashed border-blue-300 text-blue-400 hover:border-blue-500 hover:text-blue-600 hover:scale-105' : 'bg-gray-100 text-gray-300 cursor-not-allowed opacity-60')) }}"
                                            title="{{ $st ? ucfirst($st) : ($isAct ? 'Klik untuk input nilai' : 'Bukan minggu aktif') }}">
                                            @if($st === 'final')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($st === 'draft')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            @elseif($isAct)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                            @else
                                                <span class="text-xs">—</span>
                                            @endif
                                        </button>
                                    </td>
                                @endforeach

                                <td class="px-4 py-3.5 text-center border-l border-gray-50">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-xs font-black text-gray-700">{{ $doneCount }}/{{ $totalM }}</span>
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500
                                                {{ $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                                style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="20" class="py-16 text-center text-gray-400 italic text-sm">Belum ada siswa ditemukan di kelas Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── CARD LIST MOBILE ──────────────────────── --}}
        <div class="md:hidden space-y-3">
            <div class="flex items-center justify-between mb-2 px-1">
                <h3 class="font-black text-gray-900 text-sm">Daftar Siswa</h3>
                <span class="text-[11px] text-gray-400">{{ $siswa->count() }} siswa</span>
            </div>
            @forelse($siswa as $i => $s)
                @php
                    $grid2 = $statusGrid[$s->id] ?? [];
                    $mingguAktifStatus = $mingguAktif ? ($grid2[$mingguAktif->id] ?? null) : null;
                    $doneCount2 = collect($grid2)->filter(fn($v) => $v === 'final')->count();
                    $totalM2 = $semuaMinggu->count();
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-all duration-200 active:scale-[0.99]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center text-lg font-black flex-shrink-0 shadow-md">
                            {{ strtoupper(substr($s->nama, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-black text-gray-900 leading-tight">{{ $s->nama }}</h4>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $s->kode }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-700">{{ $doneCount2 }}/{{ $totalM2 }}</p>
                            <p class="text-[10px] text-gray-400">Final</p>
                        </div>
                    </div>
                    @if($mingguAktif)
                        <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-[11px] text-gray-500">Minggu {{ $mingguAktif->minggu_ke }} (Aktif)</span>
                            @if($mingguAktifStatus === 'final')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase">✓ Final</span>
                            @elseif($mingguAktifStatus === 'draft')
                                <button @click="openModal({{ $s->id }}, '{{ addslashes($s->nama) }}', {{ $mingguAktif->id }}, '{{ $mingguAktif->minggu_ke }}')"
                                    class="px-4 py-2 bg-amber-100 text-amber-700 rounded-xl text-[11px] font-black uppercase hover:bg-amber-200 transition active:scale-95">Edit Draft</button>
                            @else
                                <button @click="openModal({{ $s->id }}, '{{ addslashes($s->nama) }}', {{ $mingguAktif->id }}, '{{ $mingguAktif->minggu_ke }}')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[11px] font-black uppercase hover:bg-blue-700 transition active:scale-95 shadow-sm">+ Nilai</button>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center text-gray-400 italic text-sm">Belum ada data penilaian</div>
            @endforelse
        </div>
    @endif


    {{-- ══ MODAL INPUT (MODERN) ══════════════════════ --}}
    <template x-teleport="body">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeModal()"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(15,23,42,0.6); backdrop-filter: blur(6px);"
             x-cloak>

            <div class="w-full sm:max-w-3xl bg-white sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                 style="max-height: 95dvh;"
                 @click.stop
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 flex-shrink-0 bg-white">
                    <div>
                        <h3 class="text-base font-black text-gray-900" x-text="studentName"></h3>
                        <p class="text-xs text-gray-400 mt-0.5 font-medium">
                            Input Penilaian &bull; Minggu <span x-text="mingguName" class="font-bold text-blue-600"></span>
                            @if($periode) &bull; {{ $periode->nama_periode }} @endif
                        </p>
                    </div>
                    <button @click="closeModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 transition active:scale-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto">
                    @if($semuaMinggu && $semuaMinggu->count() > 0)
                        @foreach($semuaMinggu as $m)
                            @php $jadwalMingguIni = $jadwalPerMinggu[$m->id] ?? collect(); @endphp
                            @foreach($siswa as $s)
                                @php
                                    $isSiswaFinal = ($statusGrid[$s->id][$m->id] ?? null) === 'final';
                                    $isMingguSelesai = $m->status === 'selesai';
                                    $isReadOnly = $isMingguSelesai || $isSiswaFinal;
                                @endphp
                                <div id="form-{{ $s->id }}-{{ $m->id }}" class="penilaian-form-container hidden">
                                    <form action="{{ route('guru.penilaian.store') }}" method="POST">
                                        @csrf
                                        <div class="p-6 space-y-4">
                                            @if($isReadOnly)
                                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-semibold">
                                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-8V11m0-8a9 9 0 110 18 9 9 0 010-18z"/></svg>
                                                    {{ $isMingguSelesai ? 'Minggu sudah ditutup. Data hanya dapat dilihat.' : 'Nilai sudah Final dan tidak dapat diubah.' }}
                                                </div>
                                            @endif

                                            @forelse($jadwalMingguIni as $idx => $jadwal)
                                                @php
                                                    $existingNilai = $penilaianExisting->where('siswa_id', $s->id)->where('jadwal_sub_id', $jadwal->id)->first();
                                                    $selectedKatId = $existingNilai ? $existingNilai->kategori_id : null;
                                                    $catatanTxt = $existingNilai ? $existingNilai->catatan : '';
                                                @endphp
                                                <div class="rounded-2xl border border-gray-100 bg-gray-50/60 hover:bg-white hover:border-gray-200 hover:shadow-sm p-4 transition-all duration-200">
                                                    <div class="flex items-start gap-3 mb-4">
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black flex-shrink-0">{{ $idx + 1 }}</span>
                                                        <div>
                                                            <p class="text-sm font-black text-gray-900 leading-tight">{{ $jadwal->subkriteria->nama }}</p>
                                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $jadwal->subkriteria->kriteria->nama ?? '' }}</p>
                                                        </div>
                                                    </div>

                                                    {{-- Radio Buttons (3 kategori) --}}
                                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                                        @foreach($kategoriNilai as $kat)
                                                            @php
                                                                $ac = $kat->nama === 'MB' ? 'rose'
                                                                    : ($kat->nama === 'BSH' ? 'amber' : 'emerald');
                                                                $isSel = $selectedKatId == $kat->id;
                                                            @endphp
                                                            <label class="relative cursor-pointer {{ $isReadOnly ? 'pointer-events-none opacity-70' : '' }}">
                                                                <input type="radio"
                                                                    name="nilai[{{ $s->id }}][{{ $jadwal->id }}]"
                                                                    value="{{ $kat->id }}"
                                                                    {{ $isSel ? 'checked' : '' }}
                                                                    {{ $isReadOnly ? 'disabled' : 'required' }}
                                                                    class="sr-only peer">
                                                                <div class="flex flex-col items-center justify-center py-3 rounded-xl border-2 transition-all duration-150 cursor-pointer
                                                                    border-gray-100 bg-white hover:border-{{ $ac }}-300 hover:bg-{{ $ac }}-50/50
                                                                    peer-checked:border-{{ $ac }}-500 peer-checked:bg-{{ $ac }}-50 peer-checked:shadow-sm
                                                                    active:scale-95">
                                                                    <div class="w-3 h-3 rounded-full border-2 border-gray-200 peer-checked:border-{{ $ac }}-500 mb-1.5
                                                                        {{ $isSel ? 'border-'.$ac.'-500 bg-'.$ac.'-500' : '' }}"></div>
                                                                    <span class="text-[11px] font-black uppercase tracking-tight {{ $isSel ? 'text-'.$ac.'-700' : 'text-gray-400' }}">{{ $kat->nama }}</span>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                    {{-- Catatan --}}
                                                    <textarea
                                                        name="catatan[{{ $s->id }}][{{ $jadwal->id }}]"
                                                        rows="2"
                                                        {{ $isReadOnly ? 'disabled' : '' }}
                                                        class="w-full px-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl text-gray-600 placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition resize-none"
                                                        placeholder="Catatan perkembangan (opsional)..."
                                                        oninput="this.style.height='';this.style.height=this.scrollHeight+'px'">{{ $catatanTxt }}</textarea>
                                                </div>
                                            @empty
                                                <div class="py-12 text-center text-gray-400 italic text-sm">Jadwal belum disetel untuk minggu ini.</div>
                                            @endforelse
                                        </div>

                                        {{-- Modal Footer --}}
                                        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex gap-3">
                                            <button type="button" @click="closeModal()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition active:scale-95">Batal</button>
                                            @if(!$isReadOnly)
                                                <button type="submit" name="status" value="draft"
                                                    class="flex-1 px-5 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 text-sm font-black rounded-xl transition active:scale-95">
                                                    Simpan Draft
                                                </button>
                                                <button type="submit" name="status" value="final"
                                                    onclick="return confirm('Finalisasi nilai? Tidak dapat diubah setelah ini.')"
                                                    class="flex-1 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black rounded-xl transition active:scale-95 shadow-sm shadow-emerald-900/20">
                                                    ✓ Finalisasi
                                                </button>
                                            @else
                                                <button type="button" @click="closeModal()" class="flex-1 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition">
                                                    Tutup (Read only)
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="py-16 text-center text-gray-400 italic text-sm">Tidak ada minggu penilaian yang tersedia.</div>
                    @endif
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
