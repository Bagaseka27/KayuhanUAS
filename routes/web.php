<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController; // Tambahkan ini jika belum ada


// --------------------
// LOGIN ROUTE
// --------------------
Route::get('/', function () {
    return view('auth.login');
})->name('login.form'); // GANTI nama route

Route::post('/login', [LoginController::class, 'login'])->name('login'); // POST untuk proses login

Route::get('/login', [LoginController::class, 'index'])->name('login.view'); // Opsional

//logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


// --------------------
// DASHBOARD
// --------------------
Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');


// --------------------
// MANAJEMEN MENU
// --------------------
Route::get('/menu', function () {
    return view('pages.menu');
})->name('menu');


// --------------------
// POS (Barista)
// --------------------
Route::get('/pos', function () {
    return view('pages.pos');
})->name('pos');


// --------------------
// DATA KARYAWAN
// --------------------
Route::get('/employees', function () {
    return view('pages.employee');
})->name('employees');


// --------------------
// HISTORY TRANSAKSI
// --------------------
Route::get('/history', function () {
    return view('pages.history');
})->name('history');


// --------------------
// INVENTORY
// --------------------
Route::get('/inventory', function () {
    return view('pages.inventory');
})->name('inventory');


// --------------------
// LOKASI CABANG
// --------------------
Route::get('/locations', function () {
    return view('pages.locations');
})->name('locations');
