-- MySQL dump 10.11
--
-- Host: localhost    Database: vote
-- ------------------------------------------------------
-- Server version	5.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `BULLETIN`
--

DROP TABLE IF EXISTS `BULLETIN`;
CREATE TABLE `BULLETIN` (
  `BULLETIN_Id` int(11) NOT NULL auto_increment,
  `BULLETIN_VoteId` int(11) NOT NULL,
  `BULLETIN_Choix` varchar(32) NOT NULL,
  PRIMARY KEY  (`BULLETIN_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3759 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `BULLETIN`
--

LOCK TABLES `BULLETIN` WRITE;
/*!40000 ALTER TABLE `BULLETIN` DISABLE KEYS */;
/*!40000 ALTER TABLE `BULLETIN` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EMARGEMENT`
--

DROP TABLE IF EXISTS `EMARGEMENT`;
CREATE TABLE `EMARGEMENT` (
  `EMARGEMENT_GEId` int(11) NOT NULL,
  `EMARGEMENT_VoteId` int(11) NOT NULL,
  `EMARGEMENT_Procuration` int(11) default NULL,
  PRIMARY KEY  (`EMARGEMENT_GEId`,`EMARGEMENT_VoteId`,`EMARGEMENT_Procuration`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `EMARGEMENT`
--

LOCK TABLES `EMARGEMENT` WRITE;
/*!40000 ALTER TABLE `EMARGEMENT` DISABLE KEYS */;
/*!40000 ALTER TABLE `EMARGEMENT` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ERROR`
--

DROP TABLE IF EXISTS `ERROR`;
CREATE TABLE `ERROR` (
  `ERROR_Id` int(11) NOT NULL auto_increment,
  `ERROR_Login` varchar(64) default NULL,
  `ERROR_Mdp` varchar(64) default NULL,
  `ERROR_Date` datetime NOT NULL,
  PRIMARY KEY  (`ERROR_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ERROR`
--

LOCK TABLES `ERROR` WRITE;
/*!40000 ALTER TABLE `ERROR` DISABLE KEYS */;
/*!40000 ALTER TABLE `ERROR` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GE`
--

DROP TABLE IF EXISTS `GE`;
CREATE TABLE `GE` (
  `GE_Id` int(11) NOT NULL auto_increment,
  `GE_Nom` varchar(32) default NULL,
  `GE_Prenom` varchar(32) default NULL,
  `GE_Mail` varchar(64) default NULL,
  `GE_NumFFS` varchar(128) default NULL,
  `GE_Titre` varchar(32) default NULL,
  `GE_MotDePasse` varchar(64) NOT NULL,
  PRIMARY KEY  (`GE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GE`
--

LOCK TABLES `GE` WRITE;
/*!40000 ALTER TABLE `GE` DISABLE KEYS */;
INSERT INTO `GE` VALUES (199,'Admin','','','F00-000-000','admin','7b7af5182284356f');
/*!40000 ALTER TABLE `GE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GE_LISTE`
--

DROP TABLE IF EXISTS `GE_LISTE`;
CREATE TABLE `GE_LISTE` (
  `GELISTE_Id` int(11) NOT NULL auto_increment,
  `GELISTE_GEId` int(11) NOT NULL,
  `GELISTE_ListeId` int(11) NOT NULL,
  PRIMARY KEY  (`GELISTE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GE_LISTE`
--

LOCK TABLES `GE_LISTE` WRITE;
/*!40000 ALTER TABLE `GE_LISTE` DISABLE KEYS */;
/*!40000 ALTER TABLE `GE_LISTE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LISTE`
--

DROP TABLE IF EXISTS `LISTE`;
CREATE TABLE `LISTE` (
  `LISTE_Id` int(11) NOT NULL auto_increment,
  `LISTE_Nom` varchar(32) default NULL,
  `LISTE_Options` varchar(16) default NULL,
  PRIMARY KEY  (`LISTE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `LISTE`
--

LOCK TABLES `LISTE` WRITE;
/*!40000 ALTER TABLE `LISTE` DISABLE KEYS */;
/*!40000 ALTER TABLE `LISTE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LOG`
--

DROP TABLE IF EXISTS `LOG`;
CREATE TABLE `LOG` (
  `LOG_GEId` int(11) NOT NULL,
  `LOG_LastLogin` datetime NOT NULL,
  `LOG_LastError` datetime NOT NULL,
  PRIMARY KEY  (`LOG_GEId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `LOG`
--

LOCK TABLES `LOG` WRITE;
/*!40000 ALTER TABLE `LOG` DISABLE KEYS */;
/*!40000 ALTER TABLE `LOG` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MANAGE`
--

DROP TABLE IF EXISTS `MANAGE`;
CREATE TABLE `MANAGE` (
  `MANAGE_Id` int(11) NOT NULL auto_increment,
  `MANAGE_OwnerId` int(11) NOT NULL,
  `MANAGE_GEId` int(11) NOT NULL,
  PRIMARY KEY  (`MANAGE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MANAGE`
--

LOCK TABLES `MANAGE` WRITE;
/*!40000 ALTER TABLE `MANAGE` DISABLE KEYS */;
/*!40000 ALTER TABLE `MANAGE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MESSAGE`
--

DROP TABLE IF EXISTS `MESSAGE`;
CREATE TABLE `MESSAGE` (
  `MESSAGE_Id` int(11) NOT NULL auto_increment,
  `MESSAGE_ReunionId` int(11) NOT NULL,
  `MESSAGE_Txt` varchar(1024) NOT NULL,
  `MESSAGE_Auteur` varchar(64) default NULL,
  `MESSAGE_Date` datetime NOT NULL,
  `MESSAGE_Status` int(8) NOT NULL,
  PRIMARY KEY  (`MESSAGE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MESSAGE`
--

LOCK TABLES `MESSAGE` WRITE;
/*!40000 ALTER TABLE `MESSAGE` DISABLE KEYS */;
/*!40000 ALTER TABLE `MESSAGE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OWNER`
--

DROP TABLE IF EXISTS `OWNER`;
CREATE TABLE `OWNER` (
  `OWNER_Id` int(11) NOT NULL auto_increment,
  `OWNER_GEId` int(11) NOT NULL,
  `OWNER_ListeId` int(11) NOT NULL,
  PRIMARY KEY  (`OWNER_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `OWNER`
--

LOCK TABLES `OWNER` WRITE;
/*!40000 ALTER TABLE `OWNER` DISABLE KEYS */;
/*!40000 ALTER TABLE `OWNER` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PROCURATION`
--

DROP TABLE IF EXISTS `PROCURATION`;
CREATE TABLE `PROCURATION` (
  `PROCURATION_Id` int(11) NOT NULL auto_increment,
  `PROCURATION_GESRCId` int(11) NOT NULL,
  `PROCURATION_GEDSTId` int(11) NOT NULL,
  `PROCURATION_ListeId` int(11) NOT NULL,
  PRIMARY KEY  (`PROCURATION_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PROCURATION`
--

LOCK TABLES `PROCURATION` WRITE;
/*!40000 ALTER TABLE `PROCURATION` DISABLE KEYS */;
/*!40000 ALTER TABLE `PROCURATION` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VOTE`
--

DROP TABLE IF EXISTS `VOTE`;
CREATE TABLE `VOTE` (
  `VOTE_Id` int(11) NOT NULL auto_increment,
  `VOTE_Titre` varchar(32) default NULL,
  `VOTE_Question` varchar(2048) default NULL,
  `VOTE_Reponses` varchar(2048) default NULL,
  `VOTE_Type` int(11) NOT NULL,
  `VOTE_Status` int(8) NOT NULL,
  `VOTE_ListeId` int(11) NOT NULL,
  `VOTE_Date` datetime NOT NULL,
  PRIMARY KEY  (`VOTE_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `VOTE`
--

LOCK TABLES `VOTE` WRITE;
/*!40000 ALTER TABLE `VOTE` DISABLE KEYS */;
/*!40000 ALTER TABLE `VOTE` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-06-01 12:45:52
