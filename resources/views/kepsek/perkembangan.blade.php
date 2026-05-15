@extends('layouts.app')
@section('title', 'Perkembangan Siswa — Perbandingan & Tren')
@section('page-title', 'Perkembangan')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── TREND SECTION ── --}}
    <div class="card p-8 group overflow-hidden relative border-none shadow-xl">
        <div class="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
            <svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
        </div>
        
        <div class="flex items-center justify-between mb-8 relative z-10">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                <span class="w-6 h-px bg-gray-200"></span>
                Tren Capaian Agregat
            </h4>
            <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-var(--accent-lt) border border-var(--accent)/10">
                <span class="w-1.5 h-1.5 rounded-full bg-var(--accent) animate-pulse"></span>
                <span class="text-[10px] font-black text-var(--accent) uppercase tracking-widest">{{ $periodeAktif->nama_periode ?? '—' }}</span>
            </div>
        </div>
        
        <div class="h-[340px] w-full">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ── COMPARISON TABLE ── --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.25em] flex items-center gap-3">
                <span class="w-4 h-4 rounded-lg bg-var(--accent) text-white flex items-center justify-center">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                Cross-Student Performance Matrix
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ count($siswaData) }} Siswa Terdaftar</span>
            </div>
        </div>

        <div class="card overflow-hidden border-none shadow-xl">
            <div class="overflow-x-auto">
                <table class="tbl">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="pl-8 text-[10px] font-black text-gray-400 tracking-wide">Informasi Siswa</th>
                            @foreach($trendLabels as $label)
                                <th class="text-center w-24 text-[10px] tracking-wide font-black text-gray-400">{{ $label }}</th>
                            @endforeach
                             <th class="text-center w-32 text-[10px] tracking-wide font-black text-gray-400">Index Akhir</th>
                             <th class="text-center w-28 text-[10px] tracking-wide font-black text-gray-400">Status Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($siswaData as $row)
                            <tr class="hover:bg-var(--bg) transition-colors group">
                                <td class="py-5 pl-8">
                                    <div class="flex items-center gap-4">
                                        @if(!empty($row['foto']))
                                            <img src="{{ asset('storage/' . $row['foto']) }}" alt="{{ $row['nama'] }}" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-gray-100 group-hover:border-var(--accent)/30 transition-all">
                                        @else
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shadow-sm bg-white border border-gray-100 group-hover:border-var(--accent)/30 transition-all">
                                                {{ strtoupper(substr($row['nama'], 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-black text-gray-800 tracking-tight leading-tight mb-1">{{ $row['nama'] }}</p>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $row['kelas'] }}</span>
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">NISN: {{ $row['id_siswa'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($trendLabels as $index => $label)
                                    @php $mKe = $index + 1; $mScore = $row['mingguan'][$mKe] ?? null; @endphp
                                    <td class="text-center">
                                        @if($mScore !== null)
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-black tracking-tighter {{ $mScore >= 85 ? 'text-emerald-600' : ($mScore >= 70 ? 'text-amber-600' : 'text-rose-600') }}">
                                                    {{ $mScore }}%
                                                </span>
                                                <div class="w-12 h-1 bg-gray-100 rounded-full mt-2 overflow-hidden shadow-inner">
                                                    <div class="h-full {{ $mScore >= 85 ? 'bg-emerald-500' : ($mScore >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $mScore }}%"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-200 text-[10px] font-black uppercase tracking-widest">N/A</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($row['nilai_akhir'], 1) }}%</span>
                                </td>
                                <td class="text-center pr-4">
                                    @php $color = $row['kategori'] === 'BSB' ? 'bsb' : ($row['kategori'] === 'BSH' ? 'bsh' : 'mb'); @endphp
                                    <span class="badge badge-{{ $color }} px-4 py-1 text-[9px] font-black uppercase shadow-sm">{{ $row['kategori'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($trendLabels) }}" class="py-24 text-center">
                                    <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 border border-gray-100 shadow-inner">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-black text-gray-900 tracking-tight">Belum Ada Data Perkembangan</h4>
                                    <p class="text-xs text-gray-400 mt-2">Data mingguan untuk periode ini masih dalam tahap pengumpulan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendLabels) !!},
            datasets: [{
                label: 'Rata-rata Sekolah',
                data: {!! json_encode($trendData) !!},
                borderColor: '#84934A',
                backgroundColor: 'rgba(132, 147, 74, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#84934A'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection
