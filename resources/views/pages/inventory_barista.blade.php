@extends(Auth::user()->role == 'Barista' ? 'layouts.app_barista' : 'layouts.app')

@section('title', 'Manajemen Stok Rombong - Barista')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-primary-custom mb-1">Stok Rombong Anda</h3>
        <p class="text-muted mb-0">
            ID Rombong Aktif: 
            @if($idRombongBarista)
                <span class="badge bg-success"> Rombong #{{ $idRombongBarista }}</span>
            @else
                <span class="badge bg-danger fw-bold">Belum Terdaftar</span>
            @endif
        </p>
    </div>
    
    {{-- Tombol Ambil Stok hanya muncul jika barista sudah punya ID Rombong --}}
    @if($idRombongBarista)
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalBatchRombong">
            <i class="fas fa-plus me-2"></i> Ambil Stok dari Gudang
        </button>
    @endif
</div>

{{-- JIKA BELUM ADA SHIFT / BELUM DITUGASKAN DI ROMBONG --}}
@if(!$idRombongBarista)
    <div class="card p-5 text-center shadow-sm border-0">
        <div class="card-body">
            <div class="text-warning mb-3" style="font-size: 3rem;">
                <i class="fas fa-store-slash"></i>
            </div>
            <h5 class="fw-bold text-dark">Anda Belum Ditugaskan di Rombong</h5>
            <p class="text-muted mx-auto" style="max-width: 500px;">
                Maaf, data persediaan tidak dapat ditampilkan karena akun Anda belum dijadwalkan atau ditugaskan di rombong mana pun hari ini. Silakan hubungi Admin untuk informasi shift Anda.
            </p>
        </div>
    </div>
@else
    {{-- JIKA SUDAH ADA ROMBONG, TAMPILKAN TABEL SEPERTI BIASA --}}
    <div class="card p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID BARANG</th>
                        <th>NAMA BARANG</th>
                        <th class="text-center">STOK AWAL</th>
                        <th class="text-center">STOK AKHIR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stokRombong as $r)
                    <tr>
                        <td class="fw-bold">{{ $r->barang_id }}</td>
                        <td>{{ $r->NAMA_BARANG ?? 'Barang Tidak Ditemukan' }}</td>
                        <td class="text-center">{{ $r->stok_awal ?? 0 }}</td>
                        <td class="text-center fw-bold {{ $r->stok_akhir < $r->stok_awal ? 'text-danger' : 'text-success' }}">
                            {{ $r->stok_akhir ?? 0 }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            Belum ada stok di rombong ini. Silakan ambil dari gudang utama menggunakan tombol di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- ======================= MODAL BATCH AMBIL STOK GUDANG ======================= --}}
<div class="modal fade" id="modalBatchRombong" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('barista.inventory.batchStore') }}" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i>Form Pengambilan Stok Gudang (Batch)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="text-muted small mb-3">Pilih barang dan tentukan kuantitas yang diambil. Sistem akan otomatis memotong ketersediaan stok di gudang utama.</p>
                
                <div id="batch-container">
                    {{-- Baris Pertama Form Input (Default) --}}
                    <div class="row g-2 mb-2 align-items-center batch-row">
                        <div class="col-md-7">
                            <select name="barang_id[]" class="form-select" required>
                                <option value="">-- Pilih Barang dari Gudang --</option>
                                @foreach($masterBarang as $mb)
                                    <option value="{{ $mb->ID_BARANG }}">{{ $mb->ID_BARANG }} - {{ $mb->NAMA_BARANG }} (Sisa Gudang: {{ $mb->JUMLAH }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah Ambil" min="1" required>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="display:none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-tambah-baris">
                    <i class="fas fa-plus me-1"></i> Tambah Baris
                </button>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Ambil</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('batch-container');
    const btnTambah = document.getElementById('btn-tambah-baris');

    // Fungsi Tambah Baris Baru di Modal Batch
    btnTambah.addEventListener('click', function() {
        const rows = container.querySelectorAll('.batch-row');
        const firstRow = rows[0];
        
        // Clone baris pertama
        const newRow = firstRow.cloneNode(true);
        
        // Reset nilai input di baris clone
        newRow.querySelector('select').value = "";
        newRow.querySelector('input').value = "";
        
        // Munculkan tombol hapus baris di baris baru
        const removeBtn = newRow.querySelector('.btn-remove-row');
        removeBtn.style.display = "block";
        
        // Daftarkan event listener hapus baris
        removeBtn.addEventListener('click', function() {
            newRow.remove();
        });

        container.appendChild(newRow);
    });
});
</script>
@endpush