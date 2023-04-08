-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 01 apr 2023 om 21:46
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
-- Database: `mvc2022`
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
(2, 'pear', 'green', 2),
(3, 'banana', 'yellow', 1),
(4, 'melon', 'yellow', 2),
(5, 'lemon', 'yellow', 0),
(6, 'kiwi', 'green', 1),
(7, 'cherry', 'red', 2),
(8, 'orange', 'orange', 4),
(9, 'avocado', 'green', 0),
(10, 'blackberries', 'black', 2),
(11, 'blueberries', 'blue', 1),
(12, 'cranberry', 'red', 1),
(13, 'date palm', 'brown', 5),
(14, 'grape', 'blue', 2),
(15, 'grape', 'white', 3),
(16, 'grapefruit', 'red', 1),
(17, 'lime', 'green', 0),
(18, 'mango', 'yellow', 3),
(19, 'nectarines', 'red', 4),
(20, 'olives', 'black', 1),
(21, 'olives', 'green', 0),
(22, 'passion Fruit', 'purple', 3),
(23, 'pineapple', 'yellow', 5),
(24, 'plum', 'black-purple', 2),
(25, 'pomegranate', 'red', 2),
(26, 'raspberry', 'light red', 3),
(27, 'redberries', 'red', 1),
(28, 'strawberry', 'red', 4),
(29, 'tangerine', 'orange', 3),
(30, 'watermelon', 'green', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `nav`
--

CREATE TABLE `nav` (
  `id` int(11) NOT NULL,
  `label` varchar(60) NOT NULL,
  `href` varchar(100) NOT NULL,
  `profile` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `nav`
--

INSERT INTO `nav` (`id`, `label`, `href`, `profile`) VALUES
(1, 'home', 'index.php', NULL),
(2, 'articles index', 'articles', NULL),
(3, 'articles show id1', 'article/1', 1),
(4, 'photos', 'gallery', 1),
(5, 'fruit index', 'fruits', 1),
(6, 'fruity index', 'fruity', 1),
(7, 'login', 'login', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `forgot_hash` varchar(60) DEFAULT NULL,
  `profile` varchar(10) NOT NULL,
  `token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `forgot_hash`, `profile`, `token`) VALUES
(1, 'admin@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, 'admin', NULL),
(2, 'user@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, 'user', NULL),
(3, 'user1@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, 'user', NULL),
(4, 'user2@app.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, 'user', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users_fruits`
--

CREATE TABLE `users_fruits` (
  `user_id` int(11) NOT NULL,
  `fruit_id` int(11) NOT NULL,
  `likes` tinyint(1) NOT NULL,
  `comment` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `users_fruits`
--

INSERT INTO `users_fruits` (`user_id`, `fruit_id`, `likes`, `comment`) VALUES
(2, 28, 4, NULL),
(3, 1, 4, 'nice and juicy'),
(3, 3, 1, 'to soft'),
(3, 12, 3, 'very sweet'),
(3, 28, 5, NULL),
(4, 1, 2, 'is ok'),
(4, 9, 3, 'very healthy!'),
(4, 12, 3, 'nice in pies');

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
-- Indexen voor tabel `users_fruits`
--
ALTER TABLE `users_fruits`
  ADD PRIMARY KEY (`user_id`,`fruit_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT voor een tabel `nav`
--
ALTER TABLE `nav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
