<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Evaluasi Perkembangan Siswa — TK Negeri Pembina Kota Padang Panjang</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logotutwuri.jpg') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js Plugins MUST come before the core -->
    <script defer src="https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
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
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .container-custom {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .glass-nav {
            background: rgba(248, 249, 245, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(132, 147, 74, 0.1);
        }

        .btn-sage {
            background: var(--accent);
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            border: none;
            cursor: pointer;
        }

        .btn-sage:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(132, 147, 74, 0.4);
        }

        .btn-outline {
            border: 2px solid var(--accent);
            color: var(--accent);
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: var(--accent-lt);
            transform: translateY(-2px);
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .feature-card:hover {
            border-color: var(--accent);
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.08);
        }

        .step-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: var(--accent-lt);
            color: var(--accent);
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.125rem;
        }

        .math-formula {
            background: #1B211A;
            color: #C5CF8E;
            padding: 1.5rem;
            border-radius: 16px;
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Animations */
        [x-cloak] { display: none !important; }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-shape {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(132, 147, 74, 0.12) 0%, rgba(245, 245, 240, 0) 70%);
            z-index: -1;
            border-radius: 50%;
            filter: blur(60px);
        }

        .nav-link {
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-2);
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--accent);
        }

        .stat-item {
            position: relative;
            padding: 2.5rem;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.08);
            transition: background 0.3s;
        }

        .stat-item:hover {
            background: rgba(255,255,255,0.03);
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: rgba(132, 147, 74, 0.1);
            color: var(--accent);
            border-radius: 18px;
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }

        .stat-item:hover .stat-icon {
            transform: translateY(-5px) scale(1.05);
            background: var(--accent);
            color: white;
        }

        @media (max-width: 1024px) {
            .stat-item {
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }
            .stat-item:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body x-data="{ 
    scrolled: false,
    reveal() {
        const reveals = document.querySelectorAll('.reveal');
        reveals.forEach(el => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            const elementVisible = 80;
            if (elementTop < windowHeight - elementVisible) {
                el.classList.add('active');
            }
        });
    }
}" @scroll.window="scrolled = (window.pageYOffset > 50); reveal()" x-init="reveal()">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" 
         :class="scrolled ? 'glass-nav py-3 shadow-lg' : 'py-6'">
        <div class="container-custom flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-11 h-11 bg-var(--accent) rounded-xl flex items-center justify-center shadow-xl shadow-accent/20 transition-transform group-hover:scale-105 overflow-hidden">
                    <img src="{{ asset('images/logotutwuri.jpg') }}" 
                        alt="Logo TK" 
                        class="w-full h-full object-contain p-1.5">
                </div>
                <div class="leading-tight">
                    <span class="block text-base font-extrabold tracking-tight text-var(--text-1)">TK NEGERI <span class="text-var(--accent)">PEMBINA</span></span>
                    <span class="block text-[10px] font-bold text-var(--text-3) uppercase tracking-widest">Sistem Penilaian Siswa</span>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-10">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#tentang" class="nav-link">Tentang</a>
                <a href="#fitur" class="nav-link">Fitur</a>
                <a href="#alur" class="nav-link">Alur</a>
                <a href="#metode" class="nav-link">Metode</a>
            </div>

            <div class="flex items-center gap-4">
                <a href="/login" class="btn-sage">Masuk</a>
                <a href="{{ route('register') }}" class="btn-outline hidden md:inline-flex bg-white/50">Daftar Wali</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-48 pb-24 lg:pt-56 lg:pb-32 overflow-hidden">
        <div class="hero-shape"></div>
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="reveal active">
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-var(--accent-lt) border border-var(--accent)/20 text-var(--accent) text-[11px] font-extrabold uppercase tracking-[0.15em] mb-8">
                        Sistem Penilaian Berbasis Fuzzy SMART
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black text-var(--text-1) leading-[1.05] mb-8">
                        Penilaian Tumbuh Kembang Siswa <br>
                        <span class="text-var(--accent)">Lebih Mudah</span> & <br>
                        <span>Lebih Objektif.</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-var(--text-2) leading-relaxed mb-12 max-w-xl">
                        Membantu bapak/ibu guru di TK Negeri Pembina Kota Padang Panjang dalam mencatat dan memantau perkembangan harian siswa secara tepat dan transparan.
                    </p>
                    <div class="flex flex-wrap gap-5">
                        <a href="/login" class="btn-sage text-base px-10 py-4">
                            Mulai Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#tentang" class="btn-outline text-base px-10 py-4">Pelajari Lebih Lanjut</a>
                    </div>
                </div>

                <div class="relative reveal active" style="transition-delay: 200ms">
                    <div class="relative z-10 bg-white p-5 rounded-[40px] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.12)] border border-var(--border)">
                        <div class="rounded-[32px] overflow-hidden">
                            <img src="{{ asset('images/TKPembina.jpg') }}" 
                                alt="Kegiatan Belajar" 
                                class="w-full h-auto object-cover aspect-[4/3] transform transition-transform duration-700 hover:scale-105">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Sistem -->
    <section id="tentang" class="py-32 bg-white">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto text-center reveal">
                <h2 class="text-sm font-black text-var(--accent) uppercase tracking-[0.25em] mb-6">Tentang Aplikasi</h2>
                <h3 class="text-4xl lg:text-5xl font-black text-var(--text-1) mb-10 leading-tight">Mendigitalisasi Proses Penilaian Siswa secara Profesional.</h3>
                <p class="text-lg lg:text-xl text-var(--text-2) mb-8 leading-relaxed">
                    Aplikasi ini dikembangkan untuk memudahkan pendokumentasian perkembangan peserta didik. Dengan bantuan perhitungan Fuzzy SMART, setiap indikator penilaian diolah secara sistematis untuk menghasilkan laporan perkembangan yang akurat.
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 mt-16">
                    <div class="p-6 rounded-2xl bg-var(--bg) border border-var(--border)">
                        <p class="text-3xl font-black text-var(--accent) mb-2">Pencatatan</p>
                        <p class="text-xs font-bold text-var(--text-3) uppercase">Mingguan</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-var(--bg) border border-var(--border)">
                        <p class="text-3xl font-black text-var(--accent) mb-2">Pemantauan</p>
                        <p class="text-xs font-bold text-var(--text-3) uppercase">Orang Tua</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-var(--bg) border border-var(--border) col-span-2 md:col-span-1">
                        <p class="text-3xl font-black text-var(--accent) mb-2">Laporan</p>
                        <p class="text-xs font-bold text-var(--text-3) uppercase">Digital (PDF)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Utama -->
    <section id="fitur" class="py-32">
        <div class="container-custom">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8 reveal">
                <div class="max-w-2xl">
                    <h2 class="text-sm font-black text-var(--accent) uppercase tracking-[0.25em] mb-4 text-left">Fitur Unggulan</h2>
                    <h3 class="text-4xl lg:text-5xl font-black text-var(--text-1)">Kemudahan Pengelolaan Data dalam Satu Tempat.</h3>
                </div>
                <p class="text-var(--text-2) max-w-sm lg:text-right">Mendukung seluruh tahapan penilaian mulai dari input harian hingga cetak rapor akhir semester.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        ['Penilaian Mingguan', 'Guru dapat mencatat perkembangan siswa berdasarkan indikator pencapaian setiap minggu.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path></svg>'],
                        ['Perhitungan Otomatis', 'Sistem mengolah nilai deskriptif menjadi data numerik yang akurat.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'],
                        ['Grafik Perkembangan', 'Melihat tren kenaikan atau penurunan capaian siswa di setiap aspek.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>'],
                        ['Saran Perkembangan', 'Menampilkan saran otomatis untuk membantu orang tua memahami kebutuhan anak.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'],
                        ['Galeri Portofolio', 'Simpan bukti karya dan aktivitas siswa sebagai bukti fisik perkembangan.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'],
                        ['Rapor Digital', 'Cetak laporan hasil evaluasi periode secara otomatis tanpa perlu rekap manual.', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-var(--text-1)"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>']
                    ];
                @endphp

                @foreach($features as $index => $f)
                <div class="reveal" style="transition-delay: {{ $index * 100 }}ms">
                    <div class="feature-card">
                        <div class="mb-8">
                            {!! $f[2] !!}
                        </div>
                        <h4 class="text-xl font-extrabold mb-4 text-var(--text-1)">{{ $f[0] }}</h4>
                        <p class="text-sm text-var(--text-2) leading-relaxed">{{ $f[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Alur Sistem -->
    <section id="alur" class="py-32 bg-[#151914] text-white">
        <div class="container-custom">
            <div class="text-center mb-24 reveal">
                <h2 class="text-sm font-black text-var(--accent) uppercase tracking-[0.25em] mb-4">Langkah Kerja</h2>
                <h3 class="text-4xl lg:text-5xl font-black">Proses Penilaian yang Terorganisir.</h3>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-y-16 gap-x-12 reveal">
                @php
                    $steps = [
                        ['Penginputan Nilai', 'Bapak/Ibu Guru memasukkan hasil pengamatan harian siswa ke sistem.'],
                        ['Pengolahan Data', 'Sistem mengubah nilai deskriptif menjadi angka pasti untuk dihitung.'],
                        ['Verifikasi Admin', 'Pihak admin memverifikasi kelengkapan data sebelum perhitungan final.'],
                        ['Kalkulasi Akhir', 'Proses pembobotan pada setiap aspek kriteria untuk mendapatkan peringkat.'],
                        ['Pemberian Saran', 'Sistem mencocokkan hasil evaluasi dengan saran perkembangan yang sesuai.'],
                        ['Penyajian Laporan', 'Laporan hasil perkembangan dapat diakses oleh orang tua dan kepala sekolah.']
                    ];
                @endphp

                @foreach($steps as $i => $s)
                <div class="flex gap-6 group">
                    <div class="step-pill group-hover:bg-var(--accent) group-hover:text-white transition-colors">{{ $i + 1 }}</div>
                    <div>
                        <h5 class="text-lg font-bold mb-2 group-hover:text-var(--accent) transition-colors">{{ $s[0] }}</h5>
                        <p class="text-sm text-white/50 leading-relaxed">{{ $s[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Role Pengguna -->
    <section id="pengguna" class="py-32 bg-white">
        <div class="container-custom">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="reveal">
                    <h2 class="text-sm font-black text-var(--accent) uppercase tracking-[0.25em] mb-4">Akses Pengguna</h2>
                    <h3 class="text-4xl lg:text-5xl font-black text-var(--text-1) mb-8 leading-tight">Satu Sistem untuk Seluruh Pihak Sekolah.</h3>
                    <p class="text-lg text-var(--text-2) mb-12">Setiap pengguna mendapatkan hak akses yang berbeda sesuai dengan perannya dalam proses pendidikan.</p>
                    
                    <div class="space-y-6">
                        @php
                            $roles = [
                                ['Admin Sekolah', 'Mengelola data utama, kriteria penilaian, dan periode tahun ajaran.'],
                                ['Guru Pengajar', 'Fokus pada pencatatan nilai harian, portofolio, dan catatan siswa.'],
                                ['Wali Murid', 'Akses untuk memantau progres anak dan melihat dokumentasi kegiatan.'],
                                ['Kepala Sekolah', 'Memantau laporan keseluruhan dan statistik perkembangan sekolah.']
                            ];
                        @endphp

                        @foreach($roles as $r)
                        <div class="flex items-center gap-5 p-5 rounded-2xl border border-var(--border) hover:bg-var(--bg) transition-colors">
                            <div class="w-1.5 h-10 bg-var(--accent) rounded-full"></div>
                            <div>
                                <h6 class="font-extrabold text-var(--text-1)">{{ $r[0] }}</h6>
                                <p class="text-xs text-var(--text-2)">{{ $r[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="reveal" style="transition-delay: 200ms">
                    <img src="{{ asset('images/fotosiswa.jpg') }}" 
                        alt="Suasana Sekolah" 
                        class="w-full h-auto object-cover rounded-[40px] shadow-2xl transition-all duration-700">

                </div>
            </div>
        </div>
    </section>

    <!-- Metode Sistem -->
    <section id="metode" class="py-32 bg-var(--bg)">
        <div class="container-custom">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal">
                <h2 class="text-sm font-black text-var(--accent) uppercase tracking-[0.25em] mb-4">Dasar Perhitungan</h2>
                <h3 class="text-4xl lg:text-5xl font-black text-var(--text-1)">Metode Fuzzy SMART.</h3>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 reveal">
                <div class="bg-white p-10 rounded-[32px] border border-var(--border) shadow-xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-var(--accent-lt) rounded-2xl flex items-center justify-center text-var(--accent) text-xl">1</div>
                        <h4 class="text-2xl font-black">Fuzzy Logic</h4>
                    </div>
                    <p class="text-var(--text-2) mb-6">Mengubah penilaian deskriptif (MB, BSH, BSB) menjadi nilai pasti (*Crisp*) untuk bisa diolah secara matematis.</p>
                    <div class="math-formula">
                        Crisp = (lower + medium + upper) / 3
                    </div>
                </div>

                <div class="bg-white p-10 rounded-[32px] border border-var(--border) shadow-xl">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-var(--accent-lt) rounded-2xl flex items-center justify-center text-var(--accent) text-xl">2</div>
                        <h4 class="text-2xl font-black">SMART Method</h4>
                    </div>
                    <p class="text-var(--text-2) mb-6">Melakukan pembobotan pada setiap aspek kriteria untuk menentukan hasil evaluasi akhir yang akurat.</p>
                    <div class="math-formula">
                        V(a) = Σ (Weight_i * Utility_i)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Premium -->
    <section class="relative bg-[#0F120F] text-white overflow-hidden" x-data="{ 
        counts: { 
            siswa: 0, 
            guru: 0, 
            kelas: 0, 
            evaluasi: 0 
        },
        targets: { 
            siswa: {{ $totalSiswa ?? 0 }}, 
            guru: {{ $totalGuru ?? 0 }}, 
            kelas: {{ $totalKelas ?? 0 }}, 
            evaluasi: {{ $totalEvaluasi ?? 0 }} 
        },
        startCounting() {
            Object.keys(this.targets).forEach(key => {
                let current = 0;
                const target = parseInt(this.targets[key]);
                if (target <= 0) {
                    this.counts[key] = 0;
                    return;
                }
                const increment = Math.max(1, Math.ceil(target / 40));
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        this.counts[key] = target;
                        clearInterval(timer);
                    } else {
                        this.counts[key] = current;
                    }
                }, 30);
            });
        }
    }" x-intersect="startCounting()">
        
        <!-- Decoration -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 2px 2px, rgba(132,147,74,0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="container-custom relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                
                <div class="stat-item reveal">
                    <div class="stat-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl lg:text-6xl font-black text-white mb-2" x-text="counts.siswa"></p>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/40">Siswa Terdaftar</p>
                    </div>
                </div>

                <div class="stat-item reveal" style="transition-delay: 100ms">
                    <div class="stat-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl lg:text-6xl font-black text-white mb-2" x-text="counts.guru"></p>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/40">Bapak/Ibu Guru</p>
                    </div>
                </div>

                <div class="stat-item reveal" style="transition-delay: 200ms">
                    <div class="stat-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl lg:text-6xl font-black text-white mb-2" x-text="counts.kelas"></p>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/40">Kelas Aktif</p>
                    </div>
                </div>

                <div class="stat-item reveal" style="transition-delay: 300ms">
                    <div class="stat-icon">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl lg:text-6xl font-black text-white mb-2" x-text="counts.evaluasi"></p>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/40">Evaluasi Berjalan</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-32 bg-white">
        <div class="container-custom">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-center text-3xl lg:text-4xl font-black mb-16">Pertanyaan yang Sering Diajukan</h2>
                <div class="space-y-4 reveal">
                    @php
                        $faqs = [
                            ['Bagaimana cara login ke sistem?', 'Silakan hubungi admin sekolah untuk mendapatkan akun Guru atau Wali Murid Anda.'],
                            ['Apakah wali murid bisa mencetak rapor sendiri?', 'Ya, wali murid dapat mengunduh laporan perkembangan anak (PDF) melalui dashboard evaluasi masing-masing.'],
                            ['Seberapa sering guru mengisi nilai?', 'Guru biasanya mengisi penilaian setiap satu minggu sekali sesuai dengan progres pembelajaran di kelas.'],
                            ['Apakah data penilaian aman?', 'Keamanan data terjamin dengan hak akses yang ketat antar peran pengguna dan enkripsi data.']
                        ];
                    @endphp

                    @foreach($faqs as $faq)
                    <div x-data="{ open: false }" class="border border-var(--border) rounded-2xl overflow-hidden transition-all" :class="open ? 'border-var(--accent) bg-var(--bg)' : ''">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                            <span class="font-extrabold text-var(--text-1)">{{ $faq[0] }}</span>
                            <svg class="w-5 h-5 text-var(--accent) transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse x-cloak>
                            <div class="px-6 pb-6 text-sm text-var(--text-2) leading-relaxed">
                                {{ $faq[1] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#151914] text-white pt-24 pb-12">
        <div class="container-custom">
            <div class="grid lg:grid-cols-4 gap-16 mb-24">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-var(--accent) rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <span class="text-2xl font-black uppercase tracking-tight">TK Negeri Pembina Kota Padang Panjang <span class="text-var(--accent)">Pembina</span></span>
                    </div>
                    <p class="text-white/30 text-sm max-w-sm leading-relaxed mb-8">
                        Membantu bapak/ibu guru dalam mendokumentasikan penilaian perkembangan anak usia dini secara profesional dan terdokumentasi.
                    </p>
                </div>
                <div>
                    <h6 class="text-xs font-black uppercase tracking-widest text-white/20 mb-8">Tautan Cepat</h6>
                    <ul class="space-y-4 text-sm font-bold text-white/50">
                        <li><a href="#beranda" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">Daftar Fitur</a></li>
                        <li><a href="/login" class="hover:text-white transition-colors">Akses Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar Wali Murid</a></li>
                    </ul>
                </div>
                <div>
                    <h6 class="text-xs font-black uppercase tracking-widest text-white/20 mb-8">Kontak</h6>
                    <ul class="space-y-5 text-sm text-white/50">
                        <li>Jalan Rasuna Said, RT VIII, Kampung Manggis, Kecamatan Padang Panjang Barat, Kota Padang Panjang, Provinsi Sumatera Barat</li>
                        <li>info@tknpembina-pp.sch.id</li>
                        <li>(0752) 1234567</li>
                    </ul>
                </div>
            </div>
            <div class="pt-10 border-t border-white/5 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white/20">© 2026 TK Negeri Pembina Kota Padang Panjang. ALL RIGHTS RESERVED.</p>
            </div>
        </div>
    </footer>

</body>
</html>
