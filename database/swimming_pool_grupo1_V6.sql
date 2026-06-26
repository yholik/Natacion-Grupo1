-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generacion: 26-06-2026
-- Version del servidor: 10.4.32-MariaDB
-- Version de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `swimming_pool_grupo1`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'Coach'),
(3, 'Swimmer');

CREATE TABLE `auth` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `auth` (`id`, `email`, `password`, `role_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'juanpcesarini@hotmail.com', '$2y$10$X1w2LzRGHtDI.wjtnlFmzehdKQrg6kkI.VppvOW3o3Sjz6XdGQmaq', 3, '2026-03-15 13:17:04', '2026-03-15 13:17:55', NULL);

CREATE TABLE `specialties` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `specialties` (`id`, `name`) VALUES
(1, 'Crol'),
(2, 'Espalda'),
(3, 'Pecho'),
(4, 'Mariposa'),
(5, 'Aguas abiertas');

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default-profile.png',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfil` (
  `id`,
  `user_id`,
  `first_name`,
  `last_name`,
  `phone`,
  `birth_date`,
  `profile_image`,
  `created_at`,
  `updated_at`,
  `deleted_at`
) VALUES
(4, 4, 'Juan Pablo', 'Pompin', '1111111', NULL, 'swimmer_jpompin_5590.jpeg', '2026-03-15 13:17:04', '2026-03-15 13:17:04', NULL);

CREATE TABLE `perfil_specialty` (
  `profile_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `coach_id` int(11) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `swimmer_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `status` enum('Confirmed','Cancelled') DEFAULT 'Confirmed',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_role` (`role_id`);

ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_specialty_name` (`name`);

ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`);

ALTER TABLE `perfil_specialty`
  ADD PRIMARY KEY (`profile_id`,`specialty_id`),
  ADD KEY `fk_profile_specialty_specialty` (`specialty_id`);

ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lesson_profile` (`coach_id`);

ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking` (`swimmer_id`,`lesson_id`),
  ADD KEY `fk_booking_lesson` (`lesson_id`);

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `token` (`token`);

ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `specialties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `auth`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `perfil`
  ADD CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `auth` (`id`) ON DELETE CASCADE;

ALTER TABLE `perfil_specialty`
  ADD CONSTRAINT `fk_profile_specialty_profile` FOREIGN KEY (`profile_id`) REFERENCES `perfil` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_profile_specialty_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`);

ALTER TABLE `lessons`
  ADD CONSTRAINT `fk_lesson_profile` FOREIGN KEY (`coach_id`) REFERENCES `perfil` (`id`);

ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`),
  ADD CONSTRAINT `fk_booking_profile` FOREIGN KEY (`swimmer_id`) REFERENCES `perfil` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
