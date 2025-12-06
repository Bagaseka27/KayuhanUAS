<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        return Cabang::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_CABANG' => 'required|string|max:20|unique:cabang,ID_CABANG',
            'NAMA_LOKASI' => 'required|string|max:100'
        ]);

        Cabang::create($request->all());
        return back()->with('success', 'Cabang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);

        $request->validate([
            'NAMA_LOKASI' => 'required|string|max:100'
        ]);

        $cabang->update($request->only('NAMA_LOKASI'));

        return back()->with('success', 'Cabang berhasil diperbarui');
    }

    public function destroy($id)
    {
        Cabang::destroy($id);
        return back()->with('success', 'Cabang berhasil dihapus');
    }
}