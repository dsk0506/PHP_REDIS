-- MySQL dump 10.13  Distrib 5.5.34, for osx10.6 (i386)
--
-- Host: localhost    Database: redis_weibo_demo
-- ------------------------------------------------------
-- Server version	5.5.34

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
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `to_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `wid` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `uid_1` (`uid`),
  KEY `uid_2` (`to_uid`),
  KEY `wid_2` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uname` char(20) NOT NULL DEFAULT '',
  `upass` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uname` (`uname`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (5,'LightPink','202cb962ac59075b964b07152d234b70'),(6,'Pink','202cb962ac59075b964b07152d234b70'),(7,'Crimson','202cb962ac59075b964b07152d234b70'),(8,'LavenderBlush','202cb962ac59075b964b07152d234b70'),(9,'PaleVioletRed','202cb962ac59075b964b07152d234b70'),(10,'HotPink','202cb962ac59075b964b07152d234b70'),(11,'DeepPink','202cb962ac59075b964b07152d234b70'),(12,'MediumVioletRed','202cb962ac59075b964b07152d234b70'),(13,'Orchid','202cb962ac59075b964b07152d234b70'),(14,'Red','202cb962ac59075b964b07152d234b70'),(15,'Blue','202cb962ac59075b964b07152d234b70'),(16,'Azure','202cb962ac59075b964b07152d234b70'),(17,'Thistle','202cb962ac59075b964b07152d234b70'),(18,'Plum','202cb962ac59075b964b07152d234b70'),(19,'Violet','202cb962ac59075b964b07152d234b70'),(20,'Magenta','202cb962ac59075b964b07152d234b70'),(21,'Fuchsia','202cb962ac59075b964b07152d234b70'),(22,'DarkMagenta','202cb962ac59075b964b07152d234b70'),(23,'Purple','202cb962ac59075b964b07152d234b70'),(24,'MediumOrchid','202cb962ac59075b964b07152d234b70');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weibo`
--

DROP TABLE IF EXISTS `weibo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weibo` (
  `wid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL DEFAULT '',
  `addtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`wid`),
  KEY `uid_1` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=53305 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weibo`
--

LOCK TABLES `weibo` WRITE;
/*!40000 ALTER TABLE `weibo` DISABLE KEYS */;
/*!40000 ALTER TABLE `weibo` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-19 14:28:14
