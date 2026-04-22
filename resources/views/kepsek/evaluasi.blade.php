@extends('layouts.app')
@section('title', 'Evaluasi Siswa')
@section('page-title', 'Evaluasi Siswa')

@section('content')
@php
$students = [
    ['Aditya Pratama', 'B1', 82, 'BSB'],
    ['Bella Cantika', 'B1', 72, 'BSH'],
    ['Citra Lestari', 'A1', 91, 'BSB'],
    ['Deni Setiawan', 'B2', 45, 'MB'],
    ['Eka Saputra', 'A2', 86, 'BSB'],
    ['Farhan Malik', 'B1', 76, 'BSH'],
    ['Gita Permata', 'A1', 88, 'BSB'],
    ['Hendra Kurnia', 'B2', 52, 'MB'],
];
$bsb = count(array_filter($students, fn($s) => $s[3] == 'BSB'));
$bsh = count(array_filter($students, fn($s) => $s[3] == 'BSH'));
$mb = count(array_filter($students, fn($s) => $s[3] == 'MB'));
$total = count($students);
@endphp

<div class="space-y-5">

    <!-- Summary Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="card p-4 bg-green-50 border border-green-200 text-center">
            <p class="text-2xl font-black text-green-700">{{ $bsb }}</p>
            <p class="text-xs text-green-600 font-semibold mt-0.5">Siswa BSB</p>
            <div class="mt-2 progress-track">
                <div class="progress-fill progress-green" style="width: {{ $bsb / $total * 100 }}%"></div>
            </div>
        </div>
        <div class="card p-4 bg-amber-50 border border-amber-200 text-center">
            <p class="text-2xl font-black text-amber-700">{{ $bsh }}</p>
            <p class="text-xs text-amber-600 font-semibold mt-0.5">Siswa BSH</p>
            <div class="mt-2 progress-track">
                <div class="progress-fill progress-yellow" style="width: {{ $bsh / $total * 100 }}%"></div>
            </div>
        </div>
        <div class="card p-4 bg-red-50 border border-red-200 text-center">
            <p class="text-2xl font-black text-red-700">{{ $mb }}</p>
            <p class="text-xs text-red-600 font-semibold mt-0.5">Siswa MB</p>
            <div class="mt-2 progress-track">
                <div class="progress-fill progress-red" style="width: {{ $mb / $total * 100 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Per kelas progress -->
    <div class="card p-5">
        <h3 class="font-bold text-gray-800 mb-4">Progress Penilaian per Kelas</h3>
        <div class="space-y-3">
            @foreach(['A1 - Bintang' => [22, 24], 'A2 - Bulan' => [18, 24], 'B1 - Matahari' => [18, 24], 'B2 - Awan' => [20, 24]] as $kelas => $data)
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-gray-700 w-28 flex-shrink-0">{{ $kelas }}</span>
                    <div class="flex-1">
                        <div class="progress-track">
                            <div class="progress-fill progress-green" style="width: {{ $data[0] / $data[1] * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-600 flex-shrink-0 w-16 text-right">{{ $data[0] }}/{{ $data[1] }} ({{ round($data[0]/$data[1]*100) }}%)</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Full table -->
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">Rekapitulasi Nilai Siswa</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-gray">Export Excel</button>
                <button class="btn btn-sm btn-red">Export PDF</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Skor Akhir</th>
                        <th>Visualisasi</th>
                        <th>Kategori</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $s)
                        <tr>
                            <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full {{ $s[3] == 'BSB' ? 'bg-green-100 text-green-700' : ($s[3] == 'BSH' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }} flex items-center justify-center text-[11px] font-black">
                                        {{ strtoupper(substr($s[0], 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-800">{{ $s[0] }}</span>
                                </div>
                            </td>
                            <td><span class="text-gray-500">{{ $s[1] }}</span></td>
                            <td><span class="font-black text-gray-800 text-sm">{{ $s[2] }}%</span></td>
                            <td>
                                <div class="w-24 progress-track">
                                    <div class="progress-fill {{ $s[3] == 'BSB' ? 'progress-green' : ($s[3] == 'BSH' ? 'progress-yellow' : 'progress-red') }}" style="width: {{ $s[2] }}%"></div>
                                </div>
                            </td>
                            <td><span class="badge badge-{{ strtolower($s[3]) }}">{{ $s[3] }}</span></td>
                            <td>
                                <span class="flex items-center gap-1.5 text-[11px] font-semibold {{ $s[3] !== 'MB' ? 'text-green-600' : 'text-red-500' }}">
                                    <div class="w-1.5 h-1.5 rounded-full bg-current"></div>
                                    {{ $s[3] !== 'MB' ? 'Terverifikasi' : 'Perlu Perhatian' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
