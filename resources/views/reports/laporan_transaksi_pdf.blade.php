<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .summary {
            margin-bottom: 20px;
            font-size: 11px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Riwayat Transaksi Kayuhan</h2>
        <p>
            Dicetak oleh: {{ $rolePrefix }} |
            Periode:
            {{ \Carbon\Carbon::parse($fromDate)->format('d F Y') }}
            s/d
            {{ \Carbon\Carbon::parse($toDate)->format('d F Y') }}
        </p>
    </div>

    <div class="summary">
        <strong>RINGKASAN PENDAPATAN</strong>
        <ul>
            <li>Total Pendapatan: Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</li>
            <li>Pendapatan Tunai: Rp {{ number_format($pendapatan_tunai, 0, ',', '.') }}</li>
            <li>Pendapatan QRIS: Rp {{ number_format($pendapatan_qris, 0, ',', '.') }}</li>
        </ul>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID Trx</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Detail Item</th>
            </tr>
        </thead>

        <tbody>
            @forelse($riwayats as $trx)
                <tr>
                    <td>{{ $trx->ID_TRANSAKSI }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->DATETIME)->format('d/m H:i') }}</td>
                    <td>{{ $trx->karyawan->NAMA ?? $trx->EMAIL }}</td>
                    <td>{{ $trx->METODE_PEMBAYARAN }}</td>
                    <td>Rp {{ number_format($trx->TOTAL_BAYAR, 0, ',', '.') }}</td>
                    <td>
                        @foreach($trx->detailtransaksi as $detail)
                            {{ $detail->JML_ITEM }}x
                            {{ $detail->menu->NAMA_PRODUK ?? 'Produk Dihapus' }}<br>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">
                        Tidak ada data transaksi yang ditemukan dalam periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
