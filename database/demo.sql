-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 08 oct. 2026 à 13:52
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `atelier_mosaique_demo`
--

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int UNSIGNED NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_name` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` enum('image/jpeg','image/png') COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_bytes` int UNSIGNED NOT NULL,
  `width` int UNSIGNED NOT NULL,
  `height` int UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_images_storage_name` (`project_id`,`storage_name`),
  KEY `idx_images_project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`id`, `project_id`, `original_name`, `storage_name`, `mime_type`, `size_bytes`, `width`, `height`, `created_at`) VALUES
(1, 1, 'mosaic_1400x900_m-0_mode-normal_seed-1790255348033_gap-8_rad-12_bg-000000.png', 'd89f4a8da304b91afafe1cbf1db047ef.png', 'image/png', 1578268, 1400, 900, '2026-10-08 13:50:52');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archived_at` timestamp NULL DEFAULT NULL,
  `mosaic_width` smallint UNSIGNED NOT NULL DEFAULT '1200',
  `mosaic_height` smallint UNSIGNED NOT NULL DEFAULT '900',
  `mosaic_mode` enum('dense','normal','aere') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `mosaic_gap` tinyint UNSIGNED NOT NULL DEFAULT '8',
  `mosaic_radius` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `mosaic_bg` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#12121a',
  `mosaic_bg_transparent` tinyint(1) NOT NULL DEFAULT '0',
  `mosaic_margin` smallint UNSIGNED NOT NULL DEFAULT '0',
  `mosaic_seed` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_projects_archived_at` (`archived_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `name`, `created_at`, `archived_at`, `mosaic_width`, `mosaic_height`, `mosaic_mode`, `mosaic_gap`, `mosaic_radius`, `mosaic_bg`, `mosaic_bg_transparent`, `mosaic_margin`, `mosaic_seed`) VALUES
(1, 'Mosaïque des jardins imaginaires', '2026-10-08 13:35:59', NULL, 1200, 900, 'normal', 8, 0, '#12121a', 0, 0, NULL),
(2, 'Les couleurs de la ville', '2026-10-08 13:35:59', NULL, 1200, 900, 'normal', 8, 0, '#12121a', 0, 0, NULL),
(3, 'Le voyage des lucioles', '2026-10-08 13:35:59', NULL, 1200, 900, 'normal', 8, 0, '#12121a', 0, 0, NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
