@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 pb-20 fade-in">
    
    {{-- ── HERO BANNER ── --}}
    <div class="rounded-xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm border border-gray-100" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);">
        <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-[#84934A] font-black text-3xl shadow-xl bg-white/90 backdrop-blur-sm transform hover:scale-105 transition-transform">
                🏛️
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1.5" style="color: rgba(255,255,255,.7);">Manajemen Strategis Sekolah</p>
                <h1 class="text-2xl font-black tracking-tight text-white leading-tight">Dashboard Kepala Sekolah</h1>
                <p class="text-xs mt-2 leading-relaxed font-medium" style="color: rgba(255,255,255,.85);">
                    Pantau metrik performa akademik dan distribusi capaian siswa secara komprehensif.
                </p>
            </div>
        </div>
        <div class="flex flex-col gap-2 min-w-[200px]">
            <label class="text-[10px] font-black text-white/70 uppercase tracking-widest text-center md:text-left">Pilih Periode Analisis</label>
            <form action="{{ route('kepsek.dashboard') }}" method="GET" id="periodForm">
                <select name="periode_id" class="w-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-black rounded-xl px-4 py-2.5 focus:ring-0 focus:border-white/40 cursor-pointer appearance-none" onchange="this.form.submit()">
                    @foreach($periodeList as $p)
                        <option value="{{ $p->id_periode }}" {{ $selectedPeriodeId == $p->id_periode ? 'selected' : '' }} class="text-gray-900">{{ $p->nama_periode }} - {{ $p->tahunAjaran->nama ?? '—' }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @php
            $statCards = [
                ['label' => 'Total Siswa', 'value' => $totalSiswa, 'sub' => 'Seluruh Angkatan', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'indigo'],
                ['label' => 'Total Kelas', 'value' => $totalKelas, 'sub' => 'Unit Belajar Aktif', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'emerald'],
                ['label' => 'Rata-rata SPK', 'value' => number_format($rataRataSekolah, 1) . '%', 'sub' => 'Performa Agregat', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'amber'],
                ['label' => 'Kategori MB', 'value' => $distribusiKategori['MB'], 'sub' => 'Perlu Intervensi', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
            ];
        @endphp

        @foreach($statCards as $s)
        <div class="card p-5 group hover:border-var(--accent)/30 transition-all duration-300" style="border-left: 4px solid #84934A;">
            <div class="flex items-center gap-4 mb-4">
                <div class="stat-icon bg-var(--accent-lt) text-var(--accent)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $s['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] mb-0.5">{{ $s['label'] }}</p>
                    <h3 class="text-lg font-black text-gray-900 tracking-tighter leading-none">{{ $s['value'] }}</h3>
                </div>
            </div>
            <div class="pt-3 border-t border-gray-50 flex items-center justify-between">
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $s['sub'] }}</span>
                <svg class="w-3 h-3 text-gray-300 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── CHARTS & INSIGHTS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Distribution Chart --}}
        <div class="lg:col-span-4 card p-8 group overflow-hidden relative">
            <div class="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
                <svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                <span class="w-6 h-px bg-gray-200"></span>
                Distribusi Capaian
            </h4>
            
            <div class="aspect-square relative flex items-center justify-center max-w-[240px] mx-auto">
                <canvas id="categoryChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-gray-900 leading-none">{{ $totalSiswa }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Siswa Total</span>
                </div>
            </div>
            
            <div class="mt-8 space-y-3">
                @foreach([
                    'BSB' => ['indigo', 'Berkembang Sangat Baik', 'Mampu mandiri konsisten & membantu teman.'], 
                    'BSH' => ['emerald', 'Berkembang Sesuai Harapan', 'Mampu mandiri konsisten tanpa pengingat.'], 
                    'MB' => ['rose', 'Mulai Berkembang', 'Masih sering membutuhkan bimbingan guru.']
                ] as $kat => $meta)
                <div class="flex items-start justify-between p-3 rounded-xl bg-gray-50/50 border border-transparent hover:border-gray-100 transition-all">
                    <div class="flex items-start gap-3">
                        <span class="w-2.5 h-2.5 mt-0.5 rounded-full shadow-sm bg-{{ $meta[0] }}-500 flex-shrink-0"></span>
                        <div>
                            <p class="text-[10px] font-black text-gray-900 leading-none mb-1.5">{{ $kat }} — {{ $meta[1] }}</p>
                            <p class="text-[9px] font-medium text-gray-500 leading-relaxed pr-2">{{ $meta[2] }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-black text-gray-900 mt-0.5">{{ $distribusiKategori[$kat] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Criteria Chart --}}
        <div class="lg:col-span-8 card p-8">
            <div class="flex items-center justify-between mb-8">
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-3">
                    <span class="w-6 h-px bg-gray-200"></span>
                    Rata-rata per Aspek Kriteria
                </h4>
            </div>
            
            <div class="h-[320px] w-full">
                <canvas id="criteriaChart"></canvas>
            </div>
            
            {{-- HIGHLIGHT INSIGHTS --}}
            @if(!empty($insights))
            <div class="mt-10 pt-8 border-t border-gray-100">
                <h5 class="text-[10px] font-black text-var(--accent) uppercase tracking-[0.2em] mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Strategic Insights
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($insights as $insight)
                    <div class="p-5 rounded-2xl bg-var(--bg) border border-var(--border) hover:border-var(--accent)/20 transition-all group/insight flex items-start gap-4">
                        <div class="w-6 h-6 rounded-lg bg-white border border-var(--border) flex items-center justify-center flex-shrink-0 mt-0.5 group-hover/insight:bg-var(--accent) group-hover/insight:text-white transition-colors">
                            <span class="text-[10px] font-black">{{ $loop->iteration }}</span>
                        </div>
                        <p class="text-xs font-bold text-gray-700 leading-relaxed italic tracking-tight">"{{ $insight }}"</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Chart
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: ['BSB', 'BSH', 'MB'],
            datasets: [{
                data: [{{ $distribusiKategori['BSB'] }}, {{ $distribusiKategori['BSH'] }}, {{ $distribusiKategori['MB'] }}],
                backgroundColor: ['#6366f1', '#10b981', '#f43f5e'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Criteria Chart
    const ctxKrit = document.getElementById('criteriaChart').getContext('2d');
    new Chart(ctxKrit, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($kriteriaAverages)) !!},
            datasets: [{
                label: 'Rata-rata Skor',
                data: {!! json_encode(array_values($kriteriaAverages)) !!},
                backgroundColor: '#84934A',
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { callback: value => value + '%' }
                },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection
