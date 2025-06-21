/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: EOTS
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB-deb12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Category` (
  `CategoryId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `InternalName` varchar(100) NOT NULL,
  `PublicName` varchar(255) NOT NULL,
  `Icon` varchar(100) DEFAULT NULL,
  `Color` varchar(50) DEFAULT NULL,
  `IsDefault` tinyint(1) DEFAULT 0,
  `SortOrder` int(11) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`CategoryId`),
  UNIQUE KEY `guid` (`Guid`),
  UNIQUE KEY `internalName` (`InternalName`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `Category` VALUES
VALUES (1, '17e597fc-0c82-11f0-9b57-020017001e9a', 'application', 'Applications', 'fa-window-restore', 'primary', 0, 10, '2025-03-29 10:42:13'),
    (2, '17e59aa1-0c82-11f0-9b57-020017001e9a', 'hardware', 'Hardware', 'fa-desktop', 'dark', 0, 20, '2025-03-29 10:42:13'),
    (3, '17e59bec-0c82-11f0-9b57-020017001e9a', 'network', 'Network', 'fa-network-wired', 'info', 0, 30, '2025-03-29 10:42:13'),
    (4, '17e59c42-0c82-11f0-9b57-020017001e9a', 'server', 'Server & Infrastructure', 'fa-server', 'dark', 0, 40, '2025-03-29 10:42:13'),
    (5, '17e59c7e-0c82-11f0-9b57-020017001e9a', 'security', 'Security', 'fa-shield-halved', 'primary', 0, 50, '2025-03-29 10:42:13'),
    (6, '17e59cba-0c82-11f0-9b57-020017001e9a', 'access', 'Permissions & Access', 'fa-key', 'warning', 0, 60, '2025-03-29 10:42:13'),
    (7, '17e59cf4-0c82-11f0-9b57-020017001e9a', 'email', 'Email & Communication', 'fa-envelope', 'success', 0, 70, '2025-03-29 10:42:13'),
    (8, '17e59d2a-0c82-11f0-9b57-020017001e9a', 'cloud', 'Cloud & SaaS', 'fa-cloud', 'info', 0, 80, '2025-03-29 10:42:13'),
    (9, '17e59d62-0c82-11f0-9b57-020017001e9a', 'backup', 'Backup & Recovery', 'fa-database', 'dark', 0, 90, '2025-03-29 10:42:13'),
    (10, '17e59d95-0c82-11f0-9b57-020017001e9a', 'software_deployment', 'Software Deployment', 'fa-download', 'primary', 0, 100, '2025-03-29 10:42:13'),
    (11, '17e59dc9-0c82-11f0-9b57-020017001e9a', 'user_account', 'User Accounts', 'fa-user', 'primary', 0, 110, '2025-03-29 10:42:13'),
    (12, '17e59dfd-0c82-11f0-9b57-020017001e9a', 'printing', 'Print & Scan', 'fa-print', 'dark', 0, 120, '2025-03-29 10:42:13'),
    (13, '17e59e38-0c82-11f0-9b57-020017001e9a', 'telephony', 'Telephony & VoIP', 'fa-phone', 'info', 0, 130, '2025-03-29 10:42:13'),
    (14, '17e59e6f-0c82-11f0-9b57-020017001e9a', 'mobile', 'Mobile Devices', 'fa-mobile-screen', 'dark', 0, 140, '2025-03-29 10:42:13'),
    (15, '17e59ea5-0c82-11f0-9b57-020017001e9a', 'web', 'Web Applications', 'fa-globe', 'primary', 0, 150, '2025-03-29 10:42:13'),
    (16, '17e59edb-0c82-11f0-9b57-020017001e9a', 'erp', 'ERP & Accounting', 'fa-chart-line', 'warning', 0, 160, '2025-03-29 10:42:13'),
    (17, '17e59f1c-0c82-11f0-9b57-020017001e9a', 'reporting', 'Reporting & BI', 'fa-chart-bar', 'success', 0, 170, '2025-03-29 10:42:13'),
    (18, '17e59f7d-0c82-11f0-9b57-020017001e9a', 'other', 'Other', 'fa-star', 'primary', 0, 999, '2025-03-29 10:42:13'),
    (19, '5d745b6e-0d3f-11f0-9b57-020017001e9a', 'default', 'General', 'fa-circle-question', 'primary', 1, 0, '2025-03-30 10:17:05'),
    (20, '441c65b8-0d64-11f0-9b57-020017001e9a', 'serverupdates_linux', 'Server Updates - Linux', 'fa-server', 'danger', 0, 100, '2025-03-30 14:41:13'),
    (21, '441c68da-0d64-11f0-9b57-020017001e9a', 'serverupdates_windows', 'Server Updates - Windows', 'fa-server', 'danger', 0, 101, '2025-03-30 14:41:13'),
    (22, '441c69ec-0d64-11f0-9b57-020017001e9a', 'spam', 'Spam', 'fa-spaghetti-monster-flying', 'primary', 0, 102, '2025-03-30 14:41:13'),
    (23, '7e4059e4-4ba5-11f0-9a62-00155d893a24', 'security_incident', 'Security Incident', NULL, NULL, 0, 29, '2025-06-17 20:04:26'),
    (24, '8c51f4ca-4ba5-11f0-9a62-00155d893a24', 'security_radius', 'Security Radius', NULL, NULL, 0, 29, '2025-06-17 20:04:50');
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-06-21 14:23:09
