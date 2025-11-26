<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        return Transaksi::with('menu')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_TRANSAKSI' => 'required|string|max:10',
            'EMAIL' => 'nullable|string|max:50',
            'JUMLAH_ITEM' => 'nullable|integer',
            'HARGA_ITEM' => 'nullable|integer',
            'DATETIME' => 'nullable|date',
            'TOTAL_BAYAR' => 'nullable|integer',
            'METODE_PEMBAYARAN' => 'nullable|string|max:20',
            'MENU' => 'array' // ID produk
        ]);

        $transaksi = Transaksi::create($validated);

        if ($request->has('MENU')) {
            $transaksi->menu()->sync($request->MENU);
        }

        return $transaksi->load('menu');
    }

    public function show($id)
    {
        return Transaksi::with('menu')->findOrFail($id);
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
}
