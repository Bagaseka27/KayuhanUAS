<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct(Builder $query)
    {
    
        $this->query = $query;
    }

    public function query()
    {
        //'detailtransaksi.menu' untuk detail produk
        return $this->query->with(['karyawan', 'detailtransaksi.menu'])
                           ->orderBy('DATETIME', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Waktu Transaksi',
            'Kasir (Nama)', 
            'Metode Pembayaran',
            'Total Pembayaran',
            'Detail Item & Kuantitas', 
            'Total Item Terjual',
        ];
    }

    public function map($transaksi): array
    {
        // Variabel untuk perhitungan
        $totalItems = 0;
        $detailItemsString = '';

        // 3. Loop melalui Detail Transaksi untuk mendapatkan informasi item
        $details = $transaksi->detailtransaksi;
        
        if ($details->isNotEmpty()) {
            $mappedDetails = $details->map(function ($detail) use (&$totalItems) {
                $namaProduk = $detail->menu->NAMA_PRODUK ?? 'Produk Dihapus';
                $totalItems += $detail->JML_ITEM; 
                return "{$detail->JML_ITEM}x {$namaProduk}";
            })->implode('; ');
            
            $detailItemsString = $mappedDetails;
        }

        // 4. Mapping data ke baris Excel
        return [
            $transaksi->ID_TRANSAKSI,
            \Carbon\Carbon::parse($transaksi->DATETIME)->format('Y-m-d H:i:s'),
            $transaksi->karyawan->NAMA ?? $transaksi->EMAIL, 
            $transaksi->METODE_PEMBAYARAN,
            $transaksi->TOTAL_BAYAR, 
            $detailItemsString,
            $totalItems,
        ];
    }
}