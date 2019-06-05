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
  `date_create` datetime NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `prefix_media`
--

--------------------------------------------------------

--
-- Структура таблицы `prefix_media_target`
--

CREATE TABLE `prefix_media_target` (
  `id` int(11) UNSIGNED NOT NULL,
  `media_id` int(11) UNSIGNED NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) NOT NULL,
  `date_create` datetime NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `prefix_media_target`
--


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
  ADD KEY `date_create` (`date_create`);

--
-- Индексы таблицы `prefix_media_target`
--
ALTER TABLE `prefix_media_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `target_type` (`target_type`),
  ADD KEY `date_create` (`date_create`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `prefix_media`
--
ALTER TABLE `prefix_media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT для таблицы `prefix_media_target`
--
ALTER TABLE `prefix_media_target`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;