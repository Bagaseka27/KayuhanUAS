# Panduan Sistem Perhitungan Gaji - Kayuhan Coffee

## 📋 Daftar Isi
1. [Cara Menggunakan (Admin)](#cara-menggunakan-admin)
2. [Cara Menggunakan (Karyawan/Barista)](#cara-menggunakan-karyawanbarista)
3. [Sistem Perhitungan](#sistem-perhitungan)
4. [FAQ](#faq)

---

## Cara Menggunakan (Admin)

### 1. Setup Jabatan
1. Buka menu **Jabatan** dari sidebar
2. Klik **+ Tambah Jabatan**
3. Isi form dengan:
   - **Nama Jabatan**: Senior, Junior, atau Training
   - **Upah per Jam**: Default 5000 (bisa disesuaikan)
   - **Bonus per Cup**: Misal 500 untuk Senior, 300 untuk Junior
4. Klik **Simpan**

### 2. Hitung Gaji Otomatis
1. Buka menu **Data Gaji** dari dashboard atau sidebar
2. Pilih periode (bulan-tahun) yang ingin dihitung
3. Klik tombol **Hitung Otomatis Semua Karyawan**
4. Sistem akan otomatis menghitung gaji berdasarkan:
   - Total jam kerja dari jadwal
   - Total penjualan dari transaksi
   - Keterlambatan dari absensi
   - Izin dan sakit

### 3. Lihat Detail Gaji
1. Di halaman Data Gaji, klik icon **👁️** pada baris karyawan
2. Akan menampilkan breakdown lengkap:
   - Jam kerja dan upah per jam
   - Penjualan dan bonus
   - Keterlambatan dan potongan
   - Total akhir

### 4. Edit/Adjustment Gaji
1. Di halaman Data Gaji, klik icon **✏️** pada baris karyawan
2. Bisa menyesuaikan potongan jika diperlukan
3. Klik **Simpan**

### 5. Manage Pengambilan Gaji (Kapan saja)
1. Buka menu **Pengambilan Gaji** dari dashboard atau sidebar
2. Filter berdasarkan status: Menunggu, Disetujui, Ditolak
3. Untuk pengajuan "Menunggu":
   - Klik ✅ untuk **Terima** atau ❌ untuk **Tolak**
   - Tambahkan catatan jika diperlukan
4. Lihat riwayat semua pengambilan

### 6. Manage Penyimpanan Gaji (Kapan saja)
- Sama seperti Pengambilan Gaji
- Buka menu **Penyimpanan Gaji**

---

## Cara Menggunakan (Karyawan/Barista)

### 1. Ambil Gaji (Kapan saja)
1. Masuk ke dashboard barista
2. Cari section **Ambil Gaji** (aktif sepanjang waktu)
3. Klik **Form Pengambilan**
4. Isi nominal yang ingin diambil:
   - Lihat sisa gaji yang bisa diambil
   - Minimum Rp 1.000
   - Maksimum = sisa gaji tersedia
5. Klik **Kirim Pengambilan Gaji**
6. Tunggu admin memproses (biasanya 1-2 jam kerja)
7. Cek status di **Lihat Riwayat Pengambilan**

### 2. Simpan Gaji (Kapan saja)
1. Di dashboard barista, klik **Form Penyimpanan**
2. Isi nominal yang ingin disimpan (max = sisa gaji)
3. Klik **Kirim Penyimpanan Gaji**
4. Admin akan memproses
5. Cek status di **Lihat Riwayat Penyimpanan**

### 3. Cek Riwayat Pengambilan/Penyimpanan
1. Klik **Lihat Riwayat Pengambilan** atau **Lihat Riwayat Penyimpanan**
2. Akan menampilkan:
   - Tanggal pengambilan/penyimpanan
   - Nominal
   - Status (Menunggu, Disetujui, Ditolak)
   - Catatan dari admin

---

## Sistem Perhitungan

### Formula Perhitungan Gaji:

```
1. GAJI POKOK
   = Total Jam Kerja × Upah Per Jam

   Contoh:
   - Jadwal Senin-Jumat: 8-16 = 8 jam/hari
   - 22 hari kerja dalam sebulan
   - Total jam = 22 × 8 = 176 jam
   - Upah per jam = Rp 5.000
   - Gaji Pokok = 176 × 5.000 = Rp 880.000

2. BONUS PENJUALAN
   IF total cup terjual > 50 THEN
       = (Total Cup - 50) × Bonus Per Cup
   ELSE
       = 0

   Contoh (Senior: Bonus 500/cup):
   - Total cup terjual = 155 cup
   - Bonus cup = 155 - 50 = 105 cup
   - Bonus Penjualan = 105 × 500 = Rp 52.500

3. POTONGAN KETERLAMBATAN
   IF menit terlambat <= 15 THEN
       = Menit Terlambat × Rp 1.000
   ELSE IF menit terlambat > 15 THEN
       = 50% × (Gaji Pokok + Bonus Penjualan)

   Contoh 1 (Terlambat 10 menit):
   - Potongan = 10 × 1.000 = Rp 10.000

   Contoh 2 (Terlambat 25 menit):
   - Potongan = 50% × (880.000 + 52.500)
   - Potongan = 50% × 932.500 = Rp 466.250

4. GAJI AKHIR
   = Gaji Pokok + Bonus Penjualan - Potongan

   Contoh:
   = 880.000 + 52.500 - 10.000
   = Rp 922.500
```

### Keadaan Khusus:

**Izin atau Sakit:**
- Tidak terhitung gaji untuk hari tersebut
- Tercatat sebagai "Hari Tidak Masuk"
- Tidak mempengaruhi gaji akhir

---

## FAQ

### Q: Berapa upah per jam default?
**A:** Rp 5.000 per jam (bisa disesuaikan di setting jabatan)

### Q: Berapa minimal penjualan untuk dapat bonus?
**A:** Lebih dari 50 cup. Jika tepat 50, tidak dapat bonus. Dari 51+ cup baru dapat bonus.

### Q: Bagaimana jika sering terlambat?
**A:** 
- Terlambat ≤15 menit: Potong Rp 1.000 per menit
- Terlambat >15 menit: Potong 50% dari total (gaji pokok + bonus)

### Q: Kapan bisa ambil gaji?
**A:** Dapat diajukan kapan saja sepanjang saldo Anda mencukupi.

### Q: Berapa lama admin memproses pengambilan gaji?
**A:** Biasanya 1-2 jam kerja

### Q: Apakah data gaji bisa diubah setelah di-calculate?
**A:** Ya, admin bisa melakukan adjustment dengan klik tombol edit

### Q: Gimana jika izin atau sakit?
**A:** Tidak dihitung gaji, tapi tetap masuk ke rekapitulasi kehadiran

---

## Kontak Support
Hubungi admin untuk pertanyaan lebih lanjut mengenai perhitungan gaji.
