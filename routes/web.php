<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\IbuHamilController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\VitaminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth routes
Auth::routes(['register' => false]);

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Balita
Route::resource('balita', BalitaController::class);
Route::get('balita/{balita}/pemeriksaan/create', [BalitaController::class, 'createPemeriksaan'])->name('balita.pemeriksaan.create');
Route::post('balita/{balita}/pemeriksaan', [BalitaController::class, 'storePemeriksaan'])->name('balita.pemeriksaan.store');
Route::get('balita/{balita}/imunisasi/create', [BalitaController::class, 'createImunisasi'])->name('balita.imunisasi.create');
Route::post('balita/{balita}/imunisasi', [BalitaController::class, 'storeImunisasi'])->name('balita.imunisasi.store');
Route::get('balita/{balita}/vitamin/create', [BalitaController::class, 'createVitamin'])->name('balita.vitamin.create');
Route::post('balita/{balita}/vitamin', [BalitaController::class, 'storeVitamin'])->name('balita.vitamin.store');
Route::get('balita/{balita}/export-pdf', [BalitaController::class, 'exportPDF'])->name('balita.export-pdf');
Route::get('balita-export-excel', [BalitaController::class, 'exportExcel'])->name('balita.export-excel');

// Ibu Hamil
Route::resource('ibu-hamil', IbuHamilController::class);
Route::get('ibu-hamil/{ibuHamil}/pemeriksaan/create', [IbuHamilController::class, 'createPemeriksaan'])->name('ibu-hamil.pemeriksaan.create');
Route::post('ibu-hamil/{ibuHamil}/pemeriksaan', [IbuHamilController::class, 'storePemeriksaan'])->name('ibu-hamil.pemeriksaan.store');
Route::get('ibu-hamil/{ibuHamil}/export-pdf', [IbuHamilController::class, 'exportPDF'])->name('ibu-hamil.export-pdf');
Route::get('ibu-hamil-export-excel', [IbuHamilController::class, 'exportExcel'])->name('ibu-hamil.export-excel');

// Jadwal Kegiatan
Route::resource('jadwal-kegiatan', JadwalKegiatanController::class);
Route::post('jadwal-kegiatan/{jadwalKegiatan}/update-status', [JadwalKegiatanController::class, 'updateStatus'])->name('jadwal-kegiatan.update-status');
Route::get('kalender-kegiatan', [JadwalKegiatanController::class, 'calendar'])->name('jadwal-kegiatan.calendar');

// Pengumuman
Route::resource('pengumuman', PengumumanController::class);
Route::post('pengumuman/{pengumuman}/toggle-status', [PengumumanController::class, 'toggleStatus'])->name('pengumuman.toggle-status');

// Laporan
Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('laporan/balita', [LaporanController::class, 'balita'])->name('laporan.balita');
Route::get('laporan/ibu-hamil', [LaporanController::class, 'ibuHamil'])->name('laporan.ibu-hamil');
Route::get('laporan/pemeriksaan-balita', [LaporanController::class, 'pemeriksaanBalita'])->name('laporan.pemeriksaan-balita');
Route::get('laporan/pemeriksaan-ibu-hamil', [LaporanController::class, 'pemeriksaanIbuHamil'])->name('laporan.pemeriksaan-ibu-hamil');
Route::get('laporan/imunisasi', [LaporanController::class, 'imunisasi'])->name('laporan.imunisasi');
Route::get('laporan/vitamin', [LaporanController::class, 'vitamin'])->name('laporan.vitamin');
Route::get('laporan/kegiatan', [LaporanController::class, 'kegiatan'])->name('laporan.kegiatan');

// Admin only routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Users Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Posyandu Management
    Route::resource('posyandu', PosyanduController::class);
    
    // Imunisasi Management
    Route::resource('imunisasi', ImunisasiController::class);
    Route::post('imunisasi/{imunisasi}/toggle-status', [ImunisasiController::class, 'toggleStatus'])->name('imunisasi.toggle-status');
    
    // Vitamin Management
    Route::resource('vitamin', VitaminController::class);
    Route::post('vitamin/{vitamin}/toggle-status', [VitaminController::class, 'toggleStatus'])->name('vitamin.toggle-status');
});

// User Profile
Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');