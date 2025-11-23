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
        return Jadwal::create($request->all());
    }

    public function show($id)
    {
        return Jadwal::with(['karyawan', 'cabang'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->all());
        return $jadwal;
    }

    public function destroy($id)
    {
        return Jadwal::destroy($id);
    }
}
