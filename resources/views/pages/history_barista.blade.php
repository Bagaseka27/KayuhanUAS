@extends('layouts.app_barista')

@section('title', 'Riwayat Transaksi Barista')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-primary-custom mb-0">Riwayat Transaksi</h3>
</div>

<div class="stat-card mb-4 py-3">
    <form method="GET" action="{{ route('barista.riwayat') }}" class="row g-3 align-items-end">

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

<div class="row mb-4">

    <div class="col-md-4">
        <div class="stat-card shadow-sm text-center">
            <div class="text-xs fw-bold text-uppercase mb-1">TOTAL PENDAPATAN</div>
            <div class="h4 mb-0 fw-bold text-primary-custom">
                Rp {{ number_format($total_pendapatan, 0, ',', '.') }}
            </div>
            <small class="text-muted">Semua metode pembayaran</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm text-center">
            <div class="text-xs fw-bold text-uppercase mb-1">PENDAPATAN TUNAI</div>
            <div class="h4 mb-0 fw-bold">
                Rp {{ number_format($pendapatan_tunai, 0, ',', '.') }}
            </div>
            <small class="text-muted">Metode: Tunai</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm text-center">
            <div class="text-xs fw-bold text-uppercase mb-1">PENDAPATAN QRIS</div>
            <div class="h4 mb-0 fw-bold">
                Rp {{ number_format($pendapatan_qris, 0, ',', '.') }}
            </div>
            <small class="text-muted">Metode: QRIS</small>
        </div>
    </div>

</div>

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
                    <td>{{ \Carbon\Carbon::parse($trx->DATETIME)->format('d M Y, H:i') }}</td>

                    <td>
                        @foreach($trx->detailtransaksi as $detail)
                            <span class="d-block text-nowrap">
                                {{ $detail->JML_ITEM }}x {{ $detail->menu->NAMA_PRODUK ?? '-' }}
                            </span>
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
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-receipt d-block mb-2 h3"></i>
                        Belum ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-end mt-4 mb-5">
    {{ $riwayats->withPath(route('barista.riwayat'))->links() }}
</div>

@endsection