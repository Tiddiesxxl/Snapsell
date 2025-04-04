-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2024 at 07:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snapsell`
--

-- --------------------------------------------------------

-- First, drop any existing tables in reverse order of dependencies
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `download_history`;
DROP TABLE IF EXISTS `media_purchases`;
DROP TABLE IF EXISTS `user_activity_log`;
DROP TABLE IF EXISTS `event_analytics`;
DROP TABLE IF EXISTS `collection_analytics`;
DROP TABLE IF EXISTS `media_views`;
DROP TABLE IF EXISTS `ticket_purchases`;
DROP TABLE IF EXISTS `event_tickets`;
DROP TABLE IF EXISTS `media_access_rights`;
DROP TABLE IF EXISTS `collection_sharing`;
DROP TABLE IF EXISTS `media_items`;
DROP TABLE IF EXISTS `collections`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `photographer_profiles`;
DROP TABLE IF EXISTS `verification_codes`;
DROP TABLE IF EXISTS `user_sessions`;
DROP TABLE IF EXISTS `user_profiles`;
DROP TABLE IF EXISTS `temp_users`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- Now create tables in correct order (parent tables first)

-- 1. Core User Tables
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `user_type` ENUM('regular', 'photographer', 'admin') DEFAULT 'regular',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `social_media` JSON DEFAULT NULL,
  `preferences` JSON DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `temp_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Events Table (needed before collections)
CREATE TABLE `events` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `organizer_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_type` VARCHAR(100),
    `start_datetime` DATETIME NOT NULL,
    `end_datetime` DATETIME NOT NULL,
    `venue_name` VARCHAR(255),
    `address` TEXT,
    `max_capacity` INT,
    `status` ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'draft',
    `cover_image_path` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Collections Table
CREATE TABLE `collections` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `creator_id` INT NOT NULL,
    `event_id` INT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `cover_image_id` INT,
    `access_type` ENUM('public', 'private', 'password_protected') NOT NULL,
    `access_password` VARCHAR(255),
    `download_enabled` BOOLEAN DEFAULT TRUE,
    `watermark_enabled` BOOLEAN DEFAULT TRUE,
    `expiry_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`creator_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Media Items Table
CREATE TABLE `media_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `creator_id` INT NOT NULL,
    `collection_id` INT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `media_type` ENUM('photo', 'video') NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `thumbnail_path` VARCHAR(255),
    `original_filename` VARCHAR(255),
    `file_size` INT,
    `metadata` JSON,
    `price` DECIMAL(10,2),
    `is_downloadable` BOOLEAN DEFAULT TRUE,
    `watermark_path` VARCHAR(255),
    `status` ENUM('active', 'draft', 'archived') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`creator_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Collection Related Tables
CREATE TABLE `collection_sharing` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `collection_id` INT NOT NULL,
    `shared_email` VARCHAR(255) NOT NULL,
    `access_code` VARCHAR(255),
    `expires_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `media_access_rights` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `collection_id` INT NOT NULL,
    `access_type` ENUM('view', 'download', 'purchase') NOT NULL,
    `granted_via` ENUM('ticket', 'direct_share', 'purchase') NOT NULL,
    `expires_at` DATETIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Event Related Tables
CREATE TABLE `event_tickets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `ticket_type` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `quantity_available` INT NOT NULL,
    `quantity_sold` INT DEFAULT 0,
    `sale_start_date` DATETIME,
    `sale_end_date` DATETIME,
    `includes_photo_access` BOOLEAN DEFAULT FALSE,
    `photo_access_duration` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ticket_purchases` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `ticket_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `buyer_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'completed', 'cancelled', 'refunded') NOT NULL,
    `ticket_code` VARCHAR(255) UNIQUE,
    `purchase_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ticket_id`) REFERENCES `event_tickets`(`id`),
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`),
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Analytics Tables
CREATE TABLE `media_views` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `media_id` INT NOT NULL,
    `user_id` INT,
    `view_duration` INT,
    `device_type` VARCHAR(50),
    `browser` VARCHAR(100),
    `ip_address` VARCHAR(45),
    `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`media_id`) REFERENCES `media_items`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `collection_analytics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `collection_id` INT NOT NULL,
    `total_views` INT DEFAULT 0,
    `unique_visitors` INT DEFAULT 0,
    `total_downloads` INT DEFAULT 0,
    `total_purchases` INT DEFAULT 0,
    `revenue` DECIMAL(10,2) DEFAULT 0,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event_analytics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `event_id` INT NOT NULL,
    `tickets_sold` INT DEFAULT 0,
    `revenue_generated` DECIMAL(10,2) DEFAULT 0,
    `page_views` INT DEFAULT 0,
    `unique_visitors` INT DEFAULT 0,
    `conversion_rate` DECIMAL(5,2),
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Activity and Purchase Tracking
CREATE TABLE `user_activity_log` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `activity_type` ENUM('view', 'download', 'purchase', 'share', 'comment', 'like'),
    `target_type` ENUM('media', 'collection', 'event', 'profile'),
    `target_id` INT,
    `occurred_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `metadata` JSON,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `media_purchases` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `buyer_id` INT NOT NULL,
    `media_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `license_type` VARCHAR(50),
    `download_count` INT DEFAULT 0,
    `purchase_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`media_id`) REFERENCES `media_items`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `download_history` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `media_id` INT NOT NULL,
    `download_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`media_id`) REFERENCES `media_items`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Photographer Specific Tables
CREATE TABLE `photographer_profiles` (
    `user_id` INT PRIMARY KEY,
    `business_name` VARCHAR(255),
    `portfolio_url` VARCHAR(255),
    `specialties` TEXT,
    `equipment` TEXT,
    `pricing_info` JSON,
    `booking_availability` JSON,
    `watermark_path` VARCHAR(255),
    `default_license_terms` TEXT,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `packages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `photographer_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `includes_json` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`photographer_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `bookings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `client_id` INT NOT NULL,
    `photographer_id` INT NOT NULL,
    `package_id` INT,
    `event_date` DATETIME NOT NULL,
    `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`photographer_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 10. Create Analytics Views
CREATE OR REPLACE VIEW revenue_analytics AS
SELECT 
    DATE(mp.purchase_date) as date,
    u.id as creator_id,
    u.name as creator_name,
    COUNT(mp.id) as total_sales,
    SUM(mp.amount) as total_revenue,
    AVG(mp.amount) as average_sale_price
FROM media_purchases mp
JOIN media_items mi ON mp.media_id = mi.id
JOIN users u ON mi.creator_id = u.id
GROUP BY DATE(mp.purchase_date), u.id, u.name;

CREATE OR REPLACE VIEW collection_performance AS
SELECT 
    c.id,
    c.title,
    c.creator_id,
    COUNT(DISTINCT mv.user_id) as unique_viewers,
    COUNT(mv.id) as total_views,
    COUNT(DISTINCT mp.id) as total_purchases,
    SUM(mp.amount) as total_revenue
FROM collections c
LEFT JOIN media_items mi ON mi.collection_id = c.id
LEFT JOIN media_views mv ON mv.media_id = mi.id
LEFT JOIN media_purchases mp ON mp.media_id = mi.id
GROUP BY c.id, c.title, c.creator_id;

-- Add verification_codes table that was in your original schema
CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add useful indexes for performance
CREATE INDEX idx_media_items_status ON media_items(status);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_collections_access_type ON collections(access_type);
CREATE INDEX idx_ticket_purchases_status ON ticket_purchases(status);
CREATE INDEX idx_media_views_viewed_at ON media_views(viewed_at);
CREATE INDEX idx_user_activity_occurred_at ON user_activity_log(occurred_at);

COMMIT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `temp_users`
--
ALTER TABLE `temp_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `temp_users`
--
ALTER TABLE `temp_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `verification_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
