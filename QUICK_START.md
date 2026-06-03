# 🚀 QUICK START - Sistem Absensi Baru

## ⚡ 3 Langkah Mudah untuk Deploy

### Step 1: Jalankan Migration ✨

**Option A - Recommended (Windows)**
```
Double-click file: migrate.bat
```

**Option B - Command Line**
```bash
cd D:\laragon\www\KayuhanUAS
php migrate_manual.php
```

**Expected Output:**
```
✅ Old tables dropped
✅ Fresh absensi table created
✅ Migrations table updated
🎉 Migration completed successfully!
```

---

### Step 2: Test Absensi ✅

1. Login ke aplikasi sebagai barista
2. Go to: `/barista/absensi`
3. Click "Absen Masuk"
4. Ambil foto dengan kamera
5. Pilih lokasi di maps
6. Klik "Simpan Absen"
7. Status should show ✅ Hadir atau Terlambat

---

### Step 3: Selesai! 🎉

Sistem sudah aktif dan siap digunakan.

---

## 📸 Features yang Sudah Siap

| Feature | Status |
|---------|--------|
| Real-time Camera | ✅ Ready |
| Maps Location Picker | ✅ Ready |
| Auto Status Calculation | ✅ Ready |
| Late Compensation (-10.000) | ✅ Ready |
| Check-out Lock | ✅ Ready |
| Sick/Leave Upload | ✅ Ready |
| Photo Storage | ✅ Ready |
| Mobile Responsive | ✅ Ready |

---

## 📊 Files Structure

```
KayuhanUAS/
├── app/Models/
│   └── Absensi.php ✨ NEW
├── app/Http/Controllers/
│   └── AbsensiNewController.php ✨ NEW
├── resources/views/pages/
│   └── absensi_new.blade.php ✨ NEW
├── database/migrations/
│   └── 2026_06_03_083000_create_absensi_fresh_final.php ✨ NEW
├── migrate.bat ✨ NEW
├── migrate_manual.php ✨ NEW
├── SETUP_GUIDE.md ✨ NEW
├── DEPLOYMENT_SUMMARY.md ✨ NEW
└── QUICK_START.md (this file)
```

---

## 🎯 API Routes

```
GET  /barista/absensi                    → Tampilkan halaman
POST /barista/absensi/datang             → Absen masuk
POST /barista/absensi/pulang             → Absen pulang
POST /barista/absensi/tidak-hadir        → Pengajuan sakit/izin
GET  /barista/absensi/foto/{id}/{type}   → Download foto
```

---

## 🧪 Quick Test Checklist

- [ ] Migration berhasil dijalankan
- [ ] Bisa login sebagai barista
- [ ] Halaman `/barista/absensi` accessible
- [ ] Jadwal hari ini ditampilkan
- [ ] Kamera bisa diakses
- [ ] Maps muncul dengan benar
- [ ] Foto bisa diambil
- [ ] Absensi bisa disimpan
- [ ] Status berubah sesuai waktu
- [ ] Tombol "Absen Pulang" lock sampai waktu

---

## 🔧 Troubleshooting Quick Fix

### Problem: "Migration failed"
**Solution:** 
- Make sure database exists
- Check MySQL connection
- Verify .env credentials

### Problem: "Camera not working"
**Solution:**
- Allow camera permission in browser
- Try different browser (Chrome recommended)
- Check HTTPS or localhost

### Problem: "Maps not showing"
**Solution:**
- Check internet connection
- Maps needs active internet
- Try VPN if region-blocked

### Problem: "Absensi tidak tersimpan"
**Solution:**
- Check browser console (F12)
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connection

---

## 📚 More Documentation

For detailed information, see:
- `SETUP_GUIDE.md` - Complete setup guide
- `DEPLOYMENT_SUMMARY.md` - Technical details
- `app/Http/Controllers/AbsensiNewController.php` - Controller code
- `resources/views/pages/absensi_new.blade.php` - View code

---

## 💡 Tips

1. **First Time Users**
   - Baristas akan diminta izin akses kamera saat pertama kali
   - Ini normal, click "Allow"

2. **Photo Quality**
   - Make sure ada cahaya yang cukup saat ambil foto
   - Foto akan di-compress otomatis

3. **Location Accuracy**
   - Maps menggunakan OpenStreetMap (gratis, tidak perlu API key)
   - Click di peta untuk select lokasi

4. **Late Status**
   - Absen > 20 menit setelah JAM_MULAI = TERLAMBAT
   - Kompensasi -10.000 akan dikurangi dari gaji

---

## 🎓 How It Works

```
Barista Opens /barista/absensi
        ↓
Shows: Today's schedule + Status
        ↓
Barista clicks "Absen Masuk"
        ↓
Camera modal opens → Take photo → Maps modal → Pick location → Confirm
        ↓
Server: Calculate status (HADIR or TERLAMBAT based on time)
        ↓
Save to database with photo + location + status + compensation
        ↓
Page reload → Show ✅ Absen Masuk with status
        ↓
Later: Barista clicks "Absen Pulang" (only after JAM_SELESAI)
        ↓
Same process → Save pulang data
        ↓
Show ✅ Absen Pulang complete
```

---

## ✨ Ready to Deploy!

Run `php migrate_manual.php` and you're all set!

Questions? Check the detailed documentation files.

Happy attendance! 🎉

