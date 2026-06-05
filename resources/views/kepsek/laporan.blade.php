@extends('layouts.app')
@section('title', 'Rekap Laporan Global')
@section('page-title', 'Laporan')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── FILTER SECTION ── --}}
    <div class="card p-6 no-print shadow-sm border border-gray-100">
        <form action="{{ route('kepsek.laporan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pilih Periode Analisis</label>
                <select name="periode_id" class="form-select">
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id_periode }}" {{ $selectedPeriodeId == $p->id_periode ? 'selected' : '' }}>{{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Saring Berdasarkan Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">Seluruh Unit Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id_kelas }}" {{ $selectedKelasId == $kelas->id_kelas ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3 flex items-end">
                <button type="submit" class="btn btn-green w-full justify-center h-[42px] rounded-xl shadow-lg shadow-green-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tampilkan Rekap
                </button>
            </div>
        </form>
    </div>

    {{-- ── REKAP GLOBAL ── --}}
    <div class="card p-12 print:shadow-none print:border-none border-none shadow-2xl relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-gray-50 rounded-full -mr-32 -mt-32 opacity-50 no-print"></div>
        
        {{-- Header KOP --}}
        <div class="relative flex flex-col md:flex-row items-center justify-between border-b-2 border-gray-100 pb-10 mb-10 gap-8">
            <div class="flex items-center gap-8">
                <img src="{{ asset('images/logotutwuri.jpg') }}" alt="Logo Tut Wuri" class="w-20 h-20 object-contain drop-shadow-md">
                <div class="text-center md:text-left">
                    <h2 class="text-xl font-black text-gray-900 tracking-tight leading-none mb-2">Rekapitulasi Hasil Evaluasi Siswa</h2>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">TK Negeri Pembina Kota Padang Panjang</p>
                </div>
            </div>
            <div class="text-right no-print">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-black/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan
                </button>

                <form action="{{ route('kepsek.laporan.generate-global-word') }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">
                    <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#84934A]/10 border border-[#84934A]/20 text-[#84934A] text-[10px] font-black uppercase tracking-widest hover:bg-[#84934A]/20 transition-all shadow-sm">
                        <svg class="w-4 h-4 text-[#84934A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Word (Rekap)
                    </button>
                </form>
            </div>
        </div>

        {{-- Statistics Overview ── Harmonized Theme Colors --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 mb-12 relative z-10">
            @php
                $rekapStats = [
                    [
                        'label' => 'Total Siswa',
                        'value' => $evaluasi->count(),
                        'sub' => 'Populasi Aktif',
                        'style' => 'background-color: var(--bg); border-color: var(--border); color: var(--text-1);',
                        'text_color' => 'var(--text-1)'
                    ],
                    [
                        'label' => 'Rata-rata Skor',
                        'value' => number_format($evaluasi->avg('nilai_akhir') * 100, 1) . '%',
                        'sub' => 'Performa Agregat',
                        'style' => 'background-color: #F0F4FA; border-color: rgba(74, 93, 110, 0.15); color: #4A5D6E;',
                        'text_color' => '#4A5D6E'
                    ],
                    [
                        'label' => 'Kategori BSB',
                        'value' => $evaluasi->where('kategori_akhir', 'BSB')->count(),
                        'sub' => 'Sangat Baik',
                        'style' => 'background-color: var(--accent-lt); border-color: rgba(132, 147, 74, 0.2); color: var(--accent);',
                        'text_color' => 'var(--accent)'
                    ],
                    [
                        'label' => 'Kategori BSH',
                        'value' => $evaluasi->where('kategori_akhir', 'BSH')->count(),
                        'sub' => 'Sesuai Harapan',
                        'style' => 'background-color: #FDF8ED; border-color: rgba(146, 112, 10, 0.2); color: #92700A;',
                        'text_color' => '#92700A'
                    ],
                    [
                        'label' => 'Kategori MB',
                        'value' => $evaluasi->where('kategori_akhir', 'MB')->count(),
                        'sub' => 'Mulai Berkembang',
                        'style' => 'background-color: var(--danger-lt); border-color: rgba(192, 57, 43, 0.2); color: var(--danger);',
                        'text_color' => 'var(--danger)'
                    ],
                ];
            @endphp
            @foreach($rekapStats as $rs)
                <div class="p-6 rounded-2xl border text-center transition-all hover:scale-[1.02] duration-200 group" style="{{ $rs['style'] }}">
                    <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-70" style="color: var(--text-2);">{{ $rs['label'] }}</p>
                    <p class="text-2xl font-black tracking-tighter leading-none mb-1" style="color: {{ $rs['text_color'] }};">{{ $rs['value'] }}</p>
                    <p class="text-[8px] font-bold uppercase tracking-tighter opacity-60" style="color: var(--text-2);">{{ $rs['sub'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── KESIMPULAN GLOBAL ── --}}
        <div class="mb-12 p-10 rounded-[2.5rem] relative overflow-hidden shadow-2xl" style="background: linear-gradient(135deg, #1C201C 0%, #293029 100%);">
            <div class="absolute top-0 right-0 p-8 opacity-[0.05]">
                <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <div class="relative z-10 max-w-4xl">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-10 h-px bg-[#84934A]"></span>
                    <h4 class="text-[10px] font-black text-[#84934A] uppercase tracking-[0.3em]">Kesimpulan Global</h4>
                </div>
                <p class="text-xl font-black text-white leading-relaxed italic tracking-tight">
                    "{!! $globalInsight !!}"
                </p>
                <div class="mt-8 flex items-center gap-4 text-[9px] font-black text-white/30 uppercase tracking-[0.25em]">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Analisis Strategis
                    </div>
                    <span class="w-1 h-1 rounded-full bg-white/10"></span>
                    <span>Sistem Pendukung Keputusan</span>
                </div>
            </div>
        </div>

        {{-- Table Rekap --}}
        <div class="overflow-hidden border border-gray-100 rounded-2xl relative z-10">
            <table class="tbl">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="w-16 text-center text-[10px] font-black text-gray-400 tracking-wide">No</th>
                        <th class="text-[10px] font-black text-gray-400 tracking-wide">Informasi Siswa</th>
                        <th class="text-[10px] font-black text-gray-400 tracking-wide">Unit Kelas</th>
                        <th class="text-center text-[10px] font-black text-gray-400 tracking-wide">Skor Akhir (V)</th>
                        <th class="text-center text-[10px] font-black text-gray-400 tracking-wide">Status Capaian</th>
                        <th class="no-print text-right pr-8 text-[10px] font-black text-gray-400 tracking-wide">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($evaluasi as $index => $eval)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="text-center text-[10px] font-mono font-bold text-gray-300">{{ $index + 1 }}</td>
                            <td class="py-5">
                                <div class="flex items-center gap-3">
                                    @if($eval->siswa->foto)
                                        <img src="{{ asset('storage/' . $eval->siswa->foto) }}" alt="{{ $eval->siswa->name }}" class="w-8 h-8 rounded-lg object-cover border border-gray-100 shadow-sm">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center font-black text-[10px] text-gray-400 group-hover:text-[#84934A] transition-colors">
                                            {{ strtoupper(substr($eval->siswa->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs font-black text-gray-800 tracking-tight leading-none mb-1">{{ $eval->siswa->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">NISN: {{ $eval->siswa->kode ?: $eval->siswa->id_siswa ?: '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-tight">{{ $eval->siswa->kelas->nama_kelas ?? '—' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($eval->nilai_akhir * 100, 1) }}%</span>
                            </td>
                            <td class="text-center">
                                @php $color = $eval->kategori_akhir === 'BSB' ? 'bsb' : ($eval->kategori_akhir === 'BSH' ? 'bsh' : 'mb'); @endphp
                                <div class="flex flex-col items-center gap-1.5">
                                    <span class="badge badge-{{ $color }} px-4 py-0.5 text-[8px] font-black shadow-sm uppercase">{{ match($eval->kategori_akhir) { 'BSB' => 'Berkembang Sangat Baik (BSB)', 'BSH' => 'Berkembang Sesuai Harapan (BSH)', 'MB' => 'Mulai Berkembang (MB)', default => $eval->kategori_akhir } }}</span>
                                    @if(isset($eval->is_draft))
                                        <span class="text-[7px] font-black text-rose-400 uppercase tracking-[0.2em] animate-pulse">Draft Mode</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right no-print pr-8">
                                <form action="{{ route('kepsek.laporan.generate-word') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $eval->siswa_id }}">
                                    <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:bg-[#84934A] hover:text-white hover:border-[#84934A] transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Signature --}}
        <div class="mt-20 grid grid-cols-2 gap-20 text-center relative z-10">
            <div class="space-y-24">
                <p class="text-[10px] font-black text-gray-800 tracking-widest uppercase">Kepala Sekolah</p>
                <div class="border-b border-gray-200 w-3/4 mx-auto"></div>
                <p class="text-[10px] font-black text-gray-800 tracking-widest">( ........................................ )</p>
            </div>
            <div class="space-y-24">
                <p class="text-[10px] font-black text-gray-800 tracking-widest">Padang Panjang, {{ now()->translatedFormat('d F Y') }}</p>
                <div class="border-b border-gray-200 w-3/4 mx-auto"></div>
                <p class="text-[10px] font-black text-gray-400 tracking-widest italic opacity-50 uppercase">Generated via Decision Support System</p>
            </div>
        </div>

    </div>

</div>

<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        main { padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        table { border-collapse: collapse !important; }
        th, td { border: 1px solid #eee !important; }
    }
</style>
@endsection
