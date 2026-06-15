-- ============================================================
-- LawSphere Legal Management System - MySQL Database Schema
-- Laravel 12 | PHP 8+ | MySQL 8.0+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- users
-- Central authentication table for all roles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL,
    `email_verified_at`  TIMESTAMP NULL DEFAULT NULL,
    `password`          VARCHAR(255) NOT NULL,
    `role`              ENUM('admin', 'lawyer', 'client') NOT NULL DEFAULT 'client',
    `phone`             VARCHAR(20) NULL DEFAULT NULL,
    `profile_photo`     VARCHAR(255) NULL DEFAULT NULL,
    `address`           TEXT NULL DEFAULT NULL,
    `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
    `remember_token`    VARCHAR(100) NULL DEFAULT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- lawyers
-- Extended profile for lawyer users (1:1 with users)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lawyers` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           BIGINT UNSIGNED NOT NULL,
    `qualifications`    TEXT NULL DEFAULT NULL,
    `specialization`    VARCHAR(255) NOT NULL,
    `experience_years`  INT UNSIGNED NOT NULL DEFAULT 0,
    `biography`         TEXT NULL DEFAULT NULL,
    `bar_number`        VARCHAR(100) NULL DEFAULT NULL,
    `is_approved`       TINYINT(1) NOT NULL DEFAULT 0,
    `approved_at`       TIMESTAMP NULL DEFAULT NULL,
    `average_rating`    DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    `total_reviews`     INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lawyers_user_id_unique` (`user_id`),
    KEY `lawyers_specialization_index` (`specialization`),
    KEY `lawyers_is_approved_index` (`is_approved`),
    KEY `lawyers_average_rating_index` (`average_rating`),
    CONSTRAINT `lawyers_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- clients
-- Extended profile for client users (1:1 with users)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clients` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           BIGINT UNSIGNED NOT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clients_user_id_unique` (`user_id`),
    CONSTRAINT `clients_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- appointments
-- Consultation booking between clients and lawyers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `appointments` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id`             BIGINT UNSIGNED NOT NULL,
    `lawyer_id`             BIGINT UNSIGNED NOT NULL,
    `appointment_date`      DATE NOT NULL,
    `appointment_time`      TIME NOT NULL,
    `status`                ENUM('pending', 'approved', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `notes`                 TEXT NULL DEFAULT NULL,
    `cancellation_reason`   TEXT NULL DEFAULT NULL,
    `reschedule_reason`     TEXT NULL DEFAULT NULL,
    `rescheduled_at`         TIMESTAMP NULL DEFAULT NULL,
    `created_at`            TIMESTAMP NULL DEFAULT NULL,
    `updated_at`            TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `appointments_client_id_index` (`client_id`),
    KEY `appointments_lawyer_id_index` (`lawyer_id`),
    KEY `appointments_status_index` (`status`),
    KEY `appointments_date_index` (`appointment_date`),
    CONSTRAINT `appointments_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `appointments_lawyer_id_foreign`
        FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- legal_requests
-- Client-submitted legal advice / consultation requests
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `legal_requests` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id`         BIGINT UNSIGNED NOT NULL,
    `lawyer_id`         BIGINT UNSIGNED NULL DEFAULT NULL,
    `subject`           VARCHAR(255) NOT NULL,
    `description`       TEXT NOT NULL,
    `status`            ENUM('pending', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'pending',
    `attachment`        VARCHAR(255) NULL DEFAULT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `legal_requests_client_id_index` (`client_id`),
    KEY `legal_requests_lawyer_id_index` (`lawyer_id`),
    KEY `legal_requests_status_index` (`status`),
    CONSTRAINT `legal_requests_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `legal_requests_lawyer_id_foreign`
        FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- legal_responses
-- Lawyer responses to legal advice requests
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `legal_responses` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `legal_request_id`  BIGINT UNSIGNED NOT NULL,
    `lawyer_id`         BIGINT UNSIGNED NOT NULL,
    `response_text`     TEXT NOT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `legal_responses_legal_request_id_index` (`legal_request_id`),
    KEY `legal_responses_lawyer_id_index` (`lawyer_id`),
    CONSTRAINT `legal_responses_legal_request_id_foreign`
        FOREIGN KEY (`legal_request_id`) REFERENCES `legal_requests` (`id`) ON DELETE CASCADE,
    CONSTRAINT `legal_responses_lawyer_id_foreign`
        FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- reviews
-- Client ratings and reviews for lawyers after consultations
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id`         BIGINT UNSIGNED NOT NULL,
    `lawyer_id`         BIGINT UNSIGNED NOT NULL,
    `appointment_id`    BIGINT UNSIGNED NULL DEFAULT NULL,
    `rating`            TINYINT UNSIGNED NOT NULL,
    `review_text`       TEXT NULL DEFAULT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `reviews_client_appointment_unique` (`client_id`, `appointment_id`),
    KEY `reviews_lawyer_id_index` (`lawyer_id`),
    CONSTRAINT `reviews_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_lawyer_id_foreign`
        FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_appointment_id_foreign`
        FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
    CONSTRAINT `reviews_rating_check` CHECK (`rating` >= 1 AND `rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- memberships
-- Lawyer subscription / membership plans
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `memberships` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lawyer_id`         BIGINT UNSIGNED NOT NULL,
    `plan_name`         VARCHAR(100) NOT NULL,
    `start_date`        DATE NOT NULL,
    `end_date`          DATE NOT NULL,
    `status`            ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    `amount`            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `memberships_lawyer_id_index` (`lawyer_id`),
    KEY `memberships_status_index` (`status`),
    CONSTRAINT `memberships_lawyer_id_foreign`
        FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- notifications
-- In-app notifications for users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           BIGINT UNSIGNED NOT NULL,
    `type`              VARCHAR(100) NOT NULL,
    `title`             VARCHAR(255) NOT NULL,
    `message`           TEXT NOT NULL,
    `is_read`           TINYINT(1) NOT NULL DEFAULT 0,
    `data`              JSON NULL DEFAULT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_user_id_index` (`user_id`),
    KEY `notifications_is_read_index` (`is_read`),
    CONSTRAINT `notifications_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- activity_logs
-- System activity audit trail
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           BIGINT UNSIGNED NULL DEFAULT NULL,
    `action`            VARCHAR(100) NOT NULL,
    `model_type`        VARCHAR(255) NULL DEFAULT NULL,
    `model_id`          BIGINT UNSIGNED NULL DEFAULT NULL,
    `description`       TEXT NULL DEFAULT NULL,
    `ip_address`        VARCHAR(45) NULL DEFAULT NULL,
    `user_agent`        TEXT NULL DEFAULT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_id_index` (`user_id`),
    KEY `activity_logs_action_index` (`action`),
    KEY `activity_logs_model_index` (`model_type`, `model_id`),
    CONSTRAINT `activity_logs_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Laravel framework tables
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email`             VARCHAR(255) NOT NULL,
    `token`             VARCHAR(255) NOT NULL,
    `created_at`        TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
    `id`                VARCHAR(255) NOT NULL,
    `user_id`           BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address`        VARCHAR(45) NULL DEFAULT NULL,
    `user_agent`        TEXT NULL DEFAULT NULL,
    `payload`           LONGTEXT NOT NULL,
    `last_activity`     INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
    `key`               VARCHAR(255) NOT NULL,
    `value`             MEDIUMTEXT NOT NULL,
    `expiration`        INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key`               VARCHAR(255) NOT NULL,
    `owner`             VARCHAR(255) NOT NULL,
    `expiration`        INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue`             VARCHAR(255) NOT NULL,
    `payload`           LONGTEXT NOT NULL,
    `attempts`          TINYINT UNSIGNED NOT NULL,
    `reserved_at`       INT UNSIGNED NULL DEFAULT NULL,
    `available_at`      INT UNSIGNED NOT NULL,
    `created_at`        INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
    `id`                VARCHAR(255) NOT NULL,
    `name`              VARCHAR(255) NOT NULL,
    `total_jobs`        INT NOT NULL,
    `pending_jobs`      INT NOT NULL,
    `failed_jobs`       INT NOT NULL,
    `failed_job_ids`    LONGTEXT NOT NULL,
    `options`           MEDIUMTEXT NULL DEFAULT NULL,
    `cancelled_at`      INT NULL DEFAULT NULL,
    `created_at`        INT NOT NULL,
    `finished_at`       INT NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`              VARCHAR(255) NOT NULL,
    `connection`        TEXT NOT NULL,
    `queue`             TEXT NOT NULL,
    `payload`           LONGTEXT NOT NULL,
    `exception`         LONGTEXT NOT NULL,
    `failed_at`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
