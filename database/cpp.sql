CREATE TABLE IF NOT EXISTS `cpp` (
  `id_cpp` int(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int(11) DEFAULT NULL,
  `no_cpp` varchar(20) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `jk` varchar(20) DEFAULT NULL,
  `umur` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telp` varchar(50) DEFAULT NULL,
  `tgl_ttd` varchar(100) DEFAULT NULL,
  `thn_ttd` varchar(10) DEFAULT NULL,
  `nama_apoteker` varchar(255) DEFAULT NULL,
  `sipa_apoteker` varchar(100) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cpp`),
  KEY `id_pelanggan` (`id_pelanggan`),
  CONSTRAINT `fk_cpp_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cpp_detail` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_cpp` int(11) NOT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `tanggal` varchar(100) DEFAULT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL,
  `nama_obat_dosis` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_cpp` (`id_cpp`),
  CONSTRAINT `fk_cpp_detail` FOREIGN KEY (`id_cpp`) REFERENCES `cpp` (`id_cpp`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
