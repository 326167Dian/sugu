-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 28, 2025 at 09:54 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u877780297_damang`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL DEFAULT 'administrator',
  `password` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `no_telp` varchar(30) NOT NULL,
  `akses_level` enum('pemilik','petugas') NOT NULL DEFAULT 'petugas',
  `blokir` enum('Y','N') NOT NULL DEFAULT 'N',
  `mpengguna` varchar(1) NOT NULL,
  `mheader` varchar(1) NOT NULL,
  `mjenisbayar` varchar(1) NOT NULL,
  `mpelanggan` varchar(1) NOT NULL,
  `msupplier` varchar(1) NOT NULL,
  `msatuan` varchar(1) NOT NULL,
  `mjenisobat` varchar(1) NOT NULL,
  `mbarang` varchar(1) NOT NULL,
  `tbm` varchar(1) NOT NULL,
  `tbmpbf` varchar(1) NOT NULL,
  `tpk` varchar(1) NOT NULL,
  `lpitem` varchar(1) NOT NULL,
  `lpbrgmasuk` varchar(1) NOT NULL,
  `lpkasir` varchar(1) NOT NULL,
  `lpsupplier` varchar(1) NOT NULL,
  `lppelanggan` varchar(1) NOT NULL,
  `mstok` varchar(1) NOT NULL,
  `stok_kritis` varchar(1) NOT NULL,
  `orders` varchar(1) NOT NULL,
  `penjualansebelum` varchar(1) NOT NULL,
  `labapenjualan` varchar(1) NOT NULL,
  `byrkredit` varchar(1) NOT NULL,
  `stokopname` varchar(1) NOT NULL,
  `soharian` varchar(1) NOT NULL,
  `labajenisobat` varchar(1) NOT NULL,
  `koreksistok` varchar(1) NOT NULL,
  `shiftkerja` varchar(1) NOT NULL,
  `neraca` varchar(1) NOT NULL,
  `komisi` varchar(1) NOT NULL,
  `kartustok` varchar(1) NOT NULL,
  `catatan` varchar(1) NOT NULL,
  `cekdarah` varchar(1) NOT NULL,
  `jurnalkas` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kd_barang` bigint(20) NOT NULL,
  `category_id` int(11) NOT NULL,
  `nm_barang` varchar(100) NOT NULL,
  `stok_barang` double NOT NULL,
  `stok_buffer` double NOT NULL,
  `stok_grosir` int(11) NOT NULL,
  `sat_barang` varchar(15) NOT NULL,
  `sat_grosir` varchar(15) NOT NULL,
  `konversi` int(11) NOT NULL,
  `jenisobat` varchar(50) NOT NULL,
  `hna` double NOT NULL,
  `diskon` double NOT NULL,
  `hrgsat_barang` double NOT NULL,
  `hrgsat_grosir` int(11) NOT NULL,
  `hrgjual_barang` double NOT NULL,
  `hrgjual_barang1` double NOT NULL,
  `hrgjual_barang2` double NOT NULL,
  `komisi` double NOT NULL,
  `indikasi` text NOT NULL,
  `ket_barang` text NOT NULL,
  `dosis` varchar(100) NOT NULL,
  `waktu` datetime NOT NULL,
  `t30` int(11) NOT NULL,
  `t60` int(11) NOT NULL,
  `q30` int(11) NOT NULL,
   `gr` float NOT NULL,
  `petugas` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `image` varchar(100) DEFAULT NULL,
  `promosi` enum('standar','terlaris','diskon') NOT NULL DEFAULT 'standar'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_supplier`
--

CREATE TABLE `barang_supplier` (
  `id_brgsup` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `hrgsat_brgsupplier` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `id_batch` int(11) NOT NULL,
  `tgl_transaksi` datetime NOT NULL,
  `no_batch` varchar(10) NOT NULL,
  `exp_date` date NOT NULL,
  `qty` double NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `kd_transaksi` varchar(30) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `status` enum('masuk','keluar') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carabayar`
--

CREATE TABLE `carabayar` (
  `id_carabayar` int(11) NOT NULL,
  `nm_carabayar` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan`
--

CREATE TABLE `catatan` (
  `id_catatan` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `shift` int(11) NOT NULL,
  `petugas` varchar(30) NOT NULL,
  `deskripsi` varchar(250) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------


--
-- Table structure for table `riwayat_pelanggan`
--
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

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cekdarah`
--

CREATE TABLE `cekdarah` (
  `id_cekdarah` int(11) NOT NULL,
  `gula` varchar(50) NOT NULL,
  `asamurat` varchar(50) NOT NULL,
  `kolesterol` varchar(50) NOT NULL,
  `tensi` varchar(50) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `petugas` varchar(50) NOT NULL,
  `waktu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `peta_lokasi` text DEFAULT NULL,
  `telepon` varchar(14) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenispenjualan`
--

CREATE TABLE `jenispenjualan` (
  `id_penjualan` int(11) NOT NULL,
  `nm_penjualan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_jurnal`
--

CREATE TABLE `jenis_jurnal` (
  `idjenis` int(11) NOT NULL,
  `nm_jurnal` varchar(100) NOT NULL,
  `tipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_obat`
--

CREATE TABLE `jenis_obat` (
  `idjenis` int(11) NOT NULL,
  `jenisobat` varchar(50) NOT NULL,
  `ket` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal`
--

CREATE TABLE `jurnal` (
  `id_jurnal` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `ket` text NOT NULL,
  `petugas` varchar(30) NOT NULL,
  `idjenis` mediumint(100) NOT NULL,
  `debit` int(11) NOT NULL,
  `kredit` int(11) NOT NULL,
  `carabayar` varchar(8) NOT NULL,
  `current` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stok`
--

CREATE TABLE `kartu_stok` (
  `id_kartu` int(11) NOT NULL,
  `kode_transaksi` varchar(100) NOT NULL,
  `tgl_sekarang` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas`
--

CREATE TABLE `kas` (
  `id_kas` int(11) NOT NULL,
  `saldo` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kdbm`
--

CREATE TABLE `kdbm` (
  `id_kdbm` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `id_resto` varchar(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `stt_kdbm` varchar(3) NOT NULL DEFAULT 'ON'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kdtk`
--

CREATE TABLE `kdtk` (
  `id_kdtk` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `stt_kdtk` varchar(3) NOT NULL DEFAULT 'ON'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komisiglobal`
--

CREATE TABLE `komisiglobal` (
  `id_komisiglobal` int(11) NOT NULL,
  `nilai` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `petugas` varchar(50) NOT NULL,
  `status` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komisi_pegawai`
--

CREATE TABLE `komisi_pegawai` (
  `id_komisi` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `id_dtrkasir` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `ttl_komisi` double NOT NULL,
  `tgl_komisi` date NOT NULL,
  `status_komisi` enum('on','closed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `koreksi_stok`
--

CREATE TABLE `koreksi_stok` (
  `id_koreksi` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `tgl` date NOT NULL,
  `nm_kbarang` varchar(100) NOT NULL,
  `stok_barangawal` double NOT NULL,
  `selisih_tx` double NOT NULL,
  `stok_baru` double NOT NULL,
  `ket` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `namashift`
--

CREATE TABLE `namashift` (
  `id_shift` int(11) NOT NULL,
  `shift` int(11) NOT NULL,
  `nama_shift` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_trbmasuk` int(11) NOT NULL,
  `id_resto` varchar(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `tgl_trbmasuk` date NOT NULL,
  `petugas` varchar(100) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `nm_supplier` varchar(50) NOT NULL,
  `tlp_supplier` varchar(50) NOT NULL,
  `alamat_trbmasuk` text NOT NULL,
  `ttl_trbmasuk` double NOT NULL,
  `dp_bayar` double NOT NULL,
  `sisa_bayar` double NOT NULL,
  `ket_trbmasuk` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordersdetail`
--

CREATE TABLE `ordersdetail` (
  `id_dtrbmasuk` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrbmasuk` varchar(100) NOT NULL,
  `qty_dtrbmasuk` double NOT NULL,
  `sat_dtrbmasuk` varchar(30) NOT NULL,
  `hnasat_dtrbmasuk` double NOT NULL,
  `diskon` double NOT NULL,
  `konversi` int(11) NOT NULL,
  `hrgsat_dtrbmasuk` double NOT NULL,
  `hrgjual_dtrbmasuk` double NOT NULL,
  `hrgttl_dtrbmasuk` double NOT NULL,
  `qtygrosir_dtrbmasuk` double NOT NULL,
  `satgrosir_dtrbmasuk` varchar(30) NOT NULL,
  `no_batch` varchar(100) NOT NULL,
  `exp_date` date NOT NULL DEFAULT '2028-01-01',
  `masuk` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordersdetail_hist`
--

CREATE TABLE `ordersdetail_hist` (
  `id_dtrbmasuk` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrbmasuk` varchar(100) NOT NULL,
  `qty_dtrbmasuk` double NOT NULL,
  `sat_dtrbmasuk` varchar(30) NOT NULL,
  `hnasat_dtrbmasuk` double NOT NULL,
  `diskon` double NOT NULL,
  `konversi` int(11) NOT NULL,
  `hrgsat_dtrbmasuk` double NOT NULL,
  `hrgjual_dtrbmasuk` double NOT NULL,
  `hrgttl_dtrbmasuk` double NOT NULL,
  `qtygrosir_dtrbmasuk` double NOT NULL,
  `satgrosir_dtrbmasuk` varchar(30) NOT NULL,
  `no_batch` varchar(100) NOT NULL,
  `exp_date` date NOT NULL DEFAULT '2028-01-01',
  `masuk` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_online`
--

CREATE TABLE `order_online` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `tipe_layanan` enum('Dikirim ke alamat','Ambil di toko') DEFAULT NULL,
  `kode_pesanan` varchar(255) DEFAULT NULL,
  `layanan_pengiriman` varchar(255) DEFAULT NULL,
  `tipe_pembayaran` varchar(10) DEFAULT NULL,
  `total_harga` double DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_tlp` varchar(13) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_online_item`
--

CREATE TABLE `order_online_item` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `produk_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `harga` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nm_pelanggan` varchar(100) NOT NULL,
  `tlp_pelanggan` varchar(30) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `ket_pelanggan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nm_satuan` varchar(50) NOT NULL,
  `deskripsi` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setheader`
--

CREATE TABLE `setheader` (
  `id_setheader` int(11) NOT NULL,
  `satu` varchar(111) NOT NULL DEFAULT 'Nama Apotek',
  `dua` varchar(111) NOT NULL DEFAULT 'Alamat Jalan',
  `tiga` varchar(111) NOT NULL DEFAULT 'Nama Kelurahan',
  `empat` varchar(111) NOT NULL DEFAULT 'Nama Apoteker',
  `lima` varchar(111) NOT NULL DEFAULT 'No izin Apotek ',
  `enam` varchar(111) NOT NULL DEFAULT 'no telp apotek',
  `tujuh` varchar(111) DEFAULT NULL DEFAULT 'SIPA APOTEK',
  `delapan` varchar(100) NOT NULL DEFAULT 'Terima Kasih Semoga Tetap Jadi Langganan',
  `sembilan` varchar(100) NOT NULL DEFAULT 'PERHATIAN !!!',
  `sepuluh` varchar(100) NOT NULL DEFAULT 'Barang yang sudah dibeli tidak dapat ditukar',
  `sebelas` varchar(100) NOT NULL DEFAULT 'atau dikembalikan kecuali ada perjanjian',
  `duabelas` varchar(100) NOT NULL DEFAULT 'belum jelas',
  `tigabelas` varchar(100) NOT NULL DEFAULT 'Kota Apotek',
  `logo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname`
--

CREATE TABLE `stok_opname` (
  `id_stok_opname` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(100) NOT NULL,
  `stok_sistem` double NOT NULL,
  `stok_fisik` double NOT NULL,
  `exp_date` date NOT NULL,
  `jml` int(11) NOT NULL,
  `selisih` double NOT NULL,
  `hrgsat_barang` double NOT NULL,
  `ttl_hrgbrg` double NOT NULL,
  `tgl_current` datetime NOT NULL,
  `tgl_stokopname` date NOT NULL,
  `shift` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nm_supplier` varchar(100) NOT NULL,
  `tlp_supplier` varchar(30) NOT NULL,
  `alamat_supplier` text NOT NULL,
  `ket_supplier` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trbmasuk`
--

CREATE TABLE `trbmasuk` (
  `id_trbmasuk` int(11) NOT NULL,
  `id_resto` varchar(11) NOT NULL,
  `petugas` varchar(30) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `kd_orders` varchar(100) NOT NULL,
  `tgl_trbmasuk` date NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `nm_supplier` varchar(50) NOT NULL,
  `tlp_supplier` varchar(50) NOT NULL,
  `alamat_trbmasuk` text NOT NULL,
  `ttl_trbmasuk` double NOT NULL,
  `dp_bayar` double NOT NULL,
  `sisa_bayar` double NOT NULL,
  `diskon1` double NOT NULL,
  `diskon2` double NOT NULL,
  `ket_trbmasuk` text NOT NULL,
  `jatuhtempo` varchar(20) NOT NULL,
  `carabayar` varchar(20) NOT NULL DEFAULT 'LUNAS',
  `jenis` enum('nonpbf','pbf') NOT NULL,
  `tgl_lunas` date NOT NULL,
  `petugas_lunas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trbmasuk_detail`
--

CREATE TABLE `trbmasuk_detail` (
  `id_dtrbmasuk` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `kd_orders` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrbmasuk` varchar(100) NOT NULL,
  `qty_dtrbmasuk` double NOT NULL,
  `sat_dtrbmasuk` varchar(30) NOT NULL,
  `qty_grosir` int(11) NOT NULL,
  `satgrosir_dtrbmasuk` varchar(30) NOT NULL,
  `hnasat_dtrbmasuk` double NOT NULL,
  `diskon` double NOT NULL,
  `konversi` int(11) NOT NULL,
  `hrgsat_dtrbmasuk` double NOT NULL,
  `hrg_barang` int(11) NOT NULL,
  `hrgjual_dtrbmasuk` double NOT NULL,
  `hrgttl_dtrbmasuk` double NOT NULL,
  `no_batch` varchar(100) NOT NULL,
  `exp_date` date NOT NULL DEFAULT '2028-01-01',
  `waktu` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trbmasuk_detail_hist`
--

CREATE TABLE `trbmasuk_detail_hist` (
  `id_dtrbmasuk` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `kd_orders` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrbmasuk` varchar(100) NOT NULL,
  `qty_dtrbmasuk` double NOT NULL,
  `sat_dtrbmasuk` varchar(30) NOT NULL,
  `qty_grosir` int(11) NOT NULL,
  `satgrosir_dtrbmasuk` varchar(30) NOT NULL,
  `hnasat_dtrbmasuk` double NOT NULL,
  `diskon` double NOT NULL,
  `konversi` int(11) NOT NULL,
  `hrgsat_dtrbmasuk` double NOT NULL,
  `hrgjual_dtrbmasuk` double NOT NULL,
  `hrgttl_dtrbmasuk` double NOT NULL,
  `no_batch` varchar(100) NOT NULL,
  `exp_date` date NOT NULL DEFAULT '2028-01-01',
  `waktu` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trkasir`
--

CREATE TABLE `trkasir` (
  `id_trkasir` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `petugas` varchar(100) NOT NULL,
  `shift` int(11) NOT NULL,
  `tgl_trkasir` date NOT NULL,
  `nm_pelanggan` varchar(100) NOT NULL,
  `tlp_pelanggan` varchar(50) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `kodetx` varchar(20) DEFAULT NULL,
  `ttl_trkasir` double NOT NULL,
  `dp_bayar` double DEFAULT NULL,
  `diskon1` double DEFAULT NULL,
  `diskon2` double DEFAULT NULL,
  `sisa_bayar` double DEFAULT NULL,
  `ket_trkasir` text DEFAULT NULL,
  `id_carabayar` int(11) NOT NULL DEFAULT 1,
  `jenistx` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trkasir_detail`
--

CREATE TABLE `trkasir_detail` (
  `id_dtrkasir` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrkasir` varchar(100) NOT NULL,
  `qty_dtrkasir` double NOT NULL,
  `sat_dtrkasir` varchar(30) NOT NULL,
  `hrgjual_dtrkasir` double NOT NULL,
  `disc` int(2) DEFAULT NULL,
  `hrgttl_dtrkasir` double NOT NULL,
  `no_batch` varchar(20) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipe` int(11) NOT NULL,
  `komisi` int(11) DEFAULT NULL,
  `idadmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trkasir_detail_hist`
--

CREATE TABLE `trkasir_detail_hist` (
  `id_dtrkasir` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrkasir` varchar(100) NOT NULL,
  `qty_dtrkasir` double NOT NULL,
  `sat_dtrkasir` varchar(30) NOT NULL,
  `hrgjual_dtrkasir` double NOT NULL,
  `disc` int(2) NOT NULL,
  `hrgttl_dtrkasir` double NOT NULL,
  `no_batch` varchar(20) NOT NULL,
  `exp_date` date NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipe` int(11) NOT NULL,
  `komisi` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trkasir_restore`
--

CREATE TABLE `trkasir_restore` (
  `id_butrkasir` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `petugas` varchar(100) NOT NULL,
  `shift` int(11) NOT NULL,
  `tgl_trkasir` date NOT NULL,
  `nm_pelanggan` varchar(100) NOT NULL,
  `tlp_pelanggan` varchar(50) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `ttl_trkasir` double NOT NULL,
  `dp_bayar` double NOT NULL,
  `diskon1` double NOT NULL,
  `diskon2` double NOT NULL,
  `sisa_bayar` double NOT NULL,
  `ket_trkasir` text NOT NULL,
  `id_carabayar` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrkasir` varchar(100) NOT NULL,
  `qty_dtrkasir` double NOT NULL,
  `sat_dtrkasir` varchar(30) NOT NULL,
  `hrgjual_dtrkasir` double NOT NULL,
  `hrgttl_dtrkasir` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trx_orders`
--

CREATE TABLE `trx_orders` (
  `id_trx_ordrers` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `kd_orders` varchar(100) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_tlp` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waktukerja`
--

CREATE TABLE `waktukerja` (
  `id_shift` int(11) NOT NULL,
  `petugasbuka` varchar(100) NOT NULL,
  `petugastutup` varchar(100) NOT NULL,
  `shift` varchar(5) NOT NULL,
  `tanggal` varchar(10) NOT NULL,
  `waktubuka` time NOT NULL,
  `waktututup` time NOT NULL,
  `saldoawal` int(11) NOT NULL,
  `saldoakhir` int(11) NOT NULL,
  `status` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `barang_supplier`
--
ALTER TABLE `barang_supplier`
  ADD PRIMARY KEY (`id_brgsup`);

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`id_batch`);

--
-- Indexes for table `carabayar`
--
ALTER TABLE `carabayar`
  ADD PRIMARY KEY (`id_carabayar`);

--
-- Indexes for table `catatan`
--
ALTER TABLE `catatan`
  ADD PRIMARY KEY (`id_catatan`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Indexes for table `cekdarah`
--
ALTER TABLE `cekdarah`
  ADD PRIMARY KEY (`id_cekdarah`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenispenjualan`
--
ALTER TABLE `jenispenjualan`
  ADD PRIMARY KEY (`id_penjualan`);

--
-- Indexes for table `jenis_jurnal`
--
ALTER TABLE `jenis_jurnal`
  ADD PRIMARY KEY (`idjenis`);

--
-- Indexes for table `jenis_obat`
--
ALTER TABLE `jenis_obat`
  ADD PRIMARY KEY (`idjenis`);

--
-- Indexes for table `jurnal`
--
ALTER TABLE `jurnal`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indexes for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD PRIMARY KEY (`id_kartu`);

--
-- Indexes for table `kdbm`
--
ALTER TABLE `kdbm`
  ADD PRIMARY KEY (`id_kdbm`);

--
-- Indexes for table `kdtk`
--
ALTER TABLE `kdtk`
  ADD PRIMARY KEY (`id_kdtk`),
  ADD UNIQUE KEY `kd_trkasir` (`kd_trkasir`);

--
-- Indexes for table `komisiglobal`
--
ALTER TABLE `komisiglobal`
  ADD PRIMARY KEY (`id_komisiglobal`);

--
-- Indexes for table `komisi_pegawai`
--
ALTER TABLE `komisi_pegawai`
  ADD PRIMARY KEY (`id_komisi`);

--
-- Indexes for table `koreksi_stok`
--
ALTER TABLE `koreksi_stok`
  ADD PRIMARY KEY (`id_koreksi`);

--
-- Indexes for table `namashift`
--
ALTER TABLE `namashift`
  ADD PRIMARY KEY (`id_shift`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_trbmasuk`),
  ADD UNIQUE KEY `kd_trbmasuk` (`kd_trbmasuk`);

--
-- Indexes for table `ordersdetail`
--
ALTER TABLE `ordersdetail`
  ADD PRIMARY KEY (`id_dtrbmasuk`);

--
-- Indexes for table `ordersdetail_hist`
--
ALTER TABLE `ordersdetail_hist`
  ADD PRIMARY KEY (`id_dtrbmasuk`);

--
-- Indexes for table `order_online`
--
ALTER TABLE `order_online`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_online_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_online_item`
--
ALTER TABLE `order_online_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_online_item_order_id_foreign` (`order_id`),
  ADD KEY `order_online_item_produk_id_foreign` (`produk_id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `setheader`
--
ALTER TABLE `setheader`
  ADD PRIMARY KEY (`id_setheader`);

--
-- Indexes for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD PRIMARY KEY (`id_stok_opname`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `trbmasuk`
--
ALTER TABLE `trbmasuk`
  ADD PRIMARY KEY (`id_trbmasuk`),
  ADD UNIQUE KEY `kd_trbmasuk` (`kd_trbmasuk`);

--
-- Indexes for table `trbmasuk_detail`
--
ALTER TABLE `trbmasuk_detail`
  ADD PRIMARY KEY (`id_dtrbmasuk`),
  ADD KEY `fk_barangmasuk` (`id_barang`);

--
-- Indexes for table `trbmasuk_detail_hist`
--
ALTER TABLE `trbmasuk_detail_hist`
  ADD PRIMARY KEY (`id_dtrbmasuk`);

--
-- Indexes for table `trkasir`
--
ALTER TABLE `trkasir`
  ADD PRIMARY KEY (`id_trkasir`),
  ADD UNIQUE KEY `kd_trkasir` (`kd_trkasir`);

--
-- Indexes for table `trkasir_detail`
--
ALTER TABLE `trkasir_detail`
  ADD PRIMARY KEY (`id_dtrkasir`);

--
-- Indexes for table `trkasir_detail_hist`
--
ALTER TABLE `trkasir_detail_hist`
  ADD PRIMARY KEY (`id_dtrkasir`);

--
-- Indexes for table `trkasir_restore`
--
ALTER TABLE `trkasir_restore`
  ADD PRIMARY KEY (`id_butrkasir`);

--
-- Indexes for table `trx_orders`
--
ALTER TABLE `trx_orders`
  ADD PRIMARY KEY (`id_trx_ordrers`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `waktukerja`
--
ALTER TABLE `waktukerja`
  ADD PRIMARY KEY (`id_shift`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_supplier`
--
ALTER TABLE `barang_supplier`
  MODIFY `id_brgsup` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `id_batch` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carabayar`
--
ALTER TABLE `carabayar`
  MODIFY `id_carabayar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catatan`
--
ALTER TABLE `catatan`
  MODIFY `id_catatan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cekdarah`
--
ALTER TABLE `cekdarah`
  MODIFY `id_cekdarah` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenispenjualan`
--
ALTER TABLE `jenispenjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_jurnal`
--
ALTER TABLE `jenis_jurnal`
  MODIFY `idjenis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_obat`
--
ALTER TABLE `jenis_obat`
  MODIFY `idjenis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jurnal`
--
ALTER TABLE `jurnal`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kartu_stok`
--
ALTER TABLE `kartu_stok`
  MODIFY `id_kartu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kdbm`
--
ALTER TABLE `kdbm`
  MODIFY `id_kdbm` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kdtk`
--
ALTER TABLE `kdtk`
  MODIFY `id_kdtk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `komisiglobal`
--
ALTER TABLE `komisiglobal`
  MODIFY `id_komisiglobal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `komisi_pegawai`
--
ALTER TABLE `komisi_pegawai`
  MODIFY `id_komisi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `koreksi_stok`
--
ALTER TABLE `koreksi_stok`
  MODIFY `id_koreksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `namashift`
--
ALTER TABLE `namashift`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_trbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordersdetail`
--
ALTER TABLE `ordersdetail`
  MODIFY `id_dtrbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordersdetail_hist`
--
ALTER TABLE `ordersdetail_hist`
  MODIFY `id_dtrbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_online`
--
ALTER TABLE `order_online`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_online_item`
--
ALTER TABLE `order_online_item`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setheader`
--
ALTER TABLE `setheader`
  MODIFY `id_setheader` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_opname`
--
ALTER TABLE `stok_opname`
  MODIFY `id_stok_opname` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trbmasuk`
--
ALTER TABLE `trbmasuk`
  MODIFY `id_trbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trbmasuk_detail`
--
ALTER TABLE `trbmasuk_detail`
  MODIFY `id_dtrbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trbmasuk_detail_hist`
--
ALTER TABLE `trbmasuk_detail_hist`
  MODIFY `id_dtrbmasuk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trkasir`
--
ALTER TABLE `trkasir`
  MODIFY `id_trkasir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trkasir_detail`
--
ALTER TABLE `trkasir_detail`
  MODIFY `id_dtrkasir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trkasir_detail_hist`
--
ALTER TABLE `trkasir_detail_hist`
  MODIFY `id_dtrkasir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trkasir_restore`
--
ALTER TABLE `trkasir_restore`
  MODIFY `id_butrkasir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trx_orders`
--
ALTER TABLE `trx_orders`
  MODIFY `id_trx_ordrers` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waktukerja`
--
ALTER TABLE `waktukerja`
  MODIFY `id_shift` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_online`
--
ALTER TABLE `order_online`
  ADD CONSTRAINT `order_online_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_online_item`
--
ALTER TABLE `order_online_item`
  ADD CONSTRAINT `order_online_item_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `order_online` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_online_item_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
