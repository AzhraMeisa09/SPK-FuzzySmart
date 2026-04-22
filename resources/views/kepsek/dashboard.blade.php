@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Monitoring Dashboard</h1>
            <p class="text-xs text-slate-500 font-medium">Pemantauan statistik penilaian global</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-purple-100">
                Kepala Sekolah
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Progres Penilaian', '92%', 'Trending Up', 'chart-bar', 'emerald'],
            ['Siswa BSB', '48', 'Excellent', 'academic-cap', 'blue'],
            ['Laporan Terbit', '124', 'Sesuai Target', 'document-text', 'amber'],
            ['Kelas Terpantau', '6/6', 'Semua Kelas', 'building', 'purple'],
        ] as [$title, $val, $info, $icon, $color])
            <div class="card p-4 group hover:shadow-md transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $title }}</p>
                        <h3 class="text-xl font-black text-slate-800 leading-none">{{ $val }}</h3>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center shadow-sm border border-{{ $color }}-100 group-hover:scale-110 transition-transform">
                        @include('components.icons.' . $icon)
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[9px] font-bold text-slate-400">
                    <span class="text-{{ $color }}-600">●</span>
                    {{ $info }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Placeholder -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Capaian Perkembangan Per Kriteria</h3>
                <select class="form-select w-32 py-1 text-[10px] font-bold">
                    <option>Mei 2024</option>
                </select>
            </div>
            <div class="space-y-6">
                @foreach(['Agama & Moral' => 85, 'Fisik Motorik' => 70, 'Kognitif' => 75, 'Sosial Emosional' => 90] as $label => $val)
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-bold text-slate-700">{{ $label }}</span>
                            <span class="text-xs font-black text-slate-500">{{ $val }}%</span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $val }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side info -->
        <div class="card p-6 bg-slate-900 text-white border-none shadow-xl">
             <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Pesan Penting</h3>
             <div class="space-y-6">
                 <div class="flex gap-4">
                     <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 flex-shrink-0"></div>
                     <div>
                         <p class="text-xs font-bold text-white leading-tight mb-1">Finalisasi Rapor Semester</p>
                         <p class="text-[10px] text-slate-400 font-medium">Sisa 3 hari lagi untuk memverifikasi laporan seluruh kelas.</p>
                     </div>
                 </div>
                 <div class="flex gap-4">
                     <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 flex-shrink-0"></div>
                     <div>
                         <p class="text-xs font-bold text-white leading-tight mb-1">Rapat Evaluasi Guru</p>
                         <p class="text-[10px] text-slate-400 font-medium">Jumat, 17 Mei pukul 09:00 via Zoom Meeting.</p>
                     </div>
                 </div>
             </div>
             <div class="mt-10 pt-6 border-t border-white/10">
                 <a href="/kepsek/evaluasi?role=kepsek" class="w-full btn btn-success py-2 text-[10px] uppercase tracking-widest">Lihat Hasil Evaluasi</a>
             </div>
        </div>
    </div>
</div>
@endsection
