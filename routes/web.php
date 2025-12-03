<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
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

    Route::get('/employees', function () {
        return view('pages.employee');
    })->name('employees');

    Route::get('/history', function () {
        return view('pages.history');
    })->name('history');

    Route::get('/inventory', function () {
        return view('pages.inventory');
    })->name('inventory');

    Route::get('/locations', function () {
        return view('pages.location');
    })->name('locations');
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
});

// --------------------
// PROFILE (Bisa diakses admin dan barista)
// --------------------
Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');