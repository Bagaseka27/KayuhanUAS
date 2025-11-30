<?php

namespace App\Http\Controllers;

use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // BAGIAN ABSEN DATANG
    public function indexDatang()
    {
        return AbsenDatang::with('karyawan')->get();
    }

    public function storeDatang(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'           => 'required|exists:karyawan,EMAIL',
            'FOTO'            => 'required|string',
            'DATETIME_DATANG' => 'required|date_format:Y-m-d H:i:s',
        ]);

        return AbsenDatang::create($validated);
    }

    // BAGIAN ABSEN PULANG
    public function indexPulang()
    {
        return AbsenPulang::with('karyawan')->get();
    }

    public function storePulang(Request $request)
    {
        $validated = $request->validate([
            'EMAIL'           => 'required|exists:karyawan,EMAIL',
            'FOTO'            => 'required|string',
            'DATETIME_PULANG' => 'required|date_format:Y-m-d H:i:s',
        ]);

        return AbsenPulang::create($validated);
    }

}