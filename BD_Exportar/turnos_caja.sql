-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2025 a las 23:41:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `licoreria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos_caja`
--

CREATE TABLE `turnos_caja` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `capital_inicial` decimal(10,2) NOT NULL,
  `total_ventas_calculado` decimal(10,2) DEFAULT NULL,
  `monto_final_real` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierto','cerrado') NOT NULL DEFAULT 'abierto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos_caja`
--

INSERT INTO `turnos_caja` (`id`, `usuario_id`, `capital_inicial`, `total_ventas_calculado`, `monto_final_real`, `diferencia`, `fecha_apertura`, `fecha_cierre`, `estado`) VALUES
(1, 1, 1000.00, 7566.00, 8566.00, 0.00, '2025-10-20 18:07:58', '2025-10-21 19:44:19', 'cerrado'),
(2, 2, 1000.00, 2100.00, 3000.00, -100.00, '2025-10-21 20:05:07', '2025-10-21 20:23:52', 'cerrado'),
(3, 1, 1500.00, 1806.00, 3310.00, 4.00, '2025-10-21 20:31:29', '2025-10-21 20:32:12', 'cerrado'),
(4, 1, 1500.00, 0.00, 1500.00, 0.00, '2025-10-21 21:15:27', '2025-10-22 17:29:02', 'cerrado'),
(5, 1, 1500.00, 71.00, 1600.00, 29.00, '2025-10-22 17:57:00', '2025-10-25 23:42:50', 'cerrado'),
(6, 1, 1500.00, 0.00, 1500.00, 0.00, '2025-10-25 23:43:04', '2025-10-26 21:49:12', 'cerrado'),
(7, 1, 1500.00, NULL, NULL, NULL, '2025-10-27 14:02:45', NULL, 'abierto');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `turnos_caja`
--
ALTER TABLE `turnos_caja`
  ADD CONSTRAINT `turnos_caja_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
