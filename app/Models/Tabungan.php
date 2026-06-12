<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    protected $table = 'tabungan';
    protected $primaryKey = 'EMAIL';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'EMAIL',
        'SALDO',
    ];

    /**
     * Relationship to Employee
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'EMAIL', 'EMAIL');
    }

    /**
     * Synchronize the Tabungan balance with approved deposits/withdrawals
     */
    public static function syncTabungan($email)
    {
        $totalDisimpan = GajiDisimpan::where('EMAIL', $email)
            ->where('STATUS', 'disetujui')
            ->sum('NOMINAL');

        $tabungan = self::updateOrCreate(
            ['EMAIL' => $email],
            ['SALDO' => $totalDisimpan]
        );

        return $tabungan->SALDO;
    }
}
