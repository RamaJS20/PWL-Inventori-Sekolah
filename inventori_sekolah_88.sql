-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 04:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventori_sekolah_88`
--

-- --------------------------------------------------------

--
-- Table structure for table `aksi_barang`
--

CREATE TABLE `aksi_barang` (
  `id_aksi` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_barang` varchar(10) DEFAULT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `harga` int(11) UNSIGNED NOT NULL,
  `total_harga` int(11) UNSIGNED NOT NULL,
  `aksi` enum('Masuk','Keluar','Rusak') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aksi_barang`
--

INSERT INTO `aksi_barang` (`id_aksi`, `timestamp`, `id_barang`, `qty`, `satuan`, `harga`, `total_harga`, `aksi`, `keterangan`, `pic`) VALUES
('STM88/IN/20250805/0001', '2025-08-04 17:11:52', 'KMP001', 20, 'set', 4500000, 90000000, 'Masuk', 'test dummy data', 'EL'),
('STM88/IN/20250805/0002', '2025-08-04 17:12:25', 'MK001', 20, 'set', 500000, 10000000, 'Masuk', 'test dummy data', 'EL'),
('STM88/IN/20250805/0003', '2025-08-04 17:29:51', 'PT001', 5, 'unit', 300000, 1500000, 'Masuk', 'Test Dummy Data', 'EL');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `satuan_unit` enum('pcs','botol','lembar','unit','buah','set') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `deskripsi`, `satuan_unit`) VALUES
('KMP001', 'Bundling Komputer Intel Core i3 gen 8 - 8/256gb', 'Komputer Intel Core i3 gen 8 - 8/256gb include Monitor, Keyboard dan Mouse', 'set'),
('MK001', 'Meja Kursi Plywood Steel', 'Paket Meja Kursi Plywood Steel 70 x 45 x 75 cm untuk komputer', 'set'),
('PRJ001', 'Proyektor Infocus IN124 Brightness 3200 AnsiLumens HDMI', 'Native Resolution1024 x 768 PixelMaximum Resolution1920 x 1200 PixelNative Aspect Ratio4:3Compatible Aspect Ratio16:10Contrast Ratio4:1Projection SystemDLP', 'unit'),
('PT001', 'Papan Tulis Whiteboard Sekolah 120 x 200cm', 'Papan tulis Whiteboard Sekolah 120 x 200cm dengan kaki besi dapat digeser', 'unit'),
('RB001', 'Informa Rak Buku 203x45x223 cm', 'Informa Rak Buku 203x45x223 cm bahan Plywood rangka besi ', 'unit');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `status` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aksi_barang`
--
ALTER TABLE `aksi_barang`
  ADD PRIMARY KEY (`id_aksi`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aksi_barang`
--
ALTER TABLE `aksi_barang`
  ADD CONSTRAINT `aksi_barang_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
