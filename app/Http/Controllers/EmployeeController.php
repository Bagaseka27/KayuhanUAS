<?php

namespace App\Http\Controllers;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Cabang;
use App\Models\Rombong;
use App\Models\Gaji;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $payrollsData = Gaji::with('karyawan')->get();
        $karyawanData = Karyawan::with(['karyawan.jabatan'])->get()->map(function($gaji){
            $karyawan = $gaji->karyawan;
            return(object)[
                'id'    => $gaji->ID_GAJI,
                'email' =>$gaji->EMAIL,
                'name' =>$karyawan->NAMA ?? 'N/A',
                'jabatan_name' =>$karyawan->jabatan->NAMA_JABATAN ?? 'N/A',
                'periode' =>$gaji->PERIODE,
                'basic' =>$gaji->GAJI_POKOK_PER_HARI,
                'bonus' =>$gaji->BONUS_PER_CUP,
                'total' =>$gaji->TOTAL_GAJI,
            ];
        });
        $jabatanlist = Jabatan::pluck('NAMA_JABATAN','ID_JABATAN');
        $cabanglist  = Cabang::pluck('NAMA_LOKASI','ID_CABANG');
        $rombonglist = Rombong::pluck('ID_ROMBONG','ID_ROMBONG');

        return view('pages.employee',[
            'karyawan'    => $karyawanData,
            'payrolls'    => $payrollsData, 
            'jabatanList' => $jabatanlist,
            'cabangList'  => $cabanglist,
            'rombongList' => $rombonglist,
            'employees'   => $karyawanData,
        ]);
    }
    
}
