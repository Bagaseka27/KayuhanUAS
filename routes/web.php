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

// Controllers
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\RombongController;


/*
|--------------------------------------------------------------------------
| LOGIN ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
})->name('login.form');

Route::get('/login', [LoginController::class, 'index'])->name('login.view');
Route::post('/login', [LoginController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
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


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('pages.dashboard.admin');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CRUD KARYAWAN
    |--------------------------------------------------------------------------
    */
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee/store', [KaryawanController::class, 'store'])->name('employee.store');
    Route::put('/employee/{email}', [KaryawanController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{email}', [KaryawanController::class, 'destroy'])->name('employee.destroy');


    Route::get('/employees', [EmployeeController::class, 'index'])->name('employee');

    Route::get('/history', [TransaksiController::class, 'indexRiwayat'])->name('history'); 

    /*
    |--------------------------------------------------------------------------
    | CRUD Menu
    |--------------------------------------------------------------------------
    */
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/menu/store', [MenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');


    /*
    |--------------------------------------------------------------------------
    | CRUD GAJI
    |--------------------------------------------------------------------------
    */
    Route::post('/gaji/store', [GajiController::class, 'store'])->name('gaji.store');
    Route::put('/gaji/update/{id}', [GajiController::class, 'update'])->name('gaji.update');
    Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');

    /*
    |--------------------------------------------------------------------------
    | CRUD JADWAL
    |--------------------------------------------------------------------------
    */
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal/store', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/update/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/delete/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');


    /*
    |--------------------------------------------------------------------------
    | LOKASI, INVENTORY, HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    Route::post('/cabang', [CabangController::class, 'store']);
    Route::put('/cabang/{id}', [CabangController::class, 'update']);
    Route::delete('/cabang/{id}', [CabangController::class, 'destroy']);

    Route::post('/rombong', [RombongController::class, 'store']);
    Route::put('/rombong/{id}', [RombongController::class, 'update']);
    Route::delete('/rombong/{id}', [RombongController::class, 'destroy']);



    Route::get('/history', fn () => view('pages.history'))->name('history');
    Route::get('/inventory', fn () => view('pages.inventory'))->name('inventory');


    /*
    |--------------------------------------------------------------------------
    | MONITORING ABSENSI
    |--------------------------------------------------------------------------
    */
    Route::get('/absensi-monitoring',
        [AbsensiController::class, 'indexPulang']
    )->name('admin.absensi.monitoring');
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
/*
|--------------------------------------------------------------------------
| PROFILE (Admin & Barista)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});