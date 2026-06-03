# 🚀 SISTEM ABSENSI BARU - Setup Guide

## Status: Ready to Deploy ✅

Semua file sudah disiapkan. Sekarang tinggal execute migration dan test sistemnya.

---

## 📋 What Has Been Done

### ✅ Database Schema
- **File:** `database/migrations/2026_06_03_083000_create_absensi_fresh_final.php`
- Tabel `absensi` baru dengan struktur complete:
  - Kolom foto (LONGTEXT untuk base64)
  - Kolom lokasi & GPS coordinates
  - Status otomatis (HADIR/TERLAMBAT/TIDAK_HADIR)
  - Kompensasi terlambat (-10.000)
  - Support sakit/izin dengan surat
  - Unique constraint: EMAIL + TANGGAL (satu record per orang per hari)

### ✅ Model
- **File:** `app/Models/Absensi.php`
- Methods:
  - `calculateStatus()` - Auto-hitung status berdasarkan jam jadwal
  - `isSudahAbsenDatang()` - Check sudah absen masuk
  - `isSudahAbsenPulang()` - Check sudah absen pulang
  - `isTidakHadir()` - Check status tidak hadir
  - Helper methods untuk label

### ✅ Controller
- **File:** `app/Http/Controllers/AbsensiNewController.php`
- Methods:
  - `index()` - Show halaman absensi dengan status
  - `submitDatang()` - Process absen masuk (foto + lokasi + auto-status)
  - `submitPulang()` - Process absen pulang (foto + lokasi)
  - `submitTidakHadir()` - Process sakit/izin (surat upload)
  - `getFoto()` - Retrieve base64 foto

### ✅ View
- **File:** `resources/views/pages/absensi_new.blade.php`
- UI Features:
  - Real-time camera capture (HTML5 getUserMedia API)
  - Maps picker (Leaflet.js + OpenStreetMap)
  - Modal dialogs untuk setiap action
  - Photo preview sebelum submit
  - Status display dengan icon
  - Button lock untuk absen pulang (hanya bisa setelah jam pulang)
  - Tidak hadir form dengan upload dokumen

### ✅ Routes
- **File:** `routes/web.php` (BARISTA section)
- Routes baru:
  - `GET /barista/absensi` → `AbsensiNewController@index`
  - `POST /barista/absensi/datang` → `AbsensiNewController@submitDatang`
  - `POST /barista/absensi/pulang` → `AbsensiNewController@submitPulang`
  - `POST /barista/absensi/tidak-hadir` → `AbsensiNewController@submitTidakHadir`
  - `GET /barista/absensi/foto/{id}/{type}` → `AbsensiNewController@getFoto`

---

## 🔧 Next Steps: Execute Migration

### Option 1: Using Command Prompt (Recommended)

```batch
cd D:\laragon\www\KayuhanUAS
php migrate_manual.php
```

Output yang diharapkan:
```
🔄 Starting migration process...

1️⃣ Dropping old tables...
✅ Old tables dropped

2️⃣ Creating fresh absensi table...
✅ Fresh absensi table created

3️⃣ Updating migrations table...
✅ Migrations table updated

🎉 Migration completed successfully!
✨ Database is ready for the new attendance system
```

### Option 2: Using Laravel Artisan (After Manual Migration)

```bash
php artisan migrate:refresh --seeder
```

---

## 📸 How the System Works

### 1. Barista Opens Attendance Page
```
GET /barista/absensi
↓
Shows: Jadwal hari ini, status absensi (belum/sudah)
Buttons: "Absen Masuk", "Absen Pulang" (locked if before time), "Tidak Hadir"
```

### 2. Barista Click "Absen Masuk"
```
Modal opens:
1. Camera stream (video tag)
2. Click "Ambil Foto" button
3. Canvas captures snapshot
4. Shows preview + "Ambil Ulang" button
5. Maps picker appears
6. Click on map to select location
7. "Simpan Absen" button sends data

POST /barista/absensi/datang {
  "foto": "data:image/jpeg;base64,...",
  "lokasi": "Coffee Shop ABC",
  "lat": -7.2957,
  "lng": 112.7556,
  "datetime": "2026-06-03 08:15:00"
}

Server: calculateStatus() → 
- Compare dengan JAM_MULAI dari Jadwal
- If absen > 20 min: status = TERLAMBAT, kompensasi = -10000
- Else: status = HADIR, kompensasi = 0

Response: { "success": true, "status": "HADIR", "message": "Absen masuk berhasil" }
↓
Page reloads → Shows ✅ Absen Masuk: 08:15 (HADIR)
```

### 3. Barista Click "Absen Pulang"
```
Button locked until: JAM_SELESAI (e.g., 17:00)

After that time:
1. Same process as "Absen Masuk"
2. Camera → Maps → Preview
3. POST /barista/absensi/pulang

Server: Update existing absensi record dengan pulang data
↓
Page reloads → Shows ✅ Absen Pulang: 17:05
```

### 4. Barista Click "Tidak Hadir"
```
Modal opens: Form dengan
- Dropdown: "Sakit" / "Izin"
- File input: Upload surat izin (JPG/PNG/PDF)

User fills form, click "Ajukan":
POST /barista/absensi/tidak-hadir {
  "alasan": "SAKIT",
  "surat": "data:image/jpeg;base64,..."
}

Server: Create absensi record dengan
- STATUS = "TIDAK_HADIR"
- ALASAN = "SAKIT"
- SURAT_IZIN = base64 file

Response: { "success": true, "message": "Pengajuan sakit berhasil" }
↓
Page reloads → Shows ⚠️ Status: Tidak Hadir (Sakit)
Absensi buttons hidden
```

---

## 🧪 Testing Checklist

After migration, test these:

### ✅ Pre-Requisite
- [ ] Create test karyawan with email: `barista@test.com`
- [ ] Create jadwal for today: `08:00 - 17:00`
- [ ] Login as barista

### ✅ Absen Masuk
- [ ] Click "Absen Masuk" button
- [ ] Camera stream appears
- [ ] Click "Ambil Foto" - snapshot captured
- [ ] Maps loads with OpenStreetMap
- [ ] Click map to select location
- [ ] Click "Simpan Absen"
- [ ] Success message appears
- [ ] Page shows ✅ Absen Masuk with time & status

### ✅ Status Calculation
- [ ] Test absen <= 20 min after jam masuk → Status HADIR (kompensasi 0)
- [ ] Test absen > 20 min after jam masuk → Status TERLAMBAT (kompensasi -10.000)

### ✅ Absen Pulang Lock
- [ ] Before jam pulang: "Absen Pulang" button disabled with 🔒 icon
- [ ] After jam pulang: Button enabled
- [ ] Click button → Same flow as Absen Masuk
- [ ] Success → Page shows ✅ Absen Pulang with time

### ✅ Tidak Hadir
- [ ] Click "Tidak Hadir" button
- [ ] Modal appears: alasan + upload surat
- [ ] Select "Sakit"
- [ ] Upload file (JPG/PNG)
- [ ] Click "Ajukan"
- [ ] Success message
- [ ] Page shows ⚠️ Tidak Hadir (Sakit)
- [ ] Absensi buttons hidden

### ✅ Photo Display
- [ ] In admin monitoring page, photos can be viewed
- [ ] Photos display correctly (not binary data in URL)

---

## 📝 Important Notes

### Photo Storage
- Photos stored as BASE64 in `LONGTEXT` column
- Max size: 4GB per column (practical: ~1-2MB per photo)
- Format: `data:image/jpeg;base64,{base64string}`
- Advantage: Works across servers, easy backup

### Maps Library
- Using: **Leaflet.js** + **OpenStreetMap**
- No API key needed (unlike Google Maps)
- Click to select location
- Shows latitude, longitude, and location name

### Camera API
- Browser compatibility: Chrome 53+, Firefox 55+, Safari 11+, Edge 79+
- Requires HTTPS or localhost
- Will ask permission from user

### Database Structure
- One record per person per day (UNIQUE EMAIL+TANGGAL)
- Can update same record (datang → pulang → tidak hadir)
- Supports multiple absensi instances with same photo storage

---

## 🐛 Troubleshooting

### Camera not working
```
- Check browser permissions (Settings → Privacy → Camera)
- Make sure in HTTPS or localhost
- Try different browser
- Check console errors (F12)
```

### Maps not loading
```
- Check internet connection
- OpenStreetMap might be blocked in some regions
- Use VPN if needed
```

### Photo not saving
```
- Check file size (should be < 5MB)
- Check database column size (LONGTEXT is enough)
- Check browser console for validation errors
```

### Status not calculating correctly
```
- Verify Jadwal time is correct (JAM_MULAI)
- Check if absensi datetime is being sent correctly
- Verify timezone settings in .env
```

---

## 🚀 Deploy To Production

1. **Backup database**
   ```bash
   mysqldump -u root -p kayuhan_db > backup_$(date +%Y%m%d).sql
   ```

2. **Run migration**
   ```bash
   php migrate_manual.php
   ```

3. **Test thoroughly** using checklist above

4. **Notify baristas** about new system
   - Show them how to use camera
   - Explain status calculation
   - Explain lock mechanism

5. **Monitor for 1-2 days** for issues

---

## 📞 Support

If any issues occur:
1. Check console errors (Browser F12)
2. Check Laravel logs: `storage/logs/laravel.log`
3. Check database: `SELECT * FROM absensi ORDER BY created_at DESC LIMIT 5;`

---

**Status: Ready to Deploy** ✅
**Next Action: Run `php migrate_manual.php`**

