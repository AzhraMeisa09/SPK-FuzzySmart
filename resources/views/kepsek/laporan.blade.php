@extends('layouts.app')

@section('title', 'Laporan Akhir')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Laporan Akhir Semester</h1>
            <p class="text-xs text-slate-500 font-medium">Manajemen dokumen laporan hasil belajar</p>
        </div>
        <div class="flex items-center gap-3">
            <select class="form-select w-40 font-bold">
                <option>Semester Ganjil 2023</option>
            </select>
        </div>
    </div>

    <!-- Grouped Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(['A1 (Bintang)', 'A2 (Bulan)', 'B1 (Matahari)', 'B2 (Awan)', 'C1 (Anggrek)', 'C2 (Mawar)'] as $kelas)
            <div class="card p-6 flex flex-col justify-between hover:shadow-lg transition-all border-t-4 border-t-emerald-600">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        @include('components.icons.document-text')
                    </div>
                    <x-badge type="info">{{ $loop->index % 2 == 0 ? 'Verified' : 'Review' }}</x-badge>
                </div>
                <div>
                     <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight mb-1">Rapor Kelas {{ $kelas }}</h3>
                     <p class="text-[10px] text-slate-400 font-medium leading-relaxed mb-6">Total 24 dokumen siswa terlampir dalam satu paket laporan PDF.</p>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 btn btn-secondary py-2 text-[10px] uppercase">Review</button>
                    <button class="flex-1 btn btn-success py-2 text-[10px] uppercase">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        PDF
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
