<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
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

        // Ambil jadwal barista hari ini
        $schedule = Jadwal::with(['karyawan', 'cabang'])
            ->where('EMAIL', $email)
            ->whereDate('TANGGAL', $today)
            ->first();

        return view('pages.dashboard.barista', [
            'penjualan_shift_ini' => $penjualan_shift_ini,
            'total_items_terjual' => $total_items_terjual,
            'schedule' => $schedule
        ]);
    }
}
