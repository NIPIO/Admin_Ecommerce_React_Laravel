-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 20-04-2022 a las 17:16:09
-- Versión del servidor: 8.0.13-4
-- Versión de PHP: 7.2.24-0ubuntu0.18.04.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `BUN0rZomiP`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_caja` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `importe` double NOT NULL DEFAULT '0',
  `usuario` int(11) DEFAULT NULL,
  `observacion` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caja`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `telefono`, `created_at`, `updated_at`, `activo`) VALUES
(1, 'RAMIRO PAME', NULL, NULL, '2022-04-11 18:24:19', '2022-04-20 13:52:14', 1),
(2, 'JUANMA XIAOMI', NULL, NULL, '2022-04-20 13:59:35', '2022-04-20 13:59:35', 1),
(3, 'SEBA TIENDA PALERMO', NULL, NULL, '2022-04-20 13:59:53', '2022-04-20 13:59:53', 1),
(4, 'ENZO FLA', NULL, NULL, '2022-04-20 14:00:06', '2022-04-20 14:00:06', 1),
(5, 'AGUSTIN APPLE HAEDO', NULL, NULL, '2022-04-20 14:00:12', '2022-04-20 14:00:12', 1),
(6, 'FER DITRONICA', NULL, NULL, '2022-04-20 14:00:15', '2022-04-20 14:00:15', 1),
(7, 'ADOLFO MOTECH', NULL, NULL, '2022-04-20 14:00:48', '2022-04-20 14:00:48', 1),
(8, 'AGUSTIN SOUTH SIDE', NULL, NULL, '2022-04-20 14:00:54', '2022-04-20 14:00:54', 1),
(9, 'BARBIE TECNOSUR', NULL, NULL, '2022-04-20 14:00:59', '2022-04-20 14:00:59', 1),
(10, 'MARCIO', NULL, NULL, '2022-04-20 14:01:06', '2022-04-20 14:01:06', 1),
(11, 'ICLUB', NULL, NULL, '2022-04-20 14:01:15', '2022-04-20 14:01:15', 1),
(12, 'E.LECTRONIC BLUE', NULL, NULL, '2022-04-20 14:01:22', '2022-04-20 14:01:22', 1),
(13, 'BATEL', NULL, NULL, '2022-04-20 14:01:29', '2022-04-20 14:01:29', 1),
(14, 'IVAN WEMOVIL', NULL, NULL, '2022-04-20 14:01:31', '2022-04-20 14:01:31', 1),
(15, 'IGNACIO', NULL, NULL, '2022-04-20 14:01:39', '2022-04-20 14:01:39', 1),
(16, 'ORDUNA', NULL, NULL, '2022-04-20 14:01:40', '2022-04-20 14:01:40', 1),
(17, 'MOTOCELL', NULL, NULL, '2022-04-20 14:01:53', '2022-04-20 14:01:53', 1),
(18, 'FEDE MOTO RAMIRO', NULL, NULL, '2022-04-20 14:01:57', '2022-04-20 14:01:57', 1),
(19, 'CARLOS MI', NULL, NULL, '2022-04-20 14:02:00', '2022-04-20 14:02:00', 1),
(20, 'PABLO WEMOVIL', NULL, NULL, '2022-04-20 14:02:08', '2022-04-20 14:02:08', 1),
(21, 'HECTOR NAHUEL PAME', NULL, NULL, '2022-04-20 14:02:12', '2022-04-20 14:02:12', 1),
(22, 'RICKY', NULL, NULL, '2022-04-20 14:02:17', '2022-04-20 14:02:17', 1),
(23, 'JULIAN APPLE POINT', NULL, NULL, '2022-04-20 14:02:25', '2022-04-20 14:02:25', 1),
(24, 'FEDE RICKY', NULL, NULL, '2022-04-20 14:02:32', '2022-04-20 14:02:32', 1),
(25, 'LUCAS SOUTH', NULL, NULL, '2022-04-20 14:02:36', '2022-04-20 14:02:36', 1),
(26, 'AXEL SYSTECH', NULL, NULL, '2022-04-20 14:02:41', '2022-04-20 14:02:41', 1),
(27, 'GUIDO BETECH', NULL, NULL, '2022-04-20 14:02:49', '2022-04-20 14:02:49', 1),
(28, 'TOMAS/RAMIRO PAME', NULL, NULL, '2022-04-20 14:02:53', '2022-04-20 14:02:53', 1),
(29, 'JONAS BAIRES', NULL, NULL, '2022-04-20 14:02:57', '2022-04-20 14:02:57', 1),
(30, 'FEDE ICONS TECNO', NULL, NULL, '2022-04-20 14:03:06', '2022-04-20 14:03:06', 1),
(31, 'NICOLAS HOTMOBILE', NULL, NULL, '2022-04-20 14:03:18', '2022-04-20 14:03:18', 1),
(32, 'UPCELL', NULL, NULL, '2022-04-20 14:03:28', '2022-04-20 14:03:28', 1),
(33, 'ARIEL IMPORTACIONES', NULL, NULL, '2022-04-20 14:03:37', '2022-04-20 14:03:37', 1),
(34, 'ARGENTECNO', NULL, NULL, '2022-04-20 14:03:53', '2022-04-20 14:03:53', 1),
(35, 'EITAN', NULL, NULL, '2022-04-20 14:04:00', '2022-04-20 14:04:00', 1),
(36, 'JUAN PABLO MGM', NULL, NULL, '2022-04-20 14:04:14', '2022-04-20 14:04:14', 1),
(37, 'CITYTRONIC', NULL, NULL, '2022-04-20 14:04:25', '2022-04-20 14:04:25', 1),
(38, 'FERNANDA IMPORTACIONES', NULL, NULL, '2022-04-20 14:04:37', '2022-04-20 14:04:37', 1),
(39, 'ALEXIS', NULL, NULL, '2022-04-20 14:04:47', '2022-04-20 14:04:47', 1),
(40, 'CRISTIAN MOTECH', NULL, NULL, '2022-04-20 14:04:57', '2022-04-20 14:04:57', 1),
(41, 'KARINA KG', NULL, NULL, '2022-04-20 14:05:07', '2022-04-20 14:05:07', 1),
(42, 'DARWIN', NULL, NULL, '2022-04-20 14:05:14', '2022-04-20 14:05:14', 1),
(43, 'FEDE TOTALTECH', NULL, NULL, '2022-04-20 14:05:29', '2022-04-20 14:05:29', 1),
(44, 'MATIAS TECNOSUR', NULL, NULL, '2022-04-20 14:05:37', '2022-04-20 14:05:37', 1),
(45, 'AGUSTIN SMARTONE', NULL, NULL, '2022-04-20 14:05:52', '2022-04-20 14:05:52', 1),
(46, 'ARIEL ARES CELULARES', NULL, NULL, '2022-04-20 14:06:01', '2022-04-20 14:06:01', 1),
(47, 'TECNOCELL', NULL, NULL, '2022-04-20 14:06:11', '2022-04-20 14:06:11', 1),
(48, 'AGUSTINA FLUOR', NULL, NULL, '2022-04-20 14:06:22', '2022-04-20 14:06:22', 1),
(49, 'BAIRES PHONES', NULL, NULL, '2022-04-20 14:06:31', '2022-04-20 14:06:31', 1),
(50, 'BETA CELULARES', NULL, NULL, '2022-04-20 14:06:51', '2022-04-20 14:06:51', 1),
(51, 'MATIAS TECNOMOUSE', NULL, NULL, '2022-04-20 14:07:05', '2022-04-20 14:07:05', 1),
(52, 'ABRACADABRA', NULL, NULL, '2022-04-20 14:07:15', '2022-04-20 14:07:15', 1),
(53, 'DANIEL LAS PETRONAS', NULL, NULL, '2022-04-20 14:07:24', '2022-04-20 14:07:24', 1),
(54, 'JAVIER CELL POWER', NULL, NULL, '2022-04-20 14:07:33', '2022-04-20 14:07:33', 1),
(55, 'MATIAS TECHNO', NULL, NULL, '2022-04-20 14:07:53', '2022-04-20 14:07:53', 1),
(56, 'IVAN PHONE WORLD', NULL, NULL, '2022-04-20 14:08:02', '2022-04-20 14:08:02', 1),
(57, 'EUGE IMPORTACIONES', NULL, NULL, '2022-04-20 14:08:12', '2022-04-20 14:08:12', 1),
(58, 'MARTIN FOTOINSUMOS', NULL, NULL, '2022-04-20 14:08:22', '2022-04-20 14:08:22', 1),
(59, 'INAC', NULL, NULL, '2022-04-20 14:08:35', '2022-04-20 14:08:35', 1),
(60, 'HERNAN TECHNO', NULL, NULL, '2022-04-20 14:08:44', '2022-04-20 14:08:44', 1),
(61, 'COSTANZA EMILIO', NULL, NULL, '2022-04-20 14:08:52', '2022-04-20 14:08:52', 1),
(62, 'ANDRELAINA TECNOSUR', NULL, NULL, '2022-04-20 14:09:07', '2022-04-20 14:09:07', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `costo` double NOT NULL DEFAULT '0',
  `fecha_compra` date DEFAULT NULL,
  `tipo_caja` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `confirmada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_detalle`
--

CREATE TABLE `compras_detalle` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `costo` double DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_corrientes`
--

CREATE TABLE `cuentas_corrientes` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `saldo` int(11) DEFAULT NULL,
  `tipo_cuenta` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuentas_corrientes`
--

INSERT INTO `cuentas_corrientes` (`id`, `proveedor_id`, `cliente_id`, `saldo`, `tipo_cuenta`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, -3000, 'p', 1, '2022-04-13 14:10:12', '2022-04-13 14:10:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `nombre`, `created_at`, `updated_at`, `activo`) VALUES
(1, 'Samsung', '2022-04-11 18:24:19', '2022-04-11 18:24:19', 1),
(2, 'APPLE', '2022-04-20 13:51:00', '2022-04-20 13:51:00', 1),
(3, 'XIAOMI', '2022-04-20 13:51:06', '2022-04-20 13:51:06', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2022_03_14_190404_create_caja_table', 1),
(3, '2022_03_14_190405_create_clientes_table', 1),
(4, '2022_03_14_190406_create_proveedores_table', 1),
(5, '2022_03_14_190407_create_compras_table', 1),
(6, '2022_03_14_190408_create_marcas_table', 1),
(7, '2022_03_14_190409_create_productos_table', 1),
(8, '2022_03_14_190410_create_compras_detalle_table', 1),
(9, '2022_03_14_190411_create_cuentas_corrientes_table', 1),
(10, '2022_03_14_190413_create_roles_table', 1),
(11, '2022_03_14_190414_create_vendedores_table', 1),
(12, '2022_03_14_190415_create_movimientos_table', 1),
(13, '2022_03_14_190416_create_permisos_table', 1),
(14, '2022_03_14_190418_create_ventas_table', 1),
(15, '2022_03_14_190419_create_ventas_detalle_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `tabla` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_movimiento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `usuario` int(11) DEFAULT NULL,
  `estado_viejo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_nuevo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diferencia` int(11) DEFAULT NULL,
  `campo_modificado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `tabla`, `tipo_movimiento`, `item_id`, `usuario`, `estado_viejo`, `estado_nuevo`, `diferencia`, `campo_modificado`, `created_at`, `updated_at`) VALUES
(1, 'cuentas_corrientes', 'ALTA', 1, 1, NULL, NULL, NULL, NULL, '2022-04-13 14:10:12', '2022-04-13 14:10:12'),
(2, 'cuentas_corrientes', 'PAGO', 1, 1, NULL, NULL, 3000, NULL, '2022-04-13 14:10:22', '2022-04-13 14:10:22'),
(3, 'cuentas_corrientes', 'COBRO', 1, 1, NULL, NULL, 6000, NULL, '2022-04-13 14:10:38', '2022-04-13 14:10:38'),
(4, 'marcas', 'ALTA', 2, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:51:00', '2022-04-20 13:51:00'),
(5, 'marcas', 'ALTA', 3, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:51:07', '2022-04-20 13:51:07'),
(6, 'productos', 'ALTA', 3, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:51:25', '2022-04-20 13:51:25'),
(7, 'clientes', 'MODIFICACION', 1, 1, 'Clientes 1', 'RAMIRO PAME', NULL, 'nombre', '2022-04-20 13:52:14', '2022-04-20 13:52:14'),
(8, 'proveedores', 'MODIFICACION', 1, 1, 'Proveedor 1', 'CHULOS', NULL, 'nombre', '2022-04-20 13:52:45', '2022-04-20 13:52:45'),
(9, 'proveedores', 'ALTA', 2, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:52:54', '2022-04-20 13:52:54'),
(10, 'proveedores', 'ALTA', 3, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:53:20', '2022-04-20 13:53:20'),
(11, 'proveedores', 'ALTA', 4, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:53:26', '2022-04-20 13:53:26'),
(12, 'clientes', 'ALTA', 2, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:59:35', '2022-04-20 13:59:35'),
(13, 'clientes', 'ALTA', 3, 1, NULL, NULL, NULL, NULL, '2022-04-20 13:59:54', '2022-04-20 13:59:54'),
(14, 'clientes', 'ALTA', 4, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:06', '2022-04-20 14:00:06'),
(15, 'clientes', 'ALTA', 5, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:12', '2022-04-20 14:00:12'),
(16, 'clientes', 'ALTA', 6, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:15', '2022-04-20 14:00:15'),
(17, 'clientes', 'ALTA', 7, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:48', '2022-04-20 14:00:48'),
(18, 'clientes', 'ALTA', 8, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:54', '2022-04-20 14:00:54'),
(19, 'clientes', 'ALTA', 9, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:00:59', '2022-04-20 14:00:59'),
(20, 'clientes', 'ALTA', 10, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:06', '2022-04-20 14:01:06'),
(21, 'clientes', 'ALTA', 11, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:15', '2022-04-20 14:01:15'),
(22, 'clientes', 'ALTA', 12, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:22', '2022-04-20 14:01:22'),
(23, 'clientes', 'ALTA', 13, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:30', '2022-04-20 14:01:30'),
(24, 'clientes', 'ALTA', 14, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:31', '2022-04-20 14:01:31'),
(25, 'clientes', 'ALTA', 15, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:39', '2022-04-20 14:01:39'),
(26, 'clientes', 'ALTA', 16, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:40', '2022-04-20 14:01:40'),
(27, 'clientes', 'ALTA', 17, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:54', '2022-04-20 14:01:54'),
(28, 'clientes', 'ALTA', 18, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:01:58', '2022-04-20 14:01:58'),
(29, 'clientes', 'ALTA', 19, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:00', '2022-04-20 14:02:00'),
(30, 'clientes', 'ALTA', 20, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:08', '2022-04-20 14:02:08'),
(31, 'clientes', 'ALTA', 21, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:12', '2022-04-20 14:02:12'),
(32, 'clientes', 'ALTA', 22, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:17', '2022-04-20 14:02:17'),
(33, 'clientes', 'ALTA', 23, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:25', '2022-04-20 14:02:25'),
(34, 'clientes', 'ALTA', 24, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:32', '2022-04-20 14:02:32'),
(35, 'clientes', 'ALTA', 25, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:37', '2022-04-20 14:02:37'),
(36, 'clientes', 'ALTA', 26, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:41', '2022-04-20 14:02:41'),
(37, 'clientes', 'ALTA', 27, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:49', '2022-04-20 14:02:49'),
(38, 'clientes', 'ALTA', 28, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:53', '2022-04-20 14:02:53'),
(39, 'clientes', 'ALTA', 29, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:02:57', '2022-04-20 14:02:57'),
(40, 'clientes', 'ALTA', 30, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:07', '2022-04-20 14:03:07'),
(41, 'productos', 'ALTA', 4, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:14', '2022-04-20 14:03:14'),
(42, 'clientes', 'ALTA', 31, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:18', '2022-04-20 14:03:18'),
(43, 'clientes', 'ALTA', 32, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:29', '2022-04-20 14:03:29'),
(44, 'clientes', 'ALTA', 33, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:37', '2022-04-20 14:03:37'),
(45, 'productos', 'ALTA', 5, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:46', '2022-04-20 14:03:46'),
(46, 'clientes', 'ALTA', 34, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:03:54', '2022-04-20 14:03:54'),
(47, 'clientes', 'ALTA', 35, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:00', '2022-04-20 14:04:00'),
(48, 'clientes', 'ALTA', 36, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:14', '2022-04-20 14:04:14'),
(49, 'clientes', 'ALTA', 37, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:25', '2022-04-20 14:04:25'),
(50, 'clientes', 'ALTA', 38, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:37', '2022-04-20 14:04:37'),
(51, 'clientes', 'ALTA', 39, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:48', '2022-04-20 14:04:48'),
(52, 'clientes', 'ALTA', 40, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:04:57', '2022-04-20 14:04:57'),
(53, 'clientes', 'ALTA', 41, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:05:07', '2022-04-20 14:05:07'),
(54, 'clientes', 'ALTA', 42, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:05:14', '2022-04-20 14:05:14'),
(55, 'clientes', 'ALTA', 43, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:05:29', '2022-04-20 14:05:29'),
(56, 'clientes', 'ALTA', 44, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:05:37', '2022-04-20 14:05:37'),
(57, 'clientes', 'ALTA', 45, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:05:52', '2022-04-20 14:05:52'),
(58, 'clientes', 'ALTA', 46, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:06:02', '2022-04-20 14:06:02'),
(59, 'clientes', 'ALTA', 47, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:06:11', '2022-04-20 14:06:11'),
(60, 'clientes', 'ALTA', 48, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:06:22', '2022-04-20 14:06:22'),
(61, 'clientes', 'ALTA', 49, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:06:32', '2022-04-20 14:06:32'),
(62, 'clientes', 'ALTA', 50, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:06:52', '2022-04-20 14:06:52'),
(63, 'clientes', 'ALTA', 51, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:07:05', '2022-04-20 14:07:05'),
(64, 'clientes', 'ALTA', 52, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:07:16', '2022-04-20 14:07:16'),
(65, 'clientes', 'ALTA', 53, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:07:24', '2022-04-20 14:07:24'),
(66, 'clientes', 'ALTA', 54, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:07:33', '2022-04-20 14:07:33'),
(67, 'clientes', 'ALTA', 55, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:07:53', '2022-04-20 14:07:53'),
(68, 'clientes', 'ALTA', 56, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:02', '2022-04-20 14:08:02'),
(69, 'clientes', 'ALTA', 57, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:12', '2022-04-20 14:08:12'),
(70, 'clientes', 'ALTA', 58, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:22', '2022-04-20 14:08:22'),
(71, 'clientes', 'ALTA', 59, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:36', '2022-04-20 14:08:36'),
(72, 'clientes', 'ALTA', 60, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:44', '2022-04-20 14:08:44'),
(73, 'clientes', 'ALTA', 61, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:08:52', '2022-04-20 14:08:52'),
(74, 'clientes', 'ALTA', 62, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:09:07', '2022-04-20 14:09:07'),
(75, 'productos', 'ALTA', 6, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:11:12', '2022-04-20 14:11:12'),
(76, 'productos', 'ALTA', 7, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:11:29', '2022-04-20 14:11:29'),
(77, 'productos', 'ALTA', 8, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:19:52', '2022-04-20 14:19:52'),
(78, 'productos', 'MODIFICACION', 8, 1, '0', '0', NULL, 'costo', '2022-04-20 14:20:01', '2022-04-20 14:20:01'),
(79, 'productos', 'MODIFICACION', 8, 1, '0', '0', NULL, 'costo', '2022-04-20 14:20:28', '2022-04-20 14:20:28'),
(80, 'productos', 'ESTADO', 8, 1, 'activo', 'inactivo', NULL, NULL, '2022-04-20 14:20:53', '2022-04-20 14:20:53'),
(81, 'productos', 'MODIFICACION', 3, 1, '0', '13', NULL, 'stock', '2022-04-20 14:21:09', '2022-04-20 14:21:09'),
(82, 'productos', 'MODIFICACION', 3, 1, '0', '592', NULL, 'costo', '2022-04-20 14:21:09', '2022-04-20 14:21:09'),
(83, 'productos', 'ALTA', 9, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:21:32', '2022-04-20 14:21:32'),
(84, 'productos', 'ALTA', 10, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:21:52', '2022-04-20 14:21:52'),
(85, 'productos', 'ALTA', 11, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:23:03', '2022-04-20 14:23:03'),
(86, 'productos', 'ALTA', 12, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:23:04', '2022-04-20 14:23:04'),
(87, 'productos', 'ALTA', 13, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:23:22', '2022-04-20 14:23:22'),
(88, 'productos', 'ALTA', 14, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:23:43', '2022-04-20 14:23:43'),
(89, 'productos', 'ALTA', 15, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:24:01', '2022-04-20 14:24:01'),
(90, 'productos', 'ALTA', 16, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:26:03', '2022-04-20 14:26:03'),
(91, 'productos', 'ALTA', 17, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:26:23', '2022-04-20 14:26:23'),
(92, 'productos', 'ALTA', 18, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:26:30', '2022-04-20 14:26:30'),
(93, 'productos', 'ALTA', 19, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:26:47', '2022-04-20 14:26:47'),
(94, 'productos', 'ALTA', 20, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:26:48', '2022-04-20 14:26:48'),
(95, 'productos', 'ALTA', 21, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:27:04', '2022-04-20 14:27:04'),
(96, 'productos', 'ALTA', 22, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:27:08', '2022-04-20 14:27:08'),
(97, 'productos', 'ALTA', 23, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:27:26', '2022-04-20 14:27:26'),
(98, 'productos', 'MODIFICACION', 4, 1, '0', '3', NULL, 'stock', '2022-04-20 14:27:30', '2022-04-20 14:27:30'),
(99, 'productos', 'MODIFICACION', 4, 1, '0', '592', NULL, 'costo', '2022-04-20 14:27:30', '2022-04-20 14:27:30'),
(100, 'productos', 'ALTA', 24, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:27:43', '2022-04-20 14:27:43'),
(101, 'productos', 'MODIFICACION', 6, 1, '0', '5', NULL, 'stock', '2022-04-20 14:27:58', '2022-04-20 14:27:58'),
(102, 'productos', 'MODIFICACION', 6, 1, '0', '592', NULL, 'costo', '2022-04-20 14:27:58', '2022-04-20 14:27:58'),
(103, 'productos', 'ALTA', 25, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:28:22', '2022-04-20 14:28:22'),
(104, 'productos', 'ALTA', 26, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:28:54', '2022-04-20 14:28:54'),
(105, 'productos', 'ALTA', 27, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:29:22', '2022-04-20 14:29:22'),
(106, 'productos', 'ALTA', 28, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:29:49', '2022-04-20 14:29:49'),
(107, 'productos', 'MODIFICACION', 21, 1, '0', '10', NULL, 'stock', '2022-04-20 14:30:31', '2022-04-20 14:30:31'),
(108, 'productos', 'MODIFICACION', 21, 1, '0', '1000', NULL, 'costo', '2022-04-20 14:30:31', '2022-04-20 14:30:31'),
(109, 'productos', 'ALTA', 29, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:31:43', '2022-04-20 14:31:43'),
(110, 'productos', 'ALTA', 30, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:33:10', '2022-04-20 14:33:10'),
(111, 'productos', 'ALTA', 31, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:33:28', '2022-04-20 14:33:28'),
(112, 'productos', 'ALTA', 32, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:33:38', '2022-04-20 14:33:38'),
(113, 'productos', 'ALTA', 33, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:33:55', '2022-04-20 14:33:55'),
(114, 'productos', 'ALTA', 34, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:34:13', '2022-04-20 14:34:13'),
(115, 'productos', 'ALTA', 35, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:34:15', '2022-04-20 14:34:15'),
(116, 'productos', 'ALTA', 36, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:34:46', '2022-04-20 14:34:46'),
(117, 'productos', 'ALTA', 37, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:34:52', '2022-04-20 14:34:52'),
(118, 'productos', 'MODIFICACION', 33, 1, '0', '0', NULL, 'costo', '2022-04-20 14:34:57', '2022-04-20 14:34:57'),
(119, 'productos', 'MODIFICACION', 33, 1, '0', '0', NULL, 'costo', '2022-04-20 14:35:00', '2022-04-20 14:35:00'),
(120, 'productos', 'ALTA', 38, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:05', '2022-04-20 14:35:05'),
(121, 'productos', 'ALTA', 39, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:10', '2022-04-20 14:35:10'),
(122, 'productos', 'ALTA', 40, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:15', '2022-04-20 14:35:15'),
(123, 'productos', 'ALTA', 41, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:28', '2022-04-20 14:35:28'),
(124, 'productos', 'ESTADO', 33, 1, 'activo', 'inactivo', NULL, NULL, '2022-04-20 14:35:42', '2022-04-20 14:35:42'),
(125, 'productos', 'ALTA', 42, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:43', '2022-04-20 14:35:43'),
(126, 'productos', 'ALTA', 43, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:43', '2022-04-20 14:35:43'),
(127, 'productos', 'ALTA', 44, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:35:59', '2022-04-20 14:35:59'),
(128, 'productos', 'ESTADO', 33, 1, 'inactivo', 'activo', NULL, NULL, '2022-04-20 14:36:32', '2022-04-20 14:36:32'),
(129, 'productos', 'ESTADO', 33, 1, 'inactivo', 'activo', NULL, NULL, '2022-04-20 14:36:34', '2022-04-20 14:36:34'),
(130, 'productos', 'ALTA', 45, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:36:48', '2022-04-20 14:36:48'),
(131, 'productos', 'ALTA', 46, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:37:02', '2022-04-20 14:37:02'),
(132, 'productos', 'ALTA', 47, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:38:57', '2022-04-20 14:38:57'),
(133, 'productos', 'ALTA', 48, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:38:57', '2022-04-20 14:38:57'),
(134, 'productos', 'ALTA', 49, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:08', '2022-04-20 14:39:08'),
(135, 'productos', 'ALTA', 50, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:16', '2022-04-20 14:39:16'),
(136, 'productos', 'ALTA', 51, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:35', '2022-04-20 14:39:35'),
(137, 'productos', 'ALTA', 52, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:36', '2022-04-20 14:39:36'),
(138, 'productos', 'ALTA', 53, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:42', '2022-04-20 14:39:42'),
(139, 'productos', 'ALTA', 54, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:39:58', '2022-04-20 14:39:58'),
(140, 'productos', 'ALTA', 55, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:40:17', '2022-04-20 14:40:17'),
(141, 'productos', 'ALTA', 56, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:40:19', '2022-04-20 14:40:19'),
(142, 'productos', 'ALTA', 57, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:40:26', '2022-04-20 14:40:26'),
(143, 'productos', 'ALTA', 58, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:40:39', '2022-04-20 14:40:39'),
(144, 'productos', 'MODIFICACION', 53, 1, '0', '1', NULL, 'stock', '2022-04-20 14:40:59', '2022-04-20 14:40:59'),
(145, 'productos', 'MODIFICACION', 53, 1, '0', '510', NULL, 'costo', '2022-04-20 14:40:59', '2022-04-20 14:40:59'),
(146, 'productos', 'ALTA', 59, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:41:01', '2022-04-20 14:41:01'),
(147, 'productos', 'MODIFICACION', 49, 1, '0', '1', NULL, 'stock', '2022-04-20 14:41:31', '2022-04-20 14:41:31'),
(148, 'productos', 'MODIFICACION', 49, 1, '0', '510', NULL, 'costo', '2022-04-20 14:41:32', '2022-04-20 14:41:32'),
(149, 'productos', 'MODIFICACION', 35, 1, '0', '1', NULL, 'stock', '2022-04-20 14:51:02', '2022-04-20 14:51:02'),
(150, 'productos', 'MODIFICACION', 35, 1, '0', '1253', NULL, 'costo', '2022-04-20 14:51:03', '2022-04-20 14:51:03'),
(151, 'productos', 'MODIFICACION', 54, 1, '0', '1', NULL, 'stock', '2022-04-20 14:51:44', '2022-04-20 14:51:44'),
(152, 'productos', 'MODIFICACION', 54, 1, '0', '1353', NULL, 'costo', '2022-04-20 14:51:45', '2022-04-20 14:51:45'),
(153, 'productos', 'ALTA', 60, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:52:43', '2022-04-20 14:52:43'),
(154, 'productos', 'ALTA', 61, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:53:05', '2022-04-20 14:53:05'),
(155, 'productos', 'ALTA', 62, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:53:32', '2022-04-20 14:53:32'),
(156, 'productos', 'ALTA', 63, 1, NULL, NULL, NULL, NULL, '2022-04-20 14:55:31', '2022-04-20 14:55:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `rol_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` int(11) NOT NULL DEFAULT '0',
  `costo` double NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_reservado` int(11) NOT NULL DEFAULT '0',
  `en_transito` int(11) NOT NULL DEFAULT '0',
  `en_transito_reservado` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `marca`, `costo`, `stock`, `stock_reservado`, `en_transito`, `en_transito_reservado`, `created_at`, `updated_at`, `activo`) VALUES
(3, 'IP 11 64 BLACK', 2, 592, 13, 0, 0, 0, '2022-04-20 13:51:24', '2022-04-20 14:21:10', 1),
(4, 'IP 11 64 WHITE', 2, 592, 3, 0, 0, 0, '2022-04-20 14:03:14', '2022-04-20 14:27:30', 1),
(5, 'IP 11 64 RED', 2, 0, 0, 0, 0, 0, '2022-04-20 14:03:46', '2022-04-20 14:03:46', 1),
(6, 'IP 11 64 GREEN', 2, 592, 5, 0, 0, 0, '2022-04-20 14:11:12', '2022-04-20 14:27:59', 1),
(7, 'IP 11 64 PURPLE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:11:29', '2022-04-20 14:11:29', 1),
(8, 'IP 13 128', 2, 0, 0, 0, 0, 0, '2022-04-20 14:19:52', '2022-04-20 14:20:53', 0),
(9, 'IP 13 128 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:21:32', '2022-04-20 14:21:32', 1),
(10, 'IP 12 64 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:21:52', '2022-04-20 14:21:52', 1),
(11, 'IP 12 64 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:23:03', '2022-04-20 14:23:03', 1),
(12, 'IP 13 128 RED', 2, 0, 0, 0, 0, 0, '2022-04-20 14:23:04', '2022-04-20 14:23:04', 1),
(13, 'IP 13 128 PINK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:23:22', '2022-04-20 14:23:22', 1),
(14, 'IP 13 128 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:23:43', '2022-04-20 14:23:43', 1),
(15, 'IP 13 128 WHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:24:01', '2022-04-20 14:24:01', 1),
(16, 'IP 13 256 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:26:03', '2022-04-20 14:26:03', 1),
(17, 'IP 13 256 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:26:23', '2022-04-20 14:26:23', 1),
(18, 'IP 12 64 WHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:26:30', '2022-04-20 14:26:30', 1),
(19, 'IP 13 256 PINK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:26:47', '2022-04-20 14:26:47', 1),
(20, 'IP 12 64 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:26:47', '2022-04-20 14:26:47', 1),
(21, 'IP 13 256 WHITE', 2, 1000, 10, 0, 0, 0, '2022-04-20 14:27:03', '2022-04-20 14:30:32', 1),
(22, 'IP 12 128 WHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:27:08', '2022-04-20 14:27:08', 1),
(23, 'IP 12 128 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:27:26', '2022-04-20 14:27:26', 1),
(24, 'IP 12 128 PURPLE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:27:43', '2022-04-20 14:27:43', 1),
(25, 'IP 11 64 YELLOW', 2, 592, 3, 0, 0, 0, '2022-04-20 14:28:21', '2022-04-20 14:28:21', 1),
(26, 'IP 12 128 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:28:54', '2022-04-20 14:28:54', 1),
(27, 'IP 12 128 RED', 2, 0, 0, 0, 0, 0, '2022-04-20 14:29:19', '2022-04-20 14:29:19', 1),
(28, 'IP 12 128 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:29:48', '2022-04-20 14:29:48', 1),
(29, 'IP 11 128 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:31:43', '2022-04-20 14:31:43', 1),
(30, 'IP 13 PRO 128 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:33:08', '2022-04-20 14:33:08', 1),
(31, 'IP 13 PRO MAX 128 GOLD', 2, 0, 0, 0, 0, 0, '2022-04-20 14:33:27', '2022-04-20 14:33:27', 1),
(32, 'IPP 13 PRO 128 GRAPHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:33:38', '2022-04-20 14:33:38', 1),
(33, 'IP 13 PRO MAX 128 GRAPHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:33:54', '2022-04-20 14:36:34', 1),
(34, 'IP 13 PRO 128 SILVER', 2, 1153, 1, 0, 0, 0, '2022-04-20 14:34:13', '2022-04-20 14:34:13', 1),
(35, 'IP 13 PRO MAX 128 BLUE', 2, 1253, 1, 0, 0, 0, '2022-04-20 14:34:15', '2022-04-20 14:51:03', 1),
(36, 'IP 11 128 WHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:34:46', '2022-04-20 14:34:46', 1),
(37, 'IP 13 PRO 128 GOLD', 2, 1153, 2, 0, 0, 0, '2022-04-20 14:34:52', '2022-04-20 14:34:52', 1),
(38, 'IP 11 128 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:04', '2022-04-20 14:35:04', 1),
(39, 'IP 13 PRO 128 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:10', '2022-04-20 14:35:10', 1),
(40, 'IP 11 128 PURPLE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:15', '2022-04-20 14:35:15', 1),
(41, 'IP 11 128 RED', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:27', '2022-04-20 14:35:27', 1),
(42, 'IP 13 PRO 256 GRAPHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:42', '2022-04-20 14:35:42', 1),
(43, 'IP 11 128 YELLOW', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:43', '2022-04-20 14:35:43', 1),
(44, 'IP 13 PRO 256 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:35:58', '2022-04-20 14:35:58', 1),
(45, 'IP 13 PRO 256 GOLD', 2, 1254, 2, 0, 0, 0, '2022-04-20 14:36:47', '2022-04-20 14:36:47', 1),
(46, 'IP 13 PRO 256 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:37:02', '2022-04-20 14:37:02', 1),
(47, 'IP 13 PRO MAX 128 SILVER', 2, 0, 0, 0, 0, 0, '2022-04-20 14:38:56', '2022-04-20 14:38:56', 1),
(48, 'IP SE 2022 128 BLACK', 2, 0, 0, 0, 0, 0, '2022-04-20 14:38:57', '2022-04-20 14:38:57', 1),
(49, 'IP SE 2022 64 BLACK', 2, 510, 1, 0, 0, 0, '2022-04-20 14:39:07', '2022-04-20 14:41:32', 1),
(50, 'IP 13 PRO MAX 128 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:39:16', '2022-04-20 14:39:16', 1),
(51, 'IP SE 128 2022 WHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:39:34', '2022-04-20 14:39:34', 1),
(52, 'IP 13 PRO MAX 256 GRAPHITE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:39:35', '2022-04-20 14:39:35', 1),
(53, 'IP SE 2022 64 RED', 2, 510, 1, 0, 0, 0, '2022-04-20 14:39:41', '2022-04-20 14:40:59', 1),
(54, 'IP 13 PRO MAX 256 SILVER', 2, 1353, 1, 0, 0, 0, '2022-04-20 14:39:58', '2022-04-20 14:51:45', 1),
(55, 'IP 13 PRO MAX 256 GOLD', 2, 0, 0, 0, 0, 0, '2022-04-20 14:40:15', '2022-04-20 14:40:15', 1),
(56, 'IP SE 2022 128 RED', 2, 0, 0, 0, 0, 0, '2022-04-20 14:40:18', '2022-04-20 14:40:18', 1),
(57, 'IP SE 2022 64 WHITE', 2, 510, 3, 0, 0, 0, '2022-04-20 14:40:26', '2022-04-20 14:40:26', 1),
(58, 'IP 13 PRO MAX 256 BLUE', 2, 0, 0, 0, 0, 0, '2022-04-20 14:40:38', '2022-04-20 14:40:38', 1),
(59, 'IP 13 PRO MAX 256 GREEN', 2, 0, 0, 0, 0, 0, '2022-04-20 14:41:00', '2022-04-20 14:41:00', 1),
(60, 'IP 13 PRO MAX 512 GRAPHITE', 2, 1550, 1, 0, 0, 0, '2022-04-20 14:52:42', '2022-04-20 14:52:42', 1),
(61, 'IP 13 PRO MAX 512 GOLD', 2, 1550, 1, 0, 0, 0, '2022-04-20 14:53:05', '2022-04-20 14:53:05', 1),
(62, 'IP 13 PRO MAX 512 BLUE', 2, 1550, 2, 0, 0, 0, '2022-04-20 14:53:31', '2022-04-20 14:53:31', 1),
(63, 'IP 13 PRO MAX 512 SILVER', 2, 1550, 2, 0, 0, 0, '2022-04-20 14:55:31', '2022-04-20 14:55:31', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `created_at`, `updated_at`, `activo`) VALUES
(1, 'CHULOS', '2022-04-11 18:24:19', '2022-04-20 13:52:44', 1),
(2, 'SOUTH DEALS', '2022-04-20 13:52:54', '2022-04-20 13:52:54', 1),
(3, 'GABI', '2022-04-20 13:53:20', '2022-04-20 13:53:20', 1),
(4, 'TATO', '2022-04-20 13:53:26', '2022-04-20 13:53:26', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Acceso a todos los items del menu', '2022-04-11 18:24:19', '2022-04-11 18:24:19'),
(2, 'Vendedor', 'Acceso a todo menos \'Roles y permisos\', \'Caja\', \'Compras\' y \'Movimientos\'', '2022-04-11 18:24:19', '2022-04-11 18:24:19'),
(3, 'Otro', 'Customizar', '2022-04-11 18:24:19', '2022-04-11 18:24:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedores`
--

CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comision` double NOT NULL DEFAULT '0',
  `rol_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vendedores`
--

INSERT INTO `vendedores` (`id`, `nombre`, `email`, `usuario`, `password`, `telefono`, `comision`, `rol_id`, `created_at`, `updated_at`, `activo`) VALUES
(1, 'harry', 'dielectronics@gmail.com', 'harry', 'rivercapo', '44339966', 0, 1, '2022-04-11 18:24:19', '2022-04-11 18:24:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `precio_total` double DEFAULT NULL,
  `costo` double NOT NULL DEFAULT '0',
  `utilidad` double NOT NULL DEFAULT '0',
  `tipo_venta` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_stock` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_caja` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor_comision` double DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_venta` date DEFAULT NULL,
  `activo` tinyint(4) DEFAULT NULL,
  `confirmada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas_detalle`
--

CREATE TABLE `ventas_detalle` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `precio` double DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas_detalle`
--

-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_compras_proveedores` (`proveedor_id`);

--
-- Indices de la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_compras_detalle_compras` (`compra_id`),
  ADD KEY `FK_compras_detalle_productos` (`producto_id`);

--
-- Indices de la tabla `cuentas_corrientes`
--
ALTER TABLE `cuentas_corrientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_cuentas_corrientes_clientes` (`cliente_id`),
  ADD KEY `FK_cuentas_corrientes_proveedores` (`proveedor_id`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_movimientos_vendedores` (`usuario`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `FK_permisos_roles` (`rol_id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_productos_marcas` (`marca`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `FK_vendedores_roles` (`rol_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ventas_clientes` (`cliente_id`),
  ADD KEY `FK_ventas_vendedores` (`vendedor_id`);

--
-- Indices de la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ventas_detalle_productos` (`producto_id`),
  ADD KEY `FK_ventas_detalle_ventas` (`venta_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_corrientes`
--
ALTER TABLE `cuentas_corrientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `FK_compras_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD CONSTRAINT `FK_compras_detalle_compras` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `FK_compras_detalle_productos` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `cuentas_corrientes`
--
ALTER TABLE `cuentas_corrientes`
  ADD CONSTRAINT `FK_cuentas_corrientes_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `FK_cuentas_corrientes_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `FK_movimientos_vendedores` FOREIGN KEY (`usuario`) REFERENCES `vendedores` (`id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `FK_permisos_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `FK_productos_marcas` FOREIGN KEY (`marca`) REFERENCES `marcas` (`id`);

--
-- Filtros para la tabla `vendedores`
--
ALTER TABLE `vendedores`
  ADD CONSTRAINT `FK_vendedores_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `FK_ventas_clientes` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `FK_ventas_vendedores` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`);

--
-- Filtros para la tabla `ventas_detalle`
--
ALTER TABLE `ventas_detalle`
  ADD CONSTRAINT `FK_ventas_detalle_productos` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `FK_ventas_detalle_ventas` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
