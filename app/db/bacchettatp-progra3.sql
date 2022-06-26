-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-06-2022 a las 07:30:04
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bacchettatp-progra3`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `clave` int(11) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `nombre`, `clave`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Jorge', 1234, '2022-06-13 03:02:08.000000', '2022-06-13 03:02:08.000000', NULL),
(26, 'Pedro', 1234, '2022-06-13 04:19:33.000000', '2022-06-13 04:19:33.000000', NULL),
(27, 'Johnny', 1234, '2022-06-15 02:47:45.000000', '2022-06-15 03:07:47.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `clave` int(11) NOT NULL,
  `puesto` varchar(50) NOT NULL,
  `puntaje` float NOT NULL,
  `estado` varchar(50) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6),
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `clave`, `puesto`, `puntaje`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(38, 'Tomi_Cocinero', 1234, 'Cocinero', 6.5, 'Activo', '2022-06-26 04:23:18.464808', '2022-06-26 06:23:18.000000', NULL),
(39, 'Tomi_Mozo', 1234, 'Mozo', 5, 'Activo', '2022-06-26 03:55:18.797360', '2022-06-26 05:55:18.000000', NULL),
(40, 'Tomi_Cervecero', 1234, 'Cervecero', 0, 'Activo', '2022-06-22 04:26:04.000000', '2022-06-22 04:26:04.000000', NULL),
(41, 'Tomi_Bartender', 1234, 'Bartender', 8, 'Activo', '2022-06-22 02:01:47.417071', '2022-06-22 07:01:47.000000', NULL),
(42, 'Tomi_Bartender2', 1234, 'Bartender', 6.5, 'Activo', '2022-06-23 01:12:34.146062', '2022-06-23 06:12:34.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `calificacion_mesa` int(11) NOT NULL,
  `calificacion_restaurante` int(11) NOT NULL,
  `calificacion_mozo` int(11) NOT NULL,
  `calificacion_cocinero` int(11) NOT NULL,
  `calificacion_cervecero` int(11) NOT NULL,
  `calificacion_bartender` int(11) NOT NULL,
  `comentario` varchar(50) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `pedido_id`, `calificacion_mesa`, `calificacion_restaurante`, `calificacion_mozo`, `calificacion_cocinero`, `calificacion_cervecero`, `calificacion_bartender`, `comentario`, `created_at`, `updated_at`, `deleted_at`) VALUES
(45, 60, 7, 6, 9, 10, 8, 6, 'Me gusto la atencion. Me gusto la comida. Muy lind', '2022-06-25 02:16:34.000000', '2022-06-25 02:16:34.000000', NULL),
(50, 66, 7, 4, 5, 3, 8, 6, 'El pedido tardo mucho', '2022-06-26 05:55:18.000000', '2022-06-26 05:55:18.000000', NULL),
(51, 75, 8, 9, 5, 10, 8, 6, 'Muy rica la mila', '2022-06-26 06:23:18.000000', '2022-06-26 06:23:18.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `puntaje` float NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`, `puntaje`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10003, 'Cerrada', 0, '2022-06-22 05:05:07.000000', '2022-06-26 05:56:27.000000', NULL),
(10004, 'Cerrada', 0, '2022-06-22 05:05:15.000000', '2022-06-26 06:16:18.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordens`
--

CREATE TABLE `ordens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `empleado_id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` float NOT NULL,
  `tiempo_estimado` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ordens`
--

INSERT INTO `ordens` (`id`, `pedido_id`, `producto_id`, `empleado_id`, `descripcion`, `cantidad`, `subtotal`, `tiempo_estimado`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(95, 66, 13, 38, 'Torta', 1, 200, '00:07:00', 'Listo para servir', '2022-06-25 08:19:23.000000', '2022-06-24 05:18:40.000000', NULL),
(96, 75, 18, 38, 'Milanesa Napolitana', 3, 1800, '00:06:00', 'Listo para servir', '2022-06-26 06:07:44.000000', '2022-06-26 06:13:43.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `mesa_id` int(11) NOT NULL,
  `mozo_id` int(11) NOT NULL,
  `total` float NOT NULL,
  `tiempo_estimado` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `foto_mesa` varchar(50) NOT NULL,
  `con_retraso` varchar(2) NOT NULL DEFAULT 'NO',
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `mesa_id`, `mozo_id`, `total`, `tiempo_estimado`, `estado`, `foto_mesa`, `con_retraso`, `created_at`, `updated_at`, `deleted_at`) VALUES
(66, 10003, 39, 200, '00:07:00', 'Pagado', 'FotosMesas/66@10003-mesa.jpg', 'SI', '2022-06-25 03:00:00.000000', '2022-06-26 05:56:27.000000', NULL),
(75, 10004, 39, 1800, '00:06:00', 'Pagado', 'FotosMesas/75@10004-mesa.jpg', 'NO', '2022-06-19 06:04:19.000000', '2022-06-19 06:16:18.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `precio` float NOT NULL,
  `stock` int(11) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `tiempo_estimado` varchar(50) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `descripcion`, `precio`, `stock`, `sector`, `tiempo_estimado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 'Cerveza', 200, 85, 'Barra_Choperas', '00:05:00', '2022-06-13 05:14:22.000000', '2022-06-15 03:38:48.000000', NULL),
(12, 'Coca-Cola 600ml', 200, 84, 'Cocina', '00:05:00', '2022-06-13 07:58:55.000000', '2022-06-25 00:36:58.000000', NULL),
(13, 'Torta', 200, 73, 'Candy_Bar', '00:05:00', '2022-06-13 08:01:32.000000', '2022-06-25 08:19:23.000000', NULL),
(17, 'Daikiri', 200, 74, 'Barra_Tragos', '00:05:00', '2022-06-14 00:09:30.000000', '2022-06-25 00:18:08.000000', NULL),
(18, 'Milanesa Napolitana', 600, 37, 'Cocina', '00:05:00', '2022-06-26 06:05:22.000000', '2022-06-26 06:07:44.000000', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE `registros` (
  `id` int(11) NOT NULL,
  `empleado` varchar(50) NOT NULL,
  `puesto` varchar(50) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `registros`
--

INSERT INTO `registros` (`id`, `empleado`, `puesto`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-24 01:18:08.000000', '2022-06-24 01:18:08.000000', NULL),
(2, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-24 01:33:11.000000', '2022-06-24 01:33:11.000000', NULL),
(3, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', NULL),
(4, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', NULL),
(5, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 01:46:42.000000', '2022-06-24 01:46:42.000000', NULL),
(6, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 02:05:24.000000', '2022-06-24 02:05:24.000000', NULL),
(7, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-23 21:09:28.000000', '2022-06-23 21:09:28.000000', NULL),
(8, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-23 21:09:59.000000', '2022-06-23 21:09:59.000000', NULL),
(9, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-23 21:10:34.000000', '2022-06-23 21:10:34.000000', NULL),
(10, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 02:11:38.000000', '2022-06-24 02:11:38.000000', NULL),
(11, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-23 21:12:58.000000', '2022-06-23 21:12:58.000000', NULL),
(12, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 02:13:23.000000', '2022-06-24 02:13:23.000000', NULL),
(13, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 02:21:02.000000', '2022-06-24 02:21:02.000000', NULL),
(14, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 03:21:33.000000', '2022-06-24 03:21:33.000000', NULL),
(15, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-23 21:22:48.000000', '2022-06-23 21:22:48.000000', NULL),
(16, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 22:15:49.000000', '2022-06-24 22:15:49.000000', NULL),
(17, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-24 22:16:06.000000', '2022-06-24 22:16:06.000000', NULL),
(18, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 03:16:27.000000', '2022-06-25 03:16:27.000000', NULL),
(19, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 00:17:38.000000', '2022-06-25 00:17:38.000000', NULL),
(20, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-25 00:32:29.000000', '2022-06-25 00:32:29.000000', NULL),
(21, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 00:34:57.000000', '2022-06-25 00:34:57.000000', NULL),
(22, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 00:36:58.000000', '2022-06-25 00:36:58.000000', NULL),
(23, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 00:37:16.000000', '2022-06-25 00:37:16.000000', NULL),
(24, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 00:38:00.000000', '2022-06-25 00:38:00.000000', NULL),
(25, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 00:38:07.000000', '2022-06-25 00:38:07.000000', NULL),
(26, 'Tomi_Cocinero', 'Cocinero', 'Login', '2022-06-25 00:48:21.000000', '2022-06-25 00:48:21.000000', NULL),
(27, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 00:49:52.000000', '2022-06-25 00:49:52.000000', NULL),
(28, 'Tomi_Cocinero', 'Cocinero', 'Empezo a preparar la orden n°91, que es la ultima del pedido n°60', '2022-06-25 00:52:53.000000', '2022-06-25 00:52:53.000000', NULL),
(29, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°91del pedido n°60', '2022-06-25 00:53:57.000000', '2022-06-25 00:53:57.000000', NULL),
(30, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°91, que es la ultima que faltaba del pedido n°60', '2022-06-25 00:53:57.000000', '2022-06-25 00:53:57.000000', NULL),
(31, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-25 01:00:15.000000', '2022-06-25 01:00:15.000000', NULL),
(32, 'Tomi_Mozo', 'Mozo', 'Sirvio el pedido', '2022-06-25 01:00:28.000000', '2022-06-25 01:00:28.000000', NULL),
(33, 'Tomi_Mozo', 'Mozo', 'Cobro el pedido', '2022-06-25 01:00:51.000000', '2022-06-25 01:00:51.000000', NULL),
(34, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 01:52:18.000000', '2022-06-25 01:52:18.000000', NULL),
(35, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 01:52:42.000000', '2022-06-25 01:52:42.000000', NULL),
(36, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 01:53:10.000000', '2022-06-25 01:53:10.000000', NULL),
(37, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 06:46:32.000000', '2022-06-25 06:46:32.000000', NULL),
(38, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 06:59:49.000000', '2022-06-25 06:59:49.000000', NULL),
(39, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 07:10:15.000000', '2022-06-25 07:10:15.000000', NULL),
(40, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 05:12:16.000000', '2022-06-25 05:12:16.000000', NULL),
(41, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 07:13:07.000000', '2022-06-25 07:13:07.000000', NULL),
(42, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 07:13:50.000000', '2022-06-25 07:13:50.000000', NULL),
(43, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 07:14:39.000000', '2022-06-25 07:14:39.000000', NULL),
(44, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 07:20:57.000000', '2022-06-25 07:20:57.000000', NULL),
(45, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-25 08:07:25.000000', '2022-06-25 08:07:25.000000', NULL),
(46, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-25 08:19:23.000000', '2022-06-25 08:19:23.000000', NULL),
(47, 'Tomi_Cocinero', 'Cocinero', 'Login', '2022-06-26 04:57:26.000000', '2022-06-26 04:57:26.000000', NULL),
(48, 'Tomi_Cocinero', 'Cocinero', 'Empezo a preparar la orden n°95, que es la ultima del pedido n°66', '2022-06-26 04:59:28.000000', '2022-06-26 04:59:28.000000', NULL),
(49, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:18:40.000000', '2022-06-26 05:18:40.000000', NULL),
(50, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:18:40.000000', '2022-06-26 05:18:40.000000', NULL),
(51, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:19:21.000000', '2022-06-26 05:19:21.000000', NULL),
(52, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:19:21.000000', '2022-06-26 05:19:21.000000', NULL),
(53, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:20:43.000000', '2022-06-26 05:20:43.000000', NULL),
(54, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:20:43.000000', '2022-06-26 05:20:43.000000', NULL),
(55, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:22:00.000000', '2022-06-26 05:22:00.000000', NULL),
(56, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:22:00.000000', '2022-06-26 05:22:00.000000', NULL),
(57, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:24:10.000000', '2022-06-26 05:24:10.000000', NULL),
(58, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:24:10.000000', '2022-06-26 05:24:10.000000', NULL),
(59, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:24:39.000000', '2022-06-26 05:24:39.000000', NULL),
(60, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:24:39.000000', '2022-06-26 05:24:39.000000', NULL),
(61, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95del pedido n°66', '2022-06-26 05:25:01.000000', '2022-06-26 05:25:01.000000', NULL),
(62, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°95, que es la ultima que faltaba del pedido n°66', '2022-06-26 05:25:01.000000', '2022-06-26 05:25:01.000000', NULL),
(63, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-26 05:29:48.000000', '2022-06-26 05:29:48.000000', NULL),
(64, 'Tomi_Mozo', 'Mozo', 'Sirvio el pedido', '2022-06-26 05:29:57.000000', '2022-06-26 05:29:57.000000', NULL),
(65, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-26 05:32:42.000000', '2022-06-26 05:32:42.000000', NULL),
(66, 'Tomi_Mozo', 'Mozo', 'Cobro el pedido', '2022-06-26 05:32:59.000000', '2022-06-26 05:32:59.000000', NULL),
(67, 'Tomi_Mozo', 'Mozo', 'Cobro el pedido', '2022-06-26 05:48:20.000000', '2022-06-26 05:48:20.000000', NULL),
(68, 'Tomi_Mozo', 'Mozo', 'Creo pedido', '2022-06-26 06:04:19.000000', '2022-06-26 06:04:19.000000', NULL),
(69, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-26 06:07:34.000000', '2022-06-26 06:07:34.000000', NULL),
(70, 'Tomi_Mozo', 'Mozo', 'Agrego orden a un pedido', '2022-06-26 06:07:44.000000', '2022-06-26 06:07:44.000000', NULL),
(71, 'Tomi_Bartender', 'Bartender', 'Login', '2022-06-26 06:08:47.000000', '2022-06-26 06:08:47.000000', NULL),
(72, 'Tomi_Cocinero', 'Cocinero', 'Login', '2022-06-26 06:09:15.000000', '2022-06-26 06:09:15.000000', NULL),
(73, 'Tomi_Cocinero', 'Cocinero', 'Empezo a preparar la orden n°96, que es la ultima del pedido n°75', '2022-06-26 06:13:28.000000', '2022-06-26 06:13:28.000000', NULL),
(74, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°96del pedido n°75', '2022-06-26 06:13:43.000000', '2022-06-26 06:13:43.000000', NULL),
(75, 'Tomi_Cocinero', 'Cocinero', 'Termino la orden n°96, que es la ultima que faltaba del pedido n°75', '2022-06-26 06:13:43.000000', '2022-06-26 06:13:43.000000', NULL),
(76, 'Tomi_Mozo', 'Mozo', 'Login', '2022-06-26 06:14:43.000000', '2022-06-26 06:14:43.000000', NULL),
(77, 'Tomi_Mozo', 'Mozo', 'Sirvio el pedido', '2022-06-26 06:14:53.000000', '2022-06-26 06:14:53.000000', NULL),
(78, 'Tomi_Mozo', 'Mozo', 'Cobro el pedido', '2022-06-26 06:15:44.000000', '2022-06-26 06:15:44.000000', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ordens`
--
ALTER TABLE `ordens`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `registros`
--
ALTER TABLE `registros`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10005;

--
-- AUTO_INCREMENT de la tabla `ordens`
--
ALTER TABLE `ordens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `registros`
--
ALTER TABLE `registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
