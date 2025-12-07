<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Auth;
use App\Exports\TransaksiExport; 
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    // Method untuk menyimpan data Transaksi dari Kasir (POS)
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
            // Perbaikan ID Truncation: Menggunakan ID maksimal 10 karakter
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

    // Method BARU: Untuk Dashboard Barista (Mengambil kalkulasi hari ini)
    public function indexDashboardBarista()
    {
        $today = now()->toDateString();
        $userEmail = Auth::user()->email; 
        
        // Query dasar: filter transaksi Barista yang login pada hari ini
        $query = Transaksi::where('EMAIL', $userEmail)
                          ->whereDate('DATETIME', $today);

        // 1. Total Pendapatan Hari Ini
        $penjualan_shift_ini = $query->sum('TOTAL_BAYAR');

        // 2. Total Item Terjual Hari Ini (Perlu JOIN)
        $total_items_terjual = (clone $query)
                                    ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
                                    ->sum('detailtransaksi.JML_ITEM');

        // Data Jadwal (Anda harus mengambil dari Model Jadwal jika sudah ada)
        // Untuk saat ini, kita gunakan data dummy atau data minimal
        $schedules = [
            ['tanggal' => $today, 'nama' => Auth::user()->name ?? 'Barista', 'lokasi' => 'Taman Bungkul', 'jam' => '08:00 - 16:00'],
        ];

        return view('pages.dashboard.barista', compact('penjualan_shift_ini', 'total_items_terjual', 'schedules'));
    }


    // Method untuk menampilkan halaman Riwayat Transaksi (Untuk Admin dan Barista)
    public function indexRiwayat(Request $request)
    {
        // Filter tanggal
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
    }
    public function indexRiwayatBarista(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());

        $email = Auth::user()->email;

        $query = Transaksi::with(['detailtransaksi.menu'])
                        ->where('EMAIL', $email)
                        ->whereBetween('DATETIME', [
                                $fromDate . ' 00:00:00',
                                $toDate . ' 23:59:59'
                        ]);

        $total_pendapatan = (clone $query)->sum('TOTAL_BAYAR');
        $pendapatan_tunai = (clone $query)->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris  = (clone $query)->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        $riwayats = $query->orderBy('DATETIME', 'desc')->paginate(20);

        return view('pages.history_barista', compact(
            'riwayats',
            'total_pendapatan',
            'pendapatan_tunai',
            'pendapatan_qris',
            'fromDate',
            'toDate'
        ));
    }



    
    // ... (Method CRUD dasar lainnya)
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
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

        $totalOmsetBulanIni = Transaksi::whereBetween('DATETIME', [$startOfMonth, $endOfMonth])
                                         ->sum('TOTAL_BAYAR');
                                         
        return $totalOmsetBulanIni;
    }
    public function export()
    {
        $namaFile = 'laporan_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new TransaksiExport, $namaFile);
    }
}