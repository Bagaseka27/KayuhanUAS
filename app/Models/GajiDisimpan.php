<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiDisimpan extends Model
{
    protected $table = 'gaji_disimpan';
    protected $primaryKey = 'id';

    protected $casts = [
        'NOMINAL' => 'decimal:2',
        'TANGGAL_PENYIMPANAN' => 'date',
        'TANGGAL_DIPROSES' => 'datetime',
    ];

    protected $fillable = [
        'EMAIL',
        'TANGGAL_PENYIMPANAN',
        'NOMINAL',
        'STATUS',
        'CATATAN_ADMIN',
        'DIPROSES_OLEH',
        'TANGGAL_DIPROSES'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }

    public function adminProses()
    {
        return $this->belongsTo(Karyawan::class, 'DIPROSES_OLEH', 'EMAIL');
    }
}
