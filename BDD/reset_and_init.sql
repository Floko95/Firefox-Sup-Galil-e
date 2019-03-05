-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 05 mars 2019 à 03:26
-- Version du serveur :  5.7.24
-- Version de PHP :  7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `BDE sup Galilee`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

DROP TABLE IF EXISTS `actualites`;
CREATE TABLE IF NOT EXISTS `actualites` (
  `idActualites` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` int(10) UNSIGNED NOT NULL,
  `idImages` int(10) UNSIGNED NOT NULL,
  `actualite` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `datePublication` timestamp NOT NULL,
  PRIMARY KEY (`idActualites`),
  KEY `FK_actualites_id` (`id`),
  KEY `FK_actualites_idImages` (`idImages`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `attributiondroitsauxroles`
--

DROP TABLE IF EXISTS `attributiondroitsauxroles`;
CREATE TABLE IF NOT EXISTS `attributiondroitsauxroles` (
  `idRoles` int(10) UNSIGNED NOT NULL,
  `idDroits` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`idRoles`,`idDroits`),
  KEY `FK_aDAR_idDroits` (`idDroits`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `attributionrolesauxetudiants`
--

DROP TABLE IF EXISTS `attributionrolesauxetudiants`;
CREATE TABLE IF NOT EXISTS `attributionrolesauxetudiants` (
  `id` int(10) UNSIGNED NOT NULL,
  `idRoles` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`,`idRoles`),
  KEY `FK_aRAE_idRoles` (`idRoles`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `droits`
--

DROP TABLE IF EXISTS `droits`;
CREATE TABLE IF NOT EXISTS `droits` (
  `idDroits` int(10) UNSIGNED NOT NULL,
  `droit` varchar(30) NOT NULL,
  `descriptionDroit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idDroits`),
  UNIQUE KEY `droit` (`droit`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailUniv` varchar(70) NOT NULL,
  `mailPerso` varchar(70) DEFAULT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `numero` int(10) UNSIGNED NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `formation` varchar(4) DEFAULT NULL,
  `promotion` year(4) DEFAULT NULL,
  `dateInscription` timestamp NOT NULL,
  `etat` int(1) DEFAULT '0',
  `code` varchar(50) DEFAULT NULL,
  `typeCode` int(1) NOT NULL,
  `dateMail` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `idImages` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(25) NOT NULL,
  `binaire` blob NOT NULL,
  PRIMARY KEY (`idImages`),
  KEY `FK_boutique_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `idMessages` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` int(10) UNSIGNED NOT NULL,
  `idTopics` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `dateEnvoi` timestamp NOT NULL,
  PRIMARY KEY (`idMessages`),
  KEY `FK_messages_id` (`id`),
  KEY `FK_messages_idTopics` (`idTopics`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `objets`
--

DROP TABLE IF EXISTS `objets`;
CREATE TABLE IF NOT EXISTS `objets` (
  `idObjets` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` int(10) UNSIGNED NOT NULL,
  `idImages` int(10) UNSIGNED NOT NULL,
  `objet` varchar(255) NOT NULL,
  `prix` int(11) DEFAULT NULL,
  PRIMARY KEY (`idObjets`),
  KEY `FK_objets_id` (`id`),
  KEY `FK_objets_idImages` (`idImages`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `idRoles` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` varchar(30) NOT NULL,
  `descriptionRole` varchar(255) DEFAULT NULL,
  `supprimable` int(11) DEFAULT '1',
  PRIMARY KEY (`idRoles`),
  UNIQUE KEY `role` (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `idTags` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idTopics` int(10) UNSIGNED NOT NULL,
  `tag` varchar(20) NOT NULL,
  PRIMARY KEY (`idTags`),
  KEY `FK_tags_idTags` (`idTopics`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `topics`
--

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
  `idTopics` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id` int(10) UNSIGNED NOT NULL,
  `topic` varchar(255) NOT NULL,
  `dateCreation` timestamp NOT NULL,
  PRIMARY KEY (`idTopics`),
  KEY `FK_topics_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
