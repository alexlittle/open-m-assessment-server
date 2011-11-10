-- phpMyAdmin SQL Dump
-- version 3.4.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 10, 2011 at 05:15 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: 'assessment'
--

-- --------------------------------------------------------

--
-- Table structure for table 'language'
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  langid bigint(20) NOT NULL AUTO_INCREMENT,
  langref varchar(20) NOT NULL,
  langtext text NOT NULL,
  langcode varchar(10) NOT NULL,
  PRIMARY KEY (langid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'question'
--

DROP TABLE IF EXISTS question;
CREATE TABLE IF NOT EXISTS question (
  questionid bigint(20) NOT NULL AUTO_INCREMENT,
  questiontitleref varchar(20) NOT NULL,
  PRIMARY KEY (questionid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'questionprop'
--

DROP TABLE IF EXISTS questionprop;
CREATE TABLE IF NOT EXISTS questionprop (
  questionpropid bigint(20) NOT NULL AUTO_INCREMENT,
  questionid bigint(20) NOT NULL,
  questionpropname varchar(20) NOT NULL,
  questionpropvalue text NOT NULL,
  PRIMARY KEY (questionpropid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'questionresponse'
--

DROP TABLE IF EXISTS questionresponse;
CREATE TABLE IF NOT EXISTS questionresponse (
  questionid bigint(20) NOT NULL,
  responseid bigint(20) NOT NULL,
  orderno int(11) NOT NULL,
  PRIMARY KEY (orderno,responseid,questionid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'quiz'
--

DROP TABLE IF EXISTS quiz;
CREATE TABLE IF NOT EXISTS quiz (
  quizid bigint(20) NOT NULL AUTO_INCREMENT,
  quiztitleref varchar(20) NOT NULL,
  PRIMARY KEY (quizid) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quizattempt'
--

DROP TABLE IF EXISTS quizattempt;
CREATE TABLE IF NOT EXISTS quizattempt (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  quizref varchar(200) CHARACTER SET latin1 NOT NULL,
  qadate bigint(20) NOT NULL,
  qascore int(11) NOT NULL,
  qauser varchar(200) CHARACTER SET latin1 NOT NULL,
  submitdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  submituser varchar(200) CHARACTER SET latin1 NOT NULL,
  maxscore int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quizattemptresponse'
--

DROP TABLE IF EXISTS quizattemptresponse;
CREATE TABLE IF NOT EXISTS quizattemptresponse (
  qarid bigint(20) NOT NULL AUTO_INCREMENT,
  qaid bigint(20) NOT NULL,
  responserefid varchar(200) NOT NULL,
  questionrefid varchar(200) NOT NULL,
  qarscore int(11) NOT NULL,
  PRIMARY KEY (qarid)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quizprop'
--

DROP TABLE IF EXISTS quizprop;
CREATE TABLE IF NOT EXISTS quizprop (
  quizpropid bigint(20) NOT NULL AUTO_INCREMENT,
  quizid bigint(20) NOT NULL,
  quizpropname varchar(20) NOT NULL,
  quizpropvalue text NOT NULL,
  PRIMARY KEY (quizpropid) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quizquestion'
--

DROP TABLE IF EXISTS quizquestion;
CREATE TABLE IF NOT EXISTS quizquestion (
  quizid bigint(20) NOT NULL,
  questionid bigint(20) NOT NULL,
  orderno int(11) NOT NULL,
  PRIMARY KEY (quizid,questionid,orderno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'response'
--

DROP TABLE IF EXISTS response;
CREATE TABLE IF NOT EXISTS response (
  responseid bigint(20) NOT NULL AUTO_INCREMENT,
  responsetitleref varchar(20) NOT NULL,
  PRIMARY KEY (responseid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'responseprop'
--

DROP TABLE IF EXISTS responseprop;
CREATE TABLE IF NOT EXISTS responseprop (
  responsepropid bigint(20) NOT NULL AUTO_INCREMENT,
  responseid bigint(20) NOT NULL,
  responsepropname varchar(20) NOT NULL,
  responsepropvalue text NOT NULL,
  PRIMARY KEY (responsepropid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;