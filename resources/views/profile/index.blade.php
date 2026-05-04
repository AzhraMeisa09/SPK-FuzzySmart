@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil saya')

@section('content')
<div class="w-full space-y-5">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        {{-- ── SIDEBAR PROFIL ── --}}
        <div class="space-y-5 lg:col-span-1">
            
            {{-- Avatar Card --}}
            <div class="card p-6 flex flex-col items-center text-center">
                <div class="relative group mb-5">
                    {{-- Avatar --}}
                    <div class="w-28 h-28 rounded-2xl overflow-hidden flex items-center justify-center font-black text-3xl" style="background: var(--accent-lt); color: var(--accent);">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        @endif
                    </div>
                    {{-- Tombol Kamera --}}
                    <label for="foto_profil_input"
                           class="absolute -bottom-2 -right-2 w-9 h-9 flex items-center justify-center bg-white rounded-xl shadow-md cursor-pointer hover:scale-110 active:scale-95 transition-all"
                           style="border: 2px solid var(--border);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-1);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </label>
                </div>

                <h3 class="text-base font-semibold tracking-tight" style="color: var(--text-1);">{{ $user->nama_lengkap }}</h3>
                <p class="text-xs font-medium mt-1 capitalize" style="color: var(--text-3);">{{ str_replace('_', ' ', $user->role) }}</p>
                
                <div class="w-full mt-5 pt-5 space-y-3" style="border-top: 1px solid var(--border);">
                    <div class="flex items-center justify-between text-xs">
                        <span style="color: var(--text-3);">Status akun</span>
                        <span class="badge badge-aktif">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span style="color: var(--text-3);">Username</span>
                        <span class="font-semibold" style="color: var(--text-1);">{{ $user->username }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold" style="color: var(--text-1);">Panduan foto profil</p>
                        <p class="text-[11px] leading-relaxed mt-1" style="color: var(--text-3);">Klik ikon kamera di bawah foto untuk memperbarui foto profil secara instan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── MAIN CONTENT ── --}}
        <div class="lg:col-span-2 space-y-5">
            
            {{-- Form Informasi Profil --}}
            <div class="card overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3" style="border-bottom: 1px solid var(--border); background: var(--bg);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Informasi dasar</h3>
                        <p class="text-[11px]" style="color: var(--text-3);">Kelola identitas dan alamat email Anda.</p>
                    </div>
                </div>
                
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-5">
                    @csrf
                    @method('PATCH')
                    
                    {{-- Hidden Input Foto --}}
                    <input type="file" id="foto_profil_input" name="foto_profil" class="hidden" accept="image/*" onchange="document.getElementById('profileForm').submit()">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group !mb-0">
                            <label class="form-label">Nama lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" class="form-input" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group !mb-0">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-input" placeholder="Username">
                        </div>
                        <div class="form-group md:col-span-2 !mb-0">
                            <label class="form-label">Alamat email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" placeholder="Alamat email">
                        </div>
                    </div>

                    <div class="mt-5 pt-5 flex justify-end" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="btn btn-green px-8">
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form Ubah Password --}}
            <div class="card overflow-hidden">
                <div class="px-5 py-4 flex items-center gap-3" style="border-bottom: 1px solid var(--border); background: var(--bg);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent-lt); color: var(--accent);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold" style="color: var(--text-1);">Keamanan & password</h3>
                        <p class="text-[11px]" style="color: var(--text-3);">Perbarui kata sandi Anda secara berkala untuk keamanan.</p>
                    </div>
                </div>
                
                <form action="{{ route('profile.password') }}" method="POST" class="p-5">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-4">
                        <div class="form-group !mb-0 max-w-sm">
                            <label class="form-label">Password saat ini</label>
                            <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group !mb-0">
                                <label class="form-label">Password baru</label>
                                <input type="password" name="password" class="form-input" placeholder="••••••••">
                            </div>
                            <div class="form-group !mb-0">
                                <label class="form-label">Konfirmasi password baru</label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 flex justify-end" style="border-top: 1px solid var(--border);">
                        <button type="submit" class="btn btn-blue px-8">
                            Perbarui password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
