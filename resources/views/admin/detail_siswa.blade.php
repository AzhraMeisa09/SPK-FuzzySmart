@extends('layouts.app')
@section('title', 'Detail Siswa: ' . $siswa->nama)
@section('page-title', 'Profil Siswa')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-10">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-gray flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Siswa
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800">
        
        <!-- SECTION 1: PROFILE CARD -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                @if($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa" class="w-24 h-24 rounded-full object-cover shadow-inner ring-4 ring-blue-50/50 mb-4 bg-white">
                @else
                    <div class="w-24 h-24 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-black mb-4 shadow-inner ring-4 ring-blue-50/50">
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    </div>
                @endif
                
                <h2 class="text-xl font-bold text-gray-900 leading-tight mb-1">{{ $siswa->nama }}</h2>
                <p class="text-sm text-gray-500 mb-3 font-mono">NISN: {{ $siswa->kode ?? '-' }}</p>
                
                @if($siswa->kelas)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        Kelas {{ $siswa->kelas->nama_kelas }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-100">
                        Belum Ditempatkan
                    </span>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            
            <!-- SECTION 2: DATA SISWA -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Informasi Biodata
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Lahir</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">
                            {{ $siswa->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Alamat Lengkap</p>
                        <p class="font-medium text-gray-900">{{ $siswa->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DATA ORANG TUA / WALI -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Kontak & Orang Tua
                    </h3>
                    @if($siswa->waliMurid)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-widest border border-green-200">Terverifikasi Sistem</span>
                    @endif
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Wali Murid / Orang Tua</p>
                        <p class="font-medium text-gray-900">
                            @if($siswa->waliMurid)
                                {{ $siswa->waliMurid->nama_lengkap }}
                            @else
                                {{ $siswa->nama_orang_tua ?? '-' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nomor HP</p>
                        <p class="font-medium text-gray-900">
                            @if($siswa->waliMurid)
                                {{ $siswa->waliMurid->no_hp ?? '-' }}
                            @else
                                {{ $siswa->no_hp_orang_tua ?? '-' }}
                            @endif
                        </p>
                    </div>
                    @if($siswa->waliMurid && $siswa->waliMurid->email)
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Email Akun App Wali Murid</p>
                        <p class="font-medium text-gray-900">{{ $siswa->waliMurid->email }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
