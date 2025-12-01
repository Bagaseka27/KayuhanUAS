<?php

namespace App\Http\Controllers;

use App\Models\StokGudang;
use Illuminate\Http\Request;

class StokGudangController extends Controller
{
    public function index()
    {
        return StokGudang::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_BARANG'   => 'required|string|max:10|unique:stokgudang,ID_BARANG',
            'NAMA_BARANG' => 'required|string|max:100',
            'JUMLAH'      => 'required|integer|min:0', 
        ]);

        return StokGudang::create($validated);
    }

    public function update(Request $request, $id)
    {
        $barang = StokGudang::find($id);
        
        $validated = $request->validate([
            'NAMA_BARANG' => 'sometimes|string|max:100',
        ]);

        $barang->update($validated);
        return $barang;
    }

    public function destroy($id)
    {
        return StokGudang::destroy($id);
    }


    public function barangMasuk(Request $request, $id)
    {
        $barang = StokGudang::find($id);

        if (!$barang) return "Barang tidak ditemukan";

        $request->validate(['JUMLAH_MASUK' => 'required|integer|min:1']);

        $barang->JUMLAH = $barang->JUMLAH + $request->JUMLAH_MASUK;
        $barang->save();

        return $barang;
    }

    public function barangKeluar(Request $request, $id)
    {
        $barang = StokGudang::find($id);

        if (!$barang) return "Barang tidak ditemukan";

        $request->validate(['JUMLAH_KELUAR' => 'required|integer|min:1']);

        if ($barang->JUMLAH < $request->JUMLAH_KELUAR) {
            return "Stok tidak cukup!"; 
        }

        $barang->JUMLAH = $barang->JUMLAH - $request->JUMLAH_KELUAR;
        $barang->save();

        return $barang;
    }
}