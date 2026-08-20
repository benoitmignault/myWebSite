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
-- Structure de la table `players`
--

CREATE TABLE `players` (
  `id` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `average_score` decimal(5,1) UNSIGNED DEFAULT NULL,
  `handicap_start` decimal(4,1) NOT NULL DEFAULT 0.0,
  `handicap_league` decimal(4,1) NOT NULL DEFAULT 0.0,
  `handicap_rounded` int(11) NOT NULL DEFAULT 0,
  `previous_position` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `players`
--

INSERT INTO `players` (`id`, `firstname`, `lastname`, `average_score`, `handicap_start`, `handicap_league`, `handicap_rounded`, `previous_position`, `created_at`, `updated_at`) VALUES
(1, 'Danny', 'Guerrin', 90.8, 14.0, 13.0, 13, 20, '2026-05-25 23:56:52', '2026-08-18 20:24:29'),
(2, 'Monia', 'Roulier', 105.9, 28.0, 28.8, 29, 8, '2026-05-25 23:56:52', '2026-08-18 20:34:36'),
(3, 'Mai-Révée', 'Dolceb', 109.2, 33.9, 30.0, 30, 15, '2026-05-25 23:56:52', '2026-08-18 20:19:30'),
(4, 'Daniel', 'Lefebvre', 90.9, 15.0, 15.0, 15, 9, '2026-05-25 23:56:52', '2026-08-18 20:28:38'),
(5, 'Eric', 'Grimard', 102.9, 17.0, 24.8, 25, 4, '2026-05-25 23:56:52', '2026-08-11 01:15:42'),
(6, 'Luc', 'Grimard', 109.4, 28.0, 33.0, 33, 16, '2026-05-25 23:56:52', '2026-08-18 20:22:12'),
(7, 'Robert', 'Labelle', 85.5, 14.0, 9.8, 10, 1, '2026-05-25 23:56:52', '2026-08-18 20:28:13'),
(8, 'Benoît', 'Mignault', 87.8, 11.9, 8.5, 9, 14, '2026-05-25 23:56:52', '2026-08-18 20:19:30'),
(9, 'Robert', 'Gaboriault', 96.3, 17.0, 19.8, 20, 7, '2026-05-25 23:56:52', '2026-08-18 20:25:17'),
(10, 'Nicolas', 'Carrière', 82.0, 7.0, 6.7, 7, 5, '2026-05-25 23:56:52', '2026-08-18 20:19:30'),
(11, 'Normand', 'Gagnon', 87.8, 13.0, 12.0, 12, 19, '2026-05-25 23:56:52', '2026-08-18 20:22:35'),
(12, 'Mathieu', 'Robidas', 83.3, 7.0, 8.7, 9, 6, '2026-05-25 23:56:52', '2026-08-18 20:33:54'),
(13, 'Maxime', 'Paulin', 89.7, 20.0, 15.0, 15, 23, '2026-05-25 23:56:52', '2026-08-18 20:19:30'),
(14, 'Jean-François', 'Asselin', 94.0, 23.0, 22.0, 22, 27, '2026-05-25 23:56:52', '2026-08-03 19:03:38'),
(15, 'Sylvain', 'Gervais', 88.6, 14.0, 14.0, 14, 3, '2026-05-25 23:56:52', '2026-08-18 20:34:07'),
(16, 'Martin', 'Taillon', 89.4, 20.0, 12.7, 13, 10, '2026-05-25 23:56:52', '2026-08-18 20:19:30'),
(17, 'Jean-Sébastien', 'Patenaude', 86.6, 12.0, 11.3, 11, 12, '2026-05-25 23:56:52', '2026-08-18 20:19:44'),
(18, 'Jean-Pierre', 'Duval', 93.6, 18.0, 16.8, 17, 2, '2026-05-25 23:56:52', '2026-08-18 20:24:59'),
(19, 'Mylène', 'Pelletier', 91.0, 17.0, 19.0, 19, 26, '2026-05-25 23:56:52', '2026-08-03 19:03:38'),
(20, 'Marie-France', 'Clermont', 109.6, 34.0, 31.7, 32, 11, '2026-05-26 16:33:53', '2026-08-18 20:21:59'),
(21, 'Stéphane', 'Blain', 102.2, 23.0, 24.7, 25, 13, '2026-05-29 00:11:10', '2026-08-18 20:34:24'),
(22, 'Cédric', 'Dinardo', 108.0, 23.0, 36.0, 36, 29, '2026-06-04 13:03:14', '2026-08-18 20:19:30'),
(23, 'Dino', 'Mazza', 90.0, 23.0, 18.0, 18, 21, '2026-06-04 15:10:41', '2026-08-18 20:19:30'),
(24, 'Samuel', 'Lavoie', 90.6, 20.0, 15.0, 15, 17, '2026-06-08 14:41:31', '2026-08-18 20:19:30'),
(25, 'Annie', 'Lafontaine', 103.0, 28.0, 27.0, 27, 24, '2026-06-27 03:14:26', '2026-08-18 20:33:34'),
(26, 'Marc-André', 'Blais', 86.0, 11.5, 14.0, 14, 22, '2026-06-30 06:15:16', '2026-08-18 20:19:30'),
(27, 'Alain', 'Caouette', 86.5, 11.3, 11.0, 11, 25, '2026-07-06 14:29:59', '2026-08-03 19:03:38'),
(28, 'Stéphane', 'Dion', 90.3, 7.7, 15.0, 15, 18, '2026-07-07 16:19:07', '2026-08-18 20:24:45'),
(29, 'Ghislain', 'Bellemare', 98.0, 18.6, 26.0, 26, 30, '2026-08-08 15:09:27', '2026-08-11 01:15:27'),
(30, 'Sylvain', 'Lapointe', 86.5, 7.4, 12.0, 12, 28, '2026-08-08 15:09:39', '2026-08-18 20:24:15'),
(31, 'Olivier', 'Taillon', 86.0, 9.0, 14.0, 14, 31, '2026-08-12 22:24:08', '2026-08-18 20:19:30'),
(32, 'Patrice', 'Levert', 105.0, 27.5, 33.0, 33, 33, '2026-08-13 13:31:06', '2026-08-18 20:27:20'),
(33, 'Tony', 'O\'Malley', 92.0, 15.0, 20.0, 20, 32, '2026-08-15 15:36:01', '2026-08-18 20:33:06');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
