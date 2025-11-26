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
            'JAM_MULAI'   => 'required|date_format:H:i:s', 
            'JAM_SELESAI' => 'required|date_format:H:i:s|after:JAM_MULAI', 
        ]);

        $jadwal = Jadwal::create($validated);
    }

    public function show($id)
    {
        return Jadwal::with(['karyawan', 'cabang'])->find($id);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::find($id);
        
        $validated = $request->validate([
            'EMAIL'       => 'sometimes|exists:karyawan,EMAIL',
            'ID_CABANG'   => 'sometimes|exists:cabang,ID_CABANG',
            'TANGGAL'     => 'sometimes|date',
            'JAM_MULAI'   => 'sometimes|date_format:H:i:s',
            'JAM_SELESAI' => 'sometimes|date_format:H:i:s|after:JAM_MULAI',
        ]);

        $jadwal->update($validated);
        return $jadwal;
    }

    public function destroy($id)
    {
        return Jadwal::destroy($id);
    }
}