-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Feb 2026 pada 18.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mitrafarma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `meso`
--

CREATE TABLE `meso` (
  `id_meso` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `kode_sumber_data` varchar(50) DEFAULT NULL,
  `nama_singkat` varchar(100) DEFAULT NULL,
  `umur` varchar(20) DEFAULT NULL,
  `suku` varchar(50) DEFAULT NULL,
  `berat_badan` varchar(20) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `status_hamil` enum('hamil','tidak_hamil','tidak_tahu') DEFAULT NULL,
  `penyakit_utama` text DEFAULT NULL,
  `gangguan_ginjal` tinyint(1) DEFAULT 0,
  `gangguan_hati` tinyint(1) DEFAULT 0,
  `alergi` tinyint(1) DEFAULT 0,
  `kondisi_medis_lain` tinyint(1) DEFAULT 0,
  `kondisi_medis_lain_ket` varchar(255) DEFAULT NULL,
  `kesudahan_penyakit` enum('sembuh','sembuh_gejala_sisa','belum_sembuh','meninggal','tidak_tahu') DEFAULT NULL,
  `manifestasi_eso` text DEFAULT NULL,
  `masalah_mutu_produk` text DEFAULT NULL,
  `tanggal_mula_eso` date DEFAULT NULL,
  `kesudahan_eso` enum('sembuh','sembuh_gejala_sisa','belum_sembuh','meninggal','tidak_tahu') DEFAULT NULL,
  `riwayat_eso` text DEFAULT NULL,
  `data_obat` text DEFAULT NULL,
  `keterangan_tambahan` text DEFAULT NULL,
  `data_laboratorium` text DEFAULT NULL,
  `tanggal_pemeriksaan_lab` date DEFAULT NULL,
  `tanggal_laporan` date DEFAULT NULL,
  `nama_pelapor` varchar(100) DEFAULT NULL,
  `tanda_tangan_pelapor` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `meso`
--
ALTER TABLE `meso`
  ADD PRIMARY KEY (`id_meso`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `meso`
--
ALTER TABLE `meso`
  MODIFY `id_meso` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `meso`
--
ALTER TABLE `meso`
  ADD CONSTRAINT `fk_meso_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
