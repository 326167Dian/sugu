-- Gabungan semua migrasi di database/migrations (s.d. 20260924), versi IDEMPOTENT.
-- Aman dijalankan berkali-kali: setiap kolom/index/tabel dicek dulu sebelum dibuat.
-- Cara pakai: phpMyAdmin > pilih database > tab SQL / Import > jalankan file ini.

-- ------------------------------------------------------------
-- 20260223_add_indexes_sinkronisasi_stok
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk_detail' AND INDEX_NAME = 'idx_trbmasuk_detail_kd_barang');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk_detail ADD INDEX idx_trbmasuk_detail_kd_barang (kd_barang)', 'SELECT ''SKIP: index idx_trbmasuk_detail_kd_barang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail' AND INDEX_NAME = 'idx_trkasir_detail_kd_barang');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kd_barang (kd_barang)', 'SELECT ''SKIP: index idx_trkasir_detail_kd_barang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'barang' AND INDEX_NAME = 'idx_barang_kd_barang');
SET @s := IF(@c = 0, 'ALTER TABLE barang ADD INDEX idx_barang_kd_barang (kd_barang)', 'SELECT ''SKIP: index idx_barang_kd_barang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260225_add_indexes_laporan_laba_penjualan
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir' AND INDEX_NAME = 'idx_trkasir_shift_tgl_carabayar_kd');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir ADD INDEX idx_trkasir_shift_tgl_carabayar_kd (shift, tgl_trkasir, id_carabayar, kd_trkasir)', 'SELECT ''SKIP: index idx_trkasir_shift_tgl_carabayar_kd sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail' AND INDEX_NAME = 'idx_trkasir_detail_kdtrkasir_nmbrg');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kdtrkasir_nmbrg (kd_trkasir, nmbrg_dtrkasir)', 'SELECT ''SKIP: index idx_trkasir_detail_kdtrkasir_nmbrg sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail' AND INDEX_NAME = 'idx_trkasir_detail_id_barang');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_id_barang (id_barang)', 'SELECT ''SKIP: index idx_trkasir_detail_id_barang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260225_add_indexes_byrkredit_serverside
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk' AND INDEX_NAME = 'idx_trbmasuk_idresto_id');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_id (id_resto, id_trbmasuk)', 'SELECT ''SKIP: index idx_trbmasuk_idresto_id sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk' AND INDEX_NAME = 'idx_trbmasuk_idresto_kd');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_kd (id_resto, kd_trbmasuk)', 'SELECT ''SKIP: index idx_trbmasuk_idresto_kd sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk' AND INDEX_NAME = 'idx_trbmasuk_idresto_tgl');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_tgl (id_resto, tgl_trbmasuk)', 'SELECT ''SKIP: index idx_trbmasuk_idresto_tgl sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk' AND INDEX_NAME = 'idx_trbmasuk_idresto_supplier');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_supplier (id_resto, nm_supplier)', 'SELECT ''SKIP: index idx_trbmasuk_idresto_supplier sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk' AND INDEX_NAME = 'idx_trbmasuk_idresto_carabayar');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_carabayar (id_resto, carabayar)', 'SELECT ''SKIP: index idx_trbmasuk_idresto_carabayar sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260225_add_indexes_stok_kritis_analisa
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir' AND INDEX_NAME = 'idx_trkasir_tgl_kd');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir ADD INDEX idx_trkasir_tgl_kd (tgl_trkasir, kd_trkasir)', 'SELECT ''SKIP: index idx_trkasir_tgl_kd sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail' AND INDEX_NAME = 'idx_trkasir_detail_kdtrkasir_kdbarang');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kdtrkasir_kdbarang (kd_trkasir, kd_barang)', 'SELECT ''SKIP: index idx_trkasir_detail_kdtrkasir_kdbarang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260227_add_column_masuk_orders (UPDATE data hanya saat kolom baru dibuat)
-- ------------------------------------------------------------
SET @orders_masuk_baru := (SELECT COUNT(*) = 0 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'masuk');

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'masuk');
SET @s := IF(@c = 0, 'ALTER TABLE orders ADD COLUMN masuk ENUM(''0'',''1'') NOT NULL DEFAULT ''1''', 'SELECT ''SKIP: orders.masuk sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF(@orders_masuk_baru, 'UPDATE orders o SET o.masuk = ''0'' WHERE EXISTS (SELECT 1 FROM trbmasuk t WHERE t.kd_orders = o.kd_trbmasuk)', 'SELECT ''SKIP: data orders.masuk tidak diubah''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_orders_masuk');
SET @s := IF(@c = 0, 'ALTER TABLE orders ADD INDEX idx_orders_masuk (masuk)', 'SELECT ''SKIP: index idx_orders_masuk sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_orders_id_resto');
SET @s := IF(@c = 0, 'ALTER TABLE orders ADD INDEX idx_orders_id_resto (id_resto)', 'SELECT ''SKIP: index idx_orders_id_resto sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260313_add_table_riwayat_pelanggan_obat
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `riwayat_pelanggan_obat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_riwayat` INT(11) NOT NULL,
  `kd_barang` VARCHAR(50) NOT NULL,
  `nm_barang` VARCHAR(100) NOT NULL,
  `aturan_pakai` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rpo_id_riwayat` (`id_riwayat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 20260502_add_columns_riwayat_pelanggan_followup
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'riwayat_pelanggan' AND COLUMN_NAME = 'tgl_followup');
SET @s := IF(@c = 0, 'ALTER TABLE riwayat_pelanggan ADD COLUMN tgl_followup DATETIME NULL DEFAULT NULL AFTER followup', 'SELECT ''SKIP: riwayat_pelanggan.tgl_followup sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'riwayat_pelanggan' AND COLUMN_NAME = 'followup_by');
SET @s := IF(@c = 0, 'ALTER TABLE riwayat_pelanggan ADD COLUMN followup_by VARCHAR(100) NULL DEFAULT NULL AFTER tgl_followup', 'SELECT ''SKIP: riwayat_pelanggan.followup_by sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260516_add_column_admin_ujian
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin' AND COLUMN_NAME = 'ujian');
SET @s := IF(@c = 0, 'ALTER TABLE admin ADD COLUMN ujian VARCHAR(1) NOT NULL DEFAULT ''N'' AFTER jurnalkas', 'SELECT ''SKIP: admin.ujian sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260516_create_table_soal_header + soal + hasil_ujian
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS soal_header (
    id_soal INT(11) NOT NULL AUTO_INCREMENT,
    nm_ujian VARCHAR(100) NOT NULL,
    durasi INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_soal)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS soal (
    id INT(11) NOT NULL AUTO_INCREMENT,
    pertanyaan TEXT NOT NULL,
    opsi_a VARCHAR(255) NOT NULL,
    opsi_b VARCHAR(255) NOT NULL,
    opsi_c VARCHAR(255) NOT NULL,
    jawaban_benar ENUM('a','b','c') NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS hasil_ujian (
    id_hasil BIGINT(20) NOT NULL AUTO_INCREMENT,
    id_admin INT(11) DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL,
    nama_lengkap VARCHAR(150) DEFAULT NULL,
    total_soal INT(11) NOT NULL DEFAULT 0,
    jawaban_benar INT(11) NOT NULL DEFAULT 0,
    jawaban_salah INT(11) NOT NULL DEFAULT 0,
    soal_tidak_valid INT(11) NOT NULL DEFAULT 0,
    nilai_akhir DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    waktu_mulai DATETIME DEFAULT NULL,
    waktu_selesai DATETIME NOT NULL,
    durasi_detik INT(11) DEFAULT NULL,
    durasi_batas_detik INT(11) DEFAULT NULL,
    status_waktu ENUM('on_time','timeout') NOT NULL DEFAULT 'on_time',
    jawaban_json LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_hasil),
    KEY idx_hasil_ujian_id_admin_waktu (id_admin, waktu_selesai)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- ------------------------------------------------------------
-- 20260516_add_fk_soal_to_soal_header
-- ------------------------------------------------------------
ALTER TABLE soal ENGINE=InnoDB;
ALTER TABLE soal_header ENGINE=InnoDB;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'soal' AND COLUMN_NAME = 'id_soal');
SET @s := IF(@c = 0, 'ALTER TABLE soal ADD COLUMN id_soal INT(11) NULL AFTER id', 'SELECT ''SKIP: soal.id_soal sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

UPDATE soal s LEFT JOIN soal_header h ON h.id_soal = s.id_soal SET s.id_soal = NULL WHERE s.id_soal IS NOT NULL AND h.id_soal IS NULL;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'soal' AND INDEX_NAME = 'idx_soal_id_soal');
SET @s := IF(@c = 0, 'ALTER TABLE soal ADD INDEX idx_soal_id_soal (id_soal)', 'SELECT ''SKIP: index idx_soal_id_soal sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_soal_soal_header' AND TABLE_NAME = 'soal');
SET @s := IF(@c = 0, 'ALTER TABLE soal ADD CONSTRAINT fk_soal_soal_header FOREIGN KEY (id_soal) REFERENCES soal_header(id_soal) ON UPDATE CASCADE ON DELETE SET NULL', 'SELECT ''SKIP: fk_soal_soal_header sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260516_add_columns_hasil_ujian_for_report
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hasil_ujian' AND COLUMN_NAME = 'ujian_id');
SET @s := IF(@c = 0, 'ALTER TABLE hasil_ujian ADD COLUMN ujian_id INT(11) DEFAULT NULL AFTER nama_lengkap', 'SELECT ''SKIP: hasil_ujian.ujian_id sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hasil_ujian' AND COLUMN_NAME = 'nama_ujian');
SET @s := IF(@c = 0, 'ALTER TABLE hasil_ujian ADD COLUMN nama_ujian VARCHAR(100) DEFAULT NULL AFTER ujian_id', 'SELECT ''SKIP: hasil_ujian.nama_ujian sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hasil_ujian' AND COLUMN_NAME = 'tidak_dijawab');
SET @s := IF(@c = 0, 'ALTER TABLE hasil_ujian ADD COLUMN tidak_dijawab INT(11) NOT NULL DEFAULT 0 AFTER jawaban_salah', 'SELECT ''SKIP: hasil_ujian.tidak_dijawab sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hasil_ujian' AND INDEX_NAME = 'idx_hasil_ujian_ujian');
SET @s := IF(@c = 0, 'ALTER TABLE hasil_ujian ADD INDEX idx_hasil_ujian_ujian (ujian_id, waktu_selesai)', 'SELECT ''SKIP: index idx_hasil_ujian_ujian sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260716_add_tipetx_perubahan_trkasir
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir' AND COLUMN_NAME = 'tipetx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER jenistx', 'SELECT ''SKIP: trkasir.tipetx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail' AND COLUMN_NAME = 'tipetx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER idadmin', 'SELECT ''SKIP: trkasir_detail.tipetx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail_hist' AND COLUMN_NAME = 'tipetx_asal');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail_hist ADD COLUMN tipetx_asal INT(11) NOT NULL DEFAULT 1 AFTER idadmin', 'SELECT ''SKIP: trkasir_detail_hist.tipetx_asal sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail_hist' AND COLUMN_NAME = 'tipetx_hapus');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail_hist ADD COLUMN tipetx_hapus INT(11) NOT NULL DEFAULT 1 AFTER tipetx_asal', 'SELECT ''SKIP: trkasir_detail_hist.tipetx_hapus sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail_hist' AND COLUMN_NAME = 'waktu_hapus');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail_hist ADD COLUMN waktu_hapus TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER tipetx_hapus', 'SELECT ''SKIP: trkasir_detail_hist.waktu_hapus sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_detail_hist' AND COLUMN_NAME = 'id_admin_hapus');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_detail_hist ADD COLUMN id_admin_hapus INT(11) NULL AFTER waktu_hapus', 'SELECT ''SKIP: trkasir_detail_hist.id_admin_hapus sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'id_dtrkasir');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN id_dtrkasir INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.id_dtrkasir sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'disc');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN disc INT(2) NULL', 'SELECT ''SKIP: trkasir_restore.disc sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'resep');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN resep VARCHAR(10) NULL', 'SELECT ''SKIP: trkasir_restore.resep sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'modal');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN modal DOUBLE NULL', 'SELECT ''SKIP: trkasir_restore.modal sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'profit');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN profit DOUBLE NULL', 'SELECT ''SKIP: trkasir_restore.profit sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'no_batch');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN no_batch VARCHAR(20) NULL', 'SELECT ''SKIP: trkasir_restore.no_batch sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'exp_date');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN exp_date DATE NULL', 'SELECT ''SKIP: trkasir_restore.exp_date sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'waktu');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN waktu TIMESTAMP NULL', 'SELECT ''SKIP: trkasir_restore.waktu sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'tipe');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN tipe INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.tipe sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'komisi');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN komisi INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.komisi sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'idadmin');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN idadmin INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.idadmin sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'kd_bundle');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN kd_bundle VARCHAR(50) NULL', 'SELECT ''SKIP: trkasir_restore.kd_bundle sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'nm_bundle');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN nm_bundle VARCHAR(100) NULL', 'SELECT ''SKIP: trkasir_restore.nm_bundle sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'tipetx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1', 'SELECT ''SKIP: trkasir_restore.tipetx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'waktu_hapus');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN waktu_hapus TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT ''SKIP: trkasir_restore.waktu_hapus sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'id_admin_hapus');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN id_admin_hapus INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.id_admin_hapus sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

CREATE TABLE IF NOT EXISTS trkasir_detail_ubah_qty (
    id_log BIGINT(20) NOT NULL AUTO_INCREMENT,
    kd_trkasir VARCHAR(100) NOT NULL,
    id_dtrkasir INT(11) NOT NULL,
    kd_barang VARCHAR(50) NOT NULL,
    nmbrg_dtrkasir VARCHAR(100) NOT NULL,
    qty_sebelum DOUBLE NOT NULL,
    qty_sesudah DOUBLE NOT NULL,
    hrgttl_sebelum DOUBLE NOT NULL,
    hrgttl_sesudah DOUBLE NOT NULL,
    tipetx INT(11) NOT NULL,
    id_admin INT(11) NULL,
    waktu TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_log),
    KEY idx_trkasir_detail_ubah_qty_kd_trkasir (kd_trkasir)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- ------------------------------------------------------------
-- 20260807_create_table_ujian_progress
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ujian_progress (
    id_progress BIGINT(20) NOT NULL AUTO_INCREMENT,
    id_admin INT(11) NOT NULL,
    username VARCHAR(100) DEFAULT NULL,
    nama_lengkap VARCHAR(150) DEFAULT NULL,
    ujian_id INT(11) NOT NULL,
    nama_ujian VARCHAR(150) DEFAULT NULL,
    jawaban_json LONGTEXT DEFAULT NULL,
    waktu_mulai DATETIME DEFAULT NULL,
    waktu_update DATETIME DEFAULT NULL,
    PRIMARY KEY (id_progress),
    UNIQUE KEY uniq_ujian_progress_admin_ujian (id_admin, ujian_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- ------------------------------------------------------------
-- 20260807_fix_hasil_ujian_auto_increment
-- ------------------------------------------------------------
UPDATE hasil_ujian AS zero_row JOIN (SELECT COALESCE(MAX(id_hasil), 0) + 1 AS next_id FROM hasil_ujian WHERE id_hasil <> 0) AS calc ON 1 = 1 SET zero_row.id_hasil = calc.next_id WHERE zero_row.id_hasil = 0;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hasil_ujian' AND COLUMN_NAME = 'id_hasil' AND EXTRA LIKE '%auto_increment%');
SET @s := IF(@c = 0, 'ALTER TABLE hasil_ujian MODIFY id_hasil BIGINT(20) NOT NULL AUTO_INCREMENT', 'SELECT ''SKIP: id_hasil sudah AUTO_INCREMENT''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260810_extend_trkasir_restore_header
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'id_user');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN id_user INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.id_user sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'id_pelanggan');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN id_pelanggan INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.id_pelanggan sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'kodetx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN kodetx VARCHAR(20) NULL', 'SELECT ''SKIP: trkasir_restore.kodetx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'jenistx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN jenistx INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.jenistx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'waktu_trx');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN waktu_trx DATETIME NULL', 'SELECT ''SKIP: trkasir_restore.waktu_trx sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'poin_awal');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN poin_awal INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.poin_awal sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'tambahan_poin');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN tambahan_poin INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.tambahan_poin sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trkasir_restore' AND COLUMN_NAME = 'redeem_poin');
SET @s := IF(@c = 0, 'ALTER TABLE trkasir_restore ADD COLUMN redeem_poin INT(11) NULL', 'SELECT ''SKIP: trkasir_restore.redeem_poin sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260828_add_tipe_barang_trbmasuk_detail
-- ------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trbmasuk_detail' AND COLUMN_NAME = 'tipe_barang');
SET @s := IF(@c = 0, 'ALTER TABLE trbmasuk_detail ADD COLUMN tipe_barang ENUM(''reguler'',''bonus'') NOT NULL DEFAULT ''reguler'' AFTER tipe', 'SELECT ''SKIP: trbmasuk_detail.tipe_barang sudah ada''');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- 20260924_create_table_apoteker_profesi
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS apoteker_profesi (
    id_admin INT(11) NOT NULL,
    nama_gelar VARCHAR(150) NOT NULL DEFAULT '',
    jabatan VARCHAR(50) NOT NULL DEFAULT 'Apoteker',
    no_stra VARCHAR(100) NOT NULL DEFAULT '',
    no_sipa VARCHAR(100) NOT NULL DEFAULT '',
    sipa_berlaku DATE NULL DEFAULT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
