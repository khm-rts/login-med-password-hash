-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Vært: 127.0.0.1
-- Genereringstid: 15. 09 2016 kl. 08:16:29
-- Serverversion: 10.1.10-MariaDB
-- PHP-version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `brugere`
--

CREATE TABLE `brugere` (
  `bruger_id` mediumint(8) UNSIGNED NOT NULL,
  `bruger_navn` varchar(50) COLLATE utf8_danish_ci NOT NULL,
  `bruger_email` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `bruger_adgangskode` varchar(64) COLLATE utf8_danish_ci NOT NULL COMMENT 'Kun til at huske oprindelig adgangskode',
  `bruger_hashed_adgangskode` varchar(255) COLLATE utf8_danish_ci NOT NULL,
  `bruger_salt` varchar(16) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Data dump for tabellen `brugere`
--

INSERT INTO `brugere` (`bruger_id`, `bruger_navn`, `bruger_email`, `bruger_adgangskode`, `bruger_hashed_adgangskode`, `bruger_salt`) VALUES
(1, 'Elliot Alderson', 'mrrobot@gmail.com', 'trustno1', '$2y$10$DgqtH5o1sFZEAdKgkk5vWOAXed83c2D5h2v36pwxPUFNHEGhCJP2W', ''),
(2, 'Felicity Smoak', 'overwatch@gmail.com', 'letmein', '$2y$10$R5Ydtmz//fD3PP6.jzmqdumY9OTjNZi4pLLCNh7pX8JKjFDS1IPnW', ''),
(3, 'Thomas A. Anderson', 'neo@gmail.com', 'qwerty', '$2y$10$oulerPs3zOHyT/djRIksde65QhQOmkccQZnTGy/UKXhYYtrglW3Va', ''),
(4, 'John Connor', 'john@gmail.com', 'skynet', '$2y$10$n00q2j7nGzrdX01EiXiOw.QvAnWfCuXkD2QhtpBq5vudGnF04HwJS', ''),
(5, 'Lisbeth Salander', 'wasp@gmail.com', 'monkey', '$2y$10$//q5MzwClZQgNEiTmEmSJulTBWwu/VMTcdQLnpUz4B.Ahr.k.ez7u', ''),
(6, 'Kate Libby', 'acidburn@gmail.com', '1qaz2wsx', '$2y$10$oHew81fkMlawe/ZivJUkoO.CPKEaR8Gyp5G7SNv8woKxc4xcwuyi2', ''),
(7, 'Kasper Madsen', 'khm@rts.dk', '1234', '$2y$10$Ttm8YjMgQepEz20QUWD1xeH7C1S3emqv6K3vf7elb3hsosayqCPi6', '');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `brugere`
--
ALTER TABLE `brugere`
  ADD PRIMARY KEY (`bruger_id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `brugere`
--
ALTER TABLE `brugere`
  MODIFY `bruger_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
