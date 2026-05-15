@extends('layouts.app')
@section('title', 'Analisis & Statistik Sekolah')
@section('page-title', 'Analisis & Statistik')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── PERIOD SELECTOR ── --}}
    <div class="card p-6 shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('kepsek.analisis') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-gray-800 tracking-tight leading-none mb-1">Periode Analisis</h4>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Pilih periode untuk melihat statistik</p>
                </div>
            </div>
            <div class="w-full md:w-72">
                <select name="periode_id" class="form-select w-full font-black text-xs h-[42px] rounded-xl border-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id_periode }}" {{ $selectedPeriodeId == $p->id_periode ? 'selected' : '' }} class="text-gray-900">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if(!$periodeAktif)
        <div class="card p-20 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tidak Ada Data</h3>
            <p class="text-[11px] text-gray-400 mt-2 max-w-xs mx-auto">Silakan pilih periode yang valid atau hubungi Admin untuk konfigurasi lebih lanjut.</p>
        </div>
    @else

    {{-- ── TOP RANKING SECTION ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Top 10 terbaik --}}
        <div class="card p-8 border-none shadow-xl relative overflow-hidden group/top">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-50 rounded-full opacity-0 group-hover/top:opacity-100 transition-all duration-700 blur-3xl"></div>
            
            <div class="flex items-center justify-between mb-8 relative z-10">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                    <span class="w-6 h-px bg-gray-200"></span>
                    Performa Unggulan (Top 10)
                </h4>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"/></svg>
                </div>
            </div>
            
            <div class="space-y-3 relative z-10">
                @foreach($topSiswa as $index => $eval)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50/50 border border-transparent hover:border-emerald-100 hover:bg-white transition-all group/item">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300 w-6">#{{ $index + 1 }}</span>
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center font-black text-xs text-gray-400 group-hover/item:border-emerald-200 group-hover/item:text-emerald-600 transition-all shadow-sm">
                                {{ strtoupper(substr($eval->siswa->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-800 tracking-tight leading-none mb-1">{{ $eval->siswa->name }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $eval->siswa->kelas->nama_kelas ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-emerald-600 tracking-tighter">{{ number_format($eval->nilai_akhir * 100, 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom 10 perlu perhatian --}}
        <div class="card p-8 border-none shadow-xl relative overflow-hidden group/bottom">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-rose-50 rounded-full opacity-0 group-hover/bottom:opacity-100 transition-all duration-700 blur-3xl"></div>

            <div class="flex items-center justify-between mb-8 relative z-10">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                    <span class="w-6 h-px bg-gray-200"></span>
                    Perlu Intervensi (Bottom 10)
                </h4>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <div class="space-y-3 relative z-10">
                @foreach($bottomSiswa as $index => $eval)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50/50 border border-transparent hover:border-rose-100 hover:bg-white transition-all group/item">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black text-gray-300 w-6">#{{ count($bottomSiswa) - $index }}</span>
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center font-black text-xs text-gray-400 group-hover/item:border-rose-200 group-hover/item:text-rose-600 transition-all shadow-sm">
                                {{ strtoupper(substr($eval->siswa->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-800 tracking-tight leading-none mb-1">{{ $eval->siswa->name }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $eval->siswa->kelas->nama_kelas ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-rose-600 tracking-tighter">{{ number_format($eval->nilai_akhir * 100, 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── CRITERIA & SUB ANALYSIS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Kriteria Averages --}}
        <div class="lg:col-span-8 card p-8 border-none shadow-xl">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-6 h-px bg-gray-200"></span>
                Analisis Capaian Aspek Perkembangan
            </h4>
            <div class="h-[340px]">
                <canvas id="kriteriaStatsChart"></canvas>
            </div>
        </div>

        {{-- Automated Insights --}}
        <div class="lg:col-span-4 flex flex-col gap-6">
            @foreach($insights as $insight)
            <div class="card p-7 border-l-4 border-indigo-500 shadow-xl relative overflow-hidden group/insight">
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-indigo-50 rounded-full opacity-50 group-hover/insight:scale-110 transition-transform"></div>
                <h5 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2 relative z-10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Strategic Point
                </h5>
                <p class="text-xs font-bold text-gray-700 leading-relaxed italic tracking-tight relative z-10">
                    "{{ $insight }}"
                </p>
            </div>
            @endforeach
            
            <div class="card p-10 flex-1 flex flex-col justify-center text-center relative overflow-hidden group/global border-none shadow-2xl" style="background: linear-gradient(135deg, #1B211A 0%, #2D3A2A 100%);">
                <div class="absolute top-0 right-0 p-8 opacity-[0.05]">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <span class="w-8 h-px bg-[#84934A]"></span>
                        <h4 class="text-[10px] font-black text-[#84934A] uppercase tracking-[0.3em]">Kesimpulan Global</h4>
                    </div>
                    
                    @php $lowestSub = $subkriteriaStats->first(); @endphp
                    @if($lowestSub)
                        <p class="text-base font-black text-white leading-relaxed italic tracking-tight">
                            "Area prioritas peningkatan saat ini tertuju pada <span class="text-[#C5CF8E]">{{ $lowestSub->nama }}</span>. Dibutuhkan penguatan kurikulum dan pendampingan intensif pada indikator ini."
                        </p>
                    @endif
                    
                    <div class="mt-8 flex justify-center items-center gap-4 text-[9px] font-black text-white/30 uppercase tracking-[0.25em]">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Analisis Strategis
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    {{-- ── SUBKRITERIA & KELAS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Subkriteria Ranking --}}
        <div class="lg:col-span-7 card p-8 border-none shadow-xl">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-6 h-px bg-gray-200"></span>
                Indikator dengan Capaian Terendah
            </h4>
            <div class="space-y-6">
                @foreach($subkriteriaStats->take(5) as $sub)
                    <div class="group/bar">
                        <div class="flex items-center justify-between mb-2 text-[10px] font-black uppercase tracking-widest">
                            <span class="text-gray-600">{{ $sub->nama }}</span>
                            <span class="text-rose-600">{{ number_format($sub->avg, 1) }}%</span>
                        </div>
                        <div class="bg-gray-50 h-2 rounded-full overflow-hidden shadow-inner p-0.5">
                            <div class="bg-rose-500 h-full rounded-full group-hover/bar:bg-rose-400 transition-all duration-700 shadow-sm" style="width: {{ $sub->avg }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kelas Distribution --}}
        <div class="lg:col-span-5 card p-8 border-none shadow-xl">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-6 h-px bg-gray-200"></span>
                Rata-rata Kapasitas Kelas
            </h4>
            <div class="space-y-3">
                @foreach($kelasStats as $ks)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50/50 border border-transparent hover:border-indigo-100 hover:bg-white transition-all group/ks">
                        <div class="flex items-center gap-4">
                            <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-indigo-500 flex items-center justify-center text-[10px] font-black uppercase shadow-sm group-hover/ks:bg-indigo-500 group-hover/ks:text-white group-hover/ks:border-indigo-500 transition-all">
                                {{ substr($ks['nama'], 0, 2) }}
                            </div>
                            <span class="text-xs font-black text-gray-800 tracking-tight">{{ $ks['nama'] }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">{{ $ks['avg'] }}%</span>
                            <div class="w-16 h-1 bg-gray-100 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ $ks['avg'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxKrit = document.getElementById('kriteriaStatsChart').getContext('2d');
    new Chart(ctxKrit, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($kriteriaStats)) !!},
            datasets: [{
                label: 'Skor Rata-rata',
                data: {!! json_encode(array_values($kriteriaStats)) !!},
                backgroundColor: '#84934A',
                borderRadius: 12,
                barThickness: 50
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
