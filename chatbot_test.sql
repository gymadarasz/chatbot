-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 09, 2020 at 06:28 PM
-- Server version: 8.0.22-0ubuntu0.20.04.2
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatbot_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `name`, `deleted`) VALUES
(1, 'Test Chat 1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `chat_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversation`
--

INSERT INTO `conversation` (`id`, `user_id`, `chat_id`) VALUES
(1, 440, 1),
(2, 440, 1),
(3, 440, 1),
(4, 440, 1),
(5, 440, 1),
(6, 440, 1),
(7, 440, 1),
(8, 440, 1),
(9, 440, 1),
(10, 440, 1),
(11, 440, 1),
(12, 440, 1),
(13, 0, 1),
(14, 441, 1),
(15, 441, 1),
(16, 441, 1),
(17, 441, 1),
(18, 441, 1),
(19, 441, 1),
(20, 441, 1),
(21, 441, 1),
(22, 441, 1),
(23, 441, 1),
(24, 441, 1),
(25, 441, 1),
(26, 441, 1),
(27, 441, 1),
(28, 441, 1),
(29, 441, 1),
(30, 441, 1),
(31, 441, 1),
(32, 441, 1),
(33, 441, 1),
(34, 441, 1),
(35, 441, 1),
(36, 441, 1),
(37, 441, 1),
(38, 441, 1),
(39, 441, 1),
(40, 441, 1),
(41, 441, 1),
(42, 441, 1),
(43, 441, 1),
(44, 441, 1),
(45, 441, 1),
(46, 441, 1),
(47, 441, 1),
(48, 441, 1),
(49, 441, 1),
(50, 441, 1),
(51, 441, 1),
(52, 441, 1),
(53, 441, 1),
(54, 441, 1),
(55, 441, 1),
(56, 441, 1),
(57, 441, 1),
(58, 441, 1),
(59, 441, 1),
(60, 447, 1),
(61, 447, 1),
(62, 447, 1),
(63, 447, 1),
(64, 447, 1),
(65, 447, 1),
(66, 447, 1),
(67, 447, 1),
(68, 447, 1),
(69, 447, 1),
(70, 447, 1),
(71, 447, 1),
(72, 447, 1),
(73, 447, 1),
(74, 447, 1),
(75, 447, 1),
(76, 447, 1),
(77, 447, 1),
(78, 447, 1),
(79, 447, 1),
(80, 447, 1),
(81, 447, 1),
(82, 448, 1),
(83, 448, 1),
(84, 448, 1),
(85, 448, 1),
(86, 448, 1),
(87, 448, 1),
(88, 448, 1),
(89, 448, 1),
(90, 448, 1),
(91, 448, 1),
(92, 448, 1),
(93, 448, 1),
(94, 448, 1),
(95, 448, 1),
(96, 448, 1),
(97, 448, 1);

-- --------------------------------------------------------

--
-- Table structure for table `conversation_message`
--

CREATE TABLE `conversation_message` (
  `id` int NOT NULL,
  `conversation_id` int NOT NULL,
  `message_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conversation_message`
--

INSERT INTO `conversation_message` (`id`, `conversation_id`, `message_id`, `created_at`) VALUES
(1, 13, 1, '2020-11-08 20:39:18'),
(2, 14, 1, '2020-11-08 20:46:43'),
(3, 15, 1, '2020-11-08 20:46:59'),
(4, 16, 1, '2020-11-08 20:53:41'),
(5, 17, 1, '2020-11-08 21:21:32'),
(6, 18, 1, '2020-11-08 21:35:31'),
(7, 19, 1, '2020-11-08 21:35:46'),
(8, 20, 1, '2020-11-08 21:36:25'),
(9, 21, 1, '2020-11-08 21:36:50'),
(10, 22, 1, '2020-11-08 21:36:53'),
(11, 23, 1, '2020-11-08 21:37:00'),
(12, 24, 1, '2020-11-08 21:37:13'),
(13, 25, 1, '2020-11-08 21:44:48'),
(14, 26, 1, '2020-11-08 21:45:28'),
(15, 27, 1, '2020-11-08 21:46:32'),
(16, 28, 1, '2020-11-08 21:46:44'),
(17, 29, 1, '2020-11-08 21:47:41'),
(18, 30, 1, '2020-11-08 21:47:48'),
(19, 31, 1, '2020-11-08 21:47:52'),
(20, 32, 1, '2020-11-08 21:49:45'),
(21, 33, 1, '2020-11-08 21:53:24'),
(22, 34, 1, '2020-11-08 22:08:03'),
(23, 35, 1, '2020-11-08 22:09:15'),
(24, 36, 1, '2020-11-08 22:13:55'),
(25, 37, 1, '2020-11-08 22:14:33'),
(26, 38, 1, '2020-11-08 22:17:15'),
(27, 39, 1, '2020-11-08 22:18:24'),
(28, 40, 1, '2020-11-08 22:18:59'),
(29, 41, 1, '2020-11-08 22:19:31'),
(30, 42, 1, '2020-11-08 22:19:46'),
(31, 43, 1, '2020-11-08 22:23:19'),
(32, 44, 1, '2020-11-08 22:36:20'),
(33, 45, 1, '2020-11-08 22:37:14'),
(34, 46, 1, '2020-11-08 22:38:24'),
(35, 47, 1, '2020-11-08 22:42:47'),
(36, 48, 1, '2020-11-08 22:43:40'),
(37, 49, 1, '2020-11-08 22:46:41'),
(38, 50, 1, '2020-11-08 22:50:24'),
(39, 51, 1, '2020-11-08 22:51:58'),
(40, 52, 1, '2020-11-08 22:53:37'),
(41, 53, 1, '2020-11-08 22:54:37'),
(42, 54, 1, '2020-11-08 22:55:31'),
(43, 55, 1, '2020-11-08 22:56:57'),
(44, 56, 1, '2020-11-08 22:57:41'),
(45, 57, 1, '2020-11-08 22:58:33'),
(46, 57, 2, '2020-11-08 22:58:38'),
(47, 58, 1, '2020-11-08 22:59:13'),
(48, 58, 3, '2020-11-08 22:59:21'),
(49, 59, 1, '2020-11-08 23:12:55'),
(50, 59, 3, '2020-11-08 23:13:11'),
(51, 60, 1, '2020-11-09 00:06:21'),
(52, 60, 2, '2020-11-09 00:06:24'),
(53, 61, 1, '2020-11-09 00:21:05'),
(54, 61, 3, '2020-11-09 00:21:18'),
(55, 62, 1, '2020-11-09 00:24:18'),
(56, 62, 2, '2020-11-09 00:24:25'),
(57, 63, 1, '2020-11-09 00:26:49'),
(58, 63, 2, '2020-11-09 00:26:51'),
(59, 64, 1, '2020-11-09 00:27:51'),
(60, 64, 2, '2020-11-09 00:27:54'),
(61, 65, 1, '2020-11-09 00:28:17'),
(62, 65, 2, '2020-11-09 00:28:19'),
(63, 66, 1, '2020-11-09 00:30:51'),
(64, 66, 2, '2020-11-09 00:30:54'),
(65, 67, 1, '2020-11-09 00:31:10'),
(66, 67, 2, '2020-11-09 00:31:14'),
(67, 67, 4, '2020-11-09 00:31:15'),
(68, 68, 1, '2020-11-09 00:31:26'),
(69, 68, 3, '2020-11-09 00:31:30'),
(70, 68, 5, '2020-11-09 00:31:30'),
(71, 69, 1, '2020-11-09 00:35:08'),
(72, 69, 2, '2020-11-09 00:35:10'),
(73, 69, 4, '2020-11-09 00:35:10'),
(74, 70, 1, '2020-11-09 00:35:26'),
(75, 70, 2, '2020-11-09 00:35:28'),
(76, 70, 4, '2020-11-09 00:35:28'),
(77, 71, 1, '2020-11-09 00:41:28'),
(78, 71, 3, '2020-11-09 00:42:06'),
(79, 71, 5, '2020-11-09 00:42:06'),
(80, 72, 1, '2020-11-09 00:42:42'),
(81, 73, 1, '2020-11-09 00:43:03'),
(82, 73, 2, '2020-11-09 00:43:06'),
(83, 73, 4, '2020-11-09 00:43:06'),
(84, 73, 2, '2020-11-09 00:43:17'),
(85, 73, 4, '2020-11-09 00:43:17'),
(86, 73, 2, '2020-11-09 00:43:41'),
(87, 73, 4, '2020-11-09 00:43:41'),
(88, 74, 1, '2020-11-09 00:43:55'),
(89, 74, 2, '2020-11-09 00:44:00'),
(90, 74, 4, '2020-11-09 00:44:01'),
(91, 74, 3, '2020-11-09 00:44:12'),
(92, 74, 5, '2020-11-09 00:44:12'),
(93, 74, 2, '2020-11-09 00:45:29'),
(94, 74, 4, '2020-11-09 00:45:29'),
(95, 75, 1, '2020-11-09 00:45:46'),
(96, 75, 2, '2020-11-09 00:45:50'),
(97, 75, 4, '2020-11-09 00:45:50'),
(98, 76, 1, '2020-11-09 00:45:57'),
(99, 76, 3, '2020-11-09 00:46:01'),
(100, 76, 5, '2020-11-09 00:46:02'),
(101, 77, 1, '2020-11-09 00:48:39'),
(102, 77, 2, '2020-11-09 00:48:44'),
(103, 77, 4, '2020-11-09 00:48:44'),
(104, 78, 1, '2020-11-09 00:49:00'),
(105, 78, 3, '2020-11-09 00:49:05'),
(106, 78, 5, '2020-11-09 00:49:05'),
(107, 79, 1, '2020-11-09 00:49:24'),
(108, 79, 2, '2020-11-09 00:49:29'),
(109, 79, 4, '2020-11-09 00:49:29'),
(110, 80, 1, '2020-11-09 00:49:41'),
(111, 80, 3, '2020-11-09 00:49:48'),
(112, 80, 5, '2020-11-09 00:49:48'),
(113, 81, 1, '2020-11-09 00:56:48'),
(114, 81, 2, '2020-11-09 00:57:32'),
(115, 81, 4, '2020-11-09 00:57:32'),
(116, 82, 1, '2020-11-09 16:56:43'),
(117, 82, 3, '2020-11-09 16:56:49'),
(118, 82, 5, '2020-11-09 16:56:50'),
(119, 83, 1, '2020-11-09 17:00:40'),
(120, 84, 1, '2020-11-09 17:01:15'),
(121, 85, 1, '2020-11-09 17:01:36'),
(122, 86, 1, '2020-11-09 17:02:52'),
(123, 87, 1, '2020-11-09 17:02:57'),
(124, 87, 2, '2020-11-09 17:03:04'),
(125, 87, 4, '2020-11-09 17:03:05'),
(126, 88, 1, '2020-11-09 17:05:19'),
(127, 89, 1, '2020-11-09 17:05:41'),
(128, 90, 1, '2020-11-09 17:05:57'),
(129, 91, 1, '2020-11-09 17:17:43'),
(130, 92, 1, '2020-11-09 17:21:41'),
(131, 93, 1, '2020-11-09 17:22:16'),
(132, 94, 1, '2020-11-09 17:24:35'),
(133, 95, 1, '2020-11-09 17:25:13'),
(134, 95, 3, '2020-11-09 17:25:17'),
(135, 95, 5, '2020-11-09 17:25:17'),
(136, 96, 1, '2020-11-09 17:25:45'),
(137, 96, 3, '2020-11-09 17:25:49'),
(138, 96, 5, '2020-11-09 17:25:50'),
(139, 97, 1, '2020-11-09 17:26:25');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `chat_id` int NOT NULL,
  `talks` enum('chatbot','human') NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `chat_id`, `talks`, `content`, `deleted`) VALUES
(1, 1, 'chatbot', 'Hi, how are you? Do you want to know a secret?', 0),
(2, 1, 'human', 'Yes', 0),
(3, 1, 'human', 'No, thanks', 0),
(4, 1, 'chatbot', 'Oke, here is my secret is \"apple pie\", Bye', 0),
(5, 1, 'chatbot', 'Oke, bye', 0);

-- --------------------------------------------------------

--
-- Table structure for table `message_to_message`
--

CREATE TABLE `message_to_message` (
  `id` int NOT NULL,
  `request_message_id` int NOT NULL,
  `response_message_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message_to_message`
--

INSERT INTO `message_to_message` (`id`, `request_message_id`, `response_message_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 2, 4),
(4, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `token`, `active`) VALUES
(448, 'tester@example.com', '$2y$12$dWElxXZ5/CCaTFfNjR.zBu.sMwv8qg8OdOJ//AdvyWLmdCpKJ.bl2', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `conversation_message`
--
ALTER TABLE `conversation_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `message_to_message`
--
ALTER TABLE `message_to_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_message_id` (`response_message_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `password` (`password`),
  ADD KEY `token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `conversation_message`
--
ALTER TABLE `conversation_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `message_to_message`
--
ALTER TABLE `message_to_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
