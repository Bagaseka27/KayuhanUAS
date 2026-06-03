<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiNewController extends Controller
{
    /**
     * Tampilkan halaman absensi
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Get karyawan dengan jadwal hari ini
        $karyawan = Karyawan::where('EMAIL', $user->email)
            ->with(['jadwal' => function ($query) use ($today) {
                $query->where('TANGGAL', $today)->with('cabang');
            }])
            ->first();

        if (!$karyawan) {
            return redirect()->route('barista.absensi.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        // Get jadwal hari ini
        $jadwalHariIni = $karyawan->jadwal->first();
        if (!$jadwalHariIni) {
            return redirect()->route('barista.absensi.index')->with('error', 'Anda tidak memiliki jadwal hari ini');
        }

        // Get absensi hari ini
        $absensi = Absensi::where('EMAIL', $user->email)
            ->where('TANGGAL', $today)
            ->first();

        // Prepare jadwal data
        $jadwal = [
            'jam_masuk' => substr($jadwalHariIni->JAM_MULAI, 0, 5),
            'jam_pulang' => substr($jadwalHariIni->JAM_SELESAI, 0, 5),
            'lokasi' => $jadwalHariIni->cabang->NAMA_LOKASI ?? 'Tidak Ada',
            'id_cabang' => $jadwalHariIni->ID_CABANG
        ];

        // Check if bisa absen pulang (sudah lewat jam pulang)
        $jamPulang = Carbon::createFromTimeString($jadwal['jam_pulang']);
        $bisakAbsenPulang = Carbon::now()->greaterThanOrEqualTo($jamPulang) && $absensi?->isSudahAbsenDatang();

        return view('pages.absensi_new', [
            'karyawan' => $karyawan,
            'jadwal' => $jadwal,
            'absensi' => $absensi,
            'bisakAbsenPulang' => $bisakAbsenPulang,
            'today' => $today
        ]);
    }

    /**
     * Submit absen datang
     */
    public function submitDatang(Request $request)
    {
        $email = Auth::user()->email;
        $today = Carbon::today()->toDateString();

        // Cek jadwal
        $jadwal = Jadwal::where('EMAIL', $email)
            ->where('TANGGAL', $today)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal hari ini'
            ], 400);
        }

        // Cek sudah absen
        $sudahAbsen = Absensi::where('EMAIL', $email)
            ->where('TANGGAL', $today)
            ->whereNotNull('DATETIME_DATANG')
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini'
            ], 400);
        }

        // Validate request
        $request->validate([
            'foto' => 'required|string', // base64 encoded
            'lokasi' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'datetime' => 'required|date_format:Y-m-d H:i:s'
        ]);

        try {
            $dateTimeAbsen = Carbon::createFromFormat('Y-m-d H:i:s', $request->datetime, 'Asia/Jakarta');

            // Calculate status (HADIR / TERLAMBAT)
            $statusData = Absensi::calculateStatus($jadwal->JAM_MULAI, $dateTimeAbsen);

            // Get atau create absensi record
            $absensi = Absensi::where('EMAIL', $email)
                ->where('TANGGAL', $today)
                ->first();

            if (!$absensi) {
                $absensi = new Absensi([
                    'EMAIL' => $email,
                    'TANGGAL' => $today,
                    'ID_CABANG' => $jadwal->ID_CABANG
                ]);
            }

            // Set absen datang data
            $absensi->DATETIME_DATANG = $dateTimeAbsen;
            $absensi->FOTO_DATANG = $request->foto;
            $absensi->LOKASI_DATANG = $request->lokasi;
            $absensi->LAT_DATANG = $request->lat;
            $absensi->LNG_DATANG = $request->lng;
            $absensi->STATUS = $statusData['status'];
            $absensi->KOMPENSASI = $statusData['kompensasi'];

            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil. Status: ' . $statusData['status'],
                'status' => $statusData['status'],
                'kompensasi' => $statusData['kompensasi']
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal absen masuk: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit absen pulang
     */
    public function submitPulang(Request $request)
    {
        $email = Auth::user()->email;
        $today = Carbon::today()->toDateString();

        // Cek jadwal
        $jadwal = Jadwal::where('EMAIL', $email)
            ->where('TANGGAL', $today)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal hari ini'
            ], 400);
        }

        // Cek sudah absen datang
        $absensi = Absensi::where('EMAIL', $email)
            ->where('TANGGAL', $today)
            ->first();

        if (!$absensi?->isSudahAbsenDatang()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk'
            ], 400);
        }

        // Cek sudah absen pulang
        if ($absensi->isSudahAbsenPulang()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang'
            ], 400);
        }

        // Cek sudah lewat jam pulang
        $jamPulang = Carbon::createFromTimeString(substr($jadwal->JAM_SELESAI, 0, 5));
        if (Carbon::now()->lessThan($jamPulang)) {
            return response()->json([
                'success' => false,
                'message' => 'Absen pulang baru bisa dilakukan setelah jam ' . substr($jadwal->JAM_SELESAI, 0, 5)
            ], 400);
        }

        // Validate request
        $request->validate([
            'foto' => 'required|string',
            'lokasi' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'datetime' => 'required|date_format:Y-m-d H:i:s'
        ]);

        try {
            $absensi->DATETIME_PULANG = Carbon::createFromFormat('Y-m-d H:i:s', $request->datetime, 'Asia/Jakarta');
            $absensi->FOTO_PULANG = $request->foto;
            $absensi->LOKASI_PULANG = $request->lokasi;
            $absensi->LAT_PULANG = $request->lat;
            $absensi->LNG_PULANG = $request->lng;
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal absen pulang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit tidak hadir (sakit/izin)
     */
    public function submitTidakHadir(Request $request)
    {
        $email = Auth::user()->email;
        $today = Carbon::today()->toDateString();

        $request->validate([
            'alasan' => 'required|in:SAKIT,IZIN',
            'surat' => 'required|string' // base64 encoded
        ]);

        try {
            $jadwal = Jadwal::where('EMAIL', $email)
                ->where('TANGGAL', $today)
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki jadwal hari ini'
                ], 400);
            }

            $absensi = Absensi::where('EMAIL', $email)
                ->where('TANGGAL', $today)
                ->firstOrCreate([
                    'EMAIL' => $email,
                    'TANGGAL' => $today,
                    'ID_CABANG' => $jadwal->ID_CABANG
                ]);

            $absensi->STATUS = 'TIDAK_HADIR';
            $absensi->ALASAN_TIDAK_HADIR = $request->alasan;
            $absensi->SURAT_IZIN = $request->surat;
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan ' . ($request->alasan === 'SAKIT' ? 'sakit' : 'izin') . ' berhasil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal submit tidak hadir: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display foto (base64)
     */
    public function getFoto($id, $type)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $fotoColumn = $type === 'datang' ? 'FOTO_DATANG' : 'FOTO_PULANG';
        $foto = $absensi->{$fotoColumn};

        if (!$foto) {
            return response()->json(['error' => 'Foto tidak ditemukan'], 404);
        }

        // Return base64 langsung untuk img tag
        return response()->json([
            'foto' => $foto
        ]);
    }
    public function monitoring(Request $request)
    {
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $lokasiFilterId = $request->input('lokasi');

        $lokasiFilter = Cabang::select('ID_CABANG as id', 'NAMA_LOKASI as nama')
            ->get()
            ->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama]);

        $query = Karyawan::with([
            'cabang',
            'rombong',
            'absensi' => function ($q) use ($tanggalFilter) {
                $q->where('TANGGAL', $tanggalFilter);
            }
        ]);

        if ($lokasiFilterId) {
            if (str_starts_with($lokasiFilterId, 'C')) {
                $query->where('ID_CABANG', $lokasiFilterId);
            } elseif (str_starts_with($lokasiFilterId, 'R')) {
                $query->where('ID_ROMBONG', $lokasiFilterId);
            }
        }

        $karyawanList = $query->get();

        return view('pages.absensi_monitoring', compact('karyawanList', 'lokasiFilter', 'tanggalFilter'));
    }
}
