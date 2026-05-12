CREATE TABLE IF NOT EXISTS `homecare` (
  `id_homecare` int(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int(11) DEFAULT NULL,
  `no_homecare` varchar(20) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `umur` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telp` varchar(50) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_homecare`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `fk_homecare_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `homecare_detail` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_homecare` int(11) NOT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `tgl_kunjungan` varchar(100) DEFAULT NULL,
  `catatan_apoteker` text DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_homecare` (`id_homecare`),
  CONSTRAINT `fk_homecare_detail` FOREIGN KEY (`id_homecare`) REFERENCES `homecare` (`id_homecare`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
