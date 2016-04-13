-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Gegenereerd op: 13 apr 2016 om 14:34
-- Serverversie: 5.6.28-0ubuntu0.14.04.1
-- PHP-versie: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `km`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `auth_users`
--

CREATE TABLE IF NOT EXISTS `auth_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` text NOT NULL,
  `secret` text NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(10) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `confirm_token` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `role` text NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `routes`
--

CREATE TABLE IF NOT EXISTS `routes` (
  `id_route` int(11) NOT NULL AUTO_INCREMENT,
  `omschrijving` varchar(100) NOT NULL DEFAULT 'Geen omschrijving',
  `date` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `route` text NOT NULL,
  `kms` float NOT NULL,
  `betaald` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_route`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `se_key` varchar(100) NOT NULL,
  `se_value` varchar(100) NOT NULL,
  `se_human` varchar(100) NOT NULL,
  `se_desc` text NOT NULL,
  UNIQUE KEY `se_key` (`se_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
