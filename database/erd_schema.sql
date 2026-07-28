-- SQL schema generated from Laravel migrations
-- Final schema for the government services application

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'resident',
  `language` varchar(255) NOT NULL DEFAULT 'en',
  `avatar` varchar(255) NULL DEFAULT NULL,
  `dob` date NULL DEFAULT NULL,
  `address` text NULL,
  `civil_status` varchar(255) NULL DEFAULT NULL,
  `contact_number` varchar(255) NULL DEFAULT NULL,
  `valid_id_path` varchar(255) NULL DEFAULT NULL,
  `remember_token` varchar(100) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `description` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `government_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NULL DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `procedure` text NULL,
  `icon` varchar(255) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `government_services_category_id_foreign` (`category_id`),
  CONSTRAINT `government_services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `procedure` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_translations_service_id_foreign` (`service_id`),
  CONSTRAINT `service_translations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `requirement_text` json NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_requirements_service_id_foreign` (`service_id`),
  CONSTRAINT `service_requirements_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `document_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `requirement_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ceb` varchar(255) NOT NULL,
  `description_en` text NULL,
  `description_ceb` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_templates_service_id_foreign` (`service_id`),
  KEY `document_templates_requirement_id_foreign` (`requirement_id`),
  CONSTRAINT `document_templates_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_templates_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `service_requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_languages_user_id_foreign` (`user_id`),
  CONSTRAINT `user_languages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_inquiries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NULL DEFAULT NULL,
  `service_id` bigint unsigned NULL DEFAULT NULL,
  `guest_name` varchar(255) NULL DEFAULT NULL,
  `guest_email` varchar(255) NULL DEFAULT NULL,
  `inquiry_text` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_inquiries_user_id_foreign` (`user_id`),
  KEY `user_inquiries_service_id_foreign` (`service_id`),
  CONSTRAINT `user_inquiries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_inquiries_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inquiry_requirenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inquiry_id` bigint unsigned NOT NULL,
  `requireent_text` text NOT NULL,
  `responded_by` bigint unsigned NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_requirenses_inquiry_id_foreign` (`inquiry_id`),
  KEY `inquiry_requirenses_responded_by_foreign` (`responded_by`),
  CONSTRAINT `inquiry_requirenses_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `user_inquiries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inquiry_requirenses_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eligibility_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `question_text_en` text NOT NULL,
  `question_text_ceb` text NOT NULL,
  `question_text_fil` text NOT NULL,
  `question_text_sub` text NULL,
  `type` varchar(255) NOT NULL DEFAULT 'boolean',
  `expected_value` varchar(255) NOT NULL,
  `operator` varchar(255) NOT NULL DEFAULT '==',
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eligibility_questions_service_id_foreign` (`service_id`),
  CONSTRAINT `eligibility_questions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eligibility_assessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ineligible',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eligibility_assessments_user_id_foreign` (`user_id`),
  KEY `eligibility_assessments_service_id_foreign` (`service_id`),
  CONSTRAINT `eligibility_assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eligibility_assessments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assessment_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_answers_assessment_id_foreign` (`assessment_id`),
  CONSTRAINT `assessment_answers_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `eligibility_assessments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `remarks` text NULL,
  `application_type` varchar(255) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_checklists_user_id_foreign` (`user_id`),
  KEY `user_checklists_service_id_foreign` (`service_id`),
  CONSTRAINT `user_checklists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_checklists_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_checklist_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint unsigned NOT NULL,
  `requirement_id` bigint unsigned NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(255) NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_checklist_items_checklist_id_foreign` (`checklist_id`),
  KEY `user_checklist_items_requirement_id_foreign` (`requirement_id`),
  CONSTRAINT `user_checklist_items_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `user_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_checklist_items_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `service_requirements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reassessment_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reassessment_requests_user_id_foreign` (`user_id`),
  KEY `reassessment_requests_service_id_foreign` (`service_id`),
  CONSTRAINT `reassessment_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reassessment_requests_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `common_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NULL DEFAULT NULL,
  `question_text` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_questions_service_id_foreign` (`service_id`),
  CONSTRAINT `common_questions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `government_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
