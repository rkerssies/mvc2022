-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 23 aug 2022 om 20:30
-- Serverversie: 10.4.17-MariaDB
-- PHP-versie: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fruit_db`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `articles`
--

INSERT INTO `articles` (`id`, `title`, `text`) VALUES
(1, 'Home', 'Home bla bla <b>bla</b>'),
(2, 'Groet', 'Hallo, moi, doei, hello'),
(3, 'New input', 'New bla bla <b>bla</b>');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fruits`
--

CREATE TABLE `fruits` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `color` varchar(20) NOT NULL,
  `sweetness` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `fruits`
--

INSERT INTO `fruits` (`id`, `name`, `color`, `sweetness`) VALUES
(1, 'apple', 'green', 0),
(2, 'pear', 'green', NULL),
(3, 'banaan', 'yellow', 1),
(4, 'meloen', 'geel', 1),
(5, 'lemon', 'yellow', 0),


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `nav`
--

CREATE TABLE `nav` (
  `id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `href` varchar(100) NOT NULL,
  `profile` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `nav`
--

INSERT INTO `nav` (`id`, `label`, `href`, `profile`) VALUES
(1, 'home', 'index.php', NULL),
(2, 'articles index', 'articles', NULL),
(3, 'articles show id=1', 'article/1', 1);
(4, 'photos', 'gallery', 1);
(5, 'fruit index', 'fruits/var_value1/var_value2', 1);
(6, 'fruity index', 'fruity', 1);
(7, 'login', 'login', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `profile`) VALUES
(1, 'admin@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'admin'),
(2, 'prutser@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8\r\n', 'user');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `fruits`
--
ALTER TABLE `fruits`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `nav`
--
ALTER TABLE `nav`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `fruits`
--
ALTER TABLE `fruits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


--
-- AUTO_INCREMENT voor een tabel `nav`
--
ALTER TABLE `nav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
