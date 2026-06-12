<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $table = 'absensi';
    public $timestamps = true;

    protected $fillable = [
        'EMAIL',
        'TANGGAL',
        'DATETIME_DATANG',
        'FOTO_DATANG',
        'LOKASI_DATANG',
        'LAT_DATANG',
        'LNG_DATANG',
        'DATETIME_PULANG',
        'FOTO_PULANG',
        'LOKASI_PULANG',
        'LAT_PULANG',
        'LNG_PULANG',
        'STATUS',
        'KOMPENSASI',
        'ALASAN_TIDAK_HADIR',
        'SURAT_IZIN',
        'ID_CABANG'
    ];

    protected $casts = [
        'TANGGAL' => 'date',
        'DATETIME_DATANG' => 'datetime',
        'DATETIME_PULANG' => 'datetime',
        'LAT_DATANG' => 'float',
        'LNG_DATANG' => 'float',
        'LAT_PULANG' => 'float',
        'LNG_PULANG' => 'float',
        'KOMPENSASI' => 'integer'
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'ID_CABANG', 'ID_CABANG');
    }

    /**
     * Calculate status berdasarkan jam jadwal
     * Return: ['status' => 'HADIR'|'TERLAMBAT', 'kompensasi' => 0|-10000]
     */
    public static function calculateStatus($jamMasuk, $absenTime)
    {
        // Parse scheduled time and check-in time as Carbon objects
        // We set the date components to be identical to compare times accurately
        $jamMasukParsed = Carbon::parse($jamMasuk);
        $absenTimeParsed = Carbon::parse($absenTime);
        
        $jamMasukTimeOnly = Carbon::createFromTime($jamMasukParsed->hour, $jamMasukParsed->minute, $jamMasukParsed->second);
        $absenTimeOnly = Carbon::createFromTime($absenTimeParsed->hour, $absenTimeParsed->minute, $absenTimeParsed->second);

        if ($absenTimeOnly->greaterThan($jamMasukTimeOnly)) {
            $diffMinutes = $jamMasukTimeOnly->diffInMinutes($absenTimeOnly);
            // Approximate penalty for check-in response, will be overridden by GajiService
            $potongan = $diffMinutes <= 15 ? -$diffMinutes * 1000 : 0;
            return [
                'status' => 'TERLAMBAT',
                'kompensasi' => $potongan
            ];
        }

        return [
            'status' => 'HADIR',
            'kompensasi' => 0
        ];
    }

    /**
     * Check sudah absen datang
     */
    public function isSudahAbsenDatang()
    {
        return !is_null($this->DATETIME_DATANG);
    }

    /**
     * Check sudah absen pulang
     */
    public function isSudahAbsenPulang()
    {
        return !is_null($this->DATETIME_PULANG);
    }

    /**
     * Check status tidak hadir
     */
    public function isTidakHadir()
    {
        return $this->STATUS === 'TIDAK_HADIR';
    }

    /**
     * Get status label (Bahasa Indonesia)
     */
    public function getStatusLabel()
    {
        $labels = [
            'HADIR' => 'Hadir',
            'TERLAMBAT' => 'Terlambat',
            'TIDAK_HADIR' => 'Tidak Hadir'
        ];
        return $labels[$this->STATUS] ?? $this->STATUS;
    }

    /**
     * Get alasan label
     */
    public function getAlasanLabel()
    {
        if (!$this->ALASAN_TIDAK_HADIR) {
            return null;
        }
        return [
            'SAKIT' => 'Sakit',
            'IZIN' => 'Izin'
        ][$this->ALASAN_TIDAK_HADIR] ?? $this->ALASAN_TIDAK_HADIR;
    }
}

