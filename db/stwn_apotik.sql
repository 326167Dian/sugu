-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2022 at 06:02 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stwn_apotik`
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
  `tpk` varchar(1) NOT NULL,
  `lpitem` varchar(1) NOT NULL,
  `lpbrgmasuk` varchar(1) NOT NULL,
  `lpkasir` varchar(1) NOT NULL,
  `lpsupplier` varchar(1) NOT NULL,
  `lppelanggan` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `no_telp`, `blokir`, `mpengguna`, `mheader`, `mjenisbayar`, `mpelanggan`, `msupplier`, `msatuan`, `mjenisobat`, `mbarang`, `tbm`, `tpk`, `lpitem`, `lpbrgmasuk`, `lpkasir`, `lpsupplier`, `lppelanggan`) VALUES
(1, 'owner', '4d3289a8cf20a87e70345e1fb3ac5070', 'ahmad nurdiansyah', '081282165618', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'),
(2, 'admin2', '3d0b91c1a7f4b70e45f87391abb1c4ec', 'Rudi', '897867676', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'),
(3, 'bobi', '69b731ea8f289cf16a192ce78a37b4f0', 'bobi ardiansyah', '08118855566', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nm_barang` varchar(100) NOT NULL,
  `stok_barang` double NOT NULL,
  `stok_buffer` double NOT NULL,
  `sat_barang` varchar(15) NOT NULL,
  `jenisobat` varchar(50) NOT NULL,
  `hrgsat_barang` double NOT NULL,
  `hrgjual_barang` double NOT NULL,
  `tgl_expired` date NOT NULL,
  `ket_barang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kd_barang`, `nm_barang`, `stok_barang`, `stok_buffer`, `sat_barang`, `jenisobat`, `hrgsat_barang`, `hrgjual_barang`, `tgl_expired`, `ket_barang`) VALUES
(1, '4001', 'Salonpas Hijau 12 x 1 lembar', -16, 3, 'SCH', 'ALK', 6363, 10000, '0000-00-00', ''),
(2, '', 'Leucoplast 2,5 x 4,5', 4, 3, 'PCS', 'ALK', 15000, 20000, '0000-00-00', ''),
(3, '', 'Termometer Onemed', -3, 3, 'UNT', 'ALK', 20000, 30000, '0000-00-00', ''),
(4, '', 'Sutra Merah 12\'s', -28, 1, 'BOX', 'PKRT', 17693, 20000, '0000-00-00', ''),
(5, '', 'Viva Face Tonic Lemon 100ml', 3, 1, 'BTL', 'KMTK', 5017, 6000, '0000-00-00', ''),
(6, '', 'Curcuma DHA Sharpy 60 ml (biru)', 4, 2, 'BTL', 'OTC2', 12500, 17500, '0000-00-00', ''),
(7, '', 'Proris Syrup 60 ml', 5, 2, 'BTL', 'OTC2', 24000, 30000, '0000-00-00', ''),
(8, '', 'Proris Syrup Forte 50 ml', 7, 2, 'BTL', 'OTC2', 29700, 35000, '0000-00-00', ''),
(9, '', 'Hufagrip Batuk Pilek (BP-Hijau)', 0, 2, 'BTL', 'OTC2', 16288, 21000, '0000-00-00', ''),
(10, '', 'Hufagrip Flu dan Batuk (kuning)', 0, 2, 'BTL', 'OTC2', 17419, 21000, '0000-00-00', ''),
(11, '', 'Curcuma Emulsion 200 ml blackcurrant', 2, 2, 'BTL', 'OTC2', 26700, 30000, '0000-00-00', ''),
(12, '', 'Curcuma Emulsion 200 ml Jeruk', -1, 2, 'BTL', 'OTC2', 23392, 30000, '0000-00-00', ''),
(13, '', 'Curcuma Emulsion 200 ml Strawberry', 2, 2, 'BTL', 'OTC2', 23392, 30000, '0000-00-00', ''),
(14, '', 'Scott\'s Emulsion Vita Jeruk 200 ml', 4, 2, 'BTL', 'OTC2', 37500, 45000, '0000-00-00', ''),
(15, '', 'Scott\'s Emulsion Vita Jeruk 400 ml', 2, 2, 'BTL', 'OTC2', 64004, 73000, '0000-00-00', ''),
(16, '', 'CDR EFF 10\'s', 8, 3, 'TUB', 'OTC1', 40000, 45000, '0000-00-00', ''),
(17, '', 'Redoxon triple action 10\'s', 4, 2, 'TUB', 'OTC1', 40000, 42000, '0000-00-00', ''),
(18, '', 'Peditox', 3, 2, 'BTL', 'OTC2', 7623, 10000, '0000-00-00', ''),
(19, '', 'Herocyn 150 g', 3, 2, 'BTL', 'OTC2', 15000, 18000, '0000-00-00', ''),
(20, '', 'Betadine mouthwash and gargle 190 ml', 5, 2, 'BTL', 'OTC2', 29700, 38000, '0000-00-00', ''),
(21, '', 'Bedak Salicyl Kimia Farma', 3, 2, 'PCS', 'OTC1', 6000, 8000, '0000-00-00', ''),
(22, '', 'Minyak Tawon FF', 6, 2, 'BTL', 'OTC3', 57500, 65000, '0000-00-00', ''),
(23, '', 'Minyak Tawon EE', 5, 2, 'BTL', 'OTC3', 40000, 45000, '0000-00-00', ''),
(24, '', 'Alofar 100', 550, 30, 'TAB', 'ETC1', 272, 500, '0000-00-00', ''),
(25, '', 'Mulsanol Sirup 60 ml', -5, 0, 'BTL', 'OTC2', 6600, 15000, '0000-00-00', ''),
(26, '', 'Dexteem Plus', 320, 30, 'TAB', 'ETC1', 236, 400, '0000-00-00', ''),
(27, '', 'Lerzin Kapsul', 96, 30, 'KAPS', 'ETC1', 568, 1000, '0000-00-00', ''),
(28, '', 'Synalten Cream', 8, 3, 'TUB', 'ETC3', 4774, 14000, '0000-00-00', ''),
(29, '', 'Sanmol Tablet', 17, 3, 'STRIP', 'OTC1', 1200, 2000, '0000-00-00', ''),
(30, '', 'Mylanta Cair 50 ml', 9, 3, 'BTL', 'OTC2', 12485, 15000, '0000-00-00', ''),
(31, '', 'Yusimox Tablet', -240, 30, 'TAB', 'ETC1', 589, 1100, '0000-00-00', ''),
(32, '', 'Vitacimin Sweet Orange', 0, 3, 'STRIP', 'OTC1', 2000, 3500, '0000-00-00', ''),
(33, '', 'Minyak Tawon DD', 4, 3, 'BTL', 'OTC3', 25000, 28000, '0000-00-00', ''),
(34, '', 'Termorex Plus 60ml', 12, 3, 'BTL', 'OTC2', 12615, 18000, '0000-00-00', ''),
(35, '', 'Rohto Eye Flush 150ml', 4, 2, 'BTL', 'OTC3', 29106, 38000, '0000-00-00', ''),
(36, '', 'Woods AntiTussive ( merah ) 60 ml', 4, 3, 'BTL', 'OTC2', 17500, 22000, '0000-00-00', ''),
(37, '', 'Ternix Plus', 11, 3, 'BTL', 'OTC2', 6064, 18000, '0000-00-00', ''),
(38, '', 'Fatigon tablet 4\'s', 5, 3, 'STRIP', 'OTC1', 3500, 4000, '0000-00-00', ''),
(39, '', 'Kaditic', -22, 30, 'TAB', 'ETC1', 253, 900, '0000-00-00', ''),
(40, '', 'Alofar 300', 100, 30, 'TAB', 'ETC1', 606, 1500, '0000-00-00', ''),
(41, '', 'Nephrolit kaps 5\'s', 11, 5, 'STRIP', 'OTC1', 2250, 5000, '0000-00-00', ''),
(42, '', 'Spasminal', 140, 50, 'TAB', 'ETC1', 574, 800, '0000-00-00', ''),
(43, '', 'Neuralgin RX', 90, 30, 'TAB', 'ETC1', 784, 1100, '0000-00-00', ''),
(44, '', 'Laxing 10\'s', 6, 3, 'STRIP', 'OTC1', 6500, 8000, '0000-00-00', ''),
(45, '', 'Ziloven', 210, 30, 'TAB', 'ETC1', 427, 1700, '0000-00-00', ''),
(46, '', 'Bodrex Extra', 12, 3, 'STRIP', 'OTC1', 1880, 2500, '0000-00-00', ''),
(47, '', 'Thrombophob Gel', 0, 3, 'TUB', 'OTC3', 56193, 67000, '0000-00-00', ''),
(48, '', 'Canesten 5 gr', 4, 3, 'TUB', 'OTC3', 21623, 25000, '0000-00-00', ''),
(49, '', 'Minyak Kayu Putih Caplang 120 ml', 5, 2, 'BTL', 'OTC2', 37500, 40000, '0000-00-00', ''),
(50, '', 'Minyak Kayu Putih Caplang 60 ml', 3, 2, 'BTL', 'OTC2', 19066, 22000, '0000-00-00', ''),
(51, '', 'Minyak Kayu Putih Caplang 30 ml', 7, 2, 'BTL', 'OTC2', 10000, 13000, '0000-00-00', ''),
(52, '', 'Minyak Kayu Putih Caplang 15 ml', 6, 2, 'BTL', 'OTC2', 5409, 8000, '0000-00-00', ''),
(53, '', 'Bisoprolol Fumarate 5 mg', 40, 30, 'TAB', 'ETC1', 500, 1000, '0000-00-00', ''),
(54, '', 'Amoxicillin 500', 230, 30, 'TAB', 'ETC1', 300, 500, '0000-00-00', ''),
(55, '', 'Captopril 25 mg', 60, 0, 'TAB', 'ETC1', 120, 300, '0000-00-00', ''),
(56, '', 'Tolak Angin Anak', 13, 0, 'SCH', 'OTC2', 1917, 3000, '0000-00-00', ''),
(57, '', 'Polysilane Susp 100 ml', 7, 2, 'BTL', 'OTC2', 22000, 25000, '0000-00-00', ''),
(58, '', 'Melanox', 5, 3, 'TUB', 'ETC3', 29100, 40000, '0000-00-00', ''),
(59, '', 'Venaron 300 mg', 2, 0, 'STRIP', 'OTC1', 28000, 50000, '0000-00-00', ''),
(60, '', 'Lactacyd Baby 60 ml', 3, 2, 'BTL', 'OTC3', 25000, 30000, '0000-00-00', ''),
(61, '', 'Curcuma Tablet', 24, 3, 'STRIP', 'OTC1', 12000, 17000, '0000-00-00', ''),
(62, '', 'Combantrin sirup anak', 2, 3, 'BTL', 'OTC2', 16547, 19000, '0000-00-00', ''),
(63, '', 'Nutralix 5000', 6, 3, 'STRIP', 'OTC1', 13142, 30000, '0000-00-00', ''),
(64, '', 'Panadol Biru', 4, 3, 'STRIP', 'OTC1', 8500, 10000, '0000-00-00', ''),
(65, '', 'Panadol Merah', 0, 3, 'STRIP', 'OTC1', 9500, 11000, '0000-00-00', ''),
(66, '', 'Andalan Pil KB', -7, 3, 'STRIP', 'ETC1', 6000, 8000, '0000-00-00', ''),
(67, '', 'Scopma Plus', 102, 30, 'TAB', 'ETC1', 1100, 3000, '0000-00-00', ''),
(68, '', 'Simvastatin 10', 140, 30, 'TAB', 'ETC1', 200, 600, '0000-00-00', ''),
(69, '', 'Cetirizine', 250, 30, 'TAB', 'ETC1', 143, 500, '0000-00-00', ''),
(70, '', 'Neurobion forte', 5, 3, 'STRIP', 'OTC1', 34551, 40000, '0000-00-00', ''),
(71, '', 'Curbion Kids Emulsi 60 ml', 1, 3, 'BTL', 'OTC2', 4730, 20000, '0000-00-00', ''),
(72, '', 'Curbion Syrup 60 ml', 2, 3, 'BTL', 'OTC2', 4290, 20000, '0000-00-00', ''),
(73, '', 'Omevita syrup 60 ml', 0, 3, 'BTL', 'OTC2', 3850, 10000, '0000-00-00', ''),
(74, '', 'Muliavit Syrup 60 ml', 2, 3, 'BTL', 'OTC2', 3850, 18000, '0000-00-00', ''),
(75, '', 'Atorvastatin 10 mg mutifa 6\'S', 6, 18, 'TAB', 'ETC1', 1008, 3000, '0000-00-00', ''),
(76, '', 'betagen 5 gr cream', 6, 3, 'TUB', 'ETC3', 3300, 10000, '0000-00-00', ''),
(77, '', 'Pacdin Vitcur 60 ml', 4, 3, 'BTL', 'OTC2', 3520, 15000, '0000-00-00', ''),
(78, '', 'Metilgen 4 mg', 140, 30, 'TAB', 'ETC1', 266, 2500, '0000-00-00', ''),
(79, '', 'Omecidal', 110, 30, 'TAB', 'ETC1', 264, 1500, '0000-00-00', ''),
(80, '', 'Vira C Pot 30 kap', 10, 2, 'BTL', 'OTC2', 34430, 45000, '0000-00-00', ''),
(81, '', 'Pure Touch 100 ml hand sanitizer', 2, 3, 'BTL', 'OTC2', 10000, 20000, '0000-00-00', ''),
(82, '', 'Panvit-C', 15, 3, 'STRIP', 'OTC1', 6644, 15000, '0000-00-00', ''),
(83, '', 'Vira C Junior 30 kap', 9, 2, 'BTL', 'OTC2', 26620, 35000, '0000-00-00', ''),
(84, '', 'Lerzin Sirup 60 ml', 7, 0, 'BTL', 'ETC2', 5156, 15000, '0000-00-00', ''),
(85, '', 'Helixime kaplet', 510, 30, 'KAPS', 'ETC1', 1308, 4000, '0000-00-00', ''),
(86, '', 'Fresh care citrus (original)', 10, 3, 'BTL', 'OTC2', 10500, 13000, '0000-00-00', ''),
(87, '', 'Fresh Care Hot', 10, 0, 'BTL', 'OTC2', 10500, 13000, '0000-00-00', ''),
(88, '', 'GPU 60 ml', 5, 3, 'BTL', 'OTC2', 15000, 17000, '0000-00-00', ''),
(89, '', 'GPU 30 ml', 8, 3, 'BTL', 'OTC2', 9000, 12000, '0000-00-00', ''),
(90, '', 'Vicks 44 Anak 54 ml', 3, 2, 'BTL', 'OTC2', 17027, 20000, '0000-00-00', ''),
(91, '', 'Vicks 44 anak 27 ml', 4, 0, 'BTL', 'OTC2', 8000, 10000, '0000-00-00', ''),
(92, '', 'Tolak Linu Sidomuncul 15ml', 3, 1, 'BOX', 'OTC2', 12523, 15000, '0000-00-00', ''),
(93, '', 'Alletrol Compositum Tetes mata', 5, 3, 'BTL', 'ETC2', 13163, 25000, '0000-00-00', ''),
(94, '', 'Fargetix', 240, 30, 'TAB', 'ETC1', 300, 500, '0000-00-00', ''),
(95, '', 'FG Troches', 242, 30, 'TAB', 'ETC1', 1281, 1600, '0000-00-00', ''),
(96, '', 'Beneuron', 6, 3, 'STRIP', 'OTC1', 3500, 10000, '0000-00-00', ''),
(97, '', 'Locoriz', 5, 3, 'TUB', 'OTC3', 4002, 15000, '0000-00-00', ''),
(98, '', 'Milorin', 60, 0, 'TAB', 'ETC1', 1481, 4000, '0000-00-00', ''),
(99, '', 'Ambeven', 15, 3, 'STRIP', 'HRB1', 13971, 17000, '0000-00-00', ''),
(100, '', 'Welmove', 3, 3, 'STRIP', 'OTC1', 14500, 17000, '0000-00-00', ''),
(101, '', 'Caladine Lotion 60 ml', 4, 3, 'BTL', 'OTC3', 13500, 15000, '0000-00-00', ''),
(102, '', 'Bodrex Flu dan Batuk PE (biru)', 22, 3, 'STRIP', 'OTC1', 1680, 2000, '0000-00-00', ''),
(103, '', 'Albothyl 5 ml', 0, 2, 'BTL', 'ETC2', 27500, 32000, '0000-00-00', ''),
(104, '', 'Neo Rheumacyl tab', 18, 3, 'STRIP', 'OTC1', 7500, 9000, '0000-00-00', ''),
(105, '', 'Aspilet strip', 0, 3, 'STRIP', 'OTC1', 2500, 5000, '0000-00-00', ''),
(106, '', 'Gastrucid Tab', 18, 3, 'STRIP', 'OTC1', 3000, 6000, '0000-00-00', ''),
(107, '', 'Intunal Forte', 0, 3, 'STRIP', 'OTC1', 2600, 4000, '0000-00-00', ''),
(108, '', 'Lacto B', 26, 5, 'SCH', 'OTC1', 5750, 7000, '0000-00-00', ''),
(109, '', 'Sakatonik ABC Jeruk', 4, 3, 'BTL', 'OTC1', 14238, 18000, '0000-00-00', ''),
(110, '', 'Sakatonik ABC Strawberry', 1, 3, 'BTL', 'OTC1', 14111, 18000, '0000-00-00', ''),
(111, '', 'Insto 7,5 ml', 5, 3, 'BTL', 'OTC2', 12000, 15000, '0000-00-00', ''),
(112, '', 'Tolak Angin Cair 12 Sachet/Box', 40, 6, 'SCH', 'OTC2', 2740, 3500, '0000-00-00', ''),
(113, '', 'Farizol Syrup 60 ml', 8, 3, 'BTL', 'ETC2', 5651, 15000, '0000-00-00', ''),
(114, '', 'Nisagon Cream', 11, 3, 'TUB', 'ETC3', 7150, 14000, '0000-00-00', ''),
(115, '', 'Amlodipine 10 mg HJ', 40, 50, 'TAB', 'ETC1', 701, 1000, '0000-00-00', ''),
(116, '', 'Amlodipine 5 HJ', 127, 50, 'TAB', 'ETC1', 357, 500, '0000-00-00', ''),
(117, '', 'Trianta Tablet', 10, 3, 'STRIP', 'OTC1', 2046, 5000, '0000-00-00', ''),
(118, '', 'Fondazen', 5, 3, 'STRIP', 'OTC1', 2620, 10000, '0000-00-00', ''),
(119, '', 'Vermint B', 9, 3, 'BTL', 'OTC1', 36000, 45000, '0000-00-00', ''),
(120, '', 'Fitkom Gummy Biru', 2, 3, 'BOX', 'OTC1', 13200, 17000, '0000-00-00', ''),
(121, '', 'Tempra Drops', 7, 3, 'BTL', 'OTC2', 45650, 54000, '0000-00-00', ''),
(122, '', 'Folamil Genio', 3, 2, 'BTL', 'OTC2', 120000, 135000, '0000-00-00', ''),
(123, '', 'Tempra sirup 30 ml', 9, 3, 'BTL', 'OTC2', 20120, 24000, '0000-00-00', ''),
(124, '', 'Tempra sirup 60 ml', 6, 3, 'BTL', 'OTC2', 38563, 45000, '0000-00-00', ''),
(125, '', 'Anelat', 9, 3, 'STRIP', 'OTC1', 1535, 10000, '0000-00-00', ''),
(126, '', 'Seremig 10', 110, 30, 'TAB', 'ETC1', 661, 2500, '0000-00-00', ''),
(127, '', 'Fasidol 500 tab', 10, 3, 'STRIP', 'OTC1', 2075, 5000, '0000-00-00', ''),
(128, '', 'Flunadin tab', 10, 3, 'STRIP', 'OTC1', 3279, 10000, '0000-00-00', ''),
(129, '', 'Diastrix tab', 140, 30, 'TAB', 'ETC1', 124, 2000, '0000-00-00', ''),
(130, '', 'Ifarsyl Tablet', 14, 3, 'STRIP', 'OTC1', 2627, 10000, '0000-00-00', ''),
(131, '', 'Akita tab', 3, 3, 'STRIP', 'OTC1', 3047, 6000, '0000-00-00', ''),
(132, '', 'Farizol Tab', 65, 30, 'TAB', 'ETC1', 307, 800, '0000-00-00', ''),
(133, '', 'Vesperum tab', 150, 30, 'TAB', 'ETC1', 200, 700, '0000-00-00', ''),
(134, '', 'Pronicy', 150, 30, 'TAB', 'ETC1', 267, 500, '0000-00-00', ''),
(135, '', 'Metformin 500', 360, 30, 'TAB', 'ETC1', 150, 200, '0000-00-00', ''),
(136, '', 'Bricox Dry Syrup', 7, 3, 'BTL', 'ETC2', 17036, 45000, '0000-00-00', ''),
(137, '', 'Celestar Syrup', 2, 3, 'BTL', 'ETC2', 5155, 20000, '0000-00-00', ''),
(138, '', 'Diafural Suspensi', 5, 3, 'BTL', 'ETC2', 10500, 30000, '0000-00-00', ''),
(139, '', 'Bricox Tab', 50, 20, 'TAB', 'ETC1', 1966, 5000, '0000-00-00', ''),
(140, '', 'Faridexon Forte', 170, 30, 'TAB', 'ETC1', 115, 300, '0000-00-00', ''),
(141, '', 'Rexavin 500', 80, 30, 'TAB', 'ETC1', 1243, 3500, '0000-00-00', ''),
(142, '', 'Brinostar tab', 80, 10, 'TAB', 'ETC1', 4290, 8000, '0000-00-00', ''),
(143, '', 'Omega 3 Nutra Health 36/24', 3, 3, 'BTL', 'OTC1', 81510, 120000, '0000-00-00', ''),
(144, '', 'Beta Caroten NutraHealth 15 mg', 3, 3, 'BTL', 'OTC1', 81510, 100000, '0000-00-00', ''),
(145, '', 'Squalene Nutra Health', 3, 3, 'BTL', 'OTC1', 115899, 140000, '0000-00-00', ''),
(146, '', 'Vitamin D3 400 IU Nutra health', 4, 3, 'BTL', 'OTC1', 70015, 85000, '0000-00-00', ''),
(147, '', 'Vitamin E 400 IU Nutra Health', 7, 3, 'BTL', 'OTC1', 93375, 120000, '0000-00-00', ''),
(148, '', 'Fasidol Drops', 4, 3, 'BTL', 'OTC2', 9611, 15000, '0000-00-00', ''),
(149, '', 'Lerzin drop', 8, 3, 'BTL', 'ETC2', 11250, 30000, '0000-00-00', ''),
(150, '', 'Genalten Cream', 0, 2, 'TUB', 'ETC3', 2909, 10000, '0000-00-00', ''),
(151, '', 'Acdat Cream', 14, 3, 'TUB', 'ETC3', 9405, 30000, '0000-00-00', ''),
(152, '', 'Quantidex Sirup 60 ml', 5, 3, 'BTL', 'OTC2', 5816, 15000, '0000-00-00', ''),
(153, '', 'Vitamin C Sidomuncul Lemon 6\'s', 0, 3, 'BOX', 'OTC1', 8000, 15000, '0000-00-00', ''),
(154, '', 'Rohto V extra 7 ml', 4, 2, 'BTL', 'OTC2', 16008, 20000, '0000-00-00', ''),
(155, '', 'vermox tab 500', 8, 3, 'STRIP', 'OTC1', 18012, 22000, '0000-00-00', ''),
(156, '', 'Histigo', 100, 0, 'TAB', 'ETC1', 497, 1500, '0000-00-00', ''),
(157, '', 'Faridexon 0,5', 250, 30, 'TAB', 'ETC1', 85, 200, '0000-00-00', ''),
(158, '', 'Ofloxacin 400', 40, 20, 'TAB', 'ETC1', 785, 1500, '0000-00-00', ''),
(159, '', 'Acetylcysteine 200', 0, 30, 'KAPS', 'ETC1', 935, 1500, '0000-00-00', ''),
(160, '', 'Cefixime 200', 2, 20, 'KAPS', 'ETC1', 3025, 5000, '0000-00-00', ''),
(161, '', 'Minyak Angin Cap Kapak 3 ml', 5, 3, 'BTL', 'OTC3', 5950, 6500, '0000-00-00', ''),
(162, '', 'Minyak Angin Cap Kapak 10 ml', 0, 3, 'BTL', 'OTC3', 15700, 16500, '0000-00-00', ''),
(163, '', 'Minyak Angin Polar Bear 8 cc', 1, 3, 'BTL', 'ALK', 17150, 20000, '0000-00-00', ''),
(164, '', 'Minyak Telon Lang 30 ml', 2, 3, 'BTL', 'ALK', 9538, 11000, '0000-00-00', ''),
(165, '', 'Farsiretik 40 mg', 55, 30, 'TAB', 'ETC1', 236, 600, '0000-00-00', ''),
(166, '', 'Super Tetra', -23, 50, 'KAPS', 'ETC1', 1209, 1500, '0000-00-00', ''),
(167, '', 'Roverton Sirup 60 ml', 9, 3, 'BTL', 'ETC2', 4290, 12000, '0000-00-00', ''),
(168, '', 'Bioplacenton 15g', 1, 3, 'TUB', 'ETC3', 20570, 28000, '0000-00-00', ''),
(169, '', 'Mediklin TR', 0, 2, 'TUB', 'ETC3', 45000, 55000, '0000-00-00', ''),
(170, '', 'Flutop C 60 ml', 7, 3, 'BTL', 'OTC2', 6229, 16000, '0000-00-00', ''),
(171, '', 'Provagin Ovula', 2, 3, 'PCS', 'ETC5', 13943, 18000, '0000-00-00', ''),
(172, '', 'Paratenza Suspensi 60 ml', 4, 3, 'BTL', 'OTC2', 6353, 15000, '0000-00-00', ''),
(173, '', 'Devosix Drop', 5, 3, 'BTL', 'OTC2', 11839, 34000, '0000-00-00', ''),
(174, '', 'Mezinex Sirup', 4, 3, 'BTL', 'OTC2', 4785, 11000, '0000-00-00', ''),
(175, '', 'Fasidol Sirup 60 ml', 7, 3, 'BTL', 'OTC2', 4500, 12000, '0000-00-00', ''),
(176, '', 'Fasidol Forte Sirup 60 ml', 4, 3, 'BTL', 'OTC2', 5400, 14000, '0000-00-00', ''),
(177, '', 'Farsifen Sirup 60 ml', 1, 3, 'BTL', 'OTC2', 4950, 13000, '0000-00-00', ''),
(178, '', 'Farsifen Forte Sirup', 6, 3, 'BTL', 'OTC2', 6476, 19000, '0000-00-00', ''),
(179, '', 'Ifarsyl Plus Sirup 60 ml', 0, 3, 'BTL', 'OTC2', 5528, 15000, '0000-00-00', ''),
(180, '', 'Sanmol Sirup 60 ml', 8, 3, 'BTL', 'OTC2', 12709, 15000, '0000-00-00', ''),
(181, '', 'Fasidol Forte Tablet', 17, 3, 'STRIP', 'OTC1', 2562, 7000, '0000-00-00', ''),
(182, '', 'Brochifar Plus', 5, 3, 'STRIP', 'OTC1', 4275, 11000, '0000-00-00', ''),
(183, '', 'Brochifar', 14, 3, 'STRIP', 'OTC1', 4884, 10000, '0000-00-00', ''),
(184, '', 'Frozavit kaplet', 9, 3, 'STRIP', 'OTC1', 7941, 25000, '0000-00-00', ''),
(185, '', 'Vit Ever', 6, 3, 'STRIP', 'OTC1', 7087, 20000, '0000-00-00', ''),
(186, '', 'Luvisma', 4, 3, 'STRIP', 'OTC1', 3131, 8000, '0000-00-00', ''),
(187, '', 'Cezz Vit 500 @60\'s', 1, 3, 'BTL', 'OTC1', 31000, 50000, '0000-00-00', ''),
(188, '', 'Cezz Vit 500 kapsul', 8, 3, 'STRIP', 'OTC1', 5486, 10000, '0000-00-00', ''),
(189, '', 'Glikos 500', 450, 30, 'TAB', 'ETC1', 204, 600, '0000-00-00', ''),
(190, '', 'Glibenclamide', -30, 30, 'TAB', 'ETC1', 150, 200, '0000-00-00', ''),
(191, '', 'Antimo anak', 0, 2, 'BOX', 'OTC2', 11550, 15000, '0000-00-00', ''),
(192, '', 'Polident Fresh mint 20 gr', 1, 2, 'TUB', 'ALK', 27656, 37000, '0000-00-00', ''),
(193, '', 'Boraginol N Suppositoria', 4, 2, 'PCS', 'ETC5', 12074, 18000, '0000-00-00', ''),
(194, '', 'Counterpain 30 gr', 0, 2, 'TUB', 'OTC3', 37290, 42000, '0000-00-00', ''),
(195, '', 'Counterpain 60 gr', 3, 3, 'TUB', 'OTC3', 65560, 78000, '0000-00-00', ''),
(196, '', 'Inpepsa Syrup 100 ml', 4, 2, 'BTL', 'ETC2', 63910, 85000, '0000-00-00', ''),
(197, '', 'Inpepsa 200 ml', 3, 2, 'BTL', 'ETC2', 84084, 105000, '0000-00-00', ''),
(198, '', 'Mionalgin', 80, 30, 'TAB', 'ETC1', 525, 2000, '0000-00-00', ''),
(199, '', 'Candotens 16 mg', 40, 30, 'TAB', 'ETC1', 8085, 11000, '0000-00-00', ''),
(200, '', 'Zevask 10', 50, 30, 'TAB', 'ETC1', 3526, 1700, '0000-00-00', ''),
(201, '', 'Enzyplex', 10, 3, 'STRIP', 'OTC1', 7500, 10000, '0000-00-00', ''),
(202, '', 'Laxana tab', 7, 3, 'STRIP', 'OTC1', 2743, 8000, '0000-00-00', ''),
(203, '', 'Bufacomb In Ora Base', 2, 3, 'TUB', 'ETC3', 18000, 25000, '0000-00-00', ''),
(204, '', 'Clopidogrel 75mg tab', 30, 10, 'TAB', 'ETC1', 1976, 2700, '0000-00-00', ''),
(205, '', 'Nexitra Tab', 90, 20, 'TAB', 'ETC1', 1534, 4000, '0000-00-00', ''),
(206, '', 'VIT ZO HJ', 18, 20, 'TAB', 'ETC1', 3032, 15000, '0000-00-00', ''),
(207, '', 'Glaucon 250', 0, 10, 'TAB', 'ETC1', 5200, 6000, '0000-00-00', ''),
(208, '', 'Clindamycin 150', 50, 30, 'TAB', 'ETC1', 716, 1000, '0000-00-00', ''),
(209, '', 'Diclofenac Sodium 50', 0, 20, 'TAB', 'ETC1', 307, 500, '0000-00-00', ''),
(210, '', 'ISDN 10', 0, 30, 'TAB', 'ETC1', 261, 400, '0000-00-00', ''),
(211, '', 'Mupirocin Ca Cream 5 g', 6, 2, 'TUB', 'ETC3', 17325, 25000, '0000-00-00', ''),
(212, '', 'Pantoprazole 40', 23, 4, 'TAB', 'ETC1', 7920, 12000, '0000-00-00', ''),
(213, '', 'Glucosamin 500 Medikon', 5, 3, 'STRIP', 'OTC1', 11982, 17000, '0000-00-00', ''),
(214, '', 'Candesartan 8 Dexa', 0, 20, 'TAB', 'ETC1', 1350, 4000, '0000-00-00', ''),
(215, '', 'Candesartan 16 dexa', 30, 30, 'TAB', 'ETC1', 1950, 6000, '0000-00-00', ''),
(216, '', 'Solinfec Cream', 8, 3, 'TUB', 'OTC3', 6394, 18000, '0000-00-00', ''),
(217, '', 'Solinfec Tablet', 70, 20, 'TAB', 'ETC1', 804, 2500, '0000-00-00', ''),
(218, '', 'OBH Combi Batuk Plus Flu 100ml', 13, 3, 'BTL', 'OTC2', 15000, 19000, '0000-00-00', ''),
(219, '', 'Kanina Sirup', 5, 3, 'BTL', 'OTC2', 4208, 10000, '0000-00-00', ''),
(220, '', 'Ursodeoxycholic Acid', 0, 20, 'KAPS', 'ETC1', 3500, 6000, '0000-00-00', ''),
(221, '', 'Selvim 10', 140, 30, 'TAB', 'ETC1', 347, 1100, '0000-00-00', ''),
(222, '', 'Omega 3 Nutra Health 18/12', 3, 3, 'BTL', 'OTC1', 70015, 95000, '0000-00-00', ''),
(223, '', 'Kompolax Sirup', 4, 3, 'BTL', 'OTC2', 7920, 20000, '0000-00-00', ''),
(224, '', 'Scabimite 10 g', 3, 3, 'TUB', 'ETC3', 48518, 63000, '0000-00-00', ''),
(225, '', 'Flasicox', 70, 30, 'TAB', 'ETC1', 955, 2500, '0000-00-00', ''),
(226, '', 'Asimil - Teh Pelancar Asi', 1, 3, 'KLG', 'OTC1', 25000, 99000, '0000-00-00', ''),
(227, '', 'Estalex', 40, 30, 'TAB', 'ETC1', 780, 2400, '0000-00-00', ''),
(228, '', 'Bronchosal 4 mg', 70, 30, 'TAB', 'ETC1', 163, 500, '0000-00-00', ''),
(229, '', 'Cefadroxil 500', 10, 30, 'TAB', 'ETC1', 560, 1000, '0000-00-00', ''),
(230, '', 'Ciprofloxacin 500', 80, 30, 'TAB', 'OTC1', 380, 900, '0000-00-00', ''),
(231, '', 'Dionicol Tab', 120, 30, 'TAB', 'ETC1', 972, 2600, '0000-00-00', ''),
(232, '', 'Dobrizol', 50, 30, 'TAB', 'ETC1', 799, 2500, '0000-00-00', ''),
(233, '', 'Fasiprim', 120, 30, 'TAB', 'ETC1', 254, 700, '0000-00-00', ''),
(234, '', 'Fasiprim Forte', 100, 30, 'KPL', 'ETC1', 485, 1300, '0000-00-00', ''),
(235, '', 'Floxifar 500', 80, 30, 'KPL', 'ETC1', 525, 1500, '0000-00-00', ''),
(236, '', 'Faxiden 10', 80, 30, 'TAB', 'ETC1', 145, 400, '0000-00-00', ''),
(237, '', 'Zevask 5', 170, 30, 'TAB', 'ETC1', 342, 1000, '0000-00-00', ''),
(238, '', 'Vitacid 0,05%', 4, 2, 'TUB', 'ETC3', 36557, 47000, '0000-00-00', ''),
(239, '', 'Latibet 5', 80, 30, 'TAB', 'ETC1', 189, 500, '0000-00-00', ''),
(240, '', 'Lostacef 500', 340, 30, 'KAPS', 'ETC1', 817, 3000, '0000-00-00', ''),
(241, '', 'Bronchifar Plus', 0, 3, 'STRIP', 'OTC1', 0, 0, '0000-00-00', ''),
(242, '', 'Glimepiride 1mg', 170, 30, 'TAB', 'ETC1', 484, 700, '0000-00-00', ''),
(243, '', 'Glimepiride 2mg', 180, 30, 'TAB', 'ETC1', 880, 1200, '0000-00-00', ''),
(244, '', 'Sangobion Kapsul', 15, 3, 'STRIP', 'OTC1', 13906, 17000, '0000-00-00', ''),
(245, '', 'Sangobion VitaTonik', 2, 2, 'BTL', 'OTC2', 26600, 30000, '0000-00-00', ''),
(246, '', 'Omedrinat 50', 8, 3, 'STRIP', 'OTC1', 1870, 10000, '0000-00-00', ''),
(247, '', 'Amlodipine 5 mg Mutifa', 70, 100, 'TAB', 'ETC1', 93, 400, '0000-00-00', ''),
(248, '', 'Sucralfate Mutifa 100ml', 5, 3, 'BTL', 'ETC2', 12100, 25000, '0000-00-00', ''),
(249, '', 'Dexigen Cream', 4, 3, 'TUB', 'ETC3', 7255, 20000, '0000-00-00', ''),
(250, '', 'Clonaderm cream', 6, 3, 'TUB', 'ETC3', 7260, 20000, '0000-00-00', ''),
(251, '', 'Amlodipine 10mg Mutifa', 150, 30, 'TAB', 'ETC1', 116, 800, '0000-00-00', ''),
(252, '', 'Omeprazole 20mg 10\'s novel', 0, 30, 'KAPS', 'ETC1', 408, 600, '0000-00-00', ''),
(253, '', 'Lansoprazole 30mg Novel & HJ', 80, 30, 'KAPS', 'ETC1', 1008, 1600, '0000-00-00', ''),
(254, '', 'Zantifar', 50, 30, 'TAB', 'ETC1', 270, 900, '0000-00-00', ''),
(255, '', 'Fitkom 21\'s tab Grape', 5, 1, 'BTL', 'OTC1', 12607, 15000, '0000-00-00', ''),
(256, '', 'Microlax', 0, 3, 'TUB', 'OTC2', 18626, 25000, '0000-00-00', ''),
(257, '', 'Farsifen Kaplet 400mg', 80, 30, 'TAB', 'ETC1', 542, 1000, '0000-00-00', ''),
(258, '', 'Trianta Suspensi', 6, 3, 'BTL', 'OTC2', 4579, 12000, '0000-00-00', ''),
(259, '', 'Microgynon Libi', 4, 3, 'STRIP', 'ETC1', 15990, 19000, '0000-00-00', ''),
(260, '', 'Prodermis', 5, 3, 'TUB', 'ETC3', 6600, 13000, '0000-00-00', ''),
(261, '', 'Methyl Prednisolon 4', 180, 30, 'TAB', 'ETC1', 210, 500, '0000-00-00', ''),
(262, '', 'Starlax Sirup', 6, 3, 'BTL', 'OTC2', 18749, 40000, '0000-00-00', ''),
(263, '', 'Alermax', 1, 3, 'STRIP', 'OTC1', 705, 2000, '0000-00-00', ''),
(264, '', 'Vicee 500', 70, 5, 'STRIP', 'OTC1', 629, 3000, '0000-00-00', ''),
(265, '', 'Rhemafar 4mg', 190, 30, 'KPL', 'ETC1', 438, 1500, '0000-00-00', ''),
(266, '', 'Rhemafar 8mg', 80, 30, 'KPL', 'ETC1', 620, 2000, '0000-00-00', ''),
(267, '', 'Rohto Obat Tetes Mata 7ml', 6, 3, 'BTL', 'OTC2', 10989, 15000, '0000-00-00', ''),
(268, '', 'Teosal', 100, 30, 'TAB', 'ETC1', 194, 300, '0000-00-00', ''),
(269, '', 'Acifar 400mg', 50, 30, 'KPL', 'ETC1', 1000, 2400, '0000-00-00', ''),
(270, '', 'Degirol 10\'s', 11, 3, 'STRIP', 'OTC1', 10871, 13000, '0000-00-00', ''),
(271, '', 'Stimuno Forte Capsul', 1, 3, 'STRIP', 'OTC1', 23512, 30000, '0000-00-00', ''),
(272, '', 'Neocenta Gel 15g', 5, 3, 'TUB', 'ETC3', 16170, 33000, '0000-00-00', ''),
(273, '', 'Kanna krim 30g', 6, 3, 'TUB', 'OTC3', 14000, 20000, '0000-00-00', ''),
(274, '', 'Vitacimin Fresh Lemon', 23, 3, 'STRIP', 'OTC1', 2000, 3500, '0000-00-00', ''),
(275, '', 'Betadine Antiseptic Solution 30ml', 5, 3, 'BTL', 'OTC3', 19000, 22000, '0000-00-00', ''),
(276, '', 'Voltaren Emulgel 20g', -2, 2, 'TUB', 'OTC3', 70000, 82000, '0000-00-00', ''),
(277, '', 'Combantrin 250mg tab', 5, 3, 'STRIP', 'OTC1', 14320, 17000, '0000-00-00', ''),
(278, '', 'Canesten 10g', 3, 3, 'TUB', 'OTC3', 37218, 45000, '0000-00-00', ''),
(279, '', 'Plossa Press&Soothe Aromatic Blue Mountain', 4, 2, 'BTL', 'OTC2', 11275, 14000, '0000-00-00', ''),
(280, '', 'Plossa Press&Soothe Aromatics Red Hot', 4, 2, 'BTL', 'OTC2', 11275, 14000, '0000-00-00', ''),
(281, '', 'Plossa Press&Soothe Aromatic Minyak Kayu Putih', 3, 2, 'BTL', 'OTC2', 11275, 14000, '0000-00-00', ''),
(282, '', 'Vira D vit.D3 1000 IU', 10, 3, 'BTL', 'OTC1', 75350, 125000, '0000-00-00', ''),
(283, '', 'Cazetin Drops', 5, 3, 'BTL', 'ETC2', 19500, 40000, '0000-00-00', ''),
(284, '', 'Decolgen', 4, 3, 'STRIP', 'OTC1', 1900, 2000, '0000-00-00', ''),
(285, '', 'Zidalev', 105, 30, 'KPL', 'ETC1', 1107, 3400, '0000-00-00', ''),
(286, '', 'Dionicol Syr', 4, 2, 'BTL', 'ETC2', 7136, 19000, '0000-00-00', ''),
(287, '', 'Allopurinol 100mg', 120, 30, 'TAB', 'ETC1', 150, 250, '0000-00-00', ''),
(288, '', 'Sari Kurma Karomah 250gr', -2, 2, 'BTL', 'OTC2', 18840, 25000, '0000-00-00', ''),
(289, '', 'Air mancur param Kocok 75ml', 4, 2, 'BTL', 'OTC3', 12900, 14000, '0000-00-00', ''),
(290, '', 'Al Afiat ikan Gabus 60 kaps', 2, 2, 'BTL', 'OTC1', 38200, 80000, '0000-00-00', ''),
(291, '', 'Fresh Care Green Tea 10 ml', 4, 2, 'BTL', 'OTC3', 10900, 13000, '0000-00-00', ''),
(292, '', 'Fresh Care Lavender 10 ml', 3, 2, 'BTL', 'OTC3', 10900, 13000, '0000-00-00', ''),
(293, '', 'Fresh Care Press&Relax Strong 10 ml', 10, 2, 'BTL', 'OTC3', 9900, 14000, '0000-00-00', ''),
(294, '', 'Fresh Care Sandalwood 10ml', 0, 2, 'BTL', 'OTC3', 9950, 12000, '0000-00-00', ''),
(295, '', 'Fresh Care Splash Fruity 10ml', 0, 2, 'BTL', 'OTC3', 9950, 12000, '0000-00-00', ''),
(296, '', 'Madu TJ Murni 250gr', 1, 3, 'BTL', 'OTC2', 21950, 25000, '0000-00-00', ''),
(297, '', 'Madu TJ Strawberry 12\'s', 22, 10, 'SCH', 'OTC2', 916, 2000, '0000-00-00', ''),
(298, '', 'Tolak Angin Flu 15ml', 13, 2, 'SCH', 'OTC2', 2683, 4000, '0000-00-00', ''),
(299, '', 'Vicks Inhaler 0,5ml', 10, 2, 'TUB', 'OTC1', 14290, 17000, '0000-00-00', ''),
(300, '', 'Woods Batuk Berdahak (Biru) 60ml', 5, 3, 'BTL', 'OTC2', 17500, 22000, '0000-00-00', ''),
(301, '', 'Cooling 5 Plus Orange', 6, 3, 'BTL', 'OTC2', 32736, 40000, '0000-00-00', ''),
(302, '', 'Cooling 5 Cherry', 3, 2, 'BTL', 'OTC2', 31680, 37000, '0000-00-00', ''),
(303, '', 'Winatin', 130, 30, 'TAB', 'ETC1', 291, 1500, '0000-00-00', ''),
(304, '', 'Vesperum Suspensi 60ml', 4, 2, 'BTL', 'ETC2', 4743, 13000, '0000-00-00', ''),
(305, '', 'Salbutamol Sulfat 4mg', 120, 30, 'TAB', 'ETC1', 110, 200, '0000-00-00', ''),
(306, '', 'Roverton Drops', 6, 3, 'BTL', 'ETC2', 9446, 33000, '0000-00-00', ''),
(307, '', 'Roverton Tab', 140, 30, 'KPL', 'ETC1', 160, 1000, '0000-00-00', ''),
(308, '', 'Ondansetron 8 mg', 50, 30, 'TAB', 'ETC1', 1081, 3000, '0000-00-00', ''),
(309, '', 'Megatic 50', 200, 50, 'TAB', 'ETC1', 257, 850, '0000-00-00', ''),
(310, '', 'Calcifar Plus', 4, 3, 'STRIP', 'OTC1', 1725, 10000, '0000-00-00', ''),
(311, '', 'OBH Combi Anak 60ml', 11, 3, 'BTL', 'OTC2', 12500, 15000, '0000-00-00', ''),
(312, '', 'Andalan Laktasi', 6, 1, 'STRIP', 'ETC1', 11000, 14000, '0000-00-00', ''),
(313, '', 'Erlamycetin Tetes Telinga', 5, 1, 'BTL', 'ETC2', 7500, 11000, '0000-00-00', ''),
(314, '', 'Alletrol Compositum salep mata', 5, 3, 'TUB', 'ETC2', 11000, 17000, '0000-00-00', ''),
(315, '', 'Bronchifar', 0, 3, 'STRIP', 'OTC1', 0, 0, '0000-00-00', ''),
(316, '', 'Fasiprim Susp', 6, 2, 'BTL', 'ETC2', 4950, 13000, '0000-00-00', ''),
(317, '', 'Helixime Syr', 4, 2, 'BTL', 'ETC2', 12000, 35000, '0000-00-00', ''),
(318, '', 'Lostacef 125 Dry Syr', -1, 2, 'BTL', 'ETC1', 7961, 30000, '0000-00-00', ''),
(319, '', 'Lostacef 250 Dry Syr', 4, 2, 'BTL', 'ETC1', 11220, 40000, '0000-00-00', ''),
(320, '', 'Neosma Expectoran Syr', 3, 2, 'BTL', 'ETC2', 4908, 14000, '0000-00-00', ''),
(321, '', 'Yusimox Dry Syr', 8, 2, 'BTL', 'ETC1', 4331, 12000, '0000-00-00', ''),
(322, '', 'Yusimox Forte Dry Syr', 7, 2, 'BTL', 'ETC1', 6270, 20000, '0000-00-00', ''),
(323, '', 'Vesperum Drops', 8, 2, 'BTL', 'ETC2', 13752, 30000, '0000-00-00', ''),
(324, '', 'Acifar Cream', 5, 2, 'TUB', 'ETC3', 7940, 16000, '0000-00-00', ''),
(325, '', 'Elomox Cream', 5, 2, 'TUB', 'OTC3', 7837, 23000, '0000-00-00', ''),
(326, '', 'Polofar Plus Tab', 80, 30, 'TAB', 'ETC1', 197, 600, '0000-00-00', ''),
(327, '', 'Oxicobal', 70, 30, 'TAB', 'ETC1', 452, 1300, '0000-00-00', ''),
(328, '', 'Odanostin Tab', 100, 10, 'TAB', 'ETC1', 1244, 3500, '0000-00-00', ''),
(329, '', 'Neosma Tab', 130, 30, 'TAB', 'ETC1', 201, 600, '0000-00-00', ''),
(330, '', 'Lokev tab', 120, 10, 'KAPS', 'ETC1', 446, 1700, '0000-00-00', ''),
(331, '', 'Faxiden 20', 90, 30, 'KPL', 'ETC1', 211, 600, '0000-00-00', ''),
(332, '', 'Ventolin Nebules 2,5mg', 3, 2, 'STRIP', 'ETC1', 54500, 65000, '0000-00-00', ''),
(333, '', 'Ventolin Inhaler', 4, 2, 'BTL', 'ETC2', 120000, 145000, '0000-00-00', ''),
(334, '', 'Nestacort 2,5%', 9, 2, 'TUB', 'ETC3', 5243, 16000, '0000-00-00', ''),
(335, '', 'Apialys Drops 10ml', 3, 1, 'BTL', 'OTC2', 41800, 55000, '0000-00-00', ''),
(336, '', 'Paramex Biru', 53, 3, 'STRIP', 'OTC1', 2000, 2500, '0000-00-00', ''),
(337, '', 'Armacort Cream', 5, 3, 'TUB', 'ETC3', 6517, 17000, '0000-00-00', ''),
(338, '', 'Dermifar Cream', 6, 0, 'TUB', 'OTC3', 3632, 10500, '0000-00-00', ''),
(339, '', 'Hico Gel', 4, 2, 'TUB', 'OTC3', 12995, 22000, '0000-00-00', ''),
(340, '', 'Lespain Cream', 3, 3, 'TUB', 'OTC3', 5898, 8000, '0000-00-00', ''),
(341, '', 'Orsaderm Cream', 7, 0, 'TUB', 'ETC3', 3588, 7000, '0000-00-00', ''),
(342, '', 'Bromifar', 10, 5, 'STRIP', 'OTC1', 1196, 5000, '0000-00-00', ''),
(343, '', 'Cydifar', 100, 30, 'KPL', 'ETC1', 104, 300, '0000-00-00', ''),
(344, '', 'Decotan', 4, 0, 'STRIP', 'OTC1', 1359, 10000, '0000-00-00', ''),
(345, '', 'Lecozinc Syrup', 5, 3, 'BTL', 'OTC2', 8305, 30000, '0000-00-00', ''),
(346, '', 'Mulsanol Drops', -2, 2, 'BTL', 'OTC2', 11137, 19000, '0000-00-00', ''),
(347, '', 'Kralix Susp', 5, 0, 'BTL', 'ETC2', 10808, 30000, '0000-00-00', ''),
(348, '', 'Mulsanol Gold Syr', 0, 2, 'BTL', 'ETC2', 11385, 15000, '0000-00-00', ''),
(349, '', 'Dohixat', 50, 30, 'TAB', 'ETC1', 396, 1000, '0000-00-00', ''),
(350, '', 'Eltazon tab', 40, 30, 'KPL', 'ETC1', 162, 450, '0000-00-00', ''),
(351, '', 'Fasidol Plus', 10, 0, 'STRIP', 'OTC1', 2830, 7000, '0000-00-00', ''),
(352, '', 'Glikos 850', 110, 30, 'KPL', 'ETC1', 332, 1200, '0000-00-00', ''),
(353, '', 'Lincor Kapsul', 8, 0, 'KAPS', 'ETC1', 866, 7000, '0000-00-00', ''),
(354, '', 'Lecozinc Tab', 9, 3, 'STRIP', 'OTC1', 2371, 10000, '0000-00-00', ''),
(355, '', 'Odanostin Forte', -30, 30, 'TAB', 'ETC1', 1746, 3500, '0000-00-00', ''),
(356, '', 'Scopma', 115, 30, 'TAB', 'ETC1', 986, 2800, '0000-00-00', ''),
(357, '', 'Albendazol 400mg 10x10', -30, 30, 'TAB', 'ETC1', 598, 750, '0000-00-00', ''),
(358, '', 'Isoniazid 300mg', 0, 30, 'TAB', 'ETC1', 282, 350, '0000-00-00', ''),
(359, '', 'Guanistrep syr 60ml', 9, 0, 'BTL', 'OTC2', 4130, 10000, '0000-00-00', ''),
(360, '', 'Itrabat Syr 100ml', 5, 0, 'BTL', 'OTC2', 8030, 15000, '0000-00-00', ''),
(361, '', 'Pimtracol syr lemon', 12, 0, 'BTL', 'OTC2', 9650, 15000, '0000-00-00', ''),
(362, '', 'Pimtracol syr cherry', 12, 0, 'BTL', 'OTC2', 9650, 15000, '0000-00-00', ''),
(363, '', 'batugin elixir 300ml', 0, 0, 'BTL', 'OTC2', 43913, 54000, '0000-00-00', ''),
(364, '', 'Actifed Cough Merah 60ml', 4, 0, 'BTL', 'OTC2', 49223, 70000, '0000-00-00', ''),
(365, '', 'Actifed Plus Hijau 60ml', -6, 3, 'BTL', 'OTC2', 52500, 61000, '0000-00-00', ''),
(366, '', 'Actifed Syr Kuning 60ml', 8, 3, 'BTL', 'OTC2', 49224, 58000, '0000-00-00', ''),
(367, '', 'Becom C', 13, 3, 'STRIP', 'OTC1', 16978, 20000, '0000-00-00', ''),
(368, '', 'Becomzet', 12, 3, 'STRIP', 'OTC1', 22407, 27000, '0000-00-00', ''),
(369, '', 'Caladine Powder Original 100gr', 0, 0, 'BTL', 'OTC3', 13970, 16000, '0000-00-00', ''),
(370, '', 'Caladine Lotion 95ml', 4, 0, 'BTL', 'OTC3', 19250, 23000, '0000-00-00', ''),
(371, '', 'Cataflam 50mg', 52, 10, 'TAB', 'ETC1', 6430, 8000, '0000-00-00', ''),
(372, '', 'Daktarin 5gr', 5, 1, 'TUB', 'OTC3', 21890, 26000, '0000-00-00', ''),
(373, '', 'Decolsin caps', 13, 1, 'STRIP', 'OTC1', 2332, 3000, '0000-00-00', ''),
(374, '', 'Degirol 4\'s', 0, 3, 'STRIP', 'OTC1', 4603, 5500, '0000-00-00', ''),
(375, '', 'Enkasari Herbal 120ml', 0, 1, 'BTL', 'OTC2', 17860, 22000, '0000-00-00', ''),
(376, '', 'Fitkom 21\'s tab Orange', 3, 1, 'BTL', 'OTC1', 12212, 15000, '0000-00-00', ''),
(377, '', 'Fitkom 21\'s tab Strawberry', 3, 1, 'BTL', 'OTC1', 12212, 15000, '0000-00-00', ''),
(378, '', 'Lactacyd Feminine 60ml', 0, 1, 'BTL', 'OTC3', 25179, 30000, '0000-00-00', ''),
(379, '', 'Lactacyd Feminine 150ml', 6, 1, 'BTL', 'OTC3', 57090, 68000, '0000-00-00', ''),
(380, '', 'Mecobalamin 500mcg kaps', 290, 10, 'KAPS', 'ETC1', 253, 1200, '0000-00-00', ''),
(381, '', 'Genalog Cream 5gr', 16, 3, 'TUB', 'ETC3', 13200, 30000, '0000-00-00', ''),
(382, '', 'Rodemin Powder Lavender 100gr', 1, 3, 'BTL', 'ETC1', 12100, 15000, '0000-00-00', ''),
(383, '', 'Rodemin Powder Eucalyptus', 2, 3, 'BTL', 'ETC1', 12100, 15000, '0000-00-00', ''),
(384, '', 'Simvastatin 20', 80, 30, 'TAB', 'ETC1', 234, 1300, '0000-00-00', ''),
(385, '', 'Callusol', 2, 2, 'BTL', 'OTC3', 28000, 35000, '0000-00-00', ''),
(386, '', 'Etoricoxib 60mg tab', 0, 30, 'TAB', 'ETC1', 3465, 5000, '0000-00-00', ''),
(387, '', 'Etoricoxib 90mg tab', 53, 30, 'TAB', 'ETC1', 4950, 7000, '0000-00-00', ''),
(388, '', 'Dexketoprofen 25mg', 0, 20, 'TAB', 'ETC1', 2805, 4600, '0000-00-00', ''),
(389, '', 'Caladine Powder 60g Active Fresh', 2, 1, 'BTL', 'OTC3', 10282, 12000, '0000-00-00', ''),
(390, '', 'Caladine Powder 60g Original', 1, 1, 'BTL', 'OTC3', 11300, 13000, '0000-00-00', ''),
(391, '', 'Caladine Powder 60g Soft Comfort', 2, 1, 'BTL', 'OTC3', 9375, 11000, '0000-00-00', ''),
(392, '', 'Fatigon Spirit', 15, 3, 'STRIP', 'OTC1', 6333, 8000, '0000-00-00', ''),
(393, '', 'Cendo Cenfresh 5ml', 3, 2, 'BTL', 'ETC2', 36444, 49000, '0000-00-00', ''),
(394, '', 'Cendo Lyteers15cc', 3, 2, 'BTL', 'ETC2', 24750, 32000, '0000-00-00', ''),
(395, '', 'Cendo Xitrol Minidose', 5, 2, 'STRIP', 'ETC2', 26813, 34500, '0000-00-00', ''),
(396, '', 'Isoniazid 100mg', 280, 30, 'TAB', 'ETC1', 132, 200, '0000-00-00', ''),
(397, '', 'Diapet 4\'s', 0, 0, 'STRIP', 'HRB1', 2000, 2500, '0000-00-00', ''),
(398, '', 'KSR 600mg', 10, 0, 'TAB', 'ETC1', 3410, 4000, '0000-00-00', ''),
(399, '', 'Metformin 850', 70, 30, 'TAB', 'ETC1', 280, 400, '0000-00-00', ''),
(400, '', 'Gliquidone 30mg', -10, 30, 'TAB', 'ETC1', 1250, 2000, '0000-00-00', ''),
(401, '', 'Ecosol RL', 10, 1, 'BTL', 'ETC2', 10500, 15000, '0000-00-00', ''),
(402, '', 'NaCl 0,9% 500 ml Braun', 0, 3, 'BTL', 'ETC2', 9500, 15000, '0000-00-00', ''),
(403, '', 'Eyevit Tab', 5, 3, 'STRIP', 'OTC1', 28958, 35000, '0000-00-00', ''),
(404, '', 'Apialys Syr 100ml', 5, 3, 'BTL', 'OTC2', 34485, 45000, '0000-00-00', ''),
(405, '', 'Atorvastatin 20mg 6\'s', 120, 10, 'TAB', 'ETC1', 1167, 5000, '0000-00-00', ''),
(406, '', 'Omecal+D', 9, 30, 'STRIP', 'OTC1', 1320, 3500, '0000-00-00', ''),
(407, '', 'Nizogen Cream 2%', 4, 5, 'TUB', 'ETC3', 4180, 18000, '0000-00-00', ''),
(408, '', 'Sulfatgen Syr 100ml', 5, 2, 'BTL', 'ETC2', 17600, 70000, '0000-00-00', ''),
(409, '', 'Genbrox Syr 60ml', 7, 3, 'BTL', 'ETC2', 3190, 22000, '0000-00-00', ''),
(410, '', 'Mefenamic Acid 500mg NOVAPHARIN', 150, 30, 'TAB', 'ETC1', 180, 500, '0000-00-00', ''),
(411, '', 'Genlipid 20mg', 30, 20, 'KAPS', 'ETC1', 1467, 7000, '0000-00-00', ''),
(412, '', 'Antasida Doen tab', 7, 30, 'STRIP', 'OTC1', 847, 2000, '0000-00-00', ''),
(413, '', 'Genamox 500mg', 140, 30, 'TAB', 'ETC1', 374, 1500, '0000-00-00', ''),
(414, '', 'IPI Vitamin C 45 Tablet', 13, 3, 'POT', 'OTC1', 5414, 10000, '0000-00-00', ''),
(415, '', 'Promina Bubur Bayi Beras Merah', 3, 1, 'BOX', 'SUSU', 11915, 20000, '0000-00-00', ''),
(416, '', 'Promina Bubur Bayi Ayam Kampung Brocoli', 0, 1, 'BOX', 'SUSU', 14070, 20000, '0000-00-00', ''),
(417, '', 'Promina Bubur Ayam Tomat', 1, 1, 'BOX', 'SUSU', 15600, 20000, '0000-00-00', ''),
(418, '', 'Promina Arrowroot Biscuit', 1, 1, 'BOX', 'SUSU', 14975, 18000, '0000-00-00', ''),
(419, '', 'SGM BBLR 200g', 2, 1, 'BOX', 'SUSU', 37800, 44000, '0000-00-00', ''),
(420, '', 'SGM LLM 200g', 3, 1, 'BOX', 'SUSU', 32985, 38000, '0000-00-00', ''),
(421, '', 'SGM Ananda 400g 0-6bulan', 3, 1, 'BOX', 'SUSU', 37400, 43000, '0000-00-00', ''),
(422, '', 'SGM Ananda 150g 0-6bulan', 0, 1, 'BOX', 'SUSU', 13885, 15000, '0000-00-00', ''),
(423, '', 'SGM Ananda 150g 6-12bulan', 0, 1, 'BOX', 'SUSU', 13885, 15000, '0000-00-00', ''),
(424, '', 'SGM Explor 400 vanila', 0, 1, 'BOX', 'SUSU', 36935, 40000, '0000-00-00', ''),
(425, '', 'SGM Explor 400 madu', 1, 1, 'BOX', 'SUSU', 35785, 40000, '0000-00-00', ''),
(426, '', 'ZEE 350 Cho', 0, 1, 'BOX', 'SUSU', 35275, 43000, '0000-00-00', ''),
(427, '', 'Prenagen MOM 200 vanila', 1, 1, 'BOX', 'SUSU', 40400, 45000, '0000-00-00', ''),
(428, '', 'Prenagen MOM 200 Coklat', 0, 1, 'BOX', 'SUSU', 40400, 45000, '0000-00-00', ''),
(429, '', 'Prenagen Emesis Vanila', 1, 1, 'BOX', 'SUSU', 42500, 47000, '0000-00-00', ''),
(430, '', 'Prenagen Emesis 200 coklat', 1, 1, 'BOX', 'SUSU', 42500, 47000, '0000-00-00', ''),
(431, '', 'Milna GM 120g SM Ayam', 0, 1, 'BOX', 'SUSU', 21650, 25000, '0000-00-00', ''),
(432, '', 'Milna Bubur Tim AT', 0, 1, 'BOX', 'SUSU', 15175, 19000, '0000-00-00', ''),
(433, '', 'SUN Jeruk, Apel & Pisang 120g', 1, 1, 'BOX', 'SUSU', 6625, 10000, '0000-00-00', ''),
(434, '', 'SUN Pisang Susu 120g', 0, 1, 'BOX', 'SUSU', 7000, 10000, '0000-00-00', ''),
(435, '', 'SUN Kacang Hijau 120g', 1, 1, 'BOX', 'SUSU', 7000, 10000, '0000-00-00', ''),
(436, '', 'Tolak Angin Cair 5 Sachet/Box', 0, 1, 'BOX', 'OTC2', 13800, 17000, '0000-00-00', ''),
(437, '', 'Imboost Syr Kids 60ml', 3, 2, 'BTL', 'OTC2', 35000, 42000, '0000-00-00', ''),
(438, '', 'Imboost Force Syr 60ml', 2, 2, 'BTL', 'OTC2', 55813, 75000, '0000-00-00', ''),
(439, '', 'Imboost Force Tab', 12, 2, 'STRIP', 'OTC1', 57411, 75000, '0000-00-00', ''),
(440, '', 'Vitacid CR 0,1% 20g', 0, 3, 'TUB', 'ETC3', 42583, 58000, '0000-00-00', ''),
(441, '', 'Polysilane Tab', 4, 50, 'STRIP', 'OTC1', 8192, 11000, '0000-00-00', ''),
(442, '', 'Cendo Timol 0,5% Miinidise', 2, 30, 'STRIP', 'ETC2', 23175, 59000, '0000-00-00', ''),
(443, '', 'Transpulmin BB Baby 10g', 4, 3, 'TUB', 'OTC3', 45408, 56000, '0000-00-00', ''),
(444, '', 'Transpulmin BB Baby 20g', 7, 3, 'TUB', 'OTC3', 60836, 77000, '0000-00-00', ''),
(445, '', 'Transpulmin Balsem Kids 10g', 3, 3, 'TUB', 'OTC3', 42126, 57000, '0000-00-00', ''),
(446, '', 'Transpulmin  Balsem Kids 20g', 3, 3, 'TUB', 'OTC3', 55100, 75000, '0000-00-00', ''),
(447, '', 'Betadine Antiseptic Solution 15ml', 0, 3, 'BTL', 'OTC3', 11133, 15000, '0000-00-00', ''),
(448, '', 'Oxyvell 30 Kapsul', 3, 3, 'STRIP', 'OTC1', 35574, 44000, '0000-00-00', ''),
(449, '', 'CDR EFF 15\'s', 8, 2, 'BTL', 'OTC1', 53955, 60000, '0000-00-00', ''),
(450, '', 'CDR EFF 20\'s', 9, 3, 'BOX', 'OTC1', 72533, 85000, '0000-00-00', ''),
(451, '', 'CDR EFF In Box (12 Strip)', 5, 3, 'STRIP', 'OTC1', 8138, 12000, '0000-00-00', ''),
(452, '', 'Dextamine Tab', 60, 30, 'TAB', 'ETC1', 1815, 2200, '0000-00-00', ''),
(453, '', 'Dolo Neurobion Tab', 10, 2, 'STRIP', 'OTC1', 19110, 22000, '0000-00-00', ''),
(454, '', 'Dulcolax Suppositoria 5\'s', 7, 3, 'STRIP', 'ETC5', 22693, 30000, '0000-00-00', ''),
(455, '', 'Laserin Madu 100 ml', 5, 3, 'BTL', 'HRB2', 18399, 22000, '0000-00-00', ''),
(456, '', 'Laserin Syr 110 ml', 4, 3, 'BTL', 'HRB2', 17975, 22000, '0000-00-00', ''),
(457, '', 'Mucos Drops 20 ml', 4, 3, 'BTL', 'ETC2', 37950, 47000, '0000-00-00', ''),
(458, '', 'Ponstan 500 FCT', 27, 30, 'TAB', 'ETC1', 2586, 3000, '0000-00-00', ''),
(459, '', 'Primolut N Tab', -17, 30, 'TAB', 'ETC1', 5807, 10000, '0000-00-00', ''),
(460, '', 'Pro TB 2 28\'s', 0, 50, 'TAB', 'ETC1', 7246, 9000, '0000-00-00', ''),
(461, '', 'Pro TB 4 28\'s', 0, 50, 'TAB', 'ETC1', 5427, 6700, '0000-00-00', ''),
(462, '', 'Promag Tab 12\'s', 0, 3, 'STRIP', 'OTC1', 6417, 8000, '0000-00-00', ''),
(463, '', 'Scott\'s Orig 200ml', 0, 0, 'BTL', 'OTC2', 37910, 0, '0000-00-00', ''),
(464, '', 'Scott\'s Emulsio Orig 400ml', 4, 1, 'BTL', 'OTC2', 67064, 75000, '0000-00-00', ''),
(465, '', 'Stimuno Grape 60ml', 0, 3, 'BTL', 'OTC2', 24833, 30000, '0000-00-00', ''),
(466, '', 'Stimuno Orange Berry 60ml', 0, 0, 'BTL', 'OTC2', 24336, 0, '0000-00-00', ''),
(467, '', 'Stimuno Syr Ori 60ml', 0, 3, 'BTL', 'OTC2', 24336, 30000, '0000-00-00', ''),
(468, '', 'Strocain P 100\'s 400mg', 5, 3, 'STRIP', 'OTC1', 23298, 29000, '0000-00-00', ''),
(469, '', 'Batugin Elix 120ml', 3, 1, 'BTL', 'HRB2', 24291, 30000, '0000-00-00', ''),
(470, '', 'Bepanthen Oint 20gr', 0, 2, 'TUB', 'OTC3', 56199, 64000, '0000-00-00', ''),
(471, '', 'Betason N Cream 5g', -1, 3, 'TUB', 'ETC3', 14823, 20000, '0000-00-00', ''),
(472, '', 'Burnazin Cream 35g', 2, 3, 'TUB', 'ETC3', 61793, 72500, '0000-00-00', ''),
(473, '', 'Counterpain 15 gr', 2, 2, 'TUB', 'OTC3', 23320, 27000, '0000-00-00', ''),
(474, '', 'Counterpain Cool 15 gr', 3, 2, 'TUB', 'OTC3', 24530, 28000, '0000-00-00', ''),
(475, '', 'Counterpain Cool 30 gr', 4, 3, 'TUB', 'OTC3', 39710, 50000, '0000-00-00', ''),
(476, '', 'Cream otot geliga 30 gr', 3, 1, 'TUB', 'OTC3', 9208, 12000, '0000-00-00', ''),
(477, '', 'Cream otot geliga 60 gr', 0, 0, 'POT', 'OTC3', 21541, 0, '0000-00-00', ''),
(478, '', 'Curvit 60 ml', 3, 3, 'BTL', 'OTC2', 24915, 30000, '0000-00-00', ''),
(479, '', 'Emla Cream 50 mg', 3, 1, 'TUB', 'ETC3', 55100, 75000, '0000-00-00', ''),
(480, '', 'Enervon C tab 4\'s', 6, 3, 'STRIP', 'OTC1', 4393, 7000, '0000-00-00', ''),
(481, '', 'Enervon C tab 30', 3, 2, 'BTL', 'OTC1', 32351, 45000, '0000-00-00', ''),
(482, '', 'Enkasari pepermint 100 ml', 0, 0, 'BTL', 'OTC2', 9405, 0, '0000-00-00', ''),
(483, '', 'Enkasari Pepermint 250 ml', 0, 0, 'BTL', 'OTC2', 17243, 0, '0000-00-00', ''),
(484, '', 'Hemaviton 30\'s', 0, 0, 'BTL', 'OTC1', 28576, 0, '0000-00-00', ''),
(485, '', 'Hemaviton Act Box', 6, 50, 'STRIP', 'OTC1', 5779, 7500, '0000-00-00', ''),
(486, '', 'Hemobion Kapsul', 2, 50, 'STRIP', 'OTC1', 20322, 26000, '0000-00-00', ''),
(487, '', 'Mefinal 500 mg', 90, 50, 'TAB', 'ETC1', 1469, 1800, '0000-00-00', ''),
(488, '', 'Minyak Tawon CC', 3, 1, 'BTL', 'OTC3', 19206, 23000, '0000-00-00', ''),
(489, '', 'OBH Nellco Spesial 100 ml', 4, 0, 'BTL', 'OTC2', 35295, 42000, '0000-00-00', ''),
(490, '', 'OBH Nellco Spesial 55 ml', 3, 3, 'BTL', 'OTC2', 26794, 34000, '0000-00-00', ''),
(491, '', 'Otopain Ear Drops', 5, 1, 'BTL', 'ETC2', 71775, 93000, '0000-00-00', ''),
(492, '', 'Pharmaton Formula', 3, 2, 'STRIP', 'OTC1', 22075, 26000, '0000-00-00', ''),
(493, '', 'Redoxon Blackcurrant 10\'s', 0, 3, 'TUB', 'OTC1', 35197, 0, '0000-00-00', ''),
(494, '', 'Redoxon Strip 2\'s', 14, 3, 'STRIP', 'OTC1', 6729, 10000, '0000-00-00', ''),
(495, '', 'Sakatonik ABC Anggur', 0, 0, 'BTL', 'OTC1', 15000, 18000, '0000-00-00', ''),
(496, '', 'Sakatonik ABC Antariksa', 1, 3, 'BTL', 'OTC1', 13222, 18000, '0000-00-00', ''),
(497, '', 'Sakatonik Liver 100 ml', 0, 0, 'BTL', 'OTC2', 9282, 0, '0000-00-00', ''),
(498, '', 'Sakatonik Liver 310 ml', 0, 0, 'BTL', 'OTC2', 22090, 0, '0000-00-00', ''),
(499, '', 'Sakatonik Liver kapl 10\'s', 6, 3, 'STRIP', 'OTC1', 6518, 10000, '0000-00-00', ''),
(500, '', 'Thrombopop Ointmen', 4, 3, 'TUB', 'OTC1', 43450, 50000, '0000-00-00', ''),
(501, '', 'Woods Herbal 60ml', 4, 3, 'BTL', 'OTC2', 17000, 20000, '0000-00-00', ''),
(502, '', 'Lansoprazole 30mg Ifars', 0, 20, 'KAPS', 'ETC1', 637, 1500, '0000-00-00', ''),
(503, '', 'Redoxon Triple Action 15\'s', 0, 3, 'TUB', 'OTC1', 35197, 55000, '0000-00-00', ''),
(504, '', 'Sakatonik ABC Gummy F&V', 0, 0, 'SCH', 'OTC1', 7603, 0, '0000-00-00', ''),
(505, '', 'Curcuma DHA Support 60ml (kuning)', 2, 2, 'BTL', 'OTC2', 12500, 17500, '0000-00-00', ''),
(506, '', 'OB Herbal 100 ml', 0, 3, 'BTL', 'OTC2', 17999, 22000, '0000-00-00', ''),
(507, '', 'OBH Combi 60 ml', 0, 3, 'BTL', 'OTC2', 10726, 14000, '0000-00-00', ''),
(508, '', 'Chiliplast plester kecil', 6, 3, 'UNT', 'ALK', 1500, 3000, '0000-00-00', ''),
(509, '', 'Proris Suppositoria', 3, 0, 'PCS', 'ETC5', 5000, 10000, '0000-00-00', ''),
(510, '', 'Sevotam 800mg', 0, 30, 'TAB', 'ETC1', 578, 0, '0000-00-00', ''),
(511, '', 'Pyratibi 500mg', 200, 30, 'TAB', 'ETC1', 386, 1000, '0000-00-00', ''),
(512, '', 'Etasma tab 2,5mg', 0, 30, 'TAB', 'ETC1', 0, 0, '0000-00-00', ''),
(513, '', 'Ampicillin 500mg', 0, 30, 'TAB', 'ETC1', 325, 0, '0000-00-00', ''),
(514, '', 'Ranitidine HCL 150mg', 200, 30, 'TAB', 'ETC1', 198, 300, '0000-00-00', ''),
(515, '', 'Ethambutol 500mg', 0, 30, 'TAB', 'ETC1', 0, 0, '0000-00-00', ''),
(516, '', 'Woods Batuk Berdahak (biru) 100ml', 5, 3, 'BTL', 'OTC2', 27500, 32000, '0000-00-00', ''),
(517, '', 'Woods AntiTussive (merah) 100ml', 7, 3, 'BTL', 'OTC2', 27500, 32000, '0000-00-00', ''),
(518, '', 'Voltadex 50mg', 160, 30, 'TAB', 'ETC1', 495, 700, '0000-00-00', ''),
(519, '', 'Voltadex Gel', 4, 3, 'TUB', 'ETC3', 22550, 30000, '0000-00-00', ''),
(520, '', 'Minyak Kayu Putih Caplang 210ml', 1, 2, 'BTL', 'OTC2', 62425, 70000, '0000-00-00', ''),
(521, '', 'OBH Itrasal 100ml', 8, 2, 'BTL', 'OTC2', 3660, 6000, '0000-00-00', ''),
(522, '', 'Hufagrip Pilek (Biru)', 6, 3, 'BTL', 'OTC2', 14035, 17000, '0000-00-00', ''),
(523, '', 'Laxing 4\'s', 5, 3, 'STRIP', 'OTC1', 3132, 4000, '0000-00-00', ''),
(524, '', 'Bodrex Tab 10\'s', 8, 50, 'STRIP', 'OTC1', 3395, 5000, '0000-00-00', ''),
(525, '', 'Forumen Ear Drops', 6, 2, 'BTL', 'OTC2', 28000, 35000, '0000-00-00', ''),
(526, '', 'Ultraflu PE 4\'S', 14, 3, 'STRIP', 'OTC1', 2800, 4000, '0000-00-00', ''),
(527, '', 'Alpara syrup 60ml', 5, 3, 'BTL', 'OTC2', 10000, 15000, '0000-00-00', ''),
(528, '', 'Incidal-OD kapsul', 84, 20, 'KAPS', 'ETC1', 3250, 4000, '0000-00-00', ''),
(529, '', 'Dulcolax 5mg tab 4\'s', 22, 3, 'STRIP', 'OTC1', 7610, 10000, '0000-00-00', ''),
(530, '', 'Neo Napacin 4\'s', 30, 3, 'STRIP', 'OTC1', 2500, 4000, '0000-00-00', ''),
(531, '', 'Alpara tab', 30, 3, 'STRIP', 'OTC1', 6666, 10000, '0000-00-00', ''),
(532, '', 'Procold Flu 6\'s', 13, 3, 'STRIP', 'OTC1', 3125, 5000, '0000-00-00', ''),
(533, '', 'Komix Jeruk Nipis 7ml @30sch', 0, 3, 'SCH', 'OTC2', 1233, 2500, '0000-00-00', ''),
(534, '', 'Komix Papermint 7ml @30sch', 0, 3, 'SCH', 'OTC2', 1233, 2500, '0000-00-00', ''),
(535, '', 'Komix Jahe 7ml @30sch', 15, 3, 'SCH', 'OTC2', 1233, 2500, '0000-00-00', ''),
(536, '', 'Komix OBH 7ml @30sch', 48, 3, 'SCH', 'OTC2', 1233, 2500, '0000-00-00', ''),
(537, '', 'Sanmol Sirup Forte 60 ml', 7, 3, 'BTL', 'OTC2', 27281, 36000, '0000-00-00', ''),
(538, '', 'Allopurinol 300mg', 80, 30, 'TAB', 'ETC1', 462, 700, '0000-00-00', ''),
(539, '', 'Fenofibrate 300mg', 0, 30, 'TAB', 'ETC1', 4400, 5500, '0000-00-00', ''),
(540, '', 'Glimepiride 3mg', 190, 30, 'TAB', 'ETC1', 1320, 1800, '0000-00-00', ''),
(541, '', 'Glimepiride 4mg', 150, 30, 'TAB', 'ETC1', 1760, 2500, '0000-00-00', ''),
(542, '', 'Meloxicam 7,5mg', 70, 30, 'TAB', 'ETC1', 418, 600, '0000-00-00', ''),
(543, '', 'Aciclovir 400mg', 100, 30, 'TAB', 'ETC1', 520, 1000, '0000-00-00', ''),
(544, '', 'Baby Cough Sirup 60ml UNIBEBI', 10, 3, 'BTL', 'OTC2', 4000, 7000, '0000-00-00', ''),
(545, '', 'Lelap tab 4\'s', 4, 3, 'STRIP', 'HRB1', 14373, 25000, '0000-00-00', ''),
(546, '', 'Mixagrip Flu & Batuk tab 4\'s', 8, 3, 'STRIP', 'OTC1', 2179, 3000, '0000-00-00', ''),
(547, '', 'Mixagrip Flu tab 4\'s', 8, 3, 'STRIP', 'OTC1', 2000, 2500, '0000-00-00', ''),
(548, '', 'Nature E kaps 4\'s', 6, 3, 'STRIP', 'OTC1', 4161, 8000, '0000-00-00', ''),
(549, '', 'Entrostop tab 12\'s', 8, 2, 'STRIP', 'OTC1', 6375, 8000, '0000-00-00', ''),
(550, '', 'Puyer OSK no.16', 20, 3, 'SCH', 'OTC1', 745, 1000, '0000-00-00', ''),
(551, '', 'Antangin JRG tab 4\'s', 10, 3, 'STRIP', 'HRB1', 2509, 3000, '0000-00-00', ''),
(552, '', 'Adem sari', 3, 3, 'SCH', 'MNMK', 2100, 2500, '0000-00-00', ''),
(553, '', 'Antangin sirup Junior', 16, 3, 'SCH', 'OTC2', 1732, 2500, '0000-00-00', ''),
(554, '', 'Antangin syrup JRG', 17, 3, 'SCH', 'OTC2', 2509, 3000, '0000-00-00', ''),
(555, '', 'Antimo dewasa tab 10\'s', 14, 3, 'STRIP', 'OTC1', 3960, 5000, '0000-00-00', ''),
(556, '', 'Bejo Jahe Merah', 6, 3, 'SCH', 'HRB2', 2083, 3500, '0000-00-00', ''),
(557, '', 'Bisolvon Extra syrup 60ml', 3, 3, 'BTL', 'OTC2', 40829, 50000, '0000-00-00', ''),
(558, '', 'Entrostop syrup anak 10ml', 8, 3, 'SCH', 'HRB2', 1833, 2500, '0000-00-00', ''),
(559, '', 'Kalpanax cream 5 g', 5, 3, 'POT', 'OTC3', 5700, 16000, '0000-00-00', ''),
(560, '', 'Komix Herbal Jeruk Nipis 15ml @6sch', 6, 3, 'SCH', 'HRB2', 1383, 3000, '0000-00-00', ''),
(561, '', 'Komix Kids OBH 5ml @10sch', 4, 3, 'SCH', 'OTC2', 890, 1500, '0000-00-00', ''),
(562, '', 'Holisticare Ester C botol 30\'s', 3, 2, 'BTL', 'OTC1', 41218, 50000, '0000-00-00', ''),
(563, '', 'Holisticare Ester C tab 4\'s', 13, 3, 'STRIP', 'OTC1', 5717, 8000, '0000-00-00', ''),
(564, '', 'Tera F tab 10\'s', 16, 3, 'STRIP', 'OTC1', 3800, 7500, '0000-00-00', ''),
(565, '', 'Mylanta tab 10\'s', 14, 3, 'STRIP', 'OTC1', 6600, 8000, '0000-00-00', ''),
(566, '', 'Cavit D3', 3, 3, 'STRIP', 'OTC1', 23500, 28000, '0000-00-00', ''),
(567, '', 'Panadol Hijau', 8, 0, 'STRIP', 'OTC1', 12291, 15000, '0000-00-00', ''),
(568, '', 'Panadol syr 60ml', 0, 0, 'BTL', 'OTC2', 45305, 50000, '0000-00-00', ''),
(569, '', 'BMT GOLD 400 GR', 0, 0, 'BOX', 'PKRT', 90552, 92000, '0000-00-00', ''),
(570, '', 'CHIL KID SOYA 300 GR', 0, 0, 'BOX', 'PKRT', 84260, 90000, '0000-00-00', ''),
(571, '', 'CHIL SCHOOL SOYA 300 GR', 0, 0, 'BOX', 'PKRT', 74960, 80000, '0000-00-00', ''),
(572, '', 'DIABETASOL WAFER CHOCOLATE 2X50 G', 0, 0, 'BOX', 'MN', 16321, 20000, '0000-00-00', ''),
(573, '', 'ENTRASOL SENIOR VAN 400 GR', 0, 0, 'KLG', 'PKRT', 102951, 120000, '0000-00-00', ''),
(574, '', 'Melanox Forte', 4, 0, 'TUB', 'ETC3', 34650, 43000, '0000-00-00', ''),
(575, '', 'Zambuk 8 gr', 4, 3, 'PCS', 'OTC3', 14256, 17000, '0000-00-00', ''),
(576, '', 'Betadine Plester Elastis (5)', 4, 3, 'SCH', 'OTC3', 3702, 5000, '0000-00-00', ''),
(577, '', 'Laserin 60ml', 4, 3, 'BTL', 'HRB2', 8873, 12000, '0000-00-00', ''),
(578, '', 'Vitalong C', 2, 3, 'BTL', 'OTC1', 39022, 47000, '0000-00-00', ''),
(579, '', 'Promag Tab 10\'s', 12, 3, 'STRIP', 'OTC1', 6416, 8000, '0000-00-00', ''),
(580, '', 'Fatigon 6\'s Caps', 8, 3, 'STRIP', 'OTC1', 5400, 6500, '0000-00-00', ''),
(581, '', 'Neo Rheumacyl Cream 30g HOT (merah)', 4, 3, 'TUB', 'OTC3', 12500, 15000, '0000-00-00', ''),
(582, '', 'Betadine Feminime Hygiene 60ml', 4, 2, 'BTL', 'ETC2', 25333, 35000, '0000-00-00', ''),
(583, '', 'New Diatabs tab 4\'s', 6, 3, 'STRIP', 'OTC1', 2206, 3000, '0000-00-00', ''),
(584, '', 'Herocyn 85g', 5, 2, 'BTL', 'OTC1', 10990, 14000, '0000-00-00', ''),
(585, '', 'Vicks sirup dewasa 54ml', 8, 2, 'BTL', 'OTC2', 13450, 16000, '0000-00-00', ''),
(586, '', 'Vicks sirup dewasa 27ml', 6, 2, 'BTL', 'OTC2', 6659, 8000, '0000-00-00', ''),
(587, '', 'Paratusin syrup 60ml', 5, 3, 'BTL', 'OTC2', 31625, 40000, '0000-00-00', ''),
(588, '', 'Paratusin Tab 10\'s', 9, 3, 'STRIP', 'OTC1', 3160, 17000, '0000-00-00', ''),
(589, '', 'Madu TJ Joybee Strawberry 100 ml', 5, 3, 'BTL', 'OTC2', 10670, 15000, '0000-00-00', ''),
(590, '', 'Diapet 10\'s', 7, 3, 'STRIP', 'HRB1', 4042, 6000, '0000-00-00', ''),
(591, '', 'Mextril', 18, 5, 'STRIP', 'OTC1', 2200, 3000, '0000-00-00', ''),
(592, '', 'Peka', 17, 3, 'POT', 'OTC3', 1250, 3000, '0000-00-00', ''),
(593, '', 'Norit', 3, 3, 'UNT', 'HRB1', 14483, 17000, '0000-00-00', ''),
(594, '', 'Superhoid', 4, 0, 'PCS', 'ETC5', 30000, 40000, '0000-00-00', ''),
(595, '', 'Gastrucid Syrup', 8, 2, 'BTL', 'OTC2', 7990, 13000, '0000-00-00', ''),
(596, '', 'Hevit - Plus', 1, 3, 'STRIP', 'OTC1', 9900, 15000, '0000-00-00', ''),
(597, '', 'Erlamycetin Salep Mata', 5, 2, 'TUB', 'ETC3', 8150, 14000, '0000-00-00', '');
INSERT INTO `barang` (`id_barang`, `kd_barang`, `nm_barang`, `stok_barang`, `stok_buffer`, `sat_barang`, `jenisobat`, `hrgsat_barang`, `hrgjual_barang`, `tgl_expired`, `ket_barang`) VALUES
(598, '', 'Neozep Forte', 1, 3, 'STRIP', 'OTC1', 2074, 2500, '0000-00-00', ''),
(599, '', 'Folavit 400', 9, 3, 'STRIP', 'OTC1', 9352, 13000, '0000-00-00', ''),
(600, '', 'Vermint 12\'s', 10, 2, 'BTL', 'OTC1', 19000, 25000, '0000-00-00', ''),
(601, '', 'Xonce Orange', 25, 3, 'STRIP', 'OTC1', 1600, 3000, '0000-00-00', ''),
(602, '', 'Listerin Freshburst 100 ml', 4, 2, 'BTL', 'PKRT', 7480, 11000, '0000-00-00', ''),
(603, '', 'Listerin Coolmint 250 ml', 2, 2, 'BTL', 'PKRT', 19305, 25000, '0000-00-00', ''),
(604, '', 'Listerin Original 250 ml', 1, 2, 'BTL', 'PKRT', 19305, 25000, '0000-00-00', ''),
(605, '', 'Listerin Burst 250 ml', 0, 2, 'BTL', 'PKRT', 19305, 25000, '0000-00-00', ''),
(606, '', 'Minyak Telon Lang 60 ml', 2, 2, 'BTL', 'PKRT', 15766, 21000, '0000-00-00', ''),
(607, '', 'Termorex Plus 30ml', 12, 2, 'BTL', 'OTC2', 8000, 12000, '0000-00-00', ''),
(608, '', 'Cinolon Cream 10g', 0, 3, 'TUB', 'ETC3', 17679, 23000, '0000-00-00', ''),
(609, '', 'Betadine Oint 10g', 2, 3, 'TUB', 'OTC3', 18854, 23000, '0000-00-00', ''),
(610, '', 'Tolak Angin Sugar Free', 0, 2, 'SCH', 'OTC2', 3098, 4000, '0000-00-00', ''),
(611, '', 'Lelap Strip 4\'s New', 11, 3, 'STRIP', 'HRB1', 10823, 25000, '0000-00-00', ''),
(612, '', 'Kapsida Tab 12\'s', 5, 1, 'BTL', 'HRB1', 17482, 20000, '0000-00-00', ''),
(613, '', 'Sanmol Forte Tablet', 18, 3, 'STRIP', 'OTC1', 1466, 3000, '0000-00-00', ''),
(614, '', 'Blackmores Vitamin C 500', 5, 2, 'BTL', 'OTC1', 113000, 170000, '0000-00-00', ''),
(615, '', 'Anvomer B6', 72, 30, 'TAB', 'ETC1', 2587, 4000, '0000-00-00', ''),
(616, '', 'Vitamam 1', 9, 3, 'STRIP', 'ETC1', 32340, 55000, '0000-00-00', ''),
(617, '', 'Vitamam 2', 4, 3, 'STRIP', 'ETC1', 25872, 45000, '0000-00-00', ''),
(618, '', 'Vitamam 3', 2, 3, 'STRIP', 'ETC1', 25872, 45000, '0000-00-00', ''),
(619, '', 'Duphaston 10mg', 3, 2, 'STRIP', 'ETC1', 352465, 460000, '0000-00-00', ''),
(620, '', 'Madu Angkak 290 g', 1, 2, 'BTL', 'HRB2', 55000, 65000, '0000-00-00', ''),
(621, '', 'Madu MG AN-NASR 30g', 2, 2, 'BTL', 'HRB2', 100000, 120000, '0000-00-00', ''),
(622, '', 'Madu Gemuk Sehat IQ 150g Asyifa', 2, 2, 'BTL', 'HRB2', 35000, 45000, '0000-00-00', ''),
(623, '', 'Addawa Sejati Sari Kutuk @50 caps', 0, 2, 'BTL', 'HRB1', 75000, 130000, '0000-00-00', ''),
(624, '', 'Madu DHA Ratu Lebah Junior 150g', 2, 2, 'BTL', 'HRB2', 25000, 50000, '0000-00-00', ''),
(625, '', 'Gemuk Badan Ananda 125g', 3, 2, 'BTL', 'HRB2', 60000, 80000, '0000-00-00', ''),
(626, '', 'Montalin', 21, 3, 'SCH', 'HRB1', 3500, 5000, '0000-00-00', ''),
(627, '', 'Otsu-D5 5% Glucose @500ml', 1, 3, 'BTL', 'ETC2', 10000, 20000, '0000-00-00', ''),
(628, '', 'Ratu Langsing 60\'s', 2, 3, 'BTL', 'HRB1', 65000, 75000, '0000-00-00', ''),
(629, '', 'Sulem Susut Lemak 60\'s', 4, 3, 'BTL', 'HRB1', 65000, 130000, '0000-00-00', ''),
(630, '', 'Hufagrip TMP Merah', 8, 3, 'BTL', 'OTC2', 14515, 18000, '0000-00-00', ''),
(631, '', 'Salbutamol Sulfat 2 mg', 80, 30, 'TAB', 'ETC1', 72, 150, '0000-00-00', ''),
(632, '', 'Madu TJ Murni 150 ml', 5, 2, 'BTL', 'OTC2', 17380, 20000, '0000-00-00', ''),
(633, '', 'Madu Rasa Jeruk Nipis 150 gr', 2, 2, 'BTL', 'OTC2', 10136, 15000, '0000-00-00', ''),
(634, '', 'Madu Rasa Original 150 gr', 3, 2, 'BTL', 'OTC2', 10136, 15000, '0000-00-00', ''),
(635, '', 'Madu TJ Jeruk 12\'s', 13, 5, 'SCH', 'OTC2', 803, 2000, '0000-00-00', ''),
(636, '', 'Madu TJ Original 12\'s', -1, 5, 'SCH', 'OTC2', 803, 2000, '0000-00-00', ''),
(637, '', 'Prenagen Lactamom 200 gr (all Variant)', 2, 1, 'BOX', 'PKRT', 39450, 45000, '0000-00-00', ''),
(638, '', 'Prenagen Lactamom 400 gr (all Variant)', 2, 1, 'BOX', 'PKRT', 72651, 78000, '0000-00-00', ''),
(639, '', 'OB Herbal Junior 60ml', 5, 2, 'BTL', 'HRB2', 12499, 17000, '0000-00-00', ''),
(640, '', 'OB Herbal 60 ml', 5, 2, 'BTL', 'HRB2', 12499, 17000, '0000-00-00', ''),
(641, '', 'Propepsa', 4, 3, 'BTL', 'ETC2', 0, 83000, '0000-00-00', ''),
(642, '', 'Prove D3 5000 IU', 20, 30, 'TAB', 'ETC1', 4000, 5000, '0000-00-00', ''),
(643, '', 'Sanmol Drop', 11, 3, 'BTL', 'OTC2', 16924, 21000, '0000-00-00', ''),
(644, '', 'Obat Gigi Cap Kakak Tua', 5, 3, 'BTL', 'OTC2', 11050, 15000, '0000-00-00', ''),
(645, '', 'Siladex DMP 60 ml (Kuning)', 8, 3, 'BTL', 'OTC2', 11859, 15000, '0000-00-00', ''),
(646, '', 'Siladex DMP 100 ml (kuning)', 6, 3, 'BTL', 'OTC2', 16302, 21000, '0000-00-00', ''),
(647, '', 'Siladex Expectorant 60 ml (Hijau)', 2, 3, 'BTL', 'OTC2', 11859, 16000, '0000-00-00', ''),
(648, '', 'Siladex Expectorant 100 ml (Hijau)', 3, 3, 'BTL', 'OTC2', 16302, 22000, '0000-00-00', ''),
(649, '', 'Termorex Baby 15 ml', 8, 2, 'BTL', 'OTC2', 17339, 25000, '0000-00-00', ''),
(650, '', 'Salep Ichtyol 15 gr', 3, 2, 'TUB', 'ETC3', 4583, 9000, '0000-00-00', ''),
(651, '', 'Cooling 5 Cool Mint', 5, 1, 'BTL', 'OTC2', 31680, 38000, '0000-00-00', ''),
(652, '', 'Bodrex Migra', 6, 5, 'STRIP', 'OTC1', 2156, 2500, '0000-00-00', ''),
(653, '', 'Atorvastatin 20mg 10\'s', 10, 30, 'TAB', 'ETC1', 0, 6000, '0000-00-00', ''),
(654, '', 'Omeprazole 20mg Ifars', 0, 30, 'TAB', 'ETC1', 272, 500, '0000-00-00', ''),
(655, '', 'Lambucid 60ml', 4, 3, 'BTL', 'OTC2', 8800, 11000, '0000-00-00', ''),
(656, '', 'Lambucid 100ml', 8, 3, 'BTL', 'OTC2', 11000, 14000, '0000-00-00', ''),
(657, '', 'Listerin Green Tea 100ml', 2, 3, 'BTL', 'PKRT', 8360, 11000, '0000-00-00', ''),
(658, '', 'Shen Nong Shi (Betadine China)', 7, 2, 'BTL', 'OTC2', 23100, 27000, '0000-00-00', ''),
(659, '', 'Waisan', 24, 3, 'SCH', 'OTC1', 525, 1000, '0000-00-00', ''),
(660, '', 'Imodium', 116, 30, 'TAB', 'ETC1', 8265, 10000, '0000-00-00', ''),
(661, '', 'VIT ZO NOVEL', 0, 30, 'TAB', 'ETC1', 7425, 15000, '0000-00-00', ''),
(662, '', 'Ibuprofen 400mg', 150, 30, 'TAB', 'ETC1', 528, 600, '0000-00-00', ''),
(663, '', 'Vitamin B Kompleks 100\'s', 0, 3, 'BTL', 'OTC1', 4730, 20000, '0000-00-00', ''),
(664, '', 'Larutan Cap Badak Botol 200ml', 8, 3, 'BTL', 'MNMK', 2959, 5000, '0000-00-00', ''),
(665, '', 'Blackmores Vit D3 1000 IU', 2, 3, 'BTL', 'OTC1', 167170, 175000, '0000-00-00', ''),
(666, '', 'Madu Angkak 475 g', 3, 1, 'BTL', 'HRB2', 75000, 90000, '0000-00-00', ''),
(667, '', 'Pi Kang Shuang Krim 5g', 0, 2, 'TUB', 'ETC3', 11000, 14000, '0000-00-00', ''),
(668, '', 'Redoxon Triple Action 20\'s', 5, 2, 'TUB', 'OTC1', 69698, 85000, '0000-00-00', ''),
(669, '', 'Xonce Orange Kaplet 6\'s', 12, 3, 'STRIP', 'OTC1', 4499, 10000, '0000-00-00', ''),
(670, '', 'Mylanta Cair 150ml', 7, 2, 'BTL', 'OTC2', 35606, 45000, '0000-00-00', ''),
(671, '', 'Lianhua Qingwen Capsules', 2, 1, 'BOX', 'HRB1', 70000, 95000, '0000-00-00', ''),
(672, '', 'Urogetix 100', 100, 30, 'TAB', 'ETC1', 6382, 8000, '0000-00-00', ''),
(673, '', 'Bodrexin Tab 6\'s', 4, 6, 'STRIP', 'OTC1', 667, 2000, '0000-00-00', ''),
(674, '', 'Imboost Tab', 10, 3, 'STRIP', 'OTC1', 35134, 40000, '0000-00-00', ''),
(675, '', 'Azithromycin 500 mg PT.LLOYD PHARMA', 20, 10, 'TAB', 'ETC1', 11000, 15000, '0000-00-00', ''),
(676, '', 'Ambroxol Sirup', 7, 2, 'BTL', 'ETC2', 7920, 11000, '0000-00-00', ''),
(677, '', 'Zegavit Tab', 11, 3, 'STRIP', 'OTC1', 19800, 27000, '0000-00-00', ''),
(678, '', 'Anakonidin 30ml (Merah)', 2, 3, 'BTL', 'OTC2', 6174, 8500, '0000-00-00', ''),
(679, '', 'Anakonidin 60ml (merah)', 4, 3, 'BTL', 'OTC2', 10214, 14000, '0000-00-00', ''),
(680, '', 'Anakonidin ME (ungu) 60ml', 8, 3, 'BTL', 'OTC2', 10214, 14000, '0000-00-00', ''),
(681, '', 'Herba Asimor', 12, 3, 'STRIP', 'HRB1', 11440, 20000, '0000-00-00', ''),
(682, '', 'Acyclovir Cream', 7, 3, 'TUB', 'ETC3', 2591, 9000, '0000-00-00', ''),
(683, '', 'Genoint Cream 0,3%', 5, 3, 'TUB', 'ETC3', 8000, 11000, '0000-00-00', ''),
(684, '', 'Imboost sirup kids 120ml', 3, 1, 'BTL', 'OTC2', 55770, 80000, '0000-00-00', ''),
(685, '', 'Neurobion 5000 injeksi @20psng', 9, 3, 'AMP', 'ETC4', 12036, 15000, '0000-00-00', ''),
(686, '', 'Komix Herbal Jahe 15ml @6sch', 0, 3, 'SCH', 'HRB2', 1383, 3000, '0000-00-00', ''),
(687, '', 'Komix Herbal Papermin 15ml @6sch', 0, 3, 'SCH', 'HRB2', 1383, 3000, '0000-00-00', ''),
(688, '', 'Sucralfat 100ml Combiphar', 6, 3, 'BTL', 'ETC2', 14375, 18000, '0000-00-00', ''),
(689, '', 'Inadryl Syr 100ml', 1, 3, 'BTL', 'OTC2', 45100, 53000, '0000-00-00', ''),
(690, '', 'For D 1000 IU @3strip', -3, 3, 'STRIP', 'OTC1', 22000, 30000, '0000-00-00', ''),
(691, '', 'Obat Batuk Ibu Dan Anak 150ml', 4, 3, 'BTL', 'HRB2', 40000, 50000, '0000-00-00', ''),
(692, '', 'Paramex SK @20strip', 8, 3, 'STRIP', 'OTC1', 3300, 5000, '0000-00-00', ''),
(693, '', 'Adem Sari Box @5sch', 1, 3, 'BOX', 'OTC1', 12320, 18000, '0000-00-00', ''),
(694, '', 'Herba KOF stick', 0, 3, 'STC', 'OTC2', 1183, 5000, '0000-00-00', ''),
(695, '', 'Herba KOF syr 60ml', 6, 3, 'BTL', 'OTC2', 11495, 17000, '0000-00-00', ''),
(696, '', 'Bodrexin Flu&Batuk syr 56ml', 4, 3, 'BTL', 'OTC2', 10500, 11500, '0000-00-00', ''),
(697, '', 'Sanaflu 4\'s tab', 18, 5, 'STRIP', 'OTC1', 1800, 2000, '0000-00-00', ''),
(698, '', 'Anakonidin ME (ungu) 30ml', 10, 3, 'BTL', 'OTC2', 6237, 8500, '0000-00-00', ''),
(699, '', 'Madu Lambung ASYIFA 280 g', 2, 1, 'BTL', 'HRB2', 48000, 52000, '0000-00-00', ''),
(700, '', 'Polysilane susp 180 ml', 4, 3, 'BTL', 'OTC2', 31900, 40000, '0000-00-00', ''),
(701, '', 'Neo Rheumacyl 30 g HANGAT (oranye)', 0, 1, 'TUB', 'OTC3', 12001, 15000, '0000-00-00', ''),
(702, '', 'Trovilon 60 ml', 3, 1, 'BTL', 'ETC2', 9740, 26000, '0000-00-00', ''),
(703, '', 'Procold Flu & Batuk 6\'s', 20, 3, 'STRIP', 'OTC1', 3250, 5000, '0000-00-00', ''),
(704, '', 'Decadryl 120 ml', 1, 3, 'BTL', 'OTC2', 17325, 19000, '0000-00-00', ''),
(705, '', 'Bufacort N cream', 7, 2, 'TUB', 'ETC3', 0, 18000, '0000-00-00', ''),
(706, '', 'Holisticare D3 1000 30\'s', 2, 1, 'BTL', 'OTC1', 43560, 65000, '0000-00-00', ''),
(707, '', 'OBH Nellco Spesial PE 55ml', 4, 3, 'BTL', 'OTC2', 16757, 35000, '0000-00-00', ''),
(708, '', 'OBH Nellco Spesial PE 100ml', 6, 3, 'BTL', 'OTC2', 23265, 48000, '0000-00-00', ''),
(709, '', 'Holisticare Ester C Kids 30\'s', 3, 2, 'BTL', 'OTC1', 35200, 45000, '0000-00-00', ''),
(710, '', 'Holisticare EFF Blackcurrant 10\'s', 3, 2, 'TUB', 'OTC1', 36812, 45000, '0000-00-00', ''),
(711, '', 'Zifagra Tab 6\'s', 30, 30, 'TAB', 'ETC1', 2908, 20000, '0000-00-00', ''),
(712, '', 'Buscopan Tab', 54, 30, 'TAB', 'ETC1', 3435, 4000, '0000-00-00', ''),
(713, '', 'Buscopan Plus Tab', 60, 30, 'TAB', 'ETC1', 4665, 5500, '0000-00-00', ''),
(714, '', 'Imunped Drops', 1, 1, 'BTL', 'OTC2', 52085, 60000, '0000-00-00', ''),
(715, '', 'Gom 8 ml', 4, 1, 'BTL', 'ETC2', 3250, 4000, '0000-00-00', ''),
(716, '', 'Neurobion Putih', 4, 3, 'STRIP', 'OTC1', 19501, 23000, '0000-00-00', ''),
(717, '', 'Mycoral cream', 3, 2, 'TUB', 'OTC3', 12884, 18000, '0000-00-00', ''),
(718, '', 'Oskadon 4\'s', 17, 3, 'STRIP', 'OTC1', 1710, 2000, '0000-00-00', ''),
(719, '', 'Miniaspi 10\'s', 140, 10, 'TAB', 'ETC1', 386, 500, '0000-00-00', ''),
(720, '', 'Dulcolactol Syr 60 ml', 3, 1, 'BTL', 'OTC2', 63612, 83000, '0000-00-00', ''),
(721, '', 'Ondansetron Injeksi 8mg Novell 5\'s', 3, 3, 'AMP', 'ETC4', 18110, 23000, '0000-00-00', ''),
(722, '', 'Ondansetron Injeksi 4 mg Nulab 5\'s', 0, 3, 'AMP', 'ETC4', 7700, 9000, '0000-00-00', ''),
(723, '', 'Methylprednisolone 8 mg Novell 10\'s', 190, 10, 'TAB', 'ETC1', 573, 800, '0000-00-00', ''),
(724, '', 'Kenshin Capsul', 3, 3, 'STRIP', 'HRB1', 10000, 12000, '0000-00-00', ''),
(725, '', 'Darsi', 4, 1, 'BOX', 'HRB1', 13500, 15000, '0000-00-00', ''),
(726, '', 'Viostin DS', 2, 3, 'STRIP', 'OTC1', 27500, 30000, '0000-00-00', ''),
(727, '', 'Tay Pin San', 5, 3, 'SCH', 'HRB1', 4166, 5000, '0000-00-00', ''),
(728, '', 'Siladex Cough & Cold 100 ml (biru)', 4, 3, 'BTL', 'OTC2', 16302, 22000, '0000-00-00', ''),
(729, '', 'Siladex Cough & Cold 60 ml (biru)', 9, 3, 'BTL', 'OTC2', 11859, 16000, '0000-00-00', ''),
(730, '', 'Vital Ear Drops 10 ml', 7, 2, 'BTL', 'OTC2', 13970, 16000, '0000-00-00', ''),
(731, '', 'Konicare Natural Strechmark', 2, 3, 'BTL', 'PKRT', 87120, 90000, '0000-00-00', ''),
(732, '', 'Konicare Bedak Biang Keringat 80 g', 0, 3, 'BTL', 'PKRT', 12456, 15000, '0000-00-00', ''),
(733, '', 'Ketoconazole 10gr', 7, 3, 'TUB', 'OTC3', 6650, 9000, '0000-00-00', ''),
(734, '', 'Obat Batuk Ibu Dan Anak 75ml', 2, 3, 'BTL', 'HRB2', 21725, 27000, '0000-00-00', ''),
(735, '', 'Cendo Xitrol 5ml', 2, 3, 'BTL', 'ETC2', 28476, 32000, '0000-00-00', ''),
(736, '', 'Erlamycetin Eye Drop', 2, 3, 'BTL', 'ETC2', 8170, 15000, '0000-00-00', ''),
(737, '', 'Caviplex Tab 10\'s', 12, 3, 'STRIP', 'OTC1', 6417, 10000, '0000-00-00', ''),
(738, '', 'Promag Herbal Gazero', 6, 3, 'SCH', 'OTC2', 1801, 2000, '0000-00-00', ''),
(739, '', 'Promag Suspensi 60ml', 6, 3, 'BTL', 'OTC2', 10749, 14000, '0000-00-00', ''),
(740, '', 'Shampoo Bayam', 0, 1, 'BTL', 'PKRT', 17626, 22000, '0000-00-00', ''),
(741, '', 'EM Kapsul Pelancar Haid 12\'s', 3, 1, 'BOX', 'HRB1', 12000, 17000, '0000-00-00', ''),
(742, '', 'Apolar N cream 10 g', 5, 3, 'TUB', 'ETC3', 39270, 47000, '0000-00-00', ''),
(743, '', 'Piroxicam 10', 130, 10, 'TAB', 'ETC1', 86, 200, '0000-00-00', ''),
(744, '', 'Piroxicam 20', 80, 10, 'TAB', 'ETC1', 107, 250, '0000-00-00', ''),
(745, '', 'Hydrocortisone Acetat cream 2.5% 5 gr', 4, 3, 'TUB', 'ETC3', 4125, 7000, '0000-00-00', ''),
(746, '', 'Meloxicam 15', 160, 10, 'TAB', 'ETC1', 300, 800, '0000-00-00', ''),
(747, '', 'Omegesic tab', 70, 10, 'TAB', 'ETC1', 341, 500, '0000-00-00', ''),
(748, '', 'Vitabumin Madu Ikan Gabus 130 ml', 3, 1, 'BTL', 'OTC2', 50909, 60000, '0000-00-00', ''),
(749, '', 'Plantacid Forte syr 100ml', 3, 3, 'BTL', 'OTC2', 29700, 36000, '0000-00-00', ''),
(750, '', 'Plantacid syr 100ml', 5, 3, 'BTL', 'OTC2', 10450, 12000, '0000-00-00', ''),
(751, '', 'Mycoral tab', 40, 10, 'TAB', 'ETC1', 4620, 5000, '0000-00-00', ''),
(752, '', 'Mapoh Tampek Cap Bola 5,5 gr', 2, 2, 'BTL', 'HRB1', 16750, 22000, '0000-00-00', ''),
(753, '', 'Salep Kulit Kuning Kembang Bulan 7g', 24, 3, 'POT', 'OTC3', 5388, 6500, '0000-00-00', ''),
(754, '', 'CTM Generik 100\'s', 4, 1, 'BTL', 'OTC1', 5000, 10000, '0000-00-00', ''),
(755, '', 'Vipcol SYR 60 ml', 0, 1, 'BTL', 'OTC2', 6000, 10000, '0000-00-00', ''),
(756, '', 'Lyscavit Syr 60 ml', 2, 3, 'BTL', 'OTC2', 4500, 6000, '0000-00-00', ''),
(757, '', 'Ifitamol PCT Syr 60 ml', 0, 3, 'BTL', 'OTC2', 4500, 6000, '0000-00-00', ''),
(758, '', 'Ramagesic PCT Syr 60 ml', 0, 3, 'BTL', 'OTC2', 4500, 6000, '0000-00-00', ''),
(759, '', 'Osteocare tab', 2, 1, 'STRIP', 'OTC1', 24871, 28000, '0000-00-00', ''),
(760, '', 'Siladex Antitussive 60 ml ( merah )', 3, 3, 'BTL', 'OTC2', 11592, 16000, '0000-00-00', ''),
(761, '', 'Paracetamol Generik 500 HOLI & NOVA', 20, 3, 'STRIP', 'OTC1', 1600, 3000, '0000-00-00', ''),
(762, '', 'Ondansetron 4 Generik MUTIFA', 70, 10, 'TAB', 'ETC1', 462, 1000, '0000-00-00', ''),
(763, '', 'Dexametason 0,5 Generik NOVA', 70, 10, 'TAB', 'ETC1', 59, 150, '0000-00-00', ''),
(764, '', 'Rhinos kapsul', -10, 10, 'KAPS', 'ETC1', 7865, 8500, '0000-00-00', ''),
(765, '', 'Sanadryl Syr 60ml', 8, 3, 'BTL', 'OTC2', 10890, 13000, '0000-00-00', ''),
(766, '', 'Kalpanax Cair 10ml', 2, 2, 'BTL', 'OTC2', 9583, 10000, '0000-00-00', ''),
(767, '', 'Obat Kurap 19 10ml', 13, 2, 'BTL', 'OTC2', 4658, 6000, '0000-00-00', ''),
(768, '', 'Renovit Glod 30\'s', 6, 3, 'BTL', 'OTC1', 79954, 95000, '0000-00-00', ''),
(769, '', 'Renovit 30\'s', 5, 3, 'BTL', 'OTC1', 69554, 83000, '0000-00-00', ''),
(770, '', 'Renovit Strip', 8, 3, 'STRIP', 'OTC1', 9801, 12000, '0000-00-00', ''),
(771, '', 'Dulcolax Tab 10\'s', 5, 3, 'BOX', 'OTC1', 15551, 18000, '0000-00-00', ''),
(772, '', 'Proris Tab', 5, 3, 'STRIP', 'OTC1', 8300, 10000, '0000-00-00', ''),
(773, '', 'Lactulax Syr', 4, 2, 'BTL', 'OTC2', 64790, 80000, '0000-00-00', ''),
(774, '', 'Interhistin tab', 70, 30, 'TAB', 'ETC1', 888, 1000, '0000-00-00', ''),
(775, '', 'Baycuten N Cream', 3, 3, 'TUB', 'ETC3', 58481, 70000, '0000-00-00', ''),
(776, '', 'Pacdin Biosepta 60ml', 2, 1, 'BTL', 'OTC3', 5280, 7000, '0000-00-00', ''),
(777, '', 'Bronchitin Expetoran Syr 60ml', 3, 3, 'BTL', 'OTC2', 6000, 10000, '0000-00-00', ''),
(778, '', 'Madu Hijau 290gr', 0, 1, 'BTL', 'OTC2', 108333, 130000, '0000-00-00', ''),
(779, '', 'Holisticare EFF Orange', 2, 1, 'BTL', 'OTC1', 37950, 45000, '0000-00-00', ''),
(780, '', 'Holisticare EFF Mango', 2, 1, 'BTL', 'OTC1', 37950, 45000, '0000-00-00', ''),
(781, '', 'Protecal Defense Effervescent (kuning)', 2, 3, 'TUB', 'OTC1', 30818, 38000, '0000-00-00', ''),
(782, '', 'Protecal Solid Effervescent (orange)', 2, 0, 'TUB', 'OTC1', 29974, 37000, '0000-00-00', ''),
(783, '', 'Protecal Osteo Effervescent (biru)', 1, 3, 'TUB', 'OTC1', 32016, 40000, '0000-00-00', ''),
(784, '', 'Fungiderm 5gr', 7, 3, 'TUB', 'OTC3', 11731, 17000, '0000-00-00', ''),
(785, '', 'Inzana Tab', 0, 3, 'STRIP', 'OTC1', 849, 1500, '0000-00-00', ''),
(786, '', 'Krim 88', 3, 2, 'TUB', 'OTC3', 11250, 14000, '0000-00-00', ''),
(787, '', 'Silex Syr 100ml', 2, 3, 'BTL', 'OTC2', 62990, 78000, '0000-00-00', ''),
(788, '', 'Molexflu', 6, 3, 'STRIP', 'OTC1', 4110, 5000, '0000-00-00', ''),
(789, '', 'Dextral Tab', 0, 3, 'STRIP', 'OTC1', 5294, 7000, '0000-00-00', ''),
(790, '', 'Vitamin C Sidomuncul Orange 6\'s', 2, 2, 'BOX', 'OTC1', 7579, 20000, '0000-00-00', ''),
(791, '', 'Biogesic Tab', 4, 2, 'STRIP', 'OTC1', 1742, 2000, '0000-00-00', ''),
(792, '', 'Komix Herbal Lemon 15 ml tube botol', 4, 2, 'TUB', 'HRB1', 8000, 10000, '0000-00-00', ''),
(793, '', 'Madu TJ Joybee Original 100 ml', 4, 2, 'BTL', 'OTC2', 10375, 15000, '0000-00-00', ''),
(794, '', 'Madu TJ Joybee Orange 100 ml', 5, 2, 'BTL', 'OTC2', 10375, 15000, '0000-00-00', ''),
(795, '', 'Inadryl 50 ml', 3, 1, 'BTL', 'OTC2', 25987, 32000, '0000-00-00', ''),
(796, '', 'Tonikum bayer 100ml', 0, 1, 'BTL', 'OTC2', 12837, 16000, '0000-00-00', ''),
(797, '', 'Biolysin Kids Blackcurrant 30\'s', 8, 1, 'BTL', 'OTC2', 12003, 15000, '0000-00-00', ''),
(798, '', 'Biolysin Syrup 60ml', 1, 1, 'BTL', 'OTC2', 12003, 15000, '0000-00-00', ''),
(799, '', 'Iliadin Kinder Drop 0,25% 10ml', 2, 1, 'BTL', 'OTC2', 65220, 84000, '0000-00-00', ''),
(800, '', 'Pedialyte sol 500ml', 1, 1, 'BTL', 'OTC2', 31680, 41000, '0000-00-00', ''),
(801, '', 'Vitamin C 25mg 250\'s', 5, 2, 'BTL', 'OTC1', 20229, 26000, '0000-00-00', ''),
(802, '', 'Andalan FE', 4, 2, 'STRIP', 'ETC1', 6893, 8000, '0000-00-00', ''),
(803, '', 'Madu Hitam 470g konsinasi', 3, 1, 'BTL', 'KONS', 38000, 55000, '0000-00-00', ''),
(804, '', 'DB MIX 310g konsinasi', 2, 1, 'BTL', 'KONS', 22000, 35000, '0000-00-00', ''),
(805, '', 'Obat Gigi Cap Kutilang konsinasi', 0, 1, 'BTL', 'KONS', 7500, 10000, '0000-00-00', ''),
(806, '', 'Tremenza Syr 60ml', 3, 3, 'BTL', 'OTC2', 22550, 28500, '0000-00-00', ''),
(807, '', 'Paket CDR', 10, 3, 'STRIP', 'OTC1', 0, 0, '0000-00-00', ''),
(808, '', 'Paket Redoxon', 15, 3, 'STRIP', 'OTC1', 0, 0, '0000-00-00', ''),
(809, '', 'Paramex Flu dan Batuk', 0, 2, 'STRIP', 'OTC1', 1981, 2500, '0000-00-00', ''),
(810, '', 'Jescool Lemon Tea', 5, 2, 'SCH', 'OTC1', 1633, 2500, '0000-00-00', ''),
(811, '', 'OBH Combi Batuk Berdahak 100ml', 7, 3, 'BTL', 'OTC2', 11103, 14000, '0000-00-00', ''),
(812, '', 'Paramex Nyeri Otot', 8, 3, 'STRIP', 'OTC1', 1667, 2000, '0000-00-00', ''),
(813, '', 'Promo SGM Eksplor 3plus Vanila', 0, 0, 'BOX', 'SUSU', 37000, 37000, '0000-00-00', ''),
(814, '', 'Promo SGM Eksplor 3plus Madu', 0, 0, 'BOX', 'SUSU', 37000, 37000, '0000-00-00', ''),
(815, '', 'Cefixime 100', 130, 30, 'KAPS', 'ETC1', 866, 2000, '0000-00-00', ''),
(816, '', 'Feminax (Pereda Nyeri Haid)', 3, 3, 'STRIP', 'OTC1', 2613, 3500, '0000-00-00', ''),
(817, '', 'Stop Cold Dragee', 0, 3, 'STRIP', 'OTC1', 2589, 3000, '0000-00-00', ''),
(818, '', 'Rohto Cool 7ml', 4, 3, 'BTL', 'OTC2', 14000, 19000, '0000-00-00', ''),
(819, '', 'IPI Vitamin B1', 3, 3, 'BTL', 'OTC1', 6000, 10000, '0000-00-00', ''),
(820, '', 'IPI Vitamin B12', 4, 3, 'BTL', 'OTC1', 6000, 10000, '0000-00-00', ''),
(821, '', 'Ever E 30s', 2, 2, 'BTL', 'OTC1', 58141, 75000, '0000-00-00', ''),
(822, '', 'Safe Care', 0, 3, 'BTL', 'OTC3', 13994, 17000, '0000-00-00', ''),
(823, '', 'Vegebland Junior', 0, 2, 'BTL', 'OTC1', 116000, 135000, '0000-00-00', ''),
(824, '', 'Pilkita', 17, 3, 'STRIP', 'OTC1', 1681, 2000, '0000-00-00', ''),
(825, '', 'Rohto Dry Fresh 7ml', 4, 3, 'BTL', 'OTC2', 15345, 19000, '0000-00-00', ''),
(826, '', 'Lactacyd Woman 60 ml', 6, 1, 'BTL', 'OTC3', 24675, 30000, '0000-00-00', ''),
(827, '', 'Minyak Angin Lang 6 ml', 3, 1, 'BTL', 'OTC3', 9425, 12000, '0000-00-00', ''),
(828, '', 'Minyak Angin Lang 12 ml', 4, 1, 'BTL', 'OTC3', 17248, 22000, '0000-00-00', ''),
(829, '', 'IPI Vitamin B Complex', 2, 1, 'PCS', 'OTC1', 4500, 8000, '0000-00-00', ''),
(830, '', 'IPI Vitamin A', 4, 1, 'PCS', 'OTC1', 4708, 8000, '0000-00-00', ''),
(831, '', 'Vitamin B6 10 mg Kimia Farma', 5, 1, 'STRIP', 'ETC1', 2000, 2500, '0000-00-00', ''),
(832, '', 'Ferro K Drop', 0, 1, 'BTL', 'OTC2', 30723, 45000, '0000-00-00', ''),
(833, '', 'Ferro K Susp 60 ml', 1, 1, 'BTL', 'OTC2', 37730, 51000, '0000-00-00', ''),
(834, '', 'Ataroc tab', 40, 10, 'TAB', 'ETC1', 2323, 3500, '0000-00-00', ''),
(835, '', 'Ataroc Syr 60 ml', 0, 1, 'BTL', 'ETC2', 50127, 68000, '0000-00-00', ''),
(836, '', 'Panadol Chewable Anak', 5, 3, 'STRIP', 'OTC1', 12136, 14000, '0000-00-00', ''),
(837, '', 'Tempra Forte Syr 60 ml', 7, 3, 'BTL', 'OTC2', 43766, 52000, '0000-00-00', ''),
(838, '', 'Bromifar Plus Sirup 60ml', 4, 3, 'BTL', 'ETC2', 4350, 14000, '0000-00-00', ''),
(839, '', 'Norvom Sirup 60ml', 1, 3, 'BTL', 'ETC2', 4466, 13000, '0000-00-00', ''),
(840, '', 'Siladex Antitussive 100ml (Merah)', 6, 3, 'BTL', 'OTC2', 15215, 19000, '0000-00-00', ''),
(841, '', 'Cinolon N Cream 5gr', 4, 3, 'TUB', 'ETC3', 13200, 16000, '0000-00-00', ''),
(842, '', 'Fruit Jr 18', 0, 2, 'BTL', 'OTC1', 105512, 135000, '0000-00-00', ''),
(843, '', 'Benzolac 2,5% Obat Jerawat 5gr', 0, 3, 'TUB', 'ETC3', 14685, 18000, '0000-00-00', ''),
(844, '', 'Nutrisalin Garam Diet 200gr', 1, 1, 'BTL', 'HRB1', 43609, 56000, '0000-00-00', ''),
(845, '', 'Obat Sakit Tenggorokan - Hua Feng Yao San', 1, 1, 'BTL', 'HRB1', 4535, 8000, '0000-00-00', ''),
(846, '', 'Aspilet', 60, 30, 'TAB', 'ETC1', 610, 1000, '0000-00-00', ''),
(847, '', 'Laserin Madu 60 ml', 3, 3, 'BTL', 'OTC2', 10889, 14000, '0000-00-00', ''),
(848, '', 'Madu Hijau Innature 350g', 6, 2, 'BTL', 'HRB2', 140000, 180000, '0000-00-00', ''),
(849, '', 'Oralit', 72, 3, 'SCH', 'OTC1', 875, 1000, '0000-00-00', ''),
(850, '', 'Laxadine Syr 60ml', 1, 3, 'BTL', 'OTC2', 46200, 55000, '0000-00-00', ''),
(851, '', 'Flutrop Tab', 9, 3, 'STRIP', 'OTC1', 7700, 10000, '0000-00-00', ''),
(852, '', 'Flutamol Tab', 14, 3, 'STRIP', 'OTC1', 3850, 6000, '0000-00-00', ''),
(853, '', 'Minyak Ikan Salmon Squalene 100\'s', 8, 1, 'BTL', 'OTC2', 16225, 35000, '0000-00-00', ''),
(854, '', 'Madu Balita 50 g konsinasi', 3, 1, 'BTL', 'OTC2', 12000, 16000, '0000-00-00', ''),
(855, '', 'Fermino 12\'s kecil', 0, 1, 'BTL', 'OTC1', 20000, 24000, '0000-00-00', ''),
(856, '', 'Madu Batuk Pilek Asyifa 165 g konsinasi', 1, 1, 'BTL', 'HRB2', 32000, 40000, '0000-00-00', ''),
(857, '', '?', 0, 0, 'STRIP', 'OTC1', 0, 0, '0000-00-00', ''),
(858, '', 'Albumin Ikan Gabus 60\'s', 5, 1, 'BTL', 'OTC1', 15510, 30000, '0000-00-00', ''),
(859, '', 'Dexamethason Injeksi', 94, 3, 'AMP', 'ETC4', 2000, 6000, '0000-00-00', ''),
(860, '', 'Sumagesic Tab 4\'s', 23, 3, 'STRIP', 'OTC1', 2500, 3000, '0000-00-00', ''),
(861, '', 'Amoxsan Tab', 90, 30, 'TAB', 'ETC1', 3344, 4500, '0000-00-00', ''),
(862, '', 'Contrexyn tab', 20, 3, 'STRIP', 'OTC1', 789, 1000, '0000-00-00', ''),
(863, '', 'Anacetin syr 60 ml', 7, 3, 'BTL', 'OTC2', 7782, 12000, '0000-00-00', ''),
(864, '', 'Antasida Doen syr 50 ml', 10, 2, 'BTL', 'OTC2', 3718, 5000, '0000-00-00', ''),
(865, '', 'Caladine Ori Powder 35 gr', 4, 1, 'BTL', 'PKRT', 4000, 5000, '0000-00-00', ''),
(866, '', 'Saridon tab 4\'s', 10, 2, 'STRIP', 'OTC1', 3473, 4000, '0000-00-00', ''),
(867, '', 'Sangobion Vita Tonik Kids 100ml', 2, 1, 'BTL', 'OTC2', 32699, 40000, '0000-00-00', ''),
(868, '', 'Calcusol 30?s', 2, 1, 'BTL', 'OTC1', 37500, 43000, '0000-00-00', ''),
(869, '', 'Lycalvit 60 ml', 5, 1, 'BTL', 'OTC2', 45650, 50000, '0000-00-00', ''),
(870, '', 'Decadryl 60 ml', 0, 1, 'BTL', 'OTC2', 12622, 17000, '0000-00-00', ''),
(871, '', 'Benoson N cream 5 gr', 5, 2, 'TUB', 'ETC3', 18499, 23000, '0000-00-00', ''),
(872, '', 'Dumin tab 10\'s', 10, 3, 'STRIP', 'OTC1', 5780, 7000, '0000-00-00', ''),
(873, '', 'SGM BBLR 400g', 1, 1, 'BOX', 'SUSU', 71815, 85000, '0000-00-00', ''),
(874, '', 'SGM LLM 400g', 2, 1, 'BOX', 'SUSU', 65950, 75000, '0000-00-00', ''),
(875, '', 'Miconazole Cream 10gr', 0, 3, 'TUB', 'OTC3', 3135, 5000, '0000-00-00', ''),
(876, '', 'SGM Ananda 400g 6-12bulan', 4, 1, 'BOX', 'SUSU', 37400, 43000, '0000-00-00', ''),
(877, '', 'Larutan Cap Kaki Tiga Botol 320ml (Semua Rasa)', 6, 2, 'BTL', 'MNMK', 6000, 7000, '0000-00-00', ''),
(878, '', 'Larutan Cap Kaki Tiga Klg 320ml (Semua Rasa)', 7, 2, 'KLG', 'MNMK', 4116, 6000, '0000-00-00', ''),
(879, '', 'Larutan Cap Kaki Tiga Botol 500ml', 5, 2, 'BTL', 'MNMK', 5250, 7000, '0000-00-00', ''),
(880, '', 'Larutan Cap Kaki Tiga Klg Anak 250ml', 5, 2, 'KLG', 'MNMK', 5212, 7000, '0000-00-00', ''),
(881, '', 'Larutan Cap Kaki Tiga Botol 200ml', 6, 2, 'KLG', 'MNMK', 2650, 5000, '0000-00-00', ''),
(882, '', 'Blackmores Fish Oil 1000', 1, 1, 'BTL', 'OTC2', 79999, 100000, '0000-00-00', ''),
(883, '', 'Hydro Coco Vita-D 330ml (konsinasi)', 0, 1, 'PCS', 'MNMK', 7847, 10000, '0000-00-00', ''),
(884, '', 'Intrasol Plat.Vanila 800g (konsinasi)', 0, 1, 'KLG', 'OTC4', 198670, 220000, '0000-00-00', ''),
(885, '', 'Lipitor 20 mg', 30, 10, 'TAB', 'ETC1', 21318, 26000, '0000-00-00', ''),
(886, '', 'Tremenza tab', 90, 10, 'TAB', 'ETC1', 1600, 2000, '0000-00-00', ''),
(887, '', 'New Astar cream 15 g', 5, 2, 'TUB', 'OTC3', 8222, 15000, '0000-00-00', ''),
(888, '', 'Bufacaryl tab', 90, 20, 'TAB', 'ETC1', 181, 400, '0000-00-00', ''),
(889, '', 'Cetirizine sirup 100 ml', 4, 3, 'BTL', 'ETC2', 9498, 18000, '0000-00-00', ''),
(890, '', 'Gold G Sea Cucumber 320ml (konsinasi)', 3, 1, 'BTL', 'OTC4', 90000, 130000, '0000-00-00', ''),
(891, '', 'Olimex Ikan Gabus 60\'s (konsinasi)', 2, 1, 'BTL', 'HRB1', 100000, 150000, '0000-00-00', ''),
(892, '', 'Licokalk 500mg', 10, 3, 'STRIP', 'OTC1', 2134, 3000, '0000-00-00', ''),
(893, '', 'Bevalex Cream 5 g', 5, 2, 'TUB', 'ETC3', 9831, 15000, '0000-00-00', ''),
(894, '', 'Mucos Sirup 60ml', 0, 3, 'BTL', 'ETC2', 11844, 20000, '0000-00-00', ''),
(895, '', 'Lambucid Tab', 0, 3, 'STRIP', 'OTC1', 2541, 3000, '0000-00-00', ''),
(896, '', 'Neurosanbe tab', 0, 3, 'STRIP', 'OTC1', 12487, 16000, '0000-00-00', ''),
(897, '', 'Hemaviton Stamina Plus', 0, 3, 'STRIP', 'OTC1', 5724, 7000, '0000-00-00', ''),
(898, '', 'Teh  Javana', 0, 0, 'BTL', 'MNMK', 2042, 3000, '0000-00-00', ''),
(899, '', 'You C 1000 Orange 140 ml', 7, 5, 'BTL', 'MNMK', 8333, 15000, '0000-00-00', ''),
(900, '', 'Indomilk susu coklat 190 ml', 0, 3, 'BTL', 'MNMK', 3125, 4500, '0000-00-00', ''),
(901, '', 'Sprite pet', 4, 3, 'BTL', 'MNMK', 3708, 5000, '0000-00-00', ''),
(902, '', 'Fanta Pet', 0, 3, 'BTL', 'MNMK', 3708, 5000, '0000-00-00', ''),
(903, '', 'Magastrol Kecil', 0, 3, 'BTL', 'HRB2', 27000, 99000, '0000-00-00', ''),
(904, '', 'Magastrol Besar', 0, 2, 'BTL', 'HRB2', 55000, 140000, '0000-00-00', ''),
(905, '', 'Nutridat', -2, 3, 'BTL', 'HRB2', 25000, 99000, '0000-00-00', ''),
(906, '', 'Sarang Insulin', 0, 3, 'KLG', 'HRB2', 25000, 99000, '0000-00-00', ''),
(907, '', 'Licasma', -3, 3, 'BTL', 'HRB2', 27000, 99000, '0000-00-00', ''),
(908, '', 'Madu Deep Sleep', 0, 3, 'BTL', 'HRB2', 60000, 170000, '0000-00-00', ''),
(909, '', 'My Baby minyak Telon Plus Eucalyptus 57ml & 60ml', 0, 3, 'BTL', 'OTC2', 14488, 18000, '0000-00-00', ''),
(910, '', 'Herocyn Baby 100g', 3, 1, 'BTL', 'OTC3', 7492, 10000, '0000-00-00', ''),
(911, '', 'Herocyn Baby 200g', 4, 1, 'BTL', 'OTC3', 12074, 15000, '0000-00-00', ''),
(912, '', 'Polident Fresh Mint 60g', 2, 0, 'TUB', 'ALK', 60643, 75000, '0000-00-00', ''),
(913, '', 'Polident Flavour Free 20g', 0, 1, 'TUB', 'OTC1', 27656, 37000, '0000-00-00', ''),
(914, '', 'hot in cream tube 120gr Merah', 1, 2, 'TUB', 'OTC3', 19200, 22000, '0000-00-00', ''),
(915, '', 'salep 88 6gr', 9, 2, 'TUB', 'OTC3', 9000, 11000, '0000-00-00', ''),
(916, '', 'hot in cream tube 60gr Merah', 4, 2, 'TUB', 'OTC3', 11500, 15000, '0000-00-00', ''),
(917, '', 'Minyak Angin Cap Kapak 5 ml', 3, 2, 'BTL', 'OTC3', 8700, 10000, '0000-00-00', ''),
(918, '', 'Chiliplast plester Besar', 5, 0, 'UNT', 'ALK', 8000, 10000, '0000-00-00', ''),
(919, '', 'My Baby Minyak Telon Plus Eucalyptus 90ml', 5, 3, 'BTL', 'OTC2', 22000, 24000, '0000-00-00', ''),
(920, '', 'Hansaplast', 73, 20, 'STRIP', 'ALK', 250, 500, '0000-00-00', ''),
(921, '', 'Indomilk kids 115 ml', 0, 3, 'BTL', 'MNMK', 2500, 3000, '0000-00-00', ''),
(922, '', 'Strepsile Vit C 8\'s', 0, 2, 'SCH', 'OTC1', 11600, 15000, '0000-00-00', ''),
(923, '', 'Frozz Cerry Mint', 0, 3, 'UNT', 'OTC1', 7775, 10000, '0000-00-00', ''),
(924, '', 'Pulpy Orange', 5, 3, 'BTL', 'MNMK', 3750, 4500, '0000-00-00', ''),
(925, '', 'Frozz Rasa Mint', 0, 0, 'UNT', 'OTC1', 7725, 10000, '0000-00-00', ''),
(926, '', 'Frozz Blackberry Mint', 0, 0, 'UNT', 'OTC1', 7725, 10000, '0000-00-00', ''),
(927, '', 'Frozz Barley Mint', 0, 0, 'UNT', 'OTC1', 7725, 10000, '0000-00-00', ''),
(928, '', 'Happydent Tutti Frutti', 1, 2, 'UNT', 'OTC1', 6725, 9000, '0000-00-00', ''),
(929, '', 'Teh Pucuk', 0, 3, 'BTL', 'MNMK', 2104, 3500, '0000-00-00', ''),
(930, '', 'Active Water', 0, 2, 'BTL', 'MNMK', 4356, 6000, '0000-00-00', ''),
(931, '', 'Gofress - Permen Kertas', 0, 3, 'UNT', 'OTC1', 6921, 8500, '0000-00-00', ''),
(932, '', 'Renadinac 50 mg', 0, 30, 'TAB', 'ETC1', 270, 500, '0000-00-00', ''),
(933, '', 'Candesartan 8 OGB Dexa', 50, 30, 'TAB', 'ETC1', 4455, 6000, '0000-00-00', ''),
(934, '', 'Candesartan 16 OGB Dexa', 30, 30, 'TAB', 'ETC1', 7150, 9000, '0000-00-00', ''),
(935, '', 'Minyak Angin Cap Kapak 14 ml', 3, 2, 'BTL', 'OTC3', 0, 0, '0000-00-00', ''),
(936, '', 'Visancort Cream', 5, 3, 'TUB', 'ETC3', 13690, 17000, '0000-00-00', ''),
(937, '', 'Minyak Kayu Putih Sidola 60ml', 6, 2, 'BTL', 'PKRT', 26135, 32000, '0000-00-00', ''),
(938, '', 'Nebacetin Ointment 5g', 5, 3, 'TUB', 'ETC3', 20083, 26000, '0000-00-00', ''),
(939, '', 'Kenalog Orabase 5gr', 5, 3, 'TUB', 'ETC3', 63250, 76000, '0000-00-00', ''),
(940, '', 'Norvom Kaplet', 0, 30, 'TAB', 'ETC1', 154, 400, '0000-00-00', ''),
(941, '', 'Happydent Rasa Mint', 0, 2, 'UNT', 'OTC1', 6725, 9000, '0000-00-00', ''),
(942, '', 'Strepsil Honey & Lemon', 0, 3, 'SCH', 'OTC1', 11600, 15000, '0000-00-00', ''),
(943, '', 'Strepsil Menthol', 0, 3, 'SCH', 'OTC1', 11600, 15000, '0000-00-00', ''),
(944, '', 'Zwitsal Baby Shampo 100ml', 1, 3, 'BTL', 'PKRT', 10983, 12000, '0000-00-00', ''),
(945, '', 'Johnsons Baby Powder 100gr (putih)', 3, 3, 'BTL', 'PKRT', 7600, 10000, '0000-00-00', ''),
(946, '', 'Johnsons hair & body baby bath 400ml', 2, 3, 'UNT', 'PKRT', 30335, 35000, '0000-00-00', ''),
(947, '', 'Zwitsal Baby Milky Bath 450ml', 0, 3, 'UNT', 'PKRT', 33320, 35000, '0000-00-00', ''),
(948, '', 'Sweety Baby Wipes Chamomile', 0, 4, 'UNT', 'ALK', 8860, 10000, '0000-00-00', ''),
(949, '', 'Sweety Baby Wipes Green Tea', 0, 4, 'UNT', 'ALK', 8860, 10000, '0000-00-00', ''),
(950, '', 'Mitu Baby Wipes Antiseptic', 0, 2, 'UNT', 'ALK', 8100, 10000, '0000-00-00', ''),
(951, '', 'Mitu Baby Wipes Ganti Popok 50\'s Playful Fressia', 0, 2, 'UNT', 'ALK', 8100, 10000, '0000-00-00', ''),
(952, '', 'Cussons Baby Wipes Cucumber Extract', 0, 2, 'UNT', 'PKRT', 9462, 12000, '0000-00-00', ''),
(953, '', 'Cussons Baby Wipes Bluverry&Vit.E', 0, 2, 'UNT', 'PKRT', 9465, 12000, '0000-00-00', ''),
(954, '', 'Kodomo Bodywash Strawberry 200ml', 1, 2, 'BTL', 'PKRT', 13285, 15000, '0000-00-00', ''),
(955, '', 'Kodomo Shp&Cond Blueberry 200ml', 0, 2, 'BTL', 'PKRT', 13285, 15000, '0000-00-00', ''),
(956, '', 'Kodomo Shp&Cond Orange 200ml', 0, 2, 'BTL', 'PKRT', 13285, 15000, '0000-00-00', ''),
(957, '', 'Milagros', 0, 3, 'BTL', 'OTC2', 37500, 40000, '0000-00-00', ''),
(958, '', 'Coca Cola pet', 0, 3, 'BTL', 'MNMK', 3625, 5000, '0000-00-00', ''),
(959, '', 'Asepso Clean 80gr', 4, 2, 'UNT', 'OTC3', 5700, 10000, '0000-00-00', ''),
(960, '', 'Asepso Fresh 80gr', 5, 2, 'UNT', 'OTC3', 5700, 10000, '0000-00-00', ''),
(961, '', 'Asepso Soap 80gr', 4, 2, 'UNT', 'OTC3', 5700, 10000, '0000-00-00', ''),
(962, '', 'Asepso Sulphur 80gr', 0, 2, 'UNT', 'OTC3', 5700, 10000, '0000-00-00', ''),
(963, '', 'Cussons baby Shp A&H (Orange) 100ml', 1, 0, 'BTL', 'PKRT', 10755, 13000, '0000-00-00', ''),
(964, '', 'Cussons baby Shp CO&A (Hijau) 100ml', 0, 0, 'BTL', 'PKRT', 10755, 13000, '0000-00-00', ''),
(965, '', 'Cussons baby Oil S&S 100ml (Pink)', 3, 0, 'BTL', 'PKRT', 16875, 20000, '0000-00-00', ''),
(966, '', 'Cussons baby Oil Nat 100ml (Hijau)', 1, 0, 'BTL', 'PKRT', 16875, 20000, '0000-00-00', ''),
(967, '', 'Cussons baby Oil M&G 100ml (Biru)', 1, 0, 'BTL', 'PKRT', 16875, 20000, '0000-00-00', ''),
(968, '', 'Cussons Soap 75 S&S (Pink)', 4, 0, 'UNT', 'PKRT', 3100, 4500, '0000-00-00', ''),
(969, '', 'Cussons Soap 75 M&G', 2, 0, 'UNT', 'PKRT', 3100, 4500, '0000-00-00', ''),
(970, '', 'My Baby Soap 75g SG(Biru)', 1, 0, 'UNT', 'PKRT', 3520, 4500, '0000-00-00', ''),
(971, '', 'My Baby Soap 60 SG (Biru)', 1, 0, 'UNT', 'PKRT', 3170, 4000, '0000-00-00', ''),
(972, '', 'My Baby Soap 60g SF (Pink)', 1, 0, 'UNT', 'PKRT', 3170, 4000, '0000-00-00', ''),
(973, '', 'Cussons Baby Shp CN&C  (Ungu) 100ml', 0, 1, 'BTL', 'PKRT', 10755, 13000, '0000-00-00', ''),
(974, '', 'Yakult', -1, 5, 'PCS', 'MNMK', 1530, 2000, '0000-00-00', ''),
(975, '', 'Ultra Milk Cokelat 200ml', 0, 5, 'PCS', 'MNMK', 3791, 5000, '0000-00-00', ''),
(976, '', 'Ultra Milk Full Cream 200ml', 0, 5, 'PCS', 'MNMK', 3875, 5000, '0000-00-00', ''),
(977, '', 'Pocari Sweat 350ml', 22, 5, 'BTL', 'MNMK', 4708, 6000, '0000-00-00', ''),
(978, '', 'Kodomo Powder 50gr', 0, 0, 'BTL', 'PKRT', 2500, 8000, '0000-00-00', ''),
(979, '', 'Snow me Cotton Buds', 0, 1, 'PCS', 'ALK', 708, 1000, '0000-00-00', ''),
(980, '', 'Sikat Gigi Kodomo', 10, 0, 'UNT', 'ALK', 3000, 4500, '0000-00-00', ''),
(981, '', 'Pasta Gigi Kodomo', 7, 0, 'UNT', 'ALK', 4500, 6000, '0000-00-00', ''),
(982, '', 'Sikat Gigi Formula SLVR', 21, 0, 'UNT', 'ALK', 2750, 3500, '0000-00-00', ''),
(983, '', 'Mitu Baby Wipes Ganti Popok 50\'s Lively Vanilla', 0, 1, 'PCS', 'PKRT', 7925, 10000, '0000-00-00', ''),
(984, '', 'Paseo Wipes Jojoba Oil 50\'s', 4, 1, 'PCS', 'PKRT', 9220, 11000, '0000-00-00', ''),
(985, '', 'Paseo Wipes Chamomile 50\'s', 5, 1, 'PCS', 'PKRT', 9220, 11000, '0000-00-00', ''),
(986, '', 'dee-dee shp grape 250ml', 0, 1, 'BTL', 'PKRT', 16950, 18000, '0000-00-00', ''),
(987, '', 'dee-dee shp orange 250ml', 1, 1, 'BTL', 'PKRT', 16950, 18000, '0000-00-00', ''),
(988, '', 'dee-dee shp apple 250ml', 1, 1, 'BTL', 'PKRT', 16950, 18000, '0000-00-00', ''),
(989, '', 'dee-dee shp strawberry 250ml', 0, 1, 'BTL', 'PKRT', 16950, 18000, '0000-00-00', ''),
(990, '', 'Madu Rasa Original 12\'s', 12, 3, 'SCH', 'OTC2', 940, 2000, '0000-00-00', ''),
(991, '', 'Madu Rasa Jeruk Nipis 12\'s', 8, 3, 'SCH', 'OTC2', 940, 2000, '0000-00-00', ''),
(992, '', 'Bedak Happy Talc 150gr', 6, 1, 'POT', 'PKRT', 8810, 12000, '0000-00-00', ''),
(993, '', 'Johnson\'s baby oil 50ml (pink)', 0, 1, 'BTL', 'PKRT', 11235, 15000, '0000-00-00', ''),
(994, '', 'Johnson\'s baby hair&body 200ml (kuning)', 0, 0, 'BTL', 'PKRT', 18015, 25000, '0000-00-00', ''),
(995, '', 'Johnson\'s baby hair&body 100ml (kuning)', 1, 1, 'BTL', 'PKRT', 10680, 15000, '0000-00-00', ''),
(996, '', 'Prima 600ml', 4, 3, 'BTL', 'MNMK', 1291, 3000, '0000-00-00', ''),
(997, '', 'Nano nano rasa kulit jeruk', 0, 3, 'SCH', 'OTC1', 1434, 2000, '0000-00-00', ''),
(998, '', 'MBK Powder', 0, 3, 'SCH', 'OTC3', 2019, 2500, '0000-00-00', ''),
(999, '', 'Tropicana Slim Classic', 0, 0, 'BOX', 'MNMK', 38500, 45000, '0000-00-00', ''),
(1000, '', 'Minyak angin cap ayam 12ml', 8, 3, 'BTL', 'OTC3', 5500, 7000, '0000-00-00', ''),
(1001, '', 'Minyak angin cap ayam 25ml', 5, 3, 'BTL', 'OTC3', 9790, 13000, '0000-00-00', ''),
(1002, '', 'Minyak angin cap ayam 40ml', 0, 3, 'BTL', 'OTC3', 15125, 19000, '0000-00-00', ''),
(1003, '', 'Dettol cair antiseptic 45ml', 5, 1, 'BTL', 'PKRT', 7816, 12000, '0000-00-00', ''),
(1004, '', 'Dettol cair antiseptic 95ml', 4, 1, 'BTL', 'PKRT', 16221, 24000, '0000-00-00', ''),
(1005, '', 'Hexos mint', 0, 3, 'SCH', 'OTC1', 1400, 2000, '0000-00-00', ''),
(1006, '', 'Konicare Minyak Kayu Putih 60ml', 3, 1, 'BTL', 'PKRT', 18079, 22000, '0000-00-00', ''),
(1007, '', 'Konicare Minyak Kayu Putih Plus 60ml', 2, 1, 'BTL', 'PKRT', 20754, 25000, '0000-00-00', ''),
(1008, '', 'Konicare Minyak Telon 60ml', 1, 1, 'BTL', 'PKRT', 18079, 22000, '0000-00-00', ''),
(1009, '', 'Konicare Minyak Telon Plus 60ml', 4, 1, 'BTL', 'PKRT', 20754, 25000, '0000-00-00', ''),
(1010, '', 'GPU krim 60g', 3, 1, 'POT', 'OTC3', 8286, 12000, '0000-00-00', ''),
(1011, '', 'Balsem Tjing Tjau 20 g', 4, 3, 'POT', 'OTC3', 10844, 15000, '0000-00-00', ''),
(1012, '', 'Balsem Tjing Tjau 36 g', 2, 1, 'POT', 'OTC3', 16454, 22000, '0000-00-00', ''),
(1013, '', 'Balsem otot Geliga 10 g', 0, 2, 'POT', 'OTC3', 4436, 7000, '0000-00-00', ''),
(1014, '', 'Balsem otot Geliga 40 g', 3, 1, 'POT', 'OTC3', 15583, 20000, '0000-00-00', ''),
(1015, '', 'Kis Mint Rasa Cherry', 2, 1, 'SCH', 'OTC1', 0, 8000, '0000-00-00', ''),
(1016, '', 'Kis Mint Rasa Peppermint', 1, 1, 'SCH', 'OTC1', 0, 8000, '0000-00-00', ''),
(1017, '', 'Kis Mint Rasa Lime', 3, 1, 'SCH', 'OTC1', 0, 8000, '0000-00-00', ''),
(1018, '', 'Cimory Mini', 10, 2, 'BTL', 'MNMK', 2000, 3500, '0000-00-00', ''),
(1019, '', 'Cimory Squeeze', 0, 2, 'PCS', 'MNMK', 9000, 10500, '0000-00-00', ''),
(1020, '', 'Cimory UHT 250', 14, 2, 'PCS', 'MNMK', 5000, 7000, '0000-00-00', ''),
(1021, '', 'Cimory Botol', 2, 2, 'BTL', 'MNMK', 7500, 10000, '0000-00-00', ''),
(1022, '', 'Cimory UHT 125', 0, 2, 'PCS', 'MNMK', 2500, 4000, '0000-00-00', ''),
(1023, '', 'SANQUA', 0, 10, 'BTL', 'MNMK', 1166, 3000, '0000-00-00', ''),
(1024, '', 'hot in cream tube 60gr Hijau', 2, 2, 'TUB', 'OTC3', 11500, 15000, '0000-00-00', ''),
(1025, '', 'Minyak Zaitun Mustika Ratu 175ml', 2, 1, 'BTL', 'PKRT', 25677, 35000, '0000-00-00', ''),
(1026, '', 'Minyak Zaitun Mustika Ratu 75ml', 0, 1, 'BTL', 'PKRT', 15253, 20000, '0000-00-00', ''),
(1027, '', 'Cusson Baby Powder Fresh 150 g (ungu)', 2, 2, 'BTL', 'PKRT', 6193, 12000, '0000-00-00', ''),
(1028, '', 'Cusson Baby Powder Soft 150 g (pink)', 3, 2, 'BTL', 'PKRT', 6193, 12000, '0000-00-00', ''),
(1029, '', 'Woods Lozenges all Variant', 1, 2, 'SCH', 'OTC1', 4710, 8000, '0000-00-00', ''),
(1030, '', 'Hansaplast Elastis', 0, 2, 'SCH', 'ALK', 3036, 5000, '0000-00-00', ''),
(1031, '', 'Minyak Cemcem 175 ml', 0, 2, 'BTL', 'PKRT', 22801, 28000, '0000-00-00', ''),
(1032, '', 'Hi C 1000 Lemon 140ml(konsinasi)', 6, 3, 'BTL', 'MNMK', 4200, 7000, '0000-00-00', ''),
(1033, '', 'Entrasol Platinum Vanilla 400 g(konsinasi)', 2, 1, 'KLG', 'PKRT', 102951, 120000, '0000-00-00', ''),
(1034, '', 'Entrasol Platinum Vanilla 800g (konsinasi)', 1, 1, 'KLG', 'PKRT', 198660, 220000, '0000-00-00', ''),
(1035, '', 'Feminine Comfort (hijau)', 3, 1, 'BAG', 'PKRT', 25000, 30000, '0000-00-00', ''),
(1036, '', 'Feminine Comfort (biru)', 2, 2, 'PCS', 'PKRT', 23000, 25000, '0000-00-00', ''),
(1037, '', 'Minyak Kemiri', 2, 2, 'BTL', 'PKRT', 30000, 60000, '0000-00-00', ''),
(1038, '', 'johnson powder milk+rice 100 g (biru)', 0, 1, 'BTL', 'PKRT', 6850, 10000, '0000-00-00', ''),
(1039, '', 'johnson powder bed time 100 g (ungu)', 1, 1, 'BTL', 'PKRT', 6850, 10000, '0000-00-00', ''),
(1040, '', 'johnson powder blossom 100 g (pink)', 2, 1, 'BTL', 'PKRT', 6850, 10000, '0000-00-00', ''),
(1041, '', 'Minyak Kayu Putih Cap Ayam 150 ml', 0, 1, 'BTL', 'PKRT', 52415, 60000, '0000-00-00', ''),
(1042, '', 'Vit air mineral', 0, 3, 'BTL', 'MNMK', 1458, 3000, '0000-00-00', ''),
(1043, '', 'Balsem otot geliga 20 g', 3, 3, 'POT', 'PKRT', 7975, 10000, '0000-00-00', ''),
(1044, '', 'Baby Wipes Wet Cotton', 3, 1, 'BOX', 'ALK', 22000, 27000, '0000-00-00', ''),
(1045, '', 'Bearbrand (Susu Beruang)', 20, 3, 'KLG', 'MNMK', 13750, 10000, '0000-00-00', ''),
(1046, '', 'Johnson\'s Baby Soap Putih 100g', 1, 2, 'PCS', 'PKRT', 4851, 6000, '0000-00-00', ''),
(1047, '', 'Johnson\'s Blossoms Baby Soap 100g', 3, 2, 'PCS', 'PKRT', 4851, 6000, '0000-00-00', ''),
(1048, '', 'Johnson\'s Baby Shampoo 100ml (oranye)', 3, 2, 'BTL', 'PKRT', 9075, 13000, '0000-00-00', ''),
(1049, '', 'Cussons Baby Cream Mild & Gentle (biru)', 0, 2, 'POT', 'PKRT', 14630, 17000, '0000-00-00', ''),
(1050, '', 'Cussons Baby Cream Soft & Smooth (pink)', 0, 2, 'POT', 'PKRT', 14630, 17000, '0000-00-00', ''),
(1051, '', 'Sari Kurma AL JAZIRAH  360 g', 1, 1, 'BTL', 'HRB2', 17050, 30000, '0000-00-00', ''),
(1052, '', 'Bearbrand gold (kecil)', 3, 3, 'BTL', 'MNMK', 8396, 12000, '0000-00-00', ''),
(1053, '', 'Lemon Water', 0, 3, 'BTL', 'MNMK', 5997, 8000, '0000-00-00', ''),
(1054, '', 'You C 1000 Lemon 140ml', 15, 3, 'BTL', 'OTC2', 8333, 15000, '0000-00-00', ''),
(1055, '', 'Johnsons Powder Blossoms 200 g (pink)', 1, 1, 'BTL', 'PKRT', 10131, 20000, '0000-00-00', ''),
(1056, '', 'Natur e Moisturizing 100 ml (hijau)', 3, 1, 'BTL', 'OTC3', 12535, 16000, '0000-00-00', ''),
(1057, '', 'Citra Bengkoang 120 ml (Kuning)', 2, 1, 'BTL', 'OTC3', 11865, 15000, '0000-00-00', ''),
(1058, '', 'Citra Golden 120 ml (coklat)', 6, 1, 'BTL', 'OTC3', 11865, 15000, '0000-00-00', ''),
(1059, '', 'Vaseline SPF 100 ml (kuning-biru)', 3, 1, 'BTL', 'OTC3', 21365, 25000, '0000-00-00', ''),
(1060, '', 'Vaseline  UV 100 ml (pink)', 3, 1, 'BTL', 'OTC3', 14375, 20000, '0000-00-00', ''),
(1061, '', 'Vaseline Fresh Glow 180 ml (hijau)', 2, 1, 'BTL', 'OTC3', 26620, 30000, '0000-00-00', ''),
(1062, '', 'Citra Pearly 120 ml (pink)', 2, 1, 'BTL', 'OTC3', 11865, 15000, '0000-00-00', ''),
(1063, '', 'Vaseline Firm Glow 180 ml (merah)', 1, 1, 'BTL', 'OTC3', 26620, 30000, '0000-00-00', ''),
(1064, '', 'JF Sulfur Anti Acne (oranye)', 6, 1, 'PCS', 'OTC3', 12430, 16000, '0000-00-00', ''),
(1065, '', 'JF Sulfur Acne Protect (oranye-putih)', 2, 1, 'PCS', 'OTC3', 12430, 16000, '0000-00-00', ''),
(1066, '', 'Vaseline SPF 180 ml (oranye-biru)', 2, 1, 'BTL', 'OTC3', 40035, 48000, '0000-00-00', ''),
(1067, '', 'Herborist Aloevera Gel 100 g', 3, 2, 'BTL', 'OTC3', 31480, 37000, '0000-00-00', ''),
(1068, '', 'Teh Botol Sosro 350ml', 5, 3, 'BTL', 'MNMK', 3250, 5000, '0000-00-00', ''),
(1069, '', 'Teh Sosro Kotak 250', 0, 3, 'PCS', 'MNMK', 2270, 4000, '0000-00-00', ''),
(1070, '', 'Masker Bengkoang 15 g sachet', 2, 3, 'SCH', 'KMTK', 8389, 10000, '0000-00-00', ''),
(1071, '', 'Masker Bengkoang 60 gr tube', 1, 1, 'TUB', 'KMTK', 15761, 18000, '0000-00-00', ''),
(1072, '', 'Pantene Conditioner 130 ml', 1, 1, 'TUB', 'PKRT', 23650, 26000, '0000-00-00', ''),
(1073, '', 'Pantene Prov-V Hijab Edition 130 ml', 2, 1, 'TUB', 'PKRT', 23650, 26000, '0000-00-00', ''),
(1074, '', 'Pantene Shampo 130ml (Hitam)', 3, 1, 'TUB', 'PKRT', 23650, 26000, '0000-00-00', ''),
(1075, '', 'Hair Tonic Penyubur Rambut Mustika Ratu', 3, 3, 'BTL', 'PKRT', 18389, 22000, '0000-00-00', ''),
(1076, '', 'Hot in cream tube 120 g Hijau', 3, 2, 'TUB', 'OTC3', 24530, 28000, '0000-00-00', ''),
(1077, '', 'Parcok Minyak Urut 30 ml', 7, 2, 'BTL', 'OTC3', 7342, 9000, '0000-00-00', ''),
(1078, '', 'Verile Aktif Acne Gel 10 g', 3, 2, 'TUB', 'KMTK', 14080, 16000, '0000-00-00', ''),
(1079, '', 'Dermatix Ultra Gel 5 g', 1, 2, 'TUB', 'OTC3', 110000, 126000, '0000-00-00', ''),
(1080, '', 'Diabetasol Vanilla 600 gr', 0, 1, 'BOX', 'PKRT', 134881, 160000, '0000-00-00', ''),
(1081, '', 'Madu untuk kesehatan lambung HLV', 2, 1, 'BTL', 'HRB2', 29250, 45000, '0000-00-00', ''),
(1082, '', 'Madu Batuk Pilek HLV', 1, 1, 'BTL', 'HRB2', 26000, 40000, '0000-00-00', ''),
(1083, '', 'Madu Nafsu Makan HLV', 2, 1, 'BTL', 'HRB2', 26000, 40000, '0000-00-00', ''),
(1084, '', 'Madu Kurma for kids HLV', 2, 1, 'BTL', 'HRB2', 22750, 35000, '0000-00-00', ''),
(1085, '', 'Kanna Krim 15 gr', 4, 2, 'BTL', 'PKRT', 10000, 12000, '0000-00-00', ''),
(1086, '', 'Marcks Creme Powder', 4, 1, 'POT', 'KMTK', 12000, 15000, '0000-00-00', ''),
(1087, '', 'Paseo Cleansing Wipes 25\'s', 1, 1, 'BAG', 'PKRT', 3975, 5000, '0000-00-00', ''),
(1088, '', 'Denomix Cream 10g', 4, 3, 'TUB', 'ETC3', 12000, 14000, '0000-00-00', ''),
(1089, '', 'Madu Throm booster 280g', 3, 2, 'BTL', 'HRB2', 45500, 65000, '0000-00-00', ''),
(1090, '', 'Kapsul Daun Kelor 60', 3, 2, 'BTL', 'HRB1', 42000, 60000, '0000-00-00', ''),
(1091, '', 'Buavita 250ml', 11, 3, 'PCS', 'MNMK', 5700, 7000, '0000-00-00', ''),
(1092, '', 'Clear Fresh Lemon & Soft care', 4, 1, 'BTL', 'PKRT', 29590, 32000, '0000-00-00', ''),
(1093, '', 'Clear Menthol', 6, 0, 'BTL', 'PKRT', 29590, 32000, '0000-00-00', ''),
(1094, '', 'Clear Soft Care', 1, 0, 'BTL', 'PKRT', 29590, 32000, '0000-00-00', ''),
(1095, '', 'Lifebuoy bodywash 100 ml', 3, 1, 'BTL', 'PKRT', 0, 0, '0000-00-00', ''),
(1096, '', 'Sunsilk Shampoo', 4, 1, 'BTL', 'PKRT', 23356, 30000, '0000-00-00', ''),
(1097, '', 'Garnier Men Oil Control', 1, 1, 'PCS', 'KMTK', 31900, 41000, '0000-00-00', ''),
(1098, '', 'Garnier Men Acno Fight', 3, 1, 'PCS', 'KMTK', 31790, 41000, '0000-00-00', ''),
(1099, '', 'Ponds Men Bright Boost', 0, 1, 'PCS', 'KMTK', 32900, 35000, '0000-00-00', ''),
(1100, '', 'Citra Golden Glow UV 230 ml', 4, 0, 'BTL', 'KMTK', 0, 0, '0000-00-00', ''),
(1101, '', 'Rejoice Shampoo', 1, 0, 'BTL', 'PKRT', 24145, 27000, '0000-00-00', ''),
(1102, '', 'Natur e Advanced 245 ml', 1, 1, 'BTL', 'KMTK', 0, 0, '0000-00-00', ''),
(1103, '', 'Natur e Revitalizing 245 ml (pink)', 1, 1, 'BTL', 'KMTK', 0, 0, '0000-00-00', ''),
(1104, '', 'Natur e Moisturizing 245 ml (hijau)', 1, 1, 'BTL', 'KMTK', 0, 0, '0000-00-00', ''),
(1105, '', 'Kiranti Orange Juice 150ml', 8, 1, 'BTL', 'HRB2', 6620, 8000, '0000-00-00', ''),
(1106, '', 'Kiranti pegal linu 150ml', 8, 1, 'BTL', 'HRB2', 5937, 7000, '0000-00-00', ''),
(1107, '', 'Konicare Baby Liquid 125g', 0, 3, 'BTL', 'PKRT', 46827, 55000, '0000-00-00', ''),
(1108, '', 'Konicare Baby Hair Lotion 200ml', 1, 1, 'BTL', 'PKRT', 27725, 35000, '0000-00-00', ''),
(1109, '', 'Konicare Baby Bath 2 in 1 NB 300 ml', 1, 3, 'BTL', 'PKRT', 21954, 27000, '0000-00-00', ''),
(1110, '', 'Konicare Baby Bath 2 in 1 200 ml', 0, 3, 'BTL', 'PKRT', 14505, 19000, '0000-00-00', ''),
(1111, '', 'Konicare Baby Powder Fresh 100 gr', 2, 3, 'BTL', 'PKRT', 6860, 9000, '0000-00-00', ''),
(1112, '', 'Konicare Baby Powder Fresh 200gr', 2, 3, 'BTL', 'PKRT', 11434, 15000, '0000-00-00', ''),
(1113, '', 'Konicare Baby Powdery 100gr', 1, 3, 'BTL', 'PKRT', 6860, 9000, '0000-00-00', ''),
(1114, '', 'Konicare Baby Powdery 200gr', 2, 3, 'BTL', 'PKRT', 11434, 15000, '0000-00-00', ''),
(1115, '', 'Selsun Blue 120 ml', 1, 1, 'BTL', 'PKRT', 31882, 38000, '0000-00-00', ''),
(1116, '', 'Selsun blue five 120 ml', 1, 1, 'BTL', 'PKRT', 36107, 40000, '0000-00-00', ''),
(1117, '', 'Selsun original (oranye)', 1, 1, 'BTL', 'PKRT', 30730, 37000, '0000-00-00', ''),
(1118, '', 'Yuzu all variant', -3, 1, 'BTL', 'MNMK', 4500, 6000, '0000-00-00', ''),
(1119, '', 'Bio Oil 25 ml', 2, 1, 'BTL', 'KMTK', 53000, 65000, '0000-00-00', ''),
(1120, '', 'Pantene Anti Ketombe Biru 130 ml', 2, 1, 'BTL', 'PKRT', 23650, 26000, '0000-00-00', ''),
(1121, '', 'Head and Shoulders 130 ml', 4, 1, 'BTL', 'PKRT', 27390, 31000, '0000-00-00', ''),
(1122, '', 'Shampoo lifebuoy 170 ml', 6, 1, 'BTL', 'PKRT', 20881, 27000, '0000-00-00', ''),
(1123, '', 'GPU Krim 160g', 0, 3, 'POT', 'OTC3', 15510, 20000, '0000-00-00', ''),
(1124, '', 'Chloramfecort H Cream 10g', 5, 3, 'TUB', 'ETC3', 12196, 17000, '0000-00-00', ''),
(1125, '', 'Dettol Handsanitizer 50ml', 0, 3, 'BTL', 'ALK', 11342, 14000, '0000-00-00', ''),
(1126, '', 'Sunslik Shampo Black 170ml', 4, 1, 'BTL', 'PKRT', 23356, 30000, '0000-00-00', ''),
(1127, '', 'Salep Sriti 10 gr', 10, 3, 'TUB', 'OTC3', 4540, 6000, '0000-00-00', ''),
(1128, '', 'Minyak Sereh Dragon 60 ml', 12, 3, 'BTL', 'OTC3', 11750, 15000, '0000-00-00', ''),
(1129, '', 'Madu Probiotik konsinasi', 3, 1, 'BTL', 'HRB2', 25000, 33000, '0000-00-00', ''),
(1130, '', 'Madu Stamina Asyifa Konsinasi', 3, 1, 'BTL', 'HRB2', 35000, 46000, '0000-00-00', ''),
(1131, '', 'Minyak Telon 912 konsinasi', 3, 1, 'BTL', 'OTC3', 20000, 26000, '0000-00-00', ''),
(1132, '', 'Al Uula Minyak Zaitun Konsinasi', 2, 1, 'BTL', 'HRB2', 21000, 27000, '0000-00-00', ''),
(1133, '', 'Minyak Tawon 912 Konsinasi 50 ml', 3, 1, 'BTL', 'OTC3', 24000, 31000, '0000-00-00', ''),
(1134, '', 'Madu Penyubur Kandungan Asyifa konsinasi', 3, 1, 'BTL', 'HRB2', 45000, 59000, '0000-00-00', ''),
(1135, '', 'Madu Penyubur Pria Asyifa konsinasi', 3, 1, 'BTL', 'HRB2', 45000, 59000, '0000-00-00', ''),
(1136, '', 'Test Kehamilan One Med', 25, 10, 'PCS', 'ALK', 957, 5000, '0000-00-00', ''),
(1137, '', 'Masker JSP', 0, 10, 'BOX', 'ALK', 25000, 35000, '0000-00-00', ''),
(1138, '', 'Confidence Prem Night L7', 0, 2, 'PCS', 'ALK', 46000, 60000, '0000-00-00', ''),
(1139, '', 'Confidence Pants M 5\'s', 1, 3, 'PCS', 'ALK', 50000, 60000, '0000-00-00', ''),
(1140, '', 'Confidence Pants XL 3\'s', 0, 3, 'PCS', 'ALK', 20000, 30000, '0000-00-00', ''),
(1141, '', 'NataKasa Masinal 12\'s', 9, 3, 'BOX', 'ALK', 3347, 10000, '0000-00-00', ''),
(1142, '', 'Akurat', 23, 3, 'PCS', 'ALK', 8044, 11000, '0000-00-00', ''),
(1143, '', 'Bedak Salicyl Ciubros', 5, 0, 'BTL', 'ALK', 4500, 6000, '0000-00-00', ''),
(1144, '', 'Koyo Cabe', 0, 3, 'SCH', 'ALK', 8750, 11000, '0000-00-00', ''),
(1145, '', 'Sutra Merah 3\'s', 3, 3, 'PCS', 'ALK', 5000, 7500, '0000-00-00', ''),
(1146, '', 'Sutra Hitam 3\'s', 2, 3, 'PCS', 'ALK', 7600, 9000, '0000-00-00', ''),
(1147, '', 'Bye Bye Fever Anak', -32, 2, 'PCS', 'PKRT', 8905, 10000, '0000-00-00', ''),
(1148, '', 'Bye Bye Fever Bayi', 10, 3, 'PCS', 'PKRT', 6250, 8000, '0000-00-00', ''),
(1149, '', 'Kool Fever anak', 21, 3, 'PCS', 'PKRT', 6334, 8000, '0000-00-00', ''),
(1150, '', 'Hot In Cream Botol Merah 120 g', 0, 3, 'BTL', 'OTC3', 20000, 23000, '0000-00-00', ''),
(1151, '', 'Hot In Cream Botol Hijau 120 g', 0, 3, 'BTL', 'OTC3', 20000, 23000, '0000-00-00', ''),
(1152, '', 'Kapas Pembalut Sari Bunga 50g', 4, 3, 'BOX', 'ALK', 2667, 5000, '0000-00-00', ''),
(1153, '', 'Pispot Cowo + Tutup', 2, 0, 'UNT', 'ALK', 20000, 35000, '0000-00-00', ''),
(1154, '', 'Pispot Sendok ( cewe )', 1, 2, 'UNT', 'ALK', 35000, 50000, '0000-00-00', ''),
(1155, '', 'Softies Spray', 2, 0, 'BTL', 'ALK', 15000, 15000, '0000-00-00', ''),
(1156, '', 'Spuit Terumo 3 cc', 44, 10, 'UNT', 'ALK', 1500, 5000, '0000-00-00', ''),
(1157, '', 'Spuit Terumo 5 cc', 47, 10, 'UNT', 'ALK', 1950, 7000, '0000-00-00', ''),
(1158, '', 'Povidone Iodine One Med', 1, 3, 'BTL', 'ALK', 5000, 15000, '0000-00-00', ''),
(1159, '', 'Nata Kasa Masinal Gulung 10cm', 71, 3, 'UNT', 'ALK', 1500, 2500, '0000-00-00', ''),
(1160, '', 'Mamy Poko S (satuan)', 0, 3, 'PCS', 'ALK', 1850, 2500, '0000-00-00', ''),
(1161, '', 'Mamy Poko L (satuan)', 44, 3, 'PCS', 'ALK', 1600, 2500, '0000-00-00', ''),
(1162, '', 'Mamy Poko XXL', 0, 3, 'PCS', 'ALK', 2000, 3000, '0000-00-00', ''),
(1163, '', 'Charm EM non wings rencengan', 12, 3, 'PCS', 'ALK', 700, 1500, '0000-00-00', '');
INSERT INTO `barang` (`id_barang`, `kd_barang`, `nm_barang`, `stok_barang`, `stok_buffer`, `sat_barang`, `jenisobat`, `hrgsat_barang`, `hrgjual_barang`, `tgl_expired`, `ket_barang`) VALUES
(1164, '', 'Charm Wings 20\'s Lock Wings', 1, 3, 'PCS', 'ALK', 15000, 17000, '0000-00-00', ''),
(1165, '', 'Charm Max non Wings 20\'s', 2, 3, 'PCS', 'ALK', 11000, 13000, '0000-00-00', ''),
(1166, '', 'Charm Non Wings 8\'s', 4, 0, 'PCS', 'ALK', 6000, 7000, '0000-00-00', ''),
(1167, '', 'Huki Orthodontic Nipple CI 0116 120 ml', 0, 2, 'BTL', 'ALK', 34525, 40000, '0000-00-00', ''),
(1168, '', 'Young Young Soother 606S', 0, 3, 'PCS', 'ALK', 14850, 20000, '0000-00-00', ''),
(1169, '', 'Young Young Soother SA6', 0, 3, 'PCS', 'ALK', 17325, 23000, '0000-00-00', ''),
(1170, '', 'Young Young Soother SA8', 0, 3, 'PCS', 'ALK', 9600, 15000, '0000-00-00', ''),
(1171, '', 'Salonpas Hot 5 x 2', 9, 3, 'SCH', 'ALK', 5500, 10000, '0000-00-00', ''),
(1172, '', 'Char Mi Cotton Buds + Ear pick art 181 36\'s', 0, 3, 'UNT', 'ALK', 8050, 10000, '0000-00-00', ''),
(1173, '', 'Termometer Flexible', 0, 3, 'UNT', 'ALK', 22000, 55000, '0000-00-00', ''),
(1174, '', 'Alkohol 70% 100 ml', 1, 3, 'BTL', 'ALK', 3300, 10000, '0000-00-00', ''),
(1175, '', 'Zwitsal Mini Travel pack', 0, 1, 'BAG', 'ALK', 23795, 30000, '0000-00-00', ''),
(1176, '', 'Cussons Mini Bag', 0, 2, 'BAG', 'ALK', 22430, 30000, '0000-00-00', ''),
(1177, '', 'Masker disposable face mask Oncare 50\'S', 38, 10, 'PCS', 'ALK', 1000, 1500, '0000-00-00', ''),
(1178, '', 'Masker Sense Of Wellnes', 0, 10, 'PCS', 'ALK', 772, 1500, '0000-00-00', ''),
(1179, '', 'Selection Facial Cotton 50gr', 0, 3, 'PCS', 'ALK', 6070, 10000, '0000-00-00', ''),
(1180, '', 'Elastis perban 7,5 x 4,5 (kecil)', 5, 2, 'UNT', 'ALK', 10000, 15000, '0000-00-00', ''),
(1181, '', 'Vicks Vaporub Original 25gr', 5, 2, 'POT', 'OTC3', 15787, 18000, '0000-00-00', ''),
(1182, '', 'Vicks Vaporub Ori 10gr', 14, 2, 'POT', 'OTC3', 6936, 9000, '0000-00-00', ''),
(1183, '', 'Oxycan', -1, 2, 'BTL', 'OTC', 60000, 65000, '0000-00-00', ''),
(1184, '', 'Selection Cotton Bud 180pcs', 1, 1, 'BOX', 'ALK', 9450, 15000, '0000-00-00', ''),
(1185, '', 'Cussons Cotton Bud 100sticks', 0, 2, 'UNT', 'ALK', 4885, 10000, '0000-00-00', ''),
(1186, '', 'Ideal Cotton Bud 50batang', 0, 0, 'POT', 'ALK', 6100, 9000, '0000-00-00', ''),
(1187, '', 'Kapas Pembalut 1000gr', 1, 1, 'UNT', 'ALK', 35000, 70000, '0000-00-00', ''),
(1188, '', 'Sabun Lifeboy Lemon Fresh', 3, 2, 'PCS', 'OTC3', 2000, 2500, '0000-00-00', ''),
(1189, '', 'Sabun Lifebuoy  Biru', 1, 2, 'PCS', 'OTC3', 2000, 2500, '0000-00-00', ''),
(1190, '', 'Young Young Orthodontic Nipples Silicone 2L', 1, 2, 'UNT', 'ALK', 21700, 25000, '0000-00-00', ''),
(1191, '', 'Young Young Silicone Nipples 3XL', 0, 2, 'UNT', 'ALK', 22300, 25000, '0000-00-00', ''),
(1192, '', 'Young Young Silicone Nipples 3S', 0, 2, 'UNT', 'ALK', 22300, 25000, '0000-00-00', ''),
(1193, '', 'Young Young Silicone Nipples 3M', 0, 2, 'UNT', 'ALK', 22300, 25000, '0000-00-00', ''),
(1194, '', 'Young Young Btl Asi Pink&Biru', 2, 1, 'BTL', 'ALK', 26000, 35000, '0000-00-00', ''),
(1195, '', 'Huki Botol 240ml CI0341', 0, 1, 'BTL', 'ALK', 29300, 35000, '0000-00-00', ''),
(1196, '', 'Huki Baby Round Bottle CI 0328 120ml + Bonus (Flo Orthodontic)', 0, 1, 'BTL', 'ALK', 24000, 35000, '0000-00-00', ''),
(1197, '', 'Young Young Btl ASI 140ML', 6, 1, 'BTL', 'ALK', 20000, 25000, '0000-00-00', ''),
(1198, '', 'Pigeon Btl Asi', 2, 1, 'BTL', 'ALK', 32700, 40000, '0000-00-00', ''),
(1199, '', 'Sensi Dry S48', 1, 1, 'BAG', 'ALK', 47900, 70000, '0000-00-00', ''),
(1200, '', 'Sensi Dry NB52', 0, 1, 'BAG', 'ALK', 47900, 70000, '0000-00-00', ''),
(1201, '', 'Sensi Dry Pants S40', 2, 1, 'BAG', 'ALK', 43950, 55000, '0000-00-00', ''),
(1202, '', 'Sensi Dry Pants XL24', 0, 1, 'BAG', 'ALK', 43950, 55000, '0000-00-00', ''),
(1203, '', 'Sensi Dry Pant L30', 1, 1, 'BAG', 'ALK', 55000, 65000, '0000-00-00', ''),
(1204, '', 'Sensi Night Pant Junior Boys 3XL12', 1, 1, 'BAG', 'ALK', 33950, 95000, '0000-00-00', ''),
(1205, '', 'Sensi Night Pant Junior Boys 4XL10', 1, 1, 'BAG', 'ALK', 67900, 95000, '0000-00-00', ''),
(1206, '', 'Sensi Night Pant Junior Girl 4XL10', 3, 1, 'BAG', 'ALK', 67900, 95000, '0000-00-00', ''),
(1207, '', 'Baby Happy Pants L8', 1, 1, 'BAG', 'ALK', 16500, 25000, '0000-00-00', ''),
(1208, '', 'Thermometer Infrared', 4, 1, 'UNT', 'ALK', 200000, 250000, '0000-00-00', ''),
(1209, '', 'Tissue Trendy', 0, 2, 'PCS', 'ALK', 800, 1000, '0000-00-00', ''),
(1210, '', 'Laurier RN 35cm W12', 0, 1, 'BAG', 'ALK', 16850, 25000, '0000-00-00', ''),
(1211, '', 'Laurier RN 30cm W24', 0, 1, 'BAG', 'ALK', 23950, 35000, '0000-00-00', ''),
(1212, '', 'Laurier Softcar W30', 0, 1, 'BAG', 'ALK', 18900, 25000, '0000-00-00', ''),
(1213, '', 'Laurier Softcar W20', 0, 1, 'BAG', 'ALK', 13175, 15000, '0000-00-00', ''),
(1214, '', 'Laurier Softcar 20', 2, 1, 'BAG', 'ALK', 9250, 15000, '0000-00-00', ''),
(1215, '', 'Rivanol 100ml', 9, 0, 'BTL', 'ALK', 3300, 5000, '0000-00-00', ''),
(1216, '', 'Alat Auto Check 1set', 2, 0, 'SET', 'ALK', 200000, 250000, '0000-00-00', ''),
(1217, '', 'Alkohol Swab', 4, 0, 'BOX', 'ALK', 10000, 15000, '0000-00-00', ''),
(1218, '', 'Blood Lancet', 0, 0, 'BOX', 'ALK', 12000, 15000, '0000-00-00', ''),
(1219, '', 'Oximeter (pribadi untuk cek darah)', 1, 0, 'UNT', 'ALK', 120000, 0, '0000-00-00', ''),
(1220, '', 'Kapas Pembalut Sari Bunga 250gr', 2, 0, 'UNT', 'ALK', 12000, 14000, '0000-00-00', ''),
(1221, '', 'Leucoplast 1,25 x 4,5', 3, 3, 'PCS', 'ALK', 7800, 12000, '0000-00-00', ''),
(1222, '', 'Timbangan BB', 0, 1, 'UNT', 'ALK', 65000, 100000, '0000-00-00', ''),
(1223, '', 'Charm Nigh Wing 350 12\'s', 0, 2, 'PCS', 'ALK', 15250, 17000, '0000-00-00', ''),
(1224, '', 'Charm Non Wings Extra Maxi 10\'s', 2, 2, 'PCS', 'ALK', 4500, 6000, '0000-00-00', ''),
(1225, '', 'Charm Night 290 S10 10\'s', 3, 2, 'PCS', 'ALK', 8250, 15000, '0000-00-00', ''),
(1226, '', 'Paseo Kecil', 16, 5, 'PCS', 'ALK', 750, 2000, '0000-00-00', ''),
(1227, '', 'Charm Wing 2\'s rencengan', 5, 3, 'PCS', 'ALK', 1300, 3000, '0000-00-00', ''),
(1228, '', 'Genki Moko S (satuan)', 6, 0, 'PCS', 'ALK', 1250, 2000, '0000-00-00', ''),
(1229, '', 'Genki Moko XL (satuan)', 0, 0, 'PCS', 'ALK', 1916, 2500, '0000-00-00', ''),
(1230, '', 'Sabun Zwitsal Rich Honey 70g', 2, 0, 'PCS', 'PKRT', 4000, 6000, '0000-00-00', ''),
(1231, '', 'Mamy Poko M (satuan)', 43, 0, 'PCS', 'ALK', 1600, 2500, '0000-00-00', ''),
(1232, '', 'Sabun Zwitsal Classic 70g', 6, 0, 'PCS', 'PKRT', 4000, 6000, '0000-00-00', ''),
(1233, '', 'Balpirik Balsam Hijau 20gr', 5, 0, 'POT', 'OTC3', 8833, 10500, '0000-00-00', ''),
(1234, '', 'Balpirik Balsam Merah 20gr', 5, 0, 'POT', 'OTC3', 8833, 10500, '0000-00-00', ''),
(1235, '', 'Balpirik Balsam Kuning 20gr', 6, 0, 'POT', 'OTC3', 8833, 10500, '0000-00-00', ''),
(1236, '', 'Balsem Lang 10gr', 0, 3, 'POT', 'OTC3', 4347, 6000, '0000-00-00', ''),
(1237, '', 'Balsem Lang 20gr', 0, 0, 'POT', 'OTC3', 7975, 9500, '0000-00-00', ''),
(1238, '', 'Siwak-F Reguler 120 gr', 6, 3, 'TUB', 'ALK', 10000, 15000, '0000-00-00', ''),
(1239, '', 'Siwak-F Herbal 120 gr', 6, 3, 'TUB', 'ALK', 10800, 16000, '0000-00-00', ''),
(1240, '', 'Ideal Cotton Buds 100batang', 0, 1, 'UNT', 'ALK', 10715, 12000, '0000-00-00', ''),
(1241, '', 'Baby Huki Cotton Buds 100sticks', 6, 1, 'SCH', 'PKRT', 8165, 9500, '0000-00-00', ''),
(1242, '', 'Huki botol 120ml CI0328 + Bonus (Huki With Handle)', 0, 1, 'UNT', 'ALK', 43935, 48000, '0000-00-00', ''),
(1243, '', 'Huki Botol 250ml CI 0034', 1, 1, 'UNT', 'ALK', 36825, 40000, '0000-00-00', ''),
(1244, '', 'Huki Orthodontic 120ml CI0328 + Bonus (Huki 240ml)', 0, 1, 'UNT', 'ALK', 35615, 39000, '0000-00-00', ''),
(1245, '', 'Enzim C. Mild 124g', 1, 1, 'TUB', 'ALK', 17350, 20000, '0000-00-00', ''),
(1246, '', 'Enzim Fresh Mint 124g', 1, 1, 'TUB', 'ALK', 17350, 20000, '0000-00-00', ''),
(1247, '', 'Ideal Ichiban Cotton Bud 100sticks', 0, 1, 'PCS', 'ALK', 5150, 7000, '0000-00-00', ''),
(1248, '', 'Tweety Cotton Buds 48batang', 0, 1, 'PCS', 'ALK', 1983, 3000, '0000-00-00', ''),
(1249, '', 'Selection Round 80\'s', 6, 1, 'PCS', 'ALK', 14345, 18000, '0000-00-00', ''),
(1250, '', 'Selection Facial Cotton 35g', 0, 1, 'PCS', 'ALK', 5750, 8000, '0000-00-00', ''),
(1251, '', 'Bisyol 20g', 4, 2, 'POT', 'ETC1', 16500, 25000, '0000-00-00', ''),
(1252, '', 'Sarung Tangan Latex', 222, 6, 'PCS', 'ALK', 1100, 2000, '0000-00-00', ''),
(1253, '', 'Dot pigeon satuan', -4, 3, 'PCS', 'ALK', 8300, 10000, '0000-00-00', ''),
(1254, '', 'Sweety Silver M30', 0, 1, 'BAG', 'ALK', 48000, 77000, '0000-00-00', ''),
(1255, '', 'Sweety Silver L28', 0, 1, 'BAG', 'ALK', 48000, 77000, '0000-00-00', ''),
(1256, '', 'Sweety Silver XL26', 0, 1, 'BAG', 'ALK', 48000, 77000, '0000-00-00', ''),
(1257, '', 'Sweety Silver XXL24', 0, 1, 'BAG', 'ALK', 53500, 85000, '0000-00-00', ''),
(1258, '', 'Confidence Prem Night XL6', 0, 1, 'BAG', 'ALK', 56500, 60000, '0000-00-00', ''),
(1259, '', 'Confidence Clasic Day XL6', 0, 1, 'BAG', 'ALK', 43500, 52000, '0000-00-00', ''),
(1260, '', 'Confidence Clasic day L7', 0, 1, 'BAG', 'ALK', 43500, 52000, '0000-00-00', ''),
(1261, '', 'Confidence Pant L10', 0, 1, 'BAG', 'ALK', 94500, 115000, '0000-00-00', ''),
(1262, '', 'Cotton Ball Medisoft', 0, 2, 'BAG', 'ALK', 11200, 15000, '0000-00-00', ''),
(1263, '', 'Huki botol PP CI 0330 120ml', 2, 1, 'BTL', 'ALK', 18000, 25000, '0000-00-00', ''),
(1264, '', 'Huki Besar 240ml 40ZB/M', 0, 1, 'BTL', 'ALK', 13950, 27000, '0000-00-00', ''),
(1265, '', 'Huki Botol REG 40Z', 0, 1, 'BTL', 'ALK', 27225, 32000, '0000-00-00', ''),
(1266, '', 'Huki Botol PP CI 0329 240ml', 5, 1, 'BTL', 'ALK', 18333, 28000, '0000-00-00', ''),
(1267, '', 'Huki ST Round 8', 0, 1, 'UNT', 'ALK', 14607, 30000, '0000-00-00', ''),
(1268, '', 'Huki Botol CI 0342 60ml', 0, 1, 'BTL', 'ALK', 18000, 22000, '0000-00-00', ''),
(1269, '', 'Young Young Shooter SA1', 0, 1, 'UNT', 'ALK', 18265, 20000, '0000-00-00', ''),
(1270, '', 'Young Young Anti Pouty', 0, 1, 'UNT', 'ALK', 19185, 22000, '0000-00-00', ''),
(1271, '', 'Salonpas Gel 15g', 3, 0, 'TUB', 'OTC3', 11190, 18000, '0000-00-00', ''),
(1272, '', 'Sensitif Strip', 9, 3, 'PCS', 'ALK', 19000, 25000, '0000-00-00', ''),
(1273, '', 'Microlut tab 35\'s', 2, 3, 'STRIP', 'ETC1', 37024, 42000, '0000-00-00', ''),
(1274, '', 'Tongkat Patah kaki', 2, 1, 'UNT', 'ALK', 170000, 200000, '0000-00-00', ''),
(1275, '', 'Pispot Cowok tanpa tutup', 2, 0, 'UNT', 'ALK', 25000, 35000, '0000-00-00', ''),
(1276, '', 'Stetoskop', 1, 0, 'UNT', 'ALK', 30000, 0, '0000-00-00', ''),
(1277, '', 'Tensi', 1, 0, 'UNT', 'ALK', 85000, 0, '0000-00-00', ''),
(1278, '', 'Empeng Bayi Toples', 40, 0, 'PCS', 'ALK', 2400, 10000, '0000-00-00', ''),
(1279, '', 'Cotton Buds Cardinal', 15, 0, 'PCS', 'ALK', 1111, 4000, '0000-00-00', ''),
(1280, '', 'Cotton Buds Lusty Bunny', 0, 0, 'PCS', 'ALK', 3000, 4000, '0000-00-00', ''),
(1281, '', 'Tempat bedak bayi cici', 6, 3, 'PCS', 'ALK', 9167, 15000, '0000-00-00', ''),
(1282, '', 'Salonpas Hot 12 x 1', 8, 3, 'SCH', 'ALK', 6447, 10000, '0000-00-00', ''),
(1283, '', 'Salonpas Hijau 5 x 2', 0, 3, 'SCH', 'ALK', 5368, 10000, '0000-00-00', ''),
(1284, '', 'Tensi Digital Omron', 1, 0, 'UNT', 'ALK', 485000, 600000, '0000-00-00', ''),
(1285, '', 'Autocheck Uric Acid', 2, 3, 'BOX', 'ALK', 90000, 100000, '0000-00-00', ''),
(1286, '', 'Autocheck Cholesterol', 0, 3, 'BOX', 'ALK', 153000, 180000, '0000-00-00', ''),
(1287, '', 'Autocheck Blood Glucose', 0, 3, 'BOX', 'ALK', 73000, 80000, '0000-00-00', ''),
(1288, '', 'Mami BREAST PUMP', 1, 3, 'BOX', 'ALK', 11000, 25000, '0000-00-00', ''),
(1289, '', 'Tongkat Kaki 4', 2, 1, 'UNT', 'ALK', 80000, 140000, '0000-00-00', ''),
(1290, '', 'Tongkat Kaki 3', 2, 1, 'UNT', 'ALK', 85000, 130000, '0000-00-00', ''),
(1291, '', 'Micropore 1,25 x 9,1 kecil', 5, 3, 'UNT', 'ALK', 9375, 13000, '0000-00-00', ''),
(1292, '', 'Sensi Dry S12', 1, 1, 'UNT', 'ALK', 17915, 25000, '0000-00-00', ''),
(1293, '', 'Pot urine uk 20', 94, 3, 'POT', 'ALK', 350, 4000, '0000-00-00', ''),
(1294, '', 'Giovan Soap Fresh 90ml (sabun)', 4, 3, 'BTL', 'PKRT', 8500, 15000, '0000-00-00', ''),
(1295, '', 'Tempat Bedak Bayi Peanut', 2, 1, 'UNT', 'ALK', 29150, 35000, '0000-00-00', ''),
(1296, '', 'Powder Case Tempat Bayi', 2, 1, 'UNT', 'ALK', 17600, 25000, '0000-00-00', ''),
(1297, '', 'Nasal Aspirator (Sedot Ingus)', 3, 2, 'UNT', 'ALK', 18700, 25000, '0000-00-00', ''),
(1298, '', 'Dodo Milk Powder Container Big 3 Layer', 4, 1, 'UNT', 'ALK', 25300, 35000, '0000-00-00', ''),
(1299, '', 'Dodo Milk Powder Container Small 3 Layer', 4, 1, 'UNT', 'ALK', 25300, 30000, '0000-00-00', ''),
(1300, '', 'Ultrafix 5x5', 0, 1, 'BOX', 'ALK', 13000, 25000, '0000-00-00', ''),
(1301, '', 'Ultrafix 10x10', 0, 1, 'BOX', 'ALK', 23000, 40000, '0000-00-00', ''),
(1302, '', 'Pot Urine uk 10', 100, 3, 'POT', 'ALK', 300, 2000, '0000-00-00', ''),
(1303, '', 'Pot Cream uk 20', 25, 3, 'POT', 'ALK', 1600, 5000, '0000-00-00', ''),
(1304, '', 'Elastis perban 10 x 4,5 (sedang)', 5, 2, 'UNT', 'ALK', 13000, 20000, '0000-00-00', ''),
(1305, '', 'Elastis perban 15 x 4,5 (besar)', 5, 2, 'UNT', 'ALK', 15000, 25000, '0000-00-00', ''),
(1306, '', 'Botol Susu Inova 8 Oz Dodo', 4, 1, 'UNT', 'ALK', 24100, 35000, '0000-00-00', ''),
(1307, '', 'Botol Susu Eko 9 Oz Dodo', 4, 1, 'UNT', 'ALK', 22000, 35000, '0000-00-00', ''),
(1308, '', 'Botol Susu Eko 2 Oz Dodo', 5, 1, 'UNT', 'ALK', 18700, 25000, '0000-00-00', ''),
(1309, '', 'Botol Susu Inova 2 Oz Dodo', 3, 1, 'UNT', 'ALK', 20350, 25000, '0000-00-00', ''),
(1310, '', 'Botol Susu Eko 4 Oz Dodo', 3, 1, 'UNT', 'ALK', 20350, 30000, '0000-00-00', ''),
(1311, '', 'Botol Susu Inova 4 Oz Dodo', 3, 1, 'UNT', 'ALK', 22000, 30000, '0000-00-00', ''),
(1312, '', 'Tempat Bedak Bayi (Love)', 4, 1, 'UNT', 'ALK', 29150, 35000, '0000-00-00', ''),
(1313, '', 'Empeng UK M', 0, 10, 'UNT', 'ALK', 5830, 10000, '0000-00-00', ''),
(1314, '', 'Empeng UK M', 0, 10, 'UNT', 'ALK', 5830, 10000, '0000-00-00', ''),
(1315, '', 'Cotton Bud 121', 0, 1, 'UNT', 'ALK', 2585, 4000, '0000-00-00', ''),
(1316, '', 'Cotton Bud 132', 0, 1, 'UNT', 'ALK', 3080, 5000, '0000-00-00', ''),
(1317, '', 'Botol Susu Umix 120 ml Dodo', 3, 1, 'BTL', 'ALK', 29925, 35000, '0000-00-00', ''),
(1318, '', 'Botol Susu Donut Gripper 8 OZ Dodo', 3, 1, 'BTL', 'ALK', 33250, 37000, '0000-00-00', ''),
(1319, '', 'Botol Susu Streamline 150 ml Dodo', 3, 1, 'BTL', 'ALK', 28500, 33000, '0000-00-00', ''),
(1320, '', 'Botol Susu Streamline 280 ml Dodo', 2, 1, 'BTL', 'ALK', 30400, 35000, '0000-00-00', ''),
(1321, '', 'Botol Susu Tulip 6OZ/175 ml Dodo', 2, 1, 'BTL', 'ALK', 29925, 33000, '0000-00-00', ''),
(1322, '', 'Soother S 050 W/Holder Dodo', 4, 1, 'PCS', 'ALK', 19475, 23000, '0000-00-00', ''),
(1323, '', 'Cotton Ball isi 50 pcs Dodo', 5, 2, 'PCS', 'ALK', 7790, 10000, '0000-00-00', ''),
(1324, '', 'Water F.Teether Dodo', 4, 1, 'PCS', 'ALK', 12825, 16000, '0000-00-00', ''),
(1325, '', 'Silicon F.Teether Dodo', 2, 1, 'PCS', 'ALK', 14250, 18000, '0000-00-00', ''),
(1326, '', 'Powder Puff Dodo', 7, 2, 'PCS', 'ALK', 6650, 8000, '0000-00-00', ''),
(1327, '', 'Soother Holder Dodo', 5, 1, 'PCS', 'ALK', 14102, 18000, '0000-00-00', ''),
(1328, '', 'Bowl Set Cup Dodo', 3, 1, 'SET', 'ALK', 41800, 45000, '0000-00-00', ''),
(1329, '', 'Baby Spoon Dodo', 3, 1, 'PCS', 'ALK', 14250, 17000, '0000-00-00', ''),
(1330, '', 'Fruit Feeder Dodo', 2, 1, 'PCS', 'ALK', 28500, 35000, '0000-00-00', ''),
(1331, '', 'Silicone Food/Fruit Feeder Dodo', 2, 1, 'PCS', 'ALK', 30875, 37000, '0000-00-00', ''),
(1332, '', 'Cutlery Set Dodo', 4, 1, 'SET', 'ALK', 14250, 18000, '0000-00-00', ''),
(1333, '', 'Softex Celana Menstruasi', 1, 1, 'BAG', 'PKRT', 17435, 22000, '0000-00-00', ''),
(1334, '', 'Oximeter', 0, 2, 'PCS', 'ALK', 97916, 198000, '0000-00-00', ''),
(1335, '', 'Micropore 2,5cm x 9,1cm besar', 2, 2, 'UNT', 'ALK', 15583, 25000, '0000-00-00', ''),
(1336, '', 'Pipet Drops', 22, 3, 'PCS', 'ALK', 2300, 5000, '0000-00-00', ''),
(1337, '', 'Kleentis 100ml', 0, 2, 'BTL', 'ALK', 10000, 20000, '0000-00-00', ''),
(1338, '', 'Masker KN95', 24, 5, 'PCS', 'ALK', 1900, 5000, '0000-00-00', ''),
(1339, '', 'Jarum Insulin Biru', 93, 3, 'PCS', 'ALK', 2950, 5000, '0000-00-00', ''),
(1340, '', 'Jarum Insulin Ungu', 200, 3, 'PCS', 'ALK', 2500, 3500, '0000-00-00', ''),
(1341, '', 'Jarum Insulin Hijau', 84, 3, 'PCS', 'ALK', 2500, 3500, '0000-00-00', ''),
(1342, '', 'Sensi Dry S46', 0, 1, 'PCS', 'PKRT', 47900, 70000, '0000-00-00', ''),
(1343, '', 'pigeon silicon nipple L', 0, 1, 'PCS', 'ALK', 13585, 18000, '0000-00-00', ''),
(1344, '', 'pigeon peristaltic nipple L', 0, 1, 'PCS', 'ALK', 18480, 25000, '0000-00-00', ''),
(1345, '', 'pigeon peristaltic M', 0, 1, 'PCS', 'ALK', 18480, 25000, '0000-00-00', ''),
(1346, '', 'young young orthodontic nipple M', 0, 1, 'BOX', 'ALK', 11150, 15000, '0000-00-00', ''),
(1347, '', 'young young orthodontic nipple L', 0, 1, 'BOX', 'ALK', 11150, 15000, '0000-00-00', ''),
(1348, '', 'confidence men L4', 0, 1, 'PCS', 'PKRT', 35400, 40000, '0000-00-00', ''),
(1349, '', 'confidence men M5', 1, 1, 'PCS', 'PKRT', 36875, 42000, '0000-00-00', ''),
(1350, '', 'confidence women L4', 2, 1, 'PCS', 'PKRT', 35400, 40000, '0000-00-00', ''),
(1351, '', 'confidence women M5', 2, 1, 'PCS', 'PKRT', 36875, 42000, '0000-00-00', ''),
(1352, '', 'confidence prem night M8', 1, 1, 'PCS', 'PKRT', 47250, 50000, '0000-00-00', ''),
(1353, '', 'confidence classic day M8', 1, 1, 'PCS', 'PKRT', 43585, 52000, '0000-00-00', ''),
(1354, '', 'Mamy Poko Standar 24\'s New Born', 1, 1, 'BAG', 'ALK', 31450, 35000, '0000-00-00', ''),
(1355, '', 'Mamy Poko Standar 12\'s New Born', 2, 1, 'BAG', 'ALK', 17800, 21000, '0000-00-00', ''),
(1356, '', 'Mamy Poko Soft Royal New Born 14\'s', 1, 1, 'BAG', 'ALK', 36575, 40000, '0000-00-00', ''),
(1357, '', 'Charm Wing 20\'s extra maxi', 1, 1, 'BAG', 'PKRT', 15000, 17000, '0000-00-00', ''),
(1358, '', 'Lomatuell 10x10 10\'s @ Box', 4, 2, 'PCS', 'ALK', 12000, 16000, '0000-00-00', ''),
(1359, '', 'Mama Pembalut Bersalin', 4, 2, 'BAG', 'PKRT', 18480, 25000, '0000-00-00', ''),
(1360, '', 'Pembalut Wanita Bersalin & Nifas', 4, 2, 'BAG', 'PKRT', 22440, 27000, '0000-00-00', ''),
(1361, '', 'Kapas pembalut sari bunga 500 g', 1, 1, 'PCS', 'PKRT', 20000, 35000, '0000-00-00', ''),
(1362, '', 'Kapas pembalut sari bunga 100 g', 3, 1, 'PCS', 'PKRT', 4300, 8000, '0000-00-00', ''),
(1363, '', 'Spuit 10 cc', 61, 3, 'PCS', 'ALK', 1500, 10000, '0000-00-00', ''),
(1364, '', 'Handy Clean 60ml', 2, 3, 'BTL', 'ALK', 9000, 15000, '0000-00-00', ''),
(1365, '', 'Hot In Cream Botol 60 gr Merah', 0, 2, 'BTL', 'OTC3', 10972, 15000, '0000-00-00', ''),
(1366, '', 'Nasal Oksigen Dewasa', 2, 2, 'PCS', 'ALK', 7000, 12000, '0000-00-00', ''),
(1367, '', 'Nasal Oksigen Anak', 7, 2, 'PCS', 'ALK', 7000, 12000, '0000-00-00', ''),
(1368, '', 'Masker Oksigen', -1, 10, 'PCS', 'ALK', 15000, 20000, '0000-00-00', ''),
(1369, '', 'Masker disposable face mask Oncare', 0, 1, 'BOX', 'ALK', 20000, 30000, '0000-00-00', ''),
(1370, '', 'DODO Nipple Toples MIX uk. S (merah)', 26, 3, 'PCS', 'ALK', 5538, 10000, '0000-00-00', ''),
(1371, '', 'Balsem Lang 40g', 4, 3, 'POT', 'OTC3', 15583, 20000, '0000-00-00', ''),
(1372, '', 'BP Popok Dewasa (M, L, XL)', 2, 1, 'BAG', 'PKRT', 35000, 55000, '0000-00-00', ''),
(1373, '', 'Nurse Cap (Topi Perawat)', 97, 5, 'PCS', 'ALK', 450, 2000, '0000-00-00', ''),
(1374, '', 'Wings Needle All Uk. (23, 25, 27)', 29, 5, 'PCS', 'ALK', 1150, 5000, '0000-00-00', ''),
(1375, '', 'Pepsodent Pasta Gigi Herbal 75g Hijau', 4, 2, 'TUB', 'ALK', 7354, 10000, '0000-00-00', ''),
(1376, '', 'Sutra Hitam 12\'s', 1, 1, 'BOX', 'PKRT', 26800, 30000, '0000-00-00', ''),
(1377, '', 'Kool Fever Baby 12\'s', 14, 3, 'PCS', 'PKRT', 4743, 7000, '0000-00-00', ''),
(1378, '', 'Plossa Mini Ecualyptus 5ml', 3, 3, 'PCS', 'PKRT', 5225, 8000, '0000-00-00', ''),
(1379, '', 'Plossa Mini Red Hot 5ml', 4, 3, 'PCS', 'PKRT', 5225, 8000, '0000-00-00', ''),
(1380, '', 'Parfum', 16, 1, 'PCS', 'KMTK', 22000, 30000, '0000-00-00', ''),
(1381, '', 'Sarung Tangan Latex 100\'s', 2, 1, 'BOX', 'PKRT', 110000, 150000, '0000-00-00', ''),
(1382, '', 'Kool FeverDewasa 6\'s', 0, 3, 'SCH', 'PKRT', 10999, 13000, '0000-00-00', ''),
(1383, '', 'Vigel 60gr', 2, 3, 'TUB', 'PKRT', 22324, 29000, '0000-00-00', ''),
(1384, '', 'Vigel 30gr', 2, 3, 'TUB', 'PKRT', 13842, 18000, '0000-00-00', ''),
(1385, '', 'Termorex Patch', 1, 3, 'PCS', 'PKRT', 6207, 8000, '0000-00-00', ''),
(1386, '', 'Antis botol spray 55 ml Jasmine', 3, 1, 'BTL', 'PKRT', 11220, 15000, '0000-00-00', ''),
(1387, '', 'Antis botol spray 55 ml Timun', 2, 1, 'BTL', 'PKRT', 11220, 15000, '0000-00-00', ''),
(1388, '', 'Baby Huki Cotton Bud 150\'s', 6, 2, 'PCS', 'PKRT', 6399, 8000, '0000-00-00', ''),
(1389, '', 'Feminine Comfort (Merah)', 2, 1, 'PCS', 'PKRT', 28125, 30000, '0000-00-00', ''),
(1390, '', 'Masker Skineer Anak 5\'s', 5, 1, 'PCS', 'ALK', 17100, 20000, '0000-00-00', ''),
(1391, '', 'Dodo Nipple Toples Mix uk. XL (kuning)', 20, 3, 'PCS', 'ALK', 5538, 7000, '0000-00-00', ''),
(1392, '', 'Kapas pembalut sari bunga 25 g', 0, 1, 'PCS', 'ALK', 1558, 3000, '0000-00-00', ''),
(1393, '', 'Tisu Super Magic', 12, 3, 'SCH', 'PKRT', 1500, 5000, '0000-00-00', ''),
(1394, '', 'Sari Ayu Shampoo Lidah Buaya 170 ml', 3, 1, 'BTL', 'KMTK', 15519, 20000, '0000-00-00', ''),
(1395, '', 'Sari Ayu Pembersih Wajah All in 1 150ml', 3, 1, 'BTL', 'KMTK', 15120, 20000, '0000-00-00', ''),
(1396, '', 'Sari Ayu Cologne Tanjung 150 ml', 3, 1, 'BTL', 'KMTK', 26720, 34000, '0000-00-00', ''),
(1397, '', 'Sari Ayu Acne Care Foam 75 gr', 3, 1, 'BTL', 'KMTK', 16119, 20000, '0000-00-00', ''),
(1398, '', 'Ellips Vitamin Rambut (Kuning)', 0, 2, 'STRIP', 'KMTK', 12264, 14000, '0000-00-00', ''),
(1399, '', 'Tresemme Hair Fall 170ml', 0, 2, 'BTL', 'PKRT', 30690, 34000, '0000-00-00', ''),
(1400, '', 'Dodo Nipple Toples Mix uk. L (hijau)', 22, 3, 'PCS', 'ALK', 5538, 7000, '0000-00-00', ''),
(1401, '', 'Masker Rojukiss Acne Pore Expert 5x', 0, 1, 'SCH', 'KMTK', 8925, 11000, '0000-00-00', ''),
(1402, '', 'Masker Rojukiss Bright Pore Expert 5x', 0, 1, 'SCH', 'KMTK', 8925, 11000, '0000-00-00', ''),
(1403, '', 'Masker Rojukiss Perfect Pore Expert 5x', 0, 1, 'SCH', 'KMTK', 8925, 11000, '0000-00-00', ''),
(1404, '', 'Masker Mediheal Cacao Vita Ade', 1, 1, 'SCH', 'KMTK', 16150, 20000, '0000-00-00', ''),
(1405, '', 'Masker Ariul Tea Tree +M', 1, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1406, '', 'Masker Ariul Aloe +H', 0, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1407, '', 'Masker Ariul Lotus +N', 0, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1408, '', 'Masker Ariul Green Tea +S', 0, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1409, '', 'Masker Ariul Lemon +C', 0, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1410, '', 'Masker Ariul Calendula +P', 1, 1, 'SCH', 'KMTK', 8075, 10000, '0000-00-00', ''),
(1411, '', 'Masker Ariul Bamboo +B', 1, 1, 'SCH', 'KMTK', 8175, 10000, '0000-00-00', ''),
(1412, '', 'Masker Mediheal Vital Firming', 0, 1, 'SCH', 'KMTK', 6587, 8500, '0000-00-00', ''),
(1413, '', 'Masker Mediheal Hydra Soothing', 1, 1, 'SCH', 'KMTK', 6587, 8500, '0000-00-00', ''),
(1414, '', 'Masker Mediheal Pure Calming', 0, 1, 'SCH', 'KMTK', 6587, 8500, '0000-00-00', ''),
(1415, '', 'Sari Ayu Bedak Kuning Langsat 14g', 3, 1, 'PCS', 'KMTK', 13600, 17000, '0000-00-00', ''),
(1416, '', 'Sari Ayu Bedak Kuning Pengantin 14g', 3, 1, 'PCS', 'KMTK', 13600, 17000, '0000-00-00', ''),
(1417, '', 'Sari Ayu Pelembab Jeruk 35ml', 3, 1, 'BTL', 'KMTK', 9040, 11000, '0000-00-00', ''),
(1418, '', 'Sari Ayu Pelembab Mawar 35ml', 3, 1, 'BTL', 'KMTK', 9040, 11000, '0000-00-00', ''),
(1419, '', 'Huki Cotton Bud Baby Pot 100\'s', 3, 1, 'POT', 'ALK', 7425, 10000, '0000-00-00', ''),
(1420, '', 'Huki Cotton Bud Baby Pot 50\'s', 0, 1, 'POT', 'ALK', 4400, 6000, '0000-00-00', ''),
(1421, '', 'Dodo Silicone Nipple Deluxe Kotak', 3, 1, 'PCS', 'ALK', 17600, 20000, '0000-00-00', ''),
(1422, '', 'Parfum Oil Al Muslim HLV', 22, 3, 'BTL', 'KMTK', 7000, 10000, '0000-00-00', ''),
(1423, '', 'Dodo Nipple Deluxe S-M-L', 5, 1, 'PCS', 'ALK', 16200, 21000, '0000-00-00', ''),
(1424, '', 'Dodo Silicone Soother SA2', 2, 1, 'PCS', 'ALK', 15675, 19000, '0000-00-00', ''),
(1425, '', 'Peniti Perak Regular', 11, 3, 'PCS', 'PKRT', 35, 1000, '0000-00-00', ''),
(1426, '', 'Peniti Emas Kecil', 11, 3, 'PCS', 'PKRT', 30, 500, '0000-00-00', ''),
(1427, '', 'Sebamed Baby Cream 50ml', 4, 1, 'BTL', 'PKRT', 57000, 66000, '0000-00-00', ''),
(1428, '', 'Kaca Penny - 388', 1, 1, 'SET', 'ALK', 4583, 13000, '0000-00-00', ''),
(1429, '', 'Liquid Foundation Viva Natural 30ml', 2, 1, 'PCS', 'KMTK', 5866, 8000, '0000-00-00', ''),
(1430, '', 'Mascara Maybelline Volume Go 2in1', 1, 1, 'UNT', 'KMTK', 14666, 20000, '0000-00-00', ''),
(1431, '', 'Mascara Maybelline Sensational 2in1', 1, 1, 'UNT', 'KMTK', 14666, 20000, '0000-00-00', ''),
(1432, '', 'Iman Of Noble Eyeshadow 802', 2, 1, 'UNT', 'KMTK', 16500, 20000, '0000-00-00', ''),
(1433, '', 'Softlens Living Color Angel', 4, 1, 'UNT', 'KMTK', 27500, 35000, '0000-00-00', ''),
(1434, '', 'Pembersih Kutek Bingni 60ml', 6, 1, 'BTL', 'KMTK', 3850, 5000, '0000-00-00', ''),
(1435, '', 'Pensil Alis Sikat Loves Me', 11, 1, 'PCS', 'KMTK', 2291, 4000, '0000-00-00', ''),
(1436, '', 'Kutek Loves Me Mix Color', 2, 1, 'BTL', 'KMTK', 3850, 5000, '0000-00-00', ''),
(1437, '', 'Kutek Loves Me Bening', 2, 1, 'BTL', 'KMTK', 3575, 4000, '0000-00-00', ''),
(1438, '', 'Kutek Mahkota Mix', 2, 1, 'BTL', 'KMTK', 3500, 5000, '0000-00-00', ''),
(1439, '', 'Kutek Brasov Hitam 8ml', 1, 1, 'BTL', 'KMTK', 1760, 3000, '0000-00-00', ''),
(1440, '', 'Kutek Brasov Mix Color', 4, 1, 'BTL', 'KMTK', 5500, 7000, '0000-00-00', ''),
(1441, '', 'Kaca Penny - 333', 2, 1, 'SET', 'ALK', 4583, 11000, '0000-00-00', ''),
(1442, '', 'Gunting Kuku Bai Chang', 12, 1, 'UNT', 'ALK', 2933, 4000, '0000-00-00', ''),
(1443, '', 'Gunting Kuku WM - Kucing', 5, 1, 'UNT', 'ALK', 5041, 7000, '0000-00-00', ''),
(1444, '', 'Pinset Beauty Tools', 12, 1, 'UNT', 'ALK', 1650, 3000, '0000-00-00', ''),
(1445, '', 'Sisir Laimei', 3, 1, 'UNT', 'ALK', 3116, 4000, '0000-00-00', ''),
(1446, '', 'Sisir Twin - Cakar', 5, 1, 'UNT', 'ALK', 5500, 7000, '0000-00-00', ''),
(1447, '', 'Mascara Maybelline Colossal 2in1', 1, 1, 'UNT', 'KMTK', 14666, 20000, '0000-00-00', ''),
(1448, '', 'Sisir Fashion Ai Shu', 5, 1, 'UNT', 'ALK', 3300, 4000, '0000-00-00', ''),
(1449, '', 'Softlens BABE Exoticon', 3, 1, 'UNT', 'KMTK', 27500, 35000, '0000-00-00', ''),
(1450, '', 'Softlens MAC - Exoticon', 0, 1, 'UNT', 'KMTK', 27500, 35000, '0000-00-00', ''),
(1451, '', 'Miranda Hair Color - All Varian Color', 4, 1, 'SET', 'KMTK', 9900, 13000, '0000-00-00', ''),
(1452, '', 'Lem Bulu Mata Hei Hwa Hitam', 2, 1, 'BTL', 'KMTK', 13291, 17000, '0000-00-00', ''),
(1453, '', 'Lem Bulu Mata Hei Hwa Putih', 1, 1, 'BTL', 'KMTK', 9625, 12000, '0000-00-00', ''),
(1454, '', 'Bulu Mata Huda all type', 9, 1, 'SET', 'KMTK', 4400, 5000, '0000-00-00', ''),
(1455, '', 'Sisir Serit Aishu Kunng', 0, 1, 'PCS', 'ALK', 1650, 30000, '0000-00-00', ''),
(1456, '', 'Sisir Serit Kutu Merah', 3, 1, 'PCS', 'ALK', 5500, 7000, '0000-00-00', ''),
(1457, '', 'Manicure Set Karakter', 3, 1, 'SET', 'KMTK', 8250, 15000, '0000-00-00', ''),
(1458, '', 'Make Up Tools CherVeen', 3, 1, 'SET', 'KMTK', 12833, 15000, '0000-00-00', ''),
(1459, '', 'Spon Busa (warna) Beauty Blender', 3, 1, 'UNT', 'KMTK', 5041, 7000, '0000-00-00', ''),
(1460, '', 'Puff Cherveen Coklat Regular', 6, 1, 'PCS', 'KMTK', 4400, 6000, '0000-00-00', ''),
(1461, '', 'Roll Rambut Kecil 10\'s 25mm', 4, 1, 'PCK', 'KMTK', 15583, 18000, '0000-00-00', ''),
(1462, '', 'Kamper Swallow Putih 100g', 2, 1, 'PCK', 'ALK', 10582, 12000, '0000-00-00', ''),
(1463, '', 'Kamper Swallow Jumbo 5\'s', 2, 1, 'PCK', 'ALK', 15290, 17000, '0000-00-00', ''),
(1464, '', 'Kantong Asi - Baby One', 60, 3, 'PCS', 'PKRT', 1066, 2000, '0000-00-00', ''),
(1465, '', 'Kantong Urin Dws', 20, 3, 'SET', 'ALK', 4000, 6000, '0000-00-00', ''),
(1466, '', 'Viva Face Tonic Bengkoang 100ml', 2, 1, 'BTL', 'KMTK', 5722, 7000, '0000-00-00', ''),
(1467, '', 'Roll Rambut Sedang 8\'s 30 mm', 3, 1, 'PCK', 'KMTK', 15583, 19000, '0000-00-00', ''),
(1468, '', 'Roll Rambut Besar 6\'s 36 mm', 4, 1, 'PCK', 'KMTK', 16500, 20000, '0000-00-00', ''),
(1469, '', 'Lipstick MAC - A64', 12, 1, 'UNT', 'KMTK', 4583, 8000, '0000-00-00', ''),
(1470, '', 'Blusher Shining Dodo Girl', 2, 1, 'SET', 'KMTK', 15125, 18000, '0000-00-00', ''),
(1471, '', 'Sisir Semir Hitam Polos', 6, 1, 'UNT', 'KMTK', 2231, 3000, '0000-00-00', ''),
(1472, '', 'Mangkok Semir Hitam Besar', 4, 1, 'PCS', 'KMTK', 2750, 3500, '0000-00-00', ''),
(1473, '', 'Jas Hujan Sekali Pakai Transparan', 12, 1, 'UNT', 'ALK', 2090, 5000, '0000-00-00', ''),
(1474, '', 'Make Up Tools Naked7', 2, 1, 'SET', 'KMTK', 14666, 18000, '0000-00-00', ''),
(1475, '', 'Kuas Lipstik 6 warna Reguler', 12, 1, 'UNT', 'KMTK', 3850, 4500, '0000-00-00', ''),
(1476, '', 'Eyeshadow Dodo Girl D3109', 2, 1, 'SET', 'KMTK', 17416, 20000, '0000-00-00', ''),
(1477, '', 'Pelembab Viva Reguler 30 ml', 4, 1, 'BTL', 'KMTK', 6783, 8000, '0000-00-00', ''),
(1478, '', 'Liquid Foundation Viva Kuning Langsat 30 ml', 2, 1, 'BTL', 'KMTK', 5866, 8000, '0000-00-00', ''),
(1479, '', 'Iman Of Noble Eyebrow 8381', 2, 1, 'UNT', 'KMTK', 12100, 15000, '0000-00-00', ''),
(1480, '', 'Kotak P3K', 2, 1, 'PCS', 'ALK', 16500, 20000, '0000-00-00', ''),
(1481, '', 'Liptint Huda Velvet', 4, 1, 'PCS', 'KMTK', 5958, 10000, '0000-00-00', ''),
(1482, '', 'Serutan Pensil Alis 2 Lubang', 6, 1, 'UNT', 'KMTK', 2200, 3000, '0000-00-00', ''),
(1483, '', 'Makeup Brush Yizhilian (Pink) 9078', 6, 1, 'SET', 'KMTK', 5500, 8000, '0000-00-00', ''),
(1484, '', 'Kuas Blush On Yizhilian', 4, 1, 'UNT', 'KMTK', 6600, 10000, '0000-00-00', ''),
(1485, '', 'Lip Tint Iman Of Noble', 6, 1, 'PCS', 'KMTK', 5958, 10000, '0000-00-00', ''),
(1486, '', 'Carasun Solar UV 8ml', 0, 1, 'SCH', 'KMTK', 13387, 17000, '0000-00-00', ''),
(1487, '', 'Rojukiss Serum Tea Tree Bija 8ml', 3, 1, 'SCH', 'KMTK', 14917, 19000, '0000-00-00', ''),
(1488, '', 'Rojukiss Serum Eggplant 8ml', 2, 1, 'SCH', 'KMTK', 14917, 19000, '0000-00-00', ''),
(1489, '', 'Rojukiss Serum Orange C+ 8ml', 3, 1, 'SCH', 'KMTK', 14917, 19000, '0000-00-00', ''),
(1490, '', 'Rojukiss Serum Jeju Lotus 8ml', 2, 1, 'SCH', 'KMTK', 14917, 19000, '0000-00-00', ''),
(1491, '', 'Pepsodent Pasta Gigi Active White 75gr', 0, 2, 'TUB', 'PKRT', 8910, 12000, '0000-00-00', ''),
(1492, '', 'Ciptadent Fresh Mint 190gr', 6, 2, 'TUB', 'PKRT', 10120, 13000, '0000-00-00', ''),
(1493, '', 'Ciptadent Cool Mint 190gr', 3, 2, 'TUB', 'PKRT', 10120, 13000, '0000-00-00', ''),
(1494, '', 'Gelang Pasien Uk. Dewasa', 100, 3, 'PCS', 'ALK', 770, 1500, '0000-00-00', ''),
(1495, '', 'Gelang Pasien Uk. Anak', 100, 3, 'PCS', 'ALK', 880, 2000, '0000-00-00', ''),
(1496, '', 'Penyangga Tangan (KENIKO) S-M-XL', 10, 2, 'BOX', 'ALK', 16500, 22000, '0000-00-00', ''),
(1497, '', 'Kapur Ajaib ? BAGUS', 12, 3, 'PCS', 'ALK', 3712, 4000, '0000-00-00', ''),
(1498, '', 'Kapur Ajaib ? BAGUS JUMBO', 14, 3, 'PCS', 'ALK', 3712, 4500, '0000-00-00', ''),
(1499, '', 'Termometer AVICO lentur', 7, 2, 'PCS', 'ALK', 20000, 35000, '0000-00-00', ''),
(1500, '', 'Pepsodent White 120 g Merah', 13, 1, 'PCS', 'OTC1', 7225, 9000, '0000-00-00', ''),
(1501, '', 'Nurse Cap (Topi Perawat) 100\'s', 0, 1, 'BOX', 'ALK', 40000, 52000, '0000-00-00', ''),
(1502, '', 'Durex Extra Safe 3\'s', 2, 1, 'BOX', 'ALK', 19675, 23000, '0000-00-00', ''),
(1503, '', 'Durex Extra Safe 12\'s', 1, 1, 'BOX', 'ALK', 62739, 73000, '0000-00-00', ''),
(1504, '', 'Kantong Kompres Besar', 3, 1, 'PCS', 'ALK', 21000, 28000, '0000-00-00', ''),
(1505, '', 'Salonpas Pain Relief Patch', 0, 3, 'PCS', 'OTC3', 22887, 28000, '0000-00-00', ''),
(1506, '', 'Selection Cotton Buds Baby 100\'s', 7, 3, 'SCH', 'ALK', 0, 0, '0000-00-00', ''),
(1507, '', 'DODO Cotton Bud Reguler 125s', 2, 1, 'POT', 'PKRT', 8800, 10000, '0000-00-00', ''),
(1508, '', 'DODO Cotton Bud Reguler 128s', 2, 1, 'POT', 'PKRT', 6050, 7000, '0000-00-00', ''),
(1509, '', 'DODO Cotton Bud Baby 136s', 2, 1, 'POT', 'ALK', 8910, 10000, '0000-00-00', ''),
(1510, '', 'DODO Cotton Bud Baby 138s', 2, 1, 'POT', 'ALK', 5885, 7000, '0000-00-00', ''),
(1511, '', 'DODO Cotton Bud Dwifungsi 146s', 2, 1, 'POT', 'PKRT', 26400, 29000, '0000-00-00', ''),
(1512, '', 'DODO Cotton Bud Dwifungsi 148s', 2, 1, 'POT', 'PKRT', 17325, 20000, '0000-00-00', ''),
(1513, '', 'DODO Cotton Bud Kosmetik 158s', 2, 1, 'POT', 'PKRT', 10120, 12000, '0000-00-00', ''),
(1514, '', 'Huki Cotton Bud Extra Fine Pot 100\'s', 2, 1, 'POT', 'ALK', 0, 0, '0000-00-00', ''),
(1515, '', 'Huki Cotton Bud Baby Pot 80\'s', 4, 1, 'POT', 'ALK', 0, 0, '0000-00-00', ''),
(1516, '', 'Gillette Biru', 24, 3, 'PCS', 'ALK', 7300, 8500, '0000-00-00', ''),
(1517, '', 'Gillette Kuning', 24, 3, 'PCS', 'ALK', 6500, 7500, '0000-00-00', ''),
(1518, '', 'Pepsodent Complete 8 75gr Biru', 13, 2, 'TUB', 'PKRT', 8400, 10000, '0000-00-00', ''),
(1519, '', 'Tancho 20ml + 20ml', 1, 1, 'BOX', 'PKRT', 17500, 20000, '0000-00-00', ''),
(1520, '', 'Tancho 6gr', 2, 1, 'BOX', 'PKRT', 13000, 16000, '0000-00-00', ''),
(1521, '', 'Klarens Handsanitizer 60 ml', 2, 1, 'BTL', 'PKRT', 10000, 12000, '0000-00-00', ''),
(1522, '', 'Hansaplast Fancy 10\'s Karakter', 2, 3, 'SCH', 'PKRT', 3000, 5000, '0000-00-00', ''),
(1523, '', 'Antiseptik 44 70% 100ml', 5, 3, 'BTL', 'PKRT', 2291, 5000, '0000-00-00', ''),
(1524, '', 'Caladine Medicated Powder 60gr', 1, 1, 'BTL', 'PKRT', 9900, 12000, '0000-00-00', ''),
(1525, '', 'Jasa Resep Buat Sirup', 0, 0, 'UNT', 'JASA', 0, 5000, '0000-00-00', ''),
(1526, '', 'Cek Gula Darah', 28, 3, 'UNT', 'ALK', 2800, 10000, '0000-00-00', ''),
(1527, '', 'Cek Asam Urat', 80, 3, 'UNT', 'ALK', 3520, 10000, '0000-00-00', ''),
(1528, '', 'Cek Kolesterol', 4, 3, 'UNT', 'ALK', 14832, 20000, '0000-00-00', ''),
(1529, '', 'NaCl 0,9% 500 ml kimia farma', 8, 3, 'UNT', 'ALK', 10000, 15000, '0000-00-00', ''),
(1530, '', 'Ringer Lactat B Braun', 0, 0, 'BTL', 'ALK', 10500, 15000, '0000-00-00', ''),
(1531, '', 'Jasa Resep Non Racikan', 0, 0, 'UNT', 'JASA', 0, 6000, '0000-00-00', ''),
(1532, '', 'GEA infusion set adult (biru)', 10, 3, 'PCS', 'ALK', 2900, 8000, '0000-00-00', ''),
(1533, '', 'GEA infusion set child (hijau)', 7, 3, 'PCS', 'ALK', 4000, 8000, '0000-00-00', ''),
(1534, '', 'Underpad', 1, 2, 'PCS', 'ALK', 24000, 35000, '0000-00-00', ''),
(1535, '', 'NaCl Otsu', 0, 3, 'BTL', 'ETC2', 10000, 15000, '0000-00-00', ''),
(1536, '', 'Aseptic Onemed 500ml pump', 1, 1, 'BTL', 'PKRT', 32000, 45000, '0000-00-00', ''),
(1537, '', 'Antiseptik 70% 1000ml', 1, 3, 'BTL', 'PKRT', 18000, 30000, '0000-00-00', ''),
(1538, '', 'Dextrose 5%', 0, 3, 'BTL', 'ETC2', 10000, 20000, '0000-00-00', ''),
(1539, '', 'Catheter 20 (pink)', 13, 3, 'UNT', 'ALK', 3700, 8000, '0000-00-00', ''),
(1540, '', 'Catheter 22 (biru)', 1, 3, 'UNT', 'ALK', 3700, 8000, '0000-00-00', ''),
(1541, '', 'Catheter 24 (kuning)', 14, 3, 'UNT', 'ALK', 3700, 8000, '0000-00-00', ''),
(1542, '', 'Tourniquet', 14, 3, 'PCS', 'ALK', 12000, 20000, '0000-00-00', ''),
(1543, '', '3 way Onemed', 13, 3, 'PCS', 'ALK', 8000, 15000, '0000-00-00', ''),
(1544, '', 'Plastik Klip 8,7 x 13', -2, 0, 'BAG', 'ALK', 0, 7000, '0000-00-00', ''),
(1545, '', 'Carnela Disinfectant Spray 225ml (Konsinasi)', 6, 2, 'UNT', 'PKRT', 19000, 25000, '0000-00-00', ''),
(1546, '', 'Promo Vitcur', 2, 0, 'BTL', 'OTC2', 5000, 5000, '0000-00-00', ''),
(1547, '', 'Promo Omevita', 0, 0, 'BTL', 'OTC2', 5000, 5000, '0000-00-00', ''),
(1548, '', 'Promo Mezinex', 2, 0, 'BTL', 'OTC2', 5000, 5000, '0000-00-00', ''),
(1549, '', 'Promo Enkasari', 3, 0, 'BTL', 'HRB2', 18000, 18000, '0000-00-00', ''),
(1550, '', 'Promo Thrombophob ointmen', 2, 0, 'TUB', 'OTC3', 44000, 44000, '0000-00-00', ''),
(1551, '', 'Promo Hico Gel', 3, 0, 'TUB', 'OTC3', 15000, 15000, '0000-00-00', ''),
(1552, '', 'Promo Starlax', 1, 0, 'BTL', 'OTC2', 19000, 19000, '0000-00-00', ''),
(1553, '', 'Promo Stimuno Original', 27, 0, 'BTL', 'OTC2', 25000, 25000, '0000-00-00', ''),
(1554, '', 'Promo Tempra 60 ml', 0, 0, 'BTL', 'OTC2', 39000, 39000, '0000-00-00', ''),
(1555, '', 'Promo Stimuno Grape', 2, 0, 'BTL', 'OTC2', 25000, 25000, '0000-00-00', ''),
(1556, '', 'Promo Tempra 30 ml', 0, 0, 'BTL', 'OTC2', 20000, 20000, '0000-00-00', ''),
(1557, '', 'Promo Woods Herbal 60 ml', 2, 0, 'BTL', 'OTC2', 17000, 17000, '0000-00-00', ''),
(1558, '', 'Promo Herba KOF stick', 15, 0, 'STC', 'OTC2', 2000, 2000, '0000-00-00', ''),
(1559, '', 'Promo Panadol syr', 1, 0, 'BTL', 'OTC2', 45000, 45000, '0000-00-00', ''),
(1560, '', 'Promo Zambuk 8 gr', 0, 0, 'POT', 'OTC3', 15000, 15000, '0000-00-00', ''),
(1561, '', 'Promo Devosix drops', 0, 0, 'BTL', 'OTC2', 12000, 12000, '0000-00-00', ''),
(1562, '', 'Promo Pure Touch', 1, 0, 'BTL', 'PKRT', 10000, 10000, '0000-00-00', ''),
(1563, '', 'Promo Enzyplex', 0, 0, 'STRIP', 'OTC1', 7000, 7000, '0000-00-00', ''),
(1564, '', 'Promo Zee 350 g', 0, 0, 'BOX', 'MNMK', 36000, 36000, '0000-00-00', ''),
(1565, '', 'Promo Johnson Powder 300 g', 0, 0, 'BTL', 'PKRT', 15000, 15000, '0000-00-00', ''),
(1566, '', 'Promo Bepanthen Oint 20 g', 5, 0, 'TUB', 'OTC3', 56000, 56000, '0000-00-00', ''),
(1567, '', 'Promo Komix Peppermint', 16, 0, 'SCH', 'OTC2', 1500, 1500, '0000-00-00', ''),
(1568, '', 'Promo Nano nano', 0, 0, 'PCS', 'OTC1', 8000, 8000, '0000-00-00', ''),
(1569, '', 'Promo Kis', 1, 0, 'PCS', 'OTC1', 25000, 25000, '0000-00-00', ''),
(1570, '', 'Promo Frozz', 4, 0, 'PCS', 'OTC1', 15000, 15000, '0000-00-00', ''),
(1571, '', 'Selvim 20', 80, 10, 'TAB', 'ETC1', 472, 1500, '0000-00-00', ''),
(1572, '', 'Celana Sunat  XL, M, S, L', 3, 2, 'UNT', 'ALK', 10000, 19000, '0000-00-00', ''),
(1573, '', 'Paseo Cleansing Wipes 50\'s', 0, 1, 'BAG', 'PKRT', 8550, 10000, '0000-00-00', '');

-- --------------------------------------------------------

--
-- Table structure for table `carabayar`
--

CREATE TABLE `carabayar` (
  `id_carabayar` int(11) NOT NULL,
  `nm_carabayar` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carabayar`
--

INSERT INTO `carabayar` (`id_carabayar`, `nm_carabayar`) VALUES
(1, 'TUNAI'),
(2, 'DEBIT BCA'),
(3, 'DEBIT MANDIRI');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_obat`
--

CREATE TABLE `jenis_obat` (
  `idjenis` int(50) NOT NULL,
  `jenisobat` varchar(50) CHARACTER SET latin1 NOT NULL,
  `ket` varchar(50) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis_obat`
--

INSERT INTO `jenis_obat` (`idjenis`, `jenisobat`, `ket`) VALUES
(1, 'OTC1', 'Obat Bebas Sediaan Padat'),
(2, 'OTC2', 'Obat Bebas Sediaan Cair'),
(3, 'OTC3', 'Obat Bebas untuk Kulit'),
(4, 'OTC4', 'Obat Bebas khusus Konsinyasi'),
(5, 'ETC1', 'Obat Resep Sediaan Padat'),
(6, 'ETC2', 'Obat Resep Sediaan Cair'),
(7, 'ETC3', 'Obat Resep Sediaan Kulit'),
(8, 'ETC4', 'Obat Resep Sediaan Injeksi'),
(9, 'ETC5', 'Obat Resep Sediaan Suppo dan Ovula'),
(10, 'ETC6', 'Obat Resep Khusus Konsinyasi'),
(11, 'HRB1', 'Obat Tradisional sediaan padat'),
(12, 'HRB2', 'Obat Tradisional sediaan cair'),
(13, 'ALK', 'Alat Kesehatan'),
(14, 'PKRT', 'Perlengkapan Kesehatan Rumah Tangga'),
(15, 'SUSU', 'SUSU'),
(16, 'KONS', 'Barang Titipan '),
(17, 'KMTK', 'Kosmetika'),
(18, 'MNMK', 'Minuman dan Makanan'),
(19, 'test', 'baru');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kdbm`
--

INSERT INTO `kdbm` (`id_kdbm`, `kd_trbmasuk`, `id_resto`, `id_admin`, `stt_kdbm`) VALUES
(1, 'BMP-090222031308', 'pusat', 1, 'OFF'),
(2, 'BMP-180222081836', 'pusat', 1, 'OFF'),
(3, 'BMP-050322110700', 'pusat', 1, 'ON');

-- --------------------------------------------------------

--
-- Table structure for table `kdtk`
--

CREATE TABLE `kdtk` (
  `id_kdtk` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `stt_kdtk` varchar(3) NOT NULL DEFAULT 'ON'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kdtk`
--

INSERT INTO `kdtk` (`id_kdtk`, `kd_trkasir`, `id_admin`, `stt_kdtk`) VALUES
(1, 'TKP-220122114118', 1, 'OFF'),
(2, 'TKP-090222035252', 1, 'OFF'),
(3, 'TKP-090422085847', 1, 'OFF');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nm_pelanggan`, `tlp_pelanggan`, `alamat_pelanggan`, `ket_pelanggan`) VALUES
(1, 'Pelanggan 1', '1290', 'JL.Tridaya', ''),
(2, 'Pelanggan 2', '901', 'alamat pelanggan 2', ''),
(3, 'UMUM', '-', '-', '');

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` int(11) NOT NULL,
  `nm_satuan` varchar(50) NOT NULL,
  `deskripsi` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nm_satuan`, `deskripsi`) VALUES
(50, 'BAG', 'Kantong Besar'),
(51, 'BOX', 'Kardus'),
(52, 'BTL', 'Wadah Cairan'),
(53, 'KLG', 'Wadah Kaleng'),
(54, 'PACK', 'Wadah Bundling'),
(55, 'PCS', 'Pieces'),
(56, 'POT', 'Pot plastik / mika'),
(57, 'SCHT', 'Lembaran'),
(58, 'STRIP', 'Wadah Lembaran'),
(59, 'TUBE', 'Sediaan Krim/salep'),
(60, 'TAB', 'Tablet'),
(61, 'AMP', 'Sediaan Injeksi'),
(66, 'Stick', 'Stick');

-- --------------------------------------------------------

--
-- Table structure for table `setheader`
--

CREATE TABLE `setheader` (
  `id_setheader` int(11) NOT NULL,
  `satu` varchar(111) NOT NULL DEFAULT 'WWW.BUTIKWALLPAPER.COM',
  `dua` varchar(111) NOT NULL DEFAULT 'Spesialis Wallpaper Dinding',
  `tiga` varchar(111) NOT NULL DEFAULT 'CABANG',
  `empat` varchar(111) NOT NULL,
  `lima` varchar(111) NOT NULL,
  `enam` varchar(111) NOT NULL DEFAULT 'HP / WA : 0812 7277 6181 / 0813 3866 0225',
  `tujuh` varchar(111) DEFAULT NULL,
  `delapan` varchar(100) NOT NULL DEFAULT 'Terima Kasih Semoga Tetap Jadi Langganan',
  `sembilan` varchar(100) NOT NULL DEFAULT 'PERHATIAN !!!',
  `sepuluh` varchar(100) NOT NULL DEFAULT 'Barang yang sudah dibeli tidak dapat ditukar',
  `sebelas` varchar(100) NOT NULL DEFAULT 'atau dikembalikan kecuali ada perjanjian'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setheader`
--

INSERT INTO `setheader` (`id_setheader`, `satu`, `dua`, `tiga`, `empat`, `lima`, `enam`, `tujuh`, `delapan`, `sembilan`, `sepuluh`, `sebelas`) VALUES
(1, 'APOTIK SEGER WARAS', 'Spesialis Generik', 'BEKASI: JL. Tri Satya No.22 Rawa Lumbu - Bekasi', 'Telp : 021-82438554', 'HP / WA : 0812 7277 6181 / 0813 3866 0225', '', '', 'Terima Kasih Semoga Tetap Jadi Langganan', 'PERHATIAN !!!', 'Barang yang sudah dibeli tidak dapat ditukar', 'atau dikembalikan kecuali ada perjanjian');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nm_supplier`, `tlp_supplier`, `alamat_supplier`, `ket_supplier`) VALUES
(1, 'PT OBAT GENERIK', '02189096754', 'JL MM2100 Cibitung Kabupaten Bekasi', ''),
(2, 'PT SURYA SEMBUH CEPAT', '0216785342', 'JL.Cakung utra No 12 Jakarta Utara', ''),
(3, 'PT Pradipta Cakrawala Pasific', '083807577010', 'JL Balai Pustaka Timur, No. 39, Blok F/3, Rawamangun, Jakarta Timur, DKI Jakarta, Indonesia', 'Distributor khusus Produk PT Ifars');

-- --------------------------------------------------------

--
-- Table structure for table `trbmasuk`
--

CREATE TABLE `trbmasuk` (
  `id_trbmasuk` int(11) NOT NULL,
  `id_resto` varchar(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `tgl_trbmasuk` date NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `nm_supplier` varchar(50) NOT NULL,
  `tlp_supplier` varchar(50) NOT NULL,
  `alamat_trbmasuk` text NOT NULL,
  `ttl_trbmasuk` double NOT NULL,
  `dp_bayar` double NOT NULL,
  `sisa_bayar` double NOT NULL,
  `ket_trbmasuk` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trbmasuk`
--

INSERT INTO `trbmasuk` (`id_trbmasuk`, `id_resto`, `kd_trbmasuk`, `tgl_trbmasuk`, `id_supplier`, `nm_supplier`, `tlp_supplier`, `alamat_trbmasuk`, `ttl_trbmasuk`, `dp_bayar`, `sisa_bayar`, `ket_trbmasuk`) VALUES
(2, 'pusat', 'BMP-180222081836', '2022-02-18', 3, 'PT Pradipta Cakrawala Pasific', '083807577010', 'JL Balai Pustaka Timur, No. 39, Blok F/3, Rawamangun, Jakarta Timur, DKI Jakarta, Indonesia', 413630, 413630, 0, 'Order Perdana');

-- --------------------------------------------------------

--
-- Table structure for table `trbmasuk_detail`
--

CREATE TABLE `trbmasuk_detail` (
  `id_dtrbmasuk` int(11) NOT NULL,
  `kd_trbmasuk` varchar(100) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `kd_barang` varchar(50) NOT NULL,
  `nmbrg_dtrbmasuk` varchar(100) NOT NULL,
  `qty_dtrbmasuk` double NOT NULL,
  `sat_dtrbmasuk` varchar(30) NOT NULL,
  `hrgsat_dtrbmasuk` double NOT NULL,
  `hrgttl_dtrbmasuk` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trbmasuk_detail`
--

INSERT INTO `trbmasuk_detail` (`id_dtrbmasuk`, `kd_trbmasuk`, `id_barang`, `kd_barang`, `nmbrg_dtrbmasuk`, `qty_dtrbmasuk`, `sat_dtrbmasuk`, `hrgsat_dtrbmasuk`, `hrgttl_dtrbmasuk`) VALUES
(6, 'BMP-180222081836', 1, '4001', 'Salonpas Hijau 12 x 1 lembar', 10, 'SCH', 6363, 63630),
(7, 'BMP-180222081836', 2, '4002', 'Leucoplast 2,5 x 4,5', 10, 'PCS', 15000, 150000),
(8, 'BMP-180222081836', 3, '4003', 'Termometer Onemed', 10, 'UNT', 20000, 200000);

-- --------------------------------------------------------

--
-- Table structure for table `trkasir`
--

CREATE TABLE `trkasir` (
  `id_trkasir` int(11) NOT NULL,
  `kd_trkasir` varchar(100) NOT NULL,
  `tgl_trkasir` date NOT NULL,
  `nm_pelanggan` varchar(100) NOT NULL,
  `tlp_pelanggan` varchar(50) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `ttl_trkasir` double NOT NULL,
  `dp_bayar` double NOT NULL,
  `sisa_bayar` double NOT NULL,
  `ket_trkasir` text NOT NULL,
  `id_carabayar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trkasir`
--

INSERT INTO `trkasir` (`id_trkasir`, `kd_trkasir`, `tgl_trkasir`, `nm_pelanggan`, `tlp_pelanggan`, `alamat_pelanggan`, `ttl_trkasir`, `dp_bayar`, `sisa_bayar`, `ket_trkasir`, `id_carabayar`) VALUES
(2, 'TKP-090222035252', '2022-04-03', 'UMUM/-', '', '', 60000, 60000, 0, '', 1),
(3, 'TKP-090422085847', '2022-04-11', 'UMUM/-', '', '', 60000, 60000, 0, '', 1);

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
  `hrgttl_dtrkasir` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trkasir_detail`
--

INSERT INTO `trkasir_detail` (`id_dtrkasir`, `kd_trkasir`, `id_barang`, `kd_barang`, `nmbrg_dtrkasir`, `qty_dtrkasir`, `sat_dtrkasir`, `hrgjual_dtrkasir`, `hrgttl_dtrkasir`) VALUES
(4, 'TKP-090222035252', 2, '', 'Leucoplast 2,5 x 4,5', 2, 'PCS', 30000, 60000),
(10, 'TKP-090422085847', 1, '4001', 'Salonpas Hijau 12 x 1 lembar', 1, 'SCH', 10000, 10000),
(11, 'TKP-090422085847', 2, '4002', 'Leucoplast 2,5 x 4,5', 1, 'PCS', 20000, 20000),
(12, 'TKP-090422085847', 3, '4003', 'Termometer Onemed', 1, 'UNT', 30000, 30000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `carabayar`
--
ALTER TABLE `carabayar`
  ADD PRIMARY KEY (`id_carabayar`);

--
-- Indexes for table `jenis_obat`
--
ALTER TABLE `jenis_obat`
  ADD PRIMARY KEY (`idjenis`);

--
-- Indexes for table `kdbm`
--
ALTER TABLE `kdbm`
  ADD PRIMARY KEY (`id_kdbm`);

--
-- Indexes for table `kdtk`
--
ALTER TABLE `kdtk`
  ADD PRIMARY KEY (`id_kdtk`);

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
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `trbmasuk`
--
ALTER TABLE `trbmasuk`
  ADD PRIMARY KEY (`id_trbmasuk`);

--
-- Indexes for table `trbmasuk_detail`
--
ALTER TABLE `trbmasuk_detail`
  ADD PRIMARY KEY (`id_dtrbmasuk`);

--
-- Indexes for table `trkasir`
--
ALTER TABLE `trkasir`
  ADD PRIMARY KEY (`id_trkasir`);

--
-- Indexes for table `trkasir_detail`
--
ALTER TABLE `trkasir_detail`
  ADD PRIMARY KEY (`id_dtrkasir`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1575;

--
-- AUTO_INCREMENT for table `carabayar`
--
ALTER TABLE `carabayar`
  MODIFY `id_carabayar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jenis_obat`
--
ALTER TABLE `jenis_obat`
  MODIFY `idjenis` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `kdbm`
--
ALTER TABLE `kdbm`
  MODIFY `id_kdbm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kdtk`
--
ALTER TABLE `kdtk`
  MODIFY `id_kdtk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `setheader`
--
ALTER TABLE `setheader`
  MODIFY `id_setheader` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trbmasuk`
--
ALTER TABLE `trbmasuk`
  MODIFY `id_trbmasuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trbmasuk_detail`
--
ALTER TABLE `trbmasuk_detail`
  MODIFY `id_dtrbmasuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trkasir`
--
ALTER TABLE `trkasir`
  MODIFY `id_trkasir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trkasir_detail`
--
ALTER TABLE `trkasir_detail`
  MODIFY `id_dtrkasir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
