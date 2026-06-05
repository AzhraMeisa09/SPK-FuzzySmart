@extends('layouts.app')
@section('title', 'Panduan Penggunaan Sistem')
@section('page-title', 'Panduan & Pedoman Admin')

@section('content')
<div class="space-y-8 pb-16" x-data="{ activeTab: 'workflow' }">

    {{-- ── WELCOME BANNER WITH DYNAMIC GRADIENT ── --}}
    <div class="relative overflow-hidden rounded-[2.5rem] p-8 md:p-12 shadow-2xl border-none" style="background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);">
        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-black opacity-10 rounded-full blur-2xl"></div>
        
        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-300 shadow-[0_0_8px_rgba(134,239,172,0.8)]"></span>
                    <span class="text-[10px] font-bold text-white/90 uppercase tracking-wider">Pusat Informasi & Pedoman SPK</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight tracking-tight">Pedoman Lengkap Penggunaan Sistem SPK</h1>
                <p class="text-sm md:text-base mt-3 leading-relaxed text-white/80 font-medium">
                    Panduan operasional super detail bagi Administrator untuk mengelola data pokok, mengonfigurasi parameter SPK Fuzzy SMART, melakukan penjadwalan penilaian, serta menangani kendala teknis (troubleshooting) sistem.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <button @click="activeTab = 'workflow'" :class="activeTab === 'workflow' ? 'bg-white text-[#6A783D] scale-105' : 'bg-white/10 text-white border border-white/20 hover:bg-white/20'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">
                        Alur Kerja Sistem
                    </button>
                    <button @click="activeTab = 'detail'" :class="activeTab === 'detail' ? 'bg-white text-[#6A783D] scale-105' : 'bg-white/10 text-white border border-white/20 hover:bg-white/20'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">
                        Panduan Langkah-demi-Langkah
                    </button>
                    <button @click="activeTab = 'theory'" :class="activeTab === 'theory' ? 'bg-white text-[#6A783D] scale-105' : 'bg-white/10 text-white border border-white/20 hover:bg-white/20'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">
                        Metodologi & Perhitungan
                    </button>
                    <button @click="activeTab = 'faq'" :class="activeTab === 'faq' ? 'bg-white text-[#6A783D] scale-105' : 'bg-white/10 text-white border border-white/20 hover:bg-white/20'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md">
                        FAQ & Solusi Masalah
                    </button>
                </div>
            </div>
            
            <div class="hidden lg:block flex-shrink-0">
                <div class="w-40 h-40 bg-white/10 rounded-[2.5rem] border border-white/20 backdrop-blur-sm flex items-center justify-center rotate-6 shadow-2xl">
                    <svg class="w-20 h-20 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 1: VISUAL WORKFLOW PIPELINE ── --}}
    <div x-show="activeTab === 'workflow'" class="space-y-6 fade-in">
        <div class="card p-8 shadow-xl border-none">
            <h3 class="text-lg font-bold text-var(--text-1) mb-2">Siklus Hidup Sistem Penilaian SPK</h3>
            <p class="text-xs text-var(--text-3) mb-8">Urutan tugas krusial yang wajib dilewati agar sistem berfungsi dengan optimal dari awal semester hingga rapor tercetak.</p>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center text-center p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-var(--accent)/30 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-var(--accent-lt) text-var(--accent) flex items-center justify-center font-bold text-lg mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        1
                    </div>
                    <h4 class="text-xs font-black text-var(--text-1) uppercase tracking-wider mb-2">Data Pokok</h4>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed">
                        Input data master seperti Tahun Ajaran, Kelas, Siswa, serta akun Guru & Wali Murid.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col items-center text-center p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-var(--accent)/30 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        2
                    </div>
                    <h4 class="text-xs font-black text-var(--text-1) uppercase tracking-wider mb-2">Konfigurasi SPK</h4>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed">
                        Tentukan bobot kriteria (jumlah = 1.0), subkriteria, kategori nilai, dan draf template rekomendasi.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center text-center p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-var(--accent)/30 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        3
                    </div>
                    <h4 class="text-xs font-black text-var(--text-1) uppercase tracking-wider mb-2">Penjadwalan</h4>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed">
                        Buka Periode Penilaian aktif, bentuk Minggu Penilaian, dan jadwalkan subkriteria per minggu.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="flex flex-col items-center text-center p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-var(--accent)/30 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        4
                    </div>
                    <h4 class="text-xs font-black text-var(--text-1) uppercase tracking-wider mb-2">Input & Monitor</h4>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed">
                        Guru menginput nilai harian siswa. Pantau kelengkapannya agar status nilai diubah ke <strong>Final</strong>.
                    </p>
                </div>

                {{-- Step 5 --}}
                <div class="flex flex-col items-center text-center p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-[#6A783D]/30 transition-all group" style="background: var(--accent-lt); border-color: var(--accent)/20;">
                    <div class="w-12 h-12 rounded-full bg-[#84934A] text-white flex items-center justify-center font-bold text-lg mb-4 shadow-md group-hover:scale-110 transition-transform">
                        5
                    </div>
                    <h4 class="text-xs font-black text-[#6A783D] uppercase tracking-wider mb-2">Hitung & Rapor</h4>
                    <p class="text-[11px] text-var(--text-2) leading-relaxed">
                        Kunci periode, hitung otomatis dengan Fuzzy SMART, lalu terbitkan hasil evaluasi dan unduh rapor.
                    </p>
                </div>
            </div>
        </div>

        {{-- INFO ALERT CARD --}}
        <div class="p-6 rounded-[2rem] bg-amber-50 border border-amber-200/60 flex flex-col md:flex-row items-start gap-4">
            <div class="p-3 bg-amber-100 text-amber-800 rounded-xl flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider mb-1">Prasyarat Kritis Sebelum Memulai Semester</h4>
                <p class="text-xs text-amber-700 leading-relaxed font-medium">
                    Pastikan Anda menyelesaikan langkah 1 (Data Pokok) dan langkah 2 (Konfigurasi SPK) sebelum membuka periode pengisian baru. Merubah kriteria atau bobot di tengah-tengah periode berjalan sangat tidak disarankan karena akan merusak konsistensi data riwayat penilaian yang sedang diinput oleh guru.
                </p>
            </div>
        </div>
    </div>

    {{-- ── TAB 2: DETAILED ACCORDION GUIDE ── --}}
    <div x-show="activeTab === 'detail'" class="space-y-6 fade-in" x-data="{ openSection: 1 }">
        {{-- Section 1 --}}
        <div class="card overflow-hidden shadow-xl border-none">
            <button @click="openSection = (openSection === 1 ? null : 1)" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-var(--accent-lt) text-var(--accent) flex items-center justify-center font-bold text-xs">
                        1
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-var(--text-1) tracking-tight">Langkah 1: Mengelola Data Pokok Master</h4>
                        <p class="text-[11px] text-var(--text-3)">Panduan teknis penginputan Guru, Kelas, dan Siswa</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-var(--text-3) transition-transform duration-300" :class="openSection === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openSection === 1" x-collapse class="border-t border-gray-100 p-6 bg-gray-50/50 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs text-var(--text-2) leading-relaxed">
                    {{-- User management details --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">1A. Manajemen User & Role</span>
                        <p class="mt-2">Pengelolaan akun pengguna di aplikasi terbagi menjadi 3 role selain Admin:</p>
                        <ul class="list-decimal pl-4 space-y-2 mt-2 font-medium">
                            <li><strong>Guru:</strong> Memiliki wewenang menginput nilai perkembangan siswa dan portofolio karya.</li>
                            <li><strong>Kepala Sekolah:</strong> Dapat melihat dashboard analisis, statistik kelas, perkembangan siswa global, dan mencetak laporan.</li>
                            <li><strong>Wali Murid:</strong> Akun khusus orang tua untuk melihat nilai rapor digital, grafik capaian bulanan, dan foto portofolio anak mereka.</li>
                        </ul>
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl text-[11px]">
                            <strong>Cara input:</strong> Masuk ke menu <span class="font-bold">User</span> &rarr; Klik <span class="font-bold text-var(--accent)">+ Tambah User</span> &rarr; Isi nama lengkap, username unik, pilih role, dan buat password default.
                        </div>
                    </div>

                    {{-- Tahun Ajaran & Kelas details --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">1B. Tahun Ajaran & Kelas</span>
                        <p class="mt-2">Berfungsi mendefinisikan pembagian rombongan belajar setiap semester:</p>
                        <ul class="list-decimal pl-4 space-y-2 mt-2 font-medium">
                            <li><strong>Tahun Ajaran:</strong> Berisi status aktif/non-aktif tahun pembelajaran (misalnya "2024/2025"). Hanya boleh ada satu Tahun Ajaran yang berstatus aktif dalam satu periode waktu.</li>
                            <li><strong>Kelas:</strong> Membuat daftar rombel aktif (misal "Kelas A1", "Kelas B2"). Setiap kelas wajib ditautkan dengan satu Guru Wali Kelas sebagai penanggung jawab pengisian nilai kelas tersebut.</li>
                        </ul>
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl text-[11px]">
                            <strong>Cara input:</strong> Masuk ke menu <span class="font-bold">Tahun Ajaran</span> / <span class="font-bold">Kelas</span> &rarr; Klik <span class="font-bold text-var(--accent)">+ Tambah</span> &rarr; Isi detail form &rarr; Simpan.
                        </div>
                    </div>

                    {{-- Siswa details --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">1C. Manajemen Siswa & Orang Tua</span>
                        <p class="mt-2">Berisi data diri anak didik serta relasi wali murid untuk integrasi notifikasi:</p>
                        <ul class="list-decimal pl-4 space-y-2 mt-2 font-medium">
                            <li><strong>Identitas:</strong> Daftarkan NISN (Nomor Induk Siswa Nasional), Nama Lengkap, Tempat Tanggal Lahir, Jenis Kelamin, dan Kelas tempat siswa belajar.</li>
                            <li><strong>Relasi Wali:</strong> Hubungkan nama siswa dengan akun Wali Murid yang sudah dibuat pada langkah 1A. Jika wali murid tidak dihubungkan, orang tua tidak akan bisa melihat hasil belajar anak mereka.</li>
                        </ul>
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl text-[11px]">
                            <strong>Cara input:</strong> Buka menu <span class="font-bold">Siswa</span> &rarr; Klik <span class="font-bold text-var(--accent)">+ Tambah Siswa</span> &rarr; Isi seluruh identitas wajib &rarr; Tautkan ke akun Wali Murid &rarr; Simpan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2 --}}
        <div class="card overflow-hidden shadow-xl border-none">
            <button @click="openSection = (openSection === 2 ? null : 2)" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        2
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-var(--text-1) tracking-tight">Langkah 2: Konfigurasi Parameter SPK (Fuzzy SMART)</h4>
                        <p class="text-[11px] text-var(--text-3)">Menyiapkan Kriteria, Bobot, Subkriteria, dan Template Sistem Pakar</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-var(--text-3) transition-transform duration-300" :class="openSection === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openSection === 2" x-collapse class="border-t border-gray-100 p-6 bg-gray-50/50 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs text-var(--text-2) leading-relaxed">
                    {{-- Kriteria & Bobot --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-blue-600 uppercase tracking-wider text-[10px]">2A. Pembobotan Kriteria</span>
                        <p class="mt-2">Aspek utama penilaian anak usia dini berdasarkan kurikulum nasional. Contoh kriteria di TK:</p>
                        <ul class="list-disc pl-4 space-y-1.5 mt-2 font-medium">
                            <li>K1: Nilai Agama & Moral (Bobot: 0.20)</li>
                            <li>K2: Fisik Motorik (Bobot: 0.15)</li>
                            <li>K3: Kognitif (Bobot: 0.25)</li>
                            <li>K4: Bahasa (Bobot: 0.20)</li>
                            <li>K5: Sosial Emosional (Bobot: 0.20)</li>
                        </ul>
                        <div class="p-3 bg-red-50 text-red-700 rounded-xl mt-3 text-[11px] border border-red-100 font-semibold">
                            ⚠️ ATURAN MUTLAK:<br>
                            Jumlah total penjumlahan bobot K1 s/d K5 di atas wajib bernilai tepat 1.00 (100%). Masukkan bobot menggunakan desimal dengan pemisah tanda titik (.), misal: 0.25.
                        </div>
                    </div>

                    {{-- Subkriteria --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-blue-600 uppercase tracking-wider text-[10px]">2B. Subkriteria & Import Word</span>
                        <p class="mt-2">Indikator capaian detail untuk setiap Kriteria induk (contoh: "S1.1 Meniru gerakan ibadah").</p>
                        <p class="mt-2"><strong>Fitur Import Word:</strong> Untuk mempercepat setup, Anda dapat mengunggah file dokumen Microsoft Word (.docx) berisi daftar subkriteria.</p>
                        <div class="p-3 bg-gray-50 rounded-xl text-[11px] mt-3">
                            <strong>Format File Word:</strong> Buat judul kriteria menggunakan penomoran tebal, lalu tulis subkriteria di bawahnya menggunakan poin-poin/bullet list standar. Sistem akan otomatis membagi kriteria dan subkriteria ke dalam database.
                        </div>
                    </div>

                    {{-- Template Rekomendasi --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-blue-600 uppercase tracking-wider text-[10px]">2C. Template Rekomendasi (Sistem Pakar)</span>
                        <p class="mt-2">Merupakan database kalimat saran psikologi/pedagogi yang akan disatukan otomatis oleh sistem untuk dicetak di rapor siswa:</p>
                        <ul class="list-decimal pl-4 space-y-2 mt-2 font-medium">
                            <li><strong>Template Rincian:</strong> Dibuat per-subkriteria untuk status MB, BSH, BSB. Gunakan kode <code>@{{nama_siswa}}</code> agar nama anak terpanggil secara dinamis di teks (misal: "Ananda @{{nama_siswa}} sudah menunjukkan sikap mandiri...").</li>
                            <li><strong>Template Umum:</strong> Rangkuman global rapor akhir. Gunakan kode <code>@{{aspek}}</code> agar sistem secara otomatis menyebutkan daftar subkriteria yang anak tersebut masih mendapatkan nilai 'MB' (Mulai Berkembang) sebagai bahan evaluasi di rumah.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3 --}}
        <div class="card overflow-hidden shadow-xl border-none">
            <button @click="openSection = (openSection === 3 ? null : 3)" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                        3
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-var(--text-1) tracking-tight">Langkah 3: Mengatur Penjadwalan & Siklus Penilaian</h4>
                        <p class="text-[11px] text-var(--text-3)">Mengaktifkan Periode Penilaian dan Menentukan Subkriteria Mingguan</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-var(--text-3) transition-transform duration-300" :class="openSection === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openSection === 3" x-collapse class="border-t border-gray-100 p-6 bg-gray-50/50 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs text-var(--text-2) leading-relaxed">
                    {{-- Periode Penilaian --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-amber-600 uppercase tracking-wider text-[10px]">3A. PERIODE PENILAIAN BARU</span>
                        <p class="mt-2">Periode adalah representasi rentang waktu penilaian aktif (Semester) untuk suatu kelas.</p>
                        <ol class="list-decimal pl-4 space-y-1.5 mt-2 font-medium">
                            <li>Buka halaman menu <strong>Periode</strong> &rarr; klik <strong>+ Tambah Periode</strong>.</li>
                            <li>Tentukan nama (misal: "Semester Ganjil A1 2024").</li>
                            <li>Tautkan dengan Kelas & Tahun Ajaran yang sesuai.</li>
                            <li>Set status periode tersebut menjadi <strong>Aktif</strong>.</li>
                        </ol>
                        <p class="mt-2 text-[11px] text-var(--text-3)"><em>*Catatan: Sistem mendukung penilaian paralel di mana beberapa kelas berjalan aktif secara bersamaan dalam periode semester yang sama.</em></p>
                    </div>

                    {{-- Minggu Penilaian --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-amber-600 uppercase tracking-wider text-[10px]">3B. MEMBUAT MINGGU PENILAIAN</span>
                        <p class="mt-2">Dalam satu semester, kegiatan belajar dibagi menjadi beberapa minggu efektif (contoh: Minggu ke-1 s/d Minggu ke-17).</p>
                        <ol class="list-decimal pl-4 space-y-1.5 mt-2 font-medium">
                            <li>Masuk ke menu <strong>Minggu Penilaian</strong>.</li>
                            <li>Klik tombol <strong>+ Tambah Minggu</strong>.</li>
                            <li>Tuliskan deskripsi "Minggu 1", lalu atur tanggal mulai pengisian (misalnya 15 Juli 2024).</li>
                            <li>Pilih Periode Penilaian Induk yang telah Anda buat pada langkah 3A.</li>
                        </ol>
                    </div>

                    {{-- Penjadwalan Subkriteria --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-amber-600 uppercase tracking-wider text-[10px]">3C. PENJADWALAN SUBKRITERIA MINGGUAN</span>
                        <p class="mt-2">Agar Guru tidak terbebani mengisi puluhan indikator sekaligus setiap hari, Admin menjadwalkan subkriteria apa saja yang akan dipelajari:</p>
                        <ol class="list-decimal pl-4 space-y-1.5 mt-2 font-medium">
                            <li>Di halaman <strong>Minggu Penilaian</strong>, temukan baris minggu terkait.</li>
                            <li>Klik tombol aksi <strong>Jadwal</strong> di kolom kanan.</li>
                            <li>Klik <strong>+ Tambah Subkriteria ke Jadwal</strong>.</li>
                            <li>Pilih subkriteria yang relevan dengan tema pembelajaran minggu itu (contoh: 3 s/d 5 subkriteria per minggu).</li>
                        </ol>
                        <div class="p-3 bg-amber-50 text-amber-800 rounded-xl mt-3 text-[11px]">
                            Dengan metode penjadwalan ini, ketika Guru membuka halaman nilai mingguan di akun mereka, hanya indikator terpilih inilah yang akan muncul di layar.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4 --}}
        <div class="card overflow-hidden shadow-xl border-none">
            <button @click="openSection = (openSection === 4 ? null : 4)" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                        4
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-var(--text-1) tracking-tight">Langkah 4: Pemantauan Input Nilai & Kolaborasi Guru</h4>
                        <p class="text-[11px] text-var(--text-3)">Mengontrol progres pengisian nilai guru dan memastikan kesiapan data</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-var(--text-3) transition-transform duration-300" :class="openSection === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openSection === 4" x-collapse class="border-t border-gray-100 p-6 bg-gray-50/50 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs text-var(--text-2) leading-relaxed">
                    {{-- Progres Dashboard --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-purple-600 uppercase tracking-wider text-[10px]">4A. Monitoring Dashboard Utama</span>
                        <p class="mt-2">Di halaman utama Dashboard, Anda dibekali widget visual <strong>Monitoring Progres Penilaian</strong>:</p>
                        <ul class="list-disc pl-4 space-y-2 mt-2 font-medium">
                            <li>Sistem otomatis mendeteksi jumlah siswa terdaftar dan membandingkannya dengan jumlah penilaian yang sudah masuk pada minggu aktif.</li>
                            <li>Menampilkan diagram batang horizontal kelengkapan nilai per kelas (dalam bentuk persentase %).</li>
                            <li>Warna hijau mengindikasikan kelengkapan &ge; 80%, kuning mengindikasikan progres sedang (40% - 79%), sedangkan merah menunjukkan pengisian masih di bawah 40%.</li>
                        </ul>
                    </div>

                    {{-- Draf vs Final --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-purple-600 uppercase tracking-wider text-[10px]">4B. Memahami Status "Draft" vs "Final"</span>
                        <p class="mt-2">Pemberian nilai oleh Guru memiliki siklus pengamanan integritas data:</p>
                        <ul class="list-disc pl-4 space-y-2 mt-2 font-medium">
                            <li><strong>Draft (Kuning):</strong> Nilai baru diinput sebagian atau disimpan sementara oleh guru. Nilai berstatus draf masih bisa diubah kapan saja oleh guru, tetapi <strong>belum sah</strong> dan ditolak oleh mesin kalkulasi SPK.</li>
                            <li><strong>Final (Hijau):</strong> Penilaian mingguan kelas telah lengkap dan guru menekan tombol <strong>Finalisasi</strong> di halaman penilaian mereka. Status ini mengunci nilai agar tidak berubah secara tidak sengaja.</li>
                        </ul>
                        <div class="p-3 bg-purple-50 text-purple-800 rounded-xl mt-3 text-[11px]">
                            <strong>Peran Admin:</strong> Jika terdeteksi progres macet pada suatu kelas, ingatkan guru wali kelas bersangkutan untuk menyelesaikan penginputan dan memencet tombol <strong>Finalisasi Penilaian</strong> di akun masing-masing.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 5 --}}
        <div class="card overflow-hidden shadow-xl border-none">
            <button @click="openSection = (openSection === 5 ? null : 5)" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-[#84934A] text-white flex items-center justify-center font-bold text-xs">
                        5
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-var(--text-1) tracking-tight">Langkah 5: Finalisasi & Eksekusi Perhitungan Hasil Evaluasi</h4>
                        <p class="text-[11px] text-var(--text-3)">Langkah eksekusi penutupan periode dan pencetakan rapor akhir siswa</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-var(--text-3) transition-transform duration-300" :class="openSection === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openSection === 5" x-collapse class="border-t border-gray-100 p-6 bg-gray-50/50 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs text-var(--text-2) leading-relaxed">
                    {{-- Validasi --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">5A. Validasi Kelayakan Hitung</span>
                        <p class="mt-2">Sebelum mengklik tombol hitung, sistem secara otomatis melakukan 3 lapis validasi ketat guna memastikan tidak ada bias atau data kosong:</p>
                        <ul class="list-decimal pl-4 space-y-1.5 mt-2 font-medium">
                            <li>Memastikan status seluruh Minggu Penilaian pada periode tersebut sudah ditandai <strong>Selesai</strong> oleh Guru.</li>
                            <li>Memverifikasi jadwal subkriteria mingguan terisi (tidak ada minggu kosong).</li>
                            <li>Memastikan status seluruh lembar penilaian siswa di semua minggu berstatus <strong>Final</strong> (tidak ada draf).</li>
                        </ul>
                    </div>

                    {{-- Eksekusi --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">5B. Menjalankan Perhitungan SPK</span>
                        <p class="mt-2">Apabila prasyarat di atas telah terpenuhi, Admin siap mengeksekusi perhitungan:</p>
                        <ol class="list-decimal pl-4 space-y-1.5 mt-2 font-medium">
                            <li>Buka menu <strong>Periode Penilaian</strong>.</li>
                            <li>Temukan baris periode semester aktif yang ingin dihitung.</li>
                            <li>Klik tombol <strong>Kunci & Hitung</strong> di kolom aksi paling kanan.</li>
                            <li>Konfirmasi pop-up persetujuan. Sistem akan menjalankan perhitungan Fuzzy SMART dalam waktu kurang dari 2 detik.</li>
                        </ol>
                    </div>

                    {{-- Unduh rapor --}}
                    <div class="space-y-2 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="font-extrabold text-[#84934A] uppercase tracking-wider text-[10px]">5C. Pencetakan & Distribusi Rapor</span>
                        <p class="mt-2">Setelah proses kalkulasi berhasil diselesaikan oleh sistem:</p>
                        <ul class="list-disc pl-4 space-y-2 mt-2 font-medium">
                            <li>Masuk ke menu **Hasil Evaluasi** &rarr; pilih kelas &rarr; layar akan menampilkan daftar nilai akhir seluruh anak didik.</li>
                            <li>Klik ikon mata/detail pada baris nama siswa untuk melihat rincian matematika pengambil keputusan serta rekomendasi deskripsi.</li>
                            <li>Klik <strong>Cetak Word</strong> / <strong>Cetak Rapor</strong> untuk mengunduh berkas laporan perkembangan siswa rapi yang siap dibagikan kepada wali murid.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 3: SPK METODOLOGI THEORY ── --}}
    <div x-show="activeTab === 'theory'" class="space-y-6 fade-in">
        <div class="card p-8 shadow-xl border-none space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-var(--text-1)">Metodologi Fuzzy SMART (Simple Multi-Attribute Rating Technique)</h3>
                <p class="text-xs text-var(--text-3) mt-1">Penjelasan formulasi matematika dan alur logika perhitungan sistem pakar di TK Negeri Pembina</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs text-var(--text-2) leading-relaxed">
                {{-- Teori 1: Fuzzy Aggregation --}}
                <div class="space-y-3 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-var(--accent-lt) text-var(--accent) flex items-center justify-center font-black mb-4">
                        A
                    </div>
                    <h4 class="text-xs font-bold text-var(--text-1) uppercase tracking-wide">1. Agregasi Crisp Kriteria</h4>
                    <p>
                        Penilaian guru untuk subkriteria menggunakan kategori linguistik (MB, BSH, BSB) yang kemudian ditransformasi secara Fuzzy menjadi nilai numerik tegas (Crisp):
                    </p>
                    <ul class="list-disc pl-4 space-y-1 my-2">
                        <li><strong>MB (Mulai Berkembang):</strong> Crisp = 33,33</li>
                        <li><strong>BSH (Berkembang Sesuai Harapan):</strong> Crisp = 66,67</li>
                        <li><strong>BSB (Berkembang Sangat Baik):</strong> Crisp = 88,89</li>
                    </ul>
                    <p>
                        Sistem menghitung nilai rata-rata tiap subkriteria di sepanjang minggu aktif dalam periode penilaian tersebut, lalu menghitung rata-rata kriteria induk ($C_{out}$).
                    </p>
                </div>

                {{-- Teori 2: SMART Normalization --}}
                <div class="space-y-3 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black mb-4">
                        B
                    </div>
                    <h4 class="text-xs font-bold text-var(--text-1) uppercase tracking-wide">2. Normalisasi SMART</h4>
                    <p>
                        Untuk mengeliminasi bias, nilai rata-rata kriteria ($C_{out}$) dinormalisasikan menjadi nilai utilitas ($u_i$) berkisar antara 0 sampai 1 menggunakan persamaan SMART:
                    </p>
                    <div class="my-4 p-3 bg-white rounded-xl border border-gray-200 text-center font-semibold text-var(--text-1)">
                        $$u_i = \frac{C_{out} - C_{min}}{C_{max} - C_{min}}$$
                    </div>
                    <p>
                        Nilai $C_{min}$ dan $C_{max}$ ditentukan secara <strong>Data-Driven (Dinamis)</strong> berdasarkan nilai terendah dan tertinggi aktual seluruh siswa dalam kelas tersebut di bawah kriteria yang sama, menjamin asas pembanding yang objektif dan adil.
                    </p>
                </div>

                {{-- Teori 3: Final Score --}}
                <div class="space-y-3 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-black mb-4">
                        C
                    </div>
                    <h4 class="text-xs font-bold text-var(--text-1) uppercase tracking-wide">3. Nilai Akhir & Kategori</h4>
                    <p>
                        Nilai evaluasi akhir ($V_a$) dihitung dengan menjumlahkan perkalian antara bobot kriteria ($w_i$) dan nilai utilitas ternormalisasi ($u_i$) kriteria tersebut:
                    </p>
                    <div class="my-4 p-3 bg-white rounded-xl border border-gray-200 text-center font-semibold text-var(--text-1)">
                        $$V_a = \sum_{i=1}^{n} (w_i \times u_i)$$
                    </div>
                    <p>
                        Nilai $V_a$ (dalam skala 0 - 1) dikalikan dengan 100 lalu disesuaikan kembali dengan range <strong>Kategori Nilai</strong> yang telah ditetapkan di database untuk menentukan kategori akhir siswa (MB / BSH / BSB).
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 4: FAQ & TROUBLESHOOTING ── --}}
    <div x-show="activeTab === 'faq'" class="space-y-6 fade-in" x-data="{ openFaq: 1 }">
        <div class="card p-8 shadow-xl border-none">
            <h3 class="text-lg font-bold text-var(--text-1) mb-2">Pusat Bantuan & Solusi Masalah</h3>
            <p class="text-xs text-var(--text-3) mb-8">Solusi cepat dan panduan penyelesaian kendala operasional yang sering dialami oleh Administrator.</p>

            <div class="space-y-4">
                {{-- FAQ 1 --}}
                                    <button @click="openFaq = (openFaq === 1 ? null : 1)" class="w-full flex items-center justify-between py-2 text-left font-bold text-xs text-var(--text-1) uppercase tracking-wide hover:text-var(--accent) transition-colors">
                        <span>Q1: Kenapa tombol "Kunci & Hitung" memicu error "🔒 GAGAL: Masih ada minggu yang belum selesai..."?</span>
                        <svg class="w-4 h-4 text-var(--text-3) transition-transform duration-200" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="mt-2 text-xs text-var(--text-2) leading-relaxed bg-gray-50 p-4 rounded-xl space-y-2">
                        <p><strong>Penyebab:</strong> Di dalam periode penilaian yang Anda pilih, terdapat Minggu Penilaian yang statusnya belum diubah menjadi <strong>Selesai</strong> (atau dinonaktifkan) oleh Guru/Admin.</p>
                        <p><strong>Solusi:</strong></p>
                        <ol class="list-decimal pl-4 space-y-1 font-medium">
                            <li>Buka menu <span class="font-bold">Minggu Penilaian</span>.</li>
                            <li>Periksa kolom status untuk setiap baris minggu pada periode aktif tersebut.</li>
                            <li>Jika terdapat minggu yang pengisian nilainya telah selesai namun statusnya masih 'Aktif', minta Guru wali kelas bersangkutan untuk menekan tombol <strong>Selesai</strong> di halaman mereka, atau Anda dapat mengubah statusnya secara paksa melalui panel Admin.</li>
                        </ol>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="border-b border-gray-100 pb-4">
                    <button @click="openFaq = (openFaq === 2 ? null : 2)" class="w-full flex items-center justify-between py-2 text-left font-bold text-xs text-var(--text-1) uppercase tracking-wide hover:text-var(--accent) transition-colors">
                        <span>Q2: Kenapa sistem menolak menghitung dengan error "Penilaian siswa belum lengkap atau masih ada status draft..."?</span>
                        <svg class="w-4 h-4 text-var(--text-3) transition-transform duration-200" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="mt-2 text-xs text-var(--text-2) leading-relaxed bg-gray-50 p-4 rounded-xl space-y-2">
                        <p><strong>Penyebab:</strong> Ada salah satu siswa di dalam kelas tersebut yang datanya belum dinilai secara utuh oleh guru pada jadwal subkriteria mingguan, atau nilai siswa tersebut sudah disimpan tetapi masih berstatus <strong>Draft</strong> (belum di-finalisasi).</p>
                        <p><strong>Solusi:</strong></p>
                        <ol class="list-decimal pl-4 space-y-1 font-medium">
                            <li>Masuk ke Dashboard utama dan periksa bagian <strong>Monitoring Progres Penilaian</strong>.</li>
                            <li>Carilah kelas yang persentase pengisiannya belum mencapai 100%.</li>
                            <li>Hubungi Guru Wali Kelas tersebut dan instruksikan untuk memeriksa apakah seluruh siswa telah diberikan nilai di setiap minggu aktif, dan pastikan mereka telah mengklik tombol <strong>Finalisasi Penilaian (kunci nilai)</strong> di halaman mereka.</li>
                        </ol>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="border-b border-gray-100 pb-4">
                    <button @click="openFaq = (openFaq === 3 ? null : 3)" class="w-full flex items-center justify-between py-2 text-left font-bold text-xs text-var(--text-1) uppercase tracking-wide hover:text-var(--accent) transition-colors">
                        <span>Q3: Kenapa jumlah total bobot kriteria wajib bernilai tepat 1.0 (100%)?</span>
                        <svg class="w-4 h-4 text-var(--text-3) transition-transform duration-200" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="mt-2 text-xs text-var(--text-2) leading-relaxed bg-gray-50 p-4 rounded-xl space-y-2">
                        <p><strong>Penjelasan:</strong> Dalam metodologi pengambilan keputusan SMART (Simple Multi-Attribute Rating Technique), bobot kriteria ($w_i$) mewakili tingkat kepentingan relatif dari setiap aspek penilaian terhadap total nilai keseluruhan. Jika jumlah akumulasi seluruh bobot kurang dari 1.0 atau melebihinya, hasil pembagian skor utilitas matematis tidak akan presisi (bias) dan merusak kredibilitas laporan akhir anak didik.</p>
                        <p><strong>Solusi:</strong> Jika Anda merubah salah satu bobot kriteria, pastikan Anda juga menyesuaikan bobot kriteria lainnya agar hasil penjumlahan totalnya tepat bernilai 1.0.</p>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="border-b border-gray-100 pb-4">
                    <button @click="openFaq = (openFaq === 4 ? null : 4)" class="w-full flex items-center justify-between py-2 text-left font-bold text-xs text-var(--text-1) uppercase tracking-wide hover:text-var(--accent) transition-colors">
                        <span>Q4: Bagaimana cara menggunakan fitur "Import Word" subkriteria dengan benar?</span>
                        <svg class="w-4 h-4 text-var(--text-3) transition-transform duration-200" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === 4" x-collapse class="mt-2 text-xs text-var(--text-2) leading-relaxed bg-gray-50 p-4 rounded-xl space-y-2">
                        <p><strong>Cara Penggunaan:</strong> Fitur ini mempermudah impor data subkriteria dari berkas dokumen `.docx` Microsoft Word.</p>
                        <ol class="list-decimal pl-4 space-y-1.5 font-medium">
                            <li>Buka Microsoft Word, buat dokumen baru.</li>
                            <li>Tuliskan nama kriteria sebagai judul penomoran tebal, contoh: <br><code><strong>1. Nilai Agama Dan Moral</strong></code></li>
                            <li>Di bawahnya, ketik daftar subkriteria menggunakan bullet list standar, contoh:<br>
                                <code>- Anak dapat menirukan gerakan ibadah secara sederhana</code><br>
                                <code>- Anak menunjukkan perilaku jujur dan toleransi</code>
                            </li>
                            <li>Simpan file dengan format `.docx`. Masuk ke menu <strong>Subkriteria</strong> di aplikasi, klik <strong>Import Word</strong>, lalu unggah dokumen tersebut.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
