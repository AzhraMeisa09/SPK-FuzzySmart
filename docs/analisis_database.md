# Analisis Struktur Database Sistem SPK-FuzzySmart

Dokumen ini berisi analisis struktur database lengkap berdasarkan migrasi terbaru pada sistem SPK (Sistem Pendukung Keputusan) Fuzzy SMART untuk evaluasi perkembangan anak. Format penyajian dibuat terstruktur seperti standar dokumentasi sistem.

---

### 1. Tabel users

Tabel `users` digunakan untuk menyimpan data seluruh pengguna sistem, meliputi Admin, Guru, Kepala Sekolah, dan Wali Murid. Tabel ini menjadi acuan autentikasi dan otorisasi akses setiap aktor ke dalam sistem.

**Tabel 1. Struktur Tabel Users**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_user` | VARCHAR(10) | *Primary key*, ID unik pengguna |
| 2 | `nama_lengkap` | VARCHAR(50) | Nama lengkap pengguna |
| 3 | `username` | VARCHAR(50) | Username untuk login (*Unique*) |
| 4 | `email` | VARCHAR(50) | Alamat email (*Unique*) |
| 5 | `password` | VARCHAR(255) | Password terenkripsi |
| 6 | `role` | ENUM | Peran/hak akses pengguna: 'admin', 'guru', 'kepala_sekolah', 'wali_murid' |
| 7 | `foto_profil` | VARCHAR(255) | Path file foto profil (*Nullable*) |
| 8 | `no_hp` | VARCHAR(20) | Nomor telepon/HP (*Nullable*) |
| 9 | `alamat` | TEXT | Alamat lengkap pengguna (*Nullable*) |
| 10 | `is_active` | BOOLEAN | Status keaktifan akun (Default: *true*) |
| 11 | `remember_token` | VARCHAR(100) | Token untuk "remember me" sesi login (*Nullable*) |
| 12 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 13 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 2. Tabel tahun_ajaran

Tabel `tahun_ajaran` digunakan untuk menyimpan data tahun ajaran akademik yang terdaftar di sekolah, sebagai dasar pengelompokan kelas dan periode penilaian.

**Tabel 2. Struktur Tabel Tahun Ajaran**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_tahun_ajaran` | VARCHAR(10) | *Primary key*, ID unik tahun ajaran |
| 2 | `nama` | VARCHAR(50) | Nama tahun ajaran (*Unique*, contoh: '2023/2024') |
| 3 | `tanggal_mulai` | DATE | Tanggal mulai tahun ajaran |
| 4 | `tanggal_selesai` | DATE | Tanggal selesai tahun ajaran |
| 5 | `is_aktif` | BOOLEAN | Status keaktifan tahun ajaran (apakah sedang berjalan) |
| 6 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 3. Tabel kelas

Tabel `kelas` digunakan untuk menyimpan data kelas-kelas yang terdaftar dalam suatu tahun ajaran tertentu.

**Tabel 3. Struktur Tabel Kelas**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_kelas` | VARCHAR(10) | *Primary key*, ID unik kelas |
| 2 | `tahun_ajaran_id` | VARCHAR(10) | *Foreign key* ke `tahun_ajaran.id_tahun_ajaran` (*Cascade on delete*) |
| 3 | `nama_kelas` | VARCHAR(50) | Nama kelas (contoh: 'TK A1') |
| 4 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 4. Tabel kelas_guru

Tabel `kelas_guru` merupakan tabel pivot many-to-many yang digunakan untuk memetakan penugasan guru (wali kelas) terhadap kelas-kelas tertentu.

**Tabel 4. Struktur Tabel Kelas Guru**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_kelas_guru` | VARCHAR(10) | *Primary key*, ID unik penugasan kelas-guru |
| 2 | `kelas_id` | VARCHAR(10) | *Foreign key* ke `kelas.id_kelas` (*Cascade on delete*) |
| 3 | `guru_id` | VARCHAR(10) | *Foreign key* ke `users.id_user` (*Cascade on delete*) |

*Catatan: Terdapat constraint unik pada kombinasi `[kelas_id, guru_id]`.*

---

### 5. Tabel siswa

Tabel `siswa` digunakan untuk menyimpan informasi biodata lengkap siswa yang terdaftar di sekolah, termasuk hubungannya dengan kelas dan wali murid.

**Tabel 5. Struktur Tabel Siswa**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_siswa` | VARCHAR(10) | *Primary key*, ID unik siswa |
| 2 | `kelas_id` | VARCHAR(10) | *Foreign key* ke `kelas.id_kelas` (*Cascade on delete*) |
| 3 | `wali_murid_id` | VARCHAR(10) | *Foreign key* ke `users.id_user` (*Nullable, Null on delete*) |
| 4 | `kode` | VARCHAR(10) | Kode induk/NIS siswa (*Nullable*) |
| 5 | `kode_registrasi` | VARCHAR(10) | Kode registrasi pendaftaran unik siswa (*Nullable, Unique*) |
| 6 | `name` | VARCHAR(50) | Nama lengkap siswa |
| 7 | `tanggal_lahir` | DATE | Tanggal lahir siswa |
| 8 | `jenis_kelamin` | ENUM | Jenis kelamin siswa ('L' / 'P') |
| 9 | `nama_orang_tua` | VARCHAR(50) | Nama orang tua/wali (*Nullable*) |
| 10 | `alamat` | TEXT | Alamat lengkap siswa (*Nullable*) |
| 11 | `no_hp_orang_tua` | VARCHAR(20) | Nomor HP orang tua/wali (*Nullable*) |
| 12 | `foto` | VARCHAR(255) | Path file foto siswa (*Nullable*) |
| 13 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 14 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 6. Tabel kriteria

Tabel `kriteria` menyimpan kriteria-kriteria penilaian utama yang digunakan dalam sistem pendukung keputusan (SPK) berbasis Fuzzy SMART.

**Tabel 6. Struktur Tabel Kriteria**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_kriteria` | VARCHAR(10) | *Primary key*, ID unik kriteria |
| 2 | `nama_kriteria` | VARCHAR(100) | Nama kriteria penilaian (*Unique*) |
| 3 | `bobot_kriteria` | DOUBLE | Bobot kriteria dalam perhitungan SPK |
| 4 | `is_aktif` | BOOLEAN | Status keaktifan kriteria (Default: *true*) |
| 5 | `deleted_at` | TIMESTAMP | Waktu penghapusan logis/soft deletes (*Nullable*) |
| 6 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 7 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 7. Tabel subkriteria

Tabel `subkriteria` menyimpan indikator penilaian detail (subkriteria) yang menginduk pada kriteria tertentu, dilengkapi dengan deskripsi rubrik penilaian untuk masing-masing tingkat capaian.

**Tabel 7. Struktur Tabel Subkriteria**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_subkriteria` | VARCHAR(10) | *Primary key*, ID unik subkriteria |
| 2 | `kriteria_id` | VARCHAR(10) | *Foreign key* ke `kriteria.id_kriteria` (*Cascade on delete*) |
| 3 | `nama_subkriteria` | VARCHAR(255) | Nama indikator subkriteria |
| 4 | `rubrik_mb` | TEXT | Rubrik penilaian untuk kategori "Mulai Berkembang" (MB) |
| 5 | `rubrik_bsh` | TEXT | Rubrik penilaian untuk kategori "Berkembang Sesuai Harapan" (BSH) |
| 6 | `rubrik_bsb` | TEXT | Rubrik penilaian untuk kategori "Berkembang Sangat Baik" (BSB) |
| 7 | `deleted_at` | TIMESTAMP | Waktu soft delete (*Nullable*) |
| 8 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 9 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 8. Tabel kategori_nilai

Tabel `kategori_nilai` menyimpan kategori skala nilai (seperti MB, BSH, BSB) beserta batas-batas parameter logika fuzzy (segitiga/trapesium) dan nilai crisp (defuzzifikasi) yang bersangkutan.

**Tabel 8. Struktur Tabel Kategori Nilai**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_kategori` | VARCHAR(10) | *Primary key*, ID unik kategori nilai |
| 2 | `nama` | VARCHAR(20) | Nama kategori nilai (*Unique*, contoh: 'MB', 'BSH', 'BSB') |
| 3 | `nilai_l` | DOUBLE | Nilai Lower (batas bawah) fuzzy |
| 4 | `nilai_m` | DOUBLE | Nilai Middle (nilai tengah) fuzzy |
| 5 | `nilai_u` | DOUBLE | Nilai Upper (batas atas) fuzzy |
| 6 | `nilai_crisp` | DOUBLE | Nilai crisp (hasil konversi defuzzifikasi) |
| 7 | `rentang_min` | DOUBLE | Batas rentang minimum kategori nilai |
| 8 | `rentang_max` | DOUBLE | Batas rentang maksimum kategori nilai |
| 9 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 9. Tabel periode_penilaian

Tabel `periode_penilaian` digunakan untuk mencatat rentang periode penilaian evaluasi secara berkala (misalnya rapor bulanan, tengah semester, atau akhir semester).

**Tabel 9. Struktur Tabel Periode Penilaian**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_periode` | VARCHAR(10) | *Primary key*, ID unik periode |
| 2 | `tahun_ajaran_id` | VARCHAR(10) | *Foreign key* ke `tahun_ajaran.id_tahun_ajaran` (*Cascade on delete*) |
| 3 | `nama_periode` | VARCHAR(100) | Nama periode penilaian (contoh: 'Rapor Akhir Semester Ganjil') |
| 4 | `semester` | VARCHAR(20) | Informasi semester (contoh: '1' atau 'Ganjil') |
| 5 | `tanggal_mulai` | DATE | Tanggal dimulainya periode penilaian |
| 6 | `tanggal_selesai` | DATE | Tanggal berakhirnya periode penilaian |
| 7 | `is_aktif` | BOOLEAN | Status keaktifan periode (Default: *false*) |
| 8 | `status` | ENUM | Status tahapan periode: 'draft', 'aktif', 'proses', 'final' (Default: 'draft') |
| 9 | `finalized_at` | TIMESTAMP | Waktu ketika periode difinalisasi (*Nullable*) |
| 10 | `created_by` | VARCHAR(10) | *Foreign key* ke `users.id_user` selaku pembuat data (*Nullable, Null on delete*) |
| 11 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 12 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 10. Tabel periode_kelas

Tabel `periode_kelas` adalah tabel pivot many-to-many yang menghubungkan satu periode penilaian dengan beberapa kelas yang mengikuti proses penilaian tersebut.

**Tabel 10. Struktur Tabel Periode Kelas**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_periode_kelas` | VARCHAR(10) | *Primary key*, ID unik relasi periode-kelas |
| 2 | `periode_id` | VARCHAR(10) | *Foreign key* ke `periode_penilaian.id_periode` (*Cascade on delete*) |
| 3 | `kelas_id` | VARCHAR(10) | *Foreign key* ke `kelas.id_kelas` (*Cascade on delete*) |
| 4 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 5 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

*Catatan: Kombinasi `[periode_id, kelas_id]` didefinisikan sebagai Unique.*

---

### 11. Tabel minggu_penilaian

Tabel `minggu_penilaian` digunakan untuk merinci rentang penilaian mingguan di dalam satu periode penilaian tertentu.

**Tabel 11. Struktur Tabel Minggu Penilaian**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_minggu` | VARCHAR(10) | *Primary key*, ID unik mingguan |
| 2 | `periode_id` | VARCHAR(10) | *Foreign key* ke `periode_penilaian.id_periode` (*Cascade on delete*) |
| 3 | `minggu_ke` | INTEGER | Urutan minggu keberapa dalam periode |
| 4 | `tema` | VARCHAR(100) | Tema pembelajaran pada minggu bersangkutan (*Nullable*) |
| 5 | `tanggal_mulai` | DATE | Tanggal mulai minggu penilaian |
| 6 | `tanggal_selesai` | DATE | Tanggal selesai minggu penilaian |
| 7 | `status` | VARCHAR(20) | Status minggu penilaian (Default: 'draft') |
| 8 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 9 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

*Catatan: Kombinasi `[periode_id, minggu_ke]` didefinisikan sebagai Unique.*

---

### 12. Tabel jadwal_subkriteria

Tabel `jadwal_subkriteria` berfungsi untuk menjadwalkan subkriteria apa saja yang akan dinilai oleh guru pada minggu penilaian tertentu.

**Tabel 12. Struktur Tabel Jadwal Subkriteria**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_jadwal_sub` | VARCHAR(10) | *Primary key*, ID unik jadwal subkriteria |
| 2 | `minggu_id` | VARCHAR(10) | *Foreign key* ke `minggu_penilaian.id_minggu` (*Cascade on delete*) |
| 3 | `subkriteria_id` | VARCHAR(10) | *Foreign key* ke `subkriteria.id_subkriteria` (*Cascade on delete*) |
| 4 | `urutan` | INTEGER | Urutan tampilan subkriteria (*Nullable*) |
| 5 | `wajib` | BOOLEAN | Status wajib dinilai atau opsional (Default: *true*) |
| 6 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 7 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

*Catatan: Kombinasi `[minggu_id, subkriteria_id]` didefinisikan sebagai Unique.*

---

### 13. Tabel penilaian_mingguan

Tabel `penilaian_mingguan` digunakan untuk menyimpan entri nilai mingguan siswa per subkriteria yang dijadwalkan, lengkap dengan koordinat fuzzy dan catatan guru.

**Tabel 13. Struktur Tabel Penilaian Mingguan**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_penilaian` | VARCHAR(10) | *Primary key*, ID unik penilaian mingguan |
| 2 | `jadwal_sub_id` | VARCHAR(10) | *Foreign key* ke `jadwal_subkriteria.id_jadwal_sub` (*Cascade on delete*) |
| 3 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` (*Cascade on delete*) |
| 4 | `guru_id` | VARCHAR(10) | *Foreign key* ke `users.id_user` selaku guru penilai (*Cascade on delete*) |
| 5 | `kategori_id` | VARCHAR(10) | *Foreign key* ke `kategori_nilai.id_kategori` (*Nullable, Null on delete*) |
| 6 | `nilai_l` | DOUBLE | Batas bawah nilai fuzzy (*Nullable*) |
| 7 | `nilai_m` | DOUBLE | Nilai tengah fuzzy (*Nullable*) |
| 8 | `nilai_u` | DOUBLE | Batas atas nilai fuzzy (*Nullable*) |
| 9 | `nilai_crisp` | DOUBLE | Nilai tegas (crisp) yang diperoleh (*Nullable*) |
| 10 | `catatan` | TEXT | Catatan deskripsi perkembangan mingguan siswa (*Nullable*) |
| 11 | `status` | VARCHAR(20) | Status nilai mingguan (Default: 'draft') |
| 12 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 13 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

*Catatan: Kombinasi `[jadwal_sub_id, siswa_id]` didefinisikan sebagai Unique.*

---

### 14. Tabel portofolio

Tabel `portofolio` digunakan untuk mendokumentasikan karya atau bukti hasil belajar (portofolio) siswa pada minggu tertentu.

**Tabel 14. Struktur Tabel Portofolio**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_portofolio` | VARCHAR(10) | *Primary key*, ID unik portofolio |
| 2 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` (*Cascade on delete*) |
| 3 | `guru_id` | VARCHAR(10) | *Foreign key* ke `users.id_user` selaku guru pembuat (*Cascade on delete*) |
| 4 | `minggu_id` | VARCHAR(10) | *Foreign key* ke `minggu_penilaian.id_minggu` (*Cascade on delete*) |
| 5 | `judul` | VARCHAR(100) | Judul portofolio/kegiatan |
| 6 | `deskripsi` | TEXT | Penjelasan detail karya/kegiatan siswa (*Nullable*) |
| 7 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 15. Tabel portofolio_images

Tabel `portofolio_images` digunakan untuk menyimpan file gambar/foto dokumentasi pendukung portofolio siswa (satu portofolio dapat memiliki beberapa gambar).

**Tabel 15. Struktur Tabel Portofolio Images**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_portofolio_images` | VARCHAR(10) | *Primary key*, ID unik gambar portofolio |
| 2 | `portofolio_id` | VARCHAR(10) | *Foreign key* ke `portofolio.id_portofolio` (*Cascade on delete*) |
| 3 | `file_path` | VARCHAR(255) | Path penyimpanan file foto di server/storage |

---

### 16. Tabel template_rekomendasi_umum

Tabel `template_rekomendasi_umum` menyimpan draf teks rekomendasi umum untuk hasil akhir evaluasi perkembangan anak berdasarkan kategori akhirnya (MB, BSH, BSB).

**Tabel 16. Struktur Tabel Template Rekomendasi Umum**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_template_umum` | VARCHAR(10) | *Primary key*, ID unik template umum |
| 2 | `kategori` | VARCHAR(10) | Kategori hasil akhir (MB, BSH, BSB) |
| 3 | `isi` | TEXT | Isi teks draf saran/rekomendasi umum |
| 4 | `prioritas` | VARCHAR(20) | Tingkat prioritas penggunaan (Default: 'biasa') |
| 5 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 6 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 17. Tabel evaluasi

Tabel `evaluasi` menyimpan ringkasan hasil penilaian akhir/evaluasi siswa untuk suatu periode penilaian, yang dihitung menggunakan metode SPK Fuzzy SMART serta divalidasi oleh guru.

**Tabel 17. Struktur Tabel Evaluasi**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_evaluasi` | VARCHAR(10) | *Primary key*, ID unik evaluasi |
| 2 | `periode_id` | VARCHAR(10) | *Foreign key* ke `periode_penilaian.id_periode` |
| 3 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` |
| 4 | `template_umum_id` | VARCHAR(10) | *Foreign key* ke `template_rekomendasi_umum.id_template_umum` (*Nullable, Null on delete*) |
| 5 | `nilai_akhir` | DOUBLE | Nilai akumulasi akhir hasil SPK |
| 6 | `kategori_akhir` | VARCHAR(10) | Kategori hasil akhir evaluasi (MB, BSH, BSB) |
| 7 | `kategori_rekomendasi_sistem` | VARCHAR(10) | Kategori murni hasil rekomendasi dari sistem SPK (*Nullable*) |
| 8 | `kategori_keputusan_guru` | VARCHAR(10) | Kategori keputusan akhir berdasarkan validasi guru (*Nullable*) |
| 9 | `rekomendasi` | TEXT | Gabungan catatan rekomendasi saran perkembangan (*Nullable*) |
| 10 | `catatan_guru` | TEXT | Catatan khusus dari guru wali kelas (*Nullable*) |
| 11 | `status_validasi` | ENUM | Status peninjauan evaluasi oleh guru: 'menunggu_review', 'disetujui_guru' (Default: 'menunggu_review') |
| 12 | `tanggal_validasi` | TIMESTAMP | Tanggal dilakukannya validasi hasil (*Nullable*) |
| 13 | `id_guru_validator` | VARCHAR(10) | *Foreign key* ke `users.id_user` selaku guru validator (*Nullable, Null on delete*) |
| 14 | `is_final` | BOOLEAN | Penanda apakah rapor evaluasi sudah dikunci/final (Default: *false*) |
| 15 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

*Catatan: Kombinasi `[periode_id, siswa_id]` didefinisikan sebagai Unique.*

---

### 18. Tabel detail_evaluasi

Tabel `detail_evaluasi` berisi detail nilai per-subkriteria untuk setiap evaluasi siswa pada suatu periode, digunakan untuk merekam performa di setiap subkriteria.

**Tabel 18. Struktur Tabel Detail Evaluasi**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_detail_evaluasi` | VARCHAR(10) | *Primary key*, ID unik detail evaluasi |
| 2 | `evaluasi_id` | VARCHAR(10) | *Foreign key* ke `evaluasi.id_evaluasi` (*Cascade on delete*) |
| 3 | `subkriteria_id` | VARCHAR(10) | *Foreign key* ke `subkriteria.id_subkriteria` |
| 4 | `nilai_crisp` | DOUBLE | Nilai crisp rata-rata dari penilaian mingguan |
| 5 | `nilai_normalisasi` | DOUBLE | Nilai ternormalisasi sesuai metode SMART (*Nullable*) |
| 6 | `kategori` | VARCHAR(10) | Kategori nilai subkriteria tersebut (*Nullable*) |
| 7 | `rekomendasi_detail` | TEXT | Catatan rekomendasi detail subkriteria (*Nullable*) |
| 8 | `bobot_snapshot` | DOUBLE | Snapshot bobot subkriteria saat evaluasi diproses |
| 9 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 19. Tabel template_rekomendasi

Tabel `template_rekomendasi` menyimpan koleksi draf saran/rekomendasi untuk subkriteria tertentu berdasarkan kategori capaian siswa.

**Tabel 19. Struktur Tabel Template Rekomendasi**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_template` | VARCHAR(10) | *Primary key*, ID unik template rekomendasi subkriteria |
| 2 | `subkriteria_id` | VARCHAR(10) | *Foreign key* ke `subkriteria.id_subkriteria` (*Nullable, Null on delete*) |
| 3 | `kategori` | VARCHAR(10) | Kategori capaian perkembangan (MB, BSH, BSB) |
| 4 | `isi` | TEXT | Narasi teks rekomendasi/saran pembelajaran |
| 5 | `prioritas` | VARCHAR(20) | Prioritas penggunaan kalimat (Default: 'sedang') |
| 6 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 7 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |

---

### 20. Tabel rekomendasi

Tabel `rekomendasi` digunakan untuk menyimpan pemetaan rekomendasi subkriteria terpilih yang dilampirkan langsung pada evaluasi siswa.

**Tabel 20. Struktur Tabel Rekomendasi**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_rekomendasi` | VARCHAR(10) | *Primary key*, ID unik penugasan rekomendasi |
| 2 | `evaluasi_id` | VARCHAR(10) | *Foreign key* ke `evaluasi.id_evaluasi` (*Cascade on delete*) |
| 3 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` |
| 4 | `subkriteria_id` | VARCHAR(10) | *Foreign key* ke `subkriteria.id_subkriteria` |
| 5 | `template_id` | VARCHAR(10) | *Foreign key* ke `template_rekomendasi.id_template` |
| 6 | `kategori_hasil` | VARCHAR(10) | Kategori capaian perkembangan yang dinilai |
| 7 | `catatan_guru` | TEXT | Catatan penyesuaian/tambahan dari guru (*Nullable*) |
| 8 | `created_at` | TIMESTAMP | Waktu pembuatan data (Default: *CURRENT_TIMESTAMP*) |

---

### 21. Tabel laporan_evaluasi

Tabel `laporan_evaluasi` digunakan untuk menyimpan file rapor belajar siswa (dalam bentuk PDF) yang sudah digenerate agar dapat diunduh oleh wali murid atau dicetak oleh guru.

**Tabel 21. Struktur Tabel Laporan Evaluasi**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_laporan` | VARCHAR(10) | *Primary key*, ID unik laporan |
| 2 | `evaluasi_id` | VARCHAR(10) | *Foreign key* ke `evaluasi.id_evaluasi` |
| 3 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` |
| 4 | `kelas_id` | VARCHAR(10) | *Foreign key* ke `kelas.id_kelas` |
| 5 | `tahun_ajaran_id` | VARCHAR(10) | *Foreign key* ke `tahun_ajaran.id_tahun_ajaran` |
| 6 | `semester` | VARCHAR(20) | Semester laporan (contoh: 'Ganjil' / 'Genap') |
| 7 | `file_path` | VARCHAR(255) | Path lokasi penyimpanan file PDF laporan (*Nullable*) |
| 8 | `created_at` | TIMESTAMP | Waktu pembuatan laporan (Default: *CURRENT_TIMESTAMP*) |

---

### 22. Tabel wali_siswa

Tabel `wali_siswa` merupakan tabel pivot many-to-many yang menghubungkan akun user dengan peran wali murid kepada siswa yang bersangkutan.

**Tabel 22. Struktur Tabel Wali Siswa**

| No | Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- | :--- |
| 1 | `id_wali_siswa` | VARCHAR(10) | *Primary key*, ID unik relasi wali-siswa |
| 2 | `user_id` | VARCHAR(10) | *Foreign key* ke `users.id_user` (*Cascade on delete*) |
| 3 | `siswa_id` | VARCHAR(10) | *Foreign key* ke `siswa.id_siswa` (*Cascade on delete*) |
| 4 | `created_at` | TIMESTAMP | Waktu pembuatan data (*Nullable*) |
| 5 | `updated_at` | TIMESTAMP | Waktu pembaruan data (*Nullable*) |
