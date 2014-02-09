-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 09 2014 г., 09:58
-- Версия сервера: 5.6.14
-- Версия PHP: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `gruppef`
--

-- --------------------------------------------------------

--
-- Структура таблицы `file_liste`
--

CREATE TABLE IF NOT EXISTS `file_liste` (
  `fileid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(60) NOT NULL,
  PRIMARY KEY (`fileid`),
  UNIQUE KEY `fileid_UNIQUE` (`fileid`),
  UNIQUE KEY `filename_UNIQUE` (`filename`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=126 ;

--
-- Дамп данных таблицы `file_liste`
--

INSERT INTO `file_liste` (`fileid`, `filename`) VALUES
(121, ''),
(101, '1497863_637766332932353_1505618577_o[1].jpg'),
(102, '5j6gdg38.jpg'),
(103, '894759_606032306105756_939174192_o[1].jpg'),
(108, 'cute.jpg'),
(104, 'Horse_Denver.jpeg'),
(105, 'IMG_0518.JPG'),
(109, 'marshmallow2(alternate)2cake.png'),
(110, 'pieversuscake.png'),
(125, 'q'),
(106, 'Repin_Cossacks.jpg'),
(107, 'Seal.jpg'),
(111, 'victoria-harbour.jpg'),
(112, 'win95-boxes.jpg'),
(113, 'windows-95-wallpaper.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `new_table`
--

CREATE TABLE IF NOT EXISTS `new_table` (
  `file_name` varchar(60) NOT NULL,
  `teg` varchar(60) NOT NULL,
  PRIMARY KEY (`file_name`,`teg`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
