<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — TK Negeri Pembina Kota Padang Panjang</title>
    
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
            padding: 0.875rem 1.125rem 0.875rem 3.25rem; 
            border: 1.5px solid var(--border); 
            border-radius: 14px; 
            font-size: 0.9375rem; 
            color: var(--text-1); 
            outline: none; 
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
            background: white;
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

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
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
    <div class="hidden lg:flex lg:w-[55%] xl:w-[60%] relative p-4">
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
            <div class="relative z-10 flex flex-col justify-between p-16 h-full">
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
                        Sistem Penilaian Berbasis Fuzzy SMART
                    </div>
                    <h1 class="text-5xl xl:text-6xl font-black text-white leading-[1.1] mb-8">
                        Pantau Tumbuh Kembang <br>
                        <span class="text-var(--accent)">Anak Anda</span>
                    </h1>
                    <p class="text-white/70 text-lg leading-relaxed max-w-lg mb-12">
                        Masuk ke platform digital terpadu untuk mendokumentasikan dan menganalisis capaian perkembangan harian putra-putri Anda secara objektif.
                    </p>
                    
                    <div class="grid grid-cols-3 gap-8">
                        @php
                            $loginFeatures = [
                                ['Pencatatan', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>'],
                                ['Pemantauan', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>'],
                                ['Laporan', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>']
                            ];
                        @endphp
                        @foreach($loginFeatures as $item)
                            <div class="flex flex-col gap-3">
                                <div>{!! $item[1] !!}</div>
                                <span class="text-white/60 text-[10px] font-bold uppercase tracking-wider">{{ $item[0] }}</span>
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

    <!-- RIGHT: Login Form Section -->
    <div class="flex-1 flex flex-col items-center justify-center p-8 bg-var(--bg) relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute top-[-20%] left-[-20%] w-[400px] h-[400px] bg-var(--accent)/5 blur-[80px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[300px] h-[300px] bg-var(--accent)/5 blur-[80px] rounded-full"></div>

        <div class="w-full max-w-[400px] relative z-10">
            <!-- Back to Landing Link -->
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-[11px] font-black text-var(--text-3) uppercase tracking-widest hover:text-var(--accent) transition-colors mb-8 group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Beranda
            </a>
            <!-- Form Container -->
            <div class="bg-white p-10 rounded-[32px] shadow-[0_30px_70px_-20px_rgba(0,0,0,0.06)] border border-var(--border)">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex flex-col items-center mb-10">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-5 overflow-hidden border border-gray-100 shadow-lg">
                         <img src="{{ asset('images/logotutwuri.jpg') }}" alt="Logo" class="w-full h-full object-contain p-2">
                    </div>
                    <div class="text-center">
                        <h2 class="font-black text-var(--text-1) text-2xl tracking-tighter">TK Negeri Pembina</h2>
                        <p class="text-[10px] font-black text-var(--text-3) uppercase tracking-[0.2em] mt-1">Kota Padang Panjang</p>
                    </div>
                </div>

                <div class="mb-10">
                    <h2 class="text-3xl font-black text-var(--text-1) mb-3 tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-var(--text-2) leading-relaxed">Silakan masuk untuk mengakses dashboard penilaian Anda.</p>
                </div>

                <form id="loginForm" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest mb-2.5 ml-1">Email / Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-var(--text-3)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" id="email" name="email" class="form-input" placeholder="admin@gmail.com" required>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2.5 ml-1">
                            <label class="block text-[11px] font-black text-var(--text-3) uppercase tracking-widest">Password</label>
                            <a href="https://wa.me/6282284930275?text=Halo%20Admin%2C%20saya%20lupa%20kata%20sandi%20untuk%20akun%20SPK%20TK%20Negeri%20Pembina.%20Mohon%20bantuannya." 
                               target="_blank"
                               class="text-[11px] text-var(--accent) font-bold hover:text-var(--accent-dark) transition-colors">Lupa sandi?</a>
                        </div>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-var(--text-3)" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-input form-input-password" placeholder="••••••••" required>
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-var(--text-3) hover:text-var(--accent) transition-colors">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 014.13-5.247M9.75 9.75L14.25 14.25M9.75 14.25l4.5-4.5m-5.334 5.334l.006-.006m5.837-5.837l-.006.006M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="errorMessage" class="hidden p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[11px] font-bold leading-relaxed"></div>

                    <div class="flex items-center gap-3 ml-1">
                        <input type="checkbox" id="remember" class="w-5 h-5 rounded-lg border-var(--border) text-var(--accent) focus:ring-var(--accent-lt) transition-all">
                        <label for="remember" class="text-sm font-medium text-var(--text-2)">Biarkan saya tetap masuk</label>
                    </div>

                    <button type="submit" id="submitBtn" class="btn-sage">
                        <span>Masuk Sekarang</span>
                        <svg id="loadingIcon" class="hidden w-5 h-5 animate-spin" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg id="arrowIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm font-bold text-var(--text-2) mb-4">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-var(--accent) hover:text-var(--accent-dark) underline decoration-2 underline-offset-4 transition-colors">Daftar Wali Murid</a>
                </p>
                <p class="text-[11px] font-bold text-var(--text-3) uppercase tracking-widest">
                    Butuh bantuan sistem? <a href="#" class="text-var(--accent) hover:text-var(--accent-dark) transition-colors">Hubungi Admin</a>
                </p>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorBox = document.getElementById('errorMessage');
        const submitBtn = document.getElementById('submitBtn');
        const loadingIcon = document.getElementById('loadingIcon');
        const arrowIcon = document.getElementById('arrowIcon');

        // Reset state
        errorBox.classList.add('hidden');
        submitBtn.disabled = true;
        loadingIcon.classList.remove('hidden');
        arrowIcon.classList.add('hidden');

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (response.ok) {
                // Store token and user data
                localStorage.setItem('auth_token', result.data.token);
                localStorage.setItem('user_data', JSON.stringify(result.data.user));

                // Redirect based on role
                const role = result.data.user.role;
                let redirectUrl = '/admin/dashboard';
                if (role === 'guru') redirectUrl = '/guru/dashboard';
                if (role === 'kepala_sekolah') redirectUrl = '/kepsek/dashboard';
                if (role === 'wali_murid') redirectUrl = '/wali/dashboard';

                window.location.href = `${redirectUrl}?role=${role}`;
            } else {
                errorBox.textContent = result.message || 'Login gagal. Silakan periksa kembali email dan password Anda.';
                errorBox.classList.remove('hidden');
            }
        } catch (error) {
            errorBox.textContent = 'Terjadi kesalahan sistem. Silakan coba lagi nanti.';
            errorBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            loadingIcon.classList.add('hidden');
            arrowIcon.classList.remove('hidden');
        }
    });
</script>

</body>
</html>
