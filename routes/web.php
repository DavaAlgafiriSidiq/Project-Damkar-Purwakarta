<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Analytics\DashboardController;
use App\Http\Controllers\Analytics\ChartDataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Petugas\KejadianKebakaranController as PetugasKejadianController;
use App\Http\Controllers\Admin\VerifikasiKejadianController as AdminVerifikasiController;

/*
|--------------------------------------------------------------------------
| Web Routes — Project Sistem Pencatatan Damkar Purwakarta
|--------------------------------------------------------------------------
*/

// ── Publik ──────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('analytics.dashboard');
});

// Modul Analitik (Dashboard Publik)
Route::get('/dashboard-analitik', [DashboardController::class, 'index'])->name('analytics.dashboard');

// Endpoint JSON untuk Chart
Route::prefix('api/analytics')->name('analytics.chart.')->group(function () {
    Route::get('/tren-kebakaran-bulanan', [ChartDataController::class, 'trenKebakaranBulanan'])->name('tren-kebakaran-bulanan');
    Route::get('/tren-rescue-bulanan', [ChartDataController::class, 'trenRescueBulanan'])->name('tren-rescue-bulanan');
    Route::get('/distribusi-objek-kebakaran', [ChartDataController::class, 'distribusiObjekKebakaran'])->name('distribusi-objek-kebakaran');
    Route::get('/distribusi-penyebab-kebakaran', [ChartDataController::class, 'distribusiPenyebabKebakaran'])->name('distribusi-penyebab-kebakaran');
    Route::get('/sebaran-per-kecamatan', [ChartDataController::class, 'sebaranPerKecamatan'])->name('sebaran-per-kecamatan');
    Route::get('/sebaran-rescue-per-kecamatan', [ChartDataController::class, 'sebaranRescuePerKecamatan'])->name('sebaran-rescue-per-kecamatan');
    Route::get('/distribusi-jenis-rescue', [ChartDataController::class, 'distribusiJenisRescue'])->name('distribusi-jenis-rescue');
    Route::get('/tren-komparasi-bulanan', [ChartDataController::class, 'trenKomparasiBulanan'])->name('tren-komparasi-bulanan');
    Route::get('/ringkasan-statistik', [ChartDataController::class, 'ringkasanStatistik'])->name('ringkasan-statistik');
    Route::get('/live-alert', [ChartDataController::class, 'liveAlert'])->name('live-alert');
});

// ── Authentication ──────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ── Khusus Petugas (Entry Data Kejadian) ─────────────────────────────────
// Dilindungi middleware 'auth' dan 'role:petugas'
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Redirect dashboard petugas ke halaman input / riwayat laporan
    Route::get('/dashboard', function () {
        return redirect()->route('petugas.kejadian.index');
    })->name('dashboard');

    // Modul Input Kejadian
    Route::get('/kejadian', [PetugasKejadianController::class, 'index'])->name('kejadian.index');
    Route::get('/kejadian/create', [PetugasKejadianController::class, 'create'])->name('kejadian.create');
    Route::post('/kejadian', [PetugasKejadianController::class, 'store'])->name('kejadian.store');
    Route::get('/kejadian/{id}/edit', [PetugasKejadianController::class, 'edit'])->name('kejadian.edit');
    Route::put('/kejadian/{id}', [PetugasKejadianController::class, 'update'])->name('kejadian.update');
});


// ── Khusus Admin (Verifikasi & Manajemen Data) ───────────────────────────
// Dilindungi middleware 'auth' dan 'role:admin'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Redirect dashboard admin ke halaman verifikasi
    Route::get('/dashboard', function () {
        return redirect()->route('admin.verifikasi.index');
    })->name('dashboard');

    // Modul Verifikasi & Manajemen Data
    Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}/edit', [AdminVerifikasiController::class, 'edit'])->name('verifikasi.edit');
    Route::put('/verifikasi/{id}', [AdminVerifikasiController::class, 'update'])->name('verifikasi.update');
    Route::post('/verifikasi/{id}/approve', [AdminVerifikasiController::class, 'approve'])->name('verifikasi.approve');
    Route::delete('/verifikasi/{id}', [AdminVerifikasiController::class, 'destroy'])->name('verifikasi.destroy');
});