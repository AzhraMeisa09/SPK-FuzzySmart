<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SPK TK Pembina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px; color: #111; outline: none; transition: border-color 0.15s, box-shadow 0.15s; font-family: inherit; }
        .form-input:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.12); }
    </style>
</head>
<body class="bg-gray-50 h-screen overflow-hidden" style="font-family: 'Inter', sans-serif;">

<div class="flex h-full">
    
    <!-- LEFT: School Image -->
    <div class="hidden lg:flex lg:w-[58%] xl:w-[60%] relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1580582855239-49896796cbe4?q=80&w=2000&auto=format&fit=crop"
             alt="TK Pembina"
             class="absolute inset-0 w-full h-full object-cover">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-green-900/80 via-black/60 to-black/70"></div>

        <!-- Content overlay -->
        <div class="relative z-10 flex flex-col justify-between p-12 h-full">
            <!-- Logo top -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">TK Pembina</p>
                    <p class="text-white/60 text-xs">Negeri Teladan Jakarta</p>
                </div>
            </div>

            <!-- Center text -->
            <div>
                <div class="inline-block px-3 py-1 rounded-full bg-green-500/20 border border-green-400/30 text-green-300 text-xs font-semibold mb-6">
                    ✦ Sistem Informasi Penilaian
                </div>
                <h1 class="text-4xl xl:text-5xl font-black text-white leading-tight mb-4">
                    Sistem Penilaian<br>
                    <span class="text-green-400">Perkembangan</span><br>
                    Siswa
                </h1>
                <p class="text-white/70 text-base leading-relaxed max-w-md">
                    Platform digital terpadu untuk monitoring dan evaluasi perkembangan anak usia dini berbasis metode Fuzzy SMART yang objektif dan transparan.
                </p>
                
                <div class="flex gap-6 mt-8">
                    @foreach(['Kognitif', 'Afektif', 'Psikomotorik'] as $aspek)
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                            <span class="text-white/60 text-sm font-medium">{{ $aspek }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer left -->
            <div class="text-white/40 text-xs">
                © 2024 TK Pembina — Kementerian Pendidikan RI
            </div>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 bg-white">
        <div class="w-full max-w-sm">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">TK Pembina</p>
                    <p class="text-xs text-gray-400">Sistem Penilaian Siswa</p>
                </div>
            </div>

            <h2 class="text-2xl font-black text-gray-900 mb-1">Selamat Datang</h2>
            <p class="text-sm text-gray-500 mb-8">Masuk untuk mengakses dashboard Anda</p>

            <form id="loginForm" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email / Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" id="email" name="email" class="form-input" style="padding-left: 40px;" placeholder="admin@gmail.com" required>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700">Password</label>
                        <a href="#" class="text-xs text-green-600 font-semibold hover:underline">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" class="form-input" style="padding-left: 40px;" placeholder="••••••••" required>
                    </div>
                </div>

                <div id="errorMessage" class="hidden p-3 rounded-lg bg-red-50 border border-red-100 text-red-600 text-xs font-semibold"></div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" class="w-4 h-4 rounded text-green-600 border-gray-300 focus:ring-green-500">
                    <label for="remember" class="text-sm text-gray-600">Ingat saya selama 30 hari</label>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl transition-all hover:shadow-lg hover:shadow-green-200 active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <svg id="loadingIcon" class="hidden w-4 h-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <script>
                document.getElementById('loginForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const errorBox = document.getElementById('errorMessage');
                    const submitBtn = document.getElementById('submitBtn');
                    const loadingIcon = document.getElementById('loadingIcon');

                    // Reset state
                    errorBox.classList.add('hidden');
                    submitBtn.disabled = true;
                    loadingIcon.classList.remove('hidden');

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
                    }
                });
            </script>

            <p class="text-center text-xs text-gray-400 mt-6">
                Butuh bantuan? <a href="#" class="text-green-600 font-semibold hover:underline">Hubungi Admin</a>
            </p>
        </div>
    </div>

</div>
</body>
</html>
