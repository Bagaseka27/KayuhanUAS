<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiPengambilan extends Model
{
    protected $table = 'gaji_pengambilan';
    protected $primaryKey = 'id';

    protected $casts = [
        'NOMINAL' => 'decimal:2',
        'TANGGAL_PENGAMBILAN' => 'date',
        'TANGGAL_DIPROSES' => 'datetime',
    ];

    protected $fillable = [
        'EMAIL',
        'TANGGAL_PENGAMBILAN',
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
