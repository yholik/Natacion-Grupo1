-- Migración V7: Normalización de specialties y levels en tabla lessons
-- Convierte lessons.specialty (VARCHAR) y lessons.level (VARCHAR) a IDs con FK

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 1. Crear tabla levels
--
CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_level_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `levels` (`name`) VALUES
('Inicial'),
('Medio'),
('Avanzado');

--
-- 2. Agregar columnas specialty_id y level_id a lessons
--
ALTER TABLE `lessons`
  ADD COLUMN `specialty_id` int(11) DEFAULT NULL AFTER `coach_id`,
  ADD COLUMN `level_id` int(11) DEFAULT NULL AFTER `specialty_id`;

--
-- 3. Migrar datos existentes (mapear nombres a IDs)
--
UPDATE `lessons` l
INNER JOIN `specialties` s ON l.specialty = s.name
SET l.specialty_id = s.id;

UPDATE `lessons` l
INNER JOIN `levels` lv ON l.level = lv.name
SET l.level_id = lv.id;

--
-- 4. Eliminar columnas de texto antiguo
--
ALTER TABLE `lessons`
  DROP COLUMN `specialty`,
  DROP COLUMN `level`;

--
-- 5. Agregar restricciones NOT NULL y Foreign Keys
--
ALTER TABLE `lessons`
  MODIFY `specialty_id` int(11) NOT NULL,
  MODIFY `level_id` int(11) NOT NULL;

ALTER TABLE `lessons`
  ADD CONSTRAINT `fk_lesson_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`),
  ADD CONSTRAINT `fk_lesson_level` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
