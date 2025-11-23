<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL' => 'required|string|max:50',
            'NAMA' => 'required|string|max:100'
        ]);

        return Karyawan::create($validated);
    }

    public function show($email)
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->findOrFail($email);
    }

    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::findOrFail($email);
        $karyawan->update($request->all());
        return $karyawan;
    }

    public function destroy($email)
    {
        return Karyawan::destroy($email);
    }
}
