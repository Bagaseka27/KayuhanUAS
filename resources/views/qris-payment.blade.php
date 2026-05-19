<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran QRIS - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-center mb-6">💳 Pembayaran QRIS</h1>

            <!-- QR Code Display -->
            <div class="bg-gradient-to-br from-blue-50 to-gray-50 p-6 rounded-lg mb-6 text-center border-2 border-dashed border-blue-200">
                @if($qr_image_url)
                    <img src="{{ $qr_image_url }}" alt="QRIS Code" class="w-full max-w-xs mx-auto">
                    <p class="text-sm text-gray-600 mt-4 font-medium">Scan dengan aplikasi E-Wallet Anda</p>
                @endif
            </div>

            <!-- Amount Info -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg">
                <p class="text-sm opacity-90">Jumlah Pembayaran</p>
                <p class="text-3xl font-bold">Rp {{ number_format($amount, 0, ',', '.') }}</p>
            </div>

            <!-- Reference ID -->
            <div class="mb-6 p-4 bg-gray-100 rounded-lg border border-gray-300">
                <p class="text-xs text-gray-600 uppercase tracking-wide">Reference ID</p>
                <p class="text-sm font-mono text-gray-800 break-all mt-1">{{ $reference_id }}</p>
            </div>

            <!-- Status Check Button -->
            <button onclick="checkPaymentStatus()" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-semibold mb-3">
                🔄 Refresh Status
            </button>

            <!-- Status Info -->
            <div id="status-info" class="p-4 bg-yellow-50 rounded-lg border border-yellow-200 mb-6">
                <p class="text-sm text-yellow-800 font-medium">⏳ Menunggu pembayaran...</p>
            </div>

            <!-- Instructions -->
            <div class="p-4 bg-blue-50 rounded-lg text-sm text-gray-700 border-l-4 border-blue-500">
                <h3 class="font-semibold mb-3 text-gray-900">📱 Cara Pembayaran:</h3>
                <ol class="list-decimal list-inside space-y-2">
                    <li>Buka aplikasi E-Wallet (GoPay, OVO, Dana, LINK, etc)</li>
                    <li>Cari menu "Scan QRIS" atau "Bayar"</li>
                    <li>Arahkan kamera ke QR Code di atas</li>
                    <li>Verifikasi nominal dan konfirmasi pembayaran</li>
                </ol>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>QRIS berlaku 24 jam | Transaksi aman & terenkripsi</p>
            </div>
        </div>
    </div>

    <script>
        const referenceId = '{{ $reference_id }}';
        const qrCodeId = '{{ $qr_code_id }}';
        let checkCount = 0;
        const maxChecks = 288; // 24 jam * 5 menit = 1440 menit / 5 = 288 checks

        function checkPaymentStatus() {
            fetch(`/payment/status/${referenceId}`)
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('status-info');
                    checkCount++;
                    
                    if (data.success) {
                        if (data.status === 'PAID' || data.status === 'SETTLED') {
                            statusDiv.innerHTML = '<p class="text-sm text-green-800 font-semibold">✅ Pembayaran Berhasil!</p><p class="text-xs text-green-700 mt-1">Transaksi Anda telah dikonfirmasi.</p>';
                            statusDiv.className = 'p-4 bg-green-50 rounded-lg border-2 border-green-300 mb-6';
                            
                            // Disable auto-refresh
                            clearInterval(autoCheckInterval);
                            
                            // Redirect setelah 3 detik
                            setTimeout(() => {
                                window.location.href = '/payment/success?ref=' + referenceId;
                            }, 3000);
                        } else if (data.status === 'PENDING') {
                            // Still waiting
                            console.log('Status: PENDING (check ' + checkCount + ')');
                        } else if (data.status === 'EXPIRED') {
                            statusDiv.innerHTML = '<p class="text-sm text-red-800 font-semibold">❌ QRIS Expired</p><p class="text-xs text-red-700 mt-1">Silahkan buat pembayaran baru.</p>';
                            statusDiv.className = 'p-4 bg-red-50 rounded-lg border-2 border-red-300 mb-6';
                            clearInterval(autoCheckInterval);
                        }
                    } else {
                        console.error('Error checking status:', data.error);
                    }

                    // Stop auto-check after 24 hours
                    if (checkCount >= maxChecks) {
                        clearInterval(autoCheckInterval);
                        statusDiv.innerHTML = '<p class="text-sm text-red-800">QRIS telah expired. Silahkan buat pembayaran baru.</p>';
                        statusDiv.className = 'p-4 bg-red-50 rounded-lg border border-red-200 mb-6';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        }

        // Auto-check status setiap 5 detik
        const autoCheckInterval = setInterval(checkPaymentStatus, 5000);

        // Check immediately on load
        checkPaymentStatus();
    </script>
</body>
</html>
