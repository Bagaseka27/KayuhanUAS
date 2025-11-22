<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'ID_JADWAL';
    public $incrementing = false;
    public $timestamps = false;

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'ID_CABANG');
    }
}
