-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 20 août 2026 à 18:56
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
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(100) NOT NULL,
  `role` enum('admin','readonly') NOT NULL DEFAULT 'readonly',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'benoit_admin', '$2y$10$IK1QDuJ/n3zDFW2NKWa3WOOvPzCwunxTXR9OYIjpP2t.Tsg.LILe.', 'admin', '2026-08-18 20:18:10', '2026-06-04 04:55:29', '2026-08-18 20:18:10'),
(2, 'nicolas_admin', '$2y$10$4EMbfL9qhBtzTcxYQ74sr.lIdnQuCIlIGfp7.1CIbUWCN6dFknaGa', 'admin', NULL, '2026-06-04 04:55:29', '2026-06-04 04:55:29');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
