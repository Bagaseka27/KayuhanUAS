<?php

namespace App\Http\Controllers;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Cabang;
use App\Models\Rombong;
use App\Models\Jadwal;
use App\Models\Gaji;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {

    $payrollsData = Gaji::with('karyawan')->get();


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


    $jabatanList = Jabatan::pluck('NAMA_JABATAN','ID_JABATAN');
    $cabangList = Cabang::pluck('NAMA_LOKASI','ID_CABANG');
    $rombongList = Rombong::pluck('ID_ROMBONG','ID_ROMBONG');
    $employeeDropdown = Karyawan::pluck('NAMA','EMAIL');


    return view('pages.employee', [
    'karyawanData' => $karyawanData,
    'jadwals' => $jadwals,
    'payrollsData' => $payrollsData,
    'jabatanList' => $jabatanList,
    'cabangList' => $cabangList,
    'rombongList' => $rombongList,
    'employeeDropdown' => $employeeDropdown,
    ]);
    }
    // EmployeeController.php (Tambahkan fungsi ini)

    public function update(Request $request, $email)
    {
        $karyawan = Karyawan::findOrFail($email); // Cari karyawan berdasarkan email (PK)
        
        $validatedData = $request->validate([
            // Email tidak perlu unique lagi karena kita sedang mengedit data yang sudah ada
            'NAMA'          => 'required|string|max:255',
            'ID_JABATAN'    => 'required|exists:jabatan,ID_JABATAN',
            'NO_TELP'       => 'required|string|max:15',
            'ALAMAT'        => 'nullable|string|max:255',
            // Tambahkan kolom lain yang di-update jika ada, seperti ID_CABANG, ID_ROMBONG
        ]);
        
        $karyawan->update($validatedData);
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // Tambahkan fungsi destroy juga (opsional untuk tombol hapus)
    public function destroy($email)
    {
        // 1. Cari dan hapus Karyawan berdasarkan EMAIL (PK)
        $deleted = Karyawan::destroy($email);

        if ($deleted) {
             return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil dihapus!');
        }
        
        return redirect()->route('employee.index')->with('error', 'Data karyawan gagal ditemukan atau dihapus!');
    }
}

