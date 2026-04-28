<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardBaristaController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $email = Auth::user()->email;

        // Total pendapatan shift hari ini
        $penjualan_shift_ini = Transaksi::where('EMAIL', $email)
            ->whereDate('DATETIME', $today)
            ->sum('TOTAL_BAYAR');

        // Total item terjual hari ini
        $total_items_terjual = Transaksi::where('EMAIL', $email)
            ->whereDate('DATETIME', $today)
            ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
            ->sum('detailtransaksi.JML_ITEM');
        
        // MENU TERJUAL PER PRODUK
        $menu_terjual = DB::table('transaksi')
            ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
            ->join('menu', 'detailtransaksi.ID_PRODUK', '=', 'menu.ID_PRODUK')
            ->where('transaksi.EMAIL', $email)
            ->whereDate('transaksi.DATETIME', $today)
            ->select(
                'menu.NAMA_PRODUK',
                DB::raw('SUM(detailtransaksi.JML_ITEM) as total_terjual')
            )
            ->groupBy('menu.NAMA_PRODUK')
            ->orderByDesc('total_terjual')
            ->get();

        // Ambil jadwal barista hari ini
        $schedule = Jadwal::with(['karyawan', 'cabang'])
            ->where('EMAIL', $email)
            ->whereDate('TANGGAL', $today)
            ->first();

        return view('pages.dashboard.barista', [
            'penjualan_shift_ini' => $penjualan_shift_ini,
            'total_items_terjual' => $total_items_terjual,
            'menu_terjual' => $menu_terjual,
            'schedule' => $schedule
        ]);
    }
}
