<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2   { text-align: center; margin-bottom: 4px; }
        p.sub{ text-align: center; margin-top: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th    { background: #003d2e; color: #fff; padding: 6px 8px; text-align: left; }
        td    { padding: 5px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background: #f5f5f5; }
        .summary { margin-top: 16px; }
        .summary td { border: none; font-size: 12px; }
        .summary .label { color: #555; }
        .summary .value { font-weight: bold; }
    </style>
</head>
<body>

    <h2>Laporan Transaksi - Kayuhan</h2>
    <p class="sub">Periode: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>

    <!-- Ringkasan -->
    <table class="summary">
        <tr>
            <td class="label">Total Pendapatan</td>
            <td class="value">: Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</td>
            <td width="40"></td>
            <td class="label">Pendapatan Tunai</td>
            <td class="value">: Rp {{ number_format($pendapatan_tunai, 0, ',', '.') }}</td>
            <td width="40"></td>
            <td class="label">Pendapatan QRIS</td>
            <td class="value">: Rp {{ number_format($pendapatan_qris, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Tabel Transaksi -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ID Transaksi</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Item</th>
                <th>Metode</th>
                <th>Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayats as $i => $trx)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $trx->ID_TRANSAKSI }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->DATETIME)->format('d M Y, H:i') }}</td>
                <td>{{ $trx->karyawan->NAMA ?? $trx->EMAIL }}</td>
                <td>
                    @foreach($trx->detailtransaksi as $d)
                        {{ $d->JML_ITEM }}x {{ $d->menu->NAMA_PRODUK ?? '-' }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td>{{ $trx->METODE_PEMBAYARAN }}</td>
                <td>Rp {{ number_format($trx->TOTAL_BAYAR, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#999;">Tidak ada data transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top:20px; color:#888; font-size:10px;">
        Dicetak pada: {{ now()->format('d M Y, H:i:s') }}
    </p>

</body>
</html>