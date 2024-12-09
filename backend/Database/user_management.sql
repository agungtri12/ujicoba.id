-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Des 2024 pada 15.45
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
-- Database: `user_management`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `article_name` varchar(255) NOT NULL,
  `synopsis` text NOT NULL,
  `full_content` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `articles`
--

INSERT INTO `articles` (`id`, `article_name`, `synopsis`, `full_content`, `image`, `created_at`) VALUES
(1, 'Mari Kelola Sampah Dengan Bijak Mulai Hari Ini', 'Permasalahan sampah tidak hanya terjadi di kota-kota besar, tetapi juga di pedesaan dan perkampungan, sebagian besar disebabkan oleh kurangnya kesadaran masyarakat dalam mengelola sampah dengan baik. Sampah yang tidak dikelola dengan benar dapat menimbulkan dampak buruk seperti banjir, penyakit, dan lingkungan yang tidak sehat. Namun, beberapa daerah di Indonesia telah berhasil mengelola sampah dengan baik. Salah satu langkah penting dalam pengelolaan sampah adalah pemahaman mengenai jenis sampah, yaitu organik dan anorganik. Tips bijak dalam mengelola sampah antara lain adalah memisahkan tempat sampah sesuai jenis, menggunakan bahan pengganti plastik, mendaur ulang sampah anorganik, serta mengelola sampah organik menjadi pupuk kompos. Selain itu, penting untuk secara rutin membuang sampah ke TPS, mengelola sampah berbahaya seperti elektronik, dan memberi penghargaan untuk diri sendiri jika pengelolaan sampah di rumah berjalan dengan baik. Dengan konsistensi, masalah sampah bisa diatasi secara efektif.', 'Permasalahan sampah menjadi topik yang terus dibicarakan, baik di kota-kota besar maupun di daerah pedesaan dan perkampungan. Sampah yang menumpuk, tidak dikelola dengan baik, sering kali menyebabkan berbagai masalah lingkungan. Hal ini disebabkan oleh kurangnya kesadaran masyarakat tentang bagaimana cara mengelola sampah secara efektif dan bijak.\r\n\r\nDi beberapa daerah di Indonesia, sudah mulai terlihat langkah-langkah untuk mengelola sampah dengan lebih teratur dan baik, yang tidak hanya berdampak positif bagi lingkungan tetapi juga meningkatkan kesadaran masyarakat tentang pentingnya pengelolaan sampah. Namun, masalah sampah tetap menjadi tantangan besar jika tidak dilakukan dengan cara yang benar.\r\n\r\nDampak Buruk Sampah yang Tidak Dikelola dengan Baik\r\nJika sampah tidak dikelola dengan benar, dapat menimbulkan berbagai dampak negatif, antara lain:\r\n\r\nBanjir - Sampah yang menumpuk di saluran air dapat menyumbat aliran air, yang dapat menyebabkan banjir saat musim hujan.\r\nPenyakit - Sampah yang terurai dengan buruk dapat menjadi sarang bagi berbagai jenis penyakit, seperti demam berdarah, malaria, dan penyakit lainnya yang ditularkan oleh vektor.\r\nLingkungan yang Tidak Sehat - Sampah yang menumpuk di lingkungan menyebabkan udara, tanah, dan air tercemar, serta mengurangi kualitas hidup masyarakat sekitar.\r\nUntuk itu, sangat penting bagi masyarakat untuk memiliki kesadaran yang tinggi tentang pengelolaan sampah. Agar masalah sampah ini tidak terus berkembang, kita perlu memulai langkah-langkah untuk mengelola sampah dengan bijak, dimulai dari mengenal jenis-jenis sampah.\r\n\r\nJenis-Jenis Sampah\r\nSampah secara umum dibagi menjadi dua jenis, yaitu:\r\n\r\nSampah Organik - Sampah yang berasal dari bahan alami, seperti sisa makanan, daun, dan sampah biologis lainnya. Sampah jenis ini bisa diolah menjadi pupuk kompos yang berguna untuk pertanian.\r\n\r\nSampah Anorganik - Sampah yang berasal dari bahan yang tidak mudah terurai secara alami, seperti plastik, logam, kaca, dan kertas. Sampah anorganik ini lebih sulit untuk didegradasi, sehingga perlu penanganan khusus seperti daur ulang.\r\n\r\nDengan mengenal kedua jenis sampah ini, masyarakat dapat lebih mudah untuk memilah dan mengelola sampah secara lebih efisien.\r\n\r\nTips Mengelola Sampah Secara Bijak\r\nBerikut ini adalah beberapa tips untuk mengelola sampah dengan bijak, seperti yang dikutip dari kejarmimpi.id:\r\n\r\nMembuat Tempat Sampah Sesuai Jenisnya\r\nBuatlah tempat sampah yang terpisah antara sampah organik dan anorganik. Hal ini akan mempermudah dalam proses pengelolaan dan mendaur ulang sampah.\r\n\r\nMengganti Alas Plastik Sampah dengan Koran atau Kardus\r\nGunakan bahan-bahan yang lebih ramah lingkungan seperti koran atau kardus untuk alas tempat sampah. Ini dapat mengurangi konsumsi sampah plastik yang sulit terurai.\r\n\r\nManfaatkan Sampah Organik Menjadi Pupuk Kompos\r\nSampah organik dapat dijadikan pupuk kompos yang berguna untuk tanaman. Dengan begitu, sampah organik dapat dimanfaatkan kembali dan tidak terbuang sia-sia.\r\n\r\nManfaatkan Sampah Anorganik yang Masih Layak Daur Ulang\r\nSampah anorganik seperti botol kaca, kaleng, atau plastik bisa didaur ulang menjadi barang yang berguna. Pastikan untuk memilah sampah dengan benar agar proses daur ulang lebih efektif.\r\n\r\nMembuang Sampah ke TPS atau TPA Secara Rutin\r\nJangan biarkan sampah menumpuk terlalu lama di rumah. Buang sampah ke Tempat Pembuangan Sampah (TPS) atau Tempat Pembuangan Akhir (TPA) setiap seminggu minimal dua kali.\r\n\r\nMengelola Sampah Berbahaya\r\nJangan lupa untuk mengelola sampah yang lebih berbahaya, seperti sampah elektronik, baterai, dan bahan kimia. Jangan sembarangan membuangnya karena dapat mencemari lingkungan.\r\n\r\nMemberikan Rewards untuk Diri Sendiri\r\nBerikan penghargaan kepada diri sendiri jika berhasil mengelola sampah dengan baik setiap minggunya. Ini dapat menjadi motivasi agar kita lebih konsisten dalam mengelola sampah.\r\n\r\nBijak-Bijak Menjadi Seorang Konsumen\r\nSebagai konsumen, kita juga harus bijak dalam memilih produk. Pilihlah produk dengan kemasan yang ramah lingkungan atau yang dapat didaur ulang untuk mengurangi jumlah sampah yang dihasilkan.\r\n\r\nMengelola Sampah: Bukan Hal yang Sulit\r\nMengelola sampah memang bukan hal yang sulit untuk dilakukan, tetapi dibutuhkan konsistensi dan kesadaran tinggi dari masyarakat untuk menjadikannya sebagai kebiasaan sehari-hari. Dengan langkah-langkah sederhana seperti yang dijelaskan di atas, masalah sampah dapat teratasi dan lingkungan kita akan menjadi lebih sehat.\r\n\r\nNamun, jika pengelolaan sampah tidak dilakukan secara konsisten, maka masalah sampah akan kembali muncul dan semakin memburuk. Oleh karena itu, mari kita bersama-sama menjaga kebersihan dan kelestarian lingkungan dengan mengelola sampah secara bijak dan bertanggung jawab.\r\n\r\nDengan konsistensi dan komitmen dari setiap individu, kita bisa menciptakan lingkungan yang lebih bersih, sehat, dan nyaman untuk hidup. (FT)', 'uploads/1732579498.jpeg', '2024-11-26 00:04:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `points`, `created_at`) VALUES
(1, 'Sedotan Kertas', 'ngin menikmati minuman dengan estetik sembari menjaga lingkungan? Gunakan saja sedotan kertas steril dari ETR. Sedotan kertas ini mampu menghadirkan visual menarik saat dipadukan dengan minuman. Material kertasnya pun mudah terurai sehingga lebih ramah lingkungan serta aman digunakan oleh anak-anak. Diameter dan panjangnya pun sangat beragam yang dapat dipilih sesuai kebutuhan.', 'uploads/sedotan.jpg', 150, '2024-12-02 01:44:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_number`, `password`, `role`, `profile_image`) VALUES
(35, 'Admin', 'admin@zerotrash.sim', '08213121311231', '$2y$10$Sn0B1Z1ZwrsiL3PbdsNjPOR8amMuPYbdoRPxS8oQi1tPLqhN04yYi', 'admin', NULL),
(37, 'Nigel', 'nigel@example.com', '0821212121', '$2y$10$1m.UrN8sG1HmOjgX.P.cJuEhB1Zz8md5wGAYasOj8Ky7fu1CqK3ZO', 'user', 'uploads/bruno.png'),
(38, 'user', 'ujicoba@example.com', '0821213121', '$2y$10$888kxFNpklaPELeYvNvluOxZKPmgi3hTl6YM43Zs0SZjpMoZ5Ybm2', 'user', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_points`
--

CREATE TABLE `user_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `weight` float NOT NULL,
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_points`
--

INSERT INTO `user_points` (`id`, `user_id`, `points`, `description`, `weight`, `date_added`) VALUES
(9, 37, 30, 'Botol Plastik', 0.2383, '2024-11-26'),
(10, 37, 69, 'Botol Plastik', 0.54809, '2024-11-28'),
(11, 37, 40, 'Ember', 8, '2024-11-28'),
(12, 37, 5, 'Kardus', 0.5, '2024-11-28'),
(13, 37, 8, 'Kertas', 0.4, '2024-11-28'),
(14, 37, -150, '', 0, '0000-00-00'),
(15, 38, 15, 'Botol Plastik', 0.11915, '2024-12-02'),
(16, 38, 35, 'Kardus', 3.5, '2024-12-02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `user_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
