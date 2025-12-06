<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        Gaji::with('karyawan')->get();
        return view('gaji.index',compact('gajis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'             => 'required|string|exists:karyawan,EMAIL',
            'PERIODE'           => 'required|string',
            'JUMLAH_HARI_MASUK' => 'required|integer',
            'TOTAL_GAJI_POKOK'  => 'required|integer',
            'TOTAL_BONUS'       => 'required|integer',
            'TOTAL_KOMPENSASI'  => 'required|integer',
        ]);

        $validated['TOTAL_GAJI_AKHIR'] =
            $validated['TOTAL_GAJI_POKOK'] +
            $validated['TOTAL_BONUS'] +
            $validated['TOTAL_KOMPENSASI'];

        Gaji::create($validated);

        return redirect()->back()->with('success', 'Gaji berhasil ditambahkan');
    }



    public function show($id)
    {
        return Gaji::find($id);
    }

    public function update(Request $request, $id)
    {
        $gaji = Gaji::findOrFail($id);

        $validated = $request->validate([
            'PERIODE'           => 'sometimes|string',
            'JUMLAH_HARI_MASUK' => 'sometimes|integer',
            'TOTAL_GAJI_POKOK'  => 'sometimes|integer',
            'TOTAL_BONUS'       => 'sometimes|integer',
            'TOTAL_KOMPENSASI'  => 'sometimes|integer',
        ]);

        $gaji->update($validated);

        $gaji->TOTAL_GAJI_AKHIR =
            $gaji->TOTAL_GAJI_POKOK +
            $gaji->TOTAL_BONUS +
            $gaji->TOTAL_KOMPENSASI;

        $gaji->save();

        return redirect()->back()->with('success', 'Gaji berhasil diupdate');
    }


    public function destroy($id)
    {
        Gaji::destroy($id);
        return redirect()->back()->with('success', 'Gaji berhasil dihapus');
    }
}