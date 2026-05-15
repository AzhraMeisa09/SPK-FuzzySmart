@extends('layouts.app')
@section('title', 'Daftar Siswa')
@section('page-title', 'Daftar Siswa')

@section('content')
<div class="space-y-6 fade-in" x-data="siswaModule()">
    
    {{-- ── HEADER ── --}}
    <div class="card p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Daftar siswa</h2>
                <p class="text-xs mt-0.5" style="color: var(--text-3);">Kelola dan lihat informasi detail siswa pada kelas Anda.</p>
            </div>

            {{-- SEARCH --}}
            <div class="search-box w-full lg:w-80">
                <input type="text" 
                       x-model="search" 
                       placeholder="Cari nama atau NISN...">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-3);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- ── STUDENT GROUPS BY CLASS ── --}}
    @php
        $groupedSiswa = $siswa->groupBy(fn($s) => $s->kelas->nama_kelas);
    @endphp

    @forelse($groupedSiswa as $namaKelas => $students)
    <div class="space-y-4" x-show="hasMatchesInClass({{ json_encode($students->map(fn($s) => ['nama' => strtolower($s->name), 'nisn' => strtolower($s->id_siswa ?: '')])) }})">
        <div class="flex items-center gap-3 px-1">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-500 text-white shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-1);">Kelas {{ $namaKelas }}</h3>
                <p class="text-[10px]" style="color: var(--text-3);">{{ $students->count() }} Siswa Terdaftar</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Siswa</th>
                            <th>NISN</th>
                            <th>Orang tua / wali</th>
                            <th>No. HP</th>
                            <th class="w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                        <tr class="hover:bg-gray-50/50 transition-colors group" x-show="matches('{{ strtolower($s->name) }}', '{{ strtolower($s->id_siswa) }}')">
                            <td class="font-mono text-[10px]" style="color: var(--text-3);">{{ $loop->iteration }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shadow-sm" style="background: var(--accent-lt); color: var(--accent);">
                                        {{ strtoupper(substr($s->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-xs leading-none" style="color: var(--text-1);">{{ $s->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg bg-gray-100" style="color: var(--text-2);">
                                    {{ $s->id_siswa ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <p class="text-xs font-semibold leading-tight" style="color: var(--text-1);">{{ $s->nama_orang_tua }}</p>
                            </td>
                            <td>
                                <p class="text-[10px] font-medium" style="color: var(--text-2);">{{ $s->no_hp_orang_tua ?: '-' }}</p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('guru.siswa.show', $s->id_siswa) }}" 
                                       class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> 
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card p-20 text-center">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background: var(--bg); border: 1px solid var(--border);">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="font-medium text-xs italic" style="color: var(--text-3);">Belum ada data siswa</p>
    </div>
    @endforelse

@push('scripts')
<script>
    function siswaModule() {
        return {
            search: '',

            matches(nama, nisn) {
                if (!this.search) return true;
                const s = this.search.toLowerCase();
                return nama.includes(s) || nisn.includes(s);
            },

            hasMatchesInClass(students) {
                if (!this.search) return true;
                const s = this.search.toLowerCase();
                return students.some(std => std.nama.includes(s) || std.nisn.includes(s));
            }
        }
    }
</script>
@endpush

@endsection
