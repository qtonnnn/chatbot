-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 05:53 AM
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
-- Database: `chatbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `role` enum('user','assistant','system') DEFAULT NULL,
  `content` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_history`
--

INSERT INTO `chat_history` (`id`, `user_id`, `role`, `content`, `timestamp`) VALUES
(1, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'halo', '2025-08-20 03:10:29'),
(2, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Halo! Apa kabar? Ada yang bisa saya bantu hari ini?', '2025-08-20 03:10:31'),
(3, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'nama kamu sekarang aldi', '2025-08-20 03:10:42'),
(4, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Baiklah, mulai sekarang saya akan dipanggil Aldi. Ada yang ingin kamu bicarakan, atau ada yang bisa saya bantu?', '2025-08-20 03:10:44'),
(5, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'buatkan saya 1 paragraf tentang jakarta', '2025-08-20 03:11:03'),
(6, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Jakarta, ibu kota Indonesia, adalah sebuah kota yang penuh dinamika dan keberagaman. Sebagai pusat politik, ekonomi, dan budaya, Jakarta menawarkan berbagai atraksi mulai dari gedung-gedung pencakar langit yang modern hingga situs bersejarah yang mencerminkan warisan budaya Indonesia. Dengan penduduk yang berasal dari berbagai suku dan latar belakang, atmosfer kota ini sangatlah hidup dan berwarna. Selain itu, Jakarta juga dikenal dengan tantangan lalu lintas yang padat, namun di balik itu, terdapat banyak tempat menarik seperti Monas, Kota Tua, dan berbagai pusat perbelanjaan serta kuliner yang menggugah selera. Seiring dengan pertumbuhannya, Jakarta terus berusaha beradaptasi dengan kebutuhan penduduknya serta peningkatan infrastruktur untuk menciptakan lingkungan yang lebih nyaman bagi semua.', '2025-08-20 03:11:06'),
(7, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'buatkan tentang surabaya juga', '2025-08-20 03:11:29'),
(8, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Surabaya, yang dikenal sebagai \"Kota Pahlawan,\" adalah ibu kota Provinsi Jawa Timur dan merupakan salah satu kota terbesar di Indonesia. Kota ini memiliki sejarah yang kaya, terutama sebagai pusat perjuangan kemerdekaan yang terlihat dari banyaknya monumen dan museum yang menghormati para pahlawan nasional. Surabaya menawarkan beragam daya tarik, mulai dari jembatan Suramadu yang megah, kebun binatang terbesar di Indonesia, hingga kawasan kuliner yang terkenal dengan makanan khasnya seperti rawon dan rujak cingur. Selain itu, Surabaya juga merupakan pusat perdagangan dan industri yang penting, dengan pelabuhan Tanjung Perak yang menghubungkan Indonesia dengan negara lain. Dengan kombinasi modernitas dan tradisi, Surabaya menjadi kota yang dinamis, penuh energi, serta menyajikan pengalaman yang unik bagi pengunjung dan penduduknya.', '2025-08-20 03:11:32'),
(9, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'nama kamu siapa', '2025-08-20 03:11:41'),
(10, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Saya Aldi! Sudah disebutkan sebelumnya, tetapi kamu bisa memanggil saya dengan nama itu. Ada yang ingin kamu tanyakan atau diskusikan lebih lanjut?', '2025-08-20 03:11:43'),
(11, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'siapa nama kamu', '2025-08-20 03:17:21'),
(12, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Nama saya Aldi! Ada yang ingin kamu ketahui lebih lanjut?', '2025-08-20 03:17:23'),
(13, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'apa kamu tau bangkalan', '2025-08-20 03:17:41'),
(14, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Ya, Bangkalan adalah sebuah kabupaten yang terletak di provinsi Jawa Timur, Indonesia. Kabupaten ini terletak di Pulau Madura dan merupakan daerah yang kaya akan budaya serta tradisi. Salah satu ciri khas Bangkalan adalah kerajinan batik Madura, yang terkenal dengan motifnya yang unik dan warna-warni. Selain itu, Bangkalan juga memiliki sejumlah objek wisata menarik, seperti budaya lokal, kuliner khas, dan pantai-pantai yang indah. Salah satu objek wisata terkenal di Bangkalan adalah Masjid Agung Bangkalan yang memiliki arsitektur yang menawan. Apakah ada informasi khusus tentang Bangkalan yang ingin kamu ketahui?', '2025-08-20 03:17:44'),
(15, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'apa arti dari pelor', '2025-08-20 03:18:06'),
(16, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Pelor memiliki beberapa arti tergantung konteksnya. Dalam bahasa sehari-hari di Indonesia, \"pelor\" sering merujuk pada peluru atau proyektil yang digunakan dalam senjata api. Selain itu, dalam konteks non-teknis, \"pelor\" juga bisa digunakan untuk menggambarkan sesuatu yang bulat dan keras. Jika ada konteks tertentu yang kamu maksud, silakan beri tahu saya agar saya bisa memberikan penjelasan yang lebih spesifik!', '2025-08-20 03:18:08'),
(17, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'hewan karnivora apa saja', '2025-08-20 03:18:25'),
(18, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Hewan karnivora adalah hewan yang sebagian besar atau sepenuhnya memakan daging. Berikut adalah beberapa contoh hewan karnivora:\n\n1. **Singa** - Sebagai predator puncak di savana, singa berburu mangsa seperti zebra dan rusa.\n2. **Serigala** - Hewan sosial yang sering berburu dalam rombongan, mereka sering memangsa hewan besar seperti rusa.\n3. **Harimau** - Menjadi predator tunggal, harimau berburu hewan seperti babi hutan dan rusa.\n4. **Beruang kutub** - Hewan besar yang sebagian besar memakan ikan dan mamalia laut.\n5. **Hiu** - Dikenal sebagai predator di lautan, banyak spesies hiu memangsa ikan dan mamalia laut.\n6. **Elang** - Burung pemangsa yang berburu hewan kecil seperti tikus dan burung lain.\n7.', '2025-08-20 03:18:28'),
(19, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'buatkan saya kode untuk menampilkan teks menggunakan html', '2025-08-20 03:18:44'),
(20, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Tentu! Berikut adalah contoh kode HTML sederhana untuk menampilkan teks di halaman web:\n\n```html\n<!DOCTYPE html>\n<html lang=\"id\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Contoh Teks HTML</title>\n</head>\n<body>\n    <h1>Selamat Datang!</h1>\n    <p>Ini adalah contoh teks yang ditampilkan menggunakan HTML.</p>\n    <p>Semoga kamu menemukan informasi yang kamu cari!</p>\n</body>\n</html>\n```\n\nPenjelasan dari kode di atas:\n- Elemen `<h1>` digunakan untuk judul utama.\n- Elemen `<p>` digunakan untuk paragraf teks.\n\nKamu dapat menyalin kode ini dan menempelkannya ke dalam file dengan ekstensi `.html`, lalu membuka file tersebut di browser untuk melihat hasilnya. Jika ada yang ingin kamu', '2025-08-20 03:18:48'),
(21, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'buatkan kode yang outputnya jadi visual studio code', '2025-08-20 03:19:13'),
(22, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Berikut adalah petunjuk untuk membuat file HTML yang dapat kamu buka di Visual Studio Code dan lihat outputnya di browser. Kode ini akan menampilkan teks di halaman web.\n\n1. **Buka Visual Studio Code**.\n2. **Buat file baru** dengan nama `index.html`.\n3. **Salin dan tempel kode berikut** ke dalam file `index.html`:\n\n```html\n<!DOCTYPE html>\n<html lang=\"id\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Contoh Teks HTML</title>\n</head>\n<body>\n    <h1>Selamat Datang di Visual Studio Code!</h1>\n    <p>Ini adalah contoh teks yang ditampilkan menggunakan HTML.</p>\n    <p>Semoga kamu menikmati belajar HTML!</p>\n</body>\n</html>\n```\n\n4. **Simpan file**', '2025-08-20 03:19:16'),
(23, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'magang adalah', '2025-08-20 03:20:16'),
(24, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Magang adalah program pelatihan praktis yang biasanya dilakukan oleh mahasiswa atau lulusan baru di suatu perusahaan atau organisasi. Tujuan dari magang adalah untuk memberikan pengalaman kerja yang nyata dan memperdalam pemahaman mengenai bidang studi yang dipelajari. \n\nSelama magang, peserta biasanya akan bekerja di bawah bimbingan seorang mentor atau supervisor dan terlibat dalam berbagai tugas yang relevan dengan pekerjaan. Magang dapat berlangsung dalam jangka waktu tertentu, mulai dari beberapa minggu hingga beberapa bulan.\n\nMagang juga dapat membantu peserta membangun jaringan profesional, meningkatkan keterampilan, dan memperkuat surat lamaran kerja mereka di masa depan. Beberapa perusahaan bahkan menawarkan kesempatan untuk direkrut setelah program magang selesai.', '2025-08-20 03:20:18'),
(25, '6i4vqsqeo2hiiusru0ngli7ng7', 'user', 'magang anak smmk', '2025-08-20 03:20:36'),
(26, '6i4vqsqeo2hiiusru0ngli7ng7', 'assistant', 'Magang bagi anak Sekolah Menengah Kejuruan (SMK) adalah program praktis yang dirancang untuk memberikan pengalaman kerja di bidang yang sesuai dengan jurusan yang mereka ambil. Tujuan dari magang ini adalah untuk membantu siswa mengaplikasikan pengetahuan dan keterampilan yang telah dipelajari di sekolah ke dalam situasi dunia nyata. Berikut adalah beberapa poin penting mengenai magang bagi siswa SMK:\n\n1. **Pengalaman Praktis**: Siswa dapat belajar langsung di lapangan, memperoleh keterampilan praktis, dan memahami bagaimana dunia kerja beroperasi.\n\n2. **Penerapan Teori**: Dengan menjalani magang, siswa dapat mengaitkan teori yang dipelajari di kelas dengan praktik di dunia nyata.\n\n3. **Pengembangan Keterampilan**: Magang membantu siswa mengembangkan soft skills seperti komunikasi, kerja sama, dan manajemen waktu, serta hard skills yang spesifik sesuai bidang mereka.\n\n4. **Membangun Jaringan**:', '2025-08-20 03:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `sender` enum('user','bot') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `session_id`, `sender`, `message`, `created_at`) VALUES
(1, 1, 'user', 'halo', '2025-08-20 03:29:20'),
(2, 1, 'bot', 'Halo! Ada yang bisa saya bantu?', '2025-08-20 03:29:21'),
(3, 1, 'user', 'namamu siapa', '2025-08-20 03:29:29'),
(4, 1, 'bot', 'Saya adalah asisten virtual. Anda bisa memanggil saya Asisten. Ada yang ingin Anda tanyakan?', '2025-08-20 03:29:30'),
(5, 1, 'user', 'sekarang namamu aldo', '2025-08-20 03:29:43'),
(6, 1, 'bot', 'Baik, Anda bisa memanggil saya Aldo! Ada yang ingin dibahas?', '2025-08-20 03:29:44'),
(7, 2, 'user', 'apakah kamu menjual cctv', '2025-08-20 03:41:11'),
(8, 2, 'bot', 'Maaf, saya tidak menjual produk apa pun, termasuk CCTV. Namun, saya bisa membantu memberikan informasi atau rekomendasi tentang CCTV jika Anda membutuhkannya.', '2025-08-20 03:41:12'),
(9, 2, 'user', 'apakah kamu terhubung ke database', '2025-08-20 03:41:26'),
(10, 2, 'bot', 'Tidak, saya tidak terhubung ke database atau internet. Saya hanya dapat memberikan informasi berdasarkan pengetahuan yang telah saya pelajari hingga Oktober 2023.', '2025-08-20 03:41:27'),
(11, 2, 'user', 'halo', '2025-08-20 03:45:04'),
(12, 2, 'bot', 'Halo! Ada yang bisa saya bantu hari ini?', '2025-08-20 03:45:05'),
(13, 2, 'user', 'cctv outdor', '2025-08-20 03:47:23'),
(14, 2, 'bot', 'CCTV outdoor adalah kamera pengawas yang dirancang untuk digunakan di luar ruangan. Fitur umumnya meliputi:\n\n1. **Tahan Cuaca**: Dirancang untuk tahan terhadap air dan debu.\n2. **Resolusi Tinggi**: Untuk mendapatkan gambar yang lebih jelas.\n3. **Fitur Malam**: Seperti night vision atau infrared untuk pengawasan saat gelap.\n4. **Pemosisian yang Fleksibel**: Beberapa dilengkapi dengan kemampuan putar (PTZ) atau sudut pandang lebar.\n\nApakah Anda ingin tahu lebih lanjut tentang merek atau model tertentu?', '2025-08-20 03:47:26'),
(15, 3, 'user', 'apakah di sini ada CCTV hikvision 2MP indoor', '2025-08-20 03:48:35'),
(16, 3, 'bot', 'Maaf, saya tidak memiliki informasi tentang ketersediaan produk di lokasi tertentu. Anda bisa memeriksa toko elektronik atau situs web e-commerce untuk menemukan CCTV Hikvision 2MP indoor.', '2025-08-20 03:48:36'),
(17, 3, 'user', 'kategori', '2025-08-20 03:48:53'),
(18, 3, 'bot', 'CCTV Hikvision 2MP indoor dapat masuk dalam kategori berikut:\n\n1. **Keamanan** - Sistem pengawasan untuk keamanan rumah atau bisnis.\n2. **Elektronik** - Perangkat elektronik dan teknologi.\n3. **Peralatan Keamanan** - Alat untuk pengawasan dan perlindungan.\n4. **Perangkat Kamera** - Kamera untuk pengambilan gambar dan video.\n\nJika ada kategori lain yang Anda maksud, silakan beri tahu!', '2025-08-20 03:48:55'),
(19, 4, 'user', 'kategori', '2025-08-20 03:49:38'),
(20, 4, 'bot', 'Kategori adalah pengelompokan atau klasifikasi yang digunakan untuk mengorganisir informasi, benda, atau konsep berdasarkan kesamaan atau ciri tertentu. Misalnya, kategori bisa berupa: makanan, hewan, buku, film, dan lain-lain. Apakah ada kategori tertentu yang ingin Anda bahas?', '2025-08-20 03:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` int(11) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`id`, `session_name`, `created_at`) VALUES
(1, 'Percakapan 20 Aug 2025 05:29', '2025-08-20 03:29:16'),
(2, 'Percakapan 20 Aug 2025 05:41', '2025-08-20 03:41:01'),
(3, 'Percakapan 20 Aug 2025 05:48', '2025-08-20 03:48:05'),
(4, 'Percakapan 20 Aug 2025 05:49', '2025-08-20 03:49:02');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `harga` decimal(12,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `kategori`, `harga`, `stok`) VALUES
(1, 'CCTV Hikvision 2MP Indoor', 'CCTV', 350000.00, 15),
(2, 'CCTV Dahua 2MP Outdoor', 'CCTV', 420000.00, 12),
(3, 'CCTV Hikvision 5MP Dome', 'CCTV', 550000.00, 10),
(4, 'CCTV Wireless IP Camera', 'CCTV', 600000.00, 8),
(5, 'CCTV EZVIZ Smart Camera', 'CCTV', 750000.00, 6),
(6, 'DVR Hikvision 4 Channel', 'DVR', 950000.00, 7),
(7, 'DVR Hikvision 8 Channel', 'DVR', 1350000.00, 5),
(8, 'DVR Dahua 16 Channel', 'DVR', 2500000.00, 3),
(9, 'NVR Hikvision 8 Channel', 'NVR', 2100000.00, 4),
(10, 'NVR Dahua 16 Channel', 'NVR', 3100000.00, 2),
(11, 'Komputer Rakitan i3 8GB RAM', 'Komputer', 4500000.00, 6),
(12, 'Komputer Rakitan i5 16GB RAM', 'Komputer', 6500000.00, 5),
(13, 'Komputer Rakitan i7 16GB RAM', 'Komputer', 8500000.00, 3),
(14, 'Laptop Lenovo ThinkPad i5', 'Komputer', 7200000.00, 4),
(15, 'Laptop Asus Vivobook i3', 'Komputer', 5800000.00, 6),
(16, 'Harddisk CCTV 1TB Surveillance', 'Aksesoris', 750000.00, 10),
(17, 'Harddisk CCTV 2TB Surveillance', 'Aksesoris', 1250000.00, 7),
(18, 'Kabel Coaxial RG6 + Power 100m', 'Aksesoris', 450000.00, 15),
(19, 'Adaptor CCTV 12V 2A', 'Aksesoris', 55000.00, 30),
(20, 'BNC Connector CCTV (Pack isi 10)', 'Aksesoris', 35000.00, 40),
(21, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(22, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(23, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(24, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(25, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(26, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(27, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(28, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(29, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(30, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(31, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(32, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(33, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(34, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(35, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(36, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(37, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(38, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(39, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(40, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(41, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(42, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(43, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(44, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(45, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(46, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(47, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(48, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(49, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(50, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(51, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(52, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(53, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(54, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(55, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(56, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(57, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(58, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(59, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(60, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(61, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(62, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(63, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(64, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(65, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(66, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(67, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(68, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(69, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(70, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(71, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(72, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(73, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(74, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(75, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(76, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(77, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(78, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(79, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(80, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(81, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(82, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(83, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(84, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(85, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(86, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(87, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(88, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(89, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(90, 'Workstation Xeon', 'Komputer', 25000000.00, 2),
(91, 'CCTV Wireless 1080P', 'CCTV', 850000.00, 15),
(92, 'CCTV Outdoor 2MP', 'CCTV', 1200000.00, 8),
(93, 'CCTV PTZ 4MP', 'CCTV', 2500000.00, 5),
(94, 'CCTV Dome 5MP', 'CCTV', 1800000.00, 12),
(95, 'CCTV Bullet 8MP', 'CCTV', 3200000.00, 3),
(96, 'Laptop Gaming Core i7', 'Komputer', 15000000.00, 7),
(97, 'PC Desktop Core i5', 'Komputer', 8000000.00, 10),
(98, 'All-in-One PC 24 inch', 'Komputer', 12000000.00, 4),
(99, 'Mini PC Intel NUC', 'Komputer', 5000000.00, 6),
(100, 'Workstation Xeon', 'Komputer', 25000000.00, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
