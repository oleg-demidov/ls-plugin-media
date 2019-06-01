-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июн 01 2019 г., 17:04
-- Версия сервера: 5.7.25-0ubuntu0.16.04.2
-- Версия PHP: 7.0.33-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fend`
--

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_media`
--

CREATE TABLE `prefix_media` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `path` varchar(500) NOT NULL,
  `name` varchar(500) NOT NULL,
  `size` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `prefix_media`
--

INSERT INTO `prefix_media` (`id`, `user_id`, `type`, `path`, `name`, `size`, `date_add`, `data`) VALUES
(39, 1, 'image', '[relative]/uploads/media/image/2019/06/01/13/0771764f987daee9f4c3.jpg', 'Без названия (1).jpeg', 11537, '2019-06-01 13:58:33', '{"sizes":[{"w":500,"h":null,"crop":false},{"w":"100","h":"100","crop":true}]}'),
(40, 1, 'image', '[relative]/uploads/media/image/2019/06/01/13/f5bf608d7068ffe06e1f.jpg', 'Без названия.jpeg', 12953, '2019-06-01 13:58:43', '{"sizes":[{"w":500,"h":null,"crop":false},{"w":"100","h":"100","crop":true}]}');

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_media_target`
--

CREATE TABLE `prefix_media_target` (
  `id` int(11) UNSIGNED NOT NULL,
  `media_id` int(11) UNSIGNED NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) NOT NULL,
  `date_add` datetime NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `prefix_media_target`
--

INSERT INTO `prefix_media_target` (`id`, `media_id`, `target_id`, `target_type`, `date_add`, `data`) VALUES
(1, 1, 11, 'ser', '2018-12-19 00:00:00', ''),
(17, 4, 16, 'response', '2018-12-26 15:06:21', 'a:0:{}'),
(18, 4, 2, 'proposal', '2018-12-27 09:49:40', 'a:0:{}'),
(19, 4, 2, 'proposal', '2018-12-27 09:49:41', 'a:0:{}'),
(20, 4, 3, 'proposal', '2018-12-27 09:52:26', 'a:0:{}'),
(21, 4, 3, 'proposal', '2018-12-27 09:52:26', 'a:0:{}'),
(22, 4, 4, 'proposal', '2018-12-27 09:52:41', 'a:0:{}'),
(23, 4, 4, 'proposal', '2018-12-27 09:52:41', 'a:0:{}'),
(24, 4, 5, 'proposal', '2018-12-27 09:53:33', 'a:0:{}'),
(25, 4, 5, 'proposal', '2018-12-27 09:53:33', 'a:0:{}'),
(27, 3, 19, 'proposal', '2019-01-13 12:26:56', 'a:0:{}'),
(28, 1, 21, 'response', '2019-01-14 07:37:28', 'a:0:{}'),
(29, 1, 22, 'response', '2019-01-14 08:12:46', 'a:0:{}'),
(30, 2, 23, 'proposal', '2019-01-14 08:17:14', 'a:0:{}'),
(31, 3, 29, 'arbitrage', '2019-01-14 13:32:50', 'a:0:{}'),
(32, 3, 30, 'response', '2019-01-14 14:25:27', 'a:0:{}'),
(34, 2, 39, 'answer', '2019-02-14 08:10:58', 'a:0:{}'),
(36, 3, 1, 'user_photo', '2019-02-15 07:47:29', 'a:1:{s:4:"size";s:5:"photo";}');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `prefix_media`
--
ALTER TABLE `prefix_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `file_size` (`size`),
  ADD KEY `date_add` (`date_add`);

--
-- Индексы таблицы `prefix_media_target`
--
ALTER TABLE `prefix_media_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `target_type` (`target_type`),
  ADD KEY `date_add` (`date_add`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `prefix_media`
--
ALTER TABLE `prefix_media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT для таблицы `prefix_media_target`
--
ALTER TABLE `prefix_media_target`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;