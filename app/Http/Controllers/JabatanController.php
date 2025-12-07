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
            'GAJI_POKOK_PER_HARI' => 'required|numeric',
            'BONUS_PER_CUP' => 'required|numeric',
        ]);

        Jabatan::create($validated);

        return redirect()->back()->with('success','Jabatan berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);
        $validated = $request->validate([
            'NAMA_JABATAN' => 'sometimes|string|max:50',
            'GAJI_POKOK_PER_HARI' => 'sometimes|numeric',
            'BONUS_PER_CUP' => 'sometimes|numeric',
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
