<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;

class JabatanController extends Controller
{
    public function indexPage()
    {
        $jabatan = Jabatan::all();
        return view('pages.jabatan.index', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NAMA_JABATAN' => 'required|string|max:50',
            'UPAH_PER_JAM' => 'required|numeric|min:0',
            'BONUS_PENJUALAN_PER_CUP' => 'required|numeric|min:0',
        ]);

        // Handle manual input untuk NAMA_JABATAN
        if ($validated['NAMA_JABATAN'] === 'Lainnya') {
            $validated['NAMA_JABATAN'] = $request->input('NAMA_JABATAN_MANUAL', 'Jabatan Lainnya');
        }

        Jabatan::create($validated);

        return redirect()->back()->with('success','Jabatan berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $validated = $request->validate([
            'NAMA_JABATAN' => 'sometimes|string|max:50',
            'UPAH_PER_JAM' => 'sometimes|numeric|min:0',
            'BONUS_PENJUALAN_PER_CUP' => 'sometimes|numeric|min:0',
        ]);
        $jabatan->update($validated);
        return redirect()->back()->with('success','Jabatan berhasil diupdate');
    }

    public function destroy($id)
    {
        Jabatan::destroy($id);
        return redirect()->back()->with('success','Jabatan berhasil dihapus');
    }
}
