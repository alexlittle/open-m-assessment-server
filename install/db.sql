-- phpMyAdmin SQL Dump
-- version 3.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 20, 2011 at 09:27 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mquiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `langid` bigint(20) NOT NULL AUTO_INCREMENT,
  `langref` varchar(50) NOT NULL,
  `langtext` text NOT NULL,
  `langcode` varchar(10) NOT NULL,
  PRIMARY KEY (`langid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=113 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `loglevel` varchar(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `logtype` varchar(20) NOT NULL,
  `logmsg` text NOT NULL,
  `logip` varchar(20) NOT NULL,
  `logpagephptime` float DEFAULT NULL,
  `logpagequeries` int(11) DEFAULT NULL,
  `logpagemysqltime` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=259 ;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `questionid` bigint(20) NOT NULL AUTO_INCREMENT,
  `questiontitleref` varchar(50) NOT NULL,
  `createdby` bigint(20) NOT NULL,
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`questionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `questionprop`
--

DROP TABLE IF EXISTS `questionprop`;
CREATE TABLE IF NOT EXISTS `questionprop` (
  `questionpropid` bigint(20) NOT NULL AUTO_INCREMENT,
  `questionid` bigint(20) NOT NULL,
  `questionpropname` varchar(20) NOT NULL,
  `questionpropvalue` text NOT NULL,
  PRIMARY KEY (`questionpropid`),
  KEY `new_fk_constraint` (`questionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `questionresponse`
--

DROP TABLE IF EXISTS `questionresponse`;
CREATE TABLE IF NOT EXISTS `questionresponse` (
  `questionid` bigint(20) NOT NULL,
  `responseid` bigint(20) NOT NULL,
  `orderno` int(11) NOT NULL,
  PRIMARY KEY (`orderno`,`responseid`,`questionid`),
  KEY `qr_question_fk_constraint` (`questionid`),
  KEY `qr_response_fk_constraint` (`responseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
CREATE TABLE IF NOT EXISTS `quiz` (
  `quizid` bigint(20) NOT NULL AUTO_INCREMENT,
  `quiztitleref` varchar(200) NOT NULL,
  `createdby` bigint(20) NOT NULL,
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`quizid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizattempt`
--

DROP TABLE IF EXISTS `quizattempt`;
CREATE TABLE IF NOT EXISTS `quizattempt` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `quizref` varchar(200) CHARACTER SET latin1 NOT NULL,
  `qadate` bigint(20) NOT NULL,
  `qascore` int(11) NOT NULL,
  `qauser` varchar(200) CHARACTER SET latin1 NOT NULL,
  `submitdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submituser` varchar(200) CHARACTER SET latin1 NOT NULL,
  `maxscore` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizattemptresponse`
--

DROP TABLE IF EXISTS `quizattemptresponse`;
CREATE TABLE IF NOT EXISTS `quizattemptresponse` (
  `qarid` bigint(20) NOT NULL AUTO_INCREMENT,
  `qaid` bigint(20) NOT NULL,
  `responserefid` varchar(200) NOT NULL,
  `questionrefid` varchar(200) NOT NULL,
  `qarscore` int(11) NOT NULL,
  PRIMARY KEY (`qarid`),
  KEY `qar_quizattempt_fk_constraint` (`qaid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizprop`
--

DROP TABLE IF EXISTS `quizprop`;
CREATE TABLE IF NOT EXISTS `quizprop` (
  `quizpropid` bigint(20) NOT NULL AUTO_INCREMENT,
  `quizid` bigint(20) NOT NULL,
  `quizpropname` varchar(20) NOT NULL,
  `quizpropvalue` text NOT NULL,
  PRIMARY KEY (`quizpropid`),
  KEY `new_fk_constraint2` (`quizid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `quizquestion`
--

DROP TABLE IF EXISTS `quizquestion`;
CREATE TABLE IF NOT EXISTS `quizquestion` (
  `quizid` bigint(20) NOT NULL,
  `questionid` bigint(20) NOT NULL,
  `orderno` int(11) NOT NULL,
  PRIMARY KEY (`quizid`,`questionid`,`orderno`),
  KEY `qq_question_fk_constraint` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `response`
--

DROP TABLE IF EXISTS `response`;
CREATE TABLE IF NOT EXISTS `response` (
  `responseid` bigint(20) NOT NULL AUTO_INCREMENT,
  `responsetitleref` varchar(50) NOT NULL,
  `createdby` bigint(20) NOT NULL,
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`responseid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `responseprop`
--

DROP TABLE IF EXISTS `responseprop`;
CREATE TABLE IF NOT EXISTS `responseprop` (
  `responsepropid` bigint(20) NOT NULL AUTO_INCREMENT,
  `responseid` bigint(20) NOT NULL,
  `responsepropname` varchar(20) NOT NULL,
  `responsepropvalue` text NOT NULL,
  PRIMARY KEY (`responsepropid`),
  KEY `response_fk_constraint` (`responseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `defaultlang` varchar(2) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `userprops`
--

DROP TABLE IF EXISTS `userprops`;
CREATE TABLE IF NOT EXISTS `userprops` (
  `propid` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `propname` varchar(100) NOT NULL,
  `propvalue` text NOT NULL,
  PRIMARY KEY (`propid`),
  KEY `userprop_fk_constraint` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questionprop`
--
ALTER TABLE `questionprop`
  ADD CONSTRAINT `new_fk_constraint` FOREIGN KEY (`questionid`) REFERENCES `question` (`questionid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questionresponse`
--
ALTER TABLE `questionresponse`
  ADD CONSTRAINT `qr_response_fk_constraint` FOREIGN KEY (`responseid`) REFERENCES `response` (`responseid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `qr_question_fk_constraint` FOREIGN KEY (`questionid`) REFERENCES `question` (`questionid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizattemptresponse`
--
ALTER TABLE `quizattemptresponse`
  ADD CONSTRAINT `qar_quizattempt_fk_constraint` FOREIGN KEY (`qaid`) REFERENCES `quizattempt` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizprop`
--
ALTER TABLE `quizprop`
  ADD CONSTRAINT `new_fk_constraint2` FOREIGN KEY (`quizid`) REFERENCES `quiz` (`quizid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizquestion`
--
ALTER TABLE `quizquestion`
  ADD CONSTRAINT `qq_question_fk_constraint` FOREIGN KEY (`questionid`) REFERENCES `question` (`questionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `qq_quiz_fk_constraint` FOREIGN KEY (`quizid`) REFERENCES `quiz` (`quizid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `responseprop`
--
ALTER TABLE `responseprop`
  ADD CONSTRAINT `response_fk_constraint` FOREIGN KEY (`responseid`) REFERENCES `response` (`responseid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userprops`
--
ALTER TABLE `userprops`
  ADD CONSTRAINT `userprop_fk_constraint` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;