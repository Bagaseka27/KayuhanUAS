<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /**
     * SIMPAN TRANSAKSI (POS)
     * Menggunakan HTTP Client untuk QRIS agar tidak bentrok dengan versi SDK
     */
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
            $qrString     = null;
            $xenditId     = null;

            // LOGIKA QRIS (Langsung Tembak API Xendit)
            if ($request->metode === 'QRIS') {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode(env('XENDIT_SECRET_KEY') . ':'),
                ])->post('https://api.xendit.co/qr_codes', [
                    'external_id'  => $id_transaksi,
                    'type'         => 'DYNAMIC',
                    'callback_url' => 'https://webhook.site/kayuhan-uas-testing',
                    'amount'       => (int) $request->total_bayar,
                ]);

                if ($response->failed()) {
                    throw new \Exception("Xendit Error: " . $response->body());
                }

                $qrData   = $response->json();
                $qrString = $qrData['qr_string'];
                $xenditId = $qrData['id'];
            }

            // 1. Simpan Header Transaksi
            $transaksi = Transaksi::create([
                'ID_TRANSAKSI'      => $id_transaksi,
                'EMAIL'             => $userEmail,
                'TOTAL_BAYAR'       => $request->total_bayar,
                'DATETIME'          => now(),
                'METODE_PEMBAYARAN' => $request->metode,
                'STATUS'            => ($request->metode === 'QRIS') ? 'PENDING' : 'SUCCESS',
                'XENDIT_ID'         => $xenditId
            ]);

            // 2. Simpan Detail Transaksi
            foreach ($request->items as $item) {
                DetailTransaksi::create([
                    'ID_TRANSAKSI' => $id_transaksi,
                    'ID_PRODUK'    => $item['id_produk'],
                    'JML_ITEM'     => $item['jml_item'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Transaksi berhasil!',
                'id'           => $id_transaksi,
                'qr_string'    => $qrString, 
                'redirect_url' => ($request->metode === 'Tunai') ? route('barista.riwayat') : null,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RIWAYAT UNTUK BARISTA (DENGAN STATISTIK)
     */
    public function indexRiwayatBarista(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());
        $email    = Auth::user()->email;

        $baseQuery = Transaksi::where('EMAIL', $email)
            ->whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);

        // Hitung statistik untuk Dashboard/History
        $total_pendapatan = (clone $baseQuery)->where('STATUS', 'SUCCESS')->sum('TOTAL_BAYAR');
        $pendapatan_tunai = (clone $baseQuery)->where('STATUS', 'SUCCESS')->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris  = (clone $baseQuery)->where('STATUS', 'SUCCESS')->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        $riwayats = (clone $baseQuery)
            ->with(['detailtransaksi.menu'])
            ->orderBy('DATETIME', 'desc')
            ->paginate(20)
            ->appends($request->all());

        return view('pages.history', compact(
            'riwayats', 'fromDate', 'toDate', 
            'total_pendapatan', 'pendapatan_tunai', 'pendapatan_qris'
        ));
    }

    /**
     * RIWAYAT UNTUK ADMIN
     */
    public function indexRiwayat(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate   = $request->input('to_date', now()->toDateString());

        $baseQuery = Transaksi::whereBetween('DATETIME', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);

        $total_pendapatan = (clone $baseQuery)->where('STATUS', 'SUCCESS')->sum('TOTAL_BAYAR');
        $pendapatan_tunai = (clone $baseQuery)->where('STATUS', 'SUCCESS')->where('METODE_PEMBAYARAN', 'Tunai')->sum('TOTAL_BAYAR');
        $pendapatan_qris  = (clone $baseQuery)->where('STATUS', 'SUCCESS')->where('METODE_PEMBAYARAN', 'QRIS')->sum('TOTAL_BAYAR');

        $riwayats = (clone $baseQuery)
            ->with(['karyawan', 'detailtransaksi.menu'])
            ->orderBy('DATETIME', 'desc')
            ->paginate(20)
            ->appends($request->all());

        return view('pages.history', compact(
            'riwayats', 'fromDate', 'toDate', 
            'total_pendapatan', 'pendapatan_tunai', 'pendapatan_qris'
        ));
    }

    /**
     * CALLBACK XENDIT (WEBHOOK)
     */
    public function handleCallback(Request $request)
    {
        $data = $request->all();
        $transaksi = Transaksi::where('ID_TRANSAKSI', $data['external_id'])->first();

        if ($transaksi && ($data['status'] === 'COMPLETED' || $data['status'] === 'SUCCEEDED')) {
            $transaksi->update(['STATUS' => 'SUCCESS']);
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * HAPUS DATA
     */
    public function destroy($id)
    {
        DetailTransaksi::where('ID_TRANSAKSI', $id)->delete();
        Transaksi::destroy($id);
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}