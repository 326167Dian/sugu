-- Menambahkan kolom penanda bulan untuk fitur reset saldo kas otomatis setiap awal bulan
-- (lihat configurasi/fungsi_reset_saldo_bulanan.php).
-- Backfill dengan bulan berjalan supaya saldo yang sudah ada TIDAK langsung ke-reset
-- saat migrasi ini dijalankan; saldo baru direset saat masuk ke bulan berikutnya.

ALTER TABLE kas ADD COLUMN IF NOT EXISTS bulan_saldo VARCHAR(7) NULL AFTER saldo;

UPDATE kas SET bulan_saldo = DATE_FORMAT(CURDATE(), '%Y-%m') WHERE bulan_saldo IS NULL;
