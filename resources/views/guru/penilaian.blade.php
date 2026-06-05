@extends('layouts.app')
@section('title', 'Input Penilaian Siswa')
@section('page-title', 'Input Penilaian')

@section('content')

<div class="space-y-6 fade-in" x-data="{
    search: '',
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
    },
    matches(nama, kode) {
        if (!this.search) return true;
        const s = this.search.toLowerCase();
        return nama.toLowerCase().includes(s) || kode.toLowerCase().includes(s);
    },
    hasMatchesInClass(students) {
        if (!this.search) return true;
        const s = this.search.toLowerCase();
        return students.some(std => std.nama.includes(s) || std.nisn.includes(s));
    }
}">

    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-var(--accent-lt) text-var(--accent) flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Input Penilaian Mingguan</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        @if($periode)
                            <span class="badge badge-blue text-[9px] px-2.5 py-0.5 uppercase tracking-wider">{{ $periode->nama_periode }}</span>
                        @endif
                        @if($mingguAktif)
                            <span class="badge badge-bsb text-[9px] px-2.5 py-0.5 uppercase tracking-wider">Minggu {{ $mingguAktif->minggu_ke }}</span>
                            <span class="text-[10px] font-bold text-gray-400 italic">"{{ $mingguAktif->tema }}"</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="search-box w-full sm:w-64">
                    <input type="text" x-model="search" placeholder="Cari nama siswa...">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl border border-gray-100 shadow-sm">
                    <div class="p-1.5 bg-white rounded-lg text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-xs font-bold text-gray-700">{{ auth()->user()->kelas->pluck('nama')->implode(', ') ?: '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(!$periode)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center mx-auto mb-4 border border-rose-100">
                <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Tidak Ada Periode Aktif</h3>
            <p class="text-gray-400 text-xs mt-2 max-w-xs mx-auto leading-relaxed">Silakan hubungi Admin untuk mengaktifkan periode penilaian saat ini.</p>
        </div>
    @elseif($siswa->isEmpty())
        <div class="card p-20 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Data Siswa Kosong</h3>
            <p class="text-gray-400 text-xs mt-2 max-w-xs mx-auto leading-relaxed">Anda belum ditugaskan ke kelas atau kelas belum memiliki siswa terdaftar.</p>
        </div>
    @else

        {{-- ── GRID LEGEND ── --}}
        <div class="flex items-center justify-end gap-5 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Final</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Draft</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-gray-200 border border-gray-300"></div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Belum</span>
            </div>
        </div>

        {{-- ── STUDENT GROUPS ── --}}
        @php
            $groupedSiswa = $siswa->groupBy(fn($s) => $s->kelas->nama_kelas);
        @endphp

        @foreach($groupedSiswa as $namaKelas => $studentsInClass)
            <div class="space-y-4 mb-8" x-show="hasMatchesInClass({{ json_encode($studentsInClass->map(fn($s) => ['nama' => strtolower($s->name), 'nisn' => strtolower($s->kode ?: $s->id_siswa ?: '')])) }})">
                <div class="flex items-center gap-3 px-1 mt-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-1);">Kelas {{ $namaKelas }}</h3>
                        <p class="text-[10px]" style="color: var(--text-3);">{{ $studentsInClass->count() }} Siswa Terdaftar</p>
                    </div>
                </div>

                {{-- ── TABEL DESKTOP ── --}}
                <div class="hidden md:block card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th class="sticky left-0 bg-gray-50 z-20 w-12">No</th>
                                    <th class="sticky left-12 bg-gray-50 z-20 min-w-[200px]">Nama Siswa</th>
                                    @foreach($semuaMinggu as $m)
                                        <th class="w-20 border-l border-gray-100/50
                                            {{ $mingguAktif && $m->id === $mingguAktif->id ? 'bg-blue-50/50 text-blue-600' : '' }}">
                                            M{{ $m->minggu_ke }}
                                            @if($mingguAktif && $m->id === $mingguAktif->id)
                                                <div class="w-1 h-1 rounded-full bg-blue-500 mt-0.5"></div>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="border-l border-gray-100/50">Progres</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentsInClass as $i => $s)
                                    @php
                                        $grid = $statusGrid[$s->id_siswa] ?? [];
                                        $doneCount = collect($grid)->filter(fn($v) => $v === 'final')->count();
                                        $totalM = $semuaMinggu->count();
                                        $pct = $totalM > 0 ? ($doneCount/$totalM)*100 : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition-colors group" x-show="matches('{{ addslashes($s->name) }}', '{{ strtolower($s->kode ?: $s->id_siswa) }}')">
                                        <td class="sticky left-0 bg-white group-hover:bg-gray-50 z-10 font-mono text-[10px] text-gray-400">{{ $loop->iteration }}</td>
                                        <td class="sticky left-12 bg-white group-hover:bg-gray-50 z-10">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-var(--bg) text-var(--text-3) flex items-center justify-center font-black text-xs border border-var(--border) shadow-sm group-hover:border-var(--accent)/30 transition-colors">
                                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-800 text-xs leading-none">{{ $s->name }}</p>
                                                    <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $s->kode ?: $s->id_siswa }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        @foreach($semuaMinggu as $m)
                                            @php
                                                $st = $grid[$m->id_minggu] ?? null;
                                                $isAct = $mingguAktif && $m->id_minggu === $mingguAktif->id_minggu;
                                            @endphp
                                            <td class="border-l border-gray-50/50 {{ $isAct ? 'bg-blue-50/10' : '' }}">
                                                <button type="button"
                                                    @if($isAct) @click="openModal('{{ $s->id_siswa }}', {{ Js::from($s->name) }}, '{{ $m->id_minggu }}', '{{ $m->minggu_ke }}')" @else onclick="alert('Hanya minggu aktif yang dapat dinilai')" @endif
                                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200 active:scale-90 shadow-sm
                                                        {{ $st === 'final'
                                                            ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 hover:shadow-md'
                                                            : ($st === 'draft'
                                                                ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 hover:shadow-md'
                                                                : ($isAct ? 'bg-white border-2 border-dashed border-blue-200 text-blue-400 hover:border-blue-500 hover:text-blue-600' : 'bg-gray-100/50 text-gray-300 cursor-not-allowed opacity-50')) }}"
                                                    title="{{ $st ? ucfirst($st) : ($isAct ? 'Klik untuk input nilai' : 'Bukan minggu aktif') }}">
                                                    @if($st === 'final')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                                    @elseif($st === 'draft')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    @elseif($isAct)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                                                    @else
                                                        <span class="text-[10px] font-bold">—</span>
                                                    @endif
                                                </button>
                                            </td>
                                        @endforeach

                                        <td class="border-l border-gray-50/50 py-4">
                                            <div class="flex flex-col items-start gap-1.5 pl-2">
                                                <span class="text-[10px] font-black text-gray-700">{{ $doneCount }}/{{ $totalM }}</span>
                                                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                                                    <div class="h-full rounded-full transition-all duration-700
                                                        {{ $pct >= 100 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-blue-500' : 'bg-amber-400') }}"
                                                        style="width: {{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── CARD LIST MOBILE ── --}}
                <div class="md:hidden space-y-4">
                    @foreach($studentsInClass as $s)
                        @php
                            $grid2 = $statusGrid[$s->id_siswa] ?? [];
                            $mingguAktifStatus = $mingguAktif ? ($grid2[$mingguAktif->id_minggu] ?? null) : null;
                            $doneCount2 = collect($grid2)->filter(fn($v) => $v === 'final')->count();
                            $totalM2 = $semuaMinggu->count();
                        @endphp
                        <div class="card p-4 hover:shadow-lg transition-all active:scale-[0.98]" x-show="matches('{{ addslashes($s->name) }}', '{{ strtolower($s->kode ?: $s->id_siswa) }}')">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-var(--accent-lt) text-var(--accent) flex items-center justify-center text-lg font-black shadow-sm">
                                    {{ strtoupper(substr($s->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-sm leading-tight">{{ $s->name }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-bold">{{ $s->kode ?: $s->id_siswa }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-black text-gray-700">{{ $doneCount2 }}/{{ $totalM2 }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Final</p>
                                </div>
                            </div>
                            @if($mingguAktif)
                                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Minggu {{ $mingguAktif->minggu_ke }}</span>
                                    @if($mingguAktifStatus === 'final')
                                        <span class="badge badge-bsb text-[9px] px-3 py-1 font-black text-emerald-900">✓ FINAL</span>
                                    @elseif($mingguAktifStatus === 'draft')
                                        <button type="button" @click="openModal('{{ $s->id_siswa }}', {{ Js::from($s->name) }}, '{{ $mingguAktif->id_minggu }}', '{{ $mingguAktif->minggu_ke }}')"
                                            class="px-5 py-2 bg-amber-200 text-black rounded-xl text-[10px] font-black hover:bg-amber-300 transition-all shadow-sm">Edit Draft</button>
                                    @else
                                        <button type="button" @click="openModal('{{ $s->id_siswa }}', {{ Js::from($s->name) }}, '{{ $mingguAktif->id_minggu }}', '{{ $mingguAktif->minggu_ke }}')"
                                            class="px-5 py-2 bg-var(--accent) text-black rounded-xl text-[10px] font-black hover:bg-black hover:text-white transition-all shadow-md shadow-var(--accent)/20">+ Nilai</button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if($siswa->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30 card">
                {{ $siswa->links() }}
            </div>
        @endif

    @endif

    {{-- ══ MODAL INPUT ══ --}}
    <template x-teleport="body">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="closeModal()"
             class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="background: rgba(15,23,42,0.6); backdrop-filter: blur(6px);"
             x-cloak>

            <div class="w-full sm:max-w-3xl bg-white sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col"
                 style="max-height: 95dvh;"
                 @click.stop
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100 flex-shrink-0 bg-white">
                    <div>
                        <h3 class="text-base font-bold text-gray-800" x-text="studentName"></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Input Penilaian &bull; Minggu </span>
                            <span x-text="mingguName" class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[10px] font-black"></span>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-all active:scale-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto scrollbar-hide">
                    @foreach($semuaMinggu as $m)
                        @php $jadwalMingguIni = $jadwalPerMinggu[$m->id_minggu] ?? collect(); @endphp
                        @foreach($siswa as $s)
                            @php
                                $isSiswaFinal = ($statusGrid[$s->id_siswa][$m->id_minggu] ?? null) === 'final';
                                $isMingguSelesai = $m->status === 'selesai';
                                $isReadOnly = $isMingguSelesai || $isSiswaFinal;
                            @endphp
                            <div id="form-{{ $s->id_siswa }}-{{ $m->id_minggu }}" class="penilaian-form-container hidden">
                                <form action="{{ route('guru.penilaian.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $s->id_siswa }}">
                                    <input type="hidden" name="minggu_id" value="{{ $m->id_minggu }}">
                                    
                                    <div class="p-8 space-y-6">
                                        @if($isReadOnly)
                                            <div class="flex items-center gap-4 px-5 py-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold shadow-sm">
                                                <div class="p-2 bg-amber-200 rounded-xl">
                                                    <svg class="w-4 h-4 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 15v2m0-8V11m0-8a9 9 0 110 18 9 9 0 010-18z"/></svg>
                                                </div>
                                                <span>{{ $isMingguSelesai ? 'Minggu sudah ditutup. Data hanya dapat dilihat.' : 'Nilai sudah Final dan tidak dapat diubah oleh Guru.' }}</span>
                                            </div>
                                        @endif

                                        @forelse($jadwalMingguIni as $idx => $jadwal)
                                            @php
                                                $existingNilai = $penilaianExisting->where('siswa_id', $s->id_siswa)->where('jadwal_sub_id', $jadwal->id_jadwal_sub)->first();
                                                $selectedKatId = $existingNilai ? $existingNilai->kategori_id : null;
                                                $catatanTxt = $existingNilai ? $existingNilai->catatan : '';
                                            @endphp
                                            <div class="rounded-3xl border border-gray-100 bg-gray-50/40 p-6 transition-all duration-300 hover:shadow-md hover:bg-white hover:border-var(--accent)/20 group/card">
                                                <div class="flex items-start gap-4 mb-5">
                                                    <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center text-[10px] font-black text-var(--accent) group-hover/card:bg-var(--accent) group-hover/card:text-white">{{ $idx + 1 }}</div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold text-gray-800 leading-snug">{{ $jadwal->subkriteria->nama_subkriteria }}</p>
                                                        <p class="text-[9px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ $jadwal->subkriteria->kriteria->nama_kriteria ?? '' }}</p>
                                                    </div>
                                                </div>

                                                {{-- Radio Buttons with Rubrics (Horizontal Boxes) --}}
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-4 mb-5">
                                                    @foreach($kategoriNilai as $kat)
                                                        @php
                                                            $val = $kat->nama;
                                                            $rubrik = match($val) {
                                                                'BSB' => $jadwal->subkriteria->rubrik_bsb,
                                                                'BSH' => $jadwal->subkriteria->rubrik_bsh,
                                                                'MB'  => $jadwal->subkriteria->rubrik_mb,
                                                                default => null
                                                            };
                                                            $colorVariants = match($val) {
                                                                'BSB' => 'peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:ring-2 peer-checked:ring-emerald-100 peer-checked:text-emerald-700 peer-checked:[&_.indicator-circle]:bg-emerald-500 peer-checked:[&_.indicator-circle]:border-emerald-500 peer-checked:[&_.checkmark-icon]:opacity-100',
                                                                'BSH' => 'peer-checked:border-amber-500 peer-checked:bg-amber-50/50 peer-checked:ring-2 peer-checked:ring-amber-100 peer-checked:text-amber-700 peer-checked:[&_.indicator-circle]:bg-amber-500 peer-checked:[&_.indicator-circle]:border-amber-500 peer-checked:[&_.checkmark-icon]:opacity-100',
                                                                'MB'  => 'peer-checked:border-rose-500 peer-checked:bg-rose-50/50 peer-checked:ring-2 peer-checked:ring-rose-100 peer-checked:text-rose-700 peer-checked:[&_.indicator-circle]:bg-rose-500 peer-checked:[&_.indicator-circle]:border-rose-500 peer-checked:[&_.checkmark-icon]:opacity-100',
                                                                default => 'peer-checked:border-blue-500 peer-checked:bg-blue-50/50 peer-checked:ring-2 peer-checked:ring-blue-100 peer-checked:text-blue-700'
                                                            };
                                                        @endphp
                                                        <label class="relative flex flex-col cursor-pointer {{ $isReadOnly ? 'pointer-events-none opacity-70' : '' }}">
                                                            <input type="radio"
                                                                name="nilai[{{ $s->id_siswa }}][{{ $jadwal->id_jadwal_sub }}]"
                                                                value="{{ $kat->id_kategori }}"
                                                                {{ $selectedKatId == $kat->id_kategori ? 'checked' : '' }}
                                                                {{ $isReadOnly ? 'disabled' : 'required' }}
                                                                class="sr-only peer">
                                                            <div class="flex flex-col flex-1 gap-3 p-4 rounded-2xl border-2 transition-all duration-200 
                                                                border-gray-100 bg-white {{ $colorVariants }}
                                                                hover:border-gray-200 hover:bg-gray-50 active:scale-[0.98] shadow-sm">
                                                                
                                                                <div class="flex items-center justify-between">
                                                                    <div class="indicator-circle w-5 h-5 rounded-full border-2 border-gray-200 flex-shrink-0 flex items-center justify-center transition-all">
                                                                        <svg class="checkmark-icon w-3.5 h-3.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4.5">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                    </div>
                                                                    <span class="text-[11px] font-black uppercase tracking-wider">{{ $val }}</span>
                                                                </div>

                                                                <div class="min-w-0">
                                                                    @if($rubrik)
                                                                        <p class="text-[10px] text-gray-500 leading-snug font-medium peer-checked:text-inherit transition-colors">{{ $rubrik }}</p>
                                                                    @else
                                                                        <p class="text-[10px] text-gray-400 italic leading-snug">Tanpa keterangan.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                {{-- Catatan --}}
                                                <textarea
                                                    name="catatan[{{ $jadwal->id_jadwal_sub }}]"
                                                    rows="2"
                                                    {{ $isReadOnly ? 'disabled' : '' }}
                                                    class="w-full px-5 py-3 text-xs bg-white border border-gray-100 rounded-2xl text-gray-600 placeholder-gray-400 focus:ring-2 focus:ring-var(--accent) focus:border-var(--accent) transition-all resize-none shadow-sm"
                                                    placeholder="Tambahkan catatan perkembangan anak di sini...">{{ $catatanTxt }}</textarea>
                                            </div>
                                        @empty
                                            <div class="py-20 text-center text-gray-400 italic text-sm">Jadwal penilaian belum disetel untuk minggu ini.</div>
                                        @endforelse
                                    </div>

                                    {{-- Modal Footer --}}
                                    <div class="sticky bottom-0 bg-white/80 backdrop-blur-md border-t border-gray-100 px-8 py-5 flex gap-4">
                                        <button type="button" @click="closeModal()" class="px-8 py-3 bg-gray-50 hover:bg-gray-100 text-gray-500 text-xs font-black rounded-2xl transition active:scale-95">Batal</button>
                                        @if(!$isReadOnly)
                                            <button type="submit" name="is_final" value="0"
                                                class="flex-1 px-8 py-3 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-black rounded-2xl transition active:scale-95">Simpan Draf</button>
                                            <button type="submit" name="is_final" value="1"
                                                onclick="return confirm('Apakah Anda yakin ingin memfinalisasi nilai? Data tidak dapat diubah setelah disimpan sebagai Final.')"
                                                class="flex-1 px-8 py-3 bg-emerald-600 hover:bg-black text-white text-xs font-black rounded-2xl transition active:scale-95 shadow-lg shadow-emerald-900/20">✓ Finalisasi</button>
                                        @else
                                            <button type="button" @click="closeModal()" class="flex-1 px-8 py-3 bg-gray-900 hover:bg-black text-white text-xs font-black rounded-2xl transition active:scale-95">Tutup Rincian</button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </template>

</div>

@endsection
