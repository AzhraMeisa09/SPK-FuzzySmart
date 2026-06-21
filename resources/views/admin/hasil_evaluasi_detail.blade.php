@extends('layouts.app')
@section('title', 'Analisis SPK: ' . $evaluasi->siswa->name)
@section('page-title', 'Detail Analisis SPK')

@section('content')
<div class="space-y-6 pb-12">

    {{-- ── BACK NAVIGATION ────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.hasil_evaluasi') }}" class="w-12 h-12 flex items-center justify-center rounded-xl bg-white border border-var(--border) shadow-sm text-var(--text-3) hover:text-var(--accent) hover:border-var(--accent) transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-var(--text-1) tracking-tight leading-tight">Analisis Detail SPK</h1>
                <p class="text-[10px] text-var(--text-3) font-bold mt-0.5">Metodologi: Fuzzy SMART Decision Support</p>
            </div>
        </div>
        
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-green shadow-lg shadow-green-100 px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Rincian
            </button>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ─────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Profile --}}
        <div class="lg:col-span-4">
            <div class="card p-6 border-none shadow-xl flex items-center gap-5 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-blue-50 rounded-full"></div>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold text-2xl shadow-lg border-4 border-blue-50">
                    {{ strtoupper(substr($evaluasi->siswa->name, 0, 1)) }}
                </div>
                <div class="relative">
                    <h3 class="text-lg font-bold text-var(--text-1) tracking-tight leading-tight">{{ $evaluasi->siswa->name }}</h3>
                    <p class="text-[10px] font-bold text-blue-600 mt-1">Kelas {{ $evaluasi->siswa->kelas->nama_kelas ?? '—' }}</p>
                    <p class="text-[10px] font-bold text-var(--text-3) mt-1">NISN: {{ $evaluasi->siswa->kode ?: $evaluasi->siswa->id_siswa ?: '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Skor Akhir --}}
        <div class="lg:col-span-4">
            <div class="card p-6 border-none shadow-xl relative overflow-hidden bg-white">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-bold text-var(--text-3)">Skor Akhir Utilitas (V)</p>
                    <div class="w-8 h-8 rounded-lg bg-var(--bg) flex items-center justify-center">
                        <svg class="w-4 h-4 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-4">
                    <span class="text-4xl font-bold text-var(--text-1) tracking-tighter">{{ number_format($evaluasi->nilai_akhir, 3) }}</span>
                    @php
                        $catClass = match($evaluasi->kategori_akhir) {
                            'BSB' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                            'BSH' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
                            'MB'  => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                            default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200']
                        };
                    @endphp
                    <div class="px-6 py-2 {{ $catClass['bg'] }} {{ $catClass['border'] }} border-x-4 border-y text-xs font-black {{ $catClass['text'] }} rounded-lg uppercase tracking-widest text-center shadow-inner">
                        {{ match($evaluasi->kategori_akhir) { 'BSB' => 'Berkembang Sangat Baik (BSB)', 'BSH' => 'Berkembang Sesuai Harapan (BSH)', 'MB' => 'Mulai Berkembang (MB)', default => $evaluasi->kategori_akhir } }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Periode --}}
        <div class="lg:col-span-4">
            <div class="card p-6 border-none shadow-xl flex items-center gap-5 text-white" style="background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold opacity-80">Periode Evaluasi</p>
                    <h4 class="text-base font-bold tracking-tight">{{ $evaluasi->periode->nama_periode }}</h4>
                    <p class="text-[10px] font-bold opacity-70 mt-0.5">Semester {{ ucfirst($evaluasi->periode->semester) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── REKOMENDASI UMUM ────────────────────────────────── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-var(--accent) text-white flex items-center justify-center shadow-md">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-var(--text-1)">Saran & Rekomendasi Kolektif</h3>
        </div>
        <div class="p-8">
            <div class="p-6 rounded-2xl bg-var(--bg) border border-var(--border) text-var(--text-1) leading-relaxed text-sm font-medium italic">
                "{{ $evaluasi->rekomendasi ?? 'Data rekomendasi sistem belum digenerate.' }}"
            </div>
        </div>
    </div>

    {{-- ── TABEL RINCIAN PER SUBKRITERIA ──────────────────── --}}
    <div class="card overflow-hidden shadow-xl border-none">
        <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-var(--text-1)">Rincian Komponen Nilai (Utilisasi)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th class="pl-8">Komponen Perkembangan (Subkriteria)</th>
                        <th class="text-center">Skor Mentah (Crisp)</th>
                        <th class="text-center">Klasifikasi</th>
                        <th class="pr-8">Uraian Rekomendasi Spesifik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($evaluasi->detail as $detail)
                        <tr class="hover:bg-var(--bg) transition-colors">
                            <td class="pl-8 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-var(--text-1) leading-snug">{{ $detail->subkriteria->nama_subkriteria }}</span>
                                    <span class="text-[10px] text-var(--text-3) font-bold mt-1">{{ $detail->subkriteria->kriteria->nama_kriteria }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-sm font-bold text-var(--text-1) font-mono">{{ number_format($detail->nilai_crisp, 2) }}</span>
                                    <div class="w-12 h-1 bg-gray-100 rounded-full mt-1.5 overflow-hidden">
                                        <div class="h-full bg-var(--accent)" style="width: {{ $detail->nilai_crisp * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($detail->kategori) {
                                        'BSB' => 'bg-green-50 text-green-700 border-green-100',
                                        'BSH' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'MB'  => 'bg-red-50 text-red-700 border-red-100',
                                        default => 'bg-gray-50 text-gray-400'
                                    };
                                @endphp
                                <span class="px-2.5 py-1.5 border rounded-lg text-[10px] font-bold {{ $badgeClass }}">
                                    {{ $detail->kategori }}
                                </span>
                            </td>
                            <td class="pr-8 py-5">
                                <p class="text-[11px] text-var(--text-2) leading-relaxed font-medium max-w-sm italic">
                                    {{ $detail->rekomendasi_detail }}
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background-color: white !important; font-size: 10pt; }
    .card { box-shadow: none !important; border: 1px solid #eee !important; margin-bottom: 1.5rem; page-break-inside: avoid; }
    .bg-white { background-color: white !important; }
    @page { margin: 1cm; size: portrait; }
    table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #eee !important; }
    th, td { border: 1px solid #eee !important; padding: 12px !important; }
    .tbl thead { background-color: #f9fafb !important; }
    .text-4xl { font-size: 2rem !important; }
}
</style>
@endsection
