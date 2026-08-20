-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 20 août 2026 à 18:57
-- Version du serveur : 10.6.28-MariaDB
-- Version de PHP : 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `benoitmignault_league_golf_monteregie`
--

-- --------------------------------------------------------

--
-- Structure de la table `website_logs_sponsors`
--

CREATE TABLE `website_logs_sponsors` (
  `id` int(11) NOT NULL,
  `log_date` datetime NOT NULL,
  `media_type` varchar(50) NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `sponsor_name` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `website_logs_sponsors`
--

INSERT INTO `website_logs_sponsors` (`id`, `log_date`, `media_type`, `sponsor_id`, `sponsor_name`, `ip_address`) VALUES
(12, '2026-05-24 20:54:39', 'website', 3, 'Golf en Montérégie', NULL),
(16, '2026-05-25 20:45:31', 'website', 6, 'FlexiGolf', NULL),
(17, '2026-05-26 12:48:11', 'website', 3, 'Golf en Montérégie', NULL),
(18, '2026-05-26 12:52:05', 'website', 6, 'FlexiGolf', NULL),
(19, '2026-05-26 12:52:27', 'website', 2, 'Station GO', NULL),
(20, '2026-05-28 15:04:47', 'website', 2, 'Station GO', NULL),
(21, '2026-05-30 09:39:34', 'instagram', 2, 'Station GO', NULL),
(32, '2026-06-07 18:16:27', 'facebook', 1, 'Apex Golf', NULL),
(34, '2026-06-12 13:21:09', 'website', 4, 'Toucani', NULL),
(35, '2026-06-12 13:55:56', 'website', 4, 'Toucani', NULL),
(36, '2026-06-12 13:56:05', 'website', 4, 'Toucani', NULL),
(37, '2026-06-12 14:35:55', 'website', 6, 'FlexiGolf', NULL),
(38, '2026-06-12 14:39:37', 'website', 3, 'Golf en Montérégie', NULL),
(39, '2026-06-12 17:46:38', 'website', 4, 'Toucani', NULL),
(41, '2026-06-14 08:53:05', 'website', 4, 'Toucani', '173.179.213.88'),
(42, '2026-06-14 11:11:26', 'website', 4, 'Toucani', '167.127.90.97'),
(43, '2026-06-14 11:16:25', 'website', 4, 'Toucani', '173.179.213.88'),
(45, '2026-06-16 09:58:35', 'website', 4, 'Toucani', '184.95.227.164'),
(46, '2026-06-16 20:25:47', 'website', 4, 'Toucani', '166.62.246.101'),
(47, '2026-06-16 21:36:15', 'website', 4, 'Toucani', '142.169.16.32'),
(48, '2026-06-20 08:53:52', 'website', 4, 'Toucani', '173.179.213.88'),
(49, '2026-06-27 14:07:59', 'website', 4, 'Toucani', '166.62.246.101'),
(50, '2026-06-27 14:10:50', 'website', 4, 'Toucani', '166.62.246.101'),
(51, '2026-06-30 16:30:14', 'website', 3, 'Golf en Montérégie', '74.56.232.202'),
(52, '2026-07-01 19:20:38', 'website', 3, 'Golf en Montérégie', '70.49.215.32'),
(53, '2026-07-04 15:22:51', 'website', 4, 'Toucani', '67.69.76.209'),
(54, '2026-07-05 19:03:52', 'website', 4, 'Toucani', '166.62.246.101'),
(55, '2026-07-06 17:03:10', 'website', 4, 'Toucani', '173.179.213.88'),
(56, '2026-07-07 19:19:59', 'website', 4, 'Toucani', '173.179.213.88'),
(57, '2026-07-08 16:30:46', 'website', 6, 'FlexiGolf', '173.179.213.88'),
(58, '2026-07-08 16:40:29', 'website', 6, 'FlexiGolf', '173.179.213.88'),
(59, '2026-07-20 20:40:00', 'website', 4, 'Toucani', '204.48.78.73'),
(60, '2026-07-22 19:54:01', 'website', 4, 'Toucani', '204.48.79.155'),
(62, '2026-08-10 22:56:43', 'website', 4, 'Toucani', '107.171.158.101');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `website_logs_sponsors`
--
ALTER TABLE `website_logs_sponsors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `website_logs_sponsors`
--
ALTER TABLE `website_logs_sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
