@extends('layouts.app')
@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Rekap Nilai')

@section('content')
<div class="space-y-5 fade-in">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Rekapitulasi Nilai</h1>
            <p class="text-sm text-gray-400 mt-0.5">Ringkasan penilaian perkembangan seluruh siswa di kelas Anda.</p>
        </div>
        <button onclick="window.print()" class="btn btn-gray text-xs no-print self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak
        </button>
    </div>

    {{-- ── STAT CARDS (3 warna utama: hijau, kuning, merah) ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- BSB --}}
        <div class="card card-hover p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-green-50 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge badge-bsb text-[10px]">BSB</span>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Berkembang Sangat Baik</p>
            <p class="text-3xl font-black text-green-700 mt-1">{{ $statistics['BSB'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Entri penilaian ≥85</p>
        </div>

        {{-- BSH --}}
        <div class="card card-hover p-5 border-l-4 border-yellow-400">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-yellow-50 text-yellow-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge badge-bsh text-[10px]">BSH</span>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sesuai Harapan</p>
            <p class="text-3xl font-black text-yellow-600 mt-1">{{ $statistics['BSH'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Entri penilaian 70–84</p>
        </div>

        {{-- MB --}}
        <div class="card card-hover p-5 border-l-4 border-red-400">
            <div class="flex items-center justify-between mb-3">
                <div class="stat-icon bg-red-50 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="badge badge-mb text-[10px]">MB</span>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mulai Berkembang</p>
            <p class="text-3xl font-black text-red-600 mt-1">{{ $statistics['MB'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Entri penilaian &lt;70</p>
        </div>
    </div>

    {{-- ── TABLE (Desktop) + CARD (Mobile) ───────────── --}}

    {{-- Desktop Table --}}
    <div class="card overflow-hidden hidden sm:block">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-50">
            <h3 class="font-black text-gray-900 text-sm">Rincian Nilai Per Siswa</h3>
            <span class="text-[11px] text-gray-400">{{ $records->count() }} siswa</span>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th class="text-center w-12">No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Total Entri</th>
                        <th class="text-center">Rata-rata</th>
                        <th class="text-center">Progress</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $idx => $r)
                        <tr class="hover:bg-green-50/30 transition-colors odd:bg-white even:bg-gray-50/30">
                            <td class="text-center font-mono text-gray-400 text-xs">{{ $idx + 1 }}</td>
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-green-600 text-white flex items-center justify-center text-[11px] font-black flex-shrink-0">
                                        {{ strtoupper(substr($r['nama'], 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $r['nama'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">{{ $r['total_entri'] }}</span>
                            </td>
                            <td class="text-center font-mono font-black text-gray-900 text-sm">
                                {{ $r['avg'] > 0 ? number_format($r['avg'], 2) : '—' }}
                            </td>
                            <td class="text-center">
                                <div class="flex items-center gap-2 justify-center">
                                    <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full
                                            {{ $r['kategori'] === 'BSB' ? 'progress-green' : ($r['kategori'] === 'BSH' ? 'progress-yellow' : ($r['kategori'] === 'MB' ? 'progress-red' : '')) }} progress-fill"
                                            style="width: {{ $r['avg'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $r['kategori'] === 'BSB' ? 'badge-bsb' : ($r['kategori'] === 'BSH' ? 'badge-bsh' : ($r['kategori'] === 'MB' ? 'badge-mb' : 'badge-nonaktif')) }}">
                                    {{ $r['kategori'] ?: '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('guru.laporan') }}?siswa_id={{ $loop->iteration }}" class="btn btn-sm btn-gray text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-16 text-center text-gray-400 italic text-sm">Belum ada data nilai yang masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden space-y-3">
        <h3 class="font-black text-gray-900 text-sm px-1">Rincian Per Siswa</h3>
        @forelse($records as $idx => $r)
            <div class="card p-4 hover:shadow-md transition-all duration-200 active:scale-[0.99]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center font-black text-base flex-shrink-0">
                        {{ strtoupper(substr($r['nama'], 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-gray-900 leading-tight truncate">{{ $r['nama'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $r['total_entri'] }} data penilaian</p>
                    </div>
                    <span class="badge {{ $r['kategori'] === 'BSB' ? 'badge-bsb' : ($r['kategori'] === 'BSH' ? 'badge-bsh' : ($r['kategori'] === 'MB' ? 'badge-mb' : 'badge-nonaktif')) }}">
                        {{ $r['kategori'] ?: '—' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs text-gray-500 font-semibold">Rata-rata:</span>
                    <span class="font-mono font-black text-gray-900 text-sm">{{ $r['avg'] > 0 ? number_format($r['avg'], 2) : '—' }}</span>
                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden ml-1">
                        <div class="h-full rounded-full progress-fill
                            {{ $r['kategori'] === 'BSB' ? 'progress-green' : ($r['kategori'] === 'BSH' ? 'progress-yellow' : ($r['kategori'] === 'MB' ? 'progress-red' : '')) }}"
                            style="width: {{ $r['avg'] }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-gray-400 italic text-sm border-dashed">Belum ada data nilai.</div>
        @endforelse
    </div>

</div>
@endsection
