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

        $last = Jadwal::orderBy('ID_JADWAL', 'desc')->first();

        if ($last) {
            $num = (int) substr($last->ID_JADWAL, 4);
            $next = $num + 1;
        } else {
            $next = 1;
        }

        $validated['ID_JADWAL'] = 'JDW-' . str_pad($next, 3, '0', STR_PAD_LEFT);

        Jadwal::create($validated);
        return back()->with('success', 'Jadwal berhasil ditambahkan');
    }


    public function show($id)
    {
        return Jadwal::with(['karyawan', 'cabang'])->find($id);
    }

     public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'EMAIL' => 'required|exists:karyawan,EMAIL',
            'ID_CABANG' => 'required|exists:cabang,ID_CABANG',
            'TANGGAL' => 'required|date',
            'JAM_MULAI' => 'required',
            'JAM_SELESAI' => 'required'
        ]);

        $jadwal->update($validated);

        return back()->with('success', 'Jadwal berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $jadwal = Jadwal::find($id);

        if (!$jadwal) {
            return back()->with('error', 'Data jadwal tidak ditemukan!');
        }

        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }
}