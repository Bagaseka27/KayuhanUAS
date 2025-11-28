<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'       => 'required|email|unique:karyawan,EMAIL|max:50', 
            'NAMA'        => 'required|string|max:100',
            'ID_JABATAN'  => 'required|exists:jabatan,ID_JABATAN',
            'PASSWORD'    => 'required|string|min:6',
            'NO_HP'       => 'required|string|max:15',
        ]);

        $validated['PASSWORD'] = Hash::make($request->PASSWORD);

        return Karyawan::create($validated);
    }

    public function show($email)
    {
        return Karyawan::with(['jabatan', 'cabang', 'rombong'])->find($email);
    }

    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::find($email);
        $rules = [
            'NAMA'       => 'sometimes|string|max:100',
            'ID_JABATAN' => 'sometimes|exists:jabatan,ID_JABATAN',
            'NO_HP'      => 'sometimes|string|max:15',
            'PASSWORD'   => 'sometimes|string|min:6'
        ];
        $validated = $request->validate($rules);
        return $karyawan->update($validated);
    }

    public function destroy($email)
    {
        return Karyawan::destroy($email);
    }
}

