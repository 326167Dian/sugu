-- Migration: 20260313_add_table_riwayat_pelanggan_obat
-- Membuat tabel detail obat untuk riwayat pelanggan

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
