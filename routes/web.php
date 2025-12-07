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
use App\Http\Controllers\StokGudangController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StokRombongController;
use App\Http\Controllers\JabatanController;


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
#Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn () => view('pages.dashboard.admin'))->name('dashboard');

    // ADMIN ROUTES (dalam middleware auth,admin)
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee/store', [KaryawanController::class, 'store'])->name('employee.store');
    Route::put('/employee/{email}', [KaryawanController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{email}', [KaryawanController::class, 'destroy'])->name('employee.destroy');

    // GAJI
    Route::post('/gaji/store', [GajiController::class, 'store'])->name('gaji.store');
    Route::put('/gaji/update/{id}', [GajiController::class, 'update'])->name('gaji.update');
    Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');

    // JABATAN
    Route::get('/jabatan', [JabatanController::class, 'indexPage'])->name('jabatan.index');
    Route::post('/jabatan/store', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::put('/jabatan/update/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/delete/{id}', [JabatanController::class, 'destroy'])->name('jabatan.delete');

    // JADWAL
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal/store', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/update/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/delete/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // API helper: ambil gaji_per_hari dan bonus_per_cup berdasarkan EMAIL karyawan
    Route::get('/api/jabatan-karyawan/{email}', function($email) {
        $k = \App\Models\Karyawan::with('jabatan')->find($email);
        if (!$k || !$k->jabatan) {
            return response()->json(['gaji_per_hari' => 0, 'bonus_per_cup' => 0]);
        }
        return response()->json([
            'gaji_per_hari' => $k->jabatan->GAJI_POKOK_PER_HARI,
            'bonus_per_cup' => $k->jabatan->BONUS_PER_CUP,
        ]);
    });


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


    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    // ========== GUDANG ==========
    Route::post('/inventory/gudang/store', [StokGudangController::class, 'store'])->name('gudang.store');
    Route::put('/inventory/gudang/update/{id}', [StokGudangController::class, 'update'])->name('gudang.update');
    Route::delete('/inventory/gudang/delete/{id}', [StokGudangController::class, 'destroy'])->name('gudang.delete');

    // ========== ROMBONG ==========
    Route::post('/inventory/rombong/store', [StokRombongController::class, 'store'])->name('rombong.store');
    Route::put('/inventory/rombong/update/{id}', [StokRombongController::class, 'update'])->name('rombong.update');
    Route::delete('/inventory/rombong/delete/{id}', [StokRombongController::class, 'destroy'])->name('rombong.delete');
    Route::post('/inventory/rombong/batch-store', [StokRombongController::class, 'batchStore'])
    ->name('rombong.batchStore');

   

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


Route::get('/transaksi/export/excel', [TransaksiController::class, 'exportExcel'])
    ->name('riwayat.export.excel');

Route::get('/transaksi/cetak/pdf', [TransaksiController::class, 'cetakLaporan'])
    ->name('riwayat.cetak.pdf');
