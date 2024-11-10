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

--
-- Table structure for table `temp_users`
--

CREATE TABLE `temp_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_users`
--

INSERT INTO `temp_users` (`id`, `name`, `email`, `password_hash`, `verification_code`, `expires_at`) VALUES
(1, 'asdf', 'ayuba.nime@gmail.com', '$2y$10$ViWoVckTstUdv/Kq9XRv8ecDzIAFqGp2rE0s0siSkUbmd1VX0WdBW', '640112', '2024-11-10 20:51:43'),
(2, 'afasdf', 'ayu.banime@gmail.com', '$2y$10$Lj40xo5AhLhrR.cDn0WR0OFpl9VJ6Qp3C9zBhKnHq1nBSaZcLZx0S', '440842', '2024-11-10 20:54:31'),
(3, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$E137oPpf2a2rfr3mnBwkDOHKNRTsyA7dj.KFwi7xnDBWR2KNBEQ8y', '894820', '2024-11-10 20:56:31'),
(4, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$3cp8huXvPEvSVXCU3RtpquSzltdyBE6ieaJJ2hlskIe1QJDAroBAW', '808711', '2024-11-10 20:56:43'),
(5, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$E92uwgrmGD/wXQTEXs.s0u6vUfWTqS2l06nzXkfZ5/Y6sCYKPJNby', '506337', '2024-11-10 21:16:00'),
(6, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$W7gkjJPit.27/ifh8skSk.SEdSBsEOSThkzF3SR3sruywcTEcRYPi', '448294', '2024-11-10 21:16:21'),
(7, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$4qfErVhCCwNKQ6eevO8EiOn0OJrwrPJ8Pe5gPzdZ3uTTMScp4lA9i', '470141', '2024-11-10 21:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`, `updated_at`, `is_active`, `last_login`) VALUES
(1, 'test', 'test@test.com', '$2y$10$8xOUkthbiJ.5UjJ/E6w.SetreVzpmT3VZ0Raa3rA8VyhHKkJBeZhS', '2024-11-10 12:41:14', '2024-11-10 12:41:14', 1, NULL),
(2, 'new1', 'new@new.com', '$2y$10$K0ejVwy71NS6cMINxACCYOG0GEBsJLN3vhtrK0T8kd.odB4mBSmOW', '2024-11-10 12:59:15', '2024-11-10 12:59:15', 1, NULL),
(3, 'DASD', 'ASDF@AD', '$2y$10$x.7b1M729nt3k7Gddy5oW.MtKArSqwcTTjji93Q1qNUshwwsTEkBu', '2024-11-10 13:58:30', '2024-11-10 13:58:30', 1, NULL),
(4, 'asdf', 'grimwarayub@gmail.com', '$2y$10$bpjKZ/fW7WerqQQpO1VVQunUk0CRlxIN3HYgUsQZSaymvpD57I6Ae', '2024-11-10 14:14:45', '2024-11-10 14:14:45', 1, NULL),
(5, 'test', 'test@test', '$2y$10$2ymB2Y//zg31gEilBzz2xeSbqZwBbzPbssNdOffnSQ48mFnfHCNpy', '2024-11-10 14:41:36', '2024-11-10 14:41:36', 1, NULL),
(6, 'asudy', 'asdojas@gmail.so', '$2y$10$B7EymUPMxmfX4Z/sEUFUaON8PdH77KmAjxm6Qq/SB9Cx2CNAtY51G', '2024-11-10 15:24:45', '2024-11-10 15:24:45', 1, NULL),
(7, 'sdfd', 'sdf@sasfgsd', '$2y$10$6IU1sJSS/f.8GHgBbuPyoOFodbSFgIetWBcOZDoqIIfcEBDoCoOjW', '2024-11-10 15:37:58', '2024-11-10 15:37:58', 1, NULL),
(8, 'asdfa', 'ayubanime@gmail.com', '$2y$10$ItTI7mNw7ILVaNhVtFCJjej0TUXuMF9Rjc6BTEvZkQgRdUqGlvoce', '2024-11-10 15:39:13', '2024-11-10 15:39:13', 1, NULL),
(9, 'sada', 'grimwarayasub@gmail.com', '$2y$10$gA/0sgdOg1I.3AJaDmGB8urnLJGUGQpHtKP87vOpb0m8bpts7REjS', '2024-11-10 15:56:49', '2024-11-10 15:56:49', 1, NULL),
(10, 'ayub2', 'ayub@ayubxxl.site', '$2y$10$2/47zIFeabryb1s3y6BmwuRALl21Te5KpyAxl/h4vxMjIq5nf52xe', '2024-11-10 16:10:21', '2024-11-10 16:10:21', 1, NULL),
(11, 'trial2', 'ayubclippaz@gmail.com', '$2y$10$mqe3htBG9jkYlF9dVILsMuF9R1s4XldEmSlUDMJGIKzYSMAx8DeIG', '2024-11-10 17:06:25', '2024-11-10 17:06:25', 1, NULL),
(12, 'sdffsa', 'ayubanim.e@gmail.com', '$2y$10$rA.D2FpwnFY36Ot7IOmwm.2dxpmsLTHCFy0kR1xAn1dW6w7Ju2ayW', '2024-11-10 18:08:48', '2024-11-10 18:08:48', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `social_media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_media`)),
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_token`, `expires_at`, `created_at`, `ip_address`, `user_agent`) VALUES
(1, 1, 'b603a8f96be2e517f864388f843537e15fc8c133ae30561ad49d49bb087c9481', '2024-11-11 10:41:29', '2024-11-10 12:41:29', NULL, NULL),
(2, 1, 'e999a07148f24796e9ddf001b3a1beebdb6a712ec4d21d4b8f96509f4e1d093c', '2024-11-11 10:58:40', '2024-11-10 12:58:40', NULL, NULL),
(3, 1, '742c4a2eb74daf4f8c3a78d59701bf752feb32a1a3997085ed041340381184d5', '2024-11-11 12:41:46', '2024-11-10 14:41:46', NULL, NULL),
(4, 1, '3f4bf35bf17f3fd903ed00d70f2a04cb55b723e408c95dd4582deb5a0a1b58d9', '2024-11-11 13:21:03', '2024-11-10 15:21:03', NULL, NULL),
(5, 1, '6823bbdea57b9895f6676dd2482c428ce46660b0d66c522940f23aeb654416fa', '2024-11-11 13:38:12', '2024-11-10 15:38:12', NULL, NULL),
(6, 1, '513ebf4b3ce52b4b27d75737ca10a1858c86032e16ea635e038b53fade56aed5', '2024-11-11 13:51:08', '2024-11-10 15:51:08', NULL, NULL),
(7, 1, '70f8e6206f85156e97836837e467904670f43ef085c6c7fac4d4715a50293f9d', '2024-11-11 13:51:10', '2024-11-10 15:51:10', NULL, NULL),
(8, 1, '788f591be1e9fa73053183c3103f0aa1da9cecc463cbada56f3677f6fa594341', '2024-11-11 13:51:10', '2024-11-10 15:51:10', NULL, NULL),
(9, 1, '037a5136a440eff78ae37e62779029b1ebd781f41eab661110db285d6d34d57b', '2024-11-11 13:51:10', '2024-11-10 15:51:10', NULL, NULL),
(10, 1, '7402a4211817786f1e65e049727bc55c1e5138535103e17280b5943598205c06', '2024-11-11 13:51:11', '2024-11-10 15:51:11', NULL, NULL),
(11, 1, '7bd511e6992174f000f2b7d903e3ac24864582d9e2b0bebad4c6c45a3c2fdc5f', '2024-11-11 13:51:11', '2024-11-10 15:51:11', NULL, NULL),
(12, 1, '1675a0a3b99df446db2cdf953416541da49606b8afa6477fbdcf01921a421c96', '2024-11-11 13:51:27', '2024-11-10 15:51:27', NULL, NULL),
(13, 1, '4b7c7ab776bc5ed6660cb38b08ec9dac435a386924d0c6b3aecf574849b0a5c8', '2024-11-11 13:51:27', '2024-11-10 15:51:27', NULL, NULL),
(14, 1, '987c04804dece2aa5b64ca9ba8905c77ea55d9db59266d1ace7c7562c3728545', '2024-11-11 13:51:42', '2024-11-10 15:51:42', NULL, NULL),
(15, 1, '0a914e7cbb49bd8370abd55005bf0b9bae043604c1f9e485b919c618fec3c6cb', '2024-11-11 13:51:49', '2024-11-10 15:51:49', NULL, NULL),
(16, 1, '165c646083b47bcc70c6f679e0e1970741c94f5b62b343df956ce5870eb20e53', '2024-11-11 13:51:49', '2024-11-10 15:51:49', NULL, NULL),
(17, 1, 'f87d13d3f82846f358e40b9fa521e363252a82983b2ada2803c422609ad6db84', '2024-11-11 13:52:40', '2024-11-10 15:52:40', NULL, NULL),
(18, 1, 'b659f4ba3e36bc9f8e2bafc5e4e659c3cb9212770333ed59db87235995251bb7', '2024-11-11 13:52:42', '2024-11-10 15:52:42', NULL, NULL),
(19, 1, '8532281bf2ac46170b027f8d70ffa610812a30ec3badfd81ed46409f52d95aed', '2024-11-11 13:52:42', '2024-11-10 15:52:42', NULL, NULL),
(20, 1, '44f25ce99c3cdc214774ba0a1dd34d8904f867cd71cfbff27345ff938b7120cc', '2024-11-11 13:52:43', '2024-11-10 15:52:43', NULL, NULL),
(21, 1, '07e22bb79e789b6c3ed02bd83c6e125bdc8e8cfd8027c9dc6ee9842051ba94fa', '2024-11-11 13:52:43', '2024-11-10 15:52:43', NULL, NULL),
(22, 1, '907c3b129256139496f75c5060272732ddcc21dd5a77160ec667cb7f55229228', '2024-11-11 13:52:43', '2024-11-10 15:52:43', NULL, NULL),
(23, 1, '8bf50f8b0b87f9fa13f0a8c875c5e0d752c60183ce47d822b994928ac858b276', '2024-11-11 13:53:04', '2024-11-10 15:53:04', NULL, NULL),
(24, 1, '7730a968ac1d395daee7510db7995eea36467c6315181b6f623dbfec5184379f', '2024-11-11 13:53:06', '2024-11-10 15:53:06', NULL, NULL),
(25, 1, '61f909f342868bc47cd21b0aec07bef9a598535802e54ab61b6de2043e471a16', '2024-11-11 13:53:06', '2024-11-10 15:53:06', NULL, NULL),
(26, 1, '350867c2b732a30a768d98a7fc0bb1932ba13d94158dc24c76bed9b0a3c7755f', '2024-11-11 13:53:06', '2024-11-10 15:53:06', NULL, NULL),
(27, 1, '132ba00371d525f77e043a89b4db00691e8a4ff39b8464497583632bed505108', '2024-11-11 13:53:07', '2024-11-10 15:53:07', NULL, NULL),
(28, 1, '9e887dcbb6e63c08781adbfcaba554943152d18aafad6d2f6a108f420c6ad405', '2024-11-11 13:53:07', '2024-11-10 15:53:07', NULL, NULL),
(29, 1, '32aa1ac3f36cb96324cd1e419b36859d5fee72ce2c45b00b3c5490645d542887', '2024-11-11 13:53:28', '2024-11-10 15:53:28', NULL, NULL),
(30, 1, '44b1da5c3b33ff1794cf18aee8140e8f8390099ef2a344fde4fb1c55f15661a3', '2024-11-11 13:53:30', '2024-11-10 15:53:30', NULL, NULL),
(31, 11, 'e7df3f4121aad1c1fd5a87e6e34833cbb4b593b89715a7bd0d9a07cea85e9ae1', '2024-11-11 17:35:32', '2024-11-10 17:35:32', NULL, NULL),
(32, 11, '5b239b5fcb658860e3cd8aa4d8d3aec910da3f24cfa0a448d15a73786a90e296', '2024-11-11 18:10:28', '2024-11-10 18:10:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `user_id`, `code`, `created_at`, `expires_at`, `used`) VALUES
(51, 11, '856464', '2024-11-10 17:06:39', '2024-11-10 18:11:39', 0),
(52, 11, '352569', '2024-11-10 17:09:34', '2024-11-10 18:14:34', 0),
(53, 11, '698137', '2024-11-10 17:17:11', '2024-11-10 18:22:11', 0),
(54, 11, '570183', '2024-11-10 17:17:12', '2024-11-10 18:22:12', 0),
(55, 11, '729611', '2024-11-10 17:24:51', '2024-11-10 18:29:51', 0),
(56, 11, '569548', '2024-11-10 17:26:08', '2024-11-10 18:31:08', 0),
(57, 11, '527233', '2024-11-10 17:34:56', '2024-11-10 20:39:56', 1),
(58, 11, '977632', '2024-11-10 18:09:03', '2024-11-10 19:14:03', 0),
(59, 11, '421163', '2024-11-10 18:10:08', '2024-11-10 21:15:08', 1);

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
