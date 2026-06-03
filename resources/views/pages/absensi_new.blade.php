@extends('layouts.app_barista')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Kehadiran</h1>
        <small class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</small>
    </div>

    {{-- Alert Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Jadwal Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-info mb-3">📅 Jadwal Hari Ini</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Jam Kerja:</strong> {{ $jadwal['jam_masuk'] }} - {{ $jadwal['jam_pulang'] }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Lokasi:</strong> {{ $jadwal['lokasi'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Absensi --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <h6 class="font-weight-bold text-info mb-3">📊 Status Absensi</h6>

            @if ($absensi?->isTidakHadir())
                <div class="alert alert-warning">
                    <strong>Status: Tidak Hadir</strong> ({{ $absensi->getAlasanLabel() }})
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert {{ $absensi?->isSudahAbsenDatang() ? 'alert-success' : 'alert-secondary' }}">
                            <strong>Absen Masuk:</strong>
                            @if ($absensi?->isSudahAbsenDatang())
                                ✅ {{ $absensi->DATETIME_DATANG->format('H:i:s') }}
                                <br><small class="text-success">Status: {{ $absensi->getStatusLabel() }}</small>
                                @if ($absensi->KOMPENSASI < 0)
                                    <br><small class="text-danger">Kompensasi: Rp {{ number_format(abs($absensi->KOMPENSASI)) }}</small>
                                @endif
                            @else
                                ❌ Belum Absen
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert {{ $absensi?->isSudahAbsenPulang() ? 'alert-success' : 'alert-secondary' }}">
                            <strong>Absen Pulang:</strong>
                            @if ($absensi?->isSudahAbsenPulang())
                                ✅ {{ $absensi->DATETIME_PULANG->format('H:i:s') }}
                            @elseif ($absensi?->isSudahAbsenDatang())
                                @if ($bisakAbsenPulang)
                                    ❌ Belum Absen
                                @else
                                    🔒 Terkunci (Tunggu jam {{ $jadwal['jam_pulang'] }})
                                @endif
                            @else
                                - Absen masuk dulu
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="row mb-4">
        {{-- Absen Masuk --}}
        <div class="col-md-6">
            @if (!$absensi?->isSudahAbsenDatang() && !$absensi?->isTidakHadir())
                <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal" data-bs-target="#cameraModalDatang">
                    <i class="fas fa-sign-in-alt"></i> Absen Masuk
                </button>
            @else
                <button class="btn btn-success btn-lg w-100" disabled>
                    <i class="fas fa-check-circle"></i> Sudah Absen Masuk
                </button>
            @endif
        </div>

        {{-- Absen Pulang --}}
        <div class="col-md-6">
            @if ($bisakAbsenPulang && !$absensi->isSudahAbsenPulang())
                <button class="btn btn-warning btn-lg w-100" data-bs-toggle="modal" data-bs-target="#cameraModalPulang">
                    <i class="fas fa-sign-out-alt"></i> Absen Pulang
                </button>
            @elseif ($absensi?->isSudahAbsenPulang())
                <button class="btn btn-warning btn-lg w-100" disabled>
                    <i class="fas fa-check-circle"></i> Sudah Absen Pulang
                </button>
            @else
                <button class="btn btn-warning btn-lg w-100" disabled title="Tunggu jam pulang atau absen masuk dulu">
                    🔒 Absen Pulang (Terkunci)
                </button>
            @endif
        </div>
    </div>

    {{-- Tidak Hadir --}}
    @if (!$absensi?->isSudahAbsenDatang() && !$absensi?->isTidakHadir())
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#tidakHadirModal">
                    <i class="fas fa-file-medical"></i> Tidak Hadir (Sakit/Izin)
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Modal Camera Absen Datang --}}
<div class="modal fade" id="cameraModalDatang" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Absen Masuk - Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Camera Section --}}
                    <div class="col-md-6">
                        <h6>Kamera</h6>
                        <video id="cameraStreamDatang" class="w-100 border rounded" autoplay playsinline style="max-height: 400px; background: #000;"></video>
                        <button type="button" class="btn btn-primary w-100 mt-2" id="captureDatang">
                            📸 Ambil Foto
                        </button>
                    </div>

                    {{-- Preview Section --}}
                    <div class="col-md-6">
                        <h6>Preview</h6>
                        <canvas id="canvasDatang" class="w-100 border rounded" style="max-height: 400px; display: none;"></canvas>
                        <img id="previewDatang" class="w-100 border rounded" style="max-height: 400px; display: none;">
                        <button type="button" class="btn btn-secondary w-100 mt-2" id="retakeDatang" style="display: none;">
                            🔄 Ambil Ulang
                        </button>
                    </div>
                </div>

                {{-- Location Capture Section --}}
                <div class="mt-3" id="locationSectionDatang" style="display: none;">
                    <h6>📍 Lokasi Berhasil Dikapture</h6>
                    <div class="alert alert-success">
                        <small id="locationStatusDatang">✅ Lokasi berhasil dikapture</small>
                    </div>
                    <label class="form-label text-muted"><small>Nama Lokasi</small></label>
                    <input type="text" id="lokasiDatang" class="form-control mb-2" placeholder="Mengakses nama lokasi..." readonly>
                    <label class="form-label text-muted"><small>Koordinat GPS</small></label>
                    <input type="text" id="koordinatDatang" class="form-control" placeholder="Koordinat" readonly>
                    <input type="hidden" id="latDatang">
                    <input type="hidden" id="lngDatang">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitDatang" style="display: none;">Simpan Absen</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Camera Absen Pulang --}}
<div class="modal fade" id="cameraModalPulang" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Absen Pulang - Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Kamera</h6>
                        <video id="cameraStreamPulang" class="w-100 border rounded" autoplay playsinline style="max-height: 400px; background: #000;"></video>
                        <button type="button" class="btn btn-primary w-100 mt-2" id="capturePulang">
                            📸 Ambil Foto
                        </button>
                    </div>
                    <div class="col-md-6">
                        <h6>Preview</h6>
                        <canvas id="canvasPulang" class="w-100 border rounded" style="max-height: 400px; display: none;"></canvas>
                        <img id="previewPulang" class="w-100 border rounded" style="max-height: 400px; display: none;">
                        <button type="button" class="btn btn-secondary w-100 mt-2" id="retakePulang" style="display: none;">
                            🔄 Ambil Ulang
                        </button>
                    </div>
                </div>
                {{-- Location Capture Section --}}
                <div class="mt-3" id="locationSectionPulang" style="display: none;">
                    <h6>📍 Lokasi Berhasil Dikapture</h6>
                    <div class="alert alert-success">
                        <small id="locationStatusPulang">✅ Lokasi berhasil dikapture</small>
                    </div>
                    <label class="form-label text-muted"><small>Nama Lokasi</small></label>
                    <input type="text" id="lokasiPulang" class="form-control mb-2" placeholder="Mengakses nama lokasi..." readonly>
                    <label class="form-label text-muted"><small>Koordinat GPS</small></label>
                    <input type="text" id="koordinatPulang" class="form-control" placeholder="Koordinat" readonly>
                    <input type="hidden" id="latPulang">
                    <input type="hidden" id="lngPulang">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitPulang" style="display: none;">Simpan Absen</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tidak Hadir --}}
<div class="modal fade" id="tidakHadirModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tidak Hadir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Alasan</label>
                    <select class="form-select" id="alasanTidakHadir">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="SAKIT">Sakit</option>
                        <option value="IZIN">Izin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Surat Izin</label>
                    <input type="file" class="form-control" id="suratIzinFile" accept="image/*,.pdf">
                    <small class="text-muted">Format: JPG, PNG, PDF (Max 5MB)</small>
                </div>
                <div id="previewSuratIzin" style="margin-top: 15px; display: none;">
                    <h6>Preview</h6>
                    <img id="imgPreviewSuratIzin" class="w-100 border rounded" style="max-height: 300px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="submitTidakHadir">Ajukan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .leaflet-container { background: #f0f0f0; }
</style>
@endpush

@push('scripts')
<script>
    // ===== CAMERA UTILITIES =====
    class CameraHandler {
        constructor(videoId, canvasId, previewId, captureId, retakeId, locationSectionId) {
            this.video = document.getElementById(videoId);
            this.canvas = document.getElementById(canvasId);
            this.preview = document.getElementById(previewId);
            this.captureBtn = document.getElementById(captureId);
            this.retakeBtn = document.getElementById(retakeId);
            this.locationSection = document.getElementById(locationSectionId);
            this.photoBase64 = null;
            this.stream = null;

            this.init();
        }

        async init() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' }
                });
                this.video.srcObject = this.stream;
            } catch (err) {
                alert('❌ Tidak bisa akses kamera. Pastikan browser punya permission dan coba lagi.');
                console.error(err);
            }

            this.captureBtn.addEventListener('click', () => this.capture());
            this.retakeBtn.addEventListener('click', () => this.retake());
        }

        capture() {
            const context = this.canvas.getContext('2d');
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
            context.drawImage(this.video, 0, 0);

            this.photoBase64 = this.canvas.toDataURL('image/jpeg');
            this.preview.src = this.photoBase64;

            this.video.style.display = 'none';
            this.canvas.style.display = 'none';
            this.preview.style.display = 'block';
            this.captureBtn.style.display = 'none';
            this.retakeBtn.style.display = 'block';
            this.locationSection.style.display = 'block';
        }

        retake() {
            this.video.style.display = 'block';
            this.canvas.style.display = 'none';
            this.preview.style.display = 'none';
            this.captureBtn.style.display = 'block';
            this.retakeBtn.style.display = 'none';
            this.locationSection.style.display = 'none';
            this.photoBase64 = null;
        }

        getPhoto() {
            return this.photoBase64;
        }

        stopStream() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
        }
    }

    // ===== GEOLOCATION UTILITIES =====
    class GeolocationHandler {
        constructor(locationInputId, koordinatInputId, latInputId, lngInputId, statusId) {
            this.locationInput = document.getElementById(locationInputId);
            this.koordinatInput = document.getElementById(koordinatInputId);
            this.latInput = document.getElementById(latInputId);
            this.lngInput = document.getElementById(lngInputId);
            this.statusEl = document.getElementById(statusId);
            this.location = null;
        }

        async capture() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    this.statusEl.textContent = '❌ Browser tidak support geolocation';
                    reject('No geolocation support');
                    return;
                }

                this.statusEl.textContent = '⏳ Mengakses GPS Anda...';

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const { latitude, longitude } = position.coords;
                        this.location = {
                            lat: latitude,
                            lng: longitude,
                            name: `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`
                        };

                        this.latInput.value = latitude;
                        this.lngInput.value = longitude;
                        this.koordinatInput.value = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                        
                        // Get location name via reverse geocoding
                        await this.reverseGeocode(latitude, longitude);

                        this.statusEl.innerHTML = '✅ Lokasi berhasil dikapture';
                        resolve(this.location);
                    },
                    (error) => {
                        let message = '❌ Gagal mengakses GPS: ';
                        if (error.code === error.PERMISSION_DENIED) {
                            message += 'Izin ditolak. Aktifkan GPS di browser.';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            message += 'GPS tidak tersedia.';
                        } else if (error.code === error.TIMEOUT) {
                            message += 'Timeout. Coba lagi.';
                        }
                        this.statusEl.textContent = message;
                        reject(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        }

        async reverseGeocode(lat, lng) {
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                );
                const data = await response.json();
                
                // Extract location name from various possible address components
                let locationName = 'Lokasi tidak ditemukan';
                if (data.address) {
                    const addr = data.address;
                    // Try to get meaningful location name
                    locationName = addr.road || addr.street || 
                                  addr.hamlet || addr.village || 
                                  addr.town || addr.city || 
                                  addr.county || addr.state || 
                                  'Lokasi tidak ditemukan';
                }
                
                this.locationInput.value = locationName;
                this.location.name = locationName;
            } catch (err) {
                console.error('Reverse geocoding error:', err);
                // Fallback to coordinates if reverse geocoding fails
                this.locationInput.value = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
        }

        getLocation() {
            return this.location;
        }
    }

    // ===== ABSEN DATANG HANDLERS =====
    let cameraDatang = null;
    const geoDatang = new GeolocationHandler('lokasiDatang', 'koordinatDatang', 'latDatang', 'lngDatang', 'locationStatusDatang');

    // Inisialisasi kamera SAAT modal dibuka
    document.getElementById('cameraModalDatang').addEventListener('show.bs.modal', () => {
        cameraDatang = new CameraHandler('cameraStreamDatang', 'canvasDatang', 'previewDatang', 'captureDatang', 'retakeDatang', 'locationSectionDatang');

        // Override capture untuk auto-capture lokasi
        const originalCapture = cameraDatang.capture.bind(cameraDatang);
        cameraDatang.capture = function() {
            originalCapture();
            setTimeout(() => geoDatang.capture().then(() => {
                document.getElementById('submitDatang').style.display = 'block';
            }).catch(err => {
                console.error('Geolocation error:', err);
                document.getElementById('locationStatusDatang').innerHTML = '⚠️ Gagal akses GPS, tapi Anda bisa lanjut';
                document.getElementById('submitDatang').style.display = 'block';
            }), 500);
        };
    });

    // Stop kamera saat modal ditutup
    document.getElementById('cameraModalDatang').addEventListener('hidden.bs.modal', () => {
        if (cameraDatang) {
            cameraDatang.stopStream();
            cameraDatang = null;
        }
        document.getElementById('submitDatang').style.display = 'none';
    });

    // Reset geolocation saat retake
    document.getElementById('retakeDatang').addEventListener('click', function() {
        geoDatang.location = null;
        document.getElementById('locationStatusDatang').textContent = '⏳ Mengakses GPS Anda...';
        document.getElementById('submitDatang').style.display = 'none';
    });

    document.getElementById('submitDatang').addEventListener('click', async () => {
        if (!cameraDatang.getPhoto()) {
            alert('❌ Ambil foto terlebih dahulu');
            return;
        }

        if (!geoDatang.getLocation()) {
            alert('⚠️ GPS belum dikapture, pastikan izin GPS aktif');
            return;
        }

        const location = geoDatang.getLocation();
        const data = {
            foto: cameraDatang.getPhoto(),
            lokasi: location.name,
            lat: location.lat,
            lng: location.lng,
            datetime: (() => {
                const now = new Date();
                return now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0') + ' ' +
                    String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
            })()
        };

        try {
            document.getElementById('submitDatang').disabled = true;
            document.getElementById('submitDatang').textContent = '⏳ Menyimpan...';

            const response = await fetch('{{ route("barista.absensi.submitDatang") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                alert('✅ Absen masuk berhasil! Status: ' + result.status);
                location.reload();
            } else {
                alert('❌ Gagal: ' + result.message);
            }
        } catch (err) {
            alert('❌ Error: ' + err.message);
        } finally {
            document.getElementById('submitDatang').disabled = false;
            document.getElementById('submitDatang').textContent = 'Simpan Absen';
        }
    });

    // ===== ABSEN PULANG HANDLERS =====
    let cameraPulang = null;
    const geoPulang = new GeolocationHandler('lokasiPulang', 'koordinatPulang', 'latPulang', 'lngPulang', 'locationStatusPulang');

    // Inisialisasi kamera SAAT modal dibuka
    document.getElementById('cameraModalPulang').addEventListener('show.bs.modal', () => {
        cameraPulang = new CameraHandler('cameraStreamPulang', 'canvasPulang', 'previewPulang', 'capturePulang', 'retakePulang', 'locationSectionPulang');

        // Override capture untuk auto-capture lokasi
        const originalCapture = cameraPulang.capture.bind(cameraPulang);
        cameraPulang.capture = function() {
            originalCapture();
            setTimeout(() => geoPulang.capture().then(() => {
                document.getElementById('submitPulang').style.display = 'block';
            }).catch(err => {
                console.error('Geolocation error:', err);
                document.getElementById('locationStatusPulang').innerHTML = '⚠️ Gagal akses GPS, tapi Anda bisa lanjut';
                document.getElementById('submitPulang').style.display = 'block';
            }), 500);
        };
    });

    // Stop kamera saat modal ditutup
    document.getElementById('cameraModalPulang').addEventListener('hidden.bs.modal', () => {
        if (cameraPulang) {
            cameraPulang.stopStream();
            cameraPulang = null;
        }
        document.getElementById('submitPulang').style.display = 'none';
    });

    // Reset geolocation saat retake
    document.getElementById('retakePulang').addEventListener('click', function() {
        geoPulang.location = null;
        document.getElementById('locationStatusPulang').textContent = '⏳ Mengakses GPS Anda...';
        document.getElementById('submitPulang').style.display = 'none';
    });

    document.getElementById('submitPulang').addEventListener('click', async () => {
        if (!cameraPulang.getPhoto()) {
            alert('❌ Ambil foto terlebih dahulu');
            return;
        }

        if (!geoPulang.getLocation()) {
            alert('⚠️ GPS belum dikapture, pastikan izin GPS aktif');
            return;
        }

        const location = geoPulang.getLocation();
        const data = {
            foto: cameraPulang.getPhoto(),
            lokasi: location.name,
            lat: location.lat,
            lng: location.lng,
            datetime: (() => {
                const now = new Date();
                return now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0') + ' ' +
                    String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');
            })()
        };

        try {
            document.getElementById('submitPulang').disabled = true;
            document.getElementById('submitPulang').textContent = '⏳ Menyimpan...';

            const response = await fetch('{{ route("barista.absensi.submitPulang") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                alert('✅ Absen pulang berhasil!');
                location.reload();
            } else {
                alert('❌ Gagal: ' + result.message);
            }
        } catch (err) {
            alert('❌ Error: ' + err.message);
        } finally {
            document.getElementById('submitPulang').disabled = false;
            document.getElementById('submitPulang').textContent = 'Simpan Absen';
        }
    });

    // ===== TIDAK HADIR HANDLERS =====
    document.getElementById('suratIzinFile').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (event) => {
            const imgPreview = document.getElementById('imgPreviewSuratIzin');
            imgPreview.src = event.target.result;
            document.getElementById('previewSuratIzin').style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('submitTidakHadir').addEventListener('click', async () => {
        const alasan = document.getElementById('alasanTidakHadir').value;
        const suratFile = document.getElementById('suratIzinFile').files[0];

        if (!alasan) {
            alert('❌ Pilih alasan terlebih dahulu');
            return;
        }

        if (!suratFile) {
            alert('❌ Upload surat izin terlebih dahulu');
            return;
        }

        const reader = new FileReader();
        reader.onload = async (event) => {
            const data = {
                alasan: alasan,
                surat: event.target.result
            };

            try {
                document.getElementById('submitTidakHadir').disabled = true;
                document.getElementById('submitTidakHadir').textContent = '⏳ Mengajukan...';

                const response = await fetch('{{ route("barista.absensi.submitTidakHadir") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    alert('✅ Pengajuan berhasil!');
                    location.reload();
                } else {
                    alert('❌ Gagal: ' + result.message);
                }
            } catch (err) {
                alert('❌ Error: ' + err.message);
            } finally {
                document.getElementById('submitTidakHadir').disabled = false;
                document.getElementById('submitTidakHadir').textContent = 'Ajukan';
            }
        };
        reader.readAsDataURL(suratFile);
    });

    // Stop cameras when modal closes
    document.getElementById('cameraModalDatang').addEventListener('hidden.bs.modal', () => {
        cameraDatang.stopStream();
    });
    document.getElementById('cameraModalPulang').addEventListener('hidden.bs.modal', () => {
        cameraPulang.stopStream();
    });
</script>
@endpush
