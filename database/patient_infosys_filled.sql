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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Antacid','water','2023-04-20 01:03:57'),(2,'Analgesics','pills','2023-04-20 00:35:53'),(3,'Anorectics','pills','2023-04-20 00:35:53'),(4,'Antibiotics','pills','2023-04-20 00:35:53'),(5,'Anticoagulant','flask2','2023-04-20 00:35:53'),(6,'Antifungals','pills','2023-04-20 00:35:53'),(7,'Beta Blockers','bottle2','2023-04-20 00:35:53'),(8,'Calcium Channel Blockers','bottle2','2023-04-20 00:35:53'),(9,'Diuretics','pills','2023-04-20 00:35:53'),(10,'Hypoglycaemia','pills','2023-04-20 00:35:53'),(11,'Nasal Antiallergics','pills','2023-04-20 00:35:53'),(12,'Nasal Decongestant','pills','2023-04-20 00:35:53'),(13,'Oestrogens','pills','2023-04-20 00:35:53'),(14,'Paracetamol','supplement2','2023-04-20 00:35:53'),(15,'Parenteral Anticoagulants','pills','2023-04-20 00:35:53'),(16,'Potassium Channel Blockers','bottle2','2023-04-20 00:35:53'),(17,'Supplements','supplement3','2023-04-20 00:35:53'),(18,'Vitamins','pill_bottle','2023-04-20 00:35:53'),(19,'Loperamide','pills','2023-04-20 00:35:53'),(20,'Medical Supply','medical_bag2','2023-04-20 00:35:53'),(21,'Foods','common','2023-04-20 00:35:53'),(22,'Herbal','herb3','2023-04-20 00:35:53'),(23,'Anti-Infectives','common','2023-04-20 00:35:53'),(24,'Anti-Bacterial','common','2023-04-20 00:35:53'),(25,'Respiratory Disorder','common','2023-04-20 00:35:53'),(27,'Vaccine','syringe2','2023-04-20 01:52:05'),(28,'anti allergy','common','2023-04-20 23:40:06'),(29,'Anti Material','common','2023-05-10 13:29:06'),(30,'Armor Piercing','common','2023-05-10 13:29:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkup_details`
--

LOCK TABLES `checkup_details` WRITE;
/*!40000 ALTER TABLE `checkup_details` DISABLE KEYS */;
INSERT INTO `checkup_details` VALUES (16,12,'2023-03-08-00016',1,6,0,0,'2023-03-08 09:28:54','2023-03-08 09:28:54',3,''),(19,12,'2023-03-09-00017',2,38,180,110,'2023-03-09 14:45:01','2023-03-08 14:45:01',10,''),(20,12,'2023-02-02-00020',1,8,0,0,'2023-02-02 15:48:24','2023-03-08 15:48:24',8,''),(21,12,'2022-01-14-00021',2,14,0,0,'2022-01-14 15:48:42','2023-03-08 15:48:42',18,''),(22,3,'2023-03-10-00022',1,51,0,0,'2023-03-10 03:55:19','2023-03-10 03:55:19',3,''),(23,5,'2023-03-10-00023',2,55,0,0,'2023-03-10 03:57:20','2023-03-10 03:57:20',3,''),(30,4,'2023-03-12-00024',1,10,0,0,'2023-03-12 05:28:37','2023-03-12 05:28:37',3,''),(31,14,'2023-03-12-00031',2,1,0,0,'2023-03-12 06:37:46','2023-03-12 06:37:46',3,''),(32,22,'2023-03-12-00032',1,3,0,0,'2023-03-12 06:39:26','2023-03-12 06:39:26',3,''),(33,5,'2023-03-12-00033',2,10,0,0,'2023-03-12 06:42:33','2023-03-12 06:42:33',3,''),(34,8,'2023-03-12-00034',1,9,120,110,'2023-03-12 06:43:33','2023-03-12 06:43:33',3,''),(41,7,'2023-03-13-00035',2,35,0,0,'2023-03-13 15:28:09','2023-03-13 15:28:09',3,''),(42,4,'2023-03-13-00042',1,10,0,0,'2023-03-13 15:31:27','2023-03-13 15:31:27',3,''),(43,14,'2023-03-13-00043',2,2,0,0,'2023-03-13 15:42:44','2023-03-13 15:42:44',3,''),(44,32,'2023-03-14-00044',1,6,0,0,'2023-03-14 07:07:38','2023-03-14 07:07:38',8,''),(45,30,'2023-03-14-00045',2,9,180,110,'2023-03-14 07:08:33','2023-03-14 07:08:33',9,''),(46,34,'2023-03-14-00046',1,4,0,0,'2023-03-14 08:05:57','2023-03-14 08:05:57',2,''),(49,34,'2023-03-15-00047',2,11,0,0,'2023-03-15 02:50:29','2023-03-15 02:50:29',3,''),(50,22,'2023-03-15-00050',1,6,0,0,'2023-03-15 02:50:54','2023-03-15 02:50:54',3,''),(51,30,'2023-03-15-00051',2,4,0,0,'2023-03-15 03:16:05','2023-03-15 03:16:05',3,''),(52,32,'2023-03-15-00052',1,4,120,80,'2023-03-15 14:03:13','2023-03-15 14:03:13',3,''),(56,34,'2023-03-15-00053',1,5,180,80,'2023-03-15 14:33:09','2023-03-15 14:33:09',3,''),(58,22,'2023-03-16-00058',1,6,0,0,'2023-03-16 06:57:04','2023-03-16 06:57:04',3,''),(63,38,'2023-03-17-00059',7,6,0,0,'2023-03-17 11:30:45','2023-03-17 11:30:45',3,''),(64,43,'2023-03-17-00064',2,25,0,0,'2023-03-17 14:01:58','2023-03-17 14:01:58',3,''),(66,44,'2023-03-17-00065',7,14,0,0,'2023-03-17 15:46:42','2023-03-17 15:46:42',3,''),(68,13,'2023-03-18-00068',1,8,0,0,'2023-03-17 16:12:52','2023-03-17 16:12:52',3,''),(69,45,'2023-03-18-00069',2,6,0,0,'2023-03-17 16:20:27','2023-03-17 16:20:27',10,''),(78,32,'2023-03-20-00070',2,6,0,0,'2023-03-20 09:10:43','2023-03-20 09:10:43',3,''),(79,38,'2023-03-21-00079',2,4,0,0,'2023-03-21 03:25:35','2023-03-21 03:25:35',11,''),(80,38,'2023-03-21-00080',3,2,0,0,'2023-03-21 06:20:45','2023-03-21 06:20:45',18,''),(81,30,'2023-03-21-00081',6,4,0,0,'2023-03-21 06:50:34','2023-03-21 06:50:34',13,''),(82,38,'2023-03-22-00082',3,6,0,0,'2023-03-22 04:47:15','2023-03-22 04:47:15',12,''),(83,34,'2023-03-22-00083',3,2,0,0,'2023-03-22 07:47:34','2023-03-22 07:47:34',11,''),(84,45,'2023-03-22-00084',2,8,0,0,'2023-03-22 07:48:25','2023-03-22 07:48:25',15,''),(85,43,'2023-03-22-00085',2,9,0,0,'2023-03-22 09:58:06','2023-03-22 09:58:06',4,''),(86,3,'2023-03-25-00086',3,51,0,0,'2023-03-25 02:02:10','2023-03-25 02:02:10',11,''),(88,38,'2023-03-25-00088',5,7,180,80,'2023-03-25 02:04:33','2023-03-25 02:04:33',18,''),(104,22,'2023-04-04-00089',5,4,0,0,'2023-04-04 05:05:27','2023-04-04 05:05:27',12,''),(105,76,'2023-04-04-00105',5,4,0,0,'2023-04-04 05:34:55','2023-04-04 05:34:55',10,''),(106,4,'2023-04-07-00106',2,4,0,0,'2023-04-07 07:40:08','2023-04-07 07:40:08',3,''),(108,41,'2023-04-07-00107',3,3,0,0,'2023-04-07 10:27:35','2023-04-07 10:27:35',3,''),(109,31,'2023-04-13-00109',2,2,0,0,'2023-04-13 06:14:57','2023-04-13 06:14:57',3,''),(111,22,'2023-04-16-00110',5,17,0,0,'2023-04-16 10:50:07','2023-04-16 10:50:07',3,''),(112,22,'2023-04-17-00112',6,20,0,0,'2023-04-16 18:05:47','2023-04-16 18:05:47',3,''),(114,4,'2023-04-20-00114',1,10,0,0,'2023-04-19 17:19:59','2023-04-19 17:19:59',3,''),(115,79,'2023-04-20-00115',3,68,0,0,'2023-05-06 05:47:21','2023-04-20 05:47:21',3,''),(117,42,'2023-04-21-00116',1,5,0,0,'2023-05-06 06:20:13','2023-04-21 06:20:13',3,''),(118,81,'2023-04-27-00118',2,28,0,0,'2023-05-07 15:21:44','2023-04-27 15:21:44',3,''),(124,84,'2023-05-05-00119',2,4,120,80,'2023-05-07 05:43:00','2023-05-05 05:43:00',3,''),(125,89,'2023-05-06-00125',2,4,180,80,'2023-05-08 01:50:00','2023-05-06 01:50:00',3,''),(126,4,'2023-05-06-00126',2,4,120,80,'2023-05-13 03:17:19','2023-05-06 03:17:19',3,''),(127,79,'2023-05-06-00127',2,10,0,0,'2023-05-11 11:50:45','2023-05-06 11:50:45',3,''),(128,32,'2023-05-06-00128',2,12,0,0,'2023-05-12 11:51:35','2023-05-06 11:51:35',3,''),(129,45,'2023-05-06-00129',2,14,0,0,'2023-05-12 11:51:47','2023-05-12 11:51:47',3,''),(130,2,'2022-05-06-00130',2,21,0,0,'2022-05-03 14:07:52','2022-05-03 14:07:52',3,''),(131,5,'2022-05-06-00131',2,4,0,0,'2022-05-04 14:08:06','2022-05-04 14:08:06',3,''),(132,78,'2022-05-06-00132',2,22,0,0,'2022-05-06 14:08:36','2022-05-06 14:08:36',3,''),(133,97,'2022-05-10-00133',2,6,0,0,'2022-05-09 17:40:14','2022-05-09 17:40:14',3,''),(134,89,'2023-05-13-00134',2,66,0,0,'2023-05-13 07:32:42','2023-05-13 07:32:42',17,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctors`
--

LOCK TABLES `doctors` WRITE;
/*!40000 ALTER TABLE `doctors` DISABLE KEYS */;
INSERT INTO `doctors` VALUES (1,1100,'Kharl','Vincent','Mondares',1,8,'0933123123','','2023-03-22 03:57:41'),(2,10000,'Christia Marie','J','Posadas-Flores',1,8,'091234','','2023-03-22 03:57:41'),(3,11125,'Ara May','Lomibao','Mondocan',4,8,'0123667','','2023-03-22 03:57:41'),(4,1500,'Willie','Tan','Ong',4,8,'0999441135','','2023-03-22 03:57:41'),(5,1800,'Clarissa Joy','L','Balgua',14,8,'09975562123','','2023-03-22 03:57:41'),(6,11157,'Ken','Marco','Ulogani',11,13,'0994','','2023-03-22 03:57:41'),(7,1665,'Kenneth','C','Cordero',1,16,'0967543124','','2023-03-22 03:57:41');
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
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `illness`
--

LOCK TABLES `illness` WRITE;
/*!40000 ALTER TABLE `illness` DISABLE KEYS */;
INSERT INTO `illness` VALUES (1,'Abdominal Pain','Abdominal pain is pain that occurs between the chest and pelvic regions. Abdominal pain can be crampy, achy, dull, intermittent, or sharp. It’s also called a stomachache.','2023-04-18 06:24:36'),(2,'Abscess','An abscess is a collection of pus that has built up within the tissue of the body.','2023-04-18 06:24:36'),(3,'Allergic Rhinitis','','2023-04-18 06:24:36'),(4,'Allergies','','2023-04-18 06:24:36'),(5,'Anemia','Kakapuyat mo yan. Matulog ka din wag puro bebe time','2023-04-18 06:24:36'),(6,'Animal Bite/Scratch','An animal bite is a wound, usually a puncture or laceration, caused by the teeth.','2023-04-18 06:24:36'),(7,'Arthritis','Arthritis is the swelling and tenderness of one or more joints. The main symptoms of arthritis are joint pain and stiffness, which typically worsen with age','2023-04-18 06:24:36'),(8,'Asthma','A lung disorder characterized by narrowing of the airways, the tubes which carry air into the lungs, that are inflamed and constricted, causing shortness of breath, wheezing and cough.','2023-04-18 06:24:36'),(9,'Back Pain/Nape Pain','','2023-04-18 06:24:36'),(10,'Body Pain','','2023-04-18 06:24:36'),(11,'Boils/Cellulitis','','2023-04-18 06:24:36'),(12,'Breast Mass','','2023-04-18 06:24:36'),(13,'Bronchitis','','2023-04-18 06:24:36'),(14,'Burn','','2023-04-18 06:24:36'),(15,'Chest Pain/Angina/Cardiac Related','','2023-04-18 06:24:36'),(16,'Chicken Pox','','2023-04-18 06:24:36'),(17,'Circumcision','','2023-04-18 06:24:36'),(18,'Clogged Nose/Colds/Rhinovirus Infection','','2023-04-18 06:24:36'),(19,'Conjunctivitis/Sore Eyes','','2023-04-18 06:24:36'),(20,'Constipation','','2023-04-18 06:24:36'),(21,'Cough','','2023-04-18 06:24:36'),(22,'Depression/Anxiety','','2023-04-18 06:24:36'),(23,'Diabetes','','2023-04-18 06:24:36'),(24,'Dislocation','','2023-04-18 06:24:36'),(25,'Dizziness','','2023-04-18 06:24:36'),(26,'Dysnmenorrhea','','2023-04-18 06:24:36'),(27,'Epilepsy','','2023-04-18 06:24:36'),(28,'Eye Irritation','','2023-04-18 06:24:36'),(29,'Fever','','2023-04-18 06:24:36'),(30,'Fractures','','2023-04-18 06:24:36'),(31,'Fungal Infection','','2023-04-18 06:24:36'),(32,'Gastritis/Hyperacidity','','2023-04-18 06:24:36'),(33,'Gastrointestinal Infection','','2023-04-18 06:24:36'),(34,'Hyperlipidemia','','2023-04-18 06:24:36'),(35,'Headache/Tension/Migraine','','2023-04-18 06:24:36'),(36,'Hematoma','','2023-04-18 06:24:36'),(37,'Hepatitis A or B','','2023-04-18 06:24:36'),(38,'Hypertension','','2023-04-18 06:24:36'),(39,'Hyperventilation','','2023-04-18 06:24:36'),(40,'Hypothermia','','2023-04-18 06:24:36'),(41,'Hysteria','','2023-04-18 06:24:36'),(42,'Influenza/Flu','','2023-04-18 06:24:36'),(43,'Insect Bite and Sting','','2023-04-18 06:24:36'),(44,'Laryngitis/Pharyngitis','','2023-04-18 06:24:36'),(45,'Measles','','2023-04-18 06:24:36'),(46,'Mumps','','2023-04-18 06:24:36'),(47,'Muscle Pain/Cramps','','2023-04-18 06:24:36'),(48,'Nausea and Vomiting','','2023-04-18 06:24:36'),(49,'Nerve Pain','','2023-04-18 06:24:36'),(50,'Nose Bleeding/Epistaxis','','2023-04-18 06:24:36'),(51,'Obese','','2023-04-18 06:24:36'),(52,'Oritis Media','','2023-04-18 06:24:36'),(53,'Overweight','','2023-04-18 06:24:36'),(54,'Paronychia','','2023-04-18 06:24:36'),(55,'Seizure','','2023-04-18 06:24:36'),(56,'Sexually Transmitted Diseases','','2023-04-18 06:24:36'),(57,'Sinus Tachycardia','','2023-04-18 06:24:36'),(58,'Sinusitis','','2023-04-18 06:24:36'),(59,'Sore Throat secondary to Acute Tonsillitis','','2023-04-18 06:24:36'),(60,'Sprain','','2023-04-18 06:24:36'),(61,'Stye','','2023-04-18 06:24:36'),(62,'Syncope/Fainting','','2023-04-18 06:24:36'),(63,'Tinnitus/Ear Pain','','2023-04-18 06:24:36'),(64,'Underweight','','2023-04-18 06:24:36'),(65,'Urinary Tract Infection','','2023-04-18 06:24:36'),(66,'Vertigo','','2023-04-18 06:24:36'),(67,'Wound/Abrasion/Avulsed/Lacerated','','2023-04-18 06:24:36'),(68,'Covid-19','gawang insik','2023-04-20 05:48:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'Advil Tablets',2,'ADV-PARA-TAB','B69BDA82-29F3-2A55-E4F8-850DBADAE18A.jpg',10.00,1,11,125,50,'2022-12-16 08:00:00','2023-11-28','Ibuprofen works by reducing hormones that cause inflammation and pain in the body. Advil is used to reduce fever and treat pain or inflammation caused by many conditions such as headache, toothache, back pain, arthritis, menstrual cramps, or minor injury. Advil is used in adults and children who are at least 2 yrs old.','2022-12-16 08:00:00'),(2,'Alvedon Tablets',2,'ALV-PARA-TAB','C1AD5AAE-9FCA-049A-FC37-E196F9EDA778.jpg',40.00,1,6,98,80,'2022-12-15 08:00:00','2024-12-01','','2022-12-15 08:00:00'),(3,'Band-Aid',20,'BND-ADHESIVE','C8F771DB-3970-8DC1-398E-0FEF888E07B1.jpg',25.00,9,10,95,90,'2022-12-21 08:00:00','2024-12-01','para sa sugat','2022-12-21 08:00:00'),(4,'Bioflu',14,'BIOFLU-PARA-TABS','E039C23D-FE00-9578-9ADB-5517653541C6.jpg',50.00,1,7,100,25,'2022-12-17 08:00:00','2024-12-01','','2022-12-17 08:00:00'),(5,'Biogesic Tablets',14,'BIO-PARA-TAB','C4080480-6C7B-DF11-A2B3-02C0F09F548D.png',50.00,1,5,98,50,'2022-12-15 08:00:00','2024-04-01','para sa lagnat. Ingat!','2022-12-15 08:00:00'),(6,'Ceelin Vit C',18,'CLN-VIT-C','9337660F-8773-974C-440B-BD55A1492BDD.jpg',0.00,1,2,100,50,'2022-12-16 00:00:00',NULL,'','2022-12-16 00:00:00'),(7,'Cotton Buds',20,'CTN-BUDS','47E0E25E-0EFA-8B55-5311-EAC892F78E3D.jpg',38.00,9,9,149,10,'2022-12-21 08:00:00','2024-04-01','','2022-12-21 08:00:00'),(8,'Decolgen',12,'DCOLGN-TAB','999FA7CA-488A-A7AA-3282-CDAD4DAA6F6C.jpg',30.00,1,5,98,35,'2022-12-17 08:00:00','2024-04-01','','2022-12-17 08:00:00'),(9,'Enervon Vit C',18,'EVN-VIT-C','F865EAB5-CAB4-E772-F8B0-B43E355B490B.jpg',45.00,1,10,100,35,'2022-12-21 08:00:00','2024-07-01','','2022-12-21 08:00:00'),(10,'Face Mask',20,'MED-SPLY-MASK','67F151F4-6D4A-00C7-B20A-EA1A1BDC0875.jpg',40.00,9,9,100,25,'2022-12-18 08:00:00','2024-07-01','','2022-12-18 08:00:00'),(11,'Gauze Bandage',20,'MED-SPLY-GAUZ','E0B0571D-0A57-5376-877B-D851753CE8CD.jpg',100.00,9,7,100,20,'2022-12-18 08:00:00','2024-07-01','','2022-12-18 08:00:00'),(12,'Lomotil',19,'LOM-LOPMDE','6FD046C2-DF4B-BAC2-4C75-EF0F4DAE0537.jpg',100.00,1,6,100,20,'2022-12-18 08:00:00','','','2022-12-18 08:00:00'),(13,'Losartan Potassium',16,'LOTA-P-25','1872C07D-2A9E-FBB4-D781-4F92FAA91267.jpg',70.00,1,9,100,50,'2022-12-16 08:00:00','2024-07-01','','2022-12-16 08:00:00'),(14,'Neozep Tablets',14,'NEO-PARA-TAB','F9345138-EBB0-F59E-A4DB-3DCC3E535587.png',40.00,1,8,100,30,'2022-12-15 08:00:00','2024-07-01','','2022-12-15 08:00:00'),(15,'Alka-Seltzer',1,'ALK-SETZ','401D4339-97AD-A38B-6973-9BA491D149DA.png',40.00,1,7,50,45,'2022-12-27 20:46:43','2023-11-24','','2022-12-27 20:46:43'),(16,'Warfarin',5,'WRFN','26D2AF48-C3A0-0F57-F304-307C2588B913.jpg',30.00,1,10,100,35,'2022-12-22 04:48:56','2024-08-01','','2022-12-22 04:48:56'),(17,'Loperamide Diatabs',19,'LPD-DTAB','40EEA696-A14B-3C08-8028-2C601C15F0E2.jpg',30.00,9,9,100,50,'2023-03-12 06:36:56','2024-08-01','','2023-03-12 06:36:56'),(30,'Lucky Me Noodles',21,'LKM-NDL','6538E94D-0F08-54BA-0FA6-B9F89ABFD2F8.png',0.00,6,0,112,50,'2023-04-06 05:05:35','2024-09-12','','2023-04-06 05:05:35'),(31,'Conzace',18,'CNZ-MULTI-VIT','D030B8ED-A21A-0D1D-8D26-0D97E74C6942.jpg',0.00,9,0,16,10,'2023-04-06 16:10:19','2024-09-12','','2023-04-06 16:10:19'),(32,'Cotton Balls',20,'CTN-BAL','1F9A309B-FA50-13B6-8A5A-F14EB8C49398.jpg',0.00,9,0,100,30,'2023-04-07 04:29:41','2024-09-12','','2023-04-07 04:29:41'),(33,'Cephalexin',23,'CFLXN','B95BE11F-7F30-B9AA-BBA1-22183A4213D2.jpg',0.00,1,0,100,25,'2023-04-07 04:32:16','2024-11-05','','2023-04-07 04:32:16'),(34,'Amoxicillin Capsules',24,'AMX-ANTIBAC','A925855E-AC43-2F72-7920-BB9E1B0343B1.jpg',0.00,1,0,49,15,'2023-04-07 04:33:55','11/05/2024','','2023-04-07 04:33:55'),(35,'Solmux',25,'SLMX-CARBXN','7AD92923-4FEB-7ED6-D88C-F512C335520B.png',0.00,1,0,25,10,'2023-04-07 04:36:49','11/05/2024','','2023-04-07 04:36:49'),(36,'Modess',20,'MDS-SANITARY','14B29DE5-DC9A-7A37-4085-DC0D130AA668.jpg',0.00,9,0,9,5,'2023-04-07 04:39:11','2024-11-05','','2023-04-07 04:39:11'),(42,'Kamatis',21,'KMT',NULL,0.00,9,0,50,10,'2023-04-14 09:16:07','','','2023-04-14 09:16:07'),(44,'Unknown Item',56,'may lason',NULL,0.00,5,0,50,5,'2023-04-19 16:23:29',NULL,'','2023-04-19 16:23:29'),(46,'Covax',27,'VAXX',NULL,0.00,4,15,300,30,'2023-04-20 05:53:49','05/11/2023','gamot sa covid','2023-04-20 05:53:49'),(48,'cetirizine',28,'CTRZ','D61256D4-BFEE-D00D-06FF-724DD40BA698.jpg',0.00,1,0,99,30,'2023-04-21 03:40:06','','Cetirizine, sold under the brand name Zyrtec among others, is a second-generation antihistamine used to treat allergic rhinitis, dermatitis, and urticaria. It is taken by mouth. Effects generally begin within thirty minutes and last for about a day','2023-04-21 03:40:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,'B2C5C79B-960C-2058-79E0-1A38EEA7D598','22-LN-123',1,'Kelly','Nancy','Marion','2023-03-22',2,24,'sitio Pitong Gatang, Purok Kwatro, Provincia del Oeste',54.00,150.00,'1234','Uncle Sam','2023-03-05 02:41:26','2023-03-13 11:10:45'),(2,'83FC0037-1240-5F72-AB4F-BD1255F9DFB6','22-LN-124',2,'Nikka','N','Morrison','2023-03-24',2,34,'12 brgy Talaib, Nancayasan',0.00,0.00,'','','2023-03-05 10:41:26','2023-03-18 08:30:47'),(3,'D65E90A4-39CA-5C9B-6050-542645A38FB3','22-LN-125',1,'Kim','Jong','Un','2023-02-23',1,31,'No. 55 Ryongsong District, Nuclear State, Pyongyang, North Korea',180.00,160.00,'0855760292','Jang Sung-taek','2023-03-05 10:41:26','2023-03-07 15:02:33'),(4,'416333CB-5737-28B5-DB8A-4FCBB4B29BC5','22-LN-126',1,'Justine','Drew','Bieber','1994-03-01',1,29,'Quebec City Canada',50.00,178.00,'0968811452','Selena Gomez','2023-03-05 10:41:26','2023-03-13 10:57:36'),(5,'382EFF1D-3BCB-6814-9759-375384575C2E','22-LN-122',1,'Freddie','Queen','Mercury','2023-12-19',1,14,'34 brgy Calsib, Aguilar Pangasinan',78.00,0.00,'','Freddy Aguilar','2023-03-05 10:41:26','2023-03-07 15:19:42'),(7,'74BED227-2AD2-1C2C-3DC6-938D59FB99D0','22-LN-120',2,'Harry','Barney','Roque','2023-03-15',1,14,'139 Cordon, Isabela, Philippines',0.00,0.00,'','','2023-03-05 10:41:26','2023-03-09 15:28:34'),(8,'941EF897-8170-320D-9B7C-8AC5443B2AE8','22-LN-119',3,'Handy','Valderrama','Manny','2023-03-22',1,14,'334 Dalton Pass, Santa Fe, Nueva Vizcaya, Cagayan Valley',0.00,0.00,'','','2023-03-05 10:41:26','2023-03-07 15:37:43'),(12,'8607B714-75BF-37A4-935D-35D21B6A6D65','22-LN-145',2,'Vladimir','Vladimirovich','Putin','1952-10-07',1,70,'6, Akademika Zelinskogo Street, Saint Petersburg, Russia U.S.S.R.',77.00,167.64,'749560636','Joseph Stalin','2023-03-05 10:41:26','2023-03-11 04:32:43'),(13,'482CF467-D946-9501-E9C6-BD0A9D03B8DA','22-LN-128',2,'Pat','Turner','Felipe','2023-11-01',1,52,'72 Rio de Janeiro, Brazil',78.00,164.00,'1234','Alejandro Felipe','2023-03-05 10:41:26','2023-03-13 10:57:24'),(14,'36536BE7-337C-EE89-13C4-99C0622A7556','18-LN-001',3,'Seok','han','Yeon','1957-07-18',1,76,'235 North Uranium',85.00,0.00,'1234','Rasputin','2023-03-05 14:32:36','2023-03-12 06:51:07'),(22,'7DF9D111-BB7B-1D54-FBF7-7D2A2CC3005B','19-LN-005',1,'Allan','Peter','Cayetano','2020-05-13',1,16,'10 brgy. Diez Mil, Familia',0.00,0.00,'','','2023-03-05 15:28:13','2023-03-13 11:15:00'),(26,'6EB39D17-976A-5FC4-93B6-C6277C619691','22-LN-135',1,'Ylvis','E','Presley','2023-03-16',2,45,'',85.00,160.00,'','','2023-03-14 05:41:04','2023-03-18 08:31:33'),(27,'6E641BCF-20C7-9619-A869-DB5AE2F81B64','23-LN-146',1,'Charlie','Franco','Candelaria','1989-07-20',1,31,'455 brgy Maingal',68.00,150.00,'0855760292','Rasputin','2023-03-14 06:01:02','2023-03-16 14:08:08'),(29,'AF83297A-9D9B-CB8B-8D1A-002037817D0D','22-LN-1290',1,'Elon','Reeve','Musk','1987-01-08',1,76,'',0.00,0.00,'','','2023-03-14 06:44:15','2023-03-14 06:44:15'),(30,'C9080592-590D-EEE0-1C3D-F668021C302A','18-LN-0017',2,'Sam','Gabryelle','Junio','1995-01-04',1,34,'',0.00,0.00,'','','2023-03-14 06:48:16','2023-03-14 06:48:16'),(31,'8C9BF974-D04E-3E80-9A2C-22C6FBB79AA5','23-LN-0019',3,'Arnie','P','Centino','2023-03-10',1,30,'',0.00,0.00,'','','2023-03-14 06:49:20','2023-05-05 15:41:16'),(32,'8E02291C-D770-103F-5455-FB7269C67764','18-LN-0012',1,'Louise','Catunggal','Sarmiento','1988-12-09',2,24,'',0.00,0.00,'','','2023-03-14 07:07:12','2023-03-18 04:37:42'),(33,'92B4CE9B-1BBE-09BF-175C-C2815935B770','23-LN-109',2,'Iwasaki','Yochi','Takeo','1995-02-03',1,27,'',0.00,0.00,'','','2023-03-14 08:04:18','2023-03-14 08:04:18'),(34,'BEC8875F-0C66-0486-811F-19E892872C53','22-LN-103',1,'Shimizu','Eri','Ueda','1982-03-13',2,23,'',0.00,0.00,'','','2023-03-14 08:05:31','2023-03-14 08:05:31'),(38,'8A82C15A-52D7-8B3D-9E8D-087F6482750B','11',1,'Kyle','Mataraki','Marcopolo','2023-03-15',1,45,'123 brgy Marakep',90.00,150.00,'1234','Alejandro Felipe','2023-03-15 14:33:38','2023-03-22 07:44:43'),(39,'32C97BFB-663F-DA50-C3A7-46411842F24C','19-LN-004',1,'Paolo','Corsair','Piccolo','1995-03-10',1,18,'',0.00,0.00,'','','2023-03-16 14:09:42','2023-03-16 14:09:42'),(40,'B8949D93-0E58-D641-3FFA-B624E2B296AB','19-LN-003',1,'Jo Anne','Da Silva','Gonzales','1997-03-14',2,26,'',0.00,0.00,'','','2023-03-16 14:18:07','2023-03-16 14:18:07'),(41,'C4C7D9EC-96AF-4FD6-CCE3-14942019BEB1','19-LN-006',3,'Jonathan','G','Alvarez','1995-04-14',1,25,'',0.00,0.00,'','','2023-03-16 14:20:54','2023-03-16 14:20:54'),(42,'1DCE7C5F-A505-13D8-3776-36F40ED7CE5E','19-LN-007',1,'Miguel','A','Luis','2000-10-19',1,22,'455 brgy Maingal',0.00,0.00,'','','2023-03-16 14:22:53','2023-03-16 14:22:53'),(43,'FDD60B9F-6F0F-90E6-2063-282B699FA78D','18-LN-003',1,'Nash','Managpunas','Vintana','2003-04-11',1,22,'',0.00,0.00,'','','2023-03-16 14:24:40','2023-03-16 14:24:40'),(44,'69D88D77-05A5-4917-4B15-C5128C643A2B','18-LN-002225',3,'Annie','Managpalar','Aguilar','2013-04-19',2,22,'',0.00,0.00,'','','2023-03-16 14:26:05','2023-05-09 06:11:52'),(45,'4EB1F986-FFCE-81DC-7CC8-771A41723FC7','20-LN-001',1,'Inggo','Tamos','Dumaralos','2006-03-10',1,14,'',0.00,0.00,'1234','','2023-03-16 14:27:27','2023-03-16 14:27:27'),(46,'C0817252-0A69-4948-E4DA-039D8DEF5CB8','20-LN-005',3,'Niko','Mekaniko','Crudo','1991-01-25',1,24,'679 brgy Ambalingit',80.00,167.00,'1234','MrBean','2023-03-16 14:28:33','2023-03-22 14:54:26'),(67,'','12',1,'Goo','anDrew','Yun','2023-03-15',1,52,'',0.00,0.00,'','','2023-03-24 16:01:31','2023-03-24 16:01:31'),(68,'','144',1,'Jong','Han','Sung','2023-03-07',1,76,'',0.00,0.00,'','','2023-03-30 08:59:31','2023-05-05 15:41:27'),(76,'','22-LN-12376',1,'Tomas','Wilson','Ryder','2023-04-19',1,18,'',68.00,0.00,'','','2023-04-04 05:34:43','2023-04-04 05:34:43'),(78,'','1144',1,'Juan','Dela','cruz','2023-04-28',1,14,'',90.00,0.00,'','','2023-04-15 09:28:12','2023-04-15 09:28:12'),(79,'','110',2,'Sung','Lee','Jung','2007-04-11',1,14,'',0.00,0.00,'','','2023-04-20 05:47:08','2023-04-20 05:47:08'),(80,'','19-ln-1111',1,'Kenneth','Cabradilla','Cordero','2023-04-12',1,23,'magtaking',48.00,147.00,'098876543221','renato','2023-04-21 04:02:22','2023-05-05 15:42:26'),(81,'','145',1,'Leah','Margaux','dela Vega','2023-04-24',2,14,'',0.00,0.00,'','','2023-04-27 15:21:23','2023-04-27 15:21:23'),(84,'','003',1,'King','K','Cordel','2008-03-13',1,15,'',0.00,0.00,'','','2023-05-04 10:19:11','2023-05-05 05:39:46'),(89,'','000335',1,'Rendon','R','Labador','2000-02-05',1,23,'asdasdasd',0.00,0.00,'','','2023-05-05 16:34:04','2023-05-05 16:34:04'),(96,'','19-LN-0057',1,'Calvin','R','Harris','2023-05-11',1,0,'611 Dublin Scotland',0.00,0.00,'','','2023-05-09 17:39:15','2023-05-09 17:39:15'),(97,'','19-LN-0056',1,'John','Dave','Aquino','2023-05-05',1,0,'922, Purok 7 St. Bari, Mangaldan, Pangasinan',0.00,0.00,'','','2023-05-09 17:39:51','2023-05-09 17:39:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Youthplus Enterprises','+639171685894','#143 Calzita Building, Zamora Street., Cainta, Rizal','wecare@youthplusmedicalgroup.com',''),(2,'Abbott Laboratories','0123','San Francisco Bay Northern California','abbott_usa@gmail.com',''),(3,'GiantMed Engineering Services','0285166877','3 Dalton Pass Extension, Luzon Ave, Brgy. Holy Spirit, Quezon City, Metro Manila','giantmed@yahoo.com','GIANTMED ENGINEERING SERVICES, was formed to meet the growing demand for medical gas pipeline system and air-conditioning system, ventilation, mechanical and electrical. The Company that specialize in Installation of Medical Gas Pipeline System and Supplier of Oxygen Generating Plant, Oxygen refilling Plant, Vacuum Plant, Medical Air Compressor, Nurse Call System and Various supply and services that meet the DOH standard of supply and installation as well as independent third party contractors that checks and monitor the installation of all the supply that meet ISO standard of quality and International regulatory system'),(4,'Sacramento medical supplies trading','0434068610','23 s castillo st panganiban corner, Barangay 5, Tanuan City, Batangas','sacramenttrading@gmail.com','Products\r\nSyringes, cottons, sirgical consumables, mask, ppe, gloves, medical safety supplies, respirators, alcohol, tissue, micropore tapes, guage pads, catheter, goggles prescription frame and lens\r\nPRODUCT LIST  3M VFLEX 9105  3M  8210  3M RESPIRATORS, FILTERS AND RETAINERS• 3 PLY SURGICAL MASK  KN95 MASK • KF94 MASK  FACE SHIELD (ACRYLIC, WITH FOAM) • NITRILE GLOVES/VIWL GLOVES/LATEX GLOVES   ISOLATION GOWN (DISPOSABLE / NON-DISPOSABLEMEDICAL AND INDUSTRIAL)  LABORATORY GOWNS • SHOE COVER AND BOUFFANT CAP (MEDICAL AND INDUSTRIAL)  PPE CZHONGKA, WEI BANG, DUPONT AND ULTITEC)• GOGGLES (MEDICAL, AIRTIGHT, SPECTACLE INDUSTRIAL)  DISINFECTANTS ALCOHOLi '),(5,'Premiere Medical and Cardiovascular Laboratory, Inc.','0924351702','RII Building 136 Malakas Street, Brgy. Central, Quezon CIty, Metro Manila','@premierelabincph.com','N/A'),(6,'Aziacare Medical Trading and Services','09434889265','Arellano Street, Brgy Pantal, Dagupan City, Pangasinan','aziacaredepot@gmail.com','Aziacare Medical Trading and Services is duly licensed by the Philippine Food and Drug Administration (FDA) as Medical Devices & Drug/Pharmaceuticals Wholesaler/Retailer, Mechanical Ventilator Leasing Services, and Drugstore has consistently passed the requirements of the contract by various client/agencies with outstanding ratings. Aziacare Medical Trading and Services is licensed by Philippine FDA, Aziacare Medical Trading and Services handles vaccines, biologics and other temperature-sensitive drug products subject to Post Licensing Inspection in compliance with the Cold Chain Management requirements.'),(7,'Glaxo Smith Kline (GSK)','09966734','#123 brgy Marakep, Masanting City, Maaliguas','gsk_ph@gmail.com',''),(8,'Oxford-Astra','0992','Los Angeles California','aztravaxx@gmail.com',''),(9,'Pfizer-BioNTech','0993','235 East 42nd Street, New York, NY 10017','pfizer_bointech@gmail.com',''),(10,'Sanofi Pasteur','0994','14 Espace Henry Vallee, Lyon, France','sanofi.fr.eu@gmail.com','Sanofi Pasteur is the vaccines division of the French multinational pharmaceutical company Sanofi. Sanofi Pasteur is the largest company in the world devoted entirely to vaccines.'),(11,'Janssen Labs','0995','Area 51, Las Vegas, Nevada','jnj@gmail.com',''),(13,'Nissin','0','','',''),(14,'Astellas Pharma Philippines Inc.','0122','','',''),(15,'Moderna','090909','#200 Tech Square, Cambridge, Massachusetts','modernatx.com',''),(29,'ACI Pharma Inc','0124','','aci_pharm.com',''),(30,'Blaine Group of Companies','0125','','','');
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

-- Dump completed on 2023-05-13 19:10:44
