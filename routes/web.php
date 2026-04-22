<?php

use Illuminate\Support\Facades\Route;

// ─── AUTHENTICATION ──────────────────────────────────
Route::get('/', fn () => view('auth.login'))->name('login');
Route::post('/api/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// ─── ADMIN MODULE ─────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('user', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('user/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('user.toggle-status');

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
    Route::resource('periode', \App\Http\Controllers\Admin\PeriodeController::class);
    Route::post('periode/{periode}/finalize', [\App\Http\Controllers\Admin\PeriodeController::class, 'finalize'])->name('periode.finalize');
    Route::patch('periode/{periode}/toggle', [\App\Http\Controllers\Admin\PeriodeController::class, 'toggle'])->name('periode.toggle');
    Route::resource('minggu', \App\Http\Controllers\Admin\MingguPenilaianController::class);
    Route::patch('minggu/{id}/status', [\App\Http\Controllers\Admin\MingguPenilaianController::class, 'changeStatus'])->name('minggu.status');
    Route::get('/jadwal_subkriteria', fn () => view('admin.jadwal_subkriteria'))->name('jadwal_subkriteria');
});

// ─── GURU MODULE ──────────────────────────────────────
Route::prefix('guru')->name('guru.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard',         [\App\Http\Controllers\Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/penilaian',         [\App\Http\Controllers\Guru\PenilaianController::class, 'index'])->name('penilaian');
    Route::post('/penilaian',        [\App\Http\Controllers\Guru\PenilaianController::class, 'store'])->name('penilaian.store');
    Route::get('/riwayat',           [\App\Http\Controllers\Guru\PenilaianController::class, 'riwayat'])->name('riwayat');
    Route::get('/rekap',             [\App\Http\Controllers\Guru\PenilaianController::class, 'rekap'])->name('rekap');
    Route::get('/laporan',           [\App\Http\Controllers\Guru\PenilaianController::class, 'laporan'])->name('laporan');
    Route::get('/detail_penilaian',  fn () => view('guru.detail_penilaian'))->name('detail_penilaian');
});

// ─── KEPSEK MODULE ────────────────────────────────────
Route::prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', fn () => view('kepsek.dashboard'))->name('dashboard');
    Route::get('/evaluasi',  fn () => view('kepsek.evaluasi'))->name('evaluasi');
    Route::get('/laporan',   fn () => view('kepsek.laporan'))->name('laporan');
});

// ─── WALI MURID MODULE ────────────────────────────────
Route::prefix('wali')->name('wali.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard',    [App\Http\Controllers\Wali\WaliController::class, 'dashboard'])->name('dashboard');
    Route::get('/perkembangan', [App\Http\Controllers\Wali\WaliController::class, 'perkembangan'])->name('perkembangan');
    Route::get('/evaluasi',     [App\Http\Controllers\Wali\WaliController::class, 'evaluasi'])->name('evaluasi');
    Route::get('/laporan',      [App\Http\Controllers\Wali\WaliController::class, 'laporan'])->name('laporan');
});
