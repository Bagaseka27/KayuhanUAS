<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Karyawan; // Diperlukan untuk menghitung staf aktif
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
        // 1. Filter Tahun
        $selectedYear = $request->input('tahun', Carbon::now()->year);
        
        // 2. Metrik Dashboard (untuk bulan saat ini)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ambil data transaksi bulan ini
        $transactionsThisMonth = Transaksi::whereYear('datetime', $currentYear)
            ->whereMonth('datetime', $currentMonth)
            ->get();
        
        // --- Metrik ---
        
        // TOTAL OMSET BULAN INI
        $totalOmset = $transactionsThisMonth->sum('total_bayar');
        
        // KARYAWAN AKTIF (Asumsi: status role bukan 'Owner')
        $activeStaff = Karyawan::where('role', '!=', 'Owner')->count();
        
        // PROFIT BERSIH (Sangat Disederhanakan: Profit = 50% dari Omset, ganti sesuai rumus Anda)
        // Profit sebenarnya harus dihitung dari total_bayar - harga_dasar_per_item
        $profitBersih = $totalOmset * 0.50; 
        
        // 3. Data Tren Penjualan Bulanan (untuk Chart)
        $salesTrendData = $this->getMonthlySalesTrend($selectedYear);

        // 🟢 Kirim data ke View Admin
        return view('pages.dashboard.admin', [
            'totalOmset' => $totalOmset,
            'profitBersih' => $profitBersih,
            'activeStaff' => $activeStaff,
            'salesTrendData' => $salesTrendData,
            'selectedYear' => $selectedYear,
            
            // Contoh data untuk widget (silakan ganti dengan data real)
            'targetProfit' => 20000000,
            'omsetLastMonth' => 40000000, 
        ]);
    }

    /**
     * Mengambil data total penjualan bulanan untuk chart tren.
     * @param int $year Tahun yang difilter
     * @return array
     */
    protected function getMonthlySalesTrend(int $year): array
    {
        // 1. Ambil data penjualan per bulan dari tabel transaksi
        $sales = Transaksi::select(
                DB::raw('MONTH(datetime) as month'),
                DB::raw('SUM(total_bayar) as total_sales')
            )
            ->whereYear('datetime', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Siapkan array untuk semua 12 bulan (default 0)
        $monthlySales = array_fill(1, 12, 0); 
        
        // 3. Isi data penjualan ke array bulanan
        foreach ($sales as $sale) {
            $monthlySales[$sale->month] = (int) $sale->total_sales;
        }

        // 4. Siapkan format final untuk Chart.js (dengan nama bulan)
        $labels = [];
        $dataSales = [];
        // Menggunakan nama bulan yang disingkat untuk tampilan chart
        $monthNames = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $monthNames[$i];
            $dataSales[] = $monthlySales[$i];
        }

        return [
            'labels' => $labels,
            'data' => $dataSales,
            'year' => $year,
        ];
    }
}