<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Auth;
use App\Exports\TransaksiExport; // Pastikan class ini ada!
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;
// PENTING: Pastikan Anda telah menginstal dan mengimpor DomPDF
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // ------------------------------------------------------------------
    // 1. PENYIMPANAN DATA TRANSAKSI (POS)
    // ------------------------------------------------------------------
    public function store(Request $request)
    {
        // 1. Validasi Input Data dari POS
        $request->validate([
            'total_bayar' => 'required|integer|min:0',
            'metode' => 'required|string|in:Tunai,QRIS', 
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|string|max:10',
            'items.*.jml_item' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Membuat ID Transaksi unik
            $id_transaksi = 'TRX' . Str::upper(Str::random(7)); 
            $userEmail = Auth::user()->email; 

            // A. Simpan Header Transaksi (Tabel 'transaksi')
            $transaksi = Transaksi::create([
                'ID_TRANSAKSI' => $id_transaksi,
                'EMAIL' => $userEmail, 
                'TOTAL_BAYAR' => $request->total_bayar,
                'DATETIME' => now(), 
                'METODE_PEMBAYARAN' => $request->metode, 
            ]);

            // B. Simpan Detail Transaksi (Tabel 'detailtransaksi')
            $details = [];
            foreach ($request->items as $item) {
                $details[] = new DetailTransaksi([
                    'ID_TRANSAKSI' => $id_transaksi,
                    'ID_PRODUK' => $item['id_produk'],
                    'JML_ITEM' => $item['jml_item'],
                ]);
            }
            
            $transaksi->detailtransaksi()->saveMany($details); 

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Transaksi berhasil dicatat.', 
                'id' => $id_transaksi,
                'redirect_url' => route('barista.riwayat')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Gagal mencatat transaksi. Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ------------------------------------------------------------------
    // 2. DASHBOARD BARISTA
    // ------------------------------------------------------------------
    public function indexDashboardBarista()
    {
        $today = now()->toDateString();
        $userEmail = Auth::user()->email; 
        
        $query = Transaksi::where('EMAIL', $userEmail)
                          ->whereDate('DATETIME', $today);

        $penjualan_shift_ini = $query->sum('TOTAL_BAYAR');

        $total_items_terjual = (clone $query)
                                    ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
                                    ->sum('detailtransaksi.JML_ITEM');

        $schedules = [
            ['tanggal' => $today, 'nama' => Auth::user()->name ?? 'Barista', 'lokasi' => 'Taman Bungkul', 'jam' => '08:00 - 16:00'],
        ];

        return view('pages.dashboard.barista', compact('penjualan_shift_ini', 'total_items_terjual', 'schedules'));
    }

    // ------------------------------------------------------------------
    // 3. RIWAYAT TRANSAKSI (dengan Filter)
    // ------------------------------------------------------------------
    public function indexRiwayat(Request $request)
    {
<<<<<<< HEAD
        // Mendapatkan query dasar dengan eager loading relasi
        $query = Transaksi::with(['karyawan', 'detailtransaksi.menu']);
                            
        // --- 1. FILTER TANGGAL ---
=======
        // Filter tanggal
>>>>>>> f841a3dd4bc7efd6ccfde9cb96208e3cf57f460d
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());

        $is_admin = Auth::check() && Auth::user()->role === 'Admin';
        $email    = Auth::user()->email ?? null;

        // =============================
        // 1. QUERY DATA TABEL (PAGE)
        // =============================
        $riwayats = Transaksi::with(['karyawan', 'detailtransaksi.menu'])
            ->when(!$is_admin, fn($q) => $q->where('EMAIL', $email))
            ->whereBetween('DATETIME', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->orderBy('DATETIME','desc')
            ->paginate(20)
            ->appends($request->except('page'));

<<<<<<< HEAD
        // --- 3. KALKULASI RINGKASAN ---
        // Kalkulasi total pendapatan
        $total_pendapatan = (clone $query)->sum('TOTAL_BAYAR');
        
        // Gunakan clone query untuk kalkulasi non-tunai dan tunai
        $pendapatan_tunai = (clone $query)->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris = (clone $query)->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        // Total item terjual membutuhkan JOIN, jadi harus dihitung terpisah atau dihitung di blade
        // Kami akan membuang total_items_terjual di sini untuk menghindari JOIN yang tidak efisien pada query pagination utama.
        $total_items_terjual = 0; // Anda dapat menghapus ini atau menghitungnya dengan JOIN jika diperlukan

        // --- 4. Ambil Data Utama (dengan Pagination) ---
        $riwayats = $query->orderBy('DATETIME', 'desc')->paginate(15)->appends($request->except('page'));
        
        // --- 5. Tentukan View yang akan dipakai dan Compact Variabel ---
        $compactData = compact('riwayats', 'total_pendapatan', 'pendapatan_tunai', 'pendapatan_qris', 'fromDate', 'toDate', 'total_items_terjual');
        
        if ($is_admin) {
            return view('pages.history', $compactData);
        } else {
            // BARISTA menggunakan pages.riwayat
            return view('pages.riwayat', $compactData);
        }
=======
        // =============================
        // 2. QUERY RINGKASAN (TERPISAH)
        // =============================
        $summaryQuery = Transaksi::when(!$is_admin, fn($q) => $q->where('EMAIL', $email))
            ->whereBetween('DATETIME', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        $total_pendapatan = $summaryQuery->sum('TOTAL_BAYAR');

        $pendapatan_tunai = (clone $summaryQuery)
            ->where('METODE_PEMBAYARAN', 'Tunai')
            ->sum('TOTAL_BAYAR');

        $pendapatan_qris = (clone $summaryQuery)
            ->where('METODE_PEMBAYARAN', 'QRIS')
            ->sum('TOTAL_BAYAR');

        // Total item
        $total_items_terjual = DetailTransaksi::whereIn(
                'ID_TRANSAKSI',
                $summaryQuery->pluck('ID_TRANSAKSI')
            )->sum('JML_ITEM');

        // =============================
        // RETURN VIEW
        // =============================
        return view('pages.history', compact(
            'riwayats',
            'total_pendapatan',
            'pendapatan_tunai',
            'pendapatan_qris',
            'total_items_terjual',
            'fromDate',
            'toDate'
        ));
>>>>>>> f841a3dd4bc7efd6ccfde9cb96208e3cf57f460d
    }


    
    // ------------------------------------------------------------------
    // 4. EXPORT EXCEL (Maatwebsite/Laravel-Excel)
    // ------------------------------------------------------------------
    public function exportExcel(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        
        $is_admin = Auth::check() && Auth::user()->role === 'Admin';
        $userEmail = Auth::user()->email;
        $rolePrefix = $is_admin ? 'Admin' : 'Barista';

        // Buat query dasar berdasarkan filter
        $query = Transaksi::query();
        $query->whereBetween('DATETIME', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if (!$is_admin) {
            $query->where('EMAIL', $userEmail);
        }
        
        // Pastikan TransaksiExport diimplementasikan untuk menerima Query Builder
        $fileName = "Laporan_Excel_{$rolePrefix}_{$fromDate}_sd_{$toDate}.xlsx";

        return Excel::download(new TransaksiExport($query), $fileName);
    }
    
    // ------------------------------------------------------------------
    // 5. CETAK LAPORAN PDF (Barryvdh/DomPDF)
    // ------------------------------------------------------------------
    public function cetakLaporan(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        
        $is_admin = Auth::check() && Auth::user()->role === 'Admin';
        $userEmail = Auth::user()->email;
        $rolePrefix = $is_admin ? 'Admin' : 'Barista';

        // 1. Ambil data dengan filter yang sama
        $query = Transaksi::with(['karyawan', 'detailtransaksi.menu']);
        $query->whereBetween('DATETIME', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if (!$is_admin) {
            $query->where('EMAIL', $userEmail);
        }

        $riwayats = $query->orderBy('DATETIME', 'desc')->get();
        
        // 2. Kalkulasi Ringkasan
        $total_pendapatan = $riwayats->sum('TOTAL_BAYAR');
        $pendapatan_tunai = $riwayats->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris = $riwayats->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        // 3. Muat view PDF
        // View 'reports.laporan_transaksi_pdf' harus Anda buat terpisah
        $pdf = Pdf::loadView('reports.laporan_transaksi_pdf', compact(
            'riwayats', 
            'fromDate', 
            'toDate',
            'total_pendapatan',
            'pendapatan_tunai',
            'pendapatan_qris',
            'rolePrefix'
        ));

        // 4. Download PDF
        $fileName = "Laporan_PDF_{$rolePrefix}_{$fromDate}_sd_{$toDate}.pdf";
        return $pdf->download($fileName);
    }

    // ------------------------------------------------------------------
    // 6. METHOD CRUD DASAR
    // ------------------------------------------------------------------
    public function index()
    {
        return Transaksi::with('menu', 'karyawan')->get();
    }
    public function show($id)
    {
        return Transaksi::with('menu','karyawan')->find($id);
    }
    public function update(Request $request, $id)
    {
        $trx = Transaksi::findOrFail($id);
        $trx->update($request->all());

        if ($request->has('MENU')) {
            $trx->menu()->sync($request->MENU);
        }

        return $trx->load('menu');
    }
    public function destroy($id)
    {
        // Hapus detail transaksi terkait sebelum header
        DetailTransaksi::where('ID_TRANSAKSI', $id)->delete();
        return Transaksi::destroy($id);
    }
    public function totalpendapatan()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalOmsetBulanIni = Transaksi::whereBetween('DATETIME', [$startOfMonth, $endOfMonth])
                                         ->sum('TOTAL_BAYAR');
                                         
        return $totalOmsetBulanIni;
    }
}