-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 15, 2025 at 03:55 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixpoint_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda_direktur`
--

CREATE TABLE `agenda_direktur` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `tgl_input` datetime DEFAULT current_timestamp(),
  `user_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `akses_menu`
--

CREATE TABLE `akses_menu` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `akses_menu`
--

INSERT INTO `akses_menu` (`id`, `user_id`, `menu_id`) VALUES
(2944, 6, 32),
(2945, 6, 31),
(2946, 6, 1),
(2947, 6, 15),
(2948, 6, 18),
(2949, 6, 67),
(2950, 6, 39),
(2951, 6, 38),
(2952, 6, 40),
(2953, 6, 7),
(2954, 6, 5),
(2955, 6, 6),
(2956, 6, 71),
(2957, 6, 21),
(2958, 6, 23),
(2959, 6, 50),
(2960, 6, 11),
(2961, 6, 61),
(2962, 6, 63),
(2963, 6, 65),
(2964, 6, 64),
(2965, 6, 72),
(2966, 6, 13),
(2967, 6, 42),
(2968, 6, 56),
(2969, 6, 34),
(2970, 6, 53),
(2971, 6, 54),
(2972, 6, 20),
(2973, 6, 36),
(2974, 6, 35),
(2975, 6, 37),
(2976, 6, 33),
(2977, 6, 51),
(2978, 6, 16),
(2979, 6, 19),
(2980, 6, 49),
(2981, 6, 48),
(2982, 6, 24),
(2983, 6, 25),
(2984, 6, 26),
(2985, 6, 27),
(2986, 6, 28),
(2987, 6, 57),
(2988, 6, 12),
(2989, 6, 22),
(2990, 6, 29),
(2991, 6, 52),
(2992, 6, 9),
(2993, 6, 10),
(2994, 6, 8),
(2995, 6, 17),
(2996, 6, 55),
(2997, 94, 42),
(2998, 94, 33),
(2999, 94, 57),
(4325, 4, 32),
(4326, 4, 90),
(4327, 4, 100),
(4328, 4, 1),
(4329, 4, 50),
(4330, 4, 42),
(4331, 4, 75),
(4332, 4, 89),
(4333, 4, 68),
(4334, 4, 74),
(4335, 4, 57),
(4336, 4, 12),
(4337, 4, 9),
(4338, 4, 10),
(4339, 4, 8),
(4340, 4, 55),
(4341, 4, 88),
(5544, 5, 59),
(5545, 5, 60),
(5546, 5, 32),
(5547, 5, 57),
(5548, 5, 31),
(5549, 5, 62),
(5550, 5, 104),
(5551, 5, 111),
(5552, 5, 100),
(5553, 5, 1),
(5554, 5, 15),
(5555, 5, 46),
(5556, 5, 18),
(5557, 5, 70),
(5558, 5, 99),
(5559, 5, 112),
(5560, 5, 39),
(5561, 5, 38),
(5562, 5, 40),
(5563, 5, 7),
(5564, 5, 76),
(5565, 5, 5),
(5566, 5, 6),
(5567, 5, 71),
(5568, 5, 21),
(5569, 5, 23),
(5570, 5, 50),
(5571, 5, 11),
(5572, 5, 61),
(5573, 5, 98),
(5574, 5, 102),
(5575, 5, 72),
(5576, 5, 103),
(5577, 5, 110),
(5578, 5, 106),
(5579, 5, 13),
(5580, 5, 42),
(5581, 5, 56),
(5582, 5, 45),
(5583, 5, 34),
(5584, 5, 53),
(5585, 5, 54),
(5586, 5, 20),
(5587, 5, 75),
(5588, 5, 78),
(5589, 5, 36),
(5590, 5, 35),
(5591, 5, 37),
(5592, 5, 33),
(5593, 5, 77),
(5594, 5, 51),
(5595, 5, 16),
(5596, 5, 19),
(5597, 5, 43),
(5598, 5, 107),
(5599, 5, 108),
(5600, 5, 109),
(5601, 5, 73),
(5602, 5, 74),
(5603, 5, 44),
(5604, 5, 49),
(5605, 5, 47),
(5606, 5, 48),
(5607, 5, 24),
(5608, 5, 25),
(5609, 5, 26),
(5610, 5, 27),
(5611, 5, 28),
(5612, 5, 113),
(5613, 5, 101),
(5614, 5, 41),
(5615, 5, 12),
(5616, 5, 22),
(5617, 5, 30),
(5618, 5, 29),
(5619, 5, 52),
(5620, 5, 9),
(5621, 5, 10),
(5622, 5, 8),
(5623, 5, 17),
(5624, 5, 55),
(5625, 5, 84),
(5626, 5, 79),
(5627, 5, 86),
(5628, 5, 83),
(5629, 5, 82),
(5630, 5, 87),
(5631, 5, 85),
(5632, 5, 80),
(5633, 5, 81),
(5634, 5, 88);

-- --------------------------------------------------------

--
-- Table structure for table `arsip_digital`
--

CREATE TABLE `arsip_digital` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `file_arsip` varchar(255) DEFAULT NULL,
  `tgl_upload` datetime DEFAULT NULL,
  `user_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `berita_acara`
--

CREATE TABLE `berita_acara` (
  `id` int(11) NOT NULL,
  `tiket_id` int(11) NOT NULL,
  `nomor_ba` varchar(100) DEFAULT NULL,
  `nomor_tiket` varchar(100) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nik` varchar(50) NOT NULL,
  `nama_pelapor` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `kendala` text DEFAULT NULL,
  `catatan_teknisi` text NOT NULL,
  `tanggal_ba` datetime NOT NULL,
  `teknisi` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `berita_acara_software`
--

CREATE TABLE `berita_acara_software` (
  `id` int(11) NOT NULL,
  `nomor_ba` varchar(100) NOT NULL,
  `tiket_id` int(11) NOT NULL,
  `nomor_tiket` varchar(100) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nik` varchar(50) NOT NULL,
  `nama_pelapor` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `kendala` text DEFAULT NULL,
  `catatan_teknisi` text NOT NULL,
  `tanggal_ba` datetime NOT NULL,
  `teknisi` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `capaian`
--

CREATE TABLE `capaian` (
  `id_capaian` int(11) NOT NULL,
  `jenis_indikator` enum('nasional','rs','unit') NOT NULL,
  `id_indikator` int(11) NOT NULL,
  `periode` date NOT NULL,
  `nilai_capaian` decimal(5,2) NOT NULL,
  `status` enum('Tercapai','Tidak Tercapai') DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_kerja`
--

CREATE TABLE `catatan_kerja` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `catatan_kerja`
--

INSERT INTO `catatan_kerja` (`id`, `user_id`, `judul`, `isi`, `tanggal`) VALUES
(1, 5, 'Cutsom Khanza', '- edit repot laporan operasi \r\n- jam mulai menyimpan berdasarkan jam mulai pada menu jadwal operasi\r\n- jam selesi menyimpan berdsarkan jam selesai pada menu jadwal operasi', '2025-08-25 06:17:12'),
(2, 4, 'Edit Vidio', '- Edit vidio kemerdekaan', '2025-08-25 06:29:13'),
(4, 5, 'Cutsom Khanza RSPH', '- uji coba hasil custom khanza', '2025-08-25 11:36:21'),
(5, 5, 'Cutsom Khanza', '- edit resume pasien rawat inap\r\n- edit laporan operasi', '2025-08-26 20:34:11'),
(6, 5, 'tes', 'tes', '2025-09-14 19:13:11'),
(7, 5, 'tes', 'tes', '2025-09-14 19:15:24'),
(8, 5, 'tes', 'tes', '2025-09-14 19:17:56'),
(9, 5, 'tesss', 'tesssss', '2025-09-14 19:19:08'),
(10, 5, 'wira', 'wira', '2025-09-14 19:20:36'),
(11, 5, '123', '123', '2025-09-14 19:21:23'),
(12, 5, '123', '123', '2025-09-14 19:23:04'),
(13, 5, 'tesss', '1233456345', '2025-09-14 19:24:05'),
(14, 5, 'tess catatan kerja', 'tes catatan kerja', '2025-09-14 19:24:36'),
(15, 5, 'tes simpan pekerjaan', 'tes simpan', '2025-09-14 19:41:30');

-- --------------------------------------------------------

--
-- Table structure for table `data_barang_it`
--

CREATE TABLE `data_barang_it` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `no_barang` varchar(25) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `kategori` varchar(20) DEFAULT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `spesifikasi` varchar(255) DEFAULT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `kondisi` varchar(20) DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_barang_it`
--

INSERT INTO `data_barang_it` (`id`, `user_id`, `no_barang`, `nama_barang`, `kategori`, `merk`, `spesifikasi`, `ip_address`, `lokasi`, `kondisi`, `waktu_input`) VALUES
(13, 5, '123', 'CPU Set / Wira', 'Komputer', 'Build Up', 'Ram 8Gb, Hdd 1 Tera, Core i5', '192.168.0.50', 'IT & Marketing', 'Baik', '2025-08-12 04:20:42'),
(14, 5, 'IT002/IT/XII/2025', 'Komputer Kerja Farmasi', 'Komputer', 'Build Up ', 'Ram 8Gb, SSD 500Gb, Prosesor core i7', '192.168.10.50', 'Instalasi Farmasi', 'Baik', '2025-08-21 10:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id` int(11) NOT NULL,
  `nama_dokter` varchar(150) NOT NULL,
  `poliklinik` varchar(150) NOT NULL,
  `hari_praktek` varchar(100) NOT NULL,
  `jam_praktek` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id`, `nama_dokter`, `poliklinik`, `hari_praktek`, `jam_praktek`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'dr Husnul Abid , SpOG', 'Kandungan dan Kebidanan', 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu', '15:00 - 17:00', '68abc3108448e.jpeg', '2025-08-25 01:57:36', '2025-08-25 01:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pokja_id` int(11) NOT NULL,
  `elemen_penilaian` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name_original` varchar(255) DEFAULT NULL,
  `petugas` varchar(150) NOT NULL,
  `waktu_input` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_pendukung`
--

CREATE TABLE `dokumen_pendukung` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ktp` varchar(255) DEFAULT NULL,
  `ijazah` varchar(255) DEFAULT NULL,
  `str` varchar(255) DEFAULT NULL,
  `sip` varchar(255) DEFAULT NULL,
  `vaksin` varchar(255) DEFAULT NULL,
  `pelatihan` varchar(255) DEFAULT NULL,
  `surat_kerja` varchar(255) DEFAULT NULL,
  `pas_foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dokumen_pendukung`
--

INSERT INTO `dokumen_pendukung` (`id`, `user_id`, `ktp`, `ijazah`, `str`, `sip`, `vaksin`, `pelatihan`, `surat_kerja`, `pas_foto`, `created_at`, `updated_at`) VALUES
(1, 5, '5_ScanKTP.png', '5_Ijazah&Transkrip.jpg', '5_STR.pdf', '5_SIP.jpg', '5_SertifikatVaksin.jpeg', '5_SertifikatPelatihan.pdf', '5_SuratPengalamanKerja.png', '5_PasFoto.jpeg', '2025-09-14 15:02:03', '2025-09-14 15:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama_fasilitas`, `keterangan`, `foto`) VALUES
(1, 'Poliklinik Anak', 'Poliklinik Anak merupakan Poliklinik yang menangani dan melayani pemeriksaan untuk memberikan tindakan pencegahan penyakit pada bayi, anak, dan remaja yang sehat, serta memberikan pengobatan pada anak yang sakit serta mendiagnosis dan mengevaluasi tumbuh kembang anak, ditangani oleh dokter spesialis yang berpengalaman di bidangnya.', '689f4e3c9b18b.webp'),
(2, 'Poliklinik Kandungan & Kebidanan', 'Poli Obgyn  menyediakan berbagai jenis layanan kesehatan. Dalam Poli Obgyn, pasien akan diperiksa oleh dokter spesialis obstetri ginekologi yang terlatih dan berpengalaman dalam mengatasi berbagai masalah kesehatan perempuan. Pemeriksaan ini biasanya meliputi pemeriksaan fisik, pemeriksaan panggul, tes laboratorium, dan tes pemindaian, jika diperlukan.', '689f4f325ef5e.jpeg'),
(3, 'Poliklinik Penyakit Dalam', 'Poliklinik Penyakit Dalam menyediakan layanan kesehatan untuk masalah kesehatan yang terkait dengan organ dalam tubuh manusia, seperti jantung, paru-paru, lambung, usus, hati, ginjal, dan sistem kekebalan tubuh.', '689f4fabca7da.jpg'),
(4, 'Poliklinik Orthopedi', 'Poli Ortopedi  menyediakan layanan kesehatan untuk masalah tulang, otot, dan sendi. Poliklinik ini mendiagnosis dan pengobatan penyakit dan cedera muskuloskeletal, termasuk patah tulang, cedera ligamen, penyakit degeneratif sendi, dan gangguan tulang belakang.', '689f5044aea50.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `fungsional`
--

CREATE TABLE `fungsional` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fungsional`
--

INSERT INTO `fungsional` (`id`, `nominal`, `created_at`) VALUES
(1, 500000, '2025-07-26 12:33:05');

-- --------------------------------------------------------

--
-- Table structure for table `gaji_pokok`
--

CREATE TABLE `gaji_pokok` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `gaji_pokok`
--

INSERT INTO `gaji_pokok` (`id`, `nominal`, `created_at`) VALUES
(1, 3000000, '2025-07-26 12:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `indikator_harian`
--

CREATE TABLE `indikator_harian` (
  `id_harian` int(11) NOT NULL,
  `jenis_indikator` enum('nasional','rs','unit') NOT NULL,
  `id_indikator` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `numerator` int(11) NOT NULL,
  `denominator` int(11) NOT NULL,
  `persentase` decimal(5,2) GENERATED ALWAYS AS (case when `denominator` > 0 then `numerator` / `denominator` * 100 else 0 end) STORED,
  `keterangan` text DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `indikator_harian`
--

INSERT INTO `indikator_harian` (`id_harian`, `jenis_indikator`, `id_indikator`, `tanggal`, `numerator`, `denominator`, `keterangan`, `petugas`) VALUES
(20, 'rs', 3, '2025-09-01', 30, 34, '-', 'M Wira Satria Buana, S. Kom'),
(21, 'rs', 3, '2025-09-01', 30, 34, '-', 'M Wira Satria Buana, S. Kom'),
(22, 'rs', 3, '2025-09-01', 30, 34, '-', 'M Wira Satria Buana, S. Kom'),
(23, 'rs', 3, '2025-09-01', 30, 34, '-', 'M Wira Satria Buana, S. Kom'),
(24, 'unit', 4, '2025-09-01', 10, 15, '-', 'M Wira Satria Buana, S. Kom');

-- --------------------------------------------------------

--
-- Table structure for table `indikator_nasional`
--

CREATE TABLE `indikator_nasional` (
  `id_nasional` int(11) NOT NULL,
  `nama_indikator` varchar(255) NOT NULL,
  `definisi` text DEFAULT NULL,
  `numerator` text DEFAULT NULL,
  `denominator` text DEFAULT NULL,
  `standar` varchar(50) NOT NULL,
  `sumber_data` varchar(150) DEFAULT NULL,
  `frekuensi` varchar(50) DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `indikator_nasional`
--

INSERT INTO `indikator_nasional` (`id_nasional`, `nama_indikator`, `definisi`, `numerator`, `denominator`, `standar`, `sumber_data`, `frekuensi`, `penanggung_jawab`, `unit_id`) VALUES
(10, 'Kepatuhan kebersihan tangan', 'Kepatuhan kebersihan tangan', '-', '-', '85', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 21),
(11, 'Kepatuhan kebersihan tangan', 'Kepatuhan kebersihan tangan', '-', '-', '85', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(12, 'Kepatuhan kebersihan tangan', 'Kepatuhan kebersihan tangan', '-', '-', '85', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 22),
(13, 'Kepatuhan penggunaan APD', 'Kepatuhan penggunaan APD', '-', '-', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 12),
(14, 'Kepatuhan identifikasi pasien', 'Kepatuhan identifikasi pasien', 'Jumlah pemberi pelayanan yang melakukan identifikasi pasien secara benar dalam periode observasi', 'Jumlah pemberi pelayanan yang diobservasi dalam periode observasi', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 21),
(15, 'Kepatuhan identifikasi pasien', 'Kepatuhan identifikasi pasien', 'Jumlah pemberi pelayanan yang melakukan identifikasi pasien secara benar dalam periode observasi', 'Jumlah pemberi pelayanan yang diobservasi dalam periode observasi', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(16, 'Kepatuhan identifikasi pasien', 'Kepatuhan identifikasi pasien', 'Jumlah pemberi pelayanan yang melakukan identifikasi pasien secara benar dalam periode observasi', 'Jumlah pemberi pelayanan yang diobservasi dalam periode observasi', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 22),
(17, 'Waktu tanggap operasi sc emergenci', 'Waktu tanggap operasi sc emergenci', '-', '-', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 12),
(18, 'Waktu tunggu rawat jalan', 'Waktu tunggu rawat jalan', '-', '-', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 11),
(19, 'Penundaan operasi elektif', 'Penundaan operasi elektif', '-', '-', '5', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 12),
(20, 'Kepatuhan waktu visite dokter', 'Kepatuhan waktu visite dokter', 'Jumlah pasien yang di-visite dokter pada pukul 06.00 – 14.00', 'Jumlah pasien yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 21),
(21, 'Kepatuhan waktu visite dokter', 'Kepatuhan waktu visite dokter', 'Jumlah pasien yang di-visite dokter pada pukul 06.00 – 14.00', 'Jumlah pasien yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(22, 'Kepatuhan waktu visite dokter', 'Kepatuhan waktu visite dokter', 'Jumlah pasien yang di-visite dokter pada pukul 06.00 – 14.00', 'Jumlah pasien yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(23, 'Pelaporan hasil kritis laboratorium', 'Pelaporan hasil kritis laboratorium', '-', '-', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 18),
(24, 'Kepatuhan penggunaan formularium nasional', 'Kepatuhan penggunaan formularium nasional', '-', '-', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 14),
(25, 'Kepatuhan penggunaan formularium nasional', 'Kepatuhan penggunaan formularium nasional', 'Jumlah pelayanan oleh PPA yang sesuai dengan clinical pathway', 'Jumlah seluruh pelayanan oleh PPA pada clinical pathway yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 21),
(26, 'Kepatuhan terhadap alur klinis (CP)', 'Kepatuhan terhadap alur klinis (CP)', 'Jumlah pelayanan oleh PPA yang sesuai dengan clinical pathway', 'Jumlah seluruh pelayanan oleh PPA pada clinical pathway yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(27, 'Kepatuhan terhadap alur klinis (CP)', 'Kepatuhan terhadap alur klinis (CP)', 'Jumlah pelayanan oleh PPA yang sesuai dengan clinical pathway', 'Jumlah seluruh pelayanan oleh PPA pada clinical pathway yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 23),
(28, 'Kepatuhan terhadap alur klinis (CP)', 'Kepatuhan terhadap alur klinis (CP)', 'Jumlah pelayanan oleh PPA yang sesuai dengan clinical pathway', 'Jumlah seluruh pelayanan oleh PPA pada clinical pathway yang diobservasi', '80', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 22),
(29, 'Kepatuhan upaya pencengahan resiko pasien jatuh', 'Kepatuhan upaya pencengahan resiko pasien jatuh', 'Jumlah pasien rawat inap berisiko tinggi jatuh yang mendapatkan ketiga upaya pencegahan risiko jatuh', 'Jumlah pasien rawat inap berisiko tinggi jatuh yang diobservasi', '100', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 21),
(30, 'Kecepatan waktu tanggap complain', 'Kecepatan waktu tanggap complain', '-', '-', '90', 'SNARS', 'Harian', 'M Wira Satria Buana, S. Kom', 9),
(31, 'Kepuasaan pasien edit', 'Kepuasaan pasien tes', '-', '-', '70.61', 'SNARS', 'Mingguan', 'M Wira Satria Buana, S. Kom', 9),
(32, 'tes', 'tes', 'tes', 'tes', '60', 'tes', 'Harian', 'M Wira Satria Buana, S. Kom', 28),
(33, 'tes', 'tes', 'tes', 'tes', '60', 'tes', 'Harian', 'M Wira Satria Buana, S. Kom', 28);

-- --------------------------------------------------------

--
-- Table structure for table `indikator_rs`
--

CREATE TABLE `indikator_rs` (
  `id_rs` int(11) NOT NULL,
  `id_nasional` int(11) DEFAULT NULL,
  `kategori` varchar(100) NOT NULL,
  `nama_indikator` varchar(255) NOT NULL,
  `definisi` text DEFAULT NULL,
  `numerator` text DEFAULT NULL,
  `denominator` text DEFAULT NULL,
  `standar` varchar(50) NOT NULL,
  `sumber_data` varchar(150) DEFAULT NULL,
  `frekuensi` varchar(50) DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `indikator_rs`
--

INSERT INTO `indikator_rs` (`id_rs`, `id_nasional`, `kategori`, `nama_indikator`, `definisi`, `numerator`, `denominator`, `standar`, `sumber_data`, `frekuensi`, `penanggung_jawab`) VALUES
(3, 15, 'SKP', 'Kepatuhan identifikasi pasien', 'Kepatuhan identifikasi pasien.', '-', '-', '100', 'SNARS', 'Harian', '-');

-- --------------------------------------------------------

--
-- Table structure for table `indikator_unit`
--

CREATE TABLE `indikator_unit` (
  `id_unit` int(11) NOT NULL,
  `id_rs` int(11) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `nama_indikator` varchar(255) NOT NULL,
  `definisi` text DEFAULT NULL,
  `numerator` text DEFAULT NULL,
  `denominator` text DEFAULT NULL,
  `standar` varchar(50) NOT NULL,
  `sumber_data` varchar(150) DEFAULT NULL,
  `frekuensi` varchar(50) DEFAULT NULL,
  `penanggung_jawab` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `indikator_unit`
--

INSERT INTO `indikator_unit` (`id_unit`, `id_rs`, `unit_id`, `nama_indikator`, `definisi`, `numerator`, `denominator`, `standar`, `sumber_data`, `frekuensi`, `penanggung_jawab`) VALUES
(4, 3, 13, 'Waktu tanggap pelayanan unit Gawat Darurat <5 menit', 'Waktu tanggap pelayanan unit Gawat Darurat <5 menit', '-', '-', '100', 'Standar Kemenkes', 'Harian', '-');

-- --------------------------------------------------------

--
-- Table structure for table `input_gaji`
--

CREATE TABLE `input_gaji` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `tahun` year(4) NOT NULL,
  `gaji_pokok` bigint(20) DEFAULT 0,
  `struktural` bigint(20) DEFAULT 0,
  `fungsional` bigint(20) DEFAULT 0,
  `fungsional2` bigint(20) DEFAULT 0,
  `kesehatan` bigint(20) DEFAULT 0,
  `masa_kerja` bigint(20) DEFAULT 0,
  `lembur` bigint(20) DEFAULT 0,
  `lainya` bigint(20) DEFAULT 0,
  `bruto` bigint(20) DEFAULT 0,
  `pph21` bigint(20) DEFAULT 0,
  `potongan_total` bigint(20) DEFAULT 0,
  `gaji_bersih` bigint(20) DEFAULT 0,
  `bpjs_kes` bigint(20) DEFAULT 0,
  `bpjs_jht` bigint(20) DEFAULT 0,
  `bpjs_jp` bigint(20) DEFAULT 0,
  `dana_sosial` bigint(20) DEFAULT 0,
  `absensi` bigint(20) DEFAULT 0,
  `angsuran` bigint(20) DEFAULT 0,
  `user_input` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_status` enum('Belum','Terkirim') DEFAULT 'Belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `input_gaji`
--

INSERT INTO `input_gaji` (`id`, `karyawan_id`, `periode`, `tahun`, `gaji_pokok`, `struktural`, `fungsional`, `fungsional2`, `kesehatan`, `masa_kerja`, `lembur`, `lainya`, `bruto`, `pph21`, `potongan_total`, `gaji_bersih`, `bpjs_kes`, `bpjs_jht`, `bpjs_jp`, `dana_sosial`, `absensi`, `angsuran`, `user_input`, `created_at`, `email_status`) VALUES
(16, 5, 'Juli', 2025, 3000000, 2000000, 500000, 0, 0, 1500000, 0, 0, 7000000, 49000, 235000, 6716000, 135000, 45000, 45000, 10000, 0, 0, 5, '2025-09-03 07:26:40', 'Belum'),
(17, 5, 'Juni', 2025, 3000000, 2000000, 500000, 0, 0, 1500000, 365000, 0, 7365000, 51555, 235000, 7078445, 135000, 45000, 45000, 10000, 0, 0, 5, '2025-09-03 07:27:38', 'Belum'),
(18, 5, 'Mei', 2025, 3000000, 2000000, 500000, 0, 0, 1500000, 0, 0, 7000000, 49000, 235000, 6716000, 135000, 45000, 45000, 10000, 0, 0, 5, '2025-09-03 07:28:08', 'Belum');

-- --------------------------------------------------------

--
-- Table structure for table `izin_keluar`
--

CREATE TABLE `izin_keluar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `bagian` varchar(100) DEFAULT NULL,
  `atasan_langsung` varchar(100) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `jam_kembali` time DEFAULT NULL,
  `jam_kembali_real` datetime DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_atasan` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `waktu_acc_atasan` datetime DEFAULT NULL,
  `status_sdm` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `waktu_acc_sdm` datetime DEFAULT NULL,
  `acc_oleh_sdm` int(11) DEFAULT NULL,
  `acc_oleh_atasan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `izin_keluar`
--

INSERT INTO `izin_keluar` (`id`, `user_id`, `nik`, `nama`, `jabatan`, `bagian`, `atasan_langsung`, `tanggal`, `jam_keluar`, `jam_kembali`, `jam_kembali_real`, `keperluan`, `created_at`, `status_atasan`, `waktu_acc_atasan`, `status_sdm`, `waktu_acc_sdm`, `acc_oleh_sdm`, `acc_oleh_atasan`) VALUES
(38, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:41:00', '15:42:00', NULL, 'tesss', '2025-08-19 07:41:36', 'pending', NULL, 'pending', NULL, NULL, NULL),
(39, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:43:00', '15:44:00', NULL, 'tesss', '2025-08-19 07:43:25', 'pending', NULL, 'pending', NULL, NULL, NULL),
(40, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:43:00', '15:44:00', NULL, 'tesss', '2025-08-19 07:45:01', 'pending', NULL, 'pending', NULL, NULL, NULL),
(41, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:45:00', '14:45:00', NULL, 'tesss', '2025-08-19 07:45:13', 'pending', NULL, 'pending', NULL, NULL, NULL),
(42, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:45:00', '15:46:00', NULL, 'tes', '2025-08-19 07:45:44', 'pending', NULL, 'pending', NULL, NULL, NULL),
(43, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:45:00', '15:46:00', NULL, 'tes', '2025-08-19 07:45:57', 'pending', NULL, 'pending', NULL, NULL, NULL),
(44, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:48:00', '15:49:00', NULL, 'fd', '2025-08-19 07:48:54', 'pending', NULL, 'pending', NULL, NULL, NULL),
(45, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:48:00', '15:49:00', NULL, 'fd', '2025-08-19 07:49:28', 'disetujui', '2025-08-22 23:33:15', 'pending', NULL, NULL, 5),
(46, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:53:00', '15:54:00', NULL, 'tes', '2025-08-19 07:53:33', 'disetujui', '2025-08-22 23:33:11', 'pending', NULL, NULL, 5),
(47, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:54:00', '15:55:00', NULL, 'sdfsd', '2025-08-19 07:54:32', 'disetujui', '2025-08-22 23:33:07', 'pending', NULL, NULL, 5),
(48, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:54:00', '15:55:00', NULL, 'fdsdfsdf', '2025-08-19 07:54:54', 'disetujui', '2025-08-22 23:33:04', 'pending', NULL, NULL, 5),
(49, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:57:00', '15:58:00', NULL, 'tess', '2025-08-19 07:57:48', 'disetujui', '2025-08-22 23:32:59', 'pending', NULL, NULL, 5),
(50, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'M Wira Satria Buana, S. Kom', '2025-08-19', '14:58:00', '15:59:00', '2025-08-19 14:59:36', 'dfsf', '2025-08-19 07:58:11', 'disetujui', '2025-08-19 14:58:45', 'disetujui', '2025-08-19 20:29:40', 5, 5),
(51, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Satria', '2025-08-19', '20:28:00', '20:33:00', '2025-08-19 20:28:41', 'tes', '2025-08-19 13:28:16', 'pending', NULL, 'pending', NULL, NULL, NULL),
(52, 4, '16216009', 'M. Giano Shaquille Wiandra', 'Perawat', 'Rawat Inap Lantai 2', 'M Wira Satria Buana, S. Kom', '2025-08-22', '22:23:00', '23:24:00', NULL, 'tes', '2025-08-22 15:23:18', 'disetujui', '2025-08-22 23:25:52', 'disetujui', '2025-08-22 23:34:06', 5, 5),
(53, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Satria', '2025-08-22', '23:19:00', '23:25:00', '2025-08-22 23:23:27', 'tes', '2025-08-22 16:19:09', 'pending', NULL, 'pending', NULL, NULL, NULL),
(54, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Satria', '2025-08-25', '08:59:00', '09:00:00', '2025-08-25 08:59:18', 'tes', '2025-08-25 01:59:15', 'pending', NULL, 'pending', NULL, NULL, NULL),
(55, 5, '16210646', 'M Wira Satria Buana, S. Kom', '', 'IT & Marketing', '', '2025-09-03', '14:32:00', '15:33:00', NULL, 'Tes Izin Keluar', '2025-09-03 07:32:45', 'pending', NULL, 'pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama_jabatan`) VALUES
(32, 'IT Software'),
(33, 'IT Hardware'),
(34, 'Umum'),
(35, 'Apoteker'),
(36, 'Perawat'),
(38, 'Direktur');

-- --------------------------------------------------------

--
-- Table structure for table `jatah_cuti`
--

CREATE TABLE `jatah_cuti` (
  `id` int(11) NOT NULL,
  `karyawan_id` int(11) NOT NULL,
  `cuti_id` int(11) NOT NULL,
  `lama_hari` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jatah_cuti`
--

INSERT INTO `jatah_cuti` (`id`, `karyawan_id`, `cuti_id`, `lama_hari`, `tahun`, `created_at`) VALUES
(1, 5, 1, 12, 2025, '2025-08-30 15:03:48');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_arsip`
--

CREATE TABLE `kategori_arsip` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_arsip`
--

INSERT INTO `kategori_arsip` (`id`, `nama_kategori`) VALUES
(1, 'Memo / Nota Dinas'),
(2, 'Laporan'),
(3, 'Keputusan / SK'),
(4, 'Perjanjian / MoU'),
(5, 'Dokumen Keuangan'),
(6, 'Dokumen Hukum / Legal'),
(7, 'Sertifikat / Izin'),
(8, 'Dokumen Proyek'),
(9, 'Dokumen Personalia / SDM'),
(10, 'Dokumen Teknis'),
(11, 'Dokumen Kebijakan'),
(12, 'Dokumen Pendukung / Lampiran'),
(13, 'Arsip Umum'),
(14, 'TES');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_hardware`
--

CREATE TABLE `kategori_hardware` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_hardware`
--

INSERT INTO `kategori_hardware` (`id`, `nama_kategori`) VALUES
(19, 'CCTV'),
(20, 'Jaringan Wifi / Acces Point'),
(21, 'Jaringan Komputer'),
(22, 'Komputer Set'),
(23, 'Nurse Call'),
(24, 'Parking System'),
(25, 'Telfon Lokal'),
(26, 'TV Kamar Pasien'),
(28, 'Printer'),
(29, 'Alarm Fire Emergency');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_pelaporan`
--

CREATE TABLE `kategori_pelaporan` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_software`
--

CREATE TABLE `kategori_software` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_software`
--

INSERT INTO `kategori_software` (`id`, `nama_kategori`) VALUES
(16, 'System Antrian'),
(17, 'Absensi'),
(18, 'Bridging BPJS Kes'),
(19, 'Custom Khanza'),
(20, 'E-Klaim'),
(21, 'Display Informasi'),
(22, 'SIMRS Khanza'),
(23, 'Jaringan Wifi / Acces Point');

-- --------------------------------------------------------

--
-- Table structure for table `kesehatan`
--

CREATE TABLE `kesehatan` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_bulanan`
--

CREATE TABLE `laporan_bulanan` (
  `id` int(11) NOT NULL,
  `bulan` varchar(10) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_input` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian`
--

CREATE TABLE `laporan_harian` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `uraian` text NOT NULL,
  `tanggal_input` datetime DEFAULT current_timestamp(),
  `file_dokumen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_off_duty`
--

CREATE TABLE `laporan_off_duty` (
  `id` int(11) NOT NULL,
  `no_tiket` varchar(50) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `petugas` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_input` varchar(100) DEFAULT NULL,
  `status_validasi` enum('Menunggu','Diproses','Selesai','Tidak Bisa Diperbaiki','Ditolak') DEFAULT 'Menunggu',
  `tanggal_validasi` datetime DEFAULT NULL,
  `validator_id` int(11) DEFAULT NULL,
  `catatan_it` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `laporan_off_duty`
--

INSERT INTO `laporan_off_duty` (`id`, `no_tiket`, `nik`, `nama`, `jabatan`, `unit_kerja`, `kategori`, `petugas`, `keterangan`, `tanggal`, `user_id`, `nama_input`, `status_validasi`, `tanggal_validasi`, `validator_id`, `catatan_it`) VALUES
(60, 'TKT0017/IT-OFFDUTY/08/2025', '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'software:Custom Khanza', 'Satria', 'tes', '2025-08-19 18:57:41', 5, 'M Wira Satria Buana, S. Kom', 'Menunggu', NULL, NULL, NULL),
(61, 'TKT0018/IT-OFFDUTY/08/2025', '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'software:Absensi', '', 'tes', '2025-08-19 19:06:38', 4, 'M. Giano Shaquille Wiandra', 'Menunggu', NULL, NULL, NULL),
(62, 'TKT0019/IT-OFFDUTY/08/2025', '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'software:Absensi', 'Ratih Aulia', 'tes', '2025-08-19 19:12:25', 4, 'M. Giano Shaquille Wiandra', 'Menunggu', NULL, NULL, NULL),
(63, 'TKT0020/IT-OFFDUTY/08/2025', '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'software:System Antrian', '', 'TES', '2025-08-19 19:14:30', 4, 'M. Giano Shaquille Wiandra', 'Menunggu', NULL, NULL, NULL),
(64, 'TKT0021/IT-OFFDUTY/08/2025', '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'software:Bridging BPJS Kes', 'M. Giano Shaquille Wiandra', 'tes notif ke GRUP WA', '2025-08-19 19:16:10', 4, 'M. Giano Shaquille Wiandra', 'Menunggu', NULL, NULL, NULL),
(65, 'TKT0022/IT-OFFDUTY/08/2025', '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'software:System Antrian', 'Satria', 'tes laporan off duty', '2025-08-19 19:16:35', 4, 'M. Giano Shaquille Wiandra', 'Menunggu', NULL, NULL, NULL),
(66, 'TKT0023/IT-OFFDUTY/08/2025', '16210646', 'M Wira Satria Buana, S. Kom', 'Direktur', 'Umum', 'software:System Antrian', 'Satria', 'tes', '2025-08-19 20:09:53', 5, 'M Wira Satria Buana, S. Kom', 'Menunggu', NULL, NULL, NULL),
(67, 'TKT0024/IT-OFFDUTY/08/2025', '16210646', 'M Wira Satria Buana, S. Kom', 'Direktur', 'Umum', 'software:System Antrian', 'M. Giano Shaquille Wiandra', 'tes', '2025-08-19 20:27:01', 5, 'M Wira Satria Buana, S. Kom', 'Menunggu', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_tahunan`
--

CREATE TABLE `laporan_tahunan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `judul` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `file_laporan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `laporan_tahunan`
--

INSERT INTO `laporan_tahunan` (`id`, `user_id`, `tahun`, `judul`, `keterangan`, `file_laporan`, `created_at`) VALUES
(2, 5, 2024, 'dsfs', 'sdfds', 'LAPTAH_1754963651_322.pdf', '2025-08-12 01:54:11');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id` int(11) NOT NULL,
  `posisi` varchar(150) NOT NULL,
  `tanggal_post` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_akhir` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`id`, `posisi`, `tanggal_post`, `tanggal_akhir`, `deskripsi`, `foto`, `user_id`) VALUES
(1, 'Driver', '2025-08-25 03:09:16', '2025-08-25', 'tes', '68abd3dc9f969.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mail_settings`
--

CREATE TABLE `mail_settings` (
  `id` int(11) NOT NULL,
  `mail_host` varchar(100) DEFAULT NULL,
  `mail_port` int(11) DEFAULT NULL,
  `mail_username` varchar(100) DEFAULT NULL,
  `mail_password` varchar(100) DEFAULT NULL,
  `mail_from_email` varchar(100) DEFAULT NULL,
  `mail_from_name` varchar(100) DEFAULT NULL,
  `base_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mail_settings`
--

INSERT INTO `mail_settings` (`id`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_from_email`, `mail_from_name`, `base_url`) VALUES
(8, 'smtp.gmail.com', 587, 'wiramuhammad16@gmail.com', 'ceidjdworghqehsk', 'wiramuhammad16@gmail.com', 'FixPoint Smart Office Management System', 'http://localhost');

-- --------------------------------------------------------

--
-- Table structure for table `maintanance_rutin`
--

CREATE TABLE `maintanance_rutin` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_teknisi` varchar(100) DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp(),
  `kondisi_fisik` text DEFAULT NULL,
  `fungsi_perangkat` text DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `maintanance_rutin`
--

INSERT INTO `maintanance_rutin` (`id`, `barang_id`, `user_id`, `nama_teknisi`, `waktu_input`, `kondisi_fisik`, `fungsi_perangkat`, `catatan`) VALUES
(13, 13, 5, 'M Wira Satria Buana, S. Kom', '2025-08-14 12:11:24', 'Bodi Utuh, Layar Jernih, Kabel Normal, port tidak rusak, label aset jelas, tidak ada komponen longgar', 'Booting normal, koneksi stabil, Resoulisi oke, USB & Periperal terdeteksi, Performa responsif, Update OS dan Antivirus tersedia', 'ganti pasta prosesor, bongkar.'),
(14, 14, 5, 'M Wira Satria Buana, S. Kom', '2025-08-21 10:16:11', 'Bodi Utuh, Kabel Normal, port tidak rusak, label aset jelas, tidak ada komponen longgar', 'Booting normal, koneksi stabil, Resoulisi oke, USB & Periperal terdeteksi, Performa responsif, Update OS dan Antivirus tersedia', 'bongkar CPU, ganti pasta, bersihkan debu,layar terlihat buram.');

-- --------------------------------------------------------

--
-- Table structure for table `masa_kerja`
--

CREATE TABLE `masa_kerja` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `masa_kerja`
--

INSERT INTO `masa_kerja` (`id`, `nominal`, `created_at`) VALUES
(2, 1500000, '2025-09-03 07:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `master_cuti`
--

CREATE TABLE `master_cuti` (
  `id` int(11) NOT NULL,
  `nama_cuti` varchar(100) NOT NULL,
  `lama_hari` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `master_cuti`
--

INSERT INTO `master_cuti` (`id`, `nama_cuti`, `lama_hari`, `keterangan`, `created_at`) VALUES
(1, 'Cuti Tahunan', 12, '-', '2025-08-30 15:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `master_indikator`
--

CREATE TABLE `master_indikator` (
  `id` int(11) NOT NULL,
  `kategori` enum('Nasional','RS','Unit') NOT NULL,
  `nama_indikator` varchar(255) NOT NULL,
  `definisi_operasional` text DEFAULT NULL,
  `numerator` text DEFAULT NULL,
  `denominator` text DEFAULT NULL,
  `target` decimal(6,2) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `dibuat_oleh` varchar(100) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_pokja`
--

CREATE TABLE `master_pokja` (
  `id` int(11) NOT NULL,
  `nama_pokja` varchar(255) NOT NULL,
  `waktu_input` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `master_pokja`
--

INSERT INTO `master_pokja` (`id`, `nama_pokja`, `waktu_input`) VALUES
(7, 'TKRS ( Tata Kelola Rumah Sakit)', '2025-08-13 21:03:36'),
(8, 'MFK ( Manajemen Fasilitas dan Keselamatan)', '2025-08-13 21:03:51'),
(9, 'KPS ( Kualifikasi dan Pendidikan Staf)', '2025-08-13 21:04:06'),
(10, 'MRMIK (Manajemen Rekam Medis dan Informasi Kesehatan)', '2025-08-13 21:04:23'),
(11, 'PPI (Pencegahan dan Pengendalian Infeksi)', '2025-08-13 21:04:36'),
(12, 'PMKP (Peningkatan Mutu dan Keselamatan Pasien)', '2025-08-13 21:04:53'),
(13, 'PROGNAS (Program Nasional)', '2025-08-13 21:05:10'),
(14, 'PPK (Pelayanan dan Pengelolaan Klinis)', '2025-08-13 21:05:23'),
(15, 'AKP (Asuhan Keperawatan)', '2025-08-13 21:05:39'),
(16, 'HPK (Hak Pasien dan Keluarga)', '2025-08-13 21:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `master_url`
--

CREATE TABLE `master_url` (
  `id` int(11) NOT NULL,
  `nama_koneksi` varchar(100) NOT NULL,
  `base_url` varchar(255) NOT NULL,
  `status_last` varchar(10) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama_menu` varchar(255) DEFAULT NULL,
  `file_menu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama_menu`, `file_menu`) VALUES
(1, 'Dashboard', 'dashboard.php'),
(5, 'Data Tiket IT Hard', 'data_tiket_it_hardware.php'),
(6, 'Data Tiket IT Soft', 'data_tiket_it_software.php'),
(7, 'Data Off-Duty', 'data_off_duty.php'),
(8, 'Tiket Off-Duty', 'off_duty.php'),
(9, 'Tiket IT Hard', 'order_tiket_it_hardware.php'),
(10, 'Tiket IT Soft', 'order_tiket_it_software.php'),
(11, 'Handling Time Hardware', 'handling_time.php'),
(12, 'SPO IT', 'spo_it.php'),
(13, 'Input SPO IT', 'input_spo_it.php'),
(15, 'Data Barang IT', 'data_barang_it.php'),
(16, 'Maintenance Rutin', 'maintenance_rutin.php'),
(17, 'Transaksi Gaji', 'input_gaji.php'),
(18, 'Data Gaji', 'data_gaji.php'),
(19, 'Masa Kerja', 'masa_kerja.php'),
(20, 'Kesehatan', 'kesehatan.php'),
(21, 'Fungsional', 'fungsional.php'),
(22, 'Struktural', 'struktural.php'),
(23, 'Gaji Pokok', 'gaji_pokok.php'),
(24, 'Potongan BPJS Kesehatan', 'potongan_bpjs_kes.php'),
(25, 'Potongan BPJS TK JHT', 'potongan_bpjs_jht.php'),
(26, 'Potongan BPJS TK JP', 'potongan_bpjs_tk_jp.php'),
(27, 'Potongan Dana Sosial', 'potongan_dana_sosial.php'),
(28, 'Potongan PPH 21', 'pph21.php'),
(29, 'Surat Masuk', 'surat_masuk.php'),
(30, 'Surat Keluar', 'surat_keluar.php'),
(31, 'Arsip Digital', 'arsip_digital.php'),
(32, 'Agenda Direktur', 'agenda_direktur.php'),
(33, 'Lihat Agenda', 'lihat_agenda.php'),
(34, 'Kategori Arsip', 'kategori_arsip.php'),
(35, 'Laporan Harian', 'laporan_harian.php'),
(36, 'Laporan Bulanan', 'laporan_bulanan.php'),
(37, 'Laporan Tahunan', 'laporan_tahunan.php'),
(38, 'Data Lap Harian', 'data_laporan_harian.php'),
(39, 'Data Lap Bulanan', 'data_laporan_bulanan.php'),
(40, 'Data Lap Tahunan', 'data_laporan_tahunan.php'),
(41, 'SIP & STR', 'sip_str.php'),
(42, 'Izin Keluar', 'izin_keluar.php'),
(43, 'Master Cuti', 'master_cuti.php'),
(44, 'Pengajuan Cuti', 'pengajuan_cuti.php'),
(45, 'Jatah Cuti', 'jatah_cuti.php'),
(46, 'Data Cuti', 'data_cuti.php'),
(47, 'Persetujuan Izin Keluar', 'persetujuan_izin.php'),
(48, 'Perusahaan', 'perusahaan.php'),
(49, 'Pengguna', 'pengguna.php'),
(50, 'Hak Akses', 'hak_akses.php'),
(51, 'Mail Settings', 'mail_setting.php'),
(52, 'Telegram Settings', 'tele_setting.php'),
(53, 'Kategori Hardware', 'kategori_hardware.php'),
(54, 'Kategori Software', 'kategori_software.php'),
(55, 'Unit Kerja', 'unit_kerja.php'),
(56, 'Jabatan', 'jabatan.php'),
(57, 'Akun Saya', 'profile.php'),
(59, 'ACC Keluar Atasan', 'acc_keluar_atasan.php'),
(60, 'ACC Keluar SDM', 'acc_keluar_sdm.php'),
(61, 'Handling Time Software', 'handling_time.php'),
(62, 'Berita Acara IT', 'berita_acara_it.php'),
(69, 'Master IMP', 'master_imp.php'),
(70, 'Data Izin Keluar', 'data_izin_keluar.php'),
(71, 'Dokumen Akreditasi', 'data_dokumen.php'),
(72, 'Input Dokumen Akreditasi', 'input_dokumen.php'),
(73, 'Master Pokja', 'master_pokja.php'),
(74, 'Master URL', 'master_url.php'),
(75, 'Koneksi Bridging', 'koneksi_bridging.php'),
(76, 'Data Pelamar', 'data_pelamar.php'),
(77, 'Lowongan Kerja', 'lowongan.php'),
(78, 'Lamaran Masuk', 'lamaran.php'),
(79, 'Web Dokter', 'dokter.php'),
(80, 'Web Sejarah', 'sejarah.php'),
(81, 'Web Visi Misi', 'visi_misi.php'),
(82, 'Web Kerja Sama', 'kerja_sama.php'),
(83, 'Web Fasilitas', 'fasilitas.php'),
(84, 'Web Berita', 'berita.php'),
(85, 'Web Kuisioner', 'kuisioner.php'),
(86, 'Web Dokumentasi', 'dokumentasi.php'),
(87, 'Web Kontak', 'kontak.php'),
(88, 'Whatsapp Setting', 'wa_setting.php'),
(98, 'Handling Time Software', 'handling_time_software.php'),
(99, 'Data Karyawan', 'data_karyawan.php'),
(100, 'Catatan Kerja', 'catatan_kerja.php'),
(101, 'Rekap Kerja', 'rekap_catatan_kerja.php'),
(102, 'Imut IMP Unit', 'indikator_imp_unit.php'),
(103, 'Input IMP Unit', 'pengukuran_imp_unit.php'),
(104, 'Capaian IMP Unit', 'capaian_imp_unit.php'),
(105, 'Master Imut Nasional', 'master_imut_nasional.php'),
(106, 'Input Imut Nas', 'capaian_imut_nasional.php'),
(107, 'Master IMN NEW', 'master_indikator.php'),
(108, 'Master IMUT RS', 'master_indikator_rs.php'),
(109, 'Master IMUT UNIT NEW', 'master_indikator_unit.php'),
(110, 'Input IMUT harian NEW', 'input_harian.php'),
(111, 'Capaian IMUT NEW', 'capaian_imut.php'),
(112, 'Data Karyawan 2', 'data_karyawan2.php'),
(113, 'Profil Saya', 'profile2.php');

-- --------------------------------------------------------

--
-- Table structure for table `password_resset`
--

CREATE TABLE `password_resset` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `password_resset`
--

INSERT INTO `password_resset` (`id`, `email`, `token`, `expires_at`) VALUES
(6, 'wsatria630@gmail.com', '09b10a89d3e61b6523690dc4f6d0a4fc84c30ef9563a3d86240c9130a562f43b', '2025-08-25 09:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `pengukuran_indikator`
--

CREATE TABLE `pengukuran_indikator` (
  `id` int(11) NOT NULL,
  `indikator_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `periode` varchar(7) NOT NULL,
  `numerator` int(11) NOT NULL,
  `denominator` int(11) NOT NULL,
  `capaian` decimal(10,4) DEFAULT NULL,
  `dibuat_oleh` varchar(150) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id` int(11) NOT NULL,
  `nama_perusahaan` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kontak` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id`, `nama_perusahaan`, `alamat`, `kota`, `provinsi`, `kontak`, `email`, `logo`, `created_at`) VALUES
(12, 'FixPoint Smart Office Management System', 'Perumnas, Blok A.', 'Bungo', 'Jambi', '0821 7784 6209', 'wiramuhammad16@gmail.com', 'logo_6899e38780b05.png', '2025-08-07 13:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `pesan`
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `pengirim_id` int(11) NOT NULL,
  `penerima_id` int(11) NOT NULL,
  `isi` text NOT NULL,
  `waktu_kirim` datetime DEFAULT current_timestamp(),
  `status` enum('terkirim','dibaca') DEFAULT 'terkirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `potongan_bpjs_jht`
--

CREATE TABLE `potongan_bpjs_jht` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `potongan_bpjs_jht`
--

INSERT INTO `potongan_bpjs_jht` (`id`, `nominal`, `created_at`) VALUES
(1, 45000, '2025-07-26 12:26:21');

-- --------------------------------------------------------

--
-- Table structure for table `potongan_bpjs_kes`
--

CREATE TABLE `potongan_bpjs_kes` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `potongan_bpjs_kes`
--

INSERT INTO `potongan_bpjs_kes` (`id`, `nominal`, `created_at`) VALUES
(1, 135000, '2025-07-26 12:23:39'),
(2, 200000, '2025-07-26 15:48:26');

-- --------------------------------------------------------

--
-- Table structure for table `potongan_bpjs_tk_jp`
--

CREATE TABLE `potongan_bpjs_tk_jp` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `potongan_bpjs_tk_jp`
--

INSERT INTO `potongan_bpjs_tk_jp` (`id`, `nominal`, `created_at`) VALUES
(1, 45000, '2025-07-26 12:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `potongan_dana_sosial`
--

CREATE TABLE `potongan_dana_sosial` (
  `id` int(11) NOT NULL,
  `nominal` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `potongan_dana_sosial`
--

INSERT INTO `potongan_dana_sosial` (`id`, `nominal`) VALUES
(1, 15000),
(2, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `pph21`
--

CREATE TABLE `pph21` (
  `id` int(11) NOT NULL,
  `gaji_min` int(11) NOT NULL,
  `gaji_max` int(11) NOT NULL,
  `persentase` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pph21`
--

INSERT INTO `pph21` (`id`, `gaji_min`, `gaji_max`, `persentase`, `created_at`) VALUES
(8, 100000, 6000000, '0.50', '2025-07-28 02:17:15'),
(9, 6000001, 10000000, '0.70', '2025-07-28 02:31:23');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_kesehatan`
--

CREATE TABLE `riwayat_kesehatan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gol_darah` enum('A','B','AB','O') DEFAULT NULL,
  `riwayat_penyakit` text DEFAULT NULL,
  `status_vaksinasi` varchar(100) DEFAULT NULL,
  `no_bpjs_kesehatan` varchar(50) DEFAULT NULL,
  `no_bpjs_kerja` varchar(50) DEFAULT NULL,
  `asuransi_tambahan` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `riwayat_kesehatan`
--

INSERT INTO `riwayat_kesehatan` (`id`, `user_id`, `gol_darah`, `riwayat_penyakit`, `status_vaksinasi`, `no_bpjs_kesehatan`, `no_bpjs_kerja`, `asuransi_tambahan`, `created_at`, `updated_at`) VALUES
(1, 5, 'A', 'Tidak Ada', 'Vaksin Covid 1, Vaksin Covid 2, Vaksin Covid 3', '00008218889890001', '00089898919909001', 'Tidak Ada', '2025-09-14 14:53:06', '2025-09-14 15:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pekerjaan`
--

CREATE TABLE `riwayat_pekerjaan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `posisi` varchar(255) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `alasan_keluar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `riwayat_pekerjaan`
--

INSERT INTO `riwayat_pekerjaan` (`id`, `user_id`, `nama_perusahaan`, `posisi`, `tanggal_mulai`, `tanggal_selesai`, `alasan_keluar`) VALUES
(7, 5, 'PT Mencari Cinta Sejati TES EDIT', 'SPV', '2020-01-01', '2024-01-01', 'tes edit');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pendidikan`
--

CREATE TABLE `riwayat_pendidikan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pendidikan_terakhir` varchar(50) NOT NULL,
  `jurusan` varchar(150) DEFAULT NULL,
  `kampus` varchar(150) DEFAULT NULL,
  `tgl_lulus` date DEFAULT NULL,
  `no_ijazah` varchar(100) DEFAULT NULL,
  `ipk` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `riwayat_pendidikan`
--

INSERT INTO `riwayat_pendidikan` (`id`, `user_id`, `pendidikan_terakhir`, `jurusan`, `kampus`, `tgl_lulus`, `no_ijazah`, `ipk`, `created_at`, `updated_at`) VALUES
(1, 5, 'D3', 'Sistem Informasi ', 'UNH (Universitas Nurdin Hamzah) Jambi', '2015-10-05', '001/0001/0001/001', '3.75', '2025-09-14 13:58:07', '2025-09-14 15:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nilai` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `spo_it`
--

CREATE TABLE `spo_it` (
  `id` int(11) NOT NULL,
  `nomor_spo` varchar(100) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `file_spo` varchar(255) NOT NULL,
  `petugas_upload` varchar(100) NOT NULL,
  `tanggal_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `struktural`
--

CREATE TABLE `struktural` (
  `id` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `struktural`
--

INSERT INTO `struktural` (`id`, `nominal`, `created_at`) VALUES
(1, 2000000, '2025-07-26 12:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id` int(11) NOT NULL,
  `no_surat` varchar(100) NOT NULL,
  `tgl_surat` date NOT NULL,
  `tgl_kirim` date NOT NULL,
  `tujuan` varchar(255) NOT NULL,
  `perihal` varchar(255) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `isi_ringkas` text DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `user_input` int(11) NOT NULL,
  `balasan_untuk_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id` int(11) NOT NULL,
  `no_surat` varchar(100) NOT NULL,
  `tgl_surat` date NOT NULL,
  `tgl_terima` date NOT NULL,
  `pengirim` varchar(255) NOT NULL,
  `asal_surat` varchar(255) DEFAULT NULL,
  `perihal` text DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `jenis_surat` varchar(100) DEFAULT NULL,
  `sifat_surat` varchar(50) DEFAULT NULL,
  `perlu_balasan` enum('Ya','Tidak') DEFAULT 'Tidak',
  `status_balasan` enum('Belum Dibalas','Sudah Dibalas','Tidak Perlu Dibalas') DEFAULT 'Belum Dibalas',
  `disposisi_ke` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `user_input` int(11) DEFAULT NULL,
  `waktu_input` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tiket_it_hardware`
--

CREATE TABLE `tiket_it_hardware` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `kendala` text DEFAULT NULL,
  `nomor_tiket` varchar(50) DEFAULT NULL,
  `tanggal_input` datetime DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Tidak Bisa Diperbaiki','Ditolak') DEFAULT 'Menunggu',
  `waktu_diproses` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `status_validasi` enum('Belum Validasi','Diterima','Ditolak') DEFAULT 'Belum Validasi',
  `waktu_validasi` datetime DEFAULT NULL,
  `waktu_ditolak` datetime DEFAULT NULL,
  `waktu_tidak_bisa_diperbaiki` datetime DEFAULT NULL,
  `teknisi_nama` varchar(100) DEFAULT NULL,
  `catatan_it` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tiket_it_hardware`
--

INSERT INTO `tiket_it_hardware` (`id`, `user_id`, `nik`, `nama`, `jabatan`, `unit_kerja`, `kategori`, `kendala`, `nomor_tiket`, `tanggal_input`, `status`, `waktu_diproses`, `waktu_selesai`, `status_validasi`, `waktu_validasi`, `waktu_ditolak`, `waktu_tidak_bisa_diperbaiki`, `teknisi_nama`, `catatan_it`) VALUES
(35, 4, '16216009', 'giano', 'Perawat', 'Rawat Inap LT III Umum', 'Alarm Fire Emergency', 'TES HARDWARE ORDER', 'TKT0001/IT-HARD/08/08/2025', '2025-08-08 09:25:23', 'Selesai', NULL, '2025-08-08 09:25:55', 'Diterima', '2025-08-08 09:26:04', NULL, NULL, 'M Wira Satria Buana, S. Kom', 'sudah selesai ya'),
(36, 4, '16216009', 'M. Giano Shaquille Wiandra', 'Apoteker', 'Instalasi Farmasi', 'Komputer Set', 'Komputer di ruangan tidak bisa menyala, menyala sebentar setelah itu mati lagi.', 'TKT0001/IT-HARD/11/08/2025', '2025-08-11 19:04:33', 'Selesai', '2025-08-11 19:06:41', '2025-08-11 19:09:31', 'Diterima', '2025-08-11 19:12:00', NULL, NULL, 'M Wira Satria Buana, S. Kom', 'sudah selesai ya. Harap di aprove');

-- --------------------------------------------------------

--
-- Table structure for table `tiket_it_software`
--

CREATE TABLE `tiket_it_software` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `kendala` text DEFAULT NULL,
  `nomor_tiket` varchar(50) DEFAULT NULL,
  `tanggal_input` datetime DEFAULT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Tidak Bisa Diperbaiki') DEFAULT 'Menunggu',
  `status_validasi` enum('Belum Validasi','Diterima','Ditolak') DEFAULT 'Belum Validasi',
  `waktu_validasi` datetime DEFAULT NULL,
  `waktu_ditolak` datetime DEFAULT NULL,
  `waktu_tidak_bisa_diperbaiki` datetime DEFAULT NULL,
  `teknisi_nama` varchar(100) DEFAULT NULL,
  `catatan_it` text DEFAULT NULL,
  `waktu_diproses` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tiket_it_software`
--

INSERT INTO `tiket_it_software` (`id`, `user_id`, `nik`, `nama`, `jabatan`, `unit_kerja`, `kategori`, `kendala`, `nomor_tiket`, `tanggal_input`, `status`, `status_validasi`, `waktu_validasi`, `waktu_ditolak`, `waktu_tidak_bisa_diperbaiki`, `teknisi_nama`, `catatan_it`, `waktu_diproses`, `waktu_selesai`) VALUES
(14, 4, '16216009', 'giano', 'Perawat', 'Rawat Inap LT III Umum', 'Jaringan Wifi / Acces Point', 'LELET TES SOFTWARE', 'TKT0001/IT-SOFT/08/08/2025', '2025-08-08 09:26:24', 'Selesai', 'Diterima', '2025-08-08 09:26:49', NULL, NULL, 'M Wira Satria Buana, S. Kom', 'sudah ya', NULL, '2025-08-08 09:26:42'),
(15, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Bridging BPJS Kes', 'tesss', 'TKT0002/IT-SOFT/08/08/2025', '2025-08-08 11:18:49', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Bridging BPJS Kes', 'tesss', 'TKT0003/IT-SOFT/08/08/2025', '2025-08-08 11:25:13', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'E-Klaim', 'asdas', 'TKT0004/IT-SOFT/08/08/2025', '2025-08-08 11:27:07', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Bridging BPJS Kes', 'tesss', 'TKT0005/IT-SOFT/08/08/2025', '2025-08-08 11:41:10', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Bridging BPJS Kes', 'tesssss', 'TKT0006/IT-SOFT/08/08/2025', '2025-08-08 11:42:43', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'Bridging BPJS Kes', 'tesss', 'TKT0007/IT-SOFT/08/08/2025', '2025-08-08 11:43:38', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 4, '16216009', 'M. Giano Shaquille Wiandra', 'Apoteker', 'Instalasi Farmasi', 'System Antrian', 'panggil antrian farmasi tidak berfungsi', 'TKT0001/IT-SOFT/11/08/2025', '2025-08-11 21:53:20', 'Selesai', 'Diterima', '2025-08-11 21:58:00', NULL, NULL, 'M Wira Satria Buana, S. Kom', 'sudah normal kembali , harap di coba, dan aprove.', '2025-08-11 21:55:20', '2025-08-11 21:56:45'),
(22, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'System Antrian', 'tes', 'TKT0001/IT-SOFT/13/08/2025', '2025-08-13 21:31:21', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 4, '16216009', 'M. Giano Shaquille Wiandra', 'IT Software', 'IT & Marketing', 'Custom Khanza', 'tes notif wa', 'TKT0001/IT-SOFT/19/08/2025', '2025-08-19 19:37:38', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'System Antrian', 'tes', 'TKT0002/IT-SOFT/19/08/2025', '2025-08-19 20:27:56', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'System Antrian', 'tes', 'TKT0001/IT-SOFT/22/08/2025', '2025-08-22 23:16:10', 'Selesai', 'Diterima', '2025-08-22 23:17:03', NULL, NULL, 'M Wira Satria Buana, S. Kom', 'selesai', NULL, '2025-08-22 23:16:50'),
(26, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'SIMRS Khanza', 'tesss lgi', 'TKT0002/IT-SOFT/22/08/2025', '2025-08-22 23:17:43', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 5, '16210646', 'M Wira Satria Buana, S. Kom', 'IT Software', 'IT & Marketing', 'SIMRS Khanza', 'tesss lgi', 'TKT0003/IT-SOFT/22/08/2025', '2025-08-22 23:17:44', 'Menunggu', 'Belum Validasi', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `unit_kerja`
--

CREATE TABLE `unit_kerja` (
  `id` int(11) NOT NULL,
  `nama_unit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `unit_kerja`
--

INSERT INTO `unit_kerja` (`id`, `nama_unit`) VALUES
(7, 'Ruang VK'),
(8, 'SDM & Keuangan'),
(9, 'Umum'),
(10, 'Pendaftaran'),
(11, 'Poliklinik'),
(12, 'Ruang OK'),
(13, 'IGD'),
(14, 'Inst. Farmasi'),
(15, 'Depo Farmasi'),
(16, 'Inst. Gizi'),
(17, 'IPSRS'),
(18, 'Laboratorium'),
(19, 'ICU'),
(20, 'PICU-NICU'),
(21, 'Rawat Inap LT II'),
(22, 'Rawat Inap LT III Umum'),
(23, 'Rawat Inap LT III Obgyn'),
(24, 'Radiologi'),
(25, 'Direksi'),
(26, 'IT & Marketing'),
(27, 'Rekam Medis'),
(28, 'Casemix'),
(29, 'Inst. Gizi'),
(30, 'Komite Rumah sakit'),
(31, 'Gudang Farmasi'),
(32, 'PRT'),
(33, 'Kasir'),
(34, 'Verifikator');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `atasan_id` int(11) DEFAULT NULL,
  `status` enum('pending','active') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nik`, `nama`, `jabatan`, `unit_kerja`, `email`, `no_hp`, `password_hash`, `atasan_id`, `status`, `created_at`, `last_login`) VALUES
(4, '16216009', 'M. Giano Shaquille Wiandra', 'Perawat', 'Rawat Inap Lantai 2', 'giano@gmail.com', '6283199354543', '$2y$10$x9ucnyQVIHH4pC4Vmqwjxu/L0ScuAsHl6wgH2dY06elCeSrJZ9rT.', 5, 'active', '2025-07-24 17:09:10', '2025-08-25 11:27:56'),
(5, '16216046', 'M Wira Satria Buana, S. Kom', 'IT Hardware', 'Umum', 'wiramuhammad16@gmail.com', '6282177846209', '$2y$10$eJRH.gqvlGI8wOjE09HmOuefHcnLDfktQEOLROWXlpGin23pEstpe', 4, 'active', '2025-07-28 04:07:54', '2025-09-14 21:08:05');

-- --------------------------------------------------------

--
-- Table structure for table `wa_setting`
--

CREATE TABLE `wa_setting` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nilai` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agenda_direktur`
--
ALTER TABLE `agenda_direktur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `akses_menu`
--
ALTER TABLE `akses_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `arsip_digital`
--
ALTER TABLE `arsip_digital`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `berita_acara`
--
ALTER TABLE `berita_acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `berita_acara_software`
--
ALTER TABLE `berita_acara_software`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `capaian`
--
ALTER TABLE `capaian`
  ADD PRIMARY KEY (`id_capaian`);

--
-- Indexes for table `catatan_kerja`
--
ALTER TABLE `catatan_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `data_barang_it`
--
ALTER TABLE `data_barang_it`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pokja_id` (`pokja_id`);

--
-- Indexes for table `dokumen_pendukung`
--
ALTER TABLE `dokumen_pendukung`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fungsional`
--
ALTER TABLE `fungsional`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gaji_pokok`
--
ALTER TABLE `gaji_pokok`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `indikator_harian`
--
ALTER TABLE `indikator_harian`
  ADD PRIMARY KEY (`id_harian`),
  ADD KEY `jenis_indikator` (`jenis_indikator`,`id_indikator`,`tanggal`);

--
-- Indexes for table `indikator_nasional`
--
ALTER TABLE `indikator_nasional`
  ADD PRIMARY KEY (`id_nasional`),
  ADD KEY `fk_nasional_unit` (`unit_id`);

--
-- Indexes for table `indikator_rs`
--
ALTER TABLE `indikator_rs`
  ADD PRIMARY KEY (`id_rs`),
  ADD KEY `fk_rs_nasional` (`id_nasional`);

--
-- Indexes for table `indikator_unit`
--
ALTER TABLE `indikator_unit`
  ADD PRIMARY KEY (`id_unit`),
  ADD KEY `fk_unit_rs` (`id_rs`),
  ADD KEY `fk_unit_unit` (`unit_id`);

--
-- Indexes for table `input_gaji`
--
ALTER TABLE `input_gaji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `izin_keluar`
--
ALTER TABLE `izin_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuti_id` (`cuti_id`);

--
-- Indexes for table `kategori_arsip`
--
ALTER TABLE `kategori_arsip`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_hardware`
--
ALTER TABLE `kategori_hardware`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_pelaporan`
--
ALTER TABLE `kategori_pelaporan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_software`
--
ALTER TABLE `kategori_software`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kesehatan`
--
ALTER TABLE `kesehatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_bulanan`
--
ALTER TABLE `laporan_bulanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_off_duty`
--
ALTER TABLE `laporan_off_duty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_tiket` (`no_tiket`);

--
-- Indexes for table `laporan_tahunan`
--
ALTER TABLE `laporan_tahunan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_settings`
--
ALTER TABLE `mail_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintanance_rutin`
--
ALTER TABLE `maintanance_rutin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`barang_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `masa_kerja`
--
ALTER TABLE `masa_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_cuti`
--
ALTER TABLE `master_cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_indikator`
--
ALTER TABLE `master_indikator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `master_pokja`
--
ALTER TABLE `master_pokja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_url`
--
ALTER TABLE `master_url`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resset`
--
ALTER TABLE `password_resset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pengukuran_indikator`
--
ALTER TABLE `pengukuran_indikator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indikator_id` (`indikator_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penerima_id` (`penerima_id`),
  ADD KEY `pengirim_id` (`pengirim_id`);

--
-- Indexes for table `potongan_bpjs_jht`
--
ALTER TABLE `potongan_bpjs_jht`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `potongan_bpjs_kes`
--
ALTER TABLE `potongan_bpjs_kes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `potongan_bpjs_tk_jp`
--
ALTER TABLE `potongan_bpjs_tk_jp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `potongan_dana_sosial`
--
ALTER TABLE `potongan_dana_sosial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pph21`
--
ALTER TABLE `pph21`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat_kesehatan`
--
ALTER TABLE `riwayat_kesehatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_pekerjaan`
--
ALTER TABLE `riwayat_pekerjaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `riwayat_pendidikan`
--
ALTER TABLE `riwayat_pendidikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_pendidikan` (`user_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spo_it`
--
ALTER TABLE `spo_it`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `struktural`
--
ALTER TABLE `struktural`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `balasan_untuk_id` (`balasan_untuk_id`),
  ADD KEY `user_input` (`user_input`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_input` (`user_input`);

--
-- Indexes for table `tiket_it_hardware`
--
ALTER TABLE `tiket_it_hardware`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tiket_it_software`
--
ALTER TABLE `tiket_it_software`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unit_kerja`
--
ALTER TABLE `unit_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wa_setting`
--
ALTER TABLE `wa_setting`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agenda_direktur`
--
ALTER TABLE `agenda_direktur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `akses_menu`
--
ALTER TABLE `akses_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5635;

--
-- AUTO_INCREMENT for table `arsip_digital`
--
ALTER TABLE `arsip_digital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `berita_acara`
--
ALTER TABLE `berita_acara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `berita_acara_software`
--
ALTER TABLE `berita_acara_software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `capaian`
--
ALTER TABLE `capaian`
  MODIFY `id_capaian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catatan_kerja`
--
ALTER TABLE `catatan_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `data_barang_it`
--
ALTER TABLE `data_barang_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dokumen_pendukung`
--
ALTER TABLE `dokumen_pendukung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fungsional`
--
ALTER TABLE `fungsional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gaji_pokok`
--
ALTER TABLE `gaji_pokok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `indikator_harian`
--
ALTER TABLE `indikator_harian`
  MODIFY `id_harian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `indikator_nasional`
--
ALTER TABLE `indikator_nasional`
  MODIFY `id_nasional` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `indikator_rs`
--
ALTER TABLE `indikator_rs`
  MODIFY `id_rs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `indikator_unit`
--
ALTER TABLE `indikator_unit`
  MODIFY `id_unit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `input_gaji`
--
ALTER TABLE `input_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `izin_keluar`
--
ALTER TABLE `izin_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kategori_arsip`
--
ALTER TABLE `kategori_arsip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `kategori_hardware`
--
ALTER TABLE `kategori_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `kategori_pelaporan`
--
ALTER TABLE `kategori_pelaporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `kategori_software`
--
ALTER TABLE `kategori_software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `kesehatan`
--
ALTER TABLE `kesehatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_bulanan`
--
ALTER TABLE `laporan_bulanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_off_duty`
--
ALTER TABLE `laporan_off_duty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `laporan_tahunan`
--
ALTER TABLE `laporan_tahunan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mail_settings`
--
ALTER TABLE `mail_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `maintanance_rutin`
--
ALTER TABLE `maintanance_rutin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `masa_kerja`
--
ALTER TABLE `masa_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_cuti`
--
ALTER TABLE `master_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `master_indikator`
--
ALTER TABLE `master_indikator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `master_pokja`
--
ALTER TABLE `master_pokja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `master_url`
--
ALTER TABLE `master_url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `password_resset`
--
ALTER TABLE `password_resset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pengukuran_indikator`
--
ALTER TABLE `pengukuran_indikator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `potongan_bpjs_jht`
--
ALTER TABLE `potongan_bpjs_jht`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `potongan_bpjs_kes`
--
ALTER TABLE `potongan_bpjs_kes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `potongan_bpjs_tk_jp`
--
ALTER TABLE `potongan_bpjs_tk_jp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `potongan_dana_sosial`
--
ALTER TABLE `potongan_dana_sosial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pph21`
--
ALTER TABLE `pph21`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `riwayat_kesehatan`
--
ALTER TABLE `riwayat_kesehatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_pekerjaan`
--
ALTER TABLE `riwayat_pekerjaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `riwayat_pendidikan`
--
ALTER TABLE `riwayat_pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `spo_it`
--
ALTER TABLE `spo_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `struktural`
--
ALTER TABLE `struktural`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tiket_it_hardware`
--
ALTER TABLE `tiket_it_hardware`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tiket_it_software`
--
ALTER TABLE `tiket_it_software`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `unit_kerja`
--
ALTER TABLE `unit_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `wa_setting`
--
ALTER TABLE `wa_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `catatan_kerja`
--
ALTER TABLE `catatan_kerja`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `indikator_nasional`
--
ALTER TABLE `indikator_nasional`
  ADD CONSTRAINT `fk_nasional_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `indikator_rs`
--
ALTER TABLE `indikator_rs`
  ADD CONSTRAINT `fk_rs_nasional` FOREIGN KEY (`id_nasional`) REFERENCES `indikator_nasional` (`id_nasional`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `indikator_unit`
--
ALTER TABLE `indikator_unit`
  ADD CONSTRAINT `fk_unit_rs` FOREIGN KEY (`id_rs`) REFERENCES `indikator_rs` (`id_rs`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_unit_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `input_gaji`
--
ALTER TABLE `input_gaji`
  ADD CONSTRAINT `input_gaji_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `jatah_cuti`
--
ALTER TABLE `jatah_cuti`
  ADD CONSTRAINT `jatah_cuti_ibfk_1` FOREIGN KEY (`cuti_id`) REFERENCES `master_cuti` (`id`);

--
-- Constraints for table `laporan_bulanan`
--
ALTER TABLE `laporan_bulanan`
  ADD CONSTRAINT `laporan_bulanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_tahunan`
--
ALTER TABLE `laporan_tahunan`
  ADD CONSTRAINT `laporan_tahunan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintanance_rutin`
--
ALTER TABLE `maintanance_rutin`
  ADD CONSTRAINT `maintanance_rutin_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `data_barang_it` (`id`),
  ADD CONSTRAINT `maintanance_rutin_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `master_indikator`
--
ALTER TABLE `master_indikator`
  ADD CONSTRAINT `master_indikator_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pesan`
--
ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesan_ibfk_2` FOREIGN KEY (`penerima_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_kesehatan`
--
ALTER TABLE `riwayat_kesehatan`
  ADD CONSTRAINT `riwayat_kesehatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_pekerjaan`
--
ALTER TABLE `riwayat_pekerjaan`
  ADD CONSTRAINT `riwayat_pekerjaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_pendidikan`
--
ALTER TABLE `riwayat_pendidikan`
  ADD CONSTRAINT `fk_user_pendidikan` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD CONSTRAINT `surat_keluar_ibfk_1` FOREIGN KEY (`balasan_untuk_id`) REFERENCES `surat_masuk` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `surat_keluar_ibfk_2` FOREIGN KEY (`user_input`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD CONSTRAINT `surat_masuk_ibfk_1` FOREIGN KEY (`user_input`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
