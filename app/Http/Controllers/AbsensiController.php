<?php

namespace App\Http\Controllers;

use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    // BAGIAN ABSEN DATANG (Tampilan)
    public function indexDatang()
    {
        $karyawan = Auth::user(); 
        
        // 1. Ambil data absensi untuk Riwayat
        $riwayatAbsensi = AbsenDatang::where('EMAIL', $karyawan->email)
                                    ->orderBy('DATETIME_DATANG', 'desc')
                                    ->get();

        // 2. Cek Status Absensi (VARIABEL YANG HILANG)
        $absenDatangHariIni = AbsenDatang::where('EMAIL', $karyawan->email)->first(); 
        $absenPulangHariIni = AbsenPulang::where('EMAIL', $karyawan->email)->first();
        
        // 3. Data Jadwal Shift
        $jadwal = (object)['jam_masuk' => '08:00', 'jam_pulang' => '16:00']; 
        
        // 🟢 KEMBALIKAN VIEW DENGAN DATA
        return view('pages.absensi', [
            'riwayatAbsensi' => $riwayatAbsensi,
            'absen_datang' => $absenDatangHariIni,
            'absen_pulang' => $absenPulangHariIni,
            'jadwal' => $jadwal,
        ]);
    }
// ...

    public function storeDatang(Request $request)
    {
        $email = $request->input('EMAIL');
        
        // 🛑 PEMBATASAN: Cek apakah sudah Absen Masuk
        $sudahAbsen = AbsenDatang::where('EMAIL', $email)->exists();
        if ($sudahAbsen) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda sudah Absen Masuk. Absensi Masuk hanya dapat dilakukan sekali.');
        }

        // 1. Validasi Input
        $validated = $request->validate([
            'EMAIL'           => 'required|exists:karyawan,EMAIL',
            'FOTO_FILE'       => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            'DATETIME_DATANG' => 'required|date_format:Y-m-d H:i:s',
        ]);
        
        // 2. Simpan File Foto
        $path = $request->file('FOTO_FILE')->store('public/absensi/datang');
        $nama_file = basename($path);
        
        // 3. Simpan ke Database
        AbsenDatang::create([
            'EMAIL' => $validated['EMAIL'],
            'FOTO' => $nama_file, 
            'DATETIME_DATANG' => $validated['DATETIME_DATANG'],
        ]);
        
        return redirect()->route('barista.absensi.index')->with('success', 'Absen Masuk berhasil dicatat.');
    }

    // BAGIAN ABSEN PULANG (Tampilan & Monitoring)
    public function indexPulang(Request $request) 
{
    // Jika fungsi ini dipanggil untuk Monitoring Admin, kita harus mengembalikan View.
    // Kode Monitoring Admin yang lengkap:
    
    $tanggalFilter = $request->input('tanggal', \Carbon\Carbon::today()->toDateString());
    
    // 1. DATA KARYAWAN (DUMMY KOSONG UNTUK MENCEGAH ERROR DATABASE)
    // Data ini akan diisi dengan query Karyawan::with(...) saat database siap.
    $karyawanList = collect([]); 
    
    // 2. Data lokasi untuk dropdown filter (HANYA CABANG DUMMY)
    $lokasiFilter = collect([
        ['id' => 'C1', 'nama' => 'Cabang Pusat', 'tipe' => 'Cabang'],
        ['id' => 'C2', 'nama' => 'Cabang Utara', 'tipe' => 'Cabang'],
        // Rombong telah dihapus
    ]);
    
    // 🟢 KEMBALIKAN VIEW MONITORING
    return view('pages.absensi_monitoring', compact('karyawanList', 'lokasiFilter', 'tanggalFilter')); 
}
}