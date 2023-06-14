-- MariaDB dump 10.17  Distrib 10.4.11-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: patient_infosys
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `icon` varchar(32) DEFAULT 'common',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_icons`
--

DROP TABLE IF EXISTS `category_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_icons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fas_icon` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_icons`
--

LOCK TABLES `category_icons` WRITE;
/*!40000 ALTER TABLE `category_icons` DISABLE KEYS */;
INSERT INTO `category_icons` VALUES (1,'bottle'),(2,'bottle2'),(3,'bottle3'),(4,'cross'),(5,'dose'),(6,'flask'),(7,'flask2'),(8,'flask3'),(9,'flask4'),(10,'gloves'),(11,'gloves2'),(12,'gloves3'),(13,'graduated_cylinder'),(14,'health_book'),(15,'insulin_pen'),(16,'inventory'),(17,'inventory2'),(18,'inventory3'),(19,'inventory4'),(20,'inventory5'),(21,'inventory6'),(22,'inventory7'),(23,'jar'),(24,'medical_bag'),(25,'medical_bag2'),(26,'medical_bag3'),(27,'pharmacy'),(28,'pill'),(29,'pills'),(30,'pill_bottle'),(31,'stethoscope'),(32,'stool_analysis'),(33,'supplement'),(34,'supplement2'),(35,'supplement3'),(36,'syringe'),(37,'syringe2'),(38,'test_tube'),(39,'test_tube2'),(40,'test_tube3'),(41,'treatment'),(42,'urine_analysis'),(43,'water\r\n');
/*!40000 ALTER TABLE `category_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checkup_details`
--

DROP TABLE IF EXISTS `checkup_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkup_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_fk_id` int(11) NOT NULL,
  `checkup_number` varchar(255) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `illness_id` int(11) NOT NULL,
  `bp_systolic` int(11) NOT NULL DEFAULT 0,
  `bp_diastolic` int(11) NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(255) NOT NULL,
  `upated_by_guid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_fk_id` (`patient_fk_id`),
  CONSTRAINT `checkup_details_ibfk_1` FOREIGN KEY (`patient_fk_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkup_details`
--

LOCK TABLES `checkup_details` WRITE;
/*!40000 ALTER TABLE `checkup_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `checkup_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chmod`
--

DROP TABLE IF EXISTS `chmod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chmod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fk_id` int(11) NOT NULL,
  `perm_medical` varchar(2) NOT NULL DEFAULT 'w',
  `perm_inventory` varchar(2) NOT NULL DEFAULT 'w',
  `perm_suppliers` varchar(2) NOT NULL DEFAULT 'w',
  `perm_doctors` varchar(2) NOT NULL DEFAULT 'w',
  `perm_users` varchar(2) NOT NULL DEFAULT 'r',
  `perm_maintenance` varchar(2) NOT NULL DEFAULT 'r',
  PRIMARY KEY (`id`),
  KEY `user_fk` (`user_fk_id`),
  CONSTRAINT `user_fk` FOREIGN KEY (`user_fk_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chmod`
--

LOCK TABLES `chmod` WRITE;
/*!40000 ALTER TABLE `chmod` DISABLE KEYS */;
INSERT INTO `chmod` VALUES (1,1,'w','w','w','w','w','w'),(2,2,'w','w','w','w','w','r'),(3,3,'w','w','w','w','w','w'),(4,4,'w','x','x','x','x','x'),(5,5,'w','w','w','w','w','w'),(6,6,'w','x','x','x','x','x'),(7,7,'w','w','w','w','w','w'),(8,8,'w','w','w','w','r','r'),(9,9,'w','x','x','x','x','x'),(10,10,'w','x','x','x','x','x'),(11,11,'w','w','w','w','r','r'),(12,12,'w','w','w','w','w','w'),(13,13,'w','w','w','w','r','r'),(14,14,'w','x','x','x','x','x'),(15,15,'w','x','x','x','x','x'),(16,16,'w','x','x','x','x','x'),(17,17,'w','x','x','x','x','x'),(18,18,'w','w','w','w','r','r'),(19,19,'w','w','w','w','w','w');
/*!40000 ALTER TABLE `chmod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configurations`
--

DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurations` (
  `configs_id` int(11) NOT NULL AUTO_INCREMENT,
  `configs_Key` varchar(255) NOT NULL,
  `configs_value` text NOT NULL,
  PRIMARY KEY (`configs_id`),
  UNIQUE KEY `unique_config_key` (`configs_Key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configurations`
--

LOCK TABLES `configurations` WRITE;
/*!40000 ALTER TABLE `configurations` DISABLE KEYS */;
INSERT INTO `configurations` VALUES (1,'defuse_key','def00000d765fb7642d02852f62b7387e7e4c75048a3b5bcd855cbc255e0c3fea19fe37252c3e1bbb1b0fffff01018afec7aea36af003ce4fc4372c03983cb92776e4c6d');
/*!40000 ALTER TABLE `configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `degrees`
--

DROP TABLE IF EXISTS `degrees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `degrees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `degree` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `degrees`
--

LOCK TABLES `degrees` WRITE;
/*!40000 ALTER TABLE `degrees` DISABLE KEYS */;
INSERT INTO `degrees` VALUES (1,'AuD'),(2,'DC'),(3,'DDS'),(4,'DMD'),(5,'OD'),(6,'DPM'),(7,'DPT'),(8,'Dr'),(9,'DScPT'),(10,'DSN'),(11,'DVM'),(12,'ENT'),(13,'GP'),(14,'GYN'),(15,'LSA'),(16,'MD'),(17,'MLA'),(18,'MP'),(19,'MS'),(20,'OB/GYN'),(21,'PA'),(22,'PharmD');
/*!40000 ALTER TABLE `degrees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor_specialties`
--

DROP TABLE IF EXISTS `doctor_specialties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctor_specialties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `specialization` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`specialization`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor_specialties`
--

LOCK TABLES `doctor_specialties` WRITE;
/*!40000 ALTER TABLE `doctor_specialties` DISABLE KEYS */;
INSERT INTO `doctor_specialties` VALUES (5,'Allergist'),(22,'Anesthesiologist'),(9,'Cardiologist'),(6,'Dermatologist'),(10,'Endocrinologist'),(11,'Gastroenterologist'),(21,'General Surgeon'),(4,'Internist'),(12,'Nephrologist'),(16,'Neurologist'),(8,'OB/GYNE'),(3,'Oncologist'),(7,'Ophtalmologist'),(20,'Orthopedic Surgeon'),(15,'Otolaryngologist (ENT)'),(2,'Pediatric'),(1,'Primary Care'),(17,'Psychiatrist'),(14,'Pulmonologist'),(18,'Radiologist'),(19,'Rheumatologist'),(13,'Urologist');
/*!40000 ALTER TABLE `doctor_specialties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_num` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `specialization` int(11) NOT NULL,
  `degree` int(11) NOT NULL,
  `contact` varchar(16) NOT NULL,
  `address` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_num` (`reg_num`),
  UNIQUE KEY `contact` (`contact`),
  KEY `specialty_fk` (`specialization`),
  CONSTRAINT `specialty_fk` FOREIGN KEY (`specialization`) REFERENCES `doctor_specialties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctors`
--

LOCK TABLES `doctors` WRITE;
/*!40000 ALTER TABLE `doctors` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pref_key` varchar(255) NOT NULL,
  `pref_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pref_key` (`pref_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` VALUES (1,'checkup_records_year','2023'),(2,'system_email','none');
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `illness`
--

DROP TABLE IF EXISTS `illness`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `illness` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `illness_description` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `illness`
--

LOCK TABLES `illness` WRITE;
/*!40000 ALTER TABLE `illness` DISABLE KEYS */;
/*!40000 ALTER TABLE `illness` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(128) NOT NULL,
  `item_category` int(11) NOT NULL,
  `item_code` varchar(255) NOT NULL,
  `image` text DEFAULT NULL,
  `price_per_unit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit_measure` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL DEFAULT 0,
  `remaining` int(11) unsigned NOT NULL,
  `critical_level` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiryDate` varchar(32) DEFAULT NULL,
  `remarks` text NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_name` (`item_name`),
  UNIQUE KEY `item_code` (`item_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_key` varchar(255) NOT NULL,
  `id_number` varchar(255) NOT NULL,
  `patient_type` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(32) NOT NULL,
  `middlename` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `birthday` varchar(128) NOT NULL,
  `gender` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `weight` decimal(11,2) NOT NULL DEFAULT 0.00,
  `height` decimal(11,2) NOT NULL DEFAULT 0.00,
  `contact` varchar(32) DEFAULT '0',
  `parent` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_number` (`id_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescription_details`
--

DROP TABLE IF EXISTS `prescription_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prescription_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checkup_fk_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `amount` int(11) unsigned NOT NULL,
  `unit_measure` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `prescription_details_ibfk_1` (`checkup_fk_id`),
  CONSTRAINT `prescription_details_ibfk_1` FOREIGN KEY (`checkup_fk_id`) REFERENCES `checkup_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescription_details`
--

LOCK TABLES `prescription_details` WRITE;
/*!40000 ALTER TABLE `prescription_details` DISABLE KEYS */;
INSERT INTO `prescription_details` VALUES (1,51,3,1,0),(3,56,7,1,9),(4,58,1,1,1),(5,58,15,2,0),(6,63,15,2,7),(7,63,3,2,9),(8,63,7,2,9),(9,64,5,1,1),(10,64,8,1,1),(11,66,3,1,9),(12,69,15,1,0),(13,69,1,1,0),(14,106,12,1,0),(16,108,15,10,0),(20,115,46,1,0),(22,117,31,14,0),(23,118,7,1,0),(27,125,1,1,0),(28,125,34,1,0),(29,126,48,1,0),(30,134,36,1,0);
/*!40000 ALTER TABLE `prescription_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_measures`
--

DROP TABLE IF EXISTS `unit_measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `measurement` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `measurement_unique` (`measurement`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_measures`
--

LOCK TABLES `unit_measures` WRITE;
/*!40000 ALTER TABLE `unit_measures` DISABLE KEYS */;
INSERT INTO `unit_measures` VALUES (3,'Bag'),(5,'Blister Tray'),(4,'Bottle'),(2,'Box'),(7,'Gram'),(16,'Gramo'),(12,'karton'),(10,'Litro'),(8,'Milligram'),(6,'Pack'),(9,'Piece'),(1,'Tablet/Capsule');
/*!40000 ALTER TABLE `unit_measures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_guid` text NOT NULL,
  `perm_checkup_form` varchar(16) NOT NULL DEFAULT 'f',
  `perm_restock` varchar(16) NOT NULL DEFAULT 'f',
  `perm_inventory` varchar(16) NOT NULL DEFAULT 'f',
  `perm_suppliers` varchar(16) NOT NULL DEFAULT 'f',
  `perm_patient_records` varchar(16) NOT NULL DEFAULT 'f',
  `perm_illness` varchar(16) NOT NULL DEFAULT 'f',
  `perm_categories` varchar(16) NOT NULL DEFAULT 'f',
  `perm_users` varchar(16) NOT NULL DEFAULT 'f',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (1,'925BDD1B-195B-F214-2EAC-2F8287CA2D94','f','f','f','f','f','f','f','f'),(2,'5074512C-8B6B-B388-AC54-B86609B9F237','f','f','f','f','f','f','f','f'),(3,'F3C5DA74-4867-6613-FB0E-E9E7C668EB0E','f','f','f','f','f','f','f','f'),(4,'5CB2CBA9-3627-D9BD-20D9-5CFE91074BA8','f','f','f','f','f','x','x','f'),(5,'D7262F9F-4609-57C6-308D-551CF4A172F5','f','x','x','x','f','x','x','x'),(6,'3B196CB1-0482-1E2C-462C-422F4BCDC562','f','x','x','x','f','x','x','x'),(7,'7E15C088-E999-F96C-DF90-D317858C3962','f','f','f','f','f','f','f','f'),(8,'2E11E704-D2EE-1290-C924-A1C48346F55A','f','f','x','x','f','x','x','x'),(9,'16073FA3-97A0-16C5-E16C-7926DE62BE88','f','x','x','x','f','x','x','x'),(10,'2063898E-F346-4103-1F69-DEF1B9AE85E7','f','x','x','x','f','x','x','x'),(11,'9A669EC0-DFE6-ED1C-4652-09F9C144078B','f','f','x','x','f','x','x','x'),(12,'C148089D-B3AC-7D0F-47BF-69D22B6D8559','f','f','x','x','f','x','x','x'),(13,'D5A36C54-92C9-5AC6-DAA4-638028318E08','f','x','x','x','f','x','x','x'),(14,'DBE06D90-758A-7D07-8125-C7D729C97FC8','f','f','f','f','f','f','f','f'),(15,'0BE73AA6-9052-9221-9A1D-725C6ACFA556','f','f','f','f','f','f','f','f'),(16,'E0C4F1DC-C922-3634-32B5-8832667CDD25','f','f','f','f','f','f','f','f'),(17,'A95ED8E7-ABBD-7620-1B00-FA2A5362DDDF','f','f','x','x','f','x','x','x');
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `role` int(11) NOT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `firstname` varchar(32) NOT NULL,
  `middlename` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) NOT NULL,
  `avatar` varchar(11) NOT NULL DEFAULT 'who',
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `guid` (`guid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'system','default','$2y$10$LO7R/J75KxjI5LzUNixpo.tzLvHA/sYMMMcCq.2G3UJQe8tn9XRta',3,'907C50E3-C6FC-8050-BF1A-26BE26CFDE9C','system',NULL,'default','system','2023-03-28'),(2,'boni','godmode2@psu.edu','$2y$10$bxsK3qAS7bPWcLf//vBWsOs/GqCcITKJYU3/FO1YOEClEEkaM/pAO',2,'04904E74-3826-5CC9-F7EF-CC25783866CD','Andres','de Castro','Bonifacio','avatar_7','2023-02-22'),(3,'godmode','godmode@psu.edu','$2y$10$HY1oHPq619tPrP8nSeqOQe5l19WUp6hKELJbTSNQJ8.WjqE.72Ig2',3,'925BDD1B-195B-F214-2EAC-2F8287CA2D94','kenneth','cabradilla','cordero','avatar_29','2023-02-22'),(4,'rizal','rizal@psu.edu','$2y$10$bxsK3qAS7bPWcLf//vBWsOs/GqCcITKJYU3/FO1YOEClEEkaM/pAO',1,'28FC6023-BFEC-064C-88C3-D187025DCCC6','Jose','Mercado','Rizal','avatar_30','2023-03-27'),(5,'messi','messi@psu.edu','$2y$10$bxsK3qAS7bPWcLf//vBWsOs/GqCcITKJYU3/FO1YOEClEEkaM/pAO',2,'2027DC89-A817-BA36-79A8-D96E8A67C6F5','Lionel','Andres','Messi','avatar_57','2023-03-27'),(6,'neymar','neymar@psu','$2y$10$bxsK3qAS7bPWcLf//vBWsOs/GqCcITKJYU3/FO1YOEClEEkaM/pAO',1,'5C790987-55C6-B4BC-19D1-90680555E009','Neymar','Da Silva','Junior','avatar_58','2023-03-27'),(7,'luna','luna@psu','$2y$10$ZTUtbi4ZKucx1pJniNj/g.IMgq99RsDLBMAVlVrSQhzQ0T7QVoNCe',3,'64E5D46E-12EA-98D6-E93A-DD7C1CCE4C4A','Antonio','Narciso','Luna','avatar_61','2023-03-27'),(8,'nario','nario@psu.edu','$2y$10$8yNFtvyNIFRWNlJAgqW9D.Y/KIDDPOdAyYxe0/tMvCCShZovFQFPC',2,'49109BB5-A1DB-CB6E-17F5-250C17885380','Apolinario','Maranan','Mabini','avatar_64','2023-03-27'),(9,'gabriella','gab@psu','$2y$10$7ZnNYnAhjQiGakkonoKU6etzB1/mbGJ.1awspbrhwXejiiswMJOK6',1,'E815534E-4987-8966-507E-DE0E57513267','Gabriella','Josefa','Silang','avatar_46','2023-03-27'),(10,'pilar','mhd@psu','$2y$10$bxsK3qAS7bPWcLf//vBWsOs/GqCcITKJYU3/FO1YOEClEEkaM/pAO',1,'837C47CE-10EA-7D76-154C-4DD67E5AAF3D','Marcelo','Hilaro','Del Pilar','avatar_62','2023-03-27'),(11,'longlife','jpe@psu','$2y$10$iNOovIMWKU9TAYn39euC6.w26XEltcqOvjjoRZm5sqqiybIUzfoBC',2,'0D79EA80-E922-A8B8-EA79-127D737D1884','Juan','Ponce','Enrile','avatar_4','2023-03-27'),(12,'marvin','chef@psu','$2y$10$a5Td1q5/bn2ad.5hTH1cFuJQfb7VziqrZTQfLuJj4Bawmy2P0hHBa',3,'7929E349-63C9-EFBD-7A70-76F198735661','Marvin','','Agustin','avatar_20','2023-03-27'),(13,'trump','trump@psu','$2y$10$z7s9aQMfw2ZQd9PWMzwh8.Ts.CIAFpI2GRfVXyD3smnJTZuzRTdM.',2,'5D31A370-228C-48B8-090F-7A9194997C50','Donald','J','Trump','avatar_54','2023-03-27'),(14,'moonjae','mj@psu','$2y$10$LuCK/VO898Bg3/oJLz2a1eJOQievg9IKuY7gy.C/NIasGVZwAFC7u',1,'EAC4D65A-ABA9-8A8C-69C0-26D3B581C1FD','Moon','Jae','In','avatar_67','2023-03-27'),(15,'kpop','kpop','$2y$10$hKuFPyZe0RFaLHwy7nMrCeKt.KbJyepOBphtplpr04D69okgtk2i6',1,'F288905B-7D35-F551-157A-1F524DCE7CE2','Kim','','Taehyung','avatar_52','2023-03-27'),(16,'cardo','cardo','$2y$10$mBsRURW29bKH5kZyVZPBeu7Sfz27vdTK5DE/gZPmioDpZwNdhZ0pm',1,'E706A3BC-B235-564B-673D-E489F673B883','Ricardo','','Dalisay','avatar_63','2023-03-27'),(17,'lana','lana@psu','$2y$10$/WtI4Qyxl0QeS.PDsqCSTu9PEOEce5S3QxC0m9KUumRcniD/j52XK',1,'F7031BD4-2D42-1EEA-FA80-A7F79FD26443','Gigi','','De Lana','avatar_5','2023-03-27'),(18,'rock','rock@psu','$2y$10$33BwUtQUatDZTCCRyqvfj.Y2yXvfhjweSKvLDs9/UMp7QUA/YESZy',2,'D6DD6218-25F9-42B9-E467-5A8FF8D65319','Dwayne','The Rock','Johnson','avatar_48','2023-03-27'),(19,'elon','tesla@chat.gpt','$2y$10$OX59v4h9MmqzzN7C98UCyOlTGAEjgtGOW3SuXH0Cl6YD2entTJjrK',3,'EC0FA57B-CD0C-F433-F00B-5D84A0FFE699','Elon','Reeves','Musk','avatar_71','2023-03-30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `waste`
--

DROP TABLE IF EXISTS `waste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `waste` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `waste_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `waste`
--

LOCK TABLES `waste` WRITE;
/*!40000 ALTER TABLE `waste` DISABLE KEYS */;
INSERT INTO `waste` VALUES (1,1,1,'Overstocking','2023-04-12'),(2,33,10,'Overstocking','2023-04-12'),(3,35,14,'Human Error','2023-03-12'),(4,4,25,'Expired / Defective','2023-03-09'),(5,3,10,'Inadequate Control','2023-02-17'),(6,15,10,'Overstocking','2022-04-15'),(7,10,10,'Overstocking','2022-04-15'),(8,12,10,'Overstocking','2022-04-15'),(9,15,75,'Expired','2023-04-21'),(10,42,31,'Expired','2023-04-21'),(11,1,10,'Expired / Defective','2023-05-04');
/*!40000 ALTER TABLE `waste` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-13 20:48:10
