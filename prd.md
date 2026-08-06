# PRD - Sistem Informasi Manajemen Dinas Pertanian (SIM-Distan)

> Status: Draft v1.0

## 1. Ringkasan Proyek
SIM-Distan adalah aplikasi terpadu untuk mendukung operasional Dinas Pertanian dengan modul:
- Bidang Penyuluhan Pertanian (Bank Data)
- Bidang Produksi Tanaman Pangan
- Bidang Perkebunan & Hortikultura
- Bidang Prasarana dan Sarana Pertanian (PSP)

## 2. Tech Stack
- Laravel 13
- PHP 8.4+
- MySQL 8
- Bootstrap 5 + Blade
- DataTables
- SweetAlert2
- Alpine.js (Upload File)
- Spatie Permission
- Laravel Excel
- DomPDF

## 3. Role
### Super Admin
- Kelola seluruh sistem, master data, pengguna, konfigurasi.

### Operator Bidang
- Akses sesuai bidang yang ditugaskan.

### Kepala Dinas
- Dashboard Eksekutif, laporan, grafik, tanpa hak ubah data.

## 4. Master Data
- Bidang
- Kecamatan
- Desa
- BPP
- Penyuluh
- Kelompok Tani
- Gapoktan
- Petani
- Lahan
- Kategori Komoditas
- Komoditas (dinamis)
- Varietas
- Satuan

## 5. Modul
### Penyuluhan
- Bank data kelompok tani
- Penyuluh
- Kegiatan
- Programa
- Dokumen

### Produksi
- Luas Tanam
- Luas Panen
- Produksi
- Produktivitas
- OPT
- Bencana

### Perkebunan & Hortikultura
- Menggunakan struktur komoditas dinamis yang sama.

### PSP
- Bantuan
- Alsintan
- Infrastruktur
- Pupuk
- Benih
- Verifikasi
- Penyaluran

## 6. Hak Akses
Role + Bidang digunakan untuk pembatasan akses.

## 7. Arsitektur
Controller -> Service -> Repository -> Model -> MySQL

## 8. Sprint
### Sprint 1
- Setup Laravel
- Login
- Role Permission
- Layout Admin
- Dashboard dasar

### Sprint 2
- Master Wilayah
- Bidang
- BPP

### Sprint 3
- Penyuluh
- Kelompok Tani
- Gapoktan
- Petani

### Sprint 4
- Komoditas dinamis
- Varietas
- Satuan

### Sprint 5
- Produksi Tanaman Pangan

### Sprint 6
- Perkebunan & Hortikultura

### Sprint 7 (soon)
- PSP

### Sprint 8
- Dashboard Eksekutif

### Sprint 9
- Laporan
- Export PDF/Excel

### Sprint 10
- Audit Log
- Backup
- Optimasi
- Deployment

## 9. Standar
- Form Request Validation
- Repository Pattern
- Service Layer
- Resource Controller
- DataTables Server Side
- SweetAlert2
- Alpine.js upload
- Soft Delete
- Activity Log

## 10. Prompt Antigravity IDE
Bangun aplikasi sesuai PRD ini menggunakan Laravel 13, MySQL, Bootstrap 5, Blade, DataTables, SweetAlert2, Alpine.js. Jangan gunakan Filament. Terapkan Repository Pattern, Service Layer, Form Request Validation, RBAC dengan Spatie Permission, migration, seeder, factory, serta kode yang bersih dan terdokumentasi.
