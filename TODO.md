# TODO Perbaikan Hapus Transaksi Kasir

- [ ] Review blok `act=hapus` di `masuk/modul/mod_trkasir/aksi_trkasir.php`
- [ ] Tambahkan transaksi database (beginTransaction/commit/rollback)
- [ ] Pastikan hapus header `trkasir` konsisten berbasis `kd_trkasir` (dengan fallback `id_trkasir`)
- [ ] Tambahkan validasi kegagalan hapus agar tidak terjadi partial delete
- [ ] Uji sintaks PHP untuk file yang diubah
- [ ] Update TODO setelah selesai
