# Database Migrations (Manual)

Folder ini berisi migrasi SQL manual yang dijalankan langsung ke database produksi/staging.

## Urutan eksekusi

Jalankan file sesuai urutan nama (timestamp di awal nama file):

1. `20260223_add_indexes_sinkronisasi_stok.sql`
2. `20260225_add_indexes_laporan_laba_penjualan.sql`
3. `20260225_add_indexes_byrkredit_serverside.sql`
4. `20260225_add_indexes_stok_kritis_analisa.sql`
5. `20260516_add_column_admin_ujian.sql`
6. `20260516_create_table_soal_ujian.sql`
7. `20260516_create_table_hasil_ujian.sql`
8. `20260516_add_fk_soal_to_soal_header.sql`
9. `20260516_add_columns_hasil_ujian_for_report.sql`
10. `20260807_create_table_ujian_progress.sql`
11. `20260807_fix_hasil_ujian_auto_increment.sql`

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

`20260516_add_column_admin_ujian.sql` menambahkan kolom hak akses modul ujian:

- `ujian` pada tabel `admin` dengan default `N`

`20260516_create_table_soal_ujian.sql` menambahkan tabel bank soal untuk modul ujian:

- tabel `soal` dengan kolom pertanyaan, opsi jawaban (`opsi_a`, `opsi_b`, `opsi_c`), dan `jawaban_benar`

`20260516_create_table_hasil_ujian.sql` menambahkan tabel hasil pengerjaan ujian:

- tabel `hasil_ujian` berisi peserta, waktu mulai/selesai, skor, status waktu, dan jawaban dalam format JSON

`20260516_add_fk_soal_to_soal_header.sql` menambahkan relasi soal ke master ujian:

- kolom `id_soal` pada tabel `soal`
- index `idx_soal_id_soal` pada `soal(id_soal)`
- foreign key `fk_soal_soal_header` dari `soal.id_soal` ke `soal_header.id_soal`

`20260516_add_columns_hasil_ujian_for_report.sql` menambahkan kolom laporan hasil akhir ujian:

- `ujian_id`, `nama_ujian`, `tidak_dijawab` pada tabel `hasil_ujian`

`20260807_create_table_ujian_progress.sql` menambahkan tabel autosave progres pengerjaan ujian:

- tabel `ujian_progress` berisi jawaban sementara (JSON) yang tersimpan otomatis saat user memilih opsi, sebelum ujian di-submit. Dipakai untuk menampilkan status "Belum Submit" pada laporan Hasil Ujian. Baris dihapus otomatis begitu user berhasil submit jawaban akhir.

`20260807_fix_hasil_ujian_auto_increment.sql` memperbaiki bug data pada tabel `hasil_ujian`:

- kolom `id_hasil` (primary key) ternyata kehilangan atribut `AUTO_INCREMENT` di database produksi (migrasi awal sudah benar menyertakannya, tapi struktur live-nya berbeda). Karena `sql_mode` server tidak `STRICT_TRANS_TABLES`, INSERT tanpa `id_hasil` memakai default implisit `0`, sehingga satu baris hasil ujian tersimpan dengan `id_hasil = 0` dan submission berikutnya berisiko gagal total dengan `Duplicate entry '0' for key 'PRIMARY'` (gagal senyap karena ditangkap oleh `try/catch` di `proses.php`). Migrasi ini memindahkan baris `id_hasil = 0` ke id yang aman, lalu mengaktifkan kembali `AUTO_INCREMENT`.

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
