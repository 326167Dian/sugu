-- Menambahkan kolom penghubung ke transaksi sumber (mis. kd_trbmasuk) pada tabel jurnal,
-- supaya entri "Pembayaran Distributor" yang dibuat otomatis bisa disinkronkan/diperbarui
-- kalau transaksi sumbernya berubah (total berubah, atau status LUNAS dibatalkan),
-- alih-alih membuat entri baru setiap kali disimpan ulang.
-- Lihat configurasi/fungsi_jurnal_pembayaran_distributor.php

ALTER TABLE jurnal ADD COLUMN IF NOT EXISTS kd_referensi VARCHAR(100) NULL AFTER idjenis;
ALTER TABLE jurnal ADD INDEX IF NOT EXISTS idx_jurnal_idjenis_kdreferensi (idjenis, kd_referensi);
