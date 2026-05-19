# QRIS Payment Integration - Xendit

## Setup QRIS Pembayaran di Aplikasi

Implementasi QRIS dengan Xendit yang support pembayaran real dari E-Wallet (GoPay, OVO, Dana, LINK, etc).

### ✅ File yang Sudah Dibuat:

1. **Controller**: `app/Http/Controllers/PaymentQrisController.php`
   - Generate QRIS dinamis
   - Check status pembayaran
   - Handle webhook dari Xendit

2. **Config**: `config/xendit.php`
   - Konfigurasi Xendit API

3. **Routes**: `routes/web.php`
   - `/payment/qris` - Tampilkan halaman pembayaran
   - `/payment/qris/generate` - Generate QR code (API)
   - `/payment/status/{referenceId}` - Check status
   - `/payment/webhook` - Webhook endpoint (pastikan disertakan di firewall)

4. **View**: `resources/views/qris-payment.blade.php`
   - Halaman pembayaran dengan auto-refresh status

### 🔧 Setup di Dashboard Xendit:

1. **Daftar akun Xendit**: https://xendit.co/app/settings/developers
2. **Ambil API Key & Secret Key**
3. **Setup webhook di Xendit dashboard**:
   - URL: `https://your-domain.com/payment/webhook`
   - Event: `qr_code.charge_succeeded`

### 📝 Ubah `.env`:

```env
XENDIT_API_KEY=your_api_key_here
XENDIT_SECRET_KEY=your_secret_key_here
XENDIT_WEBHOOK_TOKEN=your_webhook_token_here
XENDIT_PRODUCTION=false  # true untuk production
```

### 💻 Cara Pakai:

#### 1. **Generate QRIS via API** (untuk POS/Programmatic):
```php
// Di controller/service Anda
$response = Http::post('/payment/qris/generate', [
    'amount' => 50000,
    'description' => 'Pembayaran Pesanan #123'
]);

// Response:
{
    "success": true,
    "qr_code": "00020219...",
    "qr_code_id": "qr_...",
    "qr_image_url": "https://api.qrserver.com/v1/...",
    "amount": 50000,
    "reference_id": "ORDER-ABC123-1234567890"
}
```

#### 2. **Tampilkan Halaman Pembayaran QRIS**:
```html
<a href="/payment/qris?amount=50000" class="btn btn-primary">
    Bayar dengan QRIS
</a>
```

#### 3. **Check Status Pembayaran** (dari client-side):
```javascript
fetch('/payment/status/ORDER-ABC123-1234567890')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'PAID') {
            // Pembayaran berhasil
        }
    });
```

### 🎯 Fitur yang Tersedia:

- ✅ Generate QRIS dinamis (bisa scan langsung dari E-Wallet)
- ✅ Status auto-check setiap 5 detik
- ✅ Webhook integration (notifikasi saat pembayaran masuk)
- ✅ QRIS valid 24 jam
- ✅ Support semua E-Wallet di Indonesia (GoPay, OVO, Dana, LINK, etc)
- ✅ Auto redirect saat pembayaran berhasil

### 📊 Flow Pembayaran:

```
User Klik Bayar
    ↓
Generate QRIS → Disimpan reference ID
    ↓
Tampilkan QR Code
    ↓
User Scan & Bayar dari E-Wallet
    ↓
Xendit Kirim Webhook
    ↓
Update Status → Auto Redirect User
```

### ⚠️ Testing di Development:

Untuk test tanpa pembayaran real, Anda bisa:
1. Gunakan **Xendit Test Dashboard**
2. Manual trigger webhook via Postman/curl:

```bash
curl -X POST http://localhost:8000/payment/webhook \
  -H "X-Callback-Token: your_webhook_token" \
  -H "Content-Type: application/json" \
  -d '{
    "event": "qr_code.charge_succeeded",
    "reference_id": "ORDER-ABC123-1234567890",
    "amount": 50000
  }'
```

### 🔒 Keamanan:

- Selalu validate webhook token
- Jangan expose API Key di frontend
- Verifikasi signature Xendit di webhook
- HTTPS required untuk production

### 📞 Support:

- Docs Xendit: https://xendit.co/docs
- Contact: support@xendit.co

---

**Siap untuk test QRIS? Jalankan server Laravel dan buka:**
```
http://localhost:8000/payment/qris?amount=50000
```
