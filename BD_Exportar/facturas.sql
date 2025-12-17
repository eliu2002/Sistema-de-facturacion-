-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-10-2025 a las 03:32:39
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
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `turno_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `caja_sesion_id` int(11) DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT 'Cliente General',
  `total` decimal(10,2) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `usuario_id`, `turno_id`, `cliente_id`, `caja_sesion_id`, `cliente_nombre`, `total`, `fecha_creacion`) VALUES
(1, 2, NULL, NULL, NULL, 'Manuel', 1110.00, '2025-09-23 04:07:29'),
(2, 2, NULL, NULL, NULL, 'Cliente General', 370.00, '2025-09-23 04:07:57'),
(3, 2, NULL, NULL, NULL, 'Cliente General', 370.00, '2025-09-23 04:08:11'),
(4, 2, NULL, NULL, NULL, 'Cliente General', 1850.00, '2025-09-23 04:08:18'),
(5, 2, NULL, NULL, NULL, 'Cliente General', 123.00, '2025-09-23 15:00:14'),
(6, 1, NULL, NULL, NULL, 'kk', 23.00, '2025-09-23 23:46:03'),
(7, 1, NULL, NULL, NULL, 'Cliente General', 3434.00, '2025-09-24 03:54:28'),
(8, 1, NULL, NULL, NULL, 'messi', 1540.00, '2025-09-24 03:55:35'),
(9, 1, NULL, NULL, NULL, 'messi', 8855.00, '2025-09-24 03:59:34'),
(10, 1, NULL, NULL, NULL, 'Cliente General', 10302.00, '2025-09-24 04:12:35'),
(11, 1, NULL, NULL, NULL, 'Cliente General', 3434.00, '2025-09-24 23:14:17'),
(12, 1, NULL, NULL, NULL, 'Cliente General', 90.00, '2025-09-25 02:26:37'),
(13, 1, NULL, NULL, NULL, 'Cliente General', 2695.00, '2025-09-25 04:21:12'),
(14, 1, NULL, NULL, NULL, 'Cliente General', 23.00, '2025-09-25 04:23:03'),
(15, 1, NULL, NULL, NULL, 'Cliente General', 3434.00, '2025-09-25 04:49:11'),
(16, 1, NULL, NULL, NULL, 'Cliente General', 1110.00, '2025-09-25 17:05:11'),
(17, 1, NULL, NULL, NULL, 'Ramon', 385.00, '2025-09-25 18:16:54'),
(18, 1, NULL, NULL, NULL, 'Cliente General', 67.00, '2025-10-01 23:56:01'),
(19, 1, NULL, NULL, NULL, 'Cliente General', 3434.00, '2025-10-02 00:03:09'),
(20, 1, NULL, NULL, NULL, 'Ramon', 3434.00, '2025-10-02 02:15:25'),
(21, 1, NULL, NULL, NULL, 'Cliente General', 454.00, '2025-10-02 02:15:51'),
(22, 1, NULL, NULL, NULL, 'Cliente General', 233.00, '2025-10-02 02:18:04'),
(23, 1, NULL, NULL, NULL, 'Cliente General', 385.00, '2025-10-02 02:38:55'),
(24, 1, NULL, NULL, NULL, 'Mariluz ', 23.00, '2025-10-03 21:01:07'),
(25, 1, NULL, NULL, NULL, 'Mariluz ', 46.00, '2025-10-03 21:01:25'),
(26, 2, NULL, NULL, NULL, 'Cliente General', 113.00, '2025-10-04 02:55:37'),
(27, 1, NULL, NULL, NULL, 'Cliente General', 23.00, '2025-10-04 02:59:22'),
(28, 1, NULL, NULL, NULL, 'Eliú ', 3457.00, '2025-10-04 03:17:04'),
(29, 1, NULL, NULL, NULL, 'Cliente General', 385.00, '2025-10-04 03:19:11'),
(30, 2, NULL, NULL, NULL, 'messi', 23.00, '2025-10-04 18:57:04'),
(31, 2, NULL, NULL, NULL, 'Cliente General', 134.00, '2025-10-08 01:33:11'),
(32, 1, NULL, NULL, NULL, 'Cliente General', 34.00, '2025-10-13 22:27:25'),
(33, 1, NULL, NULL, NULL, 'Cliente General', 1.17, '2025-10-14 00:10:39'),
(34, 1, NULL, NULL, NULL, 'RUC: 279894893843555', 385.00, '2025-10-14 00:47:43'),
(35, 1, NULL, NULL, NULL, 'messi', 3.51, '2025-10-18 00:17:13'),
(36, 1, 1, NULL, NULL, 'Eliú ', 34.00, '2025-10-21 00:08:25'),
(37, 1, 1, NULL, NULL, 'Cliente General', 7509.00, '2025-10-21 00:08:55'),
(38, 1, 1, NULL, NULL, 'Cliente General', 23.00, '2025-10-21 00:51:49'),
(39, 1, 2, NULL, NULL, 'RUC: 279894893843555', 2100.00, '2025-10-22 02:23:14'),
(40, 1, 3, NULL, NULL, 'Cliente General', 1806.00, '2025-10-22 02:31:46'),
(41, 1, 5, NULL, NULL, 'Cliente General', 71.00, '2025-10-26 03:16:48'),
(42, 1, 7, NULL, NULL, 'RUC: 279894893843555', 491.00, '2025-10-27 21:57:50'),
(43, 1, 7, NULL, NULL, '289-320102-8573P', 60.00, '2025-10-27 22:19:52'),
(44, 1, 7, NULL, NULL, 'Cliente General', 70.00, '2025-10-27 22:21:14'),
(45, 2, 7, NULL, NULL, 'Socorro', 1155.00, '2025-10-27 23:21:33'),
(46, 1, 7, NULL, NULL, 'Cliente General', 60.00, '2025-10-27 23:24:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_caja_sesion_id` (`caja_sesion_id`),
  ADD KEY `facturas_ibfk_turno` (`turno_id`),
  ADD KEY `fk_factura_cliente` (`cliente_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facturas_ibfk_turno` FOREIGN KEY (`turno_id`) REFERENCES `turnos_caja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_factura_caja` FOREIGN KEY (`caja_sesion_id`) REFERENCES `caja_sesiones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
