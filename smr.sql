-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 04, 2025 at 05:19 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smr`
--

-- --------------------------------------------------------

--
-- Table structure for table `dampak_risiko`
--

CREATE TABLE `dampak_risiko` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dampak_risiko`
--

INSERT INTO `dampak_risiko` (`id`, `name`, `value`) VALUES
(1, 'Tidak Signifikan', 1),
(2, 'Kecil', 2),
(3, 'Sedang', 3),
(4, 'Besar', 4),
(5, 'Katastropik', 5);

-- --------------------------------------------------------

--
-- Table structure for table `direktorat`
--

CREATE TABLE `direktorat` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `direktorat`
--

INSERT INTO `direktorat` (`id`, `name`) VALUES
(1, 'Direktur Utama'),
(2, 'Direktur Medik dan Keperawatan'),
(3, 'Direktur SDM Pendidikan dan Penelitian'),
(4, 'Direktur Perencanaan dan Keuangan'),
(5, 'Direktur Layanan Operasional');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_risiko`
--

CREATE TABLE `kategori_risiko` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_risiko`
--

INSERT INTO `kategori_risiko` (`id`, `name`) VALUES
(1, 'Keuangan'),
(2, 'Kebijakan'),
(3, 'Reputasi'),
(4, 'Fraud'),
(5, 'Legal'),
(6, 'Kepatuhan'),
(7, 'Operasional');

-- --------------------------------------------------------

--
-- Table structure for table `kemungkinan_risiko`
--

CREATE TABLE `kemungkinan_risiko` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kemungkinan_risiko`
--

INSERT INTO `kemungkinan_risiko` (`id`, `name`, `value`) VALUES
(1, 'Jarang', 1),
(2, 'Kemungkinan Kecil', 2),
(3, 'Kemungkinan Sedang ', 3),
(4, 'Kemungkinan Besar', 4),
(5, 'Hampir Pasti Terjadi', 5);

-- --------------------------------------------------------

--
-- Table structure for table `pemantauan_reviu`
--

CREATE TABLE `pemantauan_reviu` (
  `id` int(11) NOT NULL,
  `penilaian_risiko_id` int(11) NOT NULL,
  `p` int(1) DEFAULT NULL,
  `d` int(1) DEFAULT NULL,
  `bobot` double DEFAULT NULL,
  `nilai` int(2) DEFAULT NULL,
  `tingkat_risiko_id` int(11) DEFAULT NULL,
  `simpulan_tingkat_risiko_id` int(11) DEFAULT NULL,
  `efektif` enum('efektif','tidak efektif') DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemantauan_reviu`
--

INSERT INTO `pemantauan_reviu` (`id`, `penilaian_risiko_id`, `p`, `d`, `bobot`, `nilai`, `tingkat_risiko_id`, `simpulan_tingkat_risiko_id`, `efektif`, `is_verified`, `notes`, `verified_at`, `updated_at`) VALUES
(15, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-05-22 12:00:17'),
(16, 31, 3, 2, 1.42, 9, 3, 2, 'efektif', NULL, NULL, '2025-05-22 16:39:57', '2025-05-22 19:19:12'),
(17, 32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-05-22 16:12:31', '2025-05-22 16:12:31'),
(18, 35, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-05-22 12:09:13'),
(19, 36, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-05-22 12:10:05'),
(20, 37, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-05-22 12:39:37'),
(21, 38, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '2025-05-22 13:06:42');

-- --------------------------------------------------------

--
-- Table structure for table `penilaian_risiko`
--

CREATE TABLE `penilaian_risiko` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `risiko` text DEFAULT NULL,
  `kategori_risiko_id` int(10) DEFAULT NULL,
  `sebab` text DEFAULT NULL,
  `sumber_risiko` enum('internal','eksternal') DEFAULT NULL,
  `cuc` enum('c','uc') DEFAULT NULL,
  `dampak` text DEFAULT NULL,
  `uraian_pengendalian` text DEFAULT NULL,
  `efektif_pengendalian` enum('efektif','tidak efektif') DEFAULT NULL,
  `p_analisis` double DEFAULT NULL,
  `d_analisis` double DEFAULT NULL,
  `bobot_analisis` double DEFAULT NULL,
  `nilai_analisis` double DEFAULT NULL,
  `tingkat_risiko_analisis_id` int(11) DEFAULT NULL,
  `prioritas_risiko_id` int(11) DEFAULT NULL,
  `selera_risiko` enum('dalam batas selera risiko','diatas batas selera risiko') DEFAULT NULL,
  `pilihan_penanganan` enum('mitigasi risiko','menerima risiko') DEFAULT NULL,
  `uraian_penanganan` text DEFAULT NULL,
  `jadwal_pelaksanaan` varchar(255) DEFAULT NULL,
  `p_target` double DEFAULT NULL,
  `d_target` double DEFAULT NULL,
  `bobot_target` double DEFAULT NULL,
  `nilai_target` double DEFAULT NULL,
  `tingkat_risiko_target_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT NULL,
  `verified_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `document` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penilaian_risiko`
--

INSERT INTO `penilaian_risiko` (`id`, `unit_id`, `risiko`, `kategori_risiko_id`, `sebab`, `sumber_risiko`, `cuc`, `dampak`, `uraian_pengendalian`, `efektif_pengendalian`, `p_analisis`, `d_analisis`, `bobot_analisis`, `nilai_analisis`, `tingkat_risiko_analisis_id`, `prioritas_risiko_id`, `selera_risiko`, `pilihan_penanganan`, `uraian_penanganan`, `jadwal_pelaksanaan`, `p_target`, `d_target`, `bobot_target`, `nilai_target`, `tingkat_risiko_target_id`, `created_at`, `is_verified`, `verified_at`, `document`, `notes`) VALUES
(30, 64, 'Terhambatnya tindakan kepada pasien di Kamar Operasi', 7, 'Ketersediaan instrumen set dan linen set doek operasi tidak  sesuai dengan meningkatnya  volume tindakan', 'internal', 'uc', 'Komplain DPJP dan keluarga pasien', '1. Melakukan pengajuan penambahan instrumen set dan linen (doek operasi)  di aplikasi superbe \r\n2. Dalam kondisi urgent menggunakan doek disposible\r\n3. Segera melakukan proses sterilisasi jika instrumen selesai digunakan', 'efektif', 3, 3, 1.43, 13, 4, 3, 'diatas batas selera risiko', 'mitigasi risiko', '1. Mengajukan surat usulan pengadaan instrumen set dan linen  (doek operasi)\r\n2. Koordinasi dengan pelayanan penunjang dan rumah tangga\r\n3. Koordinasi dengan PJ Sarpras ruang tindakan untuk segera mengirimkan instrumen set yang sudah digunakan', '6 bulan', 2, 2, 1.8, 7, 3, '2025-05-22 12:00:17', NULL, '0000-00-00 00:00:00', NULL, NULL),
(31, 64, 'Petugas Binatu tertusuk jarum', 7, 'Masih ditemukan jarum bekas di dalam linen kotor', 'eksternal', 'uc', '\"Terpajan\"', 'Sudah ada SPO serah terima linen kotor', 'tidak efektif', 2, 3, 1.83, 11, 4, 3, 'diatas batas selera risiko', 'mitigasi risiko', '\"1.  Resosialisasi SOP serah terima linen kotor\r\n2. Monev proses serah terima linen kotor \r\n3. Menfeedback hasil monev  kepada unit terkait \"', '3 bulan', 2, 2, 1.8, 7, 3, '2025-05-22 12:01:24', NULL, '0000-00-00 00:00:00', NULL, NULL),
(32, 64, 'Lamanya waktu  proses sterilisasi', 7, '\"1. Dikarenakan mesin washer ultrasonic cleaner jumlahnya kurang dan kapasitas kecil sehingga alat berbahan plastik yang panjang tidak dapat masuk ke mesin washer ultrasonic tersebut\r\n\r\n2. Dikarenakan mesin draying cabinet jumlahnya kurang dan kapasitas kecil sehingga alat yang banyak tersebut tidak dapat dikeringkan sekaligus\"', 'internal', 'uc', 'Terhambatnya pelayanan kepada pasien', '\"1. Melakukan pencucian secara manual pada alat plastik yang panjang\r\n2. Melakukan pengeringan alat secara manual\r\n3. Melakukan pengajuan pengadaan mesin washer dan mesin draying di aplikasi superbe  \"', 'efektif', 4, 2, 1.19, 10, 4, 3, 'diatas batas selera risiko', 'mitigasi risiko', '\"1. Mengajukan surat usulan pengadaan mesin draying dan mesin washer ultrasonic cleaner\r\n2.  koordinasi dengan pelayanan penunjang\"', '6 bulan', 2, 2, 1.8, 7, 3, '2025-05-22 12:02:21', NULL, '0000-00-00 00:00:00', NULL, NULL),
(33, 64, 'Kemungkinan petugas tertusuk benda tajam', 6, 'Petugas tidak mematuhi SPO mencuci instrumen', 'internal', 'c', 'Terpajan', 'Sosialisasi dan monitoring SPO penggunaan APD yang lengkap', 'efektif', 1, 3, 2, 6, 3, 4, 'dalam batas selera risiko', 'menerima risiko', '-', 'Setiap bulan', 1, 2, 1.5, 3, 1, '2025-05-22 12:03:50', 0, '2025-05-22 19:21:47', NULL, 'kurang data'),
(34, 64, 'Kemungkinan kehilangan linen dan chemikal binatu', 4, 'Adanya pelayanan linen dan penggunaan chemikal untuk proses pencucian linen', 'internal', 'c', 'Kekurangan stok linen dan chemical binatu', '\"1. Penyimpanan linen dan chemical diruang khusus dan linen dalam lemari  terkunci \r\n2. Membatasi akses petugas terhadap tempat penyimpanan linen dan chemical\r\n3. Adanya kartu keluar masuk linen dan kartu kontrol penggunaan chemical\"', 'efektif', 1, 3, 2, 6, 3, 4, 'dalam batas selera risiko', 'menerima risiko', '-', 'Setiap bulan', 1, 2, 1.5, 3, 1, '2025-05-22 12:04:47', 1, '2025-05-22 15:48:13', NULL, NULL),
(35, 18, 'Layanan yang dikembangkan tidak optimal (merugi)', 1, '\"-Analisis kebutuhan yang tidak komprehensif\r\n-Tujuan yang tidak jelas\r\n-Sumber daya yang tidak memadai\"', 'eksternal', 'c', 'Layanan yang dikembangkan tidak efesien', 'Sosialisasi panduan pengembangan layanan', 'tidak efektif', 3, 4, 1.46, 18, 5, 2, 'diatas batas selera risiko', 'mitigasi risiko', 'Mengusulkan penetapan KPI untuk pengembangan layanan dengan bisnis plan', 'Januari Tahun 2025', 2, 3, 1.83, 11, 4, '2025-05-22 12:09:13', NULL, '0000-00-00 00:00:00', NULL, NULL),
(36, 18, 'Penilaian kinerja tidak sesuai', 4, 'Data yang disampaikan tidak valid', 'internal', 'c', '\"-Laporan yang disampaikan tidak sesuai dengan fakta dilapangan\r\n-Penilaian kinerja melebihi capaian kinerja sebenarnya\"', 'Melakukan validasi data yang disampaikan', 'efektif', 4, 3, 1.3, 16, 5, 2, 'diatas batas selera risiko', 'mitigasi risiko', '\"1. Monev validitas data\r\n2. Menfeedbackkan hasil validasi data kepada unit terkait\"', 'Setiap bulan', 2, 3, 1.83, 11, 4, '2025-05-22 12:10:05', 1, '2025-05-22 13:23:32', NULL, NULL),
(37, 17, 'Kegiatan penelitian belum optimal', 2, '\"1. Belum adanya Clinical Unit Research (CRU) di RS\r\n2. Belum tersedianya SDM dan Sarana prasarana terkait CRU\"', 'internal', 'uc', 'Pendapatan dari penelitian belum maksimal', '\"1. Sudah ada regulasi terkait unit CRU\r\n2. Sudah ada pendampingan dalam pembetukan unit CRU\"', 'tidak efektif', 4, 2, 1.19, 10, 4, 3, 'diatas batas selera risiko', 'mitigasi risiko', '\"1. Mengusulkan pembentukan unit kerja CRU\r\n2. Mengusulkan pengadaan SDM dan sarana prasarana penunjang unit CRU.                                                                                     3. Peningkatan kompetensi dan kualitas  SDM peneliti.                                                4. Monetisasi layanan riset klinis.                                                             5. Diversifikasi bentuk layanan riset klinis.                                                                 6. Menjalin kolaborasi dan kerjasama dengan mitra strategis dan industri kesehatan untuk penelitian dan pendanaan.                                                   7. Peningkatan dengan branding dan pemasaran\"', 'TW 1 dan 2', 2, 2, 1.8, 7, 3, '2025-05-22 12:39:37', 0, '2025-05-22 13:24:11', NULL, 'Data tidak valid ');

-- --------------------------------------------------------

--
-- Table structure for table `prioritas_risiko`
--

CREATE TABLE `prioritas_risiko` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prioritas_risiko`
--

INSERT INTO `prioritas_risiko` (`id`, `code`, `name`) VALUES
(1, 1, 'Sangat Tinggi'),
(2, 2, 'Tinggi'),
(3, 3, 'Sedang'),
(4, 4, 'Rendah'),
(5, 5, 'Sangat Rendah');

-- --------------------------------------------------------

--
-- Table structure for table `simpulan_tingkat_risiko`
--

CREATE TABLE `simpulan_tingkat_risiko` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simpulan_tingkat_risiko`
--

INSERT INTO `simpulan_tingkat_risiko` (`id`, `name`) VALUES
(1, 'tidak ada penurunan tingkat risiko '),
(2, 'tingkat risiko mengalami penurunan'),
(3, 'tingkat risiko mengalami peningkatan');

-- --------------------------------------------------------

--
-- Table structure for table `tingkat_risiko`
--

CREATE TABLE `tingkat_risiko` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tingkat_risiko`
--

INSERT INTO `tingkat_risiko` (`id`, `name`) VALUES
(1, 'Sangat Rendah'),
(3, 'Rendah'),
(4, 'Sedang'),
(5, 'Tinggi'),
(6, 'Sangat Tinggi');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `direktorat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit`, `direktorat_id`) VALUES
(1, 'SPI', 1),
(2, 'Komite Medik', 1),
(3, 'Komite Keperawatan', 1),
(4, 'Komite Mutu Rumah Sakit', 1),
(5, 'Komite Etik Dan Hukum', 1),
(6, 'Komite PPI', 1),
(7, 'Komite Farmasi Terafi Dan Program Pengendalian Resistensi Antimikroba', 1),
(8, 'Komite Kordinasi Pendididkan', 1),
(9, 'Komite Etik Penelitian', 1),
(10, 'Komite Tenaga Kesehatan Lainnya', 1),
(11, 'Unit Layanan Pengadaan dan PPK', 1),
(12, 'Timker Pelayanan Medik', 2),
(13, 'Timker Pelayanan Keperawatan', 2),
(14, 'Timker Pelayanan Penunjang', 2),
(15, 'Timker OSDM', 3),
(16, 'Timker Pendidikan Dan Pelatihan', 3),
(17, 'Timker Penelitian', 3),
(18, 'Timker Perencanaan Dan Evaluasi Program', 4),
(19, 'Timker Perencanaan Anggaran', 4),
(20, 'Timker Pelaksanaan Keuangan', 4),
(21, 'Timker Akutansi Dan Barang Milik Negara', 4),
(22, 'Timker TU dan RT', 5),
(23, 'Timker Hukmas', 5),
(24, 'KSM Bedah', 2),
(25, 'KSM PDL', 2),
(26, 'KSM Obgyn', 2),
(27, 'KSM Kesehatan Anak', 2),
(28, 'KSM Tht-Kl', 2),
(29, 'KSM Neurologi', 2),
(30, 'KSM Mata', 2),
(31, 'KSM Darmatologi Venereologi dan Estetika (DVE)', 2),
(32, 'KSM Anastesi Dan Terapi Intensif', 2),
(33, 'KSM Radiologis', 2),
(34, 'KSM Radioterapi', 2),
(35, 'KSM Patologi Klinik dan Kedokteran Laboratorium', 2),
(36, 'KSM Patologi Anatomi', 2),
(37, 'KSM Gigi Dan Mulut', 2),
(38, 'KSM Rehabilitasi Medik', 2),
(39, 'KSM Kardiologi', 2),
(40, 'KSM Gizi Klinik, Forensik,Jiwa', 2),
(41, 'KSM Orthopedi & Traumatologi', 2),
(42, 'KSM BKTV', 2),
(43, 'KSM Umum', 2),
(44, 'Instalasi Gawat Darurat', 2),
(45, 'Instalasi Rawat Jalan Dan Geriatri', 2),
(46, 'Instalasi Graha Eksekutif', 2),
(47, 'Instalasi BHC', 2),
(48, 'Instalasi Rawat Inap', 2),
(49, 'Instalasi Rawat Intensif', 2),
(50, 'Instalasi Bedah Sentral', 2),
(51, 'Instalasi Hemodialisa', 2),
(52, 'Instalasi Gizi', 2),
(53, 'Instalasi Farmasi', 2),
(54, 'Instalasi Radiologi', 2),
(55, 'Instalasi Radioterapi', 2),
(56, 'Instalasi Laboratorium Sentral', 2),
(57, 'Instalasi Forensik Dan Pemulasaran Jenazah', 2),
(58, 'Instalasi Rehabilitasi Medik', 2),
(59, 'Instalasi Graha Sehat', 2),
(60, 'Instalasi Administrasi Pasien', 4),
(61, 'IPSPRS', 5),
(62, 'Instalasi SIMRS', 5),
(63, 'Instalasi Keslin dan K3RS', 5),
(64, 'Instalasi Sterilisasi Sentral Dan Binatu', 5),
(65, 'Instalasi Promosi Kesehatan Rumah Sakit', 5),
(66, 'Instalasi Rekam Medik', 5),
(67, 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `direktorat_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `role` enum('admin','direksi','unit') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `direktorat_id`, `unit_id`, `username`, `password`, `name`, `address`, `is_active`, `role`) VALUES
(16, NULL, 64, 'issb', '149ef6a0f21d317dbdc5dbfc1c7b10db', 'issb', 'issb', 1, 'unit'),
(20, 1, NULL, 'direkturutama', 'a79924be855f431f39a0628fbc415f1d', 'direkturutamaa', 'Jalan Jendral Sudirman', 1, 'direksi'),
(22, 1, NULL, 'dlo', 'a861b5be6a3e9ebd3e39b462901544ee', 'dlo', 'Jalan Sudirman Thamrin', 1, 'direksi'),
(23, NULL, NULL, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', 1, 'admin'),
(24, NULL, 18, 'timkerpep', '181de17d10e26bc0e3aec5b5594b28d4', 'timkerpep', 'timkerpep', 1, 'unit'),
(25, NULL, 17, 'timkerpenelitian', '9399b0b2a1f363ff1ccdf662b45a88d6', 'timkerpenelitian', 'timker penelitian', 1, 'unit'),
(26, NULL, 30, 'ksmmata', 'ab184e09b76cb588081aa2349db88001', 'ksmmata', 'Jalan Soekarno Hatta ', 1, 'unit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dampak_risiko`
--
ALTER TABLE `dampak_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `direktorat`
--
ALTER TABLE `direktorat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_risiko`
--
ALTER TABLE `kategori_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kemungkinan_risiko`
--
ALTER TABLE `kemungkinan_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemantauan_reviu`
--
ALTER TABLE `pemantauan_reviu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pemantauan_tingkat_risiko_id` (`tingkat_risiko_id`),
  ADD KEY `FK_simpulan_tingkat_risiko_id` (`simpulan_tingkat_risiko_id`);

--
-- Indexes for table `penilaian_risiko`
--
ALTER TABLE `penilaian_risiko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_risk_unit_id` (`unit_id`),
  ADD KEY `FK_kategori_resiko_id` (`kategori_risiko_id`),
  ADD KEY `fk_analisis_risiko` (`tingkat_risiko_analisis_id`),
  ADD KEY `fk_target_risiko` (`tingkat_risiko_target_id`),
  ADD KEY `fk_prioritas_risiko_id` (`prioritas_risiko_id`);

--
-- Indexes for table `prioritas_risiko`
--
ALTER TABLE `prioritas_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `simpulan_tingkat_risiko`
--
ALTER TABLE `simpulan_tingkat_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tingkat_risiko`
--
ALTER TABLE `tingkat_risiko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_direktorat_id` (`direktorat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_unit_id` (`unit_id`),
  ADD KEY `fk_user_direktorat_id` (`direktorat_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dampak_risiko`
--
ALTER TABLE `dampak_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `direktorat`
--
ALTER TABLE `direktorat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori_risiko`
--
ALTER TABLE `kategori_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kemungkinan_risiko`
--
ALTER TABLE `kemungkinan_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pemantauan_reviu`
--
ALTER TABLE `pemantauan_reviu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `penilaian_risiko`
--
ALTER TABLE `penilaian_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `prioritas_risiko`
--
ALTER TABLE `prioritas_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `simpulan_tingkat_risiko`
--
ALTER TABLE `simpulan_tingkat_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tingkat_risiko`
--
ALTER TABLE `tingkat_risiko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemantauan_reviu`
--
ALTER TABLE `pemantauan_reviu`
  ADD CONSTRAINT `FK_simpulan_tingkat_risiko_id` FOREIGN KEY (`simpulan_tingkat_risiko_id`) REFERENCES `simpulan_tingkat_risiko` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pemantauan_tingkat_risiko_id` FOREIGN KEY (`tingkat_risiko_id`) REFERENCES `tingkat_risiko` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penilaian_risiko`
--
ALTER TABLE `penilaian_risiko`
  ADD CONSTRAINT `FK_kategori_resiko_id` FOREIGN KEY (`kategori_risiko_id`) REFERENCES `kategori_risiko` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_risk_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_analisis_risiko` FOREIGN KEY (`tingkat_risiko_analisis_id`) REFERENCES `tingkat_risiko` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prioritas_risiko_id` FOREIGN KEY (`prioritas_risiko_id`) REFERENCES `prioritas_risiko` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_target_risiko` FOREIGN KEY (`tingkat_risiko_target_id`) REFERENCES `tingkat_risiko` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `FK_direktorat_id` FOREIGN KEY (`direktorat_id`) REFERENCES `direktorat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_direktorat_id` FOREIGN KEY (`direktorat_id`) REFERENCES `direktorat` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
