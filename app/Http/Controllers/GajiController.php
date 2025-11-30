<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        return Gaji::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'             => 'required|string',
            'PERIODE'           => 'required|string',
            'TOTAL_GAJI_POKOK'  => 'required|integer',
            'TOTAL_BONUS'       => 'required|integer',
            'TOTAL_KOMPENSASI'  => 'required|integer',
        ]);

        $validated['TOTAL_GAJI_AKHIR'] = 
            $request->TOTAL_GAJI_POKOK + 
            $request->TOTAL_BONUS + 
            $request->TOTAL_KOMPENSASI;

        return Gaji::create($validated);
    }

    public function show($id)
    {
        return Gaji::find($id);
    }

    public function update(Request $request, $id)
    {
        $gaji = Gaji::find($id);

        $validated = $request->validate([
            'PERIODE'           => 'sometimes|string',
            'TOTAL_GAJI_POKOK'  => 'sometimes|integer',
            'TOTAL_BONUS'       => 'sometimes|integer',
            'TOTAL_KOMPENSASI'  => 'sometimes|integer',
        ]);

        $gaji->fill($validated);

        $gaji->TOTAL_GAJI_AKHIR = 
            $gaji->TOTAL_GAJI_POKOK + 
            $gaji->TOTAL_BONUS + 
            $gaji->TOTAL_KOMPENSASI;

        $gaji->save();

        return $gaji;
    }

    public function destroy($id)
    {
        return Gaji::destroy($id);
    }
}