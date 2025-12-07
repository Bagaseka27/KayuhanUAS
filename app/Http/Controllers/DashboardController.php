<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Karyawan; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama (untuk peran Admin/Owner).
     */
    public function index(Request $request)
    {
        // PENTING: Inisialisasi Timezone Database ke Jakarta (+07:00)
        DB::statement("SET time_zone = '+07:00'");
        
        $appTimezone = config('app.timezone'); 
        
        // 1. Filter Tahun
        $selectedYear = $request->input('tahun', 2025);
        
        // 2. Metrik Dashboard (untuk bulan saat ini)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ambil data transaksi bulan ini (Metrik Stat Card)
        $transactionsThisMonth = Transaksi::whereRaw('YEAR(DATETIME) = ?', [$currentYear])
            ->whereRaw('MONTH(DATETIME) = ?', [$currentMonth])
            ->get();
        
        // --- Metrik ---

        
        // TOTAL OMSET BULAN INI
        $totalOmset = $transactionsThisMonth->sum('total_bayar');
        
        // KARYAWAN AKTIF (Asumsi: status role bukan 'Owner')
        $activeStaff = Karyawan::where('role', '!=', 'Owner')->count();

        $totalOmset = $transactionsThisMonth->sum('TOTAL_BAYAR');
        $activeStaff = Karyawan::where('role', '!=', 'Owner')->count(); 

        $profitBersih = $totalOmset * 0.50; 
        
        // 3. Data Tren Penjualan Bulanan (untuk Chart)
        $salesTrendData = $this->getMonthlySalesTrend($selectedYear);

        // Kirim semua variabel yang diperlukan ke View Admin
        $adminCount = User::where('role', 'Admin')->count();
        $baristaCount = User::where('role', 'Barista')->count();

        return view('pages.dashboard.admin', [
            'totalOmset' => $totalOmset,
            'profitBersih' => $profitBersih,
            'activeStaff' => $adminCount + $baristaCount,

            'adminCount' => $adminCount,
            'baristaCount' => $baristaCount,

            'salesTrendData' => $salesTrendData,
            'selectedYear' => $selectedYear,
        ]);
    }

    /**
     * Mengambil data total penjualan bulanan untuk chart tren.
     * @param int $year Tahun yang difilter
     * @return array
     */
    protected function getMonthlySalesTrend(int $year): array
{
    DB::statement("SET time_zone = '+07:00'");

    // Ambil data penjualan per bulan
    $sales = Transaksi::select(
            DB::raw('MONTH(DATETIME) as month'),
            DB::raw('SUM(TOTAL_BAYAR) as total_sales')
        )
        ->whereRaw('YEAR(DATETIME) = ?', [$year])
        ->groupBy(DB::raw('MONTH(DATETIME)'))
        ->orderBy('month')
        ->get();

    // Array untuk 12 bulan
    $monthlySales = array_fill(1, 12, 0);

    // Isi data penjualan
    foreach ($sales as $sale) {
        $monthlySales[$sale->month] = (int) $sale->total_sales;
    }

    // Label bulan
    $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];

    $labels = [];
    $dataSales = [];

    for ($i = 1; $i <= 12; $i++) {
        $labels[] = $monthNames[$i];
        $dataSales[] = $monthlySales[$i];
    }

    // Data untuk Chart.js
    return [
        'labels' => $labels,
        'data' => $dataSales,
        'year' => $year,
    ];
}

}