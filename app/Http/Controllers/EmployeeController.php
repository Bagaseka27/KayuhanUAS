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
        // Data Gaji (sudah benar)
        $payrollsData = Gaji::with('karyawan')->get();
        
        // Data Jadwal (sudah benar)
        $jadwals = Jadwal::with(['karyawan','cabang'])->get();
        
        // --- PERBAIKAN LOGIKA PENGAMBILAN KARYAWAN MURNI ---
        $karyawanData = Karyawan::with('jabatan')->get()->map(function($karyawan){
            // Variabel iterator di sini harusnya $karyawan, bukan $gaji
            return (object)[
                // Sesuaikan dengan kolom yang ada di model Karyawan
                'email'        => $karyawan->EMAIL,
                'name'         => $karyawan->NAMA ?? 'N/A',
                'no_telp'      => $karyawan->NO_TELP,
                'alamat'       => $karyawan->ALAMAT,
                'jabatan_name' => $karyawan->jabatan->NAMA_JABATAN ?? 'N/A',
            ];
        });
        // ---------------------------------------------------
        
        $jabatanlist = Jabatan::pluck('NAMA_JABATAN','ID_JABATAN');
        $cabanglist  = Cabang::pluck('NAMA_LOKASI','ID_CABANG');
        $rombonglist = Rombong::pluck('ID_ROMBONG','ID_ROMBONG');

        // Tambahkan juga dropdown karyawan (untuk mencegah error $employeeDropdown)
        $employeeDropdown = Karyawan::pluck('NAMA', 'EMAIL');

        // --- PASTIKAN SEMUA VARIABEL DITERUSKAN KE VIEW ---
        return view('pages.employee', [
            'karyawanData'     => $karyawanData, // Pastikan ini ada!
            'jadwals'          => $jadwals,      
            'payrollsData'     => $payrollsData, 
            'jabatanList'      => $jabatanlist,
            'cabangList'       => $cabanglist,
            'rombongList'      => $rombonglist,
            'employeeDropdown' => $employeeDropdown, // Mencegah error sebelumnya
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'EMAIL'         => 'required|unique:karyawan,EMAIL|email|max:100',
            'NAMA'          => 'required|string|max:255',
            'ID_JABATAN'    => 'required|exists:jabatan,ID_JABATAN',
            'NO_TELP'       => 'required|string|max:15',
            'ALAMAT'        => 'nullable|string|max:255',
        ]);

        Karyawan::create($validatedData);
        return redirect()->route('employee.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }
}
