<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\RombongController;

/*
|--------------------------------------------------------------------------
| LOGIN ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('auth.login'))->name('login.form');

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


/*
|--------------------------------------------------------------------------
| EXPORT ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn () => view('pages.dashboard.admin'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CRUD KARYAWAN
    |--------------------------------------------------------------------------
    */
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee/store', [KaryawanController::class, 'store'])->name('employee.store');
    Route::put('/employee/{email}', [KaryawanController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{email}', [KaryawanController::class, 'destroy'])->name('employee.destroy');

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT TRANSAKSI (Admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/history', [TransaksiController::class, 'indexRiwayat'])->name('history');

    /*
    |--------------------------------------------------------------------------
    | CRUD MENU
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
    | LOKASI, CABANG, ROMBONG
    |--------------------------------------------------------------------------
    */
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    Route::post('/cabang', [CabangController::class, 'store']);
    Route::put('/cabang/{id}', [CabangController::class, 'update']);
    Route::delete('/cabang/{id}', [CabangController::class, 'destroy']);

    Route::post('/rombong', [RombongController::class, 'store']);
    Route::put('/rombong/{id}', [RombongController::class, 'update']);
    Route::delete('/rombong/{id}', [RombongController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | INVENTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/inventory', fn () => view('pages.inventory'))->name('inventory');

    /*
    |--------------------------------------------------------------------------
    | MONITORING ABSENSI
    |--------------------------------------------------------------------------
    */
    Route::get('/absensi-monitoring', [AbsensiController::class, 'indexPulang'])
        ->name('admin.absensi.monitoring');
});


/*
|--------------------------------------------------------------------------
| BARISTA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'barista'])
    ->prefix('barista')
    ->name('barista.')
    ->group(function () {

    Route::get('/dashboard', [TransaksiController::class, 'indexDashboardBarista'])->name('dashboard');

    Route::get('/pos', [MenuController::class, 'pos'])->name('pos');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    Route::get('/absensi', [AbsensiController::class, 'indexDatang'])->name('absensi.index');
    Route::post('/absensi/datang', [AbsensiController::class, 'storeDatang'])->name('absensi.storeDatang');
    Route::post('/absensi/pulang', [AbsensiController::class, 'storePulang'])->name('absensi.storePulang');

    Route::get('/riwayat', [TransaksiController::class, 'indexRiwayat'])->name('riwayat');
});

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
