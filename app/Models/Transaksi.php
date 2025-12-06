<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    // Gunakan primary key yang Anda definisikan di ERD
    protected $primaryKey = 'ID_TRANSAKSI'; 
    public $incrementing = false; // Karena ID_TRANSAKSI adalah custom string

    protected $fillable = [
        'ID_TRANSAKSI',
        'EMAIL',
        'JUMLAH_ITEM',
        'HARGA_ITEM',
        'DATETIME',
        'TOTAL_BAYAR',
        'METODE_PEMBAYARAN',
    ];

    // Relasi: Transaksi memiliki banyak Detail Transaksi (1-to-Many)
    // Relasi ini yang dicari Controller saat menyimpan keranjang
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