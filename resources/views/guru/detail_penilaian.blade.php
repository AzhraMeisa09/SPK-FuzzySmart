@extends('layouts.app')
@section('title', 'Detail Penilaian Siswa')
@section('page-title', 'Detail Penilaian')

@section('content')
<div class="space-y-5">

    <!-- Breadcrumb / Back -->
    <div class="flex items-center gap-3">
        <a href="/guru/riwayat?role=guru" class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="font-black text-gray-900">Detail Penilaian: Aditya Pratama</h1>
            <p class="text-xs text-gray-500">Kelas B1 • Minggu 12 • Lingkunganku yang Bersih</p>
        </div>
        <div class="ml-auto flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-gray no-print">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="/guru/penilaian?role=guru" class="btn btn-sm btn-green no-print">Edit Nilai</a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card p-5">
        <div class="flex flex-wrap items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center text-green-700 font-black text-xl flex-shrink-0">A</div>
            <div class="flex-1">
                <h2 class="text-xl font-black text-gray-900">Aditya Pratama</h2>
                <p class="text-sm text-gray-500">NISN: 0012345678 • Kelas B1 (Matahari)</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Minggu</p>
                    <p class="font-black text-gray-800 text-sm">12</p>
                </div>
                <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Tanggal</p>
                    <p class="font-black text-gray-800 text-sm">14 Apr 2024</p>
                </div>
                <div class="text-center px-4 py-2 bg-green-50 rounded-xl border border-green-100">
                    <p class="text-xs text-green-600 font-medium">Kategori</p>
                    <p class="font-black text-green-700 text-sm">BSB</p>
                </div>
                <div class="text-center px-4 py-2 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Status</p>
                    <p class="font-black text-gray-800 text-sm">Final</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Distribution -->
        <div class="card p-5">
            <h3 class="font-bold text-gray-800 mb-4">Distribusi Capaian</h3>
            <div class="space-y-3">
                @php
                    $dists = [['BSB', 60, 'progress-green', 'badge-bsb'], ['BSH', 30, 'progress-yellow', 'badge-bsh'], ['MB', 10, 'progress-red', 'badge-mb']];
                @endphp
                @foreach($dists as $d)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="badge {{ $d[3] }}">{{ $d[0] }}</span>
                                <span class="text-xs text-gray-500 font-medium">{{ $d[0] == 'BSB' ? '3-4 subkriteria' : ($d[0] == 'BSH' ? '1-2 subkriteria' : '1 subkriteria') }}</span>
                            </div>
                            <span class="text-xs font-black text-gray-700">{{ $d[1] }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill {{ $d[2] }}" style="width: {{ $d[1] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Skor per kriteria -->
        <div class="card p-5">
            <h3 class="font-bold text-gray-800 mb-4">Skor Per Kriteria</h3>
            <div class="space-y-3">
                @php
                    $krits = [['K1 — Agama & Moral', 86, 'BSB'], ['K2 — Fisik Motorik', 72, 'BSH'], ['K3 — Kognitif', 90, 'BSB'], ['K5 — Sosial Emosional', 80, 'BSB']];
                @endphp
                @foreach($krits as $k)
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold text-gray-700 truncate">{{ $k[0] }}</span>
                                <span class="text-xs font-black text-gray-800 flex-shrink-0 ml-2">{{ $k[1] }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill {{ $k[2] == 'BSB' ? 'progress-green' : 'progress-yellow' }}" style="width: {{ $k[1] }}%"></div>
                            </div>
                        </div>
                        <span class="badge badge-{{ strtolower($k[2]) }} flex-shrink-0">{{ $k[2] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detail Subkriteria Table -->
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Detail Per Subkriteria</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Kode</th>
                        <th>Subkriteria</th>
                        <th>Capaian</th>
                        <th>Deskripsi Rubrik</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $details = [
                            ['Agama & Moral', 'S1.1', 'Berdoa sebelum makan', 'BSB', 'Siswa berdoa dengan lancar dan memimpin teman-temannya'],
                            ['Agama & Moral', 'S1.2', 'Menghormati orang lebih tua', 'BSB', 'Selalu menyapa, sangat sopan dan mencontohkan kepada teman'],
                            ['Fisik Motorik', 'S2.1', 'Keseimbangan tubuh', 'BSH', 'Dapat berdiri satu kaki 5 detik dengan sedikit kesulitan'],
                            ['Fisik Motorik', 'S2.2', 'Koordinasi tangan-mata', 'BSB', 'Mewarnai rapi dalam garis, pegang pensil dengan benar'],
                            ['Kognitif', 'S3.1', 'Mengenal warna & bentuk', 'BSB', 'Hafal 8 warna dan 5 bentuk geometri dengan tepat'],
                            ['Sosial Emosional', 'S5.1', 'Kerjasama kelompok', 'MB', 'Masih perlu dorongan untuk aktif berpartisipasi dalam grup'],
                        ];
                    @endphp
                    @foreach($details as $d)
                        <tr>
                            <td><span class="text-xs font-semibold text-gray-500">{{ $d[0] }}</span></td>
                            <td><span class="badge badge-blue">{{ $d[1] }}</span></td>
                            <td><span class="font-medium text-gray-800">{{ $d[2] }}</span></td>
                            <td><span class="badge badge-{{ strtolower($d[3]) }}">{{ $d[3] }}</span></td>
                            <td><span class="text-xs text-gray-500 italic">{{ $d[4] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Catatan Guru -->
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm">A</div>
            <div>
                <p class="text-sm font-bold text-gray-800">Ani Wijaya, S.Pd</p>
                <p class="text-xs text-gray-400">Wali Kelas B1 • 14 April 2024</p>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
            <p class="text-sm text-gray-700 leading-relaxed">
                "Aditya menunjukkan antusiasme yang sangat tinggi selama kegiatan belajar pada tema Lingkunganku. Ia sangat membantu teman-temannya membersihkan area bermain dan aktif bertanya tentang pentingnya menjaga kebersihan. Perlu perhatian pada aspek sosial emosional — Aditya masih terlihat menghindar dari kegiatan kelompok yang melibatkan banyak anak sekaligus."
            </p>
        </div>
    </div>

</div>
@endsection
