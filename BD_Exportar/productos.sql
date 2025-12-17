-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2025 a las 23:40:58
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
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `imagen_url` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `categoria` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `cantidad`, `imagen_url`, `fecha_creacion`, `categoria`) VALUES
(17, 'Toña', 'Cerveza Toña Botella Abre Facil - 355 ml', 42.00, 100, 'uploads/products/68f977512ab4b-Toña.png', '2025-10-22 01:40:58', 'Cerveza'),
(18, 'Toña Light', 'Cerveza Toña Light Botella Oneway - 350ml', 43.00, 100, 'uploads/products/68f97690d1435-Toña Light.png', '2025-10-22 03:16:38', 'Cerveza'),
(19, 'Stella Artois', 'Contenido de alcohol por volumen:\r\n0.05', 70.00, 100, 'uploads/products/68f9710328287-Stella.png', '2025-10-23 00:04:19', 'Cerveza'),
(20, 'Toña Ultra Lata', ' Cerveza Tona Ultra 4pk 350mlCerveza Tona Ultra', 42.00, 100, 'uploads/products/68f9728da9fd8-Toña Ultra lata.png', '2025-10-23 00:09:36', 'Cerveza'),
(21, 'Heineken', 'CERVEZA BOTELLA 355 ML 16089 - UND - HEINEKEN', 70.00, 100, 'uploads/products/68f973728e7ee-Heineken .png', '2025-10-23 00:14:42', 'Cerveza'),
(22, 'Sol', 'CERVEZA BOTELLA 355 ML - UND - SOL', 65.00, 100, 'uploads/products/68f973d924a74-Sol .png', '2025-10-23 00:16:25', 'Cerveza'),
(23, 'Miller', 'CERVEZA MILLER LITE BOTELLA 12OZ - UND - MILLER', 50.00, 100, 'uploads/products/68f9748d8ef84-Lite.png', '2025-10-23 00:19:25', 'Cerveza'),
(24, 'Frost', 'CERVEZA BOTELLA ONE WAY 12 OZ N/R - UND - VICTORIA FROST', 37.00, 100, 'uploads/products/68f974e7d7b2b-Frost.png', '2025-10-23 00:20:55', 'Cerveza'),
(25, 'Maestro', 'Cerveza Victoria Maestro Botella - 355ml', 46.00, 100, 'uploads/products/68f975927703a-Maestro.png', '2025-10-23 00:23:46', 'Cerveza'),
(26, 'Clásica ', 'CERVEZA CLASICA BOTELLA 12 OZ - UND - VICTORIA', 45.00, 100, 'uploads/products/68f9761f109e1-Clasica.png', '2025-10-23 00:26:07', 'Cerveza'),
(27, 'Premium ', 'Cerveza Premium. Vidrio', 50.00, 100, 'uploads/products/68f9798d48afc-Premium .png', '2025-10-23 00:38:48', 'Cerveza'),
(28, 'Moropotente', 'Cerveza Moropotente lado oscuto - 350 ml', 85.00, 100, 'uploads/products/68f97a6906dd6-Moropotente.png', '2025-10-23 00:44:25', 'Cerveza'),
(29, 'Dark Lager', 'VICTORIA DARK LAGER', 50.00, 100, 'uploads/products/68f97b962aa55-Dark Lager Triple malta.png', '2025-10-23 00:49:26', 'Cerveza'),
(30, 'Bliss', 'Bebida Bliss Happy Mix Alcoholica - 350ml', 71.00, 99, 'uploads/products/68f97e086c056-Bliss.png', '2025-10-23 00:59:52', 'Bebida alcohólica con sabor'),
(31, 'Gallo', 'Cerveza Gallo Und 350ml', 60.00, 100, 'uploads/products/68f980172a949-Gallo.png', '2025-10-23 01:08:39', 'Cerveza'),
(32, 'Corona', 'Cerveza Corona botella - 330 ml', 80.00, 100, 'uploads/products/68fc381c03708-Corona.png', '2025-10-25 02:38:20', 'Cerveza'),
(33, 'Brahama', 'Brasil. Se fundó en 1888 y en 1934 lanzó su popular Brahma Chopp. ', 70.00, 99, 'uploads/products/68fc38e5db591-Brahama.png', '2025-10-25 02:41:41', 'Cerveza'),
(34, 'Modelo ', 'Esta cerveza equilibrada y fácil de beber contiene 145 calorías , 0 gramos de grasa y 4,4 % de alcohol por cada 355 ml ', 71.00, 100, 'uploads/products/68fc395083a0b-Modelo .png', '2025-10-25 02:43:28', 'Cerveza'),
(35, 'Cristalino ', 'RON AÑEJO CRISTALINO 375 ML - UND - FLOR DE CA¥A', 491.00, 99, 'uploads/products/68fc3e237c6b5-Cristalino .png', '2025-10-25 03:04:03', 'Ron'),
(37, 'JOHN BARR', 'WHISKY ROJO BOT 750 ML 003972 - UND - JOHN BARR', 570.00, 100, 'uploads/products/68fd92ed4098d-John Barr.png', '2025-10-26 03:18:05', 'Whisky'),
(38, 'JACK DANIEL\'S', 'WHISKY 750 ML - UND - JACK DANIEL\'S', 2000.00, 100, 'uploads/products/68fd93b292345-JACK DANIEL\'S.png', '2025-10-26 03:21:22', 'Whisky'),
(39, 'Adan y Eva', 'Seltzer Adan Y Eva Frutos Rojos Lata - 355ml', 60.00, 99, 'uploads/products/68fee318d176a-Adan y Eva.png', '2025-10-27 03:12:24', 'Bebida alcohólica con sabor'),
(40, ' Bamboo Mojito', 'Bebida Bamboo Mojito Lata - 350ml', 50.00, 100, 'uploads/products/68fee36cb0293-Bamboo Mojito.png', '2025-10-27 03:13:48', 'Bebida alcohólica con sabor'),
(41, 'Bamboo Daiquiri Fresa', 'Bebidas Bamboo Daiquiri Fresa Lata - 350ml', 50.00, 100, 'uploads/products/68fee3a6257cf-Bamboo Fresa.png', '2025-10-27 03:14:46', 'Bebida alcohólica con sabor'),
(42, 'Bamboo Pina ', 'Bamboo Pina Colada 350Ml', 50.00, 100, 'uploads/products/68fee3e244878-Bamboo Piña.png', '2025-10-27 03:15:46', 'Bebida alcohólica con sabor'),
(43, 'Bamboo Sandía ', 'Bebida Bamboo Sandía 10.5% alcohol - 437 ml', 70.00, 100, 'uploads/products/68fee41c402bd-Bamboo Sandia.png', '2025-10-27 03:16:44', 'Bebida alcohólica con sabor'),
(44, 'Bamboo Margarita', 'Bamboo Margarita 350Ml', 50.00, 100, 'uploads/products/68fee45952e6d-Bamboo Margarita.png', '2025-10-27 03:17:45', 'Bebida alcohólica con sabor'),
(45, 'Seltzer spark manzana ', 'Bebida alcohólica Hard Seltzer spark manzana verde lata - 350 ml', 40.00, 100, 'uploads/products/68fee50b72291-Seltzer Manzana.png', '2025-10-27 03:20:43', 'Bebida alcohólica con sabor'),
(46, ' Seltzer spark naked ', 'Bebida alcohólica Hard Seltzer spark naked - 355 ml', 40.00, 100, 'uploads/products/68fee54ac123c-Seltzer naked.png', '2025-10-27 03:21:46', 'Bebida alcohólica con sabor'),
(47, 'Seltzer Sabor Tropical Berry', 'Bebida Alcohólica Flor de Caña Seltzer Sabor Tropical Berry - 355 ml', 40.00, 100, 'uploads/products/68fee5c6ef85e-Flor de caña tropical berru.png', '2025-10-27 03:23:50', 'Bebida alcohólica con sabor'),
(48, 'Spark Raspberry', 'Seltzer Spark Raspberry Lata - 350ml', 40.00, 100, 'uploads/products/68fee636c4fb7-Seltzer Raspberry.png', '2025-10-27 03:25:42', 'Bebida alcohólica con sabor'),
(49, 'Flor De Caña Premiun Seltzer Lima', 'Flor De Caña Premiun Seltzer Lima - 355 ml', 40.00, 100, 'uploads/products/68fee68083ac2-Flor de caña Lima.png', '2025-10-27 03:26:56', 'Bebida alcohólica con sabor'),
(50, 'Ron Flor De Caña Centenario', 'Ron Flor De Caña Centenario 12 Años - 750 ml', 1180.00, 100, 'uploads/products/68fee7fa4ff4b-Ron 12 años.png', '2025-10-27 03:33:14', 'Ron'),
(51, 'Ron Flor De Cana Ultra Lite ', 'Ron Flor De Cana Ultra Lite 1000ml', 360.00, 100, 'uploads/products/68fee84da0268-Ultra lite.png', '2025-10-27 03:34:37', 'Ron'),
(52, 'Ron Flor De Caña 4 Años Extra Lite', 'Ron Flor De Caña 4 Años Extra Lite - 375ml', 140.00, 100, 'uploads/products/68fee8890e954-ron 4 años .png', '2025-10-27 03:35:37', 'Ron'),
(53, 'Ron Flor De Caña Clásico Añejo 5 Años', 'Ron Flor De Caña Clásico Añejo 5 Años - 375 ml', 160.00, 100, 'uploads/products/68fee8cd6c016-Ron añejo 5 años.png', '2025-10-27 03:36:45', 'Ron'),
(54, 'Ron Flor De Caña 7 Años Gran Reserva ', 'Ron Flor De Caña 7 Años Gran Reserva - 375ml', 220.00, 100, 'uploads/products/68fee908e5276-Ron gran reserva.png', '2025-10-27 03:37:44', 'Ron'),
(55, 'Ron Flor De Caña Spresso Coffee Liquor', 'Ron Flor De Caña Spresso Coffee Liquor- 750ml', 640.00, 100, 'uploads/products/68fee9812b05b-Ron coffee Liquor.png', '2025-10-27 03:39:45', 'Ron'),
(56, 'Ron Flor De Caña Sabor Coco ', 'Ron Flor De Caña Sabor Coco 750ml', 630.00, 100, 'uploads/products/68fee9c1c4ae2-Ron coco.png', '2025-10-27 03:40:49', 'Ron'),
(57, 'Whisky Jack Daniels Honey ', 'Whisky Jack Daniels Honey - 750 ml', 1580.00, 100, 'uploads/products/68feea9c9f6d0-Whisky Jack Daniels Honey.png', '2025-10-27 03:44:28', 'Whisky'),
(58, 'Whisky Johnnie Walker Black Label', 'Whisky Johnnie Walker Black Label -750 ml', 2750.00, 100, 'uploads/products/68feeafd0c59b-Whisky Johnnie Walker Black Label.png', '2025-10-27 03:46:05', 'Whisky'),
(59, 'Whisky Fireball sabor canela', 'Whisky Fireball sabor canela -750 ml', 1030.00, 100, 'uploads/products/68feeb3d55283-Whisky Fireball sabor canela .png', '2025-10-27 03:47:09', 'Whisky'),
(60, 'Whisky The Glenlivet escocés reserva de fundadores ', 'Whisky The Glenlivet escocés reserva de fundadores - 750 ml', 4000.00, 100, 'uploads/products/68feeb7fbae10-Whisky The Glenlivet escocés reserva de fundadores .png', '2025-10-27 03:48:15', 'Whisky'),
(61, 'WHISKY RED LAB', 'WHISKY RED LAB 750 ML - UND - JOHNNIE WALKER', 1340.00, 100, 'uploads/products/68ffd06f1087d-WHISKY RED LAB 750 ML - UND - JOHNNIE WALKER.png', '2025-10-27 20:05:03', 'Whisky'),
(62, 'WHISKY GOLDEN ', 'WHISKY GOLDEN 1000 ML 5174 - UND - CHANCELER', 600.00, 100, 'uploads/products/68ffd0b46e527-WHISKY GOLDEN 1000 ML 5174 - UND - CHANCELER.png', '2025-10-27 20:06:12', 'Whisky'),
(63, 'WHISKY REGULAR ', 'WHISKY REGULAR 750 ML - UND - OLD PARR', 3500.00, 100, 'uploads/products/68ffd0f6ad2a4-WHISKY REGULAR 750 ML - UND - OLD PARR.png', '2025-10-27 20:07:18', 'Whisky'),
(64, 'TEQUILA ORO.ESPECIAL REG', 'TEQUILA ORO.ESPECIAL REG. - UND - CUERVO', 1230.00, 100, 'uploads/products/68ffd18d8e8df-TEQUILA ORO.ESPECIAL REG. - UND - CUERVO.png', '2025-10-27 20:09:49', 'Whisky'),
(65, 'TEQUILA BLANCO ESPECIAL ', 'TEQUILA BLANCO ESPECIAL 750 ML 042322 - UND - CUERVO', 1155.00, 100, 'uploads/products/68ffd2ee11809-TEQUILA BLANCO ESPECIAL 750 ML 042322 - UND - CUERVO.png', '2025-10-27 20:15:42', 'Whisky'),
(66, 'VINO PINOT GRIGIO VALDADIGE ', 'VINO PINOT GRIGIO VALDADIGE 750 ML 940 - UND - SANTA MARGHERITA', 1450.00, 100, 'uploads/products/68ffd3a070b16-VINO PINOT GRIGIO VALDADIGE 750 ML 940 - UND - SANTA MARGHERITA.png', '2025-10-27 20:18:40', 'Vino'),
(67, 'VINO SAUVIGNON BLANC', 'VINO SAUVIGNON BLANC 750 ML - UND - UNDURRAGA', 420.00, 100, 'uploads/products/68ffd3e63354f-VINO SAUVIGNON BLANC 750 ML - UND - UNDURRAGA.png', '2025-10-27 20:19:50', 'Vino'),
(68, 'VINO PINOT GRIGIO ', 'VINO PINOT GRIGIO 750 ML - UND - FINCA LAS MORAS', 430.00, 100, 'uploads/products/68ffd41931cc1-VINO PINOT GRIGIO 750 ML - UND - FINCA LAS MORAS.png', '2025-10-27 20:20:41', 'Vino'),
(69, 'VINO BLANCO SAUVIGNON ', 'VINO BLANCO SAUVIGNON 750 ML 000396 - UND - UNDURRAGA', 340.00, 100, 'uploads/products/68ffd45bbd857-VINO BLANCO SAUVIGNON 750 ML 000396 - UND - UNDURRAGA.png', '2025-10-27 20:21:47', 'Vino'),
(70, 'VINO PROSECCO ROSE', 'VINO PROSECCO ROSE 750 ML (6) - UND - EPIFANIA', 920.00, 100, 'uploads/products/68ffd4f6716b9-VINO PROSECCO ROSE.png', '2025-10-27 20:24:22', 'Vino'),
(71, 'COPA DE VIDRIO GIORGIO ', 'COPA DE VIDRIO GIORGIO P/VINO 12 OZ - UND - LIBBEY', 200.00, 50, 'uploads/products/68ffd5303233b-copa de vino .png', '2025-10-27 20:25:20', 'Otros'),
(72, 'VINO ESPUMANTE ', 'VINO ESPUMANTE 750 ML - UND - FREIXENET', 1300.00, 100, 'uploads/products/68ffd57ab533c-VINO ESPUMANTE 750 ML - UND - FREIXENET.png', '2025-10-27 20:26:34', 'Vino'),
(73, 'VINO TINTO G CAPELLA', 'VINO TINTO G CAPELLA 750 ML - UND - CAMPO DE BORSAO', 440.00, 100, 'uploads/products/68ffd5e309a1f-VINO TINTO G CAPELLA 750 ML - UND - CAMPO DE BORSAO.png', '2025-10-27 20:28:19', 'Vino'),
(74, 'VINO MOSCATO ', 'VINO MOSCATO 750 ML 016688 - UND - BAREFOOT', 650.00, 100, 'uploads/products/68ffd623b6b27-VINO MOSCATO 750 ML 016688 - UND - BAREFOOT.png', '2025-10-27 20:29:23', 'Vino'),
(75, 'VINO CABERNET SAUVIGNON', 'VINO CABERNET SAUVIGNON 750 ML - UND - APOTHIC RED', 1200.00, 100, 'uploads/products/68ffd6615ed56-VINO CABERNET SAUVIGNON 750 ML - UND - APOTHIC RED.png', '2025-10-27 20:30:25', 'Vino'),
(76, 'HIELO PURIFICADO BOLSA 10 LBS ', 'HIELO PURIFICADO BOLSA 10 LBS 101148 - UND - ECONOMAX', 70.00, 100, 'uploads/products/68ffd6a47bcde-HIELO PURIFICADO BOLSA 10 LBS 101148 - UND - ECONOMAX.png', '2025-10-27 20:31:32', 'Otros'),
(77, 'HIELO PURIFICADO BOLSA 25 LBS', 'HIELO PURIFICADO BOLSA 25 LBS 101063 - UND - GLACIAL', 152.00, 100, 'uploads/products/68ffd6d8ee3ba-HIELO PURIFICADO BOLSA 25 LBS 101063 - UND - GLACIAL.png', '2025-10-27 20:32:24', 'Otros'),
(78, 'COPA DE VIDRIO P/AGUA NORM ', 'COPA DE VIDRIO P/AGUA NORM 10.7 OZ - UND - LIBBEY', 65.00, 50, 'uploads/products/68ffd72b50b05-COPA DE VIDRIO .png', '2025-10-27 20:33:47', 'Otros'),
(79, 'NACHOS RANCHITAS JALAPENOS 150 GRS', 'NACHOS RANCHITAS JALAPENOS 150 GRS - UND - YUMMIES', 53.00, 100, 'uploads/products/68ffd77917ca6-NACHOS RANCHITAS JALAPENOS 150 GRS - UND - YUMMIES.png', '2025-10-27 20:35:05', 'Snack'),
(80, 'NACHOS GRANDES CON QUESO RANCHITAS', 'NACHOS GRANDES CON QUESO RANCHITAS - UND - YUMMIES', 53.00, 100, 'uploads/products/68ffd79fe312e-NACHOS GRANDES CON QUESO RANCHITAS - UND - YUMMIES.png', '2025-10-27 20:35:43', 'Snack'),
(81, 'BOCADILLO DE MAIZ C/JALAPENO 150 GR ', 'BOCADILLO DE MAIZ C/JALAPENO 150 GR - UND - DIANA', 30.00, 100, 'uploads/products/68ffd7e315345-BOCADILLO DE MAIZ CJALAPENO 150 GR - UND - DIANA.png', '2025-10-27 20:36:51', 'Snack'),
(82, 'CHIPS JALAPENOS 170 GR', 'CHIPS JALAPENOS 170 GR - UND - BARCEL', 120.00, 100, 'uploads/products/68ffd8345eab8-CHIPS JALAPENOS 170 GR - UND - BARCEL.png', '2025-10-27 20:38:12', 'Snack'),
(83, 'TAQUERITOS C/CHILE 180 GR', 'TAQUERITOS C/CHILE 180 GR - UND - YUMMIES', 53.00, 100, 'uploads/products/68ffd8b38dbdf-TAQUERITOS CHILE 180 GR - UND - YUMMIES.png', '2025-10-27 20:40:19', 'Snack'),
(84, 'ENCHILADAS BOLSA 22S', 'ENCHILADAS BOLSA 22S - UND - MILAGROS', 60.00, 100, 'uploads/products/68ffd8e583f58-ENCHILADAS BOLSA 22S - UND - MILAGROS.png', '2025-10-27 20:41:09', 'Snack'),
(85, 'SALSA PIC HABANERO CHOC 150 ML', 'SALSA PIC HABANERO CHOC 150 ML 740009 - UND - CHILANGO', 82.00, 100, 'uploads/products/68ffd93a49e42-SALSA PIC HABANERO CHOC 150 ML.png', '2025-10-27 20:42:34', 'Otros'),
(86, 'CHILE HABANERO 5 OZ ', 'CHILE HABANERO 5 OZ - UND - CABRON', 130.00, 100, 'uploads/products/68ffd98253465-CHILE HABANERO 5 OZ - UND - CABRON.png', '2025-10-27 20:43:46', 'Otros'),
(87, 'SALSA PIC PEPERONCINO 150 ML', 'SALSA PIC PEPERONCINO 150 ML 740023 - UND - CHILANGO', 82.00, 100, 'uploads/products/68ffd9b9d911f-SALSA PIC PEPERONCINO 150 ML 740023 - UND - CHILANGO.png', '2025-10-27 20:44:41', 'Otros'),
(88, 'MANI CON CHILE 80 GRS ', 'MANI CON CHILE 80 GRS - UND - PRO', 30.00, 500, 'uploads/products/68ffd9f84c083-MANI CON CHILE 80 GRS - UND - PRO.png', '2025-10-27 20:45:44', 'Snack'),
(89, 'Tajin en polvo', 'SAZONADOR DE FRUTAS EN POLVO 5 OZ - UND - TAJIN\r\n', 115.00, 500, 'uploads/products/68ffda69d9d5f-SAZONADOR DE FRUTAS EN POLVO 5 OZ - UND - TAJIN.png', '2025-10-27 20:47:37', 'Otros'),
(90, 'SMIRNOFF', 'MEZCLA VODKA CON LIMON 12 OZ - UND - SMIRNOFF', 80.00, 100, 'uploads/products/68ffdaaa69076-MEZCLA VODKA CON LIMON 12 OZ - UND - SMIRNOFF.png', '2025-10-27 20:48:42', 'Bebida alcohólica con sabor'),
(91, 'JUGO DE NARANJA TT 1000 ML', 'JUGO DE NARANJA TT 1000 ML 008555 - UND - DE LA GRANJA', 68.00, 50, 'uploads/products/68ffdaf369244-JUGO DE NARANJA TT 1000 ML 008555 - UND - DE LA GRANJA.png', '2025-10-27 20:49:55', 'Bebida'),
(92, 'JUGO CITRUC PUNCH', 'JUGO CITRUC PUNCH 1/2 GLN - UND - TAMPICO', 82.00, 50, 'uploads/products/68ffdb354e911-JUGO CITRUC PUNCH 12 GLN - UND - TAMPICO.png', '2025-10-27 20:51:01', 'Bebida'),
(93, 'GASEOSA C.COLA+FRESCA 3 LT 2PK', 'GASEOSA C.COLA+FRESCA 3 LT 2PK 597556 - UND - COCA COLA', 140.00, 50, 'uploads/products/68ffdb83909b4-GASEOSA C.COLA+FRESCA 3 LT 2PK 597556 - UND - COCA COLA.png', '2025-10-27 20:52:19', 'Bebida'),
(94, 'SUERO ORAL FRESA 625 ML', 'SUERO ORAL FRESA 625 ML - UND - ELECTROLIT', 103.00, 50, 'uploads/products/68ffdbf768f03-SUERO ORAL FRESA 625 ML - UND - ELECTROLIT.png', '2025-10-27 20:54:15', 'Bebida'),
(95, 'SUERO ORAL MORA AZUL', 'SUERO ORAL MORA AZUL 625 ML - UND - ELECTROLIT', 103.00, 50, 'uploads/products/68ffdc43dea3c-SUERO ORAL MORA AZUL 625 ML - UND - ELECTROLIT.png', '2025-10-27 20:55:31', 'Bebida'),
(96, 'SUERO ORAL FRESA-KIWI 625 ML', 'SUERO ORAL FRESA-KIWI 625 ML - UND - ELECTROLIT', 103.00, 50, 'uploads/products/68ffdc889e191-SUERO ORAL FRESA-KIWI 625 ML - UND - ELECTROLIT.png', '2025-10-27 20:56:40', 'Bebida'),
(97, 'BEBIDA HIDRAT FRUTAS 600 ML', 'BEBIDA HIDRAT FRUTAS 600 ML 581487 - UND - POWERADE', 36.00, 50, 'uploads/products/68ffdd31c6148-BEBIDA HIDRAT FRUTAS 600 ML 581487 - UND - POWERADE.png', '2025-10-27 20:59:29', 'Bebida'),
(98, 'BEBIDA HIDRAT AVALANCHA 600 ML', 'BEBIDA HIDRAT AVALANCHA 600 ML 584488 - UND - POWERADE', 36.00, 50, 'uploads/products/68ffdd6427223-BEBIDA HIDRAT AVALANCHA 600 ML 584488 - UND - POWERADE.png', '2025-10-27 21:00:20', 'Bebida'),
(99, 'AGUA PURIFICADA 2000 ML ', 'AGUA PURIFICADA 2000 ML 90818 - UND - ALPINA', 43.00, 50, 'uploads/products/68ffdda5616db-AGUA PURIFICADA 2000 ML 90818 - UND - ALPINA.png', '2025-10-27 21:01:25', 'Bebida');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
