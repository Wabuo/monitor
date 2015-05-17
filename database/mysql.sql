-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hiawatha_monitor
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.12.04.1

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `timeout` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cgi_statistics`
--

DROP TABLE IF EXISTS `cgi_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cgi_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp_begin` datetime NOT NULL,
  `timestamp_end` datetime NOT NULL,
  `webserver_id` int(11) unsigned NOT NULL,
  `hostname_id` int(11) unsigned NOT NULL,
  `time_0_1` int(10) unsigned NOT NULL,
  `time_1_3` int(10) unsigned NOT NULL,
  `time_3_10` int(10) unsigned NOT NULL,
  `time_10_x` int(10) unsigned NOT NULL,
  `cgi_errors` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `webserver_id` (`webserver_id`),
  KEY `hostname_id` (`hostname_id`),
  CONSTRAINT `cgi_statistics_ibfk_1` FOREIGN KEY (`webserver_id`) REFERENCES `webservers` (`id`),
  CONSTRAINT `cgi_statistics_ibfk_2` FOREIGN KEY (`hostname_id`) REFERENCES `hostnames` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `webserver_id` int(10) unsigned NOT NULL,
  `event` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `webserver_id` (`webserver_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`webserver_id`) REFERENCES `webservers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `host_statistics`
--

DROP TABLE IF EXISTS `host_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp_begin` datetime NOT NULL,
  `timestamp_end` datetime NOT NULL,
  `webserver_id` int(10) unsigned NOT NULL,
  `hostname_id` int(10) unsigned NOT NULL,
  `requests` int(11) NOT NULL,
  `bytes_sent` bigint(20) NOT NULL,
  `bans` int(11) NOT NULL,
  `exploit_attempts` int(11) NOT NULL,
  `result_forbidden` int(11) NOT NULL,
  `result_not_found` int(11) NOT NULL,
  `result_internal_error` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `webserver_id` (`webserver_id`),
  KEY `hostname_id` (`hostname_id`),
  CONSTRAINT `host_statistics_ibfk_1` FOREIGN KEY (`webserver_id`) REFERENCES `webservers` (`id`),
  CONSTRAINT `host_statistics_ibfk_2` FOREIGN KEY (`hostname_id`) REFERENCES `hostnames` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hostnames`
--

DROP TABLE IF EXISTS `hostnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hostnames` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` tinytext NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `text` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,0,0,'Dashboard','/dashboard'),(2,0,0,'Request statistics','/request_statistics'),(3,0,0,'Security statistics','/security_statistics'),(4,0,0,'CGI statistics','/cgi_statistics'),(5,0,0,'Server statistics','/server_statistics'),(6,0,0,'Events','/events'),(7,0,0,'Logout','/logout');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_access`
--

DROP TABLE IF EXISTS `page_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_access` (
  `page_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  PRIMARY KEY (`page_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `page_access_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`),
  CONSTRAINT `page_access_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `language` varchar(2) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `style` text NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `profile` tinyint(4) DEFAULT '0',
  `admin` tinyint(4) DEFAULT '0',
  `admin/file` tinyint(4) DEFAULT '0',
  `admin/menu` tinyint(4) DEFAULT '0',
  `admin/page` tinyint(4) DEFAULT '0',
  `admin/role` tinyint(4) DEFAULT '0',
  `admin/switch` tinyint(4) DEFAULT '0',
  `admin/user` tinyint(4) DEFAULT '0',
  `admin/access` tinyint(4) DEFAULT '0',
  `admin/action` tinyint(4) DEFAULT '0',
  `admin/webserver` tinyint(4) DEFAULT '0',
  `server_statistics` tinyint(4) DEFAULT '0',
  `admin/hostname` tinyint(4) DEFAULT '0',
  `admin/settings` tinyint(4) DEFAULT '0',
  `session` tinyint(4) DEFAULT '0',
  `events` tinyint(4) DEFAULT '0',
  `cgi_statistics` tinyint(4) DEFAULT '0',
  `security_statistics` tinyint(4) DEFAULT '0',
  `request_statistics` tinyint(4) DEFAULT '0',
  `dashboard` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),(2,'Webmaster',1,0,0,0,0,0,0,0,0,0,0,1,0,0,1,1,1,1,1,1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `server_statistics`
--

DROP TABLE IF EXISTS `server_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server_statistics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp_begin` datetime NOT NULL,
  `timestamp_end` datetime NOT NULL,
  `webserver_id` int(10) unsigned NOT NULL,
  `connections` int(10) unsigned NOT NULL,
  `result_bad_request` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `webserver_id` (`webserver_id`),
  CONSTRAINT `server_statistics_ibfk_1` FOREIGN KEY (`webserver_id`) REFERENCES `webservers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) NOT NULL,
  `content` text,
  `expire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL,
  `name` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `type` varchar(8) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'admin_page_size','integer','25'),(2,'page_after_login','string','dashboard'),(3,'start_page','string','dashboard'),(4,'webmaster_email','string','root@localhost'),(5,'head_title','string','Hiawatha Monitor'),(6,'head_description','string','Security and performance monitoring tool for the Hiawatha webserver.'),(7,'head_keywords','string','monitor, hiawatha'),(34,'default_language','string','en'),(38,'event_page_size','integer','25'),(39,'top_connections','integer','15');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `password` varchar(35) NOT NULL DEFAULT '',
  `one_time_key` varchar(50) DEFAULT NULL,
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `fullname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `prowl_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','08b5411f848a2581a41672a759c87380',NULL,1,'Administrator','root@localhost','');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webserver_user`
--

DROP TABLE IF EXISTS `webserver_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webserver_user` (
  `webserver_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `webserver_id` (`webserver_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `webserver_user_ibfk_1` FOREIGN KEY (`webserver_id`) REFERENCES `webservers` (`id`),
  CONSTRAINT `webserver_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webservers`
--

DROP TABLE IF EXISTS `webservers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webservers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `ip_address` varchar(40) NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `ssl` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `errors` int(11) NOT NULL,
  `version` tinytext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-01  8:23:08
