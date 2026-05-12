-- Migration: create table riwayat_pelanggan
CREATE TABLE IF NOT EXISTS `riwayat_pelanggan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` INT(11) NOT NULL,
  `tgl` DATE NOT NULL,
  `diagnosa` TEXT,
  `tindakan` TEXT,
  `followup` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_id_pelanggan` (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: add foreign key if you use InnoDB and want referential integrity
-- ALTER TABLE `riwayat_pelanggan` ADD CONSTRAINT `fk_riwayat_pelanggan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan`(`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;