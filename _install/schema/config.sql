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

--
-- Dumping data for table `Config`
--

LOCK TABLES `Config` WRITE;
/*!40000 ALTER TABLE `Config` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `Config` VALUES
                         (1,'8e7b5f72-0d52-11f0-9b57-020017001e9a','tenantId',''),
                         (2,'8e7b6289-0d52-11f0-9b57-020017001e9a','application.clientId',''),
                         (3,'8e7b633f-0d52-11f0-9b57-020017001e9a','application.clientSecret',''),
                         (7,'8e7b6435-0d52-11f0-9b57-020017001e9a','source.mailbox',''),
                         (8,'8e7b6461-0d52-11f0-9b57-020017001e9a','log.dir','/var/www/html/log'),
                         (9,'8e7b648b-0d52-11f0-9b57-020017001e9a','log.retention','14'),
                         (10,'8e7b64b2-0d52-11f0-9b57-020017001e9a','user.clientId',''),
                         (11,'8e7b64d9-0d52-11f0-9b57-020017001e9a','user.clientSecret',''),
                         (12,'8e7b6503-0d52-11f0-9b57-020017001e9a','user.redirectUri','https://YOURDOAMIN/oauth/callback.php'),
                         (13,'8e7b652e-0d52-11f0-9b57-020017001e9a','user.oauthScopes','User.Read offline_access openid profile email'),
                         (14,'8e7b6553-0d52-11f0-9b57-020017001e9a','user.authUrl','https://login.microsoftonline.com/77099cf8-e4bb-4de3-8606-c5612dee8ccb/oauth2/v2.0/authorize'),
                         (15,'8e7b657e-0d52-11f0-9b57-020017001e9a','user.tokenUrl','https://login.microsoftonline.com/77099cf8-e4bb-4de3-8606-c5612dee8ccb/oauth2/v2.0/token'),
                         (17,'705845ab-1003-11f0-93ed-020017001e9a','application.certificate','-----BEGIN CERTIFICATE-----\n-----END CERTIFICATE-----'),
                         (18,'705d1682-1003-11f0-93ed-020017001e9a','application.certificateKey','-----BEGIN PRIVATE KEY-----\n-----END PRIVATE KEY-----'),
                         (19,'7d7218bc-100b-11f0-93ed-020017001e9a','application.certificateKeyPassword',NULL),
                         (20,'a0532bc0-11fb-11f0-8fd6-020017001e9a','site.title',''),
                         (21,'b42a7784-11fb-11f0-8fd6-020017001e9a','site.domain','YOURDOMAIN'),
                         (22,'7a3eabaa-11fd-11f0-8fd6-020017001e9a','site.name',''),
                         (23,'23c6fbd3-12bf-11f0-8fd6-020017001e9a','inscriptis.server',''),
                         (24,'2dfbdc2d-12bf-11f0-8fd6-020017001e9a','gotenberg.server',''),
                         (25,'3963f8c0-12bf-11f0-8fd6-020017001e9a','inscriptis.enable','true'),
                         (26,'39687322-12bf-11f0-8fd6-020017001e9a','gotenberg.enable','true'),
                         (27,'86f74689-13d8-11f0-8fd6-020017001e9a','source.mailCount','1000'),
                         (28,'17f1ce7f-13db-11f0-8fd6-020017001e9a','debug.resetBeforeImport','false'),
                         (29,'d8ae47d8-148e-11f0-8fd6-020017001e9a','source.mailbox.suffixSubject','true'),
                         (31,'51678eda-148f-11f0-8fd6-020017001e9a','sla.business.hours.from','8'),
                         (32,'516c6136-148f-11f0-8fd6-020017001e9a','sla.business.hours.to','16'),
                         (33,'b9408d9d-148f-11f0-8fd6-020017001e9a','sla.business.days.from','1'),
                         (34,'b945396b-148f-11f0-8fd6-020017001e9a','sla.business.days.to','5'),
                         (35,'4039a32e-1490-11f0-8fd6-020017001e9a','sla.reaction.interval','PT5H'),
                         (36,'8c40822d-7025-49a1-86a6-4dc3482146ba','mail.template','<!DOCTYPE html>\n<html lang=\"de\">\n<head>\n  <meta charset=\"UTF-8\">\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n  <title>HTML E-Mail Vorlage</title>\n  <style>\n    /* Grundlegende Styles */\n    body {\n      margin: 0;\n      padding: 0;\n      font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif;\n      background-color: #f4f4f4;\n      color: #333;\n    }\n    .email-container {\n      max-width: 600px;\n      margin: 20px auto;\n      background-color: #ffffff;\n      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);\n      border-radius: 6px;\n      overflow: hidden;\n    }\n    .email-header {\n      background-color: #bbbbbb;\n      text-align: center;\n      padding: 20px;\n    }\n    .email-header img {\n      display: block;\n      margin: 0 auto;\n      width: 100px;\n      height: auto;\n    }\n    .email-content {\n      padding: 40px;\n      line-height: 1.6;\n      font-size: 16px;\n    }\n    .email-content h1 {\n      font-size: 24px;\n      margin-bottom: 10px;\n    }\n    .email-content p {\n      margin-bottom: 15px;\n    }\n  </style>\n</head>\n<body>\n  <div class=\"email-container\">\n    <div class=\"email-header\">\n      <img height=\"{{mail.logo.height}}\" src=\"{{mail.logo.data}}\" alt=\"Logo\">\n    </div>\n    <div class=\"email-content\">\n      {{mailContent}}\n    </div>\n  </div>\n</body>\n</html>\n\n'),
                         (37,'dfb55674-1895-11f0-8939-020017001e9a','mail.logo.data','data:image/png;base64,<BASE64_ENCODED_IMAGE_DATA_HERE>'),
                         (38,'09389467-1896-11f0-8939-020017001e9a','mail.logo.height','128'),
                         (39,'2a9596df-1c49-11f0-8939-020017001e9a','debug.mails.route.all.to',''),
                         (40,'2a9bc4a7-1c49-11f0-8939-020017001e9a','debug.mails.route.all.enabled','tue'),
                         (41,'fc70b428-1c55-11f0-8939-020017001e9a','mail.template.start','<h1>Update for {{ticketNumber}} ({{dateTime}})</h1>\n<h2>Hello {{name}},</h2>\n<p>your Ticket {{ticketNumber}} ({{ticketLinkAbsoluteInA}}) was updated.</p>\n<p> </p>'),
                         (42,'1abb4146-1c56-11f0-8939-020017001e9a','mail.template.end','<p> </p><p><small>This Messege comes form the ticket system. You can reply to it. if so, please keep the remark \"{{ticketMarker}}\" in the subject!</p> \n<p>You can view the ticket here: {{ticketLinkAbsoluteInA}}</p>\n'),
                         (43,'53c32f86-1c5b-11f0-8939-020017001e9a','ai.enable','true'),
                         (44,'53c88783-1c5b-11f0-8939-020017001e9a','ai.openai.api.secret',''),
                         (45,'f25976c3-1c5b-11f0-8939-020017001e9a','ai.openai.model',''),
                         (46,'9b4c8182-1c5c-11f0-8939-020017001e9a','ai.prompt.system','<append system baseline prompt for setting the scene>'),
                         (47,'f73c8c5e-2513-11f0-9594-020017001e9a','ai.prompt.suffix.templatetext.generate','keep all text in german language, Output as regular HTML, without html and body, just the inside of the body. no markdown marup core code markup, just regular html. no final greeting, use html p span and b tags and listings.'),
                         (48,'05f6b6ab-2518-11f0-9594-020017001e9a','ai.prompt.suffix.ticket.answer.generate','keep your answer text in german language, output as html as it would be inside the body tag, dont use headings, just regular text markups such as br and paragraphs and formattings like lists and tables. dont directly quote the ticket thats given but generate an answer for that. dont address the users name but use the placeholder {{name}} instead'),
                         (49,'8c732d38-25a1-11f0-9594-020017001e9a','noun.api.key',''),
                         (50,'8c7a7dd4-25a1-11f0-9594-020017001e9a','noun.api.secret',''),
                         (51,'5dffb207-25a4-11f0-9594-020017001e9a','iconfinder.api.key',''),
                         (52,'5e06878b-25a4-11f0-9594-020017001e9a','iconfinder.api.clientid',''),
                         (54,'1f9af4bc-25a8-11f0-9594-020017001e9a','ai.prompt.suffix.notificationtemplate.generate','keep all text in german language, Output as regular HTML, without html and body, just the inside of the body. no markdown marup core code markup, just regular html. no final greeting, use html p span and b tags and listings.'),
                         (55,'31d118c4-275b-11f0-9594-020017001e9a','search.user.limit','10'),
                         (56,'31d6b258-275b-11f0-9594-020017001e9a','search.orguser.limit','10'),
                         (57,'31db6be7-275b-11f0-9594-020017001e9a','search.ticket.limit','25'),
                         (58,'31e03be0-275b-11f0-9594-020017001e9a','search.attachment.limit','10'),
                         (59,'3b1b6315-275f-11f0-9594-020017001e9a','ai.azure.model.endpoint','https://'),
                         (60,'3b203adb-275f-11f0-9594-020017001e9a','ai.azure.api.key',''),
                         (61,'3b24cd39-275f-11f0-9594-020017001e9a','ai.azure.model.deployment',''),
                         (62,'3b297c67-275f-11f0-9594-020017001e9a','ai.azure.api.tokens.max','2500'),
                         (63,'3b2e3196-275f-11f0-9594-020017001e9a','ai.azure.api.temperature','0.5'),
                         (65,'23cf70b1-27f6-11f0-9594-020017001e9a','status.duplicate.internalName','duplicate'),
                         (66,'fa3bd255-28c7-11f0-9594-020017001e9a','ai.openai.proxy.enable','true'),
                         (67,'fa45e4a6-28c7-11f0-9594-020017001e9a','ai.openai.proxy.url',''),
                         (68,'e95cb3e7-28c8-11f0-9594-020017001e9a','ai.prompt.summary.ticket','Summarize the folling textual representation of a ticket, keep output language in german, format as plain text with linebreaks where needed.'),
                         (70,'3cea1727-2ce6-11f0-84ad-00155d893a24','logo.big-light','<your logos svg>'),
                         (74,'3d01a67b-2ce6-11f0-84ad-00155d893a24','logo.menu','<your logo for menu in svg>'),
                         (78,'33345c47-2ce7-11f0-84ad-00155d893a24','favicon.data','<favicon as base64>'),
                         (79,'ecefce3d-2cea-11f0-84ad-00155d893a24','authentication.newuser.accesslevel.default','user'),
                         (80,'0fea99f9-2cef-11f0-84ad-00155d893a24','debug.reporting.error.mail.web.enabled','false'),
                         (81,'0fefecff-2cef-11f0-84ad-00155d893a24','debug.reporting.error.mail.cli.enabled','true'),
                         (82,'0ff4fbfa-2cef-11f0-84ad-00155d893a24','debug.reporting.error.mail.recipient','<error mail recipient>'),
                         (83,'3cad5fc3-2cf8-11f0-84ad-00155d893a24','text.login','<login message>'),
                         (84,'3cb2db44-2cf8-11f0-84ad-00155d893a24','text.logout','<logout message>'),
                         (85,'5271f00c-2d8f-11f0-84ad-00155d893a24','ai.prompt.mail.summary.subject','For the following Ticket create a short (50 Chars max) Subject that can be used as ticket title. Find actual Names and Persons that are mentioned in this ticket and make the subject as spcific as possible'),
                         (86,'96e1954d-2dce-11f0-84ad-00155d893a24','ai.prompt.article.generate','Generate a Helpful Knowlegene Base Article in German Language for the following Topic. The Output Format shall be HTML it must be well structured and with detailled Instrucutions how to reach the goal. Remember, it\'s german Language in Sie-Form. This is the Topic: '),
                         (87,'a7ff0e27-2e9b-11f0-84ad-00155d893a24','ai.vendor','azure'),
                         (88,'8d26b424-2e9d-11f0-84ad-00155d893a24','ai.azure.api.version','2024-12-01-preview'),
                         (89,'fdf4409e-2ea1-11f0-84ad-00155d893a24','ai.azure.cache.enabled','true');
/*!40000 ALTER TABLE `Config` ENABLE KEYS */;
UNLOCK TABLES;
commit;
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-06-21 14:22:53
