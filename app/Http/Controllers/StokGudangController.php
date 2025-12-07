<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokGudang;

class StokGudangController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'ID_BARANG'   => 'required',
            'NAMA_BARANG' => 'required',
            'MASUK'       => 'required|integer|min:0',
            'KELUAR'      => 'required|integer|min:0',
        ]);

        StokGudang::create([
            'ID_BARANG'   => $req->ID_BARANG,
            'NAMA_BARANG' => $req->NAMA_BARANG,
            'MASUK'       => $req->MASUK,
            'KELUAR'      => $req->KELUAR,
            'JUMLAH'      => $req->MASUK - $req->KELUAR,
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'NAMA_BARANG' => 'required',
            'MASUK'       => 'required|integer|min:0',
            'KELUAR'      => 'required|integer|min:0',
        ]);

        StokGudang::where('ID_BARANG', $id)->update([
            'NAMA_BARANG' => $req->NAMA_BARANG,
            'MASUK'       => $req->MASUK,
            'KELUAR'      => $req->KELUAR,
            'JUMLAH'      => $req->MASUK - $req->KELUAR,
        ]);

        return back()->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        StokGudang::where('ID_BARANG', $id)->delete();
        return back()->with('success', 'Barang berhasil dihapus');
    }
}
