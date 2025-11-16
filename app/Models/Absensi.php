<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = ['karyawan_id', 'foto_selfie', 'datang', 'pulang'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
