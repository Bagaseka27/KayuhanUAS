<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';
    protected $fillable = ['nama_lokasi'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
