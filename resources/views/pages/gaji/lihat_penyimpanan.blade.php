@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Riwayat Penyimpanan Gaji</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('barista.gaji.formPenyimpanan') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Simpan Gaji
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal Penyimpanan</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Catatan Admin</th>
                            <th>Diproses Oleh</th>
                            <th>Tanggal Diproses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disimpan as $d)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($d->TANGGAL_PENYIMPANAN)->format('d/m/Y') }}</td>
                            <td>
                                <strong>Rp {{ number_format($d->NOMINAL, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($d->STATUS === 'menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($d->STATUS === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($d->CATATAN_ADMIN)
                                    <small>{{ $d->CATATAN_ADMIN }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($d->DIPROSES_OLEH)
                                    {{ $d->adminProses?->NAMA ?? $d->DIPROSES_OLEH }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($d->TANGGAL_DIPROSES)
                                    {{ \Carbon\Carbon::parse($d->TANGGAL_DIPROSES)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Belum ada data penyimpanan gaji
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($disimpan->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    {{ $disimpan->links() }}
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>
@endsection
