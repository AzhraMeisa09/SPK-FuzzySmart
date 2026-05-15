@extends('layouts.app')
@section('title', 'Detail User: ' . $user->nama_lengkap)
@section('page-title', 'Profil Pengguna')

@section('content')
<div class="space-y-6 pb-12">

    <!-- Header Actions & Navigation -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ route('admin.user.index') }}" class="btn btn-gray bg-white shadow-sm hover:shadow transition-all flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-var(--text-2)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Manajemen User
        </a>
        
        <form action="{{ route('admin.user.toggle', $user) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" 
                    class="btn {{ $user->is_active ? 'btn-red' : 'btn-green' }} shadow-md hover:shadow-lg transition-all flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold" 
                    {{ auth()->user()->id_user == $user->id_user ? 'disabled' : '' }}>
                @if($user->is_active)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Nonaktifkan Akses
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aktifkan Akses Akun
                @endif
            </button>
        </form>
    </div>

    <!-- Main Profile Hero Section -->
    <div class="card overflow-hidden border-none shadow-xl">
        <div class="h-32 w-full" style="background: linear-gradient(135deg, #84934A 0%, #A3B18A 100%);"></div>
        <div class="px-8 pb-8">
            <div class="relative flex flex-col md:flex-row items-center md:items-end gap-6 -mt-12">
                <div class="relative">
                    @if($user->foto_profil)
                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-2xl bg-white">
                    @else
                        <div class="w-32 h-32 rounded-3xl bg-white border-4 border-white shadow-2xl flex items-center justify-center text-4xl font-bold text-var(--accent)">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-2 right-2 w-5 h-5 rounded-full border-4 border-white {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                </div>
                
                <div class="flex-1 text-center md:text-left mb-2">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-1">
                        <h2 class="text-3xl font-bold text-var(--text-1) tracking-tight">{{ $user->nama_lengkap }}</h2>
                        <span class="px-3 py-1 rounded-lg text-[10px] font-bold {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role == 'guru' ? 'bg-blue-100 text-blue-700' : ($user->role == 'kepala_sekolah' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>
                    <p class="text-var(--text-3) font-medium text-sm flex items-center justify-center md:justify-start gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        ID Pengguna: {{ $user->username }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- SIDEBAR INFO -->
        <div class="lg:col-span-4 space-y-6">
            <!-- ACCOUNT STATUS -->
            <div class="card p-6">
                <h4 class="text-[11px] font-bold text-var(--text-3) mb-4">Detail Akun</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-var(--text-2) font-medium">Status Akun</span>
                        <span class="text-xs font-bold {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->is_active ? 'Aktif' : 'Dinonaktifkan' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-var(--text-2) font-medium">Terdaftar Sejak</span>
                        <span class="text-xs font-bold text-var(--text-1)">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-var(--text-2) font-medium">Update Terakhir</span>
                        <span class="text-xs font-bold text-var(--text-1)">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- SECURITY TIP -->
            <div class="card p-6 border-none text-white shadow-lg" style="background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h4 class="text-xs font-bold mb-2">Keamanan Sistem</h4>
                <p class="text-[11px] leading-relaxed opacity-80 font-medium">
                    Pastikan setiap perubahan data pangguna didokumentasikan dengan benar. Pemberian hak akses pimpinan harus melalui verifikasi Kepala Sekolah.
                </p>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- BIODATA -->
            <div class="card overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-var(--text-1) flex items-center gap-2">
                        <svg class="w-4 h-4 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        Informasi Biodata & Kontak
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Alamat Email</label>
                            <div class="px-4 py-3 bg-var(--bg) rounded-xl border border-var(--border) text-sm font-bold text-var(--text-1)">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Nomor HP / WhatsApp</label>
                            <div class="px-4 py-3 bg-white rounded-xl border border-var(--border) text-sm font-bold text-var(--text-1)">
                                {{ $user->no_hp ?? 'Belum ada data' }}
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-bold text-var(--text-3) mb-1.5 block">Alamat Tempat Tinggal</label>
                            <div class="px-4 py-4 bg-white rounded-xl border border-var(--border) text-sm font-medium text-var(--text-2) leading-relaxed min-h-[80px]">
                                {{ $user->alamat ?? 'Data alamat tinggal belum dilengkapi oleh admin atau pimpinan.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERMISSION DESCRIPTION -->
            <div class="card p-8 bg-var(--accent-lt) border-var(--accent)/20 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-var(--accent)/5 rounded-full"></div>
                <div class="relative">
                    <h4 class="text-sm font-bold text-var(--accent) mb-4">Kewenangan & Hak Akses</h4>
                    
                    @if($user->role == 'guru')
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-var(--text-1) mb-1">Tenaga Pendidik / Guru</p>
                                <p class="text-xs text-var(--text-2) leading-relaxed">Dapat mengelola daftar siswa, melakukan penilaian perkembangan mingguan, serta mengunggah bukti portofolio pembelajaran.</p>
                            </div>
                        </div>
                    @elseif($user->role == 'admin')
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-var(--text-1) mb-1">Administrator Sistem</p>
                                <p class="text-xs text-var(--text-2) leading-relaxed">Memiliki akses penuh untuk mengelola master data (Tahun Ajaran, Kriteria, User) dan konfigurasi sistem secara menyeluruh.</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-var(--text-1) mb-1">Pimpinan / Kepala Sekolah</p>
                                <p class="text-xs text-var(--text-2) leading-relaxed">Berhak meninjau seluruh laporan analitik sekolah, memantau kinerja pendidik, dan mengesahkan hasil evaluasi akhir siswa.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
