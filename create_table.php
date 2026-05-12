<?php
include "configurasi/koneksi.php";

$query = "CREATE TABLE IF NOT EXISTS `user_login_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `login_time` DATETIME NOT NULL,
  `logout_time` DATETIME NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `session_id` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_login_time` (`login_time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";

$queryZataktif = "CREATE TABLE IF NOT EXISTS `zataktif` (
  `id_zataktif` INT(11) NOT NULL AUTO_INCREMENT,
  `nm_zataktif` VARCHAR(250) NOT NULL,
  `indikasi` VARCHAR(250) NOT NULL,
  `aturanpakai` VARCHAR(250) NOT NULL,
  `saran` VARCHAR(250) NOT NULL,
  `user` VARCHAR(250) NOT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_zataktif`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";

$queryPto = "CREATE TABLE IF NOT EXISTS `pto` (
  `id_pto` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelanggan` INT(11) NOT NULL,
  `nm_pelanggan` VARCHAR(120) DEFAULT NULL,
  `jenis_kelamin` VARCHAR(30) DEFAULT NULL,
  `umur` VARCHAR(30) DEFAULT NULL,
  `alamat_pelanggan` TEXT,
  `tlp_pelanggan` VARCHAR(30) DEFAULT NULL,
  `tanggal_1` DATE DEFAULT NULL,
  `catatan_1` TEXT,
  `obat_1` TEXT,
  `masalah_1` TEXT,
  `tindak_1` TEXT,
  `tanggal_2` DATE DEFAULT NULL,
  `catatan_2` TEXT,
  `obat_2` TEXT,
  `masalah_2` TEXT,
  `tindak_2` TEXT,
  `tempat_ttd` VARCHAR(120) DEFAULT NULL,
  `tanggal_ttd` DATE DEFAULT NULL,
  `created_by` VARCHAR(120) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pto`),
  KEY `idx_pto_pelanggan` (`id_pelanggan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

try {
    $db->exec($query);
    $db->exec($queryZataktif);
    $db->exec($queryPto);
    echo "Tabel user_login_logs, zataktif, dan pto berhasil dibuat.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>