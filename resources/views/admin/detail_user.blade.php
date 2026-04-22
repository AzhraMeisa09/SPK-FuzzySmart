@extends('layouts.app')
@section('title', 'Detail User: ' . $user->nama_lengkap)
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-10">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.user.index') }}" class="btn btn-gray flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen User
        </a>
        <form action="{{ route('admin.user.toggle-status', $user->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn {{ $user->is_active ? 'btn-red' : 'btn-green' }} flex items-center gap-2" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                @if($user->is_active)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Nonaktifkan Akses
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aktifkan Akses
                @endif
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800">
        
        <!-- SECTION 1: PROFILE CARD -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                @if($user->foto_profil)
                    <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="w-32 h-32 rounded-full object-cover shadow-inner ring-4 ring-green-50/50 mb-4 bg-white">
                @else
                    <div class="w-32 h-32 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-5xl font-black mb-4 shadow-inner ring-4 ring-green-50/50">
                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                    </div>
                @endif
                
                <h2 class="text-xl font-bold text-gray-900 leading-tight mb-1">{{ $user->nama_lengkap }}</h2>
                <p class="text-sm text-gray-500 mb-2 font-mono">{{ '@' . $user->username }}</p>
                <div class="flex gap-2 items-center justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role == 'guru' ? 'bg-blue-100 text-blue-700' : ($user->role == 'kepala_sekolah' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600')) }}">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                    @if($user->is_active)
                        <span class="inline-flex items-center w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse" title="Akun Aktif"></span>
                    @else
                        <span class="inline-flex items-center w-2.5 h-2.5 rounded-full bg-red-500" title="Akun Nonaktif"></span>
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm">Aktivitas & Log</h3>
                </div>
                <div class="p-5">
                    <p class="text-xs text-gray-500 mb-1">Terdaftar Sejak</p>
                    <p class="text-sm font-medium text-gray-800 mb-3">{{ $user->created_at->translatedFormat('d F Y (H:i)') }}</p>
                    
                    <p class="text-xs text-gray-500 mb-1">Terakhir Diubah</p>
                    <p class="text-sm font-medium text-gray-800">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            
            <!-- SECTION 2: BIODATA & KONTAK -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Biodata & Informasi Kontak
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-6">
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat Surel (Email)</p>
                        <p class="font-medium text-gray-900 border border-gray-100 bg-gray-50 rounded-lg p-3">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nomor Telepon / HP</p>
                        <p class="font-medium text-gray-900 border border-gray-100 bg-white rounded-lg p-3">{{ $user->no_hp ?? 'Belum diatur' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Role Kedudukan</p>
                        <p class="font-medium text-gray-900 border border-gray-100 bg-white rounded-lg p-3">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Alamat Tinggal Lengkap</p>
                        <p class="font-medium text-gray-900 border border-gray-100 bg-white rounded-lg p-4 min-h-[5rem]">{{ $user->alamat ?? 'Belum ada data alamat yang dimasukkan.' }}</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: ROLE SPECIFIC DATA -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-blue-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Informasi Terkait Portofolio / Sistem
                    </h3>
                </div>
                <div class="p-6">
                    @if($user->role == 'guru')
                        <p class="text-sm text-gray-600 mb-2">Akun ini memiliki kewenangan operasional sebagai Tenaga Pendidik / Guru. Modul penilaian, daftar kelas yang diampu, serta riwayat portofolio akan terhubung dengan akun ini.</p>
                        <ul class="list-disc pl-5 text-sm text-gray-700 bg-gray-50 border border-gray-100 rounded-lg p-4">
                            <li>Dapat mengakses dashboard penilaian spesifik kelas</li>
                            <li>Kewenangan unggah portofolio pembelajaran anak</li>
                        </ul>
                    @elseif($user->role == 'wali_murid')
                        <p class="text-sm text-gray-600 mb-2">Akun ini bertindak sebagai Wali Murid / Orang Tua Siswa.</p>
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-800">Digunakan untuk melihat raport perkembangan anak secara live. Jika ditautkan melalui Modul Siswa, data progres anak otomatis akan meluncur ke aplikasi Wali ini.</p>
                        </div>
                    @elseif($user->role == 'admin')
                        <p class="text-sm text-gray-600">Akun dengan Hak Akses Mutlak (Super Admin). Dapat memanipulasi master data, mengatur konfigurasi sekolah, serta memberhentikan/mengaktifkan akun pengguna lain.</p>
                    @else
                        <p class="text-sm text-gray-600">Akun ini diklasifikasikan sebagai Pimpinan (Kepala Sekolah). Dapat meninjau sekujur modul analitik serta mengesahkan lembar laporan evaluasi bulanan/tahunan secara eksklusif.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
