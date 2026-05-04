@extends('layouts.app')
@section('title', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai')

@section('content')
<div class="space-y-6">

    {{-- ── HEADER ── --}}
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold" style="color: var(--text-1);">Rekapitulasi Nilai</h2>
                <p class="text-xs mt-1" style="color: var(--text-3);">Total periode: {{ $totalMingguInPeriode }} minggu</p>
            </div>
            <button onclick="window.print()" class="btn btn-blue btn-sm whitespace-nowrap">
                Cetak rekap
            </button>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="card p-5 border-l-4 border-green-500 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge badge-bsb">BSB</span>
            </div>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Sangat Baik</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $statistics['bsb'] }} Siswa</p>
        </div>

        <div class="card p-5 border-l-4 border-amber-400 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge badge-bsh">BSH</span>
            </div>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Sesuai Harapan</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $statistics['bsh'] }} Siswa</p>
        </div>

        <div class="card p-5 border-l-4 border-rose-500 card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="badge badge-mb">MB</span>
            </div>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Mulai Berkembang</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $statistics['mb'] }} Siswa</p>
        </div>
    </div>

    {{-- ── REKAP TABLE ── --}}
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-gray-50 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 tracking-tight text-[10px] uppercase tracking-wider">Daftar Rekapitulasi Capaian</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th class="pl-16">Nama Siswa</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">Capaian Minggu</th>
                        <th class="text-center">Rata-rata Global (%)</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td class="text-center text-gray-400 font-mono">{{ $loop->iteration }}</td>
                            <td class="pl-16">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm">
                                        {{ strtoupper(substr($r['nama'], 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $r['nama'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-mono font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded border border-gray-100">{{ $r['nisn'] ?: '-' }}</span>
                            </td>
                            <td class="text-center"><span class="badge badge-blue">{{ $r['kelas'] }}</span></td>
                            <td class="text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-black text-gray-700">
                                        {{ $r['total_minggu_data'] }} / {{ $r['divisor'] }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 uppercase font-black tracking-tighter">
                                        {{ $r['total_minggu_period'] > 0 ? 'Minggu Periode' : 'Minggu Terisi' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 mb-1">
                                        <span class="font-black text-blue-700 text-sm">{{ number_format($r['avg'], 1) }}%</span>
                                    </div>
                                    <span class="text-[8px] text-blue-400 font-black uppercase tracking-tighter italic leading-none mb-1">Bagi {{ $r['divisor'] }} Minggu</span>
                                    <div class="w-20 progress-track bg-gray-100 h-1 rounded-full overflow-hidden">
                                        <div class="progress-fill h-1 {{ $r['avg'] >= 85 ? 'progress-green' : ($r['avg'] >= 70 ? 'progress-yellow' : 'progress-red') }}"
                                             style="width: {{ min($r['avg'], 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $r['kategori'] === 'BSB' ? 'badge-bsb' : ($r['kategori'] === 'BSH' ? 'badge-bsh' : 'badge-mb') }}">
                                    {{ $r['kategori'] }}
                                </span>
                            </td>
                            <td class="text-center py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('guru.riwayat.detail', $r['siswa_id']) }}" class="p-2 rounded-xl bg-white border border-var(--border) text-var(--text-2) hover:text-var(--accent) hover:border-var(--accent) transition-all shadow-sm group" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400 italic">Belum ada data nilai yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
