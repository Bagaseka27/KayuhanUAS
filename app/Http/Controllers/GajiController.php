<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GajiHarian;
use App\Models\Karyawan;
use App\Models\GajiPengambilan;
use App\Models\GajiDisimpan;
use App\Services\GajiService;
use Carbon\Carbon;

class GajiController extends Controller
{
    protected $gajiService;

    public function __construct(GajiService $gajiService)
    {
        $this->gajiService = $gajiService;
    }

    /**
     * Halaman data gaji untuk admin
     */
    public function index(Request $request)
    {
        // Redirect standalone view to the integrated employee page salary tab
        return redirect()->to(url('/employee#tab-gaji'));
    }

    /**
     * Hitung gaji otomatis untuk semua karyawan
     */
    public function hitungOtomatis(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        
        try {
            $this->gajiService->hitungGajiSemuaKaryawan($periode);
            return redirect()->back()->with('success', 'Gaji berhasil dihitung otomatis untuk semua karyawan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hitung gaji untuk karyawan spesifik
     */
    public function hitungKaryawan(Request $request, $email)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        
        try {
            $this->gajiService->hitungGajiHarianPeriode($email, $periode);
            return redirect()->back()->with('success', 'Gaji berhasil dihitung untuk karyawan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Redirect show to detail harian
     */
    public function show($id)
    {
        $gajiHarian = GajiHarian::findOrFail($id);
        $periode = Carbon::parse($gajiHarian->TANGGAL)->format('Y-m');
        return redirect()->route('gaji.detail', $gajiHarian->EMAIL)->with('periode', $periode);
    }

    /**
     * Update gaji harian (jika perlu adjustment)
     */
    public function update(Request $request, $id)
    {
        $gajiHarian = GajiHarian::findOrFail($id);

        $validated = $request->validate([
            'TOTAL_POTONGAN' => 'required|numeric|min:0',
        ]);

        $gajiPokok = $gajiHarian->GAJI_POKOK_HARIAN;
        $bonus = $gajiHarian->BONUS_HARIAN;
        $potongan = $validated['TOTAL_POTONGAN'];

        $totalGajiHarian = $gajiPokok + $bonus - $potongan;

        $gajiHarian->update([
            'POTONGAN_TERLAMBAT' => $potongan,
            'TOTAL_GAJI_HARIAN' => $totalGajiHarian,
        ]);

        return redirect()->back()->with('success', 'Gaji harian berhasil diupdate');
    }

    /**
     * Hapus gaji harian untuk seluruh periode karyawan
     */
    public function destroy($id)
    {
        $gajiHarian = GajiHarian::findOrFail($id);
        $email = $gajiHarian->EMAIL;
        $periode = Carbon::parse($gajiHarian->TANGGAL)->format('Y-m');
        
        $startDate = Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->startOfDay()->toDateString();
        $endDate = Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->endOfMonth()->endOfDay()->toDateString();

        GajiHarian::where('EMAIL', $email)
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->delete();

        return redirect()->back()->with('success', 'Data gaji harian periode tersebut berhasil dihapus');
    }

    /**
     * Lihat detail gaji harian per karyawan
     */
    public function detail(Request $request, $email)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        $karyawan = Karyawan::where('EMAIL', $email)->firstOrFail();
        
        $gajiHarian = GajiHarian::where('EMAIL', $email)
            ->whereBetween('TANGGAL', [
                Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->startOfDay()->toDateString(),
                Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->endOfMonth()->endOfDay()->toDateString()
            ])
            ->orderBy('TANGGAL')
            ->get();

        return view('pages.gaji.detail', compact('karyawan', 'gajiHarian', 'periode'));
    }

    /**
     * Hitung gaji harian untuk karyawan
     */
    public function hitungHarian(Request $request, $email)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        
        try {
            $this->gajiService->hitungGajiHarianPeriode($email, $periode);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ==================== PENGAMBILAN GAJI ====================

    /**
     * Redirect formPengambilan ke dashboard barista (karena modal langsung di dashboard)
     */
    public function formPengambilan()
    {
        return redirect()->route('barista.gaji.index');
    }

    /**
     * Simpan pengambilan gaji (hanya Jumat, 1x seminggu)
     */
    public function storePengambilan(Request $request)
    {
        $validated = $request->validate([
            'NOMINAL' => 'required|numeric|min:1000',
        ]);

        $email = auth()->user()->email;

        // Hitung sisa gaji tersedia (harian + tabungan)
        $totalGajiHarian = GajiHarian::where('EMAIL', $email)->sum('TOTAL_GAJI_HARIAN');
        $totalPengambilanDisetujui = GajiPengambilan::where('EMAIL', $email)->where('STATUS', 'disetujui')->sum('NOMINAL');
        $availableSalary = $totalGajiHarian - $totalPengambilanDisetujui;

        if ($validated['NOMINAL'] > $availableSalary) {
            return redirect()->back()->with('error', 'Nominal pengambilan melebihi total saldo tersedia');
        }

        GajiPengambilan::create([
            'EMAIL' => $email,
            'TANGGAL_PENGAMBILAN' => now()->toDateString(),
            'NOMINAL' => $validated['NOMINAL'],
            'STATUS' => 'menunggu'
        ]);

        return redirect()->back()->with('success', 'Pengambilan gaji berhasil dikirim ke admin');
    }

    /**
     * Lihat riwayat pengambilan
     */
    public function lihatPengambilan()
    {
        $email = auth()->user()->email;
        $pengambilan = GajiPengambilan::where('EMAIL', $email)->latest()->paginate(10);
        return view('pages.gaji.lihat_pengambilan', compact('pengambilan'));
    }

    // ==================== PENYIMPANAN GAJI ====================

    /**
     * Redirect formPenyimpanan ke dashboard barista
     */
    public function formPenyimpanan()
    {
        return redirect()->route('barista.gaji.index');
    }

    /**
     * Simpan penyimpanan gaji (otomatis simpan seluruh sisa gaji, hanya Jumat, 1x seminggu)
     */
    public function storePenyimpanan(Request $request)
    {
        $email = auth()->user()->email;

        // Hitung sisa gaji
        $totalGajiHarian = GajiHarian::where('EMAIL', $email)->sum('TOTAL_GAJI_HARIAN');
        $totalPengambilanDisetujui = GajiPengambilan::where('EMAIL', $email)->where('STATUS', 'disetujui')->sum('NOMINAL');
        $totalPenyimpananDisetujui = GajiDisimpan::where('EMAIL', $email)->where('STATUS', 'disetujui')->sum('NOMINAL');
        $availableSalary = $totalGajiHarian - $totalPengambilanDisetujui - $totalPenyimpananDisetujui;

        if ($availableSalary <= 0) {
            return redirect()->back()->with('error', 'Tidak ada sisa gaji yang bisa disimpan.');
        }

        GajiDisimpan::create([
            'EMAIL' => $email,
            'TANGGAL_PENYIMPANAN' => now()->toDateString(),
            'NOMINAL' => $availableSalary,
            'STATUS' => 'menunggu'
        ]);

        return redirect()->back()->with('success', 'Penyimpanan sisa gaji sebesar Rp ' . number_format($availableSalary, 0, ',', '.') . ' berhasil dikirim ke admin');
    }

    /**
     * Lihat riwayat penyimpanan
     */
    public function lihatPenyimpanan()
    {
        $email = auth()->user()->email;
        $disimpan = GajiDisimpan::where('EMAIL', $email)->latest()->paginate(10);
        return view('pages.gaji.lihat_penyimpanan', compact('disimpan'));
    }

    /**
     * Dashboard barista - index gaji
     */
    public function baristaIndex()
    {
        $email = auth()->user()->email;
        $periode = now()->format('Y-m');
        $hariIni = now()->format('l');

        // Trigger weekly auto-savings check before displaying to ensure real-time values
        try {
            $this->gajiService->autoSaveUnclaimedSalaries();
        } catch (\Exception $e) {
            \Log::error("Failed to run auto-save in barista view: " . $e->getMessage());
        }

        // Selalu hitung ulang gaji hari ini agar real-time dengan transaksi/sales & absensi saat ini
        try {
            $this->gajiService->hitungGajiHarian($email, now()->toDateString());
        } catch (\Exception $e) {
            // ignore if no shift schedule for today
        }

        $startDate = Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->startOfDay()->toDateString();
        $endDate = Carbon::create((int) explode('-', $periode)[0], (int) explode('-', $periode)[1], 1)->endOfMonth()->endOfDay()->toDateString();

        // Ambil gaji harian bulan ini
        $gajiHarianBulanIni = GajiHarian::where('EMAIL', $email)
            ->whereBetween('TANGGAL', [$startDate, $endDate])
            ->get();

        $totalGajiBulanIni = $gajiHarianBulanIni->sum('TOTAL_GAJI_HARIAN');
        $totalJamKerja = $gajiHarianBulanIni->sum('JAM_KERJA_TERJADWAL');

        // Hitung sisa gaji all-time (yang bisa diambil/disimpan)
        $totalGajiHarianAllTime = GajiHarian::where('EMAIL', $email)->sum('TOTAL_GAJI_HARIAN');
        $pengambilanDisetujui = GajiPengambilan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->sum('NOMINAL');
        $penyimpananDisetujui = GajiDisimpan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->sum('NOMINAL');

        $sisaGajiHarian = $totalGajiHarianAllTime - $pengambilanDisetujui - $penyimpananDisetujui;
        $totalTabungan = \App\Models\Tabungan::where('EMAIL', $email)->value('SALDO') ?? 0;
        $sisaGaji = $sisaGajiHarian + $totalTabungan; // Sisa harian + tabungan, yaitu saldo yang dapat ditarik

        // Get riwayat
        $riwayatPengambilan = GajiPengambilan::where('EMAIL', $email)->latest()->take(10)->get();
        $riwayatPenyimpanan = GajiDisimpan::where('EMAIL', $email)->latest()->take(10)->get();

        $gajiBulanIni = (object)[
            'TOTAL_GAJI_AKHIR' => $totalGajiBulanIni,
            'TOTAL_JAM_KERJA' => $totalJamKerja,
        ];

        return view('pages.gaji.barista_index', compact(
            'gajiBulanIni',
            'sisaGaji',
            'sisaGajiHarian',
            'totalTabungan',
            'periode',
            'hariIni',
            'riwayatPengambilan',
            'riwayatPenyimpanan'
        ));
    }

    // ==================== DASHBOARD ADMIN ====================

    /**
     * Lihat pengambilan gaji di admin
     */
    public function daftarPengambilan(Request $request)
    {
        $status = $request->input('status', 'menunggu');
        $pengambilan = GajiPengambilan::with('karyawan')
            ->when($status, function ($query) use ($status) {
                return $query->where('STATUS', $status);
            })
            ->latest()
            ->paginate(20);

        return view('pages.gaji.daftar_pengambilan', compact('pengambilan', 'status'));
    }

    /**
     * Terima/Proses pengambilan gaji (potong sisa dan tabungkan sisa)
     */
    public function terimaPengambilan(Request $request, $id)
    {
        $pengambilan = GajiPengambilan::findOrFail($id);
        $email = $pengambilan->EMAIL;

        // Hitung sisa gaji tersedia saat ini (sebelum approval pengajuan ini)
        $totalGajiHarian = GajiHarian::where('EMAIL', $email)->sum('TOTAL_GAJI_HARIAN');
        $totalPengambilanDisetujui = GajiPengambilan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->where('id', '!=', $id)
            ->sum('NOMINAL');
        $totalPenyimpananDisetujui = GajiDisimpan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->sum('NOMINAL');

        $totalDapatDiambil = $totalGajiHarian - $totalPengambilanDisetujui;

        if ($pengambilan->NOMINAL > $totalDapatDiambil) {
            return redirect()->back()->with('error', 'Gagal memproses. Nominal pengambilan melebihi total saldo tersedia (Rp ' . number_format($totalDapatDiambil, 0, ',', '.') . ')');
        }

        // Tandai disetujui/sudah diproses
        $pengambilan->update([
            'STATUS' => 'disetujui',
            'DIPROSES_OLEH' => auth()->user()->email,
            'TANGGAL_DIPROSES' => now(),
            'CATATAN_ADMIN' => $request->input('CATATAN_ADMIN')
        ]);

        $sisaGajiHarian = $totalGajiHarian - $totalPengambilanDisetujui - $totalPenyimpananDisetujui;

        if ($pengambilan->NOMINAL <= $sisaGajiHarian) {
            // Sisa gaji harian otomatis disimpan ke GajiDisimpan
            $remainder = $sisaGajiHarian - $pengambilan->NOMINAL;
            if ($remainder > 0) {
                GajiDisimpan::create([
                    'EMAIL' => $email,
                    'TANGGAL_PENYIMPANAN' => now()->toDateString(),
                    'NOMINAL' => $remainder,
                    'STATUS' => 'disetujui',
                    'CATATAN_ADMIN' => 'Sisa pengambilan gaji otomatis disimpan',
                    'DIPROSES_OLEH' => auth()->user()->email,
                    'TANGGAL_DIPROSES' => now()
                ]);
            }
            $msg = 'Pengambilan gaji berhasil diproses. Sisa gaji sebesar Rp ' . number_format($remainder, 0, ',', '.') . ' otomatis disimpan.';
        } else {
            // Menarik dari tabungan
            $excess = $pengambilan->NOMINAL - $sisaGajiHarian;
            if ($excess > 0) {
                GajiDisimpan::create([
                    'EMAIL' => $email,
                    'TANGGAL_PENYIMPANAN' => now()->toDateString(),
                    'NOMINAL' => -$excess,
                    'STATUS' => 'disetujui',
                    'CATATAN_ADMIN' => 'Penarikan tabungan untuk pengambilan gaji',
                    'DIPROSES_OLEH' => auth()->user()->email,
                    'TANGGAL_DIPROSES' => now()
                ]);
            }
            $msg = 'Pengambilan gaji berhasil diproses. Sebesar Rp ' . number_format($excess, 0, ',', '.') . ' ditarik dari tabungan.';
        }

        // Sync Tabungan
        \App\Models\Tabungan::syncTabungan($email);

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Tolak pengambilan gaji
     */
    public function tolakPengambilan(Request $request, $id)
    {
        $pengambilan = GajiPengambilan::findOrFail($id);
        
        $pengambilan->update([
            'STATUS' => 'ditolak',
            'DIPROSES_OLEH' => auth()->user()->email,
            'TANGGAL_DIPROSES' => now(),
            'CATATAN_ADMIN' => $request->input('CATATAN_ADMIN', 'Ditolak oleh admin')
        ]);

        return redirect()->back()->with('success', 'Pengambilan gaji ditolak');
    }

    /**
     * Lihat penyimpanan gaji di admin
     */
    public function daftarPenyimpanan(Request $request)
    {
        $status = $request->input('status', 'menunggu');
        $disimpan = GajiDisimpan::with('karyawan')
            ->when($status, function ($query) use ($status) {
                return $query->where('STATUS', $status);
            })
            ->latest()
            ->paginate(20);

        return view('pages.gaji.daftar_penyimpanan', compact('disimpan', 'status'));
    }

    /**
     * Terima penyimpanan gaji
     */
    public function terimaPenyimpanan(Request $request, $id)
    {
        $disimpan = GajiDisimpan::findOrFail($id);
        $email = $disimpan->EMAIL;

        // Hitung sisa gaji tersedia saat ini (sebelum approval pengajuan ini)
        $totalGajiHarian = GajiHarian::where('EMAIL', $email)->sum('TOTAL_GAJI_HARIAN');
        $totalPengambilanDisetujui = GajiPengambilan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->sum('NOMINAL');
        $totalPenyimpananDisetujui = GajiDisimpan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->where('id', '!=', $id)
            ->sum('NOMINAL');

        $availableSalary = $totalGajiHarian - $totalPengambilanDisetujui - $totalPenyimpananDisetujui;

        // Nominal yang disimpan dibatasi maksimal sisa gaji tersedia
        $nominal = min($disimpan->NOMINAL, $availableSalary);

        if ($nominal <= 0) {
            $disimpan->update([
                'STATUS' => 'ditolak',
                'DIPROSES_OLEH' => auth()->user()->email,
                'TANGGAL_DIPROSES' => now(),
                'CATATAN_ADMIN' => 'Dibatalkan otomatis karena sisa gaji tidak mencukupi'
            ]);
            return redirect()->back()->with('error', 'Penyimpanan dibatalkan karena sisa gaji 0.');
        }
        
        $disimpan->update([
            'NOMINAL' => $nominal,
            'STATUS' => 'disetujui',
            'DIPROSES_OLEH' => auth()->user()->email,
            'TANGGAL_DIPROSES' => now(),
            'CATATAN_ADMIN' => $request->input('CATATAN_ADMIN')
        ]);

        // Sync Tabungan
        \App\Models\Tabungan::syncTabungan($email);

        return redirect()->back()->with('success', 'Penyimpanan gaji sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' telah disetujui.');
    }

    /**
     * Tolak penyimpanan gaji
     */
    public function tolakPenyimpanan(Request $request, $id)
    {
        $disimpan = GajiDisimpan::findOrFail($id);
        
        $disimpan->update([
            'STATUS' => 'ditolak',
            'DIPROSES_OLEH' => auth()->user()->email,
            'TANGGAL_DIPROSES' => now(),
            'CATATAN_ADMIN' => $request->input('CATATAN_ADMIN', 'Ditolak oleh admin')
        ]);

        return redirect()->back()->with('success', 'Penyimpanan gaji ditolak');
    }
}
