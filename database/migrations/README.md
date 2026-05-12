# Database Migrations (Manual)

Folder ini berisi migrasi SQL manual yang dijalankan langsung ke database produksi/staging.

## Urutan eksekusi

Jalankan file sesuai urutan nama (timestamp di awal nama file):

1. `20260223_add_indexes_sinkronisasi_stok.sql`
2. `20260225_add_indexes_laporan_laba_penjualan.sql`
3. `20260225_add_indexes_byrkredit_serverside.sql`
4. `20260225_add_indexes_stok_kritis_analisa.sql`

## Cara menjalankan

### Opsi 1: phpMyAdmin
1. Buka database aplikasi.
2. Klik tab **SQL**.
3. Paste isi file migrasi, lalu **Run**.

### Opsi 2: MySQL CLI
```bash
mysql -u USERNAME -p NAMA_DATABASE < database/migrations/20260223_add_indexes_sinkronisasi_stok.sql
```

## Isi migrasi saat ini

`20260223_add_indexes_sinkronisasi_stok.sql` menambahkan index performa untuk proses sinkronisasi stok:

- `idx_trbmasuk_detail_kd_barang` pada tabel `trbmasuk_detail(kd_barang)`
- `idx_trkasir_detail_kd_barang` pada tabel `trkasir_detail(kd_barang)`
- `idx_barang_kd_barang` pada tabel `barang(kd_barang)`

`20260225_add_indexes_laporan_laba_penjualan.sql` menambahkan index performa untuk laporan laba penjualan:

- `idx_trkasir_shift_tgl_carabayar_kd` pada tabel `trkasir(shift, tgl_trkasir, id_carabayar, kd_trkasir)`
- `idx_trkasir_detail_kdtrkasir_nmbrg` pada tabel `trkasir_detail(kd_trkasir, nmbrg_dtrkasir)`
- `idx_trkasir_detail_id_barang` pada tabel `trkasir_detail(id_barang)`

`20260225_add_indexes_byrkredit_serverside.sql` menambahkan index performa untuk tabel byrkredit server-side:

- `idx_trbmasuk_idresto_id` pada tabel `trbmasuk(id_resto, id_trbmasuk)`
- `idx_trbmasuk_idresto_kd` pada tabel `trbmasuk(id_resto, kd_trbmasuk)`
- `idx_trbmasuk_idresto_tgl` pada tabel `trbmasuk(id_resto, tgl_trbmasuk)`
- `idx_trbmasuk_idresto_supplier` pada tabel `trbmasuk(id_resto, nm_supplier)`
- `idx_trbmasuk_idresto_carabayar` pada tabel `trbmasuk(id_resto, carabayar)`

`20260225_add_indexes_stok_kritis_analisa.sql` menambahkan index performa untuk proses analisa stok kritis 30 hari:

- `idx_trkasir_tgl_kd` pada tabel `trkasir(tgl_trkasir, kd_trkasir)`
- `idx_trkasir_detail_kdtrkasir_kdbarang` pada tabel `trkasir_detail(kd_trkasir, kd_barang)`

Migrasi bersifat **idempotent** (aman dijalankan ulang). Jika index sudah ada, script akan skip.

## Verifikasi setelah eksekusi

Jalankan query berikut:

```sql
SHOW INDEX FROM trbmasuk_detail;
SHOW INDEX FROM trkasir_detail;
SHOW INDEX FROM barang;
SHOW INDEX FROM trkasir;
SHOW INDEX FROM trbmasuk;
```

Pastikan nama index di atas sudah muncul.
