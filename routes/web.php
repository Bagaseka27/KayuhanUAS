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
<<<<<<< HEAD
    return view('pages.menu');
=======
    return view('pages.menu'); 
>>>>>>> 4263384acb60caadc14d568c588c98f714f457ae
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
<<<<<<< HEAD
    return view('pages.employee');
=======
    return view('pages.employee'); 
>>>>>>> 4263384acb60caadc14d568c588c98f714f457ae
})->name('employees');


// --------------------
// HISTORY TRANSAKSI
// --------------------
Route::get('/history', function () {
<<<<<<< HEAD
    return view('pages.history');
=======
    return view('pages.history'); 
>>>>>>> 4263384acb60caadc14d568c588c98f714f457ae
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
<<<<<<< HEAD
})->name('locations');
=======
})->name('locations');
>>>>>>> 4263384acb60caadc14d568c588c98f714f457ae
