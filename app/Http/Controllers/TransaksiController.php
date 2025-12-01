<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Exports\TransaksiExport; 
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index()
    {
        return Transaksi::with('menu', 'karyawan')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_TRANSAKSI' => 'required|string|max:10|unique:transaksi,ID_TRANSAKSI',
            'EMAIL' => 'required|exists:karyawan,EMAIL',
            'JUMLAH_ITEM'=> 'required|integer|min:1',
            'HARGA_ITEM'=> 'required|integer', 
            'DATETIME'=> 'required|date',
            'TOTAL_BAYAR'=> 'required|integer',
            'METODE_PEMBAYARAN'=> 'required|string|max:20',
            'MENU'=> 'array',           
        ]);

        $transaksi = Transaksi::create($validated);

        if ($request->has('MENU')) {
            $transaksi->menu()->sync($request->MENU);
        }

        return $transaksi->load('menu');
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
        return Transaksi::destroy($id);
    }

    public function export()
    {
        $namaFile = 'laporan_transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new TransaksiExport, $namaFile);
    }

}
