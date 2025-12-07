<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RombongStok;

class StokRombongController extends Controller
{
    public function store(Request $req)
    {
        $req->validate([
            'barang_id'  => 'required',
            'rombong_id' => 'required',
            'stok_awal'  => 'required|integer|min:0',
            'stok_akhir' => 'required|integer|min:0',
        ]);

        RombongStok::create($req->all());

        return back()->with('success', 'Stok rombong ditambahkan');
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'stok_awal'  => 'required|integer|min:0',
            'stok_akhir' => 'required|integer|min:0',
        ]);

        RombongStok::find($id)->update($req->all());

        return back()->with('success', 'Stok rombong diperbarui');
    }

    public function destroy($id)
    {
        RombongStok::find($id)->delete();
        return back()->with('success', 'Stok rombong dihapus');
    }
    public function batchStore(Request $req)
    {
        $req->validate([
            'rombong_tujuan' => 'required',
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        foreach ($req->items as $item) {
            RombongStok::create([
                'barang_id'  => $item['id'],
                'rombong_id' => $req->rombong_tujuan,
                'stok_awal'  => $item['qty'],
                'stok_akhir' => $item['qty'], // default sama
            ]);
        }

        return back()->with('success', 'Stok rombong berhasil ditambahkan');
    }

}
