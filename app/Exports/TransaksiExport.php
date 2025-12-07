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
        // 1. Terima Query Builder yang sudah difilter dari Controller
        $this->query = $query;
    }

    public function query()
    {
        // 2. Terapkan eager loading untuk relasi yang dibutuhkan:
        // 'karyawan' untuk nama kasir
        // 'detailtransaksi.menu' untuk detail produk
        return $this->query->with(['karyawan', 'detailtransaksi.menu'])
                           ->orderBy('DATETIME', 'desc');
    }

    public function headings(): array
    {
        // Penyesuaian header agar lebih akurat dengan data yang digabungkan
        return [
            'ID Transaksi',
            'Waktu Transaksi',
            'Kasir (Nama)', 
            'Metode Pembayaran',
            'Total Pembayaran',
            'Detail Item & Kuantitas', // Kolom gabungan
            'Total Item Terjual',
        ];
    }

    public function map($transaksi): array
    {
        // Inisialisasi variabel untuk perhitungan
        $totalItems = 0;
        $detailItemsString = '';

        // 3. Loop melalui Detail Transaksi untuk mendapatkan informasi item
        $details = $transaksi->detailtransaksi;
        
        if ($details->isNotEmpty()) {
            $mappedDetails = $details->map(function ($detail) use (&$totalItems) {
                $namaProduk = $detail->menu->NAMA_PRODUK ?? 'Produk Dihapus';
                $totalItems += $detail->JML_ITEM; // Hitung total item
                return "{$detail->JML_ITEM}x {$namaProduk}";
            })->implode('; '); // Gabungkan menjadi string tunggal
            
            $detailItemsString = $mappedDetails;
        }


        // 4. Mapping data ke baris Excel
        return [
            $transaksi->ID_TRANSAKSI,
            \Carbon\Carbon::parse($transaksi->DATETIME)->format('Y-m-d H:i:s'),
            $transaksi->karyawan->NAMA ?? $transaksi->EMAIL, // Fallback ke email jika nama kosong
            $transaksi->METODE_PEMBAYARAN,
            $transaksi->TOTAL_BAYAR, // Biarkan sebagai angka jika Excel akan memformatnya
            $detailItemsString,
            $totalItems,
        ];
    }
}