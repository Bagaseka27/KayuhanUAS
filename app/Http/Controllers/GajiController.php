<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Karyawan;

class GajiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'EMAIL' => 'required|exists:karyawan,EMAIL',
            'PERIODE' => 'required|string',
            'JUMLAH_HARI_MASUK' => 'required|integer|min:0',
            'INPUT_BONUS' => 'nullable|integer|min:0', // jumlah cup
            'TOTAL_KOMPENSASI' => 'nullable|integer|min:0',
        ]);

        $karyawan = Karyawan::with('jabatan')->where('EMAIL', $validated['EMAIL'])->first();
        if (!$karyawan || !$karyawan->jabatan) {
            return redirect()->back()->with('error','Data jabatan karyawan tidak ditemukan');
        }

        $gajiPerHari = (float) $karyawan->jabatan->GAJI_POKOK_PER_HARI;
        $bonusPerCup = (float) $karyawan->jabatan->BONUS_PER_CUP;

        $jumlahHari = (int) $validated['JUMLAH_HARI_MASUK'];
        $inputBonusCups = (int) ($validated['INPUT_BONUS'] ?? 0);
        $kompensasi = (int) ($validated['TOTAL_KOMPENSASI'] ?? 0);

        $totalGajiPokok = $jumlahHari * $gajiPerHari;
        $totalBonus = $inputBonusCups * $bonusPerCup;
        $totalAkhir = $totalGajiPokok + $totalBonus + $kompensasi;

        Gaji::create([
            'EMAIL' => $validated['EMAIL'],
            'PERIODE' => $validated['PERIODE'],
            'JUMLAH_HARI_MASUK' => $jumlahHari,
            'TOTAL_GAJI_POKOK' => $totalGajiPokok,
            'TOTAL_BONUS' => $totalBonus,
            'TOTAL_KOMPENSASI' => $kompensasi,
            'TOTAL_GAJI_AKHIR' => $totalAkhir,
        ]);

        return redirect()->back()->with('success','Gaji berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $gaji = Gaji::findOrFail($id);

        $validated = $request->validate([
            'PERIODE' => 'sometimes|string',
            'JUMLAH_HARI_MASUK' => 'sometimes|integer|min:0',
            'INPUT_BONUS' => 'sometimes|integer|min:0',
            'TOTAL_KOMPENSASI' => 'sometimes|integer|min:0',
        ]);

        $karyawan = $gaji->karyawan()->with('jabatan')->first();
        if (!$karyawan || !$karyawan->jabatan) {
            return redirect()->back()->with('error','Data jabatan karyawan tidak ditemukan');
        }

        $gajiPerHari = (float) $karyawan->jabatan->GAJI_POKOK_PER_HARI;
        $bonusPerCup = (float) $karyawan->jabatan->BONUS_PER_CUP;

        $hari = $validated['JUMLAH_HARI_MASUK'] ?? $gaji->JUMLAH_HARI_MASUK;
        $inputBonusCups = $validated['INPUT_BONUS'] ?? null;

        if ($inputBonusCups !== null) {
            $totalBonus = $inputBonusCups * $bonusPerCup;
        } else {
            $totalBonus = $gaji->TOTAL_BONUS;
        }

        $totalGajiPokok = $hari * $gajiPerHari;
        $kompensasi = $validated['TOTAL_KOMPENSASI'] ?? $gaji->TOTAL_KOMPENSASI;

        $gaji->update([
            'PERIODE' => $validated['PERIODE'] ?? $gaji->PERIODE,
            'JUMLAH_HARI_MASUK' => $hari,
            'TOTAL_GAJI_POKOK' => $totalGajiPokok,
            'TOTAL_BONUS' => $totalBonus,
            'TOTAL_KOMPENSASI' => $kompensasi,
            'TOTAL_GAJI_AKHIR' => $totalGajiPokok + $totalBonus + $kompensasi,
        ]);

        return redirect()->back()->with('success','Gaji berhasil diupdate');
    }

    public function destroy($id)
    {
        Gaji::destroy($id);
        return redirect()->back()->with('success','Gaji berhasil dihapus');
    }
}
