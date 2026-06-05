<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Wali Murid — TK Negeri Pembina Kota Padang Panjang</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --bg:           #F8F9F5;
            --surface:      #FFFFFF;
            --border:       #E8EBE3;
            --accent:       #84934A;
            --accent-dark:  #6A783D;
            --accent-lt:    #F1F4E9;
            --text-1:       #151914;
            --text-2:       #545F52;
            --text-3:       #8E998B;
        }

        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; 
            background: var(--bg); 
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
        }

        .form-input { 
            width: 100%; 
            padding: 0.875rem 1.125rem 0.875rem 1.125rem; 
            border: 1.5px solid var(--border); 
            border-radius: 14px; 
            font-size: 0.9375rem; 
            color: var(--text-1); 
            outline: none; 
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
            background: white;
        }
        
        .form-input-with-icon {
            padding-left: 3.25rem;
        }

        .form-input-password {
            padding-right: 3.25rem;
        }
        
        .form-input:focus { 
            border-color: var(--accent); 
            box-shadow: 0 0 0 4px var(--accent-lt);
            background: white;
        }

        .btn-sage {
            background: var(--accent);
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.9375rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-sage:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -6px rgba(132, 147, 74, 0.3);
        }

        .btn-sage:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Customize Scrollbar for the form container */
        .form-container::-webkit-scrollbar {
            width: 6px;
        }
        .form-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .form-container::-webkit-scrollbar-thumb {
            background-color: var(--border);
            border-radius: 10px;
        }

        @keyframes slowZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        .animate-slow-zoom {
            animation: slowZoom 20s ease-in-out infinite alternate;
        }
    </style>
</head>
<body class="h-screen overflow-hidden">

<div class="flex h-full">
    
    <!-- LEFT: Decorative Section -->
    <div class="hidden lg:flex lg:w-[45%] xl:w-[50%] relative p-4">
        <div class="relative w-full h-full overflow-hidden rounded-[32px]">
            <!-- Hero Background Image -->
            <div class="absolute inset-0 scale-110 animate-slow-zoom">
                <img src="{{ asset('images/TKPembina.jpg') }}"
                     alt="TK Pembina Background"
                     class="w-full h-full object-cover">
            </div>
            
            <!-- Premium Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#151914]/90 via-[#151914]/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#151914]/80 via-transparent to-transparent"></div>

            <!-- Decorative Elements -->
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-var(--accent)/20 blur-[100px] rounded-full"></div>

            <!-- Content Overlay -->
            <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 h-full">
                <!-- Logo Section -->
                <div class="flex items-center gap-6 group">
                    <div class="w-16 h-16 bg-white rounded-[1.5rem] flex items-center justify-center shadow-2xl shadow-black/20 overflow-hidden border-4 border-white/10 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                        <img src="{{ asset('images/logotutwuri.jpg') }}" alt="Logo" class="w-full h-full object-contain p-2">
                    </div>
                    <div>
                        <h3 class="text-white font-black text-2xl tracking-tighter leading-none mb-1.5">TK NEGERI PEMBINA</h3>
                        <p class="text-white/40 text-xs font-bold uppercase tracking-[0.2em]">Kota Padang Panjang</p>
                    </div>
                </div>

                <!-- Main Message -->
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white/90 text-[10px] font-bold uppercase tracking-widest mb-8 backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-var(--accent) animate-pulse"></span>
                        Akses Wali Murid
                    </div>
                    <h1 class="text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-6">
                        Mari Bergabung <br>
                        <span class="text-var(--accent)">Bersama Kami</span>
                    </h1>
                    <p class="text-white/70 text-lg leading-relaxed max-w-lg mb-8">
                        Daftarkan akun Anda untuk terhubung langsung dengan sistem pemantauan evaluasi harian dan tumbuh kembang anak Anda.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6">
                        @php
                            $features = [
                                ['Aman', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>', 'Data anak Anda terlindungi dengan sistem verifikasi.'],
                                ['Terpusat', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>', 'Akses seluruh informasi pendidikan anak dalam satu dashboard.']
                            ];
                        @endphp
                        @foreach($features as $item)
                            <div class="flex flex-col gap-2 p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <div class="w-10 h-10 rounded-xl bg-var(--accent)/20 flex items-center justify-center mb-1">
                                    {!! $item[1] !!}
                                </div>
                                <span class="text-white font-bold text-sm">{{ $item[0] }}</span>
                                <p class="text-white/50 text-[10px] leading-relaxed">{{ $item[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Text -->
                <div class="text-white/30 text-[10px] font-bold uppercase tracking-[0.2em]">
                    © 2026 TK Negeri Pembina Kota Padang Panjang
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Registration Form Section -->
    <div class="flex-1 flex flex-col items-center justify-center py-4 px-8 bg-var(--bg) relative overflow-hidden h-full">
        <!-- Background Decorations -->
        <div class="absolute top-[-20%] left-[-20%] w-[400px] h-[400px] bg-var(--accent)/5 blur-[80px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[300px] h-[300px] bg-var(--accent)/5 blur-[80px] rounded-full"></div>

        <div class="w-full max-w-[500px] relative z-10 flex flex-col h-full">
            <div class="flex justify-between items-center mb-6 pt-4">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-var(--text-3) uppercase tracking-widest hover:text-var(--accent) transition-colors group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                    Beranda
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-var(--text-3) uppercase tracking-widest hover:text-var(--accent) transition-colors group">
                    Sudah punya akun?
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Form Container -->
            <div class="bg-white p-8 md:p-10 rounded-[32px] shadow-[0_30px_70px_-20px_rgba(0,0,0,0.06)] border border-var(--border) flex-1 overflow-y-auto form-container pb-10">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex flex-col items-center mb-8">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-gray-100 shadow-lg">
                         <img src="{{ asset('images/logotutwuri.jpg') }}" alt="Logo" class="w-full h-full object-contain p-2">
                    </div>
                    <div class="text-center">
                        <h2 class="font-black text-var(--text-1) text-2xl tracking-tighter">TK Negeri Pembina</h2>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-black text-var(--text-1) mb-3 tracking-tight">Buat Akun Wali</h2>
                    <p class="text-sm text-var(--text-2) leading-relaxed">Silakan lengkapi formulir di bawah ini dengan data yang valid.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100">
                        <ul class="list-disc list-inside text-red-600 text-[12px] font-bold leading-relaxed">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- NISN Siswa -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">NISN Siswa</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-input @error('nisn') border-red-500 @enderror" placeholder="Masukkan NISN anak" required>
                            <p class="text-[10px] text-var(--text-3) mt-1.5 ml-1">*NISN digunakan untuk menghubungkan akun Anda dengan data anak.</p>
                        </div>

                        <!-- Kode Registrasi -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Kode Registrasi <span class="text-red-400">*</span></label>
                            <input type="text" name="kode_registrasi" value="{{ old('kode_registrasi') }}" class="form-input @error('kode_registrasi') border-red-500 @enderror" placeholder="Contoh: TKP-8H2K9" required style="text-transform: uppercase; letter-spacing: 0.05em;">
                            <p class="text-[10px] text-var(--text-3) mt-1.5 ml-1">*Kode registrasi diberikan oleh pihak sekolah saat mendaftarkan data anak.</p>
                            @error('kode_registrasi')
                                <p class="text-[10px] text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input @error('nama_lengkap') border-red-500 @enderror" placeholder="Nama lengkap Anda" required>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" class="form-input @error('username') border-red-500 @enderror" placeholder="Contoh: andi123" required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Email Aktif</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-input @error('email') border-red-500 @enderror" placeholder="email@contoh.com" required>
                        </div>

                        <!-- Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" class="form-input form-input-password @error('password') border-red-500 @enderror" placeholder="Minimal 8 karakter" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-var(--text-3) hover:text-var(--accent) transition-colors">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.13-5.247M9.75 9.75L14.25 14.25M9.75 14.25l4.5-4.5m-5.334 5.334l.006-.006m5.837-5.837l-.006.006M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div x-data="{ show: false }">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Ulangi Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" class="form-input form-input-password" placeholder="Ulangi password" required>
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-var(--text-3) hover:text-var(--accent) transition-colors">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.13-5.247M9.75 9.75L14.25 14.25M9.75 14.25l4.5-4.5m-5.334 5.334l.006-.006m5.837-5.837l-.006.006M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Nomor WhatsApp</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-input" placeholder="08xxxxxxxxxx">
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-input resize-none" placeholder="Alamat domisili Anda">{{ old('alamat') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn-sage mt-6">
                        <span>Daftar Akun</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </form>
            </div>
            
        </div>
    </div>

</div>

</body>
</html>
