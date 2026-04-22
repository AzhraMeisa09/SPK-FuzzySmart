@extends('layouts.app')
@section('title', 'Laporan Penilaian')
@section('page-title', 'Laporan')

@section('content')
<div class="space-y-5 fade-in">

    {{-- ── PAGE HEADER ─────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Laporan Penilaian Siswa</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pilih siswa untuk menampilkan laporan perkembangan lengkap.</p>
        </div>
        @if(!empty($reportData))
            <button onclick="window.print()" class="btn btn-gray text-xs no-print self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan
            </button>
        @endif
    </div>

    {{-- ── FILTER ───────────────────────────────────── --}}
    <div class="card p-5">
        <form action="{{ route('guru.laporan') }}" method="GET">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="form-label">Pilih Nama Siswa</label>
                    <select name="siswa_id" class="form-select">
                        <option value="">— Pilih siswa —</option>
                        @foreach($allSiswa as $siswa)
                            <option value="{{ $siswa->id }}" {{ $selectedSiswaId == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nama }} ({{ $siswa->kelas->nama ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-green whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- ── LAPORAN CONTENT ──────────────────────────── --}}
    @if(!empty($reportData))
        <div class="card overflow-hidden">

            {{-- Header siswa --}}
            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-green-600 text-white flex items-center justify-center text-2xl font-black flex-shrink-0 shadow-md">
                        {{ strtoupper(substr($reportData['siswa']->nama, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-black text-gray-900">{{ $reportData['siswa']->nama }}</h2>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">
                            <span class="badge badge-blue text-[10px]">{{ $reportData['siswa']->kelas->nama ?? '—' }}</span>
                            <span class="text-xs text-gray-400 font-medium">NISN: {{ $reportData['siswa']->nisn ?? '—' }}</span>
                            <span class="text-xs text-gray-400">Semester Ganjil 2024</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="text-center px-4 py-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Nilai Akhir</p>
                            <p class="text-2xl font-black text-gray-900 leading-none mt-1">{{ number_format($reportData['final_score'], 1) }}%</p>
                        </div>
                        <div class="text-center px-4 py-3 rounded-xl border-2 shadow-sm
                            {{ $reportData['final_kategori'] === 'BSB' ? 'border-green-400 bg-green-50' : ($reportData['final_kategori'] === 'BSH' ? 'border-yellow-400 bg-yellow-50' : 'border-red-400 bg-red-50') }}">
                            <p class="text-[9px] font-bold uppercase tracking-widest {{ $reportData['final_kategori'] === 'BSB' ? 'text-green-500' : ($reportData['final_kategori'] === 'BSH' ? 'text-yellow-600' : 'text-red-500') }}">Kategori</p>
                            <span class="badge {{ $reportData['final_kategori'] === 'BSB' ? 'badge-bsb' : ($reportData['final_kategori'] === 'BSH' ? 'badge-bsh' : 'badge-mb') }} text-sm mt-1">
                                {{ $reportData['final_kategori'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- I. Kartu Kriteria --}}
            <div class="p-6 border-b border-gray-50">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-[10px] font-black">I</span>
                    Capaian Per Kriteria
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($reportData['kriteria'] as $k)
                        @php $bColor = $k['kategori'] === 'BSB' ? 'border-green-400' : ($k['kategori'] === 'BSH' ? 'border-yellow-400' : 'border-red-400'); @endphp
                        <div class="p-4 rounded-xl border {{ $bColor }} border-l-4 bg-gray-50/50 hover:shadow-sm transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="badge badge-blue text-[9px] font-mono">{{ $k['kode'] }}</span>
                                <span class="badge {{ $k['kategori'] === 'BSB' ? 'badge-bsb' : ($k['kategori'] === 'BSH' ? 'badge-bsh' : 'badge-mb') }} text-[9px]">{{ $k['kategori'] }}</span>
                            </div>
                            <p class="text-sm font-bold text-gray-800 mb-3 leading-snug min-h-[36px]">{{ $k['nama'] }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 progress-track h-1.5">
                                    <div class="progress-fill h-1.5 {{ $k['kategori'] === 'BSB' ? 'progress-green' : ($k['kategori'] === 'BSH' ? 'progress-yellow' : 'progress-red') }}"
                                         style="width: {{ $k['avg'] }}%"></div>
                                </div>
                                <span class="text-xs font-black text-gray-700 w-10 text-right">{{ $k['avg'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- II. Detail Subkriteria (Desktop table + Mobile cards) --}}
            <div class="p-6 border-b border-gray-50">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-black">II</span>
                    Detail Indikator Subkriteria
                </h4>

                {{-- Desktop --}}
                <div class="overflow-x-auto hidden sm:block rounded-xl border border-gray-100">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th class="w-16">Kode</th>
                                <th>Indikator</th>
                                <th class="text-center w-24">Hasil</th>
                                <th>Catatan Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData['subkriteria'] as $sub)
                                <tr class="hover:bg-green-50/20 transition-colors odd:bg-white even:bg-gray-50/30">
                                    <td><span class="badge badge-blue font-mono text-[10px]">{{ $sub['kode'] }}</span></td>
                                    <td class="font-semibold text-gray-800 text-sm py-3.5">{{ $sub['nama'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $sub['nilai'] === 'BSB' ? 'badge-bsb' : ($sub['nilai'] === 'BSH' ? 'badge-bsh' : ($sub['nilai'] === 'MB' ? 'badge-mb' : 'badge-nonaktif')) }}">
                                            {{ $sub['nilai'] ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="text-xs text-gray-500 italic max-w-xs">{{ $sub['catatan'] !== '-' ? '"'.$sub['catatan'].'"' : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="sm:hidden space-y-3">
                    @foreach($reportData['subkriteria'] as $sub)
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-blue font-mono text-[9px]">{{ $sub['kode'] }}</span>
                                    <p class="text-sm font-bold text-gray-800">{{ $sub['nama'] }}</p>
                                </div>
                                <span class="badge {{ $sub['nilai'] === 'BSB' ? 'badge-bsb' : ($sub['nilai'] === 'BSH' ? 'badge-bsh' : ($sub['nilai'] === 'MB' ? 'badge-mb' : 'badge-nonaktif')) }} flex-shrink-0">
                                    {{ $sub['nilai'] ?: '—' }}
                                </span>
                            </div>
                            @if($sub['catatan'] !== '-')
                                <p class="text-xs text-gray-400 italic">"{{ $sub['catatan'] }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- III. Tanda Tangan --}}
            <div class="p-6 no-print">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center text-[10px] font-black">III</span>
                    Pengesahan
                </h4>
                <div class="grid grid-cols-3 gap-6 text-center">
                    @foreach(['Wali Murid', 'Wali Kelas', 'Kepala Sekolah'] as $sign)
                        <div>
                            <p class="text-xs text-gray-500 font-semibold">{{ $sign }}</p>
                            <div class="h-14 mt-3 border-b-2 border-dashed border-gray-200"></div>
                            <p class="text-[10px] text-gray-300 mt-2">(..................................)</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        {{-- Empty state --}}
        <div class="card p-16 text-center border-dashed border-2">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-black text-gray-400 text-base">Laporan belum dimuat</h3>
            <p class="text-gray-300 text-sm mt-1.5 max-w-xs mx-auto">Pilih nama siswa di atas lalu klik <strong>Tampilkan</strong> untuk melihat laporan perkembangan.</p>
        </div>
    @endif

</div>
@endsection
