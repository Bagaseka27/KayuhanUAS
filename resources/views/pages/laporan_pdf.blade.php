<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body{ font-family: Arial; }
        table{ width:100%; border-collapse: collapse; }
        th,td{ border:1px solid black; padding:6px; font-size:12px; }
        th{ background:#f2f2f2; }
    </style>
</head>
<body>

<h2>Laporan Transaksi</h2>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Email</th>
    <th>Total</th>
    <th>Metode</th>
    <th>Status</th>
    <th>Tanggal</th>
</tr>
</thead>

<tbody>
@foreach($data as $trx)
<tr>
<td>{{ $trx->ID_TRANSAKSI }}</td>
<td>{{ $trx->EMAIL }}</td>
<td>Rp {{ number_format($trx->TOTAL_BAYAR,0,',','.') }}</td>
<td>{{ $trx->METODE_PEMBAYARAN }}</td>
<td>{{ $trx->STATUS }}</td>
<td>{{ $trx->DATETIME }}</td>
</tr>
@endforeach
</tbody>

</table>

</body>
</html>