<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RombongStok;
use App\Models\StokGudang;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokRombongController extends Controller
{
    // =========================================================================
    // 1. INDEX UNTUK ADMIN (Melihat Semua Stok Rombong)
    // =========================================================================
    public function index()
    {
        $stokRombong = DB::table('rombong_stok')
            ->join('stokgudang', 'rombong_stok.barang_id', '=', 'stokgudang.ID_BARANG')
            ->select('rombong_stok.*', 'stokgudang.NAMA_BARANG')
            ->get();

        return view('pages.stok_rombong_admin', compact('stokRombong'));
    }

    // =========================================================================
    // 2. INDEX UNTUK BARISTA (Melihat Stok Milik Cabangnya Hari Ini)
    // =========================================================================
    public function baristaIndex()
    {
        $today = Carbon::today()->toDateString();
        $user = Auth::user();
        $email = $user->email;

        // Ambil jadwal barista hari ini
        $schedule = Jadwal::where('EMAIL', $email)
            ->whereDate('TANGGAL', $today)
            ->first();

        // Ambil kode lokasi kerja (Contoh: CBG-SBY01)
        $idCabang = $schedule ? $schedule->ID_CABANG : null;
        $rombong = $idCabang ? \App\Models\Rombong::where('ID_CABANG', $idCabang)->first() : null;
        $idRombongBarista = $rombong ? $rombong->ID_ROMBONG : null;

        // Menggunakan DB Builder Query langsung agar bypass masalah Primary Key Model
        $stokRombong = [];
        if ($idRombongBarista) {
            $stokRombong = DB::table('rombong_stok')
                ->join('stokgudang', 'rombong_stok.barang_id', '=', 'stokgudang.ID_BARANG')
                ->where('rombong_stok.rombong_id', $idRombongBarista)
                ->select('rombong_stok.*', 'stokgudang.NAMA_BARANG')
                ->get();
        }

        $masterBarang = StokGudang::where('JUMLAH', '>', 0)->get();

        return view('pages.inventory_barista', compact('stokRombong', 'idRombongBarista', 'masterBarang'));
    }

    // =========================================================================
    // 3. BATCH STORE UNTUK BARISTA (Tambah Stok Rombong & Potong Stok Gudang)
    // =========================================================================
    public function baristaBatchStore(Request $request)
    {
        $request->validate([
            'barang_id'   => 'required|array',
            'barang_id.*' => 'required|exists:stokgudang,ID_BARANG',
            'jumlah'      => 'required|array',
            'jumlah.*'    => 'required|integer|min:1',
        ]);

        $today = Carbon::today()->toDateString();
        $schedule = Jadwal::where('EMAIL', Auth::user()->email)->whereDate('TANGGAL', $today)->first();
        $idCabang = $schedule ? $schedule->ID_CABANG : null;
        $rombong = $idCabang ? \App\Models\Rombong::where('ID_CABANG', $idCabang)->first() : null;
        $idRombongBarista = $rombong ? $rombong->ID_ROMBONG : null;

        if (!$idRombongBarista) {
            return redirect()->back()->with('error', 'Gagal memproses. ID Rombong tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->barang_id as $index => $barangId) {
                $qtyInput = $request->jumlah[$index];
                
                // 1. Cek ketersediaan barang di gudang utama
                $barangGudang = StokGudang::where('ID_BARANG', $barangId)->first();

                if (!$barangGudang || $barangGudang->JUMLAH < $qtyInput) {
                    return redirect()->action([self::class, 'baristaIndex'])->with('error', "Stok gudang tidak cukup!");
                }

                // 2. Potong stok gudang utama
                $barangGudang->increment('KELUAR', $qtyInput);
                $barangGudang->refresh(); // tambah ini
                $barangGudang->JUMLAH = $barangGudang->MASUK - $barangGudang->KELUAR;
                $barangGudang->save();

                // 3. Simpan langsung ke database menggunakan DB Builder mentah (Anti-Gagal Primary Key)
                $stokExist = DB::table('rombong_stok')
                    ->where('rombong_id', $idRombongBarista)
                    ->where('barang_id', $barangId)
                    ->first();

                if ($stokExist) {
                    DB::table('rombong_stok')
                        ->where('rombong_id', $idRombongBarista)
                        ->where('barang_id', $barangId)
                        ->update([
                            'stok_awal'  => $stokExist->stok_awal + $qtyInput,
                            'stok_akhir' => $stokExist->stok_akhir + $qtyInput,
                            'updated_at' => Carbon::now()
                        ]);
                } else {
                    DB::table('rombong_stok')->insert([
                        'rombong_id' => $idRombongBarista,
                        'barang_id'  => $barangId,
                        'stok_awal'  => $qtyInput,
                        'stok_akhir' => $qtyInput,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();
            return redirect()->action([self::class, 'baristaIndex'])->with('success', 'Berhasil mengisi stok rombong!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->action([self::class, 'baristaIndex'])->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}