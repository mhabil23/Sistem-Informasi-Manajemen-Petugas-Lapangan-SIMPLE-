# Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan (SIMPLE)

Aplikasi terpadu berbasis web (Laravel) untuk pendataan sensus / survei lapangan, administrasi dokumen Berita Acara Serah Terima (BAST Responden & BAST Petugas), Laporan Perjalanan Dinas resmi (SPD BPS), serta Monitoring dan Rekapitulasi Progres Lapangan secara real-time.

---

## 🚀 Fitur Utama

### 📱 1. Portal Petugas Lapangan (Mobile-First)
- **Dashboard Ringkas & Cepat**: Notifikasi sisa target responden, *countdown* batas waktu (*deadline*), dan status progres real-time.
- **Pendataan Responden Lapangan**: Formulir entri sampel responden lapangan lengkap dengan validasi instansi, alamat, dan nomor kontak.
- **Berita Acara Serah Terima (BAST)**:
  - Penerbitan otomatis BAST Responden (format PDF resmi).
  - Penerbitan otomatis BAST Penugasan Petugas.
- **Laporan Kegiatan / Perjalanan Dinas**:
  - Format standar SPD (Surat Perjalanan Dinas) BPS.
  - Tanda tangan digital (*signature canvas*).
  - Unggah foto dokumentasi kegiatan lapangan.
  - Unduh & cetak PDF otomatis.
  - Fitur perbaikan revisi dari catatan Administrator.
- **Jadwal & Penugasan**: Kalender agenda dan rincian mandat tugas lapangan.

### 🖥️ 2. Portal Administrator (Desktop Monitoring & Rekap)
- **Monitoring & Evaluasi Real-Time**:
  - Rekap penyelesaian target responden dan BAST.
  - Breakdown capaian progres per Kecamatan.
  - Monitoring kinerja per petugas & deteksi dini petugas lambat / belum berprogres.
  - Tombol otomatis *WhatsApp Follow-Up / Reminder* ke petugas.
  - Monitoring pengumpulan laporan kegiatan perjalanan dinas.
- **Verifikasi Laporan**: Fitur periksa, setujui, atau kembalikan dengan catatan evaluasi (*revision notes*).
- **Manajemen Kegiatan & Penugasan**: Pembuatan kegiatan, penentuan target kuota, serta pembagian petugas lapangan.
- **Pelatihan SPKRT**: Manajemen peserta, pencatatan presensi, dan cetak lembar daftar hadir resmi.
- **Rekapitulasi & Ekspor Dokumen**:
  - Ekspor Rekapitulasi format PDF Lanskap.
  - Ekspor Dataset ke Spreadsheet / Excel (CSV dengan UTF-8 BOM).
- **Audit Log & Keamanan**: Pencatatan riwayat aktivitas pengguna (*Activity Logs*).

---

## 🛠️ Teknologi & Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL / MariaDB (Didukung opsi Google Spreadsheet Database Sync)
- **Styling**: Bootstrap 5 + Custom Modern UI (`app-custom.css`) + Font Awesome 6
- **PDF Engine**: Barryvdh DomPDF
- **Arsitektur**: Role-Based Access Control (`role:admin` & `role:petugas`)

---

## 💻 Panduan Instalasi Lokal

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/mhabil23/Sistem-Informasi-Manajemen-Petugas-Lapangan-SIMPLE-.git
   cd Sistem-Informasi-Manajemen-Petugas-Lapangan-SIMPLE-
   ```

2. **Install Dependensi PHP & JavaScript**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan pengaturan database `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env`.*

4. **Migrasi & Seeder Database**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser: `http://localhost:8000`

---

## 🔐 Akun Default (Hasil Seeder)
- **Administrator**:
  - Username: `admin`
  - Password: `password`
- **Petugas Lapangan**:
  - Username: `petugas1`
  - Password: `password`

---

## 📄 Lisensi
Hak Cipta © 2026 SIMPLE - Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan. Dilindungi undang-undang.
