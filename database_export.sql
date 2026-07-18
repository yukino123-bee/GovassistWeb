/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: gov
-- ------------------------------------------------------
-- Server version	11.8.8-MariaDB

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
-- Table structure for table `assessment_answers`
--

DROP TABLE IF EXISTS `assessment_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `assessment_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_answers_assessment_id_foreign` (`assessment_id`),
  CONSTRAINT `assessment_answers_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `eligibility_assessments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_answers`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `assessment_answers` WRITE;
/*!40000 ALTER TABLE `assessment_answers` DISABLE KEYS */;
INSERT INTO `assessment_answers` VALUES
(3,2,'What is your family\'s monthly household income?','15000','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(4,2,'Are you currently enrolled in an accredited school or college?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(5,2,'Do you have any failing grades from the previous semester?','false','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(6,2,'Are you a resident of this municipality for at least 6 months?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(7,2,'Are you currently a recipient of any other government scholarship or financial assistance?','false','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(8,2,'Are you a Filipino citizen?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(9,2,'Are you a registered voter or a child of a registered voter in this municipality?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(10,2,'Do you possess a Certificate of Good Moral Character from your current/last school?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(11,2,'Are you currently employed in a full-time capacity?','false','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(12,2,'Do you have any parents currently working as regular government employees?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(13,2,'Have you ever been convicted of any crime or offense?','false','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(14,2,'Are you willing to render at least 20 hours of community service per semester?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(15,2,'Is your general weighted average (GWA) from the previous semester 85% or higher?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(16,2,'Are you transferring from a school outside this municipality?','true','2026-07-18 01:32:32','2026-07-18 01:32:32'),
(17,2,'Can you provide a valid Certificate of Indigency from your Barangay?','true','2026-07-18 01:32:32','2026-07-18 01:32:32');
/*!40000 ALTER TABLE `assessment_answers` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `document_templates`
--

DROP TABLE IF EXISTS `document_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `requirement_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ceb` varchar(255) NOT NULL,
  `description_en` text DEFAULT NULL,
  `description_ceb` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_templates_service_id_foreign` (`service_id`),
  KEY `document_templates_requirement_id_foreign` (`requirement_id`),
  CONSTRAINT `document_templates_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `service_requirements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_templates_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_templates`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `document_templates` WRITE;
/*!40000 ALTER TABLE `document_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_templates` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `eligibility_assessments`
--

DROP TABLE IF EXISTS `eligibility_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `eligibility_assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ineligible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eligibility_assessments_user_id_foreign` (`user_id`),
  KEY `eligibility_assessments_service_id_foreign` (`service_id`),
  CONSTRAINT `eligibility_assessments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eligibility_assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eligibility_assessments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `eligibility_assessments` WRITE;
/*!40000 ALTER TABLE `eligibility_assessments` DISABLE KEYS */;
INSERT INTO `eligibility_assessments` VALUES
(2,15,1,'ineligible','2026-07-18 01:32:32','2026-07-18 01:32:32');
/*!40000 ALTER TABLE `eligibility_assessments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `eligibility_questions`
--

DROP TABLE IF EXISTS `eligibility_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `eligibility_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `question_text_en` text NOT NULL,
  `question_text_ceb` text NOT NULL,
  `question_text_fil` text NOT NULL,
  `question_text_sub` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'boolean',
  `expected_value` varchar(255) NOT NULL,
  `operator` varchar(255) NOT NULL DEFAULT '==',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eligibility_questions_service_id_foreign` (`service_id`),
  CONSTRAINT `eligibility_questions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eligibility_questions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `eligibility_questions` WRITE;
/*!40000 ALTER TABLE `eligibility_questions` DISABLE KEYS */;
INSERT INTO `eligibility_questions` VALUES
(1,1,'What is your family\'s monthly household income?','Pila ang binuwan nga kita sa inyong panimalay?','Magkano ang buwanang kita ng inyong pamilya?',NULL,'number','15000','<=','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(2,1,'Are you currently enrolled in an accredited school or college?','Kasamtangan ba ikaw nga naka-enrol sa usa ka akreditadong eskwelahan o kolehiyo?','Kasalukuyan ka bang nag-aaral sa isang kinikilalang paaralan?',NULL,'boolean','true','==','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(7,1,'Do you have any failing grades from the previous semester?','Aduna ba kay mga hagbong nga grado gikan sa miaging semester?','Mayroon ka bang mga bagsak na grado mula sa nakaraang semestre?',NULL,'boolean','false','==','2026-07-18 01:24:07','2026-07-18 01:24:07'),
(8,1,'Are you a resident of this municipality for at least 6 months?','Residente ba ikaw niini nga lungsod sulod sa labing menos 6 ka bulan?','Ikaw ba ay residente ng munisipalidad na ito ng hindi bababa sa 6 na buwan?',NULL,'boolean','true','==','2026-07-18 01:24:07','2026-07-18 01:24:07'),
(9,1,'Are you currently a recipient of any other government scholarship or financial assistance?','Nakadawat ba ikaw karon og bisan unsang laing scholarship o pinansyal nga tabang gikan sa gobyerno?','Kasalukuyan ka bang tumatanggap ng anumang ibang scholarship o tulong pinansyal mula sa gobyerno?',NULL,'boolean','false','==','2026-07-18 01:24:07','2026-07-18 01:29:22'),
(10,1,'Are you a Filipino citizen?','Usa ba ikaw ka lungsoranon sa Pilipinas?','Ikaw ba ay isang mamamayang Pilipino?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(11,1,'Are you a registered voter or a child of a registered voter in this municipality?','Rehistrado ka ba nga botante o anak sa usa ka rehistradong botante niini nga lungsod?','Ikaw ba ay rehistradong botante o anak ng isang rehistradong botante sa munisipalidad na ito?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(12,1,'Do you possess a Certificate of Good Moral Character from your current/last school?','Aduna ba kay Certificate of Good Moral Character gikan sa imong kasamtangan/katapusan nga eskwelahan?','Mayroon ka bang Sertipiko ng Mabuting Asal mula sa iyong kasalukuyan/huling paaralan?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(13,1,'Are you currently employed in a full-time capacity?','Aduna ba kay full-time nga trabaho karon?','Kasalukuyan ka bang may full-time na trabaho?',NULL,'boolean','false','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(14,1,'Do you have any parents currently working as regular government employees?','Aduna ba kay ginikanan nga nagtrabaho isip regular nga empleyado sa gobyerno?','Mayroon ka bang mga magulang na kasalukuyang nagtatrabaho bilang regular na empleyado ng gobyerno?',NULL,'boolean','false','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(15,1,'Have you ever been convicted of any crime or offense?','Nakonbikto na ba ikaw sa bisan unsang krimen o kalapasan?','Nahatulan ka na ba sa anumang krimen o pagkakasala?',NULL,'boolean','false','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(16,1,'Are you willing to render at least 20 hours of community service per semester?','Andam ka ba nga mohatag og labing menos 20 ka oras nga serbisyo sa komunidad matag semester?','Handa ka bang magbigay ng hindi bababa sa 20 oras ng serbisyo sa komunidad bawat semestre?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(17,1,'Is your general weighted average (GWA) from the previous semester 85% or higher?','Ang imong general weighted average (GWA) ba gikan sa miaging semester kay 85% o pataas?','Ang iyong pangkalahatang average (GWA) ba mula sa nakaraang semestre ay 85% o mas mataas?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(18,1,'Are you transferring from a school outside this municipality?','Nagbalhin ba ikaw gikan sa usa ka eskwelahan sa gawas niini nga lungsod?','Lumilipat ka ba mula sa isang paaralan sa labas ng munisipalidad na ito?',NULL,'boolean','false','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(19,1,'Can you provide a valid Certificate of Indigency from your Barangay?','Makahatag ba ikaw og balido nga Certificate of Indigency gikan sa imong Barangay?','Maaari ka bang magbigay ng balidong Sertipiko ng Kahirapan mula sa inyong Barangay?',NULL,'boolean','true','==','2026-07-18 01:27:22','2026-07-18 01:27:22'),
(20,2,'Where is the patient currently admitted or receiving treatment?','Asa man ang pasyente nga gi-admit o nagpatambal karon?','Saan kasalukuyang tinatanggap o tumatanggap ng paggamot ang pasyente?','Asa man ang pasyente nga gi-admit o nagpatambal karon?','text','N/A','==','2026-07-18 01:52:26','2026-07-18 01:59:37'),
(21,2,'When did the incident occur, or when did the medical condition/illness start?','Kanus-a nahitabo ang insidente, o kanus-a nagsugod ang medikal nga kondisyon/sakit?','Kailan nangyari ang insidente, o kailan nagsimula ang medikal na kondisyon/sakit?','Kanus-a nahitabo ang insidente, o kanus-a nagsugod ang medikal nga kondisyon/sakit?','text','N/A','==','2026-07-18 01:52:29','2026-07-18 02:13:36'),
(22,2,'What is the reason why the patient got hospitalized?','Unsa ang rason nganong na-ospital ang pasyente?','Ano ang dahilan kung bakit na-ospital ang pasyente?','Unsa ang rason nganong na-ospital ang pasyente?','text','N/A','==','2026-07-18 01:52:32','2026-07-18 02:02:36'),
(23,2,'Does the patient have a valid hospital bill or promissory note?','Aduna bay balidong bayronon sa ospital o promissory note ang pasyente?','Mayroon bang balidong bill sa ospital o promissory note ang pasyente?','Aduna bay balidong bayronon sa ospital o promissory note ang pasyente?','boolean','true','==','2026-07-18 01:52:36','2026-07-18 02:08:56'),
(24,2,'Is the patient a registered resident of the city/municipality?','Rehistrado ba ang pasyente sa siyudad/munisipyo?','Ang pasyente ba ay rehistradong residente ng lungsod/munisipyo?','Rehistrado ba ang pasyente sa siyudad/munisipyo?','boolean','true','==','2026-07-18 01:52:40','2026-07-18 01:52:40'),
(25,2,'Is the assistance for dialysis treatment?','Para ba sa dialysis treatment ang tabang?','Para ba sa dialysis treatment ang tulong?','Para ba sa dialysis treatment ang tabang?','boolean','true','==','2026-07-18 01:52:44','2026-07-18 02:08:56'),
(26,2,'Is the assistance for chemotherapy sessions?','Para ba sa chemotherapy session ang tabang?','Para ba sa chemotherapy session ang tulong?','Para ba sa chemotherapy session ang tabang?','boolean','true','==','2026-07-18 01:52:47','2026-07-18 02:08:56'),
(27,2,'Is the assistance for maintenance medicines?','Para ba sa maintenance nga tambal ang tabang?','Para ba sa maintenance na gamot ang tulong?','Para ba sa maintenance nga tambal ang tabang?','boolean','true','==','2026-07-18 01:52:50','2026-07-18 02:08:56'),
(28,2,'Does the patient belong to the indigent sector based on MSWDO assessment?','Nahisakop ba ang pasyente sa indigent sector base sa MSWDO assessment?','Kabilang ba ang pasyente sa indigent sector base sa MSWDO assessment?','Nahisakop ba ang pasyente sa indigent sector base sa MSWDO assessment?','boolean','true','==','2026-07-18 01:52:53','2026-07-18 02:08:56'),
(29,2,'Has the patient received medical assistance from us in the last 3 months?','Nakadawat ba og medikal nga tabang ang pasyente gikan namo sa miaging 3 ka bulan?','Nakatanggap ba ng medikal na tulong ang pasyente mula sa amin sa nakalipas na 3 buwan?','Nakadawat ba og medikal nga tabang ang pasyente gikan namo sa miaging 3 ka bulan?','boolean','true','==','2026-07-18 01:52:57','2026-07-18 02:08:56'),
(30,2,'Is the patient a senior citizen or a Person with Disability (PWD)?','Ang pasyente ba usa ka senior citizen o Person with Disability (PWD)?','Ang pasyente ba ay isang senior citizen o Person with Disability (PWD)?','Ang pasyente ba usa ka senior citizen o Person with Disability (PWD)?','boolean','true','==','2026-07-18 01:53:00','2026-07-18 02:08:56'),
(31,2,'Does the patient have an active PhilHealth membership?','Aduna bay aktibong PhilHealth membership ang pasyente?','May aktibo bang PhilHealth membership ang pasyente?','Aduna bay aktibong PhilHealth membership ang pasyente?','boolean','true','==','2026-07-18 01:53:02','2026-07-18 02:08:56'),
(32,2,'Is the requested assistance for a surgical operation?','Ang gipangayo ba nga tabang alang sa operasyon sa pag-opera?','Ang hinihinging tulong ba para sa operasyon ng kirurhiko?','Ang gipangayo ba nga tabang alang sa operasyon sa pag-opera?','boolean','true','==','2026-07-18 01:53:06','2026-07-18 01:53:06'),
(33,2,'Does the claimant/representative have a valid government-issued ID?','Aduna bay balidong government-issued ID ang claimant/representante?','Mayroon bang balidong government-issued ID ang claimant/kinatawan?','Aduna bay balidong government-issued ID ang claimant/representante?','boolean','true','==','2026-07-18 01:53:09','2026-07-18 02:08:56'),
(34,2,'Is the patient\'s monthly household income less than Php 12,000?','Ubos ba sa Php 12,000 ang binuwan nga kita sa pamilya sa pasyente?','Mababa ba sa Php 12,000 ang buwanang kita ng pamilya ng pasyente?','Ubos ba sa Php 12,000 ang binuwan nga kita sa pamilya sa pasyente?','boolean','true','==','2026-07-18 01:53:12','2026-07-18 02:08:56'),
(35,3,'Is the deceased an immediate family member (parent, spouse, child)?','Ang namatay ba usa ka membro sa pamilya (ginikanan, kapikas, anak)?','Ang namatay ba ay isang malapit na miyembro ng pamilya (magulang, asawa, anak)?','Ang namatay ba usa ka membro sa pamilya (ginikanan, kapikas, anak)?','boolean','true','==','2026-07-18 01:53:16','2026-07-18 01:53:16'),
(36,3,'Do you have the original registered Death Certificate?','Aduna ka bay orihinal nga narehistro nga Death Certificate?','Mayroon ka bang orihinal na rehistradong Death Certificate?','Aduna ka bay orihinal nga narehistro nga Death Certificate?','boolean','true','==','2026-07-18 01:53:18','2026-07-18 01:53:18'),
(37,3,'Did the death occur within the last 30 days?','Nahitabo ba ang kamatayon sa miaging 30 ka adlaw?','Naganap ba ang kamatayan sa loob ng huling 30 araw?','Nahitabo ba ang kamatayon sa miaging 30 ka adlaw?','boolean','true','==','2026-07-18 01:53:20','2026-07-18 01:53:20'),
(38,3,'Do you have a funeral service contract or statement of account?','Aduna ka bay kontrata sa serbisyo sa paglubong o statement of account?','Mayroon ka bang kontrata sa serbisyo ng libing o statement of account?','Aduna ka bay kontrata sa serbisyo sa paglubong o statement of account?','boolean','true','==','2026-07-18 01:53:24','2026-07-18 01:53:24'),
(39,3,'Was the deceased a registered resident of this municipality?','Rehistrado ba nga lumulupyo sa maong lungsod ang namatay?','Ang namatay ba ay isang rehistradong residente ng munisipyong ito?','Rehistrado ba nga lumulupyo sa maong lungsod ang namatay?','boolean','true','==','2026-07-18 01:53:27','2026-07-18 01:53:27'),
(40,3,'Do you have a Barangay Certificate of Indigency?','Aduna ka bay Barangay Certificate of Indigency?','May Barangay Certificate of Indigency ka ba?','Aduna ka bay Barangay Certificate of Indigency?','boolean','true','==','2026-07-18 01:53:30','2026-07-18 01:53:30'),
(41,3,'Are you the person who directly paid for the funeral expenses?','Ikaw ba ang tawo nga direktang nagbayad sa mga galastohan sa paglubong?','Ikaw ba ang taong direktang nagbayad para sa mga gastusin sa libing?','Ikaw ba ang tawo nga direktang nagbayad sa mga galastohan sa paglubong?','boolean','true','==','2026-07-18 01:53:33','2026-07-18 01:53:33'),
(42,3,'Where is the wake or funeral currently being held?','Asa kasamtangang gihimo ang haya o lamay?','Saan kasalukuyang ginaganap ang burol o lamay?','Asa kasamtangang gihimo ang haya o lamay?','text','N/A','==','2026-07-18 01:53:36','2026-07-18 02:29:35'),
(43,3,'Are you requesting assistance for embalming or casket costs?','Nangayo ka ba og tabang alang sa mga gasto sa pag-embalsamar o lungon?','Humihiling ka ba ng tulong para sa mga gastos sa pag-embalsamo o kabaong?','Nangayo ka ba og tabang alang sa mga gasto sa pag-embalsamar o lungon?','boolean','true','==','2026-07-18 01:53:40','2026-07-18 01:53:40'),
(44,3,'Are you requesting assistance for cemetery lot or niche rental?','Nangayo ka ba og tabang para sa lote sa sementeryo o pag-abang sa niche?','Humihingi ka ba ng tulong para sa lote o niche rental?','Nangayo ka ba og tabang para sa lote sa sementeryo o pag-abang sa niche?','boolean','true','==','2026-07-18 01:53:43','2026-07-18 01:53:43'),
(45,3,'Are you an active member of any local burial association or cooperative?','Aktibo ka ba nga miyembro sa bisan unsang lokal nga asosasyon sa paglubong o kooperatiba?','Ikaw ba ay isang aktibong miyembro ng anumang lokal na asosasyon o kooperatiba?','Aktibo ka ba nga miyembro sa bisan unsang lokal nga asosasyon sa paglubong o kooperatiba?','boolean','true','==','2026-07-18 01:53:46','2026-07-18 01:53:46'),
(46,3,'Do you have a valid government-issued ID of the claimant?','Aduna ka bay balido nga government-issued ID sa nag-angkon?','Mayroon ka bang valid na government-issued ID ng claimant?','Aduna ka bay balido nga government-issued ID sa nag-angkon?','boolean','true','==','2026-07-18 01:53:48','2026-07-18 01:53:48'),
(47,3,'Is your monthly household income less than Php 10,000?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 10,000?','Mas mababa ba sa Php 10,000 ang iyong buwanang kita ng sambahayan?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 10,000?','boolean','true','==','2026-07-18 01:53:50','2026-07-18 01:53:50'),
(48,3,'Have you received any burial assistance from DSWD for this deceased?','Nakadawat ka ba og burial assistance gikan sa DSWD alang niining namatay?','Nakatanggap ka na ba ng burial assistance mula sa DSWD para sa namatay na ito?','Nakadawat ka ba og burial assistance gikan sa DSWD alang niining namatay?','boolean','true','==','2026-07-18 01:53:53','2026-07-18 01:53:53'),
(49,3,'Are you willing to sign a waiver of non-duplication of claims?','Andam ka ba nga mopirma sa usa ka waiver sa non-duplication of claims?','Handa ka bang pumirma ng waiver ng hindi pagdoble ng mga claim?','Andam ka ba nga mopirma sa usa ka waiver sa non-duplication of claims?','boolean','true','==','2026-07-18 01:53:56','2026-07-18 01:53:56'),
(50,4,'Are you stranded and need to return to your home province?','Na-stranded ka ba ug kinahanglang muuli sa imong probinsya?','Na-stranded ka ba at kailangan nang bumalik sa iyong probinsya?','Na-stranded ka ba ug kinahanglang muuli sa imong probinsya?','boolean','true','==','2026-07-18 01:53:59','2026-07-18 01:53:59'),
(51,4,'Do you have a referral letter from MSWDO or local officials?','Aduna ka bay referral letter gikan sa MSWDO o lokal nga opisyal?','Mayroon ka bang referral letter mula sa MSWDO o mga lokal na opisyal?','Aduna ka bay referral letter gikan sa MSWDO o lokal nga opisyal?','boolean','true','==','2026-07-18 01:54:03','2026-07-18 01:54:03'),
(52,4,'Is your travel due to a medical emergency or hospital referral?','Ang imong pagbiyahe tungod ba sa usa ka medikal nga emerhensya o referral sa ospital?','Dahil ba sa isang medikal na emergency o referral sa ospital ang iyong paglalakbay?','Ang imong pagbiyahe tungod ba sa usa ka medikal nga emerhensya o referral sa ospital?','boolean','true','==','2026-07-18 01:54:06','2026-07-18 01:54:06'),
(53,4,'Are you traveling to seek employment outside the municipality?','Nagbiyahe ka ba aron mangita og trabaho sa gawas sa munisipyo?','Naglalakbay ka ba para maghanap ng trabaho sa labas ng munisipyo?','Nagbiyahe ka ba aron mangita og trabaho sa gawas sa munisipyo?','boolean','true','==','2026-07-18 01:54:09','2026-07-18 01:54:09'),
(54,4,'Have you secured a valid Police Clearance or NBI Clearance?','Nakakuha ka na ba ug balido nga Police Clearance o NBI Clearance?','Nakakuha ka na ba ng valid Police Clearance o NBI Clearance?','Nakakuha ka na ba ug balido nga Police Clearance o NBI Clearance?','boolean','true','==','2026-07-18 01:54:12','2026-07-18 01:54:12'),
(55,4,'Do you have a valid government-issued ID?','Aduna ka bay balido nga ID nga gihatag sa gobyerno?','Mayroon ka bang valid na government-issued ID?','Aduna ka bay balido nga ID nga gihatag sa gobyerno?','boolean','true','==','2026-07-18 01:54:14','2026-07-18 01:54:14'),
(56,4,'Is your intended destination within the Philippines?','Sulod ba sa Pilipinas ang imong tuyo nga destinasyon?','Nasa Pilipinas ba ang iyong hinahangad na destinasyon?','Sulod ba sa Pilipinas ang imong tuyo nga destinasyon?','boolean','true','==','2026-07-18 01:54:17','2026-07-18 01:54:17'),
(57,4,'Are you a victim of a recent calamity or disaster?','Biktima ka ba sa bag-o lang nga kalamidad o katalagman?','Biktima ka ba ng kamakailang kalamidad o sakuna?','Biktima ka ba sa bag-o lang nga kalamidad o katalagman?','boolean','true','==','2026-07-18 01:54:19','2026-07-18 01:54:19'),
(58,4,'Are you a rescued victim of human trafficking or abuse?','Naluwas ka ba nga biktima sa human trafficking o pag-abuso?','Isa ka bang nailigtas na biktima ng human trafficking o pang-aabuso?','Naluwas ka ba nga biktima sa human trafficking o pag-abuso?','boolean','true','==','2026-07-18 01:54:23','2026-07-18 01:54:23'),
(59,4,'Have you received transportation assistance from us in the past 6 months?','Nakadawat ka ba og tabang sa transportasyon gikan kanamo sa miaging 6 ka bulan?','Nakatanggap ka ba ng tulong sa transportasyon mula sa amin sa nakalipas na 6 na buwan?','Nakadawat ka ba og tabang sa transportasyon gikan kanamo sa miaging 6 ka bulan?','boolean','true','==','2026-07-18 01:54:26','2026-07-18 01:54:26'),
(60,4,'Are you traveling alone?','Nag-inusara ka ba nga nagbiyahe?','Ikaw ba ay naglalakbay mag-isa?','Nag-inusara ka ba nga nagbiyahe?','boolean','true','==','2026-07-18 01:54:28','2026-07-18 01:54:28'),
(61,4,'Do you have a Barangay Certificate of Indigency?','Naa kay Barangay Certificate of Indigency?','May Barangay Certificate of Indigency ka ba?','Naa kay Barangay Certificate of Indigency?','boolean','true','==','2026-07-18 01:54:32','2026-07-18 01:54:32'),
(62,4,'Are you a senior citizen or a Person with Disability (PWD)?','Senior citizen ka ba o Person with Disability (PWD)?','Ikaw ba ay isang senior citizen o isang Person with Disability (PWD)?','Senior citizen ka ba o Person with Disability (PWD)?','boolean','true','==','2026-07-18 01:54:33','2026-07-18 01:54:33'),
(63,4,'Are you willing to undergo an interview with our social worker?','Andam ka ba nga mopailalom sa interbyu sa among social worker?','Handa ka bang sumailalim sa isang pakikipanayam sa aming social worker?','Andam ka ba nga mopailalom sa interbyu sa among social worker?','boolean','true','==','2026-07-18 01:54:37','2026-07-18 01:54:37'),
(64,4,'Is your monthly household income less than Php 8,000?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 8,000?','Mas mababa ba sa Php 8,000 ang iyong buwanang kita ng sambahayan?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 8,000?','boolean','true','==','2026-07-18 01:54:40','2026-07-18 01:54:40'),
(65,5,'Are you currently unemployed or underemployed?','Ikaw ba karon walay trabaho o kulang sa trabaho?','Ikaw ba ay kasalukuyang walang trabaho o kulang sa trabaho?','Ikaw ba karon walay trabaho o kulang sa trabaho?','boolean','true','==','2026-07-18 01:54:42','2026-07-18 01:54:42'),
(66,5,'Are you between the ages of 18 and 60?','Ikaw ba tali sa edad nga 18 ug 60?','Ikaw ba ay nasa pagitan ng edad na 18 at 60?','Ikaw ba tali sa edad nga 18 ug 60?','boolean','true','==','2026-07-18 01:54:45','2026-07-18 01:54:45'),
(67,5,'Do you have a valid Resume or Biodata?','Aduna ka bay balido nga Resume o Biodata?','Mayroon ka bang valid na Resume o Biodata?','Aduna ka bay balido nga Resume o Biodata?','boolean','true','==','2026-07-18 01:54:47','2026-07-18 01:54:47'),
(68,5,'Have you secured a Barangay Clearance for employment purposes?','Nakakuha ka na ba ug Barangay Clearance para sa katuyoan sa pagpanarbaho?','Nakakuha ka na ba ng Barangay Clearance para sa mga layunin ng trabaho?','Nakakuha ka na ba ug Barangay Clearance para sa katuyoan sa pagpanarbaho?','boolean','true','==','2026-07-18 01:54:50','2026-07-18 01:54:50'),
(69,5,'Do you have a valid Police Clearance or NBI Clearance?','Aduna ka bay balido nga Police Clearance o NBI Clearance?','Mayroon ka bang valid Police Clearance o NBI Clearance?','Aduna ka bay balido nga Police Clearance o NBI Clearance?','boolean','true','==','2026-07-18 01:54:53','2026-07-18 01:54:53'),
(70,5,'Have you completed at least high school or Alternative Learning System (ALS)?','Nakahuman ka ba sa labing menos high school o Alternative Learning System (ALS)?','Nakapagtapos ka na ba ng kahit high school o Alternative Learning System (ALS)?','Nakahuman ka ba sa labing menos high school o Alternative Learning System (ALS)?','boolean','true','==','2026-07-18 01:54:56','2026-07-18 01:54:56'),
(71,5,'Are you applying for a starter kit or capital assistance?','Nag-aplay ka ba alang sa usa ka starter kit o tabang sa kapital?','Nag-a-apply ka ba para sa isang starter kit o tulong sa kapital?','Nag-aplay ka ba alang sa usa ka starter kit o tabang sa kapital?','boolean','true','==','2026-07-18 01:54:58','2026-07-18 01:54:58'),
(72,5,'Are you a solo parent seeking livelihood support?','Usa ka ba ka solo nga ginikanan nga nangita og suporta sa panginabuhi?','Isa ka bang solo parent na naghahanap ng livelihood support?','Usa ka ba ka solo nga ginikanan nga nangita og suporta sa panginabuhi?','boolean','true','==','2026-07-18 01:55:02','2026-07-18 01:55:02'),
(73,5,'Are you a returning Overseas Filipino Worker (OFW)?','Usa ka ba ka nibalik nga Overseas Filipino Worker (OFW)?','Isa ka bang nagbabalik na Overseas Filipino Worker (OFW)?','Usa ka ba ka nibalik nga Overseas Filipino Worker (OFW)?','boolean','true','==','2026-07-18 01:55:05','2026-07-18 01:55:05'),
(74,5,'Are you willing to attend a mandatory livelihood training seminar?','Andam ka ba nga motambong sa mandatory livelihood training seminar?','Handa ka bang dumalo sa isang mandatory livelihood training seminar?','Andam ka ba nga motambong sa mandatory livelihood training seminar?','boolean','true','==','2026-07-18 01:55:09','2026-07-18 01:55:09'),
(75,5,'Do you have a business plan or project proposal (for capital assistance)?','Aduna ka bay plano sa negosyo o proposal sa proyekto (alang sa tabang sa kapital)?','Mayroon ka bang plano sa negosyo o panukalang proyekto (para sa tulong sa kapital)?','Aduna ka bay plano sa negosyo o proposal sa proyekto (alang sa tabang sa kapital)?','boolean','true','==','2026-07-18 01:55:12','2026-07-18 01:55:12'),
(76,5,'Have you received livelihood assistance from us in the past 12 months?','Nakadawat ka ba og livelihood assistance gikan kanamo sa miaging 12 ka bulan?','Nakatanggap ka ba ng tulong pangkabuhayan mula sa amin sa nakalipas na 12 buwan?','Nakadawat ka ba og livelihood assistance gikan kanamo sa miaging 12 ka bulan?','boolean','true','==','2026-07-18 01:55:14','2026-07-18 01:55:14'),
(77,5,'Are you a member of a registered cooperative or workers\' association?','Miyembro ka ba sa usa ka rehistradong kooperatiba o asosasyon sa mga mamumuo?','Miyembro ka ba ng isang rehistradong kooperatiba o asosasyon ng mga manggagawa?','Miyembro ka ba sa usa ka rehistradong kooperatiba o asosasyon sa mga mamumuo?','boolean','true','==','2026-07-18 01:55:17','2026-07-18 01:55:17'),
(78,5,'Do you have a valid government-issued ID?','Aduna ka bay balido nga ID nga gihatag sa gobyerno?','Mayroon ka bang valid na government-issued ID?','Aduna ka bay balido nga ID nga gihatag sa gobyerno?','boolean','true','==','2026-07-18 01:55:20','2026-07-18 01:55:20'),
(79,5,'Is your monthly household income less than Php 15,000?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 15,000?','Mas mababa ba sa Php 15,000 ang iyong buwanang kita ng sambahayan?','Ang imong binuwan nga kita sa panimalay ubos ba sa Php 15,000?','boolean','true','==','2026-07-18 01:55:24','2026-07-18 01:55:24'),
(80,2,'What is your relationship to the patient? (e.g. self, spouse, child, representative)','Unsa ang imong relasyon sa pasyente? (pananglitan: kaugalingon, asawa/bana, anak, representante)','Ano ang iyong relasyon sa pasyente? (halimbawa: sarili, asawa, anak, kinatawan)','Unsa ang imong relasyon sa pasyente? (pananglitan: kaugalingon, asawa/bana, anak, representante)','text','N/A','==','2026-07-18 02:08:56','2026-07-18 02:08:56');
/*!40000 ALTER TABLE `eligibility_questions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `government_services`
--

DROP TABLE IF EXISTS `government_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `government_services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `procedure` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `government_services_category_id_foreign` (`category_id`),
  CONSTRAINT `government_services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `government_services`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `government_services` WRITE;
/*!40000 ALTER TABLE `government_services` DISABLE KEYS */;
INSERT INTO `government_services` VALUES
(1,1,'Educational Assistance','Educational Assistance Provides financial aid, scholarships, tuition support, school supplies, or other educational benefits to eligible students to help them continue their studies and reduce the cost of education.','1. Submit required documents to the SSFO office.\n2. Complete the Eligibility Assessment.\n3. Wait for validation and approval of application.\n4. Claim financial assistance during payout scheduling.','assets/icons/civil.png','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(2,1,'Medical Assistance','Provides financial assistance to eligible individuals to help cover medical expenses, including hospitalization, laboratory tests, medicines, surgical procedures, and other necessary healthcare services.','1. Submit Hospital Bill or Medical Certificate.\n2. Undergo assessment by facilitator.\n3. Approved requests will receive guarantee letters or financial payouts.','assets/icons/civil.png','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(3,1,'Burial Assistance','Provides financial assistance to the family or authorized representative of a deceased individual to help cover funeral, burial, and other related expenses.','1. Present Death Certificate and Funeral Contract.\n2. Fill out social case study report.\n3. Receive financial assistance for burial expenses.','assets/icons/civil.png','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(4,1,'Transportation','Provides financial assistance to eligible individuals who require transportation support for medical treatment, education, employment, emergencies, or other essential travel needs.','1. Present travel referral or endorsement.\n2. Submit indigency certification.\n3. Receive travel allowance or tickets.','assets/icons/civil.png','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(5,1,'Employment','Provides assistance to qualified individuals seeking employment by supporting job application requirements and facilitating access to employment opportunities.','1. Register in the employment database.\n2. Attend skills training workshops.\n3. Get matched with local government or private job placement offers.','assets/icons/civil.png','2026-07-16 17:09:52','2026-07-16 17:09:52');
/*!40000 ALTER TABLE `government_services` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `inquiry_requirenses`
--

DROP TABLE IF EXISTS `inquiry_requirenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inquiry_requirenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint(20) unsigned NOT NULL,
  `requireent_text` text NOT NULL,
  `responded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_requirenses_inquiry_id_foreign` (`inquiry_id`),
  KEY `inquiry_requirenses_responded_by_foreign` (`responded_by`),
  CONSTRAINT `inquiry_requirenses_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `user_inquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiry_requirenses_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inquiry_requirenses`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `inquiry_requirenses` WRITE;
/*!40000 ALTER TABLE `inquiry_requirenses` DISABLE KEYS */;
INSERT INTO `inquiry_requirenses` VALUES
(2,2,'I\'m sorry, I didn\'t quite understand your query. You can ask about our programs: **Educational, Medical, Burial, Transportation, or Employment** assistance, and their required documents.',1,'2026-07-16 19:58:03','2026-07-16 19:58:03');
/*!40000 ALTER TABLE `inquiry_requirenses` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'2026_07_15_000000_create_gov_system_tables',1),
(3,'2026_07_17_010754_create_document_templates_table',2),
(4,'2026_07_17_013823_alter_user_inquiries_add_guest_fields',3),
(5,'2026_07_18_093552_create_reassessment_requests_table',4),
(6,'2026_07_18_094048_add_subanen_to_eligibility_questions_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `reassessment_requests`
--

DROP TABLE IF EXISTS `reassessment_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reassessment_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reassessment_requests_user_id_foreign` (`user_id`),
  KEY `reassessment_requests_service_id_foreign` (`service_id`),
  CONSTRAINT `reassessment_requests_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reassessment_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reassessment_requests`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `reassessment_requests` WRITE;
/*!40000 ALTER TABLE `reassessment_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `reassessment_requests` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `service_categories`
--

DROP TABLE IF EXISTS `service_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `service_categories` WRITE;
/*!40000 ALTER TABLE `service_categories` DISABLE KEYS */;
INSERT INTO `service_categories` VALUES
(1,'Civil Registry','Scholarships, tuition support, and allowances.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(2,'Licenses & Permits','Support for hospitalization, medicines, and surgical procedures.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(3,'Burial Assistance','Financial aid for casket, funeral services, and burial costs.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(4,'Transportation Assistance','Referrals and fare support for emergency displacement/travel.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(5,'Employment Assistance','Skills training, livelihood support, and job matching.','2026-07-16 17:09:52','2026-07-16 17:09:52');
/*!40000 ALTER TABLE `service_categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `service_requirements`
--

DROP TABLE IF EXISTS `service_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_requirements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `requirement_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`requirement_text`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_requirements_service_id_foreign` (`service_id`),
  CONSTRAINT `service_requirements_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_requirements`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `service_requirements` WRITE;
/*!40000 ALTER TABLE `service_requirements` DISABLE KEYS */;
INSERT INTO `service_requirements` VALUES
(1,1,'{\"en\":\"School ID\",\"ceb\":\"School ID\",\"fil\":\"School ID ng Mag-aaral\"}',1,1,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(2,1,'{\"en\":\"Certificate of Enrollment\",\"ceb\":\"Sertipiko sa Pagpa-enrol\",\"fil\":\"Sertipiko ng Pagpapatala\"}',1,2,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(3,1,'{\"en\":\"Certificate of Indigency\",\"ceb\":\"Sertipiko sa Kakabus\",\"fil\":\"Sertipiko ng Katunayan ng Kahirapan\"}',1,3,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(4,2,'{\"en\":\"Medical Certificate\",\"ceb\":\"Sertipiko sa Medikal\",\"fil\":\"Sertipiko Medikal\"}',1,1,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(5,2,'{\"en\":\"Hospital Bill or Quotation\",\"ceb\":\"Bayranan sa Ospital o Quotation\",\"fil\":\"Kabayaran sa Ospital o Reseta\"}',1,2,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(6,2,'{\"en\":\"Barangay Certificate of Indigency\",\"ceb\":\"Sertipiko sa Kakabus sa Barangay\",\"fil\":\"Sertipiko ng Barangay Indigency\"}',1,3,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(7,3,'{\"en\":\"Registered Death Certificate\",\"ceb\":\"Rehistradong Death Certificate\",\"fil\":\"Rehistradong Sertipiko ng Kamatayan\"}',1,1,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(8,3,'{\"en\":\"Funeral Contract\",\"ceb\":\"Kontrata sa Punerarya\",\"fil\":\"Kontrata sa Punerarya\"}',1,2,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(9,4,'{\"en\":\"Referral Letter \\/ Endorsement\",\"ceb\":\"Sulat sa Referral \\/ Endorsement\",\"fil\":\"Liham ng Pagre-refer o Endorsement\"}',1,1,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(10,4,'{\"en\":\"Valid ID of Traveler\",\"ceb\":\"Gibalido nga ID sa Mobyahi\",\"fil\":\"Balidong ID ng Manlalakbay\"}',1,2,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(11,5,'{\"en\":\"PSA Birth Certificate\",\"ceb\":\"PSA Birth Certificate\",\"fil\":\"Sertipiko ng Kapanganakan mula sa PSA\"}',1,1,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(12,5,'{\"en\":\"Resume \\/ Bio-Data\",\"ceb\":\"Resume \\/ Bio-Data\",\"fil\":\"Resume o Bio-data\"}',1,2,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(13,5,'{\"en\":\"Barangay Clearance\",\"ceb\":\"Barangay Clearance\",\"fil\":\"Barangay Clearance\"}',0,3,'2026-07-16 17:09:52','2026-07-16 17:09:52'),
(14,1,'{\"en\":\"Statement of Account (Private Schools Only)\"}',1,0,NULL,NULL),
(15,1,'{\"en\":\"Certificate of Enrollment\"}',1,0,NULL,NULL),
(16,1,'{\"en\":\"Certificate of Registration (COR)\"}',1,0,NULL,NULL),
(17,1,'{\"en\":\"Barangay Certificate of Indigency\"}',1,0,NULL,NULL),
(18,1,'{\"en\":\"School ID\"}',1,0,NULL,NULL),
(19,1,'{\"en\":\"One (1) Valid Government-Issued ID\"}',1,0,NULL,NULL),
(20,1,'{\"en\":\"Latest Grades\"}',1,0,NULL,NULL),
(21,2,'{\"en\":\"Medical Certificate\"}',1,0,NULL,NULL),
(22,2,'{\"en\":\"Barangay Certificate of Indigency\"}',1,0,NULL,NULL),
(23,2,'{\"en\":\"One (1) Valid Government-Issued ID of the Applicant\"}',1,0,NULL,NULL),
(24,2,'{\"en\":\"One (1) Valid Government-Issued ID of the Patient\"}',1,0,NULL,NULL),
(25,2,'{\"en\":\"Hospital Bill or Statement of Account\"}',1,0,NULL,NULL),
(26,2,'{\"en\":\"Authorization Letter\"}',1,0,NULL,NULL),
(27,2,'{\"en\":\"Letter of Request\"}',1,0,NULL,NULL),
(28,2,'{\"en\":\"Social Case Study Report\\/Form (MSWDO)\"}',1,0,NULL,NULL),
(29,3,'{\"en\":\"Death Certificate\"}',1,0,NULL,NULL),
(30,3,'{\"en\":\"Barangay Certificate of Indigency\"}',1,0,NULL,NULL),
(31,3,'{\"en\":\"One (1) Valid Government-Issued ID of the Applicant\"}',1,0,NULL,NULL),
(32,3,'{\"en\":\"Letter of Request\"}',1,0,NULL,NULL),
(33,3,'{\"en\":\"Social Case Study Report\\/Form (MSWDO)\"}',1,0,NULL,NULL),
(34,5,'{\"en\":\"Personal Data Sheet (PDS)\"}',1,0,NULL,NULL),
(35,5,'{\"en\":\"Resume\"}',1,0,NULL,NULL),
(36,5,'{\"en\":\"Recommendation Letter\"}',1,0,NULL,NULL),
(37,5,'{\"en\":\"Endorsement Letter\"}',1,0,NULL,NULL);
/*!40000 ALTER TABLE `service_requirements` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `service_translations`
--

DROP TABLE IF EXISTS `service_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) unsigned NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `procedure` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_translations_service_id_foreign` (`service_id`),
  CONSTRAINT `service_translations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_translations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `service_translations` WRITE;
/*!40000 ALTER TABLE `service_translations` DISABLE KEYS */;
INSERT INTO `service_translations` VALUES
(1,1,'en','Educational Assistance Program','Provides financial aid, scholarships, tuition support, and educational subsidies for underprivileged students.','1. Submit required documents to the SSFO office.\n2. Complete the Eligibility Assessment.\n3. Wait for validation and approval of application.\n4. Claim financial assistance during payout scheduling.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(2,1,'ceb','Tabang sa Edukasyon','Naghatag og pinansyal nga tabang, mga scholarship, suporta sa matrikula, ug mga subsidyo sa edukasyon alang sa mga nanginahanglan nga estudyante.','1. Isumite ang gikinahanglan nga mga dokumento sa opisina sa SSFO.\n2. Kompletoha ang Eligibility Assessment.\n3. Paghulat sa pag-validate ug pag-apruba sa aplikasyon.\n4. I-claim ang pinansyal nga tabang sa panahon sa gieskedyul nga payout.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(3,1,'fil','Tulong sa Edukasyon','Nagbibigay ng pinansyal na tulong, iskolarsip, suporta sa matrikula, at edukasyonal na subsidyo para sa mga kapus-palad na mag-aaral.','1. Isumite ang mga kinakailangang dokumento sa opisina ng SSFO.\n2. Kumpletuhin ang Eligibility Assessment.\n3. Maghintay para sa pagpapatunay at pag-apruba ng aplikasyon.\n4. Kunin ang tulong pinansyal sa nakatakdang oras.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(4,2,'en','Medical Assistance Program','Provides financial assistance to eligible individuals to help cover medical expenses, including hospital bills, medicine, and treatments.','1. Submit Hospital Bill or Medical Certificate.\n2. Undergo assessment by facilitator.\n3. Approved requests will receive guarantee letters or financial payouts.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(5,2,'ceb','Tabang sa Medikal','Naghatag og pinansyal nga tabang sa mga kwalipikadong indibidwal aron matabangan ang pagtabon sa mga gasto sa medikal, lakip ang mga bayranan sa ospital, tambal, ug mga pagtambal.','1. Isumite ang Hospital Bill o Medical Certificate.\n2. Moagi sa assessment sa facilitator.\n3. Ang giaprobahan nga mga hangyo makadawat og garantiya nga mga sulat o pinansyal nga payout.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(6,2,'fil','Tulong Medikal','Nagbibigay ng pinansyal na tulong sa mga kwalipikadong indibidwal upang matugunan ang mga gastusing medikal, kabilang ang bayad sa ospital, gamot, at paggamot.','1. Isumite ang Hospital Bill o Medical Certificate.\n2. Sumailalim sa pagsusuri ng facilitator.\n3. Ang mga naaprubahang aplikante ay makakatanggap ng guarantee letter o tulong pinansyal.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(7,3,'en','Burial Assistance Program','Provides financial assistance to the family or authorized representative of a deceased individual to cover funeral and burial costs.','1. Present Death Certificate and Funeral Contract.\n2. Fill out social case study report.\n3. Receive financial assistance for burial expenses.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(8,3,'ceb','Tabang sa Pagpalubong','Naghatag og pinansyal nga tabang sa pamilya o awtorisado nga representante sa namatay nga indibidwal aron matabonan ang gasto sa punerarya ug pagpalubong.','1. Ipakita ang Death Certificate ug Funeral Contract.\n2. Sulati ang social case study report.\n3. Makadawat og pinansyal nga tabang alang sa gasto sa pagpalubong.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(9,3,'fil','Tulong sa Libing','Nagbibigay ng tulong pinansyal sa pamilya ng namatay upang makatulong sa mga gastusin sa libing at punerarya.','1. Ipakita ang Death Certificate at Funeral Contract.\n2. Sagutan ang social case study report.\n3. Tanggapin ang tulong pinansyal para sa libing.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(10,4,'en','Transportation Assistance Program','Provides financial assistance to eligible individuals needing emergency travel support for medical, employment, or emergency displacement.','1. Present travel referral or endorsement.\n2. Submit indigency certification.\n3. Receive travel allowance or tickets.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(11,4,'ceb','Tabang sa Transportasyon','Naghatag og pinansyal nga tabang sa mga kwalipikadong indibidwal nga nanginahanglan og dinalian nga suporta sa pagbiyahe alang sa medikal, trabaho, o emerhensya nga pagbakwit.','1. Ipakita ang travel referral o endorsement.\n2. Isumite ang indigency certification.\n3. Makadawat og travel allowance o mga tiket.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(12,4,'fil','Tulong sa Transportasyon','Nagbibigay ng tulong pinansyal para sa emergency na pamasahe o transportasyon para sa mga layuning medikal, trabaho, o iba pang kagipitan.','1. Ipakita ang travel referral o endorsement.\n2. Isumite ang indigency certification.\n3. Tanggapin ang travel allowance o ticket.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(13,5,'en','Employment and Livelihood Assistance','Provides assistance to job seekers, including livelihood support, skill training, and referral programs.','1. Register in the employment database.\n2. Attend skills training workshops.\n3. Get matched with local government or private job placement offers.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(14,5,'ceb','Tabang sa Trabaho','Naghatag og tabang sa mga nangita og trabaho, lakip ang suporta sa panginabuhian, pagbansay sa kahanas, ug mga programa sa referral.','1. Pagrehistro sa database sa trabaho.\n2. Pagtambong sa mga workshop sa pagbansay sa kahanas.\n3. I-match sa mga tanyag sa gobyerno o pribadong trabaho.','2026-07-16 17:09:52','2026-07-16 17:09:52'),
(15,5,'fil','Tulong sa Trabaho at Kabuhayan','Nagbibigay ng tulong sa mga naghahanap ng trabaho, kabilang ang suporta sa pangkabuhayan, pagsasanay, at mga programang referral.','1. Magparehistro sa database ng trabaho.\n2. Dumalo sa pagsasanay sa kasanayan.\n3. I-ugnay sa mga alok ng lokal na pamahalaan o pribadong kumpanya.','2026-07-16 17:09:52','2026-07-16 17:09:52');
/*!40000 ALTER TABLE `service_translations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user_checklist_items`
--

DROP TABLE IF EXISTS `user_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_checklist_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint(20) unsigned NOT NULL,
  `requirement_id` bigint(20) unsigned NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_checklist_items_checklist_id_foreign` (`checklist_id`),
  KEY `user_checklist_items_requirement_id_foreign` (`requirement_id`),
  CONSTRAINT `user_checklist_items_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `user_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_checklist_items_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `service_requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_checklist_items`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_checklist_items` WRITE;
/*!40000 ALTER TABLE `user_checklist_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user_checklists`
--

DROP TABLE IF EXISTS `user_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_checklists_user_id_foreign` (`user_id`),
  KEY `user_checklists_service_id_foreign` (`service_id`),
  CONSTRAINT `user_checklists_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_checklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_checklists`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_checklists` WRITE;
/*!40000 ALTER TABLE `user_checklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_checklists` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user_inquiries`
--

DROP TABLE IF EXISTS `user_inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_inquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `service_id` bigint(20) unsigned DEFAULT NULL,
  `inquiry_text` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_inquiries_user_id_foreign` (`user_id`),
  KEY `user_inquiries_service_id_foreign` (`service_id`),
  CONSTRAINT `user_inquiries_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_inquiries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_inquiries`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_inquiries` WRITE;
/*!40000 ALTER TABLE `user_inquiries` DISABLE KEYS */;
INSERT INTO `user_inquiries` VALUES
(2,15,NULL,NULL,NULL,'unsaon pag apply?','pending','2026-07-16 19:58:03','2026-07-16 19:58:03');
/*!40000 ALTER TABLE `user_inquiries` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `user_languages`
--

DROP TABLE IF EXISTS `user_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_languages_user_id_foreign` (`user_id`),
  CONSTRAINT `user_languages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_languages`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `user_languages` WRITE;
/*!40000 ALTER TABLE `user_languages` DISABLE KEYS */;
INSERT INTO `user_languages` VALUES
(1,1,'en',1,'2026-07-16 17:09:52','2026-07-16 17:09:52');
/*!40000 ALTER TABLE `user_languages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'citizen',
  `language` varchar(255) NOT NULL DEFAULT 'en',
  `avatar` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `valid_id_path` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Lanny M Cagatin','jedcagat@gmail.com','2026-07-17 03:23:32','$2y$12$tRh7hpucWDgks7qxJvEA8OP9dWIfFWemPmjEOmWSSakleYdILw/vi','facilitator','fil','avatars/kDZOnf6XWZwPZ6E1nyMXYbtfP0uV2coXmOv0apqO.jpg','1974-02-14','Fatima, San Miguel, Zamboanga del Sur','Married','09123129957',NULL,NULL,'2026-07-05 00:14:28','2026-07-18 02:35:12'),
(15,'Mark Jed M. Cagatin','cagatirarjed@gmail.com','2026-07-17 03:23:42','$2y$12$yWxQgCNnmlXf1UatKtB6Nu78eHDjRAHoro/QUy/5vDjuOskKKtPc6','citizen','en','avatars/ox1IgOyqX8YxCjNcGwKmshaYNLigtNHhBSCwHFlZ.jpg','2005-03-11','Fatima, San Miguel, Zamboanga del Sur','Single','09187439096',NULL,NULL,'2026-07-16 19:11:10','2026-07-18 01:46:08');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-07-18 19:07:12
