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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // ================================================================
    // 1. PENYIMPANAN DATA TRANSAKSI (POS)
    // ================================================================
    public function store(Request $request)
    {
        $request->validate([
            'total_bayar'         => 'required|integer|min:0',
            'metode'              => 'required|string|in:Tunai,QRIS',
            'items'               => 'required|array|min:1',
            'items.*.id_produk'   => 'required|string|max:10',
            'items.*.jml_item'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $id_transaksi = 'TRX' . Str::upper(Str::random(7));
            $userEmail    = Auth::user()->email;

            // A. Simpan Header Transaksi
            $transaksi = Transaksi::create([
                'ID_TRANSAKSI'     => $id_transaksi,
                'EMAIL'            => $userEmail,
                'TOTAL_BAYAR'      => $request->total_bayar,
                'DATETIME'         => now(),
                'METODE_PEMBAYARAN'=> $request->metode,
            ]);

            // B. Simpan Detail Transaksi
            $details = [];
            foreach ($request->items as $item) {
                $details[] = new DetailTransaksi([
                    'ID_TRANSAKSI' => $id_transaksi,
                    'ID_PRODUK'    => $item['id_produk'],
                    'JML_ITEM'     => $item['jml_item'],
                ]);
            }

            $transaksi->detailtransaksi()->saveMany($details);

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Transaksi berhasil dicatat.',
                'id'            => $id_transaksi,
                'redirect_url'  => route('barista.riwayat'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat transaksi. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ================================================================
    // 2. DASHBOARD BARISTA
    // ================================================================
    public function indexDashboardBarista()
    {
        $today     = now()->toDateString();
        $userEmail = Auth::user()->email;

        $query = Transaksi::where('EMAIL', $userEmail)
                          ->whereDate('DATETIME', $today);

        $penjualan_shift_ini = $query->sum('TOTAL_BAYAR');

        $total_items_terjual = (clone $query)
            ->join('detailtransaksi', 'transaksi.ID_TRANSAKSI', '=', 'detailtransaksi.ID_TRANSAKSI')
            ->sum('detailtransaksi.JML_ITEM');

        $schedules = [
            [
                'tanggal' => $today,
                'nama'    => Auth::user()->name ?? 'Barista',
                'lokasi'  => 'Taman Bungkul',
                'jam'     => '08:00 - 16:00'
            ],
        ];

        return view('pages.dashboard.barista', compact(
            'penjualan_shift_ini',
            'total_items_terjual',
            'schedules'
        ));
    }

    // ================================================================
    // 3. RIWAYAT TRANSAKSI (Admin & Barista)
    // ================================================================
    public function indexRiwayat(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());

        $email    = Auth::user()->email;
        $is_admin = Auth::check() && Auth::user()->role === 'Admin';

        // Query untuk tabel
        $riwayats = Transaksi::with(['karyawan', 'detailtransaksi.menu'])
            ->when(!$is_admin, fn($q) => $q->where('EMAIL', $email))
            ->whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
            ->orderBy('DATETIME', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Query ringkasan
        $summary = Transaksi::when(!$is_admin, fn($q) => $q->where('EMAIL', $email))
            ->whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);

        $total_pendapatan = $summary->sum('TOTAL_BAYAR');
        $pendapatan_tunai = (clone $summary)->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris  = (clone $summary)->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        $total_items_terjual = DetailTransaksi::whereIn(
            'ID_TRANSAKSI',
            $summary->pluck('ID_TRANSAKSI')
        )->sum('JML_ITEM');

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
        $email    = Auth::user()->email;

        $query = Transaksi::with(['detailtransaksi.menu'])
            ->where('EMAIL', $email)
            ->whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);

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

    // ================================================================
    // 4. EXPORT EXCEL
    // ================================================================
    public function exportExcel(Request $request)
    {
        $fromDate   = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate     = $request->input('to_date', now()->toDateString());
        $is_admin   = Auth::user()->role === 'Admin';
        $email      = Auth::user()->email;
        $rolePrefix = $is_admin ? 'Admin' : 'Barista';

        $query = Transaksi::whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
            ->when(!$is_admin, fn($q) => $q->where('EMAIL', $email));

        $fileName = "Laporan_Excel_{$rolePrefix}_{$fromDate}_sd_{$toDate}.xlsx";

        return Excel::download(new TransaksiExport($query), $fileName);
    }

    // ================================================================
    // 5. CETAK PDF
    // ================================================================
    public function cetakLaporan(Request $request)
    {
        $fromDate   = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate     = $request->input('to_date', now()->toDateString());
        $is_admin   = Auth::user()->role === 'Admin';
        $email      = Auth::user()->email;
        $rolePrefix = $is_admin ? 'Admin' : 'Barista';

        $riwayats = Transaksi::with(['karyawan', 'detailtransaksi.menu'])
            ->whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
            ->when(!$is_admin, fn($q) => $q->where('EMAIL', $email))
            ->orderBy('DATETIME', 'desc')
            ->get();

        $total_pendapatan = $riwayats->sum('TOTAL_BAYAR');
        $pendapatan_tunai = $riwayats->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris  = $riwayats->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        $pdf = Pdf::loadView('reports.laporan_transaksi_pdf', compact(
            'riwayats',
            'fromDate',
            'toDate',
            'total_pendapatan',
            'pendapatan_tunai',
            'pendapatan_qris',
            'rolePrefix'
        ));

        return $pdf->download("Laporan_PDF_{$rolePrefix}_{$fromDate}_sd_{$toDate}.pdf");
    }

    // ================================================================
    // 6. CRUD DASAR
    // ================================================================
    public function index()
    {
        return Transaksi::with('menu', 'karyawan')->get();
    }

    public function show($id)
    {
        return Transaksi::with('menu', 'karyawan')->find($id);
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
        DetailTransaksi::where('ID_TRANSAKSI', $id)->delete();
        return Transaksi::destroy($id);
    }

    public function totalpendapatan()
    {
        return Transaksi::whereBetween('DATETIME', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('TOTAL_BAYAR');
    }
}
