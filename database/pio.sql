-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Feb 2026 pada 10.00
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
-- Struktur dari tabel `pio`
--

CREATE TABLE `pio` (
  `id_pio` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `no_pio` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `metode` enum('Lisan','Tertulis','Telepon') DEFAULT NULL,
  `nama_penanya` varchar(255) DEFAULT NULL,
  `no_telp_penanya` varchar(50) DEFAULT NULL,
  `status_penanya` enum('Pasien','Keluarga Pasien','Petugas Kesehatan') DEFAULT NULL,
  `status_penanya_ket` varchar(255) DEFAULT NULL COMMENT 'instansi/jabatan untuk Petugas Kesehatan',
  `umur_pasien` int(11) DEFAULT NULL,
  `tinggi_pasien` int(11) DEFAULT NULL,
  `berat_pasien` int(11) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `kehamilan` tinyint(1) DEFAULT 0,
  `kehamilan_minggu` int(11) DEFAULT NULL,
  `menyusui` tinyint(1) DEFAULT 0,
  `uraian_pertanyaan` text DEFAULT NULL,
  `jenis_pertanyaan_identifikasi_obat` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_stabilitas` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_farmakokinetika` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_interaksi_obat` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_dosis` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_farmakodinamika` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_harga_obat` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_keracunan` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_ketersediaan_obat` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_kontra_indikasi` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_efek_samping` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_cara_pemakaian` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_penggunaan_terapeutik` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_lain_lain` tinyint(1) DEFAULT 0,
  `jenis_pertanyaan_lain_lain_ket` varchar(255) DEFAULT NULL,
  `jawaban` text DEFAULT NULL,
  `referensi` text DEFAULT NULL,
  `penyampaian_jawaban` enum('Segera','Dalam 24 jam','Lebih dari 24 jam') DEFAULT NULL,
  `apoteker_penjawab` varchar(255) DEFAULT NULL,
  `tanggal_jawab` date DEFAULT NULL,
  `waktu_jawab` time DEFAULT NULL,
  `metode_jawab` enum('Lisan','Tertulis','Telepon') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pio`
--
ALTER TABLE `pio`
  ADD PRIMARY KEY (`id_pio`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pio`
--
ALTER TABLE `pio`
  MODIFY `id_pio` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pio`
--
ALTER TABLE `pio`
  ADD CONSTRAINT `fk_pio_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
