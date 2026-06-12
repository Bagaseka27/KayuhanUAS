<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiHarian extends Model
{
    protected $table = 'gaji_harian';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = "int";
    public $timestamps = true;

    protected $casts = [
        'JAM_KERJA_TERJADWAL' => 'decimal:2',
        'GAJI_PER_JAM' => 'decimal:2',
        'GAJI_POKOK_HARIAN' => 'decimal:2',
        'BONUS_PER_CUP' => 'decimal:2',
        'BONUS_HARIAN' => 'decimal:2',
        'POTONGAN_TERLAMBAT' => 'decimal:2',
        'TOTAL_GAJI_HARIAN' => 'decimal:2',
    ];

    protected $fillable = [
        'EMAIL',
        'TANGGAL',
        'JAM_KERJA_TERJADWAL',
        'JAM_MULAI_JADWAL',
        'JAM_SELESAI_JADWAL',
        'WAKTU_DATANG',
        'WAKTU_PULANG',
        'MENIT_TERLAMBAT',
        'GAJI_PER_JAM',
        'GAJI_POKOK_HARIAN',
        'PENJUALAN_CUP',
        'CUP_BONUS',
        'BONUS_PER_CUP',
        'BONUS_HARIAN',
        'POTONGAN_TERLAMBAT',
        'POTONGAN_50_PCT',
        'TOTAL_GAJI_HARIAN',
        'STATUS_ABSENSI'
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }
}
