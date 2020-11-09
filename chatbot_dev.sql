-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 09, 2020 at 07:15 PM
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
-- Database: `chatbot_dev`
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
(1, 'Test Chat 1 dev', 0),
(2, 'Test Escape Game', 0);

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
(97, 448, 1),
(98, 448, 1),
(99, 448, 2),
(100, 448, 2);

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
(139, 97, 1, '2020-11-09 17:26:25'),
(140, 98, 1, '2020-11-09 17:32:42'),
(141, 99, 6, '2020-11-09 18:09:42'),
(142, 99, 8, '2020-11-09 18:09:50'),
(143, 99, 11, '2020-11-09 18:09:52'),
(144, 99, 14, '2020-11-09 18:10:01'),
(145, 99, 16, '2020-11-09 18:10:01'),
(146, 99, 18, '2020-11-09 18:10:11'),
(147, 99, 20, '2020-11-09 18:10:11'),
(148, 99, 29, '2020-11-09 18:10:20'),
(149, 99, 20, '2020-11-09 18:10:20'),
(150, 99, 25, '2020-11-09 18:10:26'),
(151, 99, 19, '2020-11-09 18:10:28'),
(152, 99, 21, '2020-11-09 18:10:43'),
(153, 99, 26, '2020-11-09 18:10:43'),
(154, 99, 30, '2020-11-09 18:11:12'),
(155, 99, 19, '2020-11-09 18:11:12'),
(156, 99, 22, '2020-11-09 18:11:26'),
(157, 99, 27, '2020-11-09 18:11:26'),
(158, 99, 32, '2020-11-09 18:11:35'),
(159, 99, 19, '2020-11-09 18:11:36'),
(160, 99, 23, '2020-11-09 18:11:49'),
(161, 99, 28, '2020-11-09 18:11:49'),
(162, 99, 36, '2020-11-09 18:11:55'),
(163, 100, 6, '2020-11-09 18:13:01'),
(164, 100, 7, '2020-11-09 18:13:05'),
(165, 100, 10, '2020-11-09 18:13:05'),
(166, 100, 13, '2020-11-09 18:13:08'),
(167, 100, 16, '2020-11-09 18:13:09'),
(168, 100, 17, '2020-11-09 18:13:19'),
(169, 100, 19, '2020-11-09 18:13:21'),
(170, 100, 23, '2020-11-09 18:13:33'),
(171, 100, 28, '2020-11-09 18:13:33'),
(172, 100, 36, '2020-11-09 18:13:39'),
(173, 100, 37, '2020-11-09 18:13:39'),
(174, 100, 40, '2020-11-09 18:13:51'),
(175, 100, 41, '2020-11-09 18:13:52'),
(176, 100, 42, '2020-11-09 18:13:57'),
(177, 100, 45, '2020-11-09 18:13:58'),
(178, 100, 49, '2020-11-09 18:14:05'),
(179, 100, 52, '2020-11-09 18:14:06'),
(180, 100, 62, '2020-11-09 18:14:53'),
(181, 100, 63, '2020-11-09 18:14:53');

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
(5, 1, 'chatbot', 'Oke, bye', 0),
(6, 2, 'chatbot', 'Hi, It is a smal text based escape game. Would you like to play?', 0),
(7, 2, 'human', 'Yes, why not. Let\'s play!', 0),
(8, 2, 'human', 'Not sure. What game is it?', 0),
(9, 2, 'human', 'No, thanks.', 0),
(10, 2, 'chatbot', 'Oke, the game is started.', 0),
(11, 2, 'chatbot', 'It is a small conversation based game, try it and you will see how it works!', 0),
(12, 2, 'chatbot', 'Oke, see you next time, bye!', 0),
(13, 2, 'human', 'Go ahead!', 0),
(14, 2, 'human', 'Ok, let\'s go ahead!', 0),
(15, 2, 'human', 'Ah.. no, thanks.', 0),
(16, 2, 'chatbot', 'Now you are in a small room. It is so dark here.', 0),
(17, 2, 'human', 'I am look around.', 0),
(18, 2, 'human', 'I am waiting.', 0),
(19, 2, 'chatbot', 'It\'s dark here but you see a window at left and a door right. Next to you there is a desk.', 0),
(20, 2, 'chatbot', 'You just keep waiting but nothing happens...', 0),
(21, 2, 'human', 'I am going to the window.', 0),
(22, 2, 'human', 'I am going to the door.', 0),
(23, 2, 'human', 'I am going to the desk.', 0),
(24, 2, 'human', 'I am waiting for a little while...', 0),
(25, 2, 'human', 'Oke, let\'s look around.', 0),
(26, 2, 'chatbot', 'You are at the window. It is dark outside. Some light from a city far away at near of the horizont.', 0),
(27, 2, 'chatbot', 'You are at the door. It seem it\'s closed.', 0),
(28, 2, 'chatbot', 'You are at the desk.', 0),
(29, 2, 'human', 'Oke, I am going to wait a little more...', 0),
(30, 2, 'human', 'Oke, let\'s look around.', 0),
(31, 2, 'human', 'Oke, I am going to wait a little more...', 0),
(32, 2, 'human', 'Oke, let\'s look around.', 0),
(33, 2, 'human', 'I am waiting for a little while...', 0),
(34, 2, 'human', 'Oke, let\'s look around.', 0),
(35, 2, 'human', 'I am waiting for a little while...', 0),
(36, 2, 'human', 'I am looking at the desk.', 0),
(37, 2, 'chatbot', 'Wow you just found a key here.', 0),
(38, 2, 'human', 'I am going to look around here.', 0),
(39, 2, 'human', 'I am waiting.', 0),
(40, 2, 'human', 'Cool, I take the key.', 0),
(41, 2, 'chatbot', 'You have a key now.', 0),
(42, 2, 'human', 'I am going to look around here.', 0),
(43, 2, 'human', 'I am waiting for a little while...', 0),
(44, 2, 'chatbot', 'It\'s dark here but you see a window at left and a door right. Next to you there is a desk', 1),
(45, 2, 'chatbot', 'You can see a window at left and a door right. Next to you there is a desk', 0),
(46, 2, 'chatbot', 'Nothing happens..', 0),
(47, 2, 'human', 'I am going to the window.', 0),
(48, 2, 'human', 'I am going to the desk.', 0),
(49, 2, 'human', 'I am going to the door.', 0),
(50, 2, 'chatbot', 'You are at the window. It is dark outside. Some light from a city far away at near of the horizont. ', 0),
(51, 2, 'chatbot', 'You are at the desk, but nothings here.', 0),
(52, 2, 'chatbot', 'You are at the door. It seems it\'s closed.', 0),
(53, 2, 'human', 'I am going to the window.', 0),
(54, 2, 'human', 'I am going to the desk.', 0),
(55, 2, 'human', 'I am going to the door.', 0),
(56, 2, 'human', 'I am going to the desk.', 0),
(57, 2, 'human', 'I am going to the door.', 0),
(58, 2, 'human', 'I am going to the window.', 0),
(59, 2, 'human', 'I am going to the door.', 0),
(60, 2, 'human', 'I am going to the window.', 0),
(61, 2, 'human', 'I am going to the desk.', 0),
(62, 2, 'human', 'I have a key, I am going to try it.', 0),
(63, 2, 'chatbot', 'Wow! It works, now you free. Congratulation, you won the game!', 0);

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
(4, 3, 5),
(5, 6, 7),
(6, 6, 8),
(7, 6, 9),
(8, 7, 10),
(9, 8, 11),
(10, 9, 12),
(11, 10, 13),
(12, 11, 14),
(13, 11, 15),
(14, 15, 12),
(15, 13, 16),
(16, 14, 16),
(17, 16, 17),
(18, 16, 18),
(19, 17, 19),
(20, 18, 20),
(21, 19, 21),
(22, 19, 22),
(23, 19, 23),
(24, 19, 24),
(25, 20, 25),
(26, 25, 19),
(27, 24, 20),
(28, 21, 26),
(29, 22, 27),
(30, 23, 28),
(31, 20, 29),
(32, 29, 20),
(33, 26, 30),
(34, 26, 31),
(35, 30, 19),
(36, 31, 20),
(37, 27, 32),
(38, 27, 33),
(39, 32, 19),
(40, 33, 20),
(41, 28, 34),
(42, 28, 35),
(43, 34, 19),
(44, 35, 20),
(45, 28, 36),
(46, 37, 38),
(47, 37, 39),
(48, 37, 40),
(49, 38, 19),
(50, 39, 20),
(51, 40, 41),
(52, 41, 42),
(53, 41, 43),
(54, 42, 45),
(55, 43, 46),
(56, 45, 47),
(57, 45, 48),
(58, 45, 49),
(59, 47, 50),
(60, 48, 51),
(61, 49, 52),
(62, 46, 53),
(63, 46, 54),
(64, 46, 55),
(65, 53, 50),
(66, 54, 51),
(67, 55, 52),
(68, 50, 56),
(69, 50, 57),
(70, 56, 51),
(71, 57, 52),
(72, 51, 58),
(73, 51, 59),
(74, 58, 50),
(75, 59, 52),
(76, 52, 60),
(77, 52, 61),
(78, 52, 62),
(79, 60, 50),
(80, 61, 51),
(81, 62, 63),
(82, 36, 37);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `conversation_message`
--
ALTER TABLE `conversation_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `message_to_message`
--
ALTER TABLE `message_to_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
