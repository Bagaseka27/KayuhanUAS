<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rombong extends Model
{
    protected $table = 'rombong';
    protected $primaryKey = 'ID_ROMBONG';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    
    protected $fillable = [
        'ID_ROMBONG',
        'ID_CABANG'
    ];
    
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'ID_ROMBONG');
    }

    public function stokawal()
    {
        return $this->hasOne(StokAwal::class, 'ID_ROMBONG');
    }

    public function stokakhir()
    {
        return $this->hasOne(StokAkhir::class, 'ID_ROMBONG');
    }

    public function cabang(){
        return $this->belongsTo(Cabang::class,'ID_CABANG');
    }
}
