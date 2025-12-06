<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use App\Models\Karyawan;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
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
// EXPORT ROUTES
// --------------------
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');

Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');

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

    Route::get('/history', [TransaksiController::class, 'indexRiwayat'])->name('history'); 

    Route::get('/inventory', function () {
        return view('pages.inventory');
    })->name('inventory');

    Route::get('/locations', [LocationController::class, 'index'])->name('location');

    Route::get('/absensi-monitoring', [App\Http\Controllers\AbsensiController::class, 'indexPulang'])->name('admin.absensi.monitoring');
});

// --------------------
// BARISTA ROUTES
// --------------------
Route::middleware(['auth', 'barista'])->prefix('barista')->name('barista.')->group(function () {
    
    // 1. Dashboard Barista 
    Route::get('/dashboard', [TransaksiController::class, 'indexDashboardBarista'])->name('dashboard');
    
    // 2. POS (Kasir)
    Route::get('/pos', function () {
        return view('pages.dashboard.pos');
    })->name('pos'); 

    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
    
    // 3. Manajemen Menu (Barista)
    // Route ini memanggil controller yang sama, menggunakan nama 'menu' untuk navigasi Barista.
    Route::get('/menu', [MenuController::class, 'index'])->name('menu'); 

    // 4. Presensi/Absensi
    Route::get('/absensi', [AbsensiController::class, 'indexDatang'])->name('absensi.index'); 
    
    // Proses Absensi Masuk (POST)
    Route::post('/absensi/datang', [AbsensiController::class, 'storeDatang'])->name('absensi.storeDatang');
    
    // Proses Absensi Pulang (POST)
    Route::post('/absensi/pulang', [AbsensiController::class, 'storePulang'])->name('absensi.storePulang');
    
    // 5. Riwayat Transaksi
    Route::get('/riwayat', [TransaksiController::class, 'indexRiwayat'])->name('riwayat'); 
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