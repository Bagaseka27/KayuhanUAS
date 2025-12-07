@extends('layouts.app_barista')

@section('title', 'Riwayat Transaksi Barista')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary-custom mb-0">Riwayat Transaksi</h3>
</div>

<!-- FILTER -->
<div class="stat-card mb-4 py-3">
    <form method="GET" class="row g-3 align-items-end">

        <div class="col-md-3">
            <label class="small text-muted fw-bold">Dari Tanggal</label>
            <input type="date" name="from_date" class="form-control form-control-sm"
                   value="{{ $fromDate }}">
        </div>

        <div class="col-md-3">
            <label class="small text-muted fw-bold">Sampai Tanggal</label>
            <input type="date" name="to_date" class="form-control form-control-sm"
                   value="{{ $toDate }}">
        </div>

        <div class="col-md-3">
            <button class="btn btn-sm w-100 fw-bold text-white" style="background-color: var(--primary);">
                Tampilkan Data
            </button>
        </div>

    </form>
</div>

<!-- STATISTICS -->
<div class="row mb-4">

    <div class="col-md-4">
        <div class="stat-card  shadow-sm">
            <div class="text-xs fw-bold text-uppercase mb-1">TOTAL PENDAPATAN</div>
            <div class="h4 mb-0 fw-bold">
                Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
            </div>
            <small>Semua metode pembayaran</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm">
            <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN TUNAI</div>
            <div class="h4 mb-0 fw-bold">
                Rp {{ number_format($pendapatan_tunai, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm">
            <div class="text-xs fw-bold text-primary-custom text-uppercase mb-1">PENDAPATAN QRIS</div>
            <div class="h4 mb-0 fw-bold">
                Rp {{ number_format($pendapatan_qris, 0, ',', '.') }}
            </div>
        </div>
    </div>

</div>

<!-- TABEL RIWAYAT -->
<div class="stat-card p-0 overflow-hidden">

    <div class="table-responsive">
        <table class="table custom-table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>ID Trx</th>
                    <th>Waktu</th>
                    <th>Item</th>
                    <th>Total Item</th>
                    <th>Metode</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>

            <tbody>

            @forelse($riwayats as $trx)
                <tr>
                    <td class="fw-bold">{{ $trx->ID_TRANSAKSI }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->DATETIME)->format('d M, H:i') }}</td>

                    <td>
                        @foreach($trx->detailtransaksi as $detail)
                            {{ $detail->JML_ITEM }}x {{ $detail->menu->NAMA_PRODUK ?? '-' }}
                            @if(!$loop->last), @endif
                        @endforeach
                    </td>

                    <td class="fw-bold">
                        {{ $trx->detailtransaksi->sum('JML_ITEM') }} Item
                    </td>

                    <td>
                        @if($trx->METODE_PEMBAYARAN == 'QRIS')
                            <span class="badge bg-info text-dark">QRIS</span>
                        @else
                            <span class="badge bg-secondary">Tunai</span>
                        @endif
                    </td>

                    <td class="fw-bold text-primary-custom">
                        Rp {{ number_format($trx->TOTAL_BAYAR, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">
                        Belum ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

</div>

<div class="d-flex justify-content-end mt-3">
    {{ $riwayats->links() }}
</div>

@endsection
