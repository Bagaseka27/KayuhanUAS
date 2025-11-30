<?php

use Illuminate\Support\Facades\Route;


#----------------------------------------
Route::get('/', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/login', [LoginController::class, 'index'])->name('login.form');

// --- 2. DASHBOARD (Halaman Utama) ---
Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

// --- 3. MANAJEMEN MENU ---
Route::get('/menu', function () {
    return view('pages.menu'); // Pastikan kamu buat file: resources/views/pages/menu.blade.php
})->name('menu');

// --- 4. KASIR / POS (Khusus Barista) ---
Route::get('/pos', function () {
    return view('pages.pos'); // Pastikan kamu buat file: resources/views/pages/pos.blade.php
})->name('pos');

// --- 5. DATA KARYAWAN (Khusus Admin) ---
Route::get('/employees', function () {
    return view('pages.employee'); // Pastikan kamu buat file: resources/views/pages/employee.blade.php
})->name('employees');

// --- 6. HISTORY TRANSAKSI ---
Route::get('/history', function () {
    return view('pages.history'); // Buat file: resources/views/pages/history.blade.php
})->name('history');

// --- 7. STOK / INVENTORY ---
Route::get('/inventory', function () {
    return view('pages.inventory'); // Buat file: resources/views/pages/inventory.blade.php
})->name('inventory');

// --- 8. LOKASI & CABANG ---
Route::get('/locations', function () {
    return view('pages.locations'); // Buat file: resources/views/pages/locations.blade.php
})->name('locations');