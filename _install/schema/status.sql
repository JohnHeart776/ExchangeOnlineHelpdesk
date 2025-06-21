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
-- Table structure for table `Status`
--

DROP TABLE IF EXISTS `Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Status` (
  `StatusId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `InternalName` varchar(100) NOT NULL,
  `Color` varchar(50) DEFAULT NULL,
  `PublicName` varchar(100) NOT NULL,
  `Icon` varchar(100) DEFAULT NULL,
  `IsOpen` int(11) DEFAULT NULL,
  `IsFinal` int(11) DEFAULT NULL,
  `IsDefault` int(11) DEFAULT NULL,
  `IsDefaultAssignedStatus` int(11) DEFAULT NULL,
  `IsDefaultWorkingStatus` int(11) DEFAULT NULL,
  `IsDetaultWaitingForCustomerStatus` int(11) DEFAULT NULL,
  `IsDefaultCustomerReplyStatus` int(11) DEFAULT NULL,
  `IsDefaultClosedStatus` int(11) DEFAULT NULL,
  `IsDefaultSolvedStatus` int(11) DEFAULT NULL,
  `SortOrder` int(11) DEFAULT 0,
  `CustomerNotificationTemplateId` int(11) DEFAULT NULL,
  `AgentNotificationTemplateId` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`StatusId`),
  UNIQUE KEY `guid` (`Guid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Status`
--

LOCK TABLES `Status` WRITE;
/*!40000 ALTER TABLE `Status` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `Status` VALUES
(1,'da1cf1ca-0c81-11f0-9b57-020017001e9a','open','primary','Open','fa-folder-open',1,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,10,0,0,'2025-03-29 10:40:29','2025-04-29 21:29:53'),
(3,'da1cf59c-0c81-11f0-9b57-020017001e9a','assigned','info','Assigned','fa-user-check',1,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,20,5,24,'2025-03-29 10:40:29','2025-05-04 18:17:20'),
(4,'da1cf5db-0c81-11f0-9b57-020017001e9a','working','success','Working','fa-spinner fa-pulse',1,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,30,NULL,NULL,'2025-03-29 10:40:29','2025-05-10 10:50:32'),
(5,'da1cf606-0c81-11f0-9b57-020017001e9a','waiting_customer','warning','Waiting for Customer','fa-user-clock',1,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,50,NULL,NULL,'2025-03-29 10:40:29','2025-05-17 18:01:29'),
(6,'da1cf633-0c81-11f0-9b57-020017001e9a','waiting_internal','warning','internally waiting','fa-users-gear',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,60,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29'),
(7,'da1cf65b-0c81-11f0-9b57-020017001e9a','on_hold','dark','Postponed','fa-pause-circle',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,70,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29'),
(8,'3e50c28c-5681-4c04-bd3a-8439d0936241','reply_customer','warning','Customer-Answer','fa-beat fa-reply',1,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,40,NULL,26,'2025-05-14 23:40:59','2025-06-19 14:15:48'),
(10,'da1cf6d8-0c81-11f0-9b57-020017001e9a','resolved','success','Solved','fa-circle-check',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,1,80,NULL,NULL,'2025-03-29 10:40:29','2025-06-04 09:46:47'),
(12,'da1cf725-0c81-11f0-9b57-020017001e9a','closed','tertiary','Closed','fa-box-archive',NULL,1,NULL,NULL,NULL,NULL,NULL,1,NULL,100,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29'),
(13,'da1cf74e-0c81-11f0-9b57-020017001e9a','reopened','primary','Reopened','fa-redo',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,110,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29'),
(16,'da1cf7c3-0c81-11f0-9b57-020017001e9a','duplicate','tertiary','Duplicate','fa-copy',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,120,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29'),
(17,'da1cf7e9-0c81-11f0-9b57-020017001e9a','escalated','danger','Escalated','fa-arrow-up-right-dots',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,130,NULL,NULL,'2025-03-29 10:40:29','2025-05-15 00:17:29');
/*!40000 ALTER TABLE `Status` ENABLE KEYS */;
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

-- Dump completed on 2025-06-21 14:23:00
