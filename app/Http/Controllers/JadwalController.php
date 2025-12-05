<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        return Jadwal::with(['karyawan', 'cabang'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'       => 'required|exists:karyawan,EMAIL',
            'ID_CABANG'   => 'required|exists:cabang,ID_CABANG',
            'TANGGAL'     => 'required|date',
            'JAM_MULAI'   => 'required',
            'JAM_SELESAI' => 'required',
        ]);

        // Convert input time (HH:MM) → (HH:MM:SS)
        $validated['JAM_MULAI'] = $validated['JAM_MULAI'] . ':00';
        $validated['JAM_SELESAI'] = $validated['JAM_SELESAI'] . ':00';

        return Jadwal::create($validated);
    }


    public function show($id)
    {
        return Jadwal::with(['karyawan', 'cabang'])->find($id);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'EMAIL'       => 'sometimes|exists:karyawan,EMAIL',
            'ID_CABANG'   => 'sometimes|exists:cabang,ID_CABANG',
            'TANGGAL'     => 'sometimes|date',
            'JAM_MULAI'   => 'sometimes',
            'JAM_SELESAI' => 'sometimes',
        ]);

        if (isset($validated['JAM_MULAI'])) {
            $validated['JAM_MULAI'] .= ':00';
        }
        if (isset($validated['JAM_SELESAI'])) {
            $validated['JAM_SELESAI'] .= ':00';
        }

        $jadwal->update($validated);

        return $jadwal;
    }


    public function destroy($id)
    {
        return Jadwal::destroy($id);
    }
}