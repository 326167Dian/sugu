-- Menambahkan kolom untuk menandai item barang masuk PBF sebagai "reguler" atau "bonus"
-- (mis. beli 10 box gratis 1 box). Item bertipe bonus tidak mengubah data master barang
-- (HNA, harga jual, harga satuan, konversi, dst) saat diterima -- hanya menambah stok_barang.
--
-- Catatan: kolom ini juga otomatis dibuat saat runtime lewat
-- configurasi/fungsi_perubahan_trbmasuk.php (pastikan_kolom_tipe_barang_trbmasuk) sebagai
-- jaring pengaman kalau migrasi manual ini belum sempat dijalankan. Migrasi ini TIDAK
-- idempotent (ADD COLUMN) -- jangan dijalankan dua kali.

ALTER TABLE trbmasuk_detail
    ADD COLUMN tipe_barang ENUM('reguler','bonus') NOT NULL DEFAULT 'reguler' AFTER tipe;
