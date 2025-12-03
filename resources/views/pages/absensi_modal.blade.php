<div class="modal fade" id="absen{{ $type }}Modal" tabindex="-1" aria-labelledby="absen{{ $type }}ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absen{{ $type }}ModalLabel">Absen {{ $type }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('barista.absensi.store'. $type) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info text-center">
                        Silakan unggah foto Anda saat presensi.
                    </div>

                    <div class="mb-3">
                        <label for="foto_{{ strtolower($type) }}" class="form-label">Unggah Foto Absensi</label>
                        
                        <input class="form-control" type="file" id="foto_{{ strtolower($type) }}" name="FOTO_FILE" accept="image/*" required>
                        <small class="form-text text-muted">Format yang diizinkan: JPEG, PNG.</small>
                    </div>

                    <div class="mt-3">
                        <p>Waktu Absensi: <strong id="timestamp-{{ strtolower($type) }}">{{ \Carbon\Carbon::now()->translatedFormat('H:i:s, d F Y') }}</strong></p>
                        
                        <input type="hidden" name="DATETIME_{{ strtoupper($type) }}" value="{{ now()->format('Y-m-d H:i:s') }}">
                        <input type="hidden" name="EMAIL" value="{{ Auth::user()->email }}">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        Simpan Absen {{ $type }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mendapatkan waktu saat ini dalam format Y-m-d H:i:s
    function getCurrentTimeFormatted() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    // Fungsi untuk memperbarui tampilan waktu di modal
    function updateTimestamp(type) {
        const now = new Date();
        const displayOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', year: 'numeric', month: 'long', day: 'numeric' };
        
        // Perbarui teks yang dilihat pengguna
        document.getElementById('timestamp-' + type).innerText = now.toLocaleTimeString('id-ID', displayOptions);
        
        // Perbarui nilai input tersembunyi yang akan dikirim ke Controller
        const inputName = 'DATETIME_' + type.toUpperCase();
        document.querySelector('input[name="' + inputName + '"]').value = getCurrentTimeFormatted();
    }

    // Panggil fungsi update setiap detik
    setInterval(() => {
        // Asumsi modal dipanggil dengan 'Datang' dan 'Pulang'
        updateTimestamp('datang'); 
        updateTimestamp('pulang'); 
    }, 1000);

    // Pastikan modal selalu menampilkan waktu saat dibuka (Jika belum ada setInterval)
    $('#absenDatangModal').on('shown.bs.modal', function () {
        updateTimestamp('datang');
    });
    $('#absenPulangModal').on('shown.bs.modal', function () {
        updateTimestamp('pulang');
    });

</script>