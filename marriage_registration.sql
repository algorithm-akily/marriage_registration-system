-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 10:08 AM
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
-- Database: `marriage_registration`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `generate_application_number` () RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE app_num VARCHAR(50);
    SET app_num = CONCAT('APP-', DATE_FORMAT(NOW(), '%Y%m%d-'), LPAD(FLOOR(RAND() * 10000), 4, '0'));
    RETURN app_num;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `application_status_history`
--

CREATE TABLE `application_status_history` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `document_type` enum('id_proof','marriage_proof','affidavit','photo','other') DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marriage_applications`
--

CREATE TABLE `marriage_applications` (
  `id` int(11) NOT NULL,
  `application_number` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `groom_name` varchar(100) NOT NULL,
  `groom_dob` date DEFAULT NULL,
  `groom_id_type` varchar(50) DEFAULT NULL,
  `groom_id_number` varchar(50) DEFAULT NULL,
  `groom_address` text DEFAULT NULL,
  `groom_phone` varchar(20) DEFAULT NULL,
  `groom_email` varchar(100) DEFAULT NULL,
  `bride_name` varchar(100) NOT NULL,
  `bride_dob` date DEFAULT NULL,
  `bride_id_type` varchar(50) DEFAULT NULL,
  `bride_id_number` varchar(50) DEFAULT NULL,
  `bride_address` text DEFAULT NULL,
  `bride_phone` varchar(20) DEFAULT NULL,
  `bride_email` varchar(100) DEFAULT NULL,
  `marriage_date` date NOT NULL,
  `marriage_location` varchar(200) DEFAULT NULL,
  `marriage_type` enum('civil','religious','traditional') DEFAULT 'civil',
  `has_children` tinyint(1) DEFAULT 0,
  `number_of_children` int(11) DEFAULT 0,
  `current_status` enum('draft','submitted','pending','under_review','approved','rejected') DEFAULT 'draft',
  `payment_status` enum('pending','paid','waived') DEFAULT 'pending',
  `certificate_generated` tinyint(1) DEFAULT 0,
  `certificate_path` varchar(255) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `certificate_fee` decimal(10,2) DEFAULT 5000.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `status_notes` text DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `id_proof_path` varchar(255) DEFAULT NULL,
  `marriage_proof_path` varchar(255) DEFAULT NULL,
  `affidavit_path` varchar(255) DEFAULT NULL,
  `photos_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marriage_applications`
--

INSERT INTO `marriage_applications` (`id`, `application_number`, `user_id`, `groom_name`, `groom_dob`, `groom_id_type`, `groom_id_number`, `groom_address`, `groom_phone`, `groom_email`, `bride_name`, `bride_dob`, `bride_id_type`, `bride_id_number`, `bride_address`, `bride_phone`, `bride_email`, `marriage_date`, `marriage_location`, `marriage_type`, `has_children`, `number_of_children`, `current_status`, `payment_status`, `certificate_generated`, `certificate_path`, `certificate_number`, `certificate_fee`, `payment_method`, `payment_date`, `admin_notes`, `status_notes`, `registration_date`, `review_date`, `approval_date`, `id_proof_path`, `marriage_proof_path`, `affidavit_path`, `photos_path`, `created_at`, `updated_at`) VALUES
(1, 'APP-20260204-8424', 2, 'Adam Adam', NULL, NULL, NULL, '123 Main St, New York, NY', '555-1234', NULL, 'Hawa Eve', NULL, NULL, NULL, '123 Main St, New York, NY', '555-5678', NULL, '2023-06-15', 'New York City Hall', 'civil', 0, 0, 'approved', 'paid', 1, 'certificates/certificate_1_1770219014.html', 'MRC-2026-000001', 5000.00, NULL, NULL, 'remember thats for test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-04 13:24:00', '2026-02-04 15:30:14'),
(2, 'APP-20260205-6696', 6, 'johnson john', NULL, NULL, NULL, 'mozambiques', '0765456556', NULL, 'Aminiel seif', NULL, NULL, NULL, 'New york', '0657847777', NULL, '2026-02-08', 'Italy', 'civil', 0, 0, 'approved', 'paid', 1, 'certificates/certificate_2_1770265593.html', 'MRC-2026-000002', 5000.00, '0', '2026-02-05', 'wait a minutes', NULL, '2026-02-05', NULL, '2026-02-05', NULL, NULL, NULL, NULL, '2026-02-05 04:18:46', '2026-02-05 05:22:16');

--
-- Triggers `marriage_applications`
--
DELIMITER $$
CREATE TRIGGER `before_application_insert` BEFORE INSERT ON `marriage_applications` FOR EACH ROW BEGIN
    IF NEW.application_number IS NULL THEN
        SET NEW.application_number = generate_application_number();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Marriage Registration System', 'Website name', '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(2, 'site_email', 'info@marriageregistration.com', 'System email address', '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(3, 'max_file_size', '5242880', 'Maximum file upload size in bytes (5MB)', '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(4, 'allowed_file_types', 'pdf,jpg,jpeg,png,doc,docx', 'Allowed file extensions', '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(5, 'application_fee', '0', 'Application fee amount', '2026-02-04 13:16:45', '2026-02-04 13:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `role` enum('user','admin','staff') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `address`, `date_of_birth`, `id_type`, `id_number`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'akily', 'akilykaaya@gmail.com', '$2y$10$cUdgk/bXATn3AHd5/dQQCOZ6uT3bHKv0m87ANYSjUGZCEcYMLcgJq', 'System Administrator', NULL, NULL, NULL, NULL, NULL, 'admin', 1, NULL, '2026-02-04 13:16:44', '2026-02-04 13:53:18'),
(2, 'jumanne', 'jumanne@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', '555-1001', '789 Staff St', NULL, NULL, NULL, 'staff', 1, NULL, '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(3, 'Agape', 'agape@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', '555-2001', '456 User Ave', NULL, NULL, NULL, 'user', 1, NULL, '2026-02-04 13:16:45', '2026-02-04 13:16:45'),
(4, 'admin', 'jmahunyo@gmail.com', '$2y$10$nOWCKO1p8Fu3YDakbWWtu.IcP1C4UbUxYQik/kIqkYmhXfyjfnSHe', 'joseph mahunyo', NULL, NULL, NULL, NULL, NULL, 'user', 1, NULL, '2026-02-04 13:35:02', '2026-02-04 13:35:02'),
(5, '@theson', 'msukuma@gmail.com', '$2y$10$zDMWyNe35Ptur.ewUD1HreqzT0xYRRI2Tvo.7EHTJ3vKNlWjnP9Da', 'marriage register', NULL, NULL, NULL, NULL, NULL, 'user', 1, NULL, '2026-02-04 13:56:52', '2026-02-04 13:56:52'),
(6, 'said', 'mimi@gmail.com', '$2y$10$bQjKdzu0YcKFOmUpjWx.I.rJViCitqv8pKZ7JCJmbd.4j18XAk2/6', 'said said', NULL, NULL, NULL, NULL, NULL, 'user', 1, NULL, '2026-02-05 03:52:22', '2026-02-05 03:52:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_status_history`
--
ALTER TABLE `application_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_status_history_app_id` (`application_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_documents_app_id` (`application_id`);

--
-- Indexes for table `marriage_applications`
--
ALTER TABLE `marriage_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_number` (`application_number`),
  ADD KEY `idx_applications_user_id` (`user_id`),
  ADD KEY `idx_applications_status` (`current_status`),
  ADD KEY `idx_applications_app_num` (`application_number`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_status_history`
--
ALTER TABLE `application_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marriage_applications`
--
ALTER TABLE `marriage_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application_status_history`
--
ALTER TABLE `application_status_history`
  ADD CONSTRAINT `application_status_history_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `marriage_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `marriage_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `marriage_applications`
--
ALTER TABLE `marriage_applications`
  ADD CONSTRAINT `marriage_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
