-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql110.infinityfree.com
-- Generation Time: Apr 02, 2026 at 04:26 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41276323_if0_41276323_stonehaven`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `shelf` varchar(50) DEFAULT 'Section A',
  `isbn` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'available',
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `book_title`, `author`, `shelf`, `isbn`, `category`, `quantity`, `created_at`, `status`, `file_path`) VALUES
(35, 'Artificial Inteligence', 'Amazon', 'Section B-1', NULL, 'tech', 1, '2026-03-12 20:51:01', 'borrowed', '1773348661_Artificial intelligence.pdf'),
(37, 'computer systems', 'Ryan', 'section c-2', NULL, 'Tech', 1, '2026-03-13 07:21:32', 'available', '1773386492_Computer Systems.pdf'),
(38, 'computer maintenance and repair', 'yuves', 'section B-7', NULL, 'Tech', 1, '2026-03-20 16:55:23', 'available', '1774025723_Computer Maintenance and Repair.pdf'),
(39, 'LAB PRACTICE', 'Laban', 'section C-5', NULL, 'Networking', 1, '2026-03-20 16:56:50', 'available', '1774025810_z. LAB PRACTICE - SET (Social Engineer Toolkit) v1.pdf'),
(40, 'operating system ', 'Tony', 'section F-10', NULL, 'Tech', 1, '2026-03-20 17:07:44', 'available', '1774026464_OPERATING-SYSTEMS.pdf'),
(42, 'faste design', 'Bridget', 'section f-9', NULL, 'design', 1, '2026-03-24 06:48:40', 'available', '1774334920_Faste design.pdf'),
(43, 'design art', 'Ian', 'section H-10', NULL, 'design', 1, '2026-03-24 06:49:32', 'available', '1774334972_design.pdf'),
(44, 'artificial intelligence and medical education', 'Scotzz', 'section B-8', NULL, 'science', 1, '2026-03-24 06:56:50', 'available', '1774335410_Artificial intelligence and medical education.pdf'),
(47, 'neurosegon book', 'Isaac', 'Section r-6', NULL, 'Science', 1, '2026-04-02 10:46:24', 'available', '1775126784_neurosergoun book.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) DEFAULT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `status` enum('borrowed','returned') DEFAULT 'borrowed',
  `fine_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`id`, `user_id`, `book_id`, `book_title`, `borrow_date`, `due_date`, `return_date`, `status`, `fine_amount`) VALUES
(39, 27, 35, 'Artificial Inteligence', '2026-03-13 07:00:00', '2026-03-27', '2026-03-17 07:03:24', 'returned', '0.00'),
(40, 32, 35, 'Artificial Inteligence', '2026-03-24 05:51:52', '2026-04-07', NULL, 'borrowed', '0.00'),
(41, 31, 42, 'faste design', '2026-04-01 08:56:21', '2026-04-15', '2026-04-01 08:56:46', 'returned', '0.00'),
(42, 24, 37, 'computer systems', '2026-04-02 06:16:57', '2026-04-16', '2026-04-02 06:17:05', 'returned', '0.00'),
(43, 21, 37, 'computer systems', '2026-04-02 07:00:00', '2026-04-16', '2026-04-02 08:29:07', 'returned', '0.00'),
(44, 31, 43, 'design art', '2026-04-02 08:28:55', '2026-04-16', '2026-04-02 08:29:01', 'returned', '0.00'),
(45, 21, 37, 'computer systems', '2026-04-02 07:00:00', '2026-04-16', '2026-04-02 10:55:00', 'returned', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(50) NOT NULL,
  `librarian_id` int(11) NOT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`id`, `user_id`, `librarian_id`, `action_type`, `details`, `log_time`) VALUES
(27, 0, 7, 'BOOK_ADDED', 'New digital book added: computer systems', '2026-03-12 07:08:24'),
(28, 0, 7, 'BOOK_ADDED', 'New digital book added: computer maintenance and repair', '2026-03-12 07:11:46'),
(33, 0, 7, 'BOOK_ADDED', 'New digital book added: computer system', '2026-03-12 15:51:14'),
(34, 7, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-03-12 18:52:13'),
(35, 0, 25, 'BOOK_ADDED', 'Added: neurosergoun  book', '2026-03-12 20:33:36'),
(36, 0, 25, 'BOOK_ADDED', 'Added: neurosergoun  book', '2026-03-12 20:44:54'),
(37, 0, 25, 'BOOK_ADDED', 'Added: Artificial Inteligence', '2026-03-12 20:51:01'),
(38, 7, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-03-12 21:11:13'),
(39, 25, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-03-12 21:11:54'),
(40, 0, 25, 'BOOK_ADDED', 'Added: INFORMATION AND COMMUNICATIONTECHNOLOGY', '2026-03-13 06:31:14'),
(41, 0, 27, 'BOOK_ADDED', 'Added: computer systems', '2026-03-13 07:21:32'),
(42, 25, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-03-17 07:39:14'),
(43, 0, 25, 'BOOK_ADDED', 'Added: computer maintenance and repair', '2026-03-20 16:55:23'),
(44, 0, 25, 'BOOK_ADDED', 'Added: LAB PRACTICE', '2026-03-20 16:56:50'),
(45, 0, 25, 'BOOK_ADDED', 'Added: operating system ', '2026-03-20 17:07:44'),
(46, 0, 25, 'BOOK_ADDED', 'Added: information communication technology', '2026-03-20 17:11:05'),
(47, 25, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-03-24 05:52:09'),
(48, 0, 25, 'BOOK_ADDED', 'Added: faste design', '2026-03-24 06:48:40'),
(49, 0, 25, 'BOOK_ADDED', 'Added: design art', '2026-03-24 06:49:32'),
(50, 0, 25, 'BOOK_ADDED', 'Added: artificial intelligence and medical education', '2026-03-24 06:56:50'),
(51, 0, 34, 'BOOK_ADDED', 'Added: report and analysis', '2026-03-26 15:42:10'),
(52, 7, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-04-01 08:55:59'),
(53, 25, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-04-02 06:15:34'),
(54, 0, 25, 'BOOK_ADDED', 'Added: neurosergon', '2026-04-02 08:28:16'),
(55, 25, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-04-02 08:34:22'),
(56, 0, 7, 'BOOK_ADDED', 'Added: neurosegon book', '2026-04-02 10:46:24'),
(57, 7, 0, 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.', '2026-04-02 10:53:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','librarian') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `password`, `role`, `created_at`, `reset_token`, `token_expires`) VALUES
(7, 'rose linda', 'rozi', 'roselinda@gmail.com', '$2y$10$jqzH5l43qOGXP3kl3ty/aOEwaTd9vdlzQzQOzKU9Jv2QzaK74Pls2', 'librarian', '2026-02-21 11:38:54', NULL, NULL),
(21, 'isaac maina', 'maish', 'isaacnewtonemaina@gmail.com', '$2y$10$TGyjcEbrde9.4Bkf23NfKOq5j4LxH5vZla8j5zmWp2MaKkFDln6km', 'student', '2026-03-11 09:22:54', '2b645d5072ea8b077f29eb9f1ac9a634ad921289fc9123cbc4e4c32773e9ac04', '2026-03-12 17:29:00'),
(22, 'Newton Maina', 'new', 'mainanewton611@gmail.com', '$2y$10$80icUmH6yfHM8pSg5R1Ai.pTE4vZTTr.n4hsnjA7718rgMd32yCWa', '', '2026-03-11 09:43:37', NULL, NULL),
(23, 'kelvin muinde', 'kevo', 'kelvinmuinde90@gmail.com', '$2y$10$RrNuuHq29xzBTfH7snJMNemQ5VYJGxPAxdRRzBhcSQgsY2a4IJRYG', 'student', '2026-03-11 11:16:05', NULL, NULL),
(24, 'ian kipkoech', 'ian', 'ianyoko399@gmail.com', '$2y$10$W8V2KQz91HyyWVP6lg7nWe/AT/Q0cgMs8brBrMKupHOlkChr57xUa', 'student', '2026-03-12 11:47:43', '1b90199b63726a675ca5f0e95d01a4b314dc61245f3e90006d09a465765b6380', '2026-03-12 09:33:25'),
(25, 'Jeremy Ochieng', 'jere', 'jeremyochieng464@gmail.com', '$2y$10$l2WvzTXqN027cXcKdml/uu9KFL5yTYUrnmSZH1rycRwDqzy.zP0JW', 'librarian', '2026-03-12 18:56:00', NULL, NULL),
(27, 'Rose Gacheri', 'rose', 'rosegacheri@gmail.com', '$2y$10$MVwB3KCWKuowRuArjWejseO5/ubNDQuKP/Sb7K8MQrKrYgtdPFVwC', 'librarian', '2026-03-13 07:19:55', NULL, NULL),
(28, 'kelvin kiprop', 'kelvin', 'kelvinkiprop@gmail.com', '$2y$10$OKK8S9ZCVBAo/q7Vn0Nx9.wkePWtP3mv5YUVDM/k5oLq9jWH91TMa', 'student', '2026-03-20 15:09:10', NULL, NULL),
(29, 'mercy masika', 'mercy', 'mercymasika@gmail.com', '$2y$10$djWDT0m5S3of71pRPuenqO.Z2xLKEZohNQe7KPwYeNXY.J3l8hSwO', 'librarian', '2026-03-20 15:10:28', NULL, NULL),
(30, 'stephen kaimenyi', 'steve', 'stephenkaimenyi@gmail.com', '$2y$10$cV.Ikmo4KkkgSzKZutsNUeBUwaXvhXY8xVaQJ7sx1Vh1Aj99xkpxG', 'librarian', '2026-03-20 15:12:05', NULL, NULL),
(31, 'Gladys Kaimenyi', 'gladys', 'gladyskaimenyi@gmail.com', '$2y$10$Ht6BIPhViTwF87XNpnTK/.VhqjzqK/rGAKV7SZ2bCUaTP9ujTTjh.', 'student', '2026-03-20 15:13:34', NULL, NULL),
(32, 'micheal joram  maina', 'joram', 'michealmaina@gmail.com', '$2y$10$UcYk9EceMe0LgXyCw816n.bX7F1aBX328cT4s/jzHMm1isUG5MYai', 'student', '2026-03-20 15:15:29', NULL, NULL),
(33, 'Mr.Samwel Kinuthia', 'ss', 'samkih1@yahoo.com', '$2y$10$3chuOvTZlY9NaBVqg.nRx.NnCIqEQ2M4NfKnSmBxxu2DEuXZdqu9S', 'librarian', '2026-03-24 07:01:40', NULL, NULL),
(34, 'tracey karei', 'tracey', 'traceykarei254@gmail.com', '$2y$10$X9GhwJlOPwhcIDVWylIAK.kSRA2pvTMtTNPT3BLt7zy331oniShSm', 'librarian', '2026-03-26 15:34:40', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
