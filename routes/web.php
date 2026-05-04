<?php

use Illuminate\Support\Facades\Route;

// ─── AUTHENTICATION ──────────────────────────────────
Route::get('/', function () {
    return view('welcome', [
        'totalSiswa' => \App\Models\Siswa::count(),
        'totalGuru' => \App\Models\User::where('role', 'guru')->count(),
        'totalKelas' => \App\Models\Kelas::count(),
        'totalEvaluasi' => \App\Models\Evaluasi::count(),
    ]);
})->name('welcome');
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/api/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// ─── SHARED PROFILE ───────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ─── ADMIN MODULE ─────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('user', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('user/{user}/toggle', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('user.toggle');

    // Tahun Ajaran
    Route::resource('tahun_ajaran', \App\Http\Controllers\Admin\TahunAjaranController::class);
    Route::patch('tahun_ajaran/{tahun_ajaran}/toggle', [\App\Http\Controllers\Admin\TahunAjaranController::class, 'toggleStatus'])->name('tahun_ajaran.toggle');
    // Kelas
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);
    // Siswa
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class);
    Route::resource('kriteria', \App\Http\Controllers\Admin\KriteriaController::class)->parameters(['kriteria' => 'kriteria']);
    Route::resource('subkriteria', \App\Http\Controllers\Admin\SubkriteriaController::class)->parameters(['subkriteria' => 'subkriteria']);
    Route::resource('kategori-nilai', \App\Http\Controllers\Admin\KategoriNilaiController::class);
    // Template Rekomendasi Wizard
    Route::get('template-rekomendasi', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'index'])->name('template-rekomendasi.index');
    Route::post('template-rekomendasi/generate', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'generate'])->name('template-rekomendasi.generate');
    Route::get('template-rekomendasi/generated', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'showGenerated'])->name('template-rekomendasi.generated');
    Route::put('template-rekomendasi/{template}', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'update'])->name('template-rekomendasi.update');
    Route::delete('template-rekomendasi/{template}', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'destroy'])->name('template-rekomendasi.destroy');
    Route::get('template-rekomendasi/{kriteria}', [\App\Http\Controllers\Admin\TemplateRekomendasiController::class, 'showSubkriteria'])->name('template-rekomendasi.subkriteria');
    Route::resource('template-rekomendasi-umum', \App\Http\Controllers\Admin\TemplateRekomendasiUmumController::class);
    Route::resource('periode', \App\Http\Controllers\Admin\PeriodeController::class);
    Route::post('periode/{periode}/finalize', [\App\Http\Controllers\Admin\PeriodeController::class, 'finalize'])->name('periode.finalize');
    Route::patch('periode/{periode}/toggle', [\App\Http\Controllers\Admin\PeriodeController::class, 'toggle'])->name('periode.toggle');
    Route::resource('minggu', \App\Http\Controllers\Admin\MingguPenilaianController::class);
    Route::patch('minggu/{id}/status', [\App\Http\Controllers\Admin\MingguPenilaianController::class, 'changeStatus'])->name('minggu.status');
    Route::get('/jadwal_subkriteria', fn () => view('admin.jadwal_subkriteria'))->name('jadwal_subkriteria');

    // Hasil Evaluasi SPK
    Route::get('/hasil-evaluasi', [\App\Http\Controllers\Admin\HasilEvaluasiController::class, 'index'])->name('hasil_evaluasi');
    Route::get('/hasil-evaluasi/{evaluasi}', [\App\Http\Controllers\Admin\HasilEvaluasiController::class, 'show'])->name('hasil_evaluasi.show');
});

// ─── GURU MODULE ──────────────────────────────────────
Route::prefix('guru')->name('guru.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard',         [\App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/penilaian',         [\App\Http\Controllers\Guru\PenilaianController::class, 'index'])->name('penilaian');
    Route::post('/penilaian',        [\App\Http\Controllers\Guru\PenilaianController::class, 'store'])->name('penilaian.store');
    Route::post('/penilaian/finalize-week/{id}', [\App\Http\Controllers\Guru\PenilaianController::class, 'finalizeWeek'])->name('penilaian.finalize-week');
    Route::get('/riwayat',           [\App\Http\Controllers\Guru\PenilaianController::class, 'riwayat'])->name('riwayat');
    Route::get('/riwayat/{siswa}',   [\App\Http\Controllers\Guru\PenilaianController::class, 'riwayatDetail'])->name('riwayat.detail');
    Route::get('/rekap',             [\App\Http\Controllers\Guru\PenilaianController::class, 'rekap'])->name('rekap');
    Route::get('/hasil-evaluasi',    [\App\Http\Controllers\Guru\PenilaianController::class, 'hasilEvaluasi'])->name('hasil-evaluasi');
    Route::get('/hasil-evaluasi/{siswa}', [\App\Http\Controllers\Guru\PenilaianController::class, 'hasilEvaluasiDetail'])->name('hasil-evaluasi.detail');
    Route::get('/hasil-evaluasi/{siswa}/cetak', [\App\Http\Controllers\Guru\PenilaianController::class, 'cetakLaporan'])->name('hasil-evaluasi.cetak');
    Route::post('/hasil-evaluasi/{evaluasi}/catatan', [\App\Http\Controllers\Guru\PenilaianController::class, 'updateCatatanEvaluasi'])->name('hasil-eval-catatan.update');
    Route::get('/laporan',           [\App\Http\Controllers\Guru\PenilaianController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/generate-word', [\App\Http\Controllers\Guru\PenilaianController::class, 'generateWordReport'])->name('laporan.generate-word');
    Route::get('/detail_penilaian',  fn () => view('guru.detail_penilaian'))->name('detail_penilaian');

    // Modul Daftar Siswa
    Route::get('/siswa', [\App\Http\Controllers\Guru\SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{id}', [\App\Http\Controllers\Guru\SiswaController::class, 'show'])->name('siswa.show');

    // Modul Portofolio
    Route::resource('portofolio', \App\Http\Controllers\Guru\PortofolioController::class);
    Route::delete('portofolio/image/{id}', [\App\Http\Controllers\Guru\PortofolioController::class, 'destroyImage'])->name('portofolio.image.destroy');
});

// ─── KEPSEK MODULE ────────────────────────────────────
Route::prefix('kepsek')->name('kepsek.')->middleware(['auth', 'role:kepala_sekolah'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kepsek\KepsekController::class, 'dashboard'])->name('dashboard');
    Route::get('/siswa', [\App\Http\Controllers\Kepsek\KepsekController::class, 'siswa'])->name('siswa');
    Route::get('/evaluasi', [\App\Http\Controllers\Kepsek\KepsekController::class, 'evaluasi'])->name('evaluasi');
    Route::get('/perkembangan', [\App\Http\Controllers\Kepsek\KepsekController::class, 'perkembangan'])->name('perkembangan');
    Route::get('/analisis', [\App\Http\Controllers\Kepsek\KepsekController::class, 'analisis'])->name('analisis');
    Route::get('/laporan', [\App\Http\Controllers\Kepsek\KepsekController::class, 'laporan'])->name('laporan');
    Route::get('/siswa/{id}', [\App\Http\Controllers\Kepsek\KepsekController::class, 'siswaDetail'])->name('siswa.show');
    Route::post('/laporan/generate-word', [\App\Http\Controllers\Kepsek\KepsekController::class, 'generateWordReport'])->name('laporan.generate-word');
});

// ─── WALI MURID MODULE ────────────────────────────────
Route::prefix('wali')->name('wali.')->middleware(['auth', 'role:wali_murid'])->group(function () {
    Route::get('/dashboard',    [\App\Http\Controllers\Wali\WaliController::class, 'dashboard'])->name('dashboard');
    Route::get('/perkembangan', [\App\Http\Controllers\Wali\WaliController::class, 'perkembangan'])->name('perkembangan');
    Route::get('/portofolio',   [\App\Http\Controllers\Wali\WaliController::class, 'portofolio'])->name('portofolio');
    Route::get('/evaluasi',     [\App\Http\Controllers\Wali\WaliController::class, 'evaluasi'])->name('evaluasi');
    Route::get('/laporan',      [\App\Http\Controllers\Wali\WaliController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/generate-word', [\App\Http\Controllers\Wali\WaliController::class, 'generateWordReport'])->name('laporan.generate-word');
});
