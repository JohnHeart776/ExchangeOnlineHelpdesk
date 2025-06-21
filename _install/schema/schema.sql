/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: dev_ticketharbor_net
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
-- Table structure for table `ActionGroup`
--

DROP TABLE IF EXISTS `ActionGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ActionGroup` (
  `ActionGroupId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` char(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `SortOrder` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ActionGroupId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ActionItem`
--

DROP TABLE IF EXISTS `ActionItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ActionItem` (
  `ActionItemId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` char(36) NOT NULL,
  `ActionGroupId` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `IsOptional` tinyint(1) DEFAULT 0,
  `SortOrder` int(11) DEFAULT 0,
  PRIMARY KEY (`ActionItemId`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AiCache`
--

DROP TABLE IF EXISTS `AiCache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AiCache` (
  `AiCacheId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Payload` longtext DEFAULT NULL,
  `PayloadHash` varchar(255) DEFAULT NULL,
  `Response` longtext DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`AiCacheId`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Article`
--

DROP TABLE IF EXISTS `Article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Article` (
  `ArticleId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Published` int(11) DEFAULT NULL,
  `AccessLevel` enum('Public','Agent') DEFAULT NULL,
  `CreatedDatetime` datetime DEFAULT current_timestamp(),
  `UpdatedAtDatetime` datetime DEFAULT current_timestamp(),
  `Slug` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Content` longtext DEFAULT NULL,
  PRIMARY KEY (`ArticleId`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `CategorySuggestion`
--

DROP TABLE IF EXISTS `CategorySuggestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CategorySuggestion` (
  `CategorySuggestionId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `Enabled` tinyint(1) NOT NULL DEFAULT 1,
  `Priority` int(11) DEFAULT NULL,
  `Filter` varchar(255) NOT NULL,
  `CategoryId` int(11) NOT NULL,
  `AutoClose` int(11) DEFAULT NULL,
  PRIMARY KEY (`CategorySuggestionId`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_insert_ticketcategorysuggestion`
    BEFORE INSERT ON `CategorySuggestion` FOR EACH ROW
BEGIN
    IF NEW.guid IS NULL OR NEW.guid = '' THEN
        SET NEW.guid = UUID();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Config`
--

DROP TABLE IF EXISTS `Config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Config` (
  `ConfigId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Value` longtext DEFAULT NULL,
  PRIMARY KEY (`ConfigId`),
  UNIQUE KEY `name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER config_before_insert
    BEFORE INSERT ON `Config` FOR EACH ROW
BEGIN
    IF NEW.guid IS NULL OR NEW.guid = '' THEN
        SET NEW.guid = UUID();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `File`
--

DROP TABLE IF EXISTS `File`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `File` (
  `FileId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Secret1` varchar(255) DEFAULT NULL,
  `Secret2` varchar(255) DEFAULT NULL,
  `Secret3` varchar(255) DEFAULT NULL,
  `HashSha256` varchar(255) DEFAULT NULL,
  `CreatedDatetime` datetime DEFAULT current_timestamp(),
  `Name` varchar(255) DEFAULT NULL,
  `Size` int(11) DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Data` longblob DEFAULT NULL,
  PRIMARY KEY (`FileId`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `LogMailSent`
--

DROP TABLE IF EXISTS `LogMailSent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `LogMailSent` (
  `LogMailSentId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Recipient` varchar(255) DEFAULT NULL,
  `Subject` longtext DEFAULT NULL,
  `Body` longtext DEFAULT NULL,
  `Created` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`LogMailSentId`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mail`
--

DROP TABLE IF EXISTS `Mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mail` (
  `MailId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `SecureObjectHash` varchar(255) DEFAULT NULL,
  `SourceMailbox` varchar(255) DEFAULT NULL,
  `AzureId` varchar(255) DEFAULT NULL,
  `MessageId` varchar(255) NOT NULL,
  `TicketId` int(11) DEFAULT NULL,
  `Subject` text DEFAULT NULL,
  `SenderName` varchar(255) DEFAULT NULL,
  `SenderEmail` varchar(255) DEFAULT NULL,
  `FromName` varchar(255) DEFAULT NULL,
  `FromEmail` varchar(255) DEFAULT NULL,
  `ToRecipients` text DEFAULT NULL,
  `CcRecipients` text DEFAULT NULL,
  `BccRecipients` text DEFAULT NULL,
  `Body` longtext DEFAULT NULL,
  `BodyType` varchar(20) DEFAULT NULL,
  `ReceivedDatetime` datetime DEFAULT NULL,
  `SentDatetime` datetime DEFAULT NULL,
  `Importance` varchar(50) DEFAULT NULL,
  `ConversationId` varchar(255) NOT NULL,
  `BodyPreview` text DEFAULT NULL,
  `HasAttachments` tinyint(1) DEFAULT NULL,
  `MailHeadersRaw` text DEFAULT NULL,
  `HeadersJson` text DEFAULT NULL,
  `AzureObject` longtext DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MailId`),
  UNIQUE KEY `guid` (`Guid`),
  KEY `Mail_Subject_index` (`Subject`(768))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MailAttachment`
--

DROP TABLE IF EXISTS `MailAttachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MailAttachment` (
  `MailAttachmentId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `AzureId` varchar(255) DEFAULT NULL,
  `Secret1` varchar(255) DEFAULT NULL,
  `Secret2` varchar(255) DEFAULT NULL,
  `Secret3` varchar(255) DEFAULT NULL,
  `MailId` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `ContentType` varchar(255) DEFAULT NULL,
  `Size` int(11) DEFAULT NULL,
  `IsInline` tinyint(1) DEFAULT NULL,
  `HashSha256` varchar(255) DEFAULT NULL,
  `Content` longblob DEFAULT NULL,
  `TextRepresentation` longtext DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MailAttachmentId`),
  UNIQUE KEY `guid` (`Guid`),
  KEY `idx_mail_guid` (`MailId`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MailAttachmentIgnore`
--

DROP TABLE IF EXISTS `MailAttachmentIgnore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MailAttachmentIgnore` (
  `MailAttachmentIgnoreId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Enabled` int(11) DEFAULT NULL,
  `HashSha256` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`MailAttachmentIgnoreId`),
  UNIQUE KEY `MailAttachmentIgnore_Guid_uindex` (`Guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Menu`
--

DROP TABLE IF EXISTS `Menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Menu` (
  `MenuId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Enabled` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `SortNum` int(11) DEFAULT NULL,
  PRIMARY KEY (`MenuId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MenuItem`
--

DROP TABLE IF EXISTS `MenuItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MenuItem` (
  `MenuItemId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `MenuId` int(11) DEFAULT NULL,
  `ParentMenuItemId` int(11) DEFAULT NULL,
  `SortOrder` int(11) DEFAULT NULL,
  `Enabled` int(11) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Link` varchar(255) DEFAULT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `Color` varchar(255) DEFAULT NULL,
  `ImageFileId` int(11) DEFAULT NULL,
  `requireIsUser` int(11) DEFAULT NULL,
  `requireIsAgent` int(11) DEFAULT NULL,
  `requireIsAdmin` int(11) DEFAULT NULL,
  PRIMARY KEY (`MenuItemId`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `NotificationTemplate`
--

DROP TABLE IF EXISTS `NotificationTemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `NotificationTemplate` (
  `NotificationTemplateId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` char(36) NOT NULL,
  `Enabled` int(1) DEFAULT 1,
  `InternalName` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `MailSubject` varchar(255) NOT NULL,
  `MailText` text NOT NULL,
  PRIMARY KEY (`NotificationTemplateId`),
  UNIQUE KEY `NotificationTemplate_pk` (`InternalName`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OrganizationUser`
--

DROP TABLE IF EXISTS `OrganizationUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `OrganizationUser` (
  `OrganizationUserId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `AzureObjectId` varchar(255) NOT NULL,
  `DisplayName` varchar(255) DEFAULT NULL,
  `UserPrincipalName` varchar(255) DEFAULT NULL,
  `Mail` varchar(255) DEFAULT NULL,
  `GivenName` varchar(255) DEFAULT NULL,
  `Surname` varchar(255) DEFAULT NULL,
  `JobTitle` varchar(255) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `MobilePhone` varchar(255) DEFAULT NULL,
  `OfficeLocation` varchar(255) DEFAULT NULL,
  `CompanyName` varchar(255) DEFAULT NULL,
  `BusinessPhones` text DEFAULT NULL,
  `AccountEnabled` tinyint(1) DEFAULT NULL,
  `EmployeeId` varchar(255) DEFAULT NULL,
  `SamAccountName` varchar(255) DEFAULT NULL,
  `Photo` mediumblob DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`OrganizationUserId`),
  UNIQUE KEY `guid` (`Guid`),
  UNIQUE KEY `azure_id` (`AzureObjectId`)
) ENGINE=InnoDB AUTO_INCREMENT=1725 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `TemplateText`
--

DROP TABLE IF EXISTS `TemplateText`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TemplateText` (
  `TemplateTextId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Content` longtext DEFAULT NULL,
  `CreatedDatetime` datetime DEFAULT current_timestamp(),
  UNIQUE KEY `TemplateText_pk` (`TemplateTextId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TextReplace`
--

DROP TABLE IF EXISTS `TextReplace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TextReplace` (
  `TextReplaceId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `Enabled` tinyint(1) DEFAULT 1,
  `SearchFor` text NOT NULL,
  `ReplaceBy` text DEFAULT NULL,
  PRIMARY KEY (`TextReplaceId`),
  UNIQUE KEY `guid` (`Guid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER before_insert_textreplace
    BEFORE INSERT ON `TextReplace` FOR EACH ROW
BEGIN
    IF NEW.guid IS NULL OR CHAR_LENGTH(TRIM(NEW.guid)) = 0 THEN
        SET NEW.guid = UUID();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Ticket`
--

DROP TABLE IF EXISTS `Ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ticket` (
  `TicketId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `Secret1` varchar(255) DEFAULT NULL,
  `Secret2` varchar(255) DEFAULT NULL,
  `Secret3` varchar(255) DEFAULT NULL,
  `TicketNumber` char(10) NOT NULL,
  `ConversationId` varchar(255) NOT NULL,
  `StatusId` int(11) DEFAULT NULL,
  `MessengerName` varchar(255) DEFAULT NULL,
  `MessengerEmail` varchar(255) DEFAULT NULL,
  `Subject` varchar(500) DEFAULT NULL,
  `CategoryId` int(11) NOT NULL,
  `AssigneeUserId` int(11) DEFAULT NULL,
  `DueDatetime` datetime DEFAULT NULL,
  `CreatedDatetime` datetime DEFAULT current_timestamp(),
  `UpdatedDatetime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`TicketId`),
  UNIQUE KEY `guid` (`Guid`),
  UNIQUE KEY `ticket_number` (`TicketNumber`),
  KEY `category_id` (`CategoryId`),
  KEY `assignee` (`AssigneeUserId`),
  KEY `Ticket_Subject_index` (`Subject`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TicketActionItem`
--

DROP TABLE IF EXISTS `TicketActionItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TicketActionItem` (
  `TicketActionItemId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` char(36) NOT NULL,
  `TicketId` int(11) NOT NULL,
  `ActionItemId` int(11) DEFAULT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `DueDatetime` datetime DEFAULT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `Completed` tinyint(1) DEFAULT 0,
  `CompletedByUserId` int(11) DEFAULT NULL,
  `CompletedAt` datetime DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `CreatedByUserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`TicketActionItemId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TicketAssociate`
--

DROP TABLE IF EXISTS `TicketAssociate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TicketAssociate` (
  `TicketAssociateId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `TicketId` int(11) DEFAULT NULL,
  `OrganizationUserId` int(11) DEFAULT NULL,
  PRIMARY KEY (`TicketAssociateId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TicketComment`
--

DROP TABLE IF EXISTS `TicketComment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TicketComment` (
  `TicketCommentId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `TicketId` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `AccessLevel` enum('Public','Internal') DEFAULT 'Internal',
  `CreatedDatetime` datetime DEFAULT current_timestamp(),
  `LastUpdatedDatetime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Facility` enum('system','automatic','user','other') DEFAULT 'system',
  `TextType` enum('txt','html') DEFAULT 'txt',
  `Text` longtext NOT NULL,
  `MailId` int(11) DEFAULT NULL,
  `GraphObject` longtext DEFAULT NULL,
  `IsEditable` int(11) DEFAULT NULL,
  PRIMARY KEY (`TicketCommentId`),
  UNIQUE KEY `guid` (`Guid`),
  KEY `ticketId` (`TicketId`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TicketFile`
--

DROP TABLE IF EXISTS `TicketFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TicketFile` (
  `TicketFileId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) DEFAULT NULL,
  `TicketId` int(11) DEFAULT NULL,
  `FileId` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `CreatedDatetime` datetime DEFAULT NULL,
  `AccessLevel` enum('Public','Internal') DEFAULT NULL,
  PRIMARY KEY (`TicketFileId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TicketStatus`
--

DROP TABLE IF EXISTS `TicketStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TicketStatus` (
  `TicketStatusId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `CreatedDatetime` datetime NOT NULL DEFAULT current_timestamp(),
  `TicketId` int(11) NOT NULL,
  `OldStatusId` int(11) DEFAULT NULL,
  `OldStatusIdIsFinal` int(11) DEFAULT NULL,
  `NewStatusId` int(11) NOT NULL,
  `NewStatusIdIsFinal` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Comment` text DEFAULT NULL,
  PRIMARY KEY (`TicketStatusId`),
  KEY `fk_ticketstatus_ticket` (`TicketId`),
  KEY `fk_ticketstatus_status` (`NewStatusId`),
  KEY `fk_ticketstatus_user` (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER before_insert_ticketstatus
    BEFORE INSERT ON `TicketStatus` FOR EACH ROW
BEGIN
    IF NEW.guid IS NULL OR NEW.guid = '' THEN
        SET NEW.guid = UUID();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `Enabled` tinyint(1) DEFAULT 1,
  `TenantId` varchar(255) NOT NULL,
  `AzureObjectId` varchar(255) NOT NULL,
  `Upn` varchar(255) NOT NULL,
  `DisplayName` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Surname` varchar(255) DEFAULT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Mail` varchar(255) DEFAULT NULL,
  `Telephone` varchar(255) DEFAULT NULL,
  `OfficeLocation` varchar(255) DEFAULT NULL,
  `CompanyName` varchar(255) DEFAULT NULL,
  `MobilePhone` varchar(255) DEFAULT NULL,
  `BusinessPhones` text DEFAULT NULL,
  `AccountEnabled` tinyint(1) DEFAULT 1,
  `UserRole` enum('guest','user','agent','admin') DEFAULT 'guest',
  `LastLogin` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `guid_unique` (`Guid`),
  UNIQUE KEY `upn_unique` (`Upn`),
  UNIQUE KEY `user_azure_object_id_tenant_id_uindex` (`AzureObjectId`,`TenantId`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserImage`
--

DROP TABLE IF EXISTS `UserImage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `UserImage` (
  `UserImageId` int(11) NOT NULL AUTO_INCREMENT,
  `Guid` varchar(255) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Base64Image` mediumtext NOT NULL,
  `LastUpdated` datetime NOT NULL,
  PRIMARY KEY (`UserImageId`),
  UNIQUE KEY `userId` (`UserId`),
  KEY `guid` (`Guid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-06-21 14:22:42
