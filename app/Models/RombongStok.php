<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RombongStok extends Model
{
    protected $table = 'rombong_stok';

    // TAMBAHKAN 3 BARIS INI UNTUK MENYELAMATKAN STRING PRIMARY KEY
    protected $primaryKey = null; 
    public $incrementing = false;          
    protected $keyType = 'string';         

    protected $fillable = [
        'barang_id',
        'rombong_id',
        'stok_awal',
        'stok_akhir'
    ];

    public function barang()
    {
        return $this->belongsTo(StokGudang::class, 'barang_id', 'ID_BARANG');
    }

    public function rombong()
    {
        return $this->belongsTo(Rombong::class, 'rombong_id', 'ID_ROMBONG');
    }
}