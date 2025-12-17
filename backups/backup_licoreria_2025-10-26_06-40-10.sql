-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: licoreria
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `caja_sesiones`
--

DROP TABLE IF EXISTS `caja_sesiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `total_ventas` decimal(10,2) DEFAULT NULL,
  `monto_final_esperado` decimal(10,2) DEFAULT NULL,
  `monto_final_real` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierta','cerrada') NOT NULL DEFAULT 'abierta',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_caja_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_sesiones`
--

LOCK TABLES `caja_sesiones` WRITE;
/*!40000 ALTER TABLE `caja_sesiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_sesiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `cedula_ruc` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula_ruc` (`cedula_ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_detalles`
--

DROP TABLE IF EXISTS `factura_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factura_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_id` (`factura_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `factura_detalles_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factura_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_detalles`
--

LOCK TABLES `factura_detalles` WRITE;
/*!40000 ALTER TABLE `factura_detalles` DISABLE KEYS */;
INSERT INTO `factura_detalles` VALUES (46,39,17,50,42.00,2100.00),(47,40,17,43,42.00,1806.00),(48,41,30,1,71.00,71.00);
/*!40000 ALTER TABLE `factura_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `turno_id` int(11) DEFAULT NULL,
  `caja_sesion_id` int(11) DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT 'Cliente General',
  `total` decimal(10,2) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_caja_sesion_id` (`caja_sesion_id`),
  KEY `facturas_ibfk_turno` (`turno_id`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `facturas_ibfk_turno` FOREIGN KEY (`turno_id`) REFERENCES `turnos_caja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_factura_caja` FOREIGN KEY (`caja_sesion_id`) REFERENCES `caja_sesiones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,2,NULL,NULL,'Manuel',1110.00,'2025-09-23 04:07:29'),(2,2,NULL,NULL,'Cliente General',370.00,'2025-09-23 04:07:57'),(3,2,NULL,NULL,'Cliente General',370.00,'2025-09-23 04:08:11'),(4,2,NULL,NULL,'Cliente General',1850.00,'2025-09-23 04:08:18'),(5,2,NULL,NULL,'Cliente General',123.00,'2025-09-23 15:00:14'),(6,1,NULL,NULL,'kk',23.00,'2025-09-23 23:46:03'),(7,1,NULL,NULL,'Cliente General',3434.00,'2025-09-24 03:54:28'),(8,1,NULL,NULL,'messi',1540.00,'2025-09-24 03:55:35'),(9,1,NULL,NULL,'messi',8855.00,'2025-09-24 03:59:34'),(10,1,NULL,NULL,'Cliente General',10302.00,'2025-09-24 04:12:35'),(11,1,NULL,NULL,'Cliente General',3434.00,'2025-09-24 23:14:17'),(12,1,NULL,NULL,'Cliente General',90.00,'2025-09-25 02:26:37'),(13,1,NULL,NULL,'Cliente General',2695.00,'2025-09-25 04:21:12'),(14,1,NULL,NULL,'Cliente General',23.00,'2025-09-25 04:23:03'),(15,1,NULL,NULL,'Cliente General',3434.00,'2025-09-25 04:49:11'),(16,1,NULL,NULL,'Cliente General',1110.00,'2025-09-25 17:05:11'),(17,1,NULL,NULL,'Ramon',385.00,'2025-09-25 18:16:54'),(18,1,NULL,NULL,'Cliente General',67.00,'2025-10-01 23:56:01'),(19,1,NULL,NULL,'Cliente General',3434.00,'2025-10-02 00:03:09'),(20,1,NULL,NULL,'Ramon',3434.00,'2025-10-02 02:15:25'),(21,1,NULL,NULL,'Cliente General',454.00,'2025-10-02 02:15:51'),(22,1,NULL,NULL,'Cliente General',233.00,'2025-10-02 02:18:04'),(23,1,NULL,NULL,'Cliente General',385.00,'2025-10-02 02:38:55'),(24,1,NULL,NULL,'Mariluz ',23.00,'2025-10-03 21:01:07'),(25,1,NULL,NULL,'Mariluz ',46.00,'2025-10-03 21:01:25'),(26,2,NULL,NULL,'Cliente General',113.00,'2025-10-04 02:55:37'),(27,1,NULL,NULL,'Cliente General',23.00,'2025-10-04 02:59:22'),(28,1,NULL,NULL,'Eliú ',3457.00,'2025-10-04 03:17:04'),(29,1,NULL,NULL,'Cliente General',385.00,'2025-10-04 03:19:11'),(30,2,NULL,NULL,'messi',23.00,'2025-10-04 18:57:04'),(31,2,NULL,NULL,'Cliente General',134.00,'2025-10-08 01:33:11'),(32,1,NULL,NULL,'Cliente General',34.00,'2025-10-13 22:27:25'),(33,1,NULL,NULL,'Cliente General',1.17,'2025-10-14 00:10:39'),(34,1,NULL,NULL,'RUC: 279894893843555',385.00,'2025-10-14 00:47:43'),(35,1,NULL,NULL,'messi',3.51,'2025-10-18 00:17:13'),(36,1,1,NULL,'Eliú ',34.00,'2025-10-21 00:08:25'),(37,1,1,NULL,'Cliente General',7509.00,'2025-10-21 00:08:55'),(38,1,1,NULL,'Cliente General',23.00,'2025-10-21 00:51:49'),(39,1,2,NULL,'RUC: 279894893843555',2100.00,'2025-10-22 02:23:14'),(40,1,3,NULL,'Cliente General',1806.00,'2025-10-22 02:31:46'),(41,1,5,NULL,'Cliente General',71.00,'2025-10-26 03:16:48');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `imagen_url` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `categoria` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (17,'Toña','Cerveza Toña Botella Abre Facil - 355 ml',42.00,100,'uploads/products/68f977512ab4b-Toña.png','2025-10-22 01:40:58','Cerveza'),(18,'Toña Light','Cerveza Toña Light Botella Oneway - 350ml',43.00,100,'uploads/products/68f97690d1435-Toña Light.png','2025-10-22 03:16:38','Cerveza'),(19,'Stella Artois','Contenido de alcohol por volumen:\r\n0.05',70.00,100,'uploads/products/68f9710328287-Stella.png','2025-10-23 00:04:19','Cerveza'),(20,'Toña Ultra Lata',' Cerveza Tona Ultra 4pk 350mlCerveza Tona Ultra',42.00,100,'uploads/products/68f9728da9fd8-Toña Ultra lata.png','2025-10-23 00:09:36','Cerveza'),(21,'Heineken','CERVEZA BOTELLA 355 ML 16089 - UND - HEINEKEN',70.00,100,'uploads/products/68f973728e7ee-Heineken .png','2025-10-23 00:14:42','Cerveza'),(22,'Sol','CERVEZA BOTELLA 355 ML - UND - SOL',65.00,100,'uploads/products/68f973d924a74-Sol .png','2025-10-23 00:16:25','Cerveza'),(23,'Miller','CERVEZA MILLER LITE BOTELLA 12OZ - UND - MILLER',50.00,100,'uploads/products/68f9748d8ef84-Lite.png','2025-10-23 00:19:25','Cerveza'),(24,'Frost','CERVEZA BOTELLA ONE WAY 12 OZ N/R - UND - VICTORIA FROST',37.00,100,'uploads/products/68f974e7d7b2b-Frost.png','2025-10-23 00:20:55','Cerveza'),(25,'Maestro','Cerveza Victoria Maestro Botella - 355ml',46.00,100,'uploads/products/68f975927703a-Maestro.png','2025-10-23 00:23:46','Cerveza'),(26,'Clásica ','CERVEZA CLASICA BOTELLA 12 OZ - UND - VICTORIA',45.00,100,'uploads/products/68f9761f109e1-Clasica.png','2025-10-23 00:26:07','Cerveza'),(27,'Premium ','Cerveza Premium. Vidrio',50.00,100,'uploads/products/68f9798d48afc-Premium .png','2025-10-23 00:38:48','Cerveza'),(28,'Moropotente','Cerveza Moropotente lado oscuto - 350 ml',85.00,100,'uploads/products/68f97a6906dd6-Moropotente.png','2025-10-23 00:44:25','Cerveza'),(29,'Dark Lager','VICTORIA DARK LAGER',50.00,100,'uploads/products/68f97b962aa55-Dark Lager Triple malta.png','2025-10-23 00:49:26','Cerveza'),(30,'Bliss','Bebida Bliss Happy Mix Alcoholica - 350ml',71.00,99,'uploads/products/68f97e086c056-Bliss.png','2025-10-23 00:59:52','Bebida alcohólica con sabor'),(31,'Gallo','Cerveza Gallo Und 350ml',60.00,100,'uploads/products/68f980172a949-Gallo.png','2025-10-23 01:08:39','Cerveza'),(32,'Corona','Cerveza Corona botella - 330 ml',80.00,100,'uploads/products/68fc381c03708-Corona.png','2025-10-25 02:38:20','Cerveza'),(33,'Brahama','Brasil. Se fundó en 1888 y en 1934 lanzó su popular Brahma Chopp. ',70.00,100,'uploads/products/68fc38e5db591-Brahama.png','2025-10-25 02:41:41','Cerveza'),(34,'Modelo ','Esta cerveza equilibrada y fácil de beber contiene 145 calorías , 0 gramos de grasa y 4,4 % de alcohol por cada 355 ml ',71.00,100,'uploads/products/68fc395083a0b-Modelo .png','2025-10-25 02:43:28','Cerveza'),(35,'Cristalino ','RON AÑEJO CRISTALINO 375 ML - UND - FLOR DE CA¥A',491.00,100,'uploads/products/68fc3e237c6b5-Cristalino .png','2025-10-25 03:04:03','Ron'),(37,'JOHN BARR','WHISKY ROJO BOT 750 ML 003972 - UND - JOHN BARR',570.00,100,'uploads/products/68fd92ed4098d-John Barr.png','2025-10-26 03:18:05','Whisky'),(38,'JACK DANIEL\'S','WHISKY 750 ML - UND - JACK DANIEL\'S',2000.00,100,'uploads/products/68fd93b292345-JACK DANIEL\'S.png','2025-10-26 03:21:22','Whisky');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respaldos`
--

DROP TABLE IF EXISTS `respaldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respaldos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `respaldos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respaldos`
--

LOCK TABLES `respaldos` WRITE;
/*!40000 ALTER TABLE `respaldos` DISABLE KEYS */;
/*!40000 ALTER TABLE `respaldos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turnos_caja`
--

DROP TABLE IF EXISTS `turnos_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `turnos_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `capital_inicial` decimal(10,2) NOT NULL,
  `total_ventas_calculado` decimal(10,2) DEFAULT NULL,
  `monto_final_real` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierto','cerrado') NOT NULL DEFAULT 'abierto',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `turnos_caja_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turnos_caja`
--

LOCK TABLES `turnos_caja` WRITE;
/*!40000 ALTER TABLE `turnos_caja` DISABLE KEYS */;
INSERT INTO `turnos_caja` VALUES (1,1,1000.00,7566.00,8566.00,0.00,'2025-10-20 18:07:58','2025-10-21 19:44:19','cerrado'),(2,2,1000.00,2100.00,3000.00,-100.00,'2025-10-21 20:05:07','2025-10-21 20:23:52','cerrado'),(3,1,1500.00,1806.00,3310.00,4.00,'2025-10-21 20:31:29','2025-10-21 20:32:12','cerrado'),(4,1,1500.00,0.00,1500.00,0.00,'2025-10-21 21:15:27','2025-10-22 17:29:02','cerrado'),(5,1,1500.00,NULL,NULL,NULL,'2025-10-22 17:57:00',NULL,'abierto');
/*!40000 ALTER TABLE `turnos_caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','empleado') DEFAULT 'empleado',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@licoreria.com','$2y$10$g.QRBP6wae3NQp8wQZMIu.wxw9A1YkgY2ybz7wRCxYrpB5HovrdmO','admin','2025-08-21 16:03:56'),(2,'Empleado1','empleado1@licoreria.com','$2y$10$r81Q6KQYaobUHSZsPMTsi.X5j.GHmvGVtmpalnIusw5M90AoQwMUG','empleado','2025-08-21 16:07:44');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 23:40:10
