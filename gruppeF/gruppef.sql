-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2014 at 06:07 PM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gruppef`
--

-- --------------------------------------------------------

--
-- Table structure for table `file_liste`
--

CREATE TABLE IF NOT EXISTS `file_liste` (
  `fileid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(60) NOT NULL,
  `commentary` varchar(255) DEFAULT NULL,
  `rating` int(2) DEFAULT NULL,
  PRIMARY KEY (`fileid`),
  UNIQUE KEY `fileid_UNIQUE` (`fileid`),
  UNIQUE KEY `filename_UNIQUE` (`filename`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=138 ;

--
-- Dumping data for table `file_liste`
--

INSERT INTO `file_liste` (`fileid`, `filename`, `commentary`, `rating`) VALUES
(106, 'Repin_Cossacks.jpg', NULL, NULL),
(107, 'Seal.jpg', NULL, NULL),
(108, 'cute.jpg', NULL, NULL),
(109, 'marshmallow2(alternate)2cake.png', NULL, NULL),
(110, 'pieversuscake.png', NULL, NULL),
(129, 'IMG_0518.JPG', NULL, NULL),
(130, '1412781_613033752072278_1785234711_o[1].jpg', NULL, NULL),
(131, '1497863_637766332932353_1505618577_o[1].jpg', NULL, NULL),
(132, '5j6gdg38.jpg', NULL, NULL),
(133, '894759_606032306105756_939174192_o[1].jpg', NULL, NULL),
(134, 'Horse_Denver.jpeg', NULL, NULL),
(135, 'victoria-harbour.jpg', NULL, NULL),
(136, 'win95-boxes.jpg', NULL, NULL),
(137, 'windows-95-wallpaper.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `fileid` int(10) unsigned NOT NULL,
  `tags` varchar(60) NOT NULL,
  PRIMARY KEY (`fileid`,`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tag`
--
ALTER TABLE `tag`
  ADD CONSTRAINT `fileid` FOREIGN KEY (`fileid`) REFERENCES `file_liste` (`fileid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
