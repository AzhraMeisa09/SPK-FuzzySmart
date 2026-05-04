@extends('layouts.app')
@section('title', 'Detail Analisis Siswa — ' . $siswa->nama)
@section('page-title', 'Detail Siswa')

@section('content')
<div class="space-y-6 pb-20 fade-in">

    {{-- ── HERO BANNER ── --}}
    <div class="rounded-[2.5rem] p-10 flex flex-col md:flex-row md:items-center justify-between gap-8 shadow-2xl relative overflow-hidden" style="background: linear-gradient(135deg, #313D29 0%, #4A5D3E 100%);">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#84934A]/10 rounded-full -ml-24 -mb-24 blur-2xl"></div>

        <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left relative z-10">
            <div class="w-24 h-24 rounded-3xl flex items-center justify-center text-[#313D29] font-black text-4xl shadow-2xl transform hover:scale-105 transition-transform duration-500 bg-white/90 backdrop-blur-md border-4 border-white/20">
                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <p class="text-[10px] font-bold uppercase tracking-[0.3em]" style="color: rgba(255,255,255,.7);">Analisis Profil Siswa</p>
                </div>
                <h1 class="text-3xl font-black text-white leading-tight tracking-tight">{{ $siswa->nama }}</h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-4">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 text-[#C5CF8E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span class="text-[11px] font-bold text-white uppercase tracking-wider">{{ $siswa->kode ?: 'NISN: —' }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 text-[#C5CF8E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-[11px] font-bold text-[#C5CF8E] uppercase tracking-wider">{{ $siswa->kelas->nama_kelas ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6 items-center md:items-end relative z-10">
            <div class="flex flex-col gap-2 min-w-[200px]">
                <label class="text-[9px] font-black text-white/60 uppercase tracking-[0.2em] text-center md:text-right">Filter Periode Analisis</label>
                <form action="{{ route('kepsek.siswa.show', $siswa->id) }}" method="GET">
                    <div class="relative group">
                        <select name="periode_id" class="w-full bg-white/10 border border-white/10 text-white text-xs font-bold rounded-xl px-4 py-2.5 cursor-pointer appearance-none focus:ring-2 focus:ring-[#84934A]/50 focus:border-[#84934A] transition-all" onchange="this.form.submit()">
                            @foreach($periodeList as $p)
                                <option value="{{ $p->id }}" {{ $selectedPeriodeId == $p->id ? 'selected' : '' }} class="text-gray-900">{{ $p->nama_periode }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-white/40 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kepsek.siswa') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white transition-all bg-white/5 border border-white/10 hover:bg-white/10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                <form action="{{ route('kepsek.laporan.generate-word') }}" method="POST">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                    <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-2.5 rounded-xl text-[#313D29] font-black uppercase tracking-widest text-[10px] shadow-2xl shadow-black/10 hover:scale-105 active:scale-95 transition-all bg-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak Laporan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── ANALYSIS SECTION ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Final Score & Insights --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="card p-8 flex flex-col items-center justify-center text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-48 h-48 text-[#84934A]" fill="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-10 relative z-10">Hasil Evaluasi SPK</p>
                
                @if($evaluasi)
                    <div class="relative mb-10 group-hover:scale-105 transition-transform duration-500">
                        <div class="w-40 h-40 rounded-full border-[12px] border-gray-50 flex flex-col items-center justify-center shadow-inner relative z-10 bg-white">
                            <span class="text-4xl font-black text-gray-900 leading-none tracking-tighter">{{ number_format($evaluasi->nilai_akhir * 100, 1) }}%</span>
                            <span class="text-[10px] font-black text-gray-400 mt-2 uppercase tracking-widest">Global Index</span>
                        </div>
                        <div class="absolute inset-0 rounded-full border-[12px] border-[#84934A] opacity-20" style="clip-path: inset(0 {{ 100 - ($evaluasi->nilai_akhir * 100) }}% 0 0);"></div>
                    </div>

                    @php 
                        $colors = [
                            'BSB' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100', 'dot' => 'bg-emerald-500'],
                            'BSH' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100', 'dot' => 'bg-amber-500'],
                            'MB'  => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100', 'dot' => 'bg-rose-500'],
                        ];
                        $c = $colors[$evaluasi->kategori_akhir] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-100', 'dot' => 'bg-gray-500'];
                    @endphp

                    <div class="flex items-center gap-3 {{ $c['bg'] }} {{ $c['text'] }} {{ $c['border'] }} border px-8 py-2.5 rounded-2xl font-black text-[11px] shadow-sm uppercase tracking-widest relative z-10">
                        <span class="w-2 h-2 rounded-full {{ $c['dot'] }} animate-pulse"></span>
                        {{ $evaluasi->kategori_akhir }}
                    </div>
                    
                    <div class="w-full mt-12 pt-8 border-t border-gray-100 text-left space-y-8 relative z-10">
                        <div class="bg-indigo-50/30 p-5 rounded-2xl border border-indigo-100/50">
                            <h4 class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Rekomendasi Strategis
                            </h4>
                            <p class="text-[11px] font-bold text-indigo-900 leading-relaxed italic">"{{ $evaluasi->rekomendasi }}"</p>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                Catatan Guru Pembimbing
                            </h4>
                            <p class="text-[11px] text-gray-500 leading-relaxed font-medium italic">"{{ $evaluasi->catatan_guru ?: 'Tidak ada catatan tambahan untuk periode ini.' }}"</p>
                        </div>
                    </div>
                @else
                    <div class="py-24">
                        <div class="w-20 h-20 rounded-[2rem] bg-gray-50 flex items-center justify-center mx-auto mb-6 border border-gray-100 shadow-inner">
                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-xs text-gray-300 font-bold uppercase tracking-widest">Belum Ada Evaluasi Final</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Criteria Analysis Chart --}}
        <div class="lg:col-span-8 card p-10 flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Visualisasi Capaian</h4>
                    <p class="text-sm font-black text-gray-900 tracking-tight">Perbandingan Aspek Kriteria</p>
                </div>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#84934A]"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">Siswa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-100 border border-indigo-200"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tight">Rata-rata</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 min-h-[500px] relative">
                <canvas id="criteriaDetailChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── PORTOFOLIO SECTION ── --}}
    <div class="space-y-6">
        <div class="flex items-center gap-4 px-2">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Portofolio & Dokumentasi</h3>
            <div class="h-px flex-1 bg-gray-100"></div>
        </div>
        
        @if($portofolio->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($portofolio as $p)
                    <div class="card overflow-hidden group hover:translate-y-[-4px] transition-all duration-500 hover:shadow-2xl hover:shadow-gray-200/50 border-none">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-[10px] shadow-sm">
                                        M{{ $p->minggu->minggu_ke }}
                                    </div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $p->minggu->tema }}</span>
                                </div>
                                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-tighter">{{ $p->created_at->format('d/m/Y') }}</span>
                            </div>
                            <h3 class="text-sm font-black text-gray-900 leading-tight mb-3 group-hover:text-indigo-600 transition-colors">{{ $p->judul }}</h3>
                            <div class="relative pl-4">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-100 rounded-full group-hover:bg-indigo-100 transition-colors"></div>
                                <p class="text-[11px] text-gray-500 line-clamp-3 italic font-medium leading-relaxed">"{{ $p->deskripsi }}"</p>
                            </div>
                        </div>
                        @if($p->images->count() > 0)
                            <div class="px-6 pb-6 mt-2">
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($p->images->take(3) as $img)
                                        <div class="aspect-square rounded-2xl overflow-hidden border border-gray-100 shadow-sm relative group/img cursor-pointer">
                                            <img src="{{ asset('storage/' . $img->file_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110">
                                            <div class="absolute inset-0 bg-indigo-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card p-24 text-center border-dashed border-2 border-gray-100 bg-gray-50/30">
                <div class="w-24 h-24 rounded-[2.5rem] bg-white flex items-center justify-center mx-auto mb-8 shadow-sm text-gray-200 border border-gray-50">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h4 class="text-sm font-black text-gray-900 tracking-tight">Belum Ada Dokumentasi</h4>
                <p class="text-xs text-gray-400 mt-2 max-w-xs mx-auto font-medium">Siswa ini belum mengunggah karya atau dokumentasi portofolio untuk periode yang dipilih.</p>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('criteriaDetailChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: {!! json_encode(array_keys($kriteriaScores->toArray())) !!},
            datasets: [{
                label: 'Capaian Siswa',
                data: {!! json_encode(array_values($kriteriaScores->toArray())) !!},
                backgroundColor: 'rgba(132, 147, 74, 0.25)',
                borderColor: '#84934A',
                borderWidth: 3,
                pointBackgroundColor: '#84934A',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                tension: 0
            }, {
                label: 'Rata-rata Sekolah',
                data: {!! json_encode(array_values($schoolAverages->toArray())) !!},
                backgroundColor: 'rgba(99, 102, 241, 0.05)',
                borderColor: 'rgba(99, 102, 241, 0.5)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(99, 102, 241, 0.5)',
                fill: true,
                tension: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 20,
                    bottom: 20
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    min: 0,
                    ticks: { 
                        display: false,
                        stepSize: 20
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.08)',
                        lineWidth: 1
                    },
                    angleLines: {
                        color: 'rgba(0,0,0,0.08)',
                        lineWidth: 1
                    },
                    pointLabels: {
                        padding: 20,
                        font: {
                            size: 11,
                            weight: '800',
                            family: "'Inter', sans-serif"
                        },
                        color: '#475569'
                    }
                }
            },
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    cornerRadius: 12,
                    displayColors: true
                }
            }
        }
    });
</script>
@endpush
@endsection
