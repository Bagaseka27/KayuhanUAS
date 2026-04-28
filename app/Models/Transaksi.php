<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'ID_TRANSAKSI'; 
    public $incrementing = false; 

    protected $fillable = [
        'ID_TRANSAKSI',
        'EMAIL',
        'DATETIME',
        'TOTAL_BAYAR',
        'METODE_PEMBAYARAN',
        'STATUS',
        'XENDIT_ID'
    ];

    // Relasi: Transaksi memiliki banyak Detail Transaksi (1-to-Many)
    public function detailtransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'ID_TRANSAKSI', 'ID_TRANSAKSI');
    }
    
    // Relasi: Transaksi dimiliki oleh satu Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }
}