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
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `golf_course` varchar(255) NOT NULL,
  `golf_course_website` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_open` tinyint(1) NOT NULL DEFAULT 0,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `is_updated` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `event_name`, `golf_course`, `golf_course_website`, `event_date`, `created_at`, `updated_at`, `is_open`, `is_closed`, `is_updated`) VALUES
(1, 'Semaine 1', 'Club de golf Vallée des forts', 'https://golfvalleedesforts.com/', '2026-05-10', '2026-05-25 23:56:29', '2026-06-04 04:58:31', 0, 1, 0),
(2, 'Semaine 2', 'Club de golf Farnham', 'https://www.farnhamgolf.com/', '2026-05-17', '2026-05-25 23:56:29', '2026-06-04 04:58:36', 0, 1, 0),
(3, 'Semaine 3', 'Club de golf La Seigneurie', 'https://golflaseigneurie.ca/', '2026-05-24', '2026-05-25 23:56:29', '2026-06-04 04:58:41', 0, 1, 0),
(4, 'Semaine 4', 'Club de golf Napierville', 'https://golfnapierville.ca/', '2026-05-31', '2026-05-25 23:56:29', '2026-06-10 05:46:09', 0, 1, 0),
(5, 'Semaine 5', 'Club de golf Rouville', 'https://golfderouville.com/', '2026-06-07', '2026-06-04 06:03:23', '2026-06-11 03:20:20', 0, 1, 0),
(6, 'Semaine 11', 'Club de golf Acton Vale (Parcours Valois & Renne)', 'https://golfactonvale.qc.ca/', '2026-07-19', '2026-06-11 00:24:47', '2026-07-20 21:31:02', 0, 1, 0),
(7, 'Semaine 7 (Cancel après presque 9 trous de joués)', 'Club de golf Verchères (Parcours Verchères)', 'https://golfvercheres.com/', '2026-06-21', '2026-06-11 22:55:32', '2026-06-23 02:52:23', 0, 1, 0),
(8, 'The Masters', 'Parcours du Vieux-Village (Bromont)', 'https://parcoursduvieuxvillage.com/', '2026-06-28', '2026-06-11 22:57:30', '2026-06-30 03:42:59', 0, 1, 0),
(9, 'Semaine 9', 'Club de golf Hemmingford (Parcours Village)', 'https://golfhemmingford.com/', '2026-07-05', '2026-06-11 23:02:16', '2026-07-07 16:03:09', 0, 1, 0),
(10, 'Semaine 10 (Mode Vegas)', 'Club de golf Continental', 'https://www.golfcontinental.ca/', '2026-07-12', '2026-06-11 23:15:48', '2026-07-13 16:10:45', 0, 1, 0),
(11, 'Semaine 12', 'Club de golf International 2000 (Parcours St-Bernard & Champlain)', 'https://international2000.ca/', '2026-07-26', '2026-07-07 20:39:45', '2026-07-28 11:48:44', 0, 1, 0),
(12, 'Semaine 13', 'Club de golf Milby', 'https://golfmilby.com/', '2026-08-02', '2026-07-24 13:01:24', '2026-08-03 19:13:23', 0, 1, 0),
(13, 'Semaine 14', 'Club de golf Saint-Césaire', 'https://golfsaintcesaire.com/', '2026-08-09', '2026-07-09 16:27:32', '2026-08-11 01:15:42', 0, 1, 0),
(14, 'Semaine 15 (Mode Vegas)', 'Club de golf Vallée des forts', 'https://golfvalleedesforts.com/', '2026-08-16', '2026-08-11 01:17:31', '2026-08-18 20:34:36', 0, 1, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
