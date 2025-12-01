<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Transaksi::with('karyawan')->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Email Karyawan',
            'Nama Karyawan', 
            'Jumlah Item',
            'Harga Item',
            'Total Bayar',
            'Metode Pembayaran',
            'Waktu Transaksi',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->ID_TRANSAKSI,
            $transaksi->EMAIL,
            $transaksi->karyawan->NAMA ?? '-', 
            $transaksi->JUMLAH_ITEM,
            'Rp ' . number_format($transaksi->HARGA_ITEM, 0, ',', '.'),
            'Rp ' . number_format($transaksi->TOTAL_BAYAR, 0, ',', '.'),
            $transaksi->METODE_PEMBAYARAN,
            $transaksi->DATETIME,
        ];
    }
}