<?php

namespace App\Http\Controllers;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Cabang;
use App\Models\Rombong;
use App\Models\Jadwal;
use App\Models\GajiHarian;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        // Trigger weekly auto-savings check to keep everything updated
        try {
            app(\App\Services\GajiService::class)->autoSaveUnclaimedSalaries();
        } catch (\Exception $e) {
            \Log::error("Failed to run auto-save in employee index: " . $e->getMessage());
        }

        $gajiHarian = GajiHarian::with('karyawan', 'karyawan.jabatan')->get();
        $payrollsData = $gajiHarian->groupBy(function($item) {
            return $item->EMAIL . '_' . \Carbon\Carbon::parse($item->TANGGAL)->format('Y-m');
        })->map(function ($items) {
            $karyawan = $items->first()->karyawan;
            $periode = \Carbon\Carbon::parse($items->first()->TANGGAL)->format('Y-m');
            return (object)[
                'EMAIL' => $items->first()->EMAIL,
                'karyawan' => $karyawan,
                'PERIODE' => $periode,
                'TOTAL_GAJI_POKOK' => $items->sum('GAJI_POKOK_HARIAN'),
                'TOTAL_BONUS' => $items->sum('BONUS_HARIAN'),
                'TOTAL_KOMPENSASI' => $items->sum('POTONGAN_TERLAMBAT'),
                'TOTAL_GAJI_AKHIR' => $items->sum('TOTAL_GAJI_HARIAN'),
                'ID_GAJI' => $items->first()->id, // Mock ID using the first daily salary ID
                'TABUNGAN' => \App\Models\Tabungan::where('EMAIL', $items->first()->EMAIL)->value('SALDO') ?? 0,
            ];
        })->values();
        $jadwals = Jadwal::with(['karyawan','cabang'])->get();

        $karyawanData = Karyawan::with(['jabatan','cabang','rombong'])->get()->map(function($k){
            return (object)[
                'email' => $k->EMAIL,
                'name' => $k->NAMA ?? 'N/A',
                'no_telp' => $k->NO_HP ?? '',
                'jabatan_name' => $k->jabatan->NAMA_JABATAN ?? '',
                'ID_JABATAN' => $k->ID_JABATAN,
                'ID_CABANG' => $k->ID_CABANG,
                'ID_ROMBONG' => $k->ID_ROMBONG,
                'role' => $k->ROLE,
            ];
        });

        $jabatanList      = Jabatan::pluck('NAMA_JABATAN','ID_JABATAN');
        $jabatanListFull  = Jabatan::all();
        $cabangList       = Cabang::pluck('NAMA_LOKASI','ID_CABANG');
        $rombongList      = Rombong::pluck('ID_ROMBONG','ID_ROMBONG');
        $employeeDropdown = Karyawan::pluck('NAMA','EMAIL');

        return view('pages.employee', compact(
            'karyawanData','jadwals','payrollsData','jabatanList','jabatanListFull','cabangList','rombongList','employeeDropdown'
        ));
    }

    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::findOrFail($email); 
        
        $validatedData = $request->validate([
            'NAMA'          => 'required|string|max:255',
            'ID_JABATAN'    => 'required|exists:jabatan,ID_JABATAN',
            'NO_HP'       => 'required|string|max:15',
        ]);
        
        $karyawan->update($validatedData);
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy($email)
    {
        $deleted = Karyawan::destroy($email);

        if ($deleted) {
             return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil dihapus!');
        }
        
        return redirect()->route('employee.index')->with('error', 'Data karyawan gagal ditemukan atau dihapus!');
    }
}

