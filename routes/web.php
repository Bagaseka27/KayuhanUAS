<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use App\Models\Karyawan;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Auth;

// --------------------
// LOGIN ROUTE
// --------------------
Route::get('/', function () {
    return view('auth.login');
})->name('login.form');

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/login', [LoginController::class, 'index'])->name('login.view');

// --------------------
// LOGOUT
// --------------------
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// --------------------
// ADMIN ROUTES
// --------------------
Route::middleware(['auth', 'admin'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('pages.dashboard.admin');
    })->name('dashboard');

    Route::get('/menu', function () {
        return view('pages.menu');
    })->name('menu');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employee');
    Route::get('/history', function () {
        return view('pages.history');
    })->name('history');

    Route::get('/inventory', function () {
        return view('pages.inventory');
    })->name('inventory');

    Route::get('/locations', function () {
        return view('pages.location');
    })->name('locations');

    Route::get('/absensi-monitoring', [App\Http\Controllers\AbsensiController::class, 'indexPulang'])->name('admin.absensi.monitoring');
});

// --------------------
// BARISTA ROUTES
// --------------------
Route::prefix('barista')->middleware(['auth', 'barista'])->group(function () {
    
    // 1. Dashboard Barista
    Route::get('/dashboard', function () {
        return view('pages.dashboard.barista');
    })->name('barista.dashboard');
    
    // 2. POS (Kasir)
    Route::get('/pos', function () {
        return view('pages.dashboard.pos');
    })->name('barista.pos');
    
    // 3. Manajemen Menu
    Route::get('/menu', function () {
        return view('pages.menu');
    })->name('barista.menu');
    
    // 4. Riwayat Transaksi
    Route::get('/riwayat', function () {
        return view('pages.riwayat');
    })->name('barista.riwayat');

    Route::get('/absensi', [AbsensiController::class, 'indexDatang'])->name('barista.absensi.index'); 
    
    // Proses Absensi Masuk (POST): Memanggil storeDatang()
    Route::post('/absensi/datang', [AbsensiController::class, 'storeDatang'])->name('barista.absensi.storeDatang');
    
    // Proses Absensi Pulang (POST): Memanggil storePulang()
    Route::post('/absensi/pulang', [AbsensiController::class, 'storePulang'])->name('barista.absensi.storePulang');
});

// --------------------
// PROFILE (Bisa diakses admin dan barista)
// --------------------
Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

Route::middleware('auth')->group(function () {
    // Route untuk update profil (sesuai action di form modal tadi)
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});