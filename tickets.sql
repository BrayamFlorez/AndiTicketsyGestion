-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-08-2026 a las 22:09:33
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
-- Base de datos: `tickets`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('anditickets-cache-admin@admin.com|127.0.0.1', 'i:1;', 1784838958),
('anditickets-cache-admin@admin.com|127.0.0.1:timer', 'i:1784838958;', 1784838958),
('anditickets-cache-sidebar.flujos.pendientes.user.1', 'i:0;', 1788206864),
('anditickets-cache-sidebar.flujos.pendientes.user.2', 'i:4;', 1788206825),
('anditickets-cache-sidebar.flujos.pendientes.user.4', 'i:4;', 1785536261),
('anditickets-cache-sidebar.flujos.pendientes.user.6', 'i:1;', 1786401717),
('anditickets-cache-sidebar.flujos.pendientes.user.7', 'i:0;', 1788205363),
('anditickets-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:40:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:17:\"tickets.ver-todos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;i:4;i:7;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:21:\"tickets.ver-asignados\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:19:\"tickets.ver-propios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:9:{i:0;i:1;i:1;i:2;i:2;i:8;i:3;i:9;i:4;i:10;i:5;i:11;i:6;i:12;i:7;i:13;i:8;i:14;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:13:\"tickets.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:13:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"tickets.asignar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"tickets.iniciar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"tickets.resolver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:14:\"tickets.cerrar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:16:\"tickets.cancelar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"tickets.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"usuarios.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:6;i:4;i:7;i:5;i:11;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"usuarios.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"usuarios.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"usuarios.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:24:\"usuarios.asignar-empresa\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:18:\"empresas.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:15:\"sedes.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"equipos.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:11:\"equipos.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:9;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:13:\"auditoria.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:15:\"roles.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:15:\" tickets.editar\";s:1:\"c\";s:3:\"web\";}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:13:\"equipos.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:16:\"equipos.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:17:\"mpreventivo.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:18:\"mpreventivo.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:15:\"mpreventivo.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:20:\"mpreventivo.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:27:\"flujos.plantillas.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:32:\"flujos.tipos-documento.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:14:\"flujos.iniciar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:13:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:16:\"flujos.ver-todos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:5;i:3;i:7;i:4;i:8;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:23:\"flujos.ver-participando\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:11:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;i:4;i:8;i:5;i:9;i:6;i:10;i:7;i:11;i:8;i:12;i:9;i:13;i:10;i:14;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:26:\"flujos.cancelar-cualquiera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:22:\"flujos.corregir-propio\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:13:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"flujos.reabrir\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:22:\"flujos.desistir-propio\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:13:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;i:5;i:7;i:6;i:8;i:7;i:9;i:8;i:10;i:9;i:11;i:10;i:12;i:11;i:13;i:12;i:14;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:17:\"equipos.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:2;i:1;i:5;i:2;i:6;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:14:\"tickets.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:39;a:3:{s:1:\"a\";i:40;s:1:\"b\";s:22:\"flujos.editar-cabecera\";s:1:\"c\";s:3:\"web\";}}s:5:\"roles\";a:13:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:7:\"gerente\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:13:\"lider-tecnico\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"lider-ventas\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"tecnico\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"asesor-ventas\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"auxiliar-bodega\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:10;s:1:\"b\";s:23:\"auxiliar-administrativo\";s:1:\"c\";s:3:\"web\";}i:9;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"talento-humano\";s:1:\"c\";s:3:\"web\";}i:10;a:3:{s:1:\"a\";i:12;s:1:\"b\";s:16:\"auxiliar-cartera\";s:1:\"c\";s:3:\"web\";}i:11;a:3:{s:1:\"a\";i:13;s:1:\"b\";s:21:\"auxiliar-contabilidad\";s:1:\"c\";s:3:\"web\";}i:12;a:3:{s:1:\"a\";i:14;s:1:\"b\";s:9:\"recepcion\";s:1:\"c\";s:3:\"web\";}}}', 1788290492),
('anditickets-cache-tecnico@andi.co|127.0.0.1', 'i:1;', 1786401687),
('anditickets-cache-tecnico@andi.co|127.0.0.1:timer', 'i:1786401687;', 1786401687),
('anditickets-cache-tecnico@andi.com|127.0.0.1', 'i:3;', 1786401671),
('anditickets-cache-tecnico@andi.com|127.0.0.1:timer', 'i:1786401671;', 1786401671),
('anditickets-cache-usuario@andi.com|127.0.0.1', 'i:1;', 1788187083),
('anditickets-cache-usuario@andi.com|127.0.0.1:timer', 'i:1788187083;', 1788187083);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_types`
--

CREATE TABLE `document_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(255) NOT NULL DEFAULT 'fa-file-alt',
  `workflow_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `document_types`
--

INSERT INTO `document_types` (`id`, `nombre`, `slug`, `descripcion`, `icono`, `workflow_template_id`, `activo`, `created_at`, `updated_at`) VALUES
(5, 'Cotización', 'cotizacion', 'para que las asesoras pasen al cliente las cotizaciones', 'fa-ticket', 6, 1, '2026-07-15 02:31:45', '2026-07-15 02:51:27'),
(6, 'prueba de indice', 'prueba-indice', 'prueba de indice', 'fa-file-alt', 7, 1, '2026-08-01 01:54:30', '2026-08-01 01:54:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `nit` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nombre`, `nit`, `telefono`, `email`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Oficanon', '8090516089', '3202387718', 'oficanon@gmail.com', 1, NULL, NULL),
(2, 'SERGIO ANDRES CAICEDO PEREZ', '22141313', '1231231', 'hola@configuroweb.com', 1, '2026-06-24 21:44:55', '2026-06-24 21:58:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_user`
--

CREATE TABLE `empresa_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `empresa_id` bigint(20) UNSIGNED NOT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asignado_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresa_user`
--

INSERT INTO `empresa_user` (`id`, `user_id`, `empresa_id`, `sede_id`, `asignado_at`, `created_at`, `updated_at`) VALUES
(6, 6, 1, 2, '2026-08-06 03:18:50', '2026-08-06 03:18:50', '2026-08-06 03:18:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `empresa_id` bigint(20) UNSIGNED NOT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `responsable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `creado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `categoria` varchar(30) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `estado` varchar(30) NOT NULL DEFAULT 'activo',
  `especificaciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`especificaciones`)),
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `empresa_id`, `sede_id`, `responsable_id`, `creado_por`, `categoria`, `nombre`, `marca`, `modelo`, `numero_serie`, `ubicacion`, `estado`, `especificaciones`, `notas`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 4, 2, 'computadora', 'hp omen', 'hp', 'dlx360', 'afasdasd2343addad', NULL, 'activo', '{\"procesador\":\"core i7\",\"ram_gb\":\"8\",\"almacenamiento_gb\":\"250\",\"tipo_disco\":\"HDD\",\"sistema_operativo\":\"windows 11\",\"tarjeta_grafica\":\"no\"}', NULL, '2026-06-26 21:14:58', '2026-06-27 03:21:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimientos_preventivos`
--

CREATE TABLE `mantenimientos_preventivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipo_id` bigint(20) UNSIGNED NOT NULL,
  `tecnico_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_programada` date DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_finalizacion` datetime DEFAULT NULL,
  `estado` enum('programado','en_progreso','completado','cancelado') NOT NULL DEFAULT 'programado',
  `hw_limpieza_interna` tinyint(1) NOT NULL DEFAULT 0,
  `hw_limpieza_externa` tinyint(1) NOT NULL DEFAULT 0,
  `hw_cambio_pasta_termica` tinyint(1) NOT NULL DEFAULT 0,
  `hw_revision_ventiladores` tinyint(1) NOT NULL DEFAULT 0,
  `hw_verificacion_cables` tinyint(1) NOT NULL DEFAULT 0,
  `sw_limpieza_temporales` tinyint(1) NOT NULL DEFAULT 0,
  `sw_actualizacion_so` tinyint(1) NOT NULL DEFAULT 0,
  `sw_revision_antivirus` tinyint(1) NOT NULL DEFAULT 0,
  `sw_revision_disco_duro` tinyint(1) NOT NULL DEFAULT 0,
  `sw_desinstalacion_basura` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones_tecnicas` text DEFAULT NULL,
  `recomendaciones_cliente` text DEFAULT NULL,
  `firma_cliente_path` varchar(255) DEFAULT NULL,
  `evidencia_fotografica_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mantenimientos_preventivos`
--

INSERT INTO `mantenimientos_preventivos` (`id`, `equipo_id`, `tecnico_id`, `fecha_programada`, `fecha_inicio`, `fecha_finalizacion`, `estado`, `hw_limpieza_interna`, `hw_limpieza_externa`, `hw_cambio_pasta_termica`, `hw_revision_ventiladores`, `hw_verificacion_cables`, `sw_limpieza_temporales`, `sw_actualizacion_so`, `sw_revision_antivirus`, `sw_revision_disco_duro`, `sw_desinstalacion_basura`, `observaciones_tecnicas`, `recomendaciones_cliente`, `firma_cliente_path`, `evidencia_fotografica_path`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-06-18', '2026-06-11 15:15:00', NULL, 'en_progreso', 1, 0, 0, 0, 0, 1, 0, 0, 1, 0, 'fdsdsdasdsa', 'dasdasdasdas', NULL, NULL, '2026-06-27 01:16:16', '2026-06-27 01:16:16'),
(3, 1, 2, NULL, '2026-06-13 19:09:00', '2026-06-26 22:09:00', 'completado', 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 'sadasdADadsad', 'ADSasdDADadaS', NULL, NULL, '2026-06-27 03:09:40', '2026-06-27 03:09:40'),
(4, 1, 2, NULL, '2026-06-13 19:09:00', '2026-06-26 22:09:00', 'completado', 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 'sadasdADadsad', 'ADSasdDADadaS', NULL, NULL, '2026-06-27 03:16:56', '2026-06-27 03:16:56'),
(5, 1, 2, NULL, '2026-06-13 19:09:00', '2026-06-26 22:09:00', 'completado', 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 'sadasdADadsad', 'ADSasdDADadaS', NULL, NULL, '2026-06-27 03:17:27', '2026-06-27 03:17:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_01_000003_create_empresas_table', 2),
(5, '2025_01_01_000004_create_sedes_table', 2),
(6, '2025_01_01_000005_create_tickets_table', 2),
(7, '2025_02_02_000002_add_fields_to_users_table', 3),
(8, '2026_03_06_161316_create_permission_tables', 3),
(9, '2026_06_24_000002_create_equipos_table', 4),
(11, '2026_06_24_000001_create_empresa_user_table', 5),
(12, '2026_06_26_165209_create_mantenimiento_preventivos_table', 6),
(13, '2026_07_10_100001_create_workflow_templates_table', 7),
(14, '2026_07_10_100002_create_document_types_table', 8),
(15, '2026_07_10_100003_create_workflow_stages_table', 9),
(16, '2026_07_10_100004_create_workflow_stage_actors_table', 10),
(17, '2026_07_10_100005_create_workflow_instances_table', 11),
(18, '2026_07_10_100006_create_workflow_instance_steps_table', 12),
(19, '2026_07_10_100007_create_workflow_step_assignments_table', 13),
(20, '2026_07_10_100008_create_workflow_attachments_table', 14),
(21, '2026_07_13_230029_create_notifications_table', 15),
(22, '2026_08_05_152638_add_deleted_at_to_users_table', 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 6),
(8, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0284c6fb-41f1-4e2f-a22f-e3849205d551', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tu flujo FLJ-2026-000005 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-14 08:56:36', '2026-07-23 21:18:14'),
('079a5571-4b5b-4fb7-9d3c-207f8fcd3edc', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":15,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:41:55', '2026-07-15 02:41:55'),
('08d9f8b7-100f-4b1d-b86a-ef962c675343', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tu flujo FLJ-2026-000007 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-14 09:07:41', '2026-07-23 21:18:14'),
('0db88086-f800-4f0e-b9f2-efb628681142', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":14,\"etapa\":\"Crear solicitud de compras\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-15 02:40:01', '2026-07-23 21:18:14'),
('120b9879-0779-4f0e-bf81-2a12ef16cd86', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:50:26', '2026-07-14 08:50:26'),
('139a9dbb-2e69-44f1-a5b6-2af618e8883f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:50:26', '2026-07-14 08:50:26'),
('1513ff7c-4f08-4dac-8944-a02888c14a10', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":13,\"codigo\":\"FLJ-2026-000013\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/13\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-08-01 03:06:34', '2026-08-01 03:06:34'),
('194986ef-9d77-46cb-b45a-b66707259a02', 'App\\Notifications\\Tickets\\TicketIniciadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"ticket_iniciado\",\"mensaje\":\"Jhonatan Suarez comenz\\u00f3 a trabajar en \\\"prueba de notificaciones\\\"\",\"ticket_id\":10,\"codigo\":\"TKT-10\",\"titulo\":\"prueba de notificaciones\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/tickets\\/10\",\"icono\":\"fa-sync-alt\",\"color\":\"accent\",\"actor\":{\"name\":\"Jhonatan Suarez\",\"iniciales\":\"JS\",\"rol\":\"T\\u00e9cnico\"},\"tipo_documento\":\"Otro\",\"tipo_icono\":\"fa-ticket-alt\",\"etapa\":\"En progreso\"}', '2026-08-31 20:07:43', '2026-08-06 01:09:46', '2026-08-31 20:07:43'),
('1e30dcf0-ca4c-43cb-b301-817ca0cd4119', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba indice\\\"\",\"instance_id\":12,\"codigo\":\"FLJ-2026-000012\",\"titulo\":\"prueba indice\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/12\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', '2026-08-06 01:22:58', '2026-08-01 01:54:42', '2026-08-06 01:22:58'),
('238fd2cc-4739-4493-82d8-2497a2d2b0f5', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":16,\"etapa\":\"Crear solicitud de compras\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-15 02:44:54', '2026-07-15 02:44:18', '2026-07-15 02:44:54'),
('2aafa13c-ee89-4801-9018-313aa52d47d5', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"flujo_aprobado\",\"mensaje\":\"Administrador firm\\u00f3 \\\"prueba edicion\\\" \\u2014 tr\\u00e1mite aprobado\",\"instance_id\":16,\"codigo\":\"FLJ-2026-000016\",\"titulo\":\"prueba edicion\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/16\",\"icono\":\"fa-check-circle\",\"color\":\"success\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Firma final\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"firmantes\":[\"Administrador\"]}', '2026-08-31 20:07:43', '2026-08-31 19:39:44', '2026-08-31 20:07:43'),
('3d114d15-1740-4d5e-b5bb-68b73d963fa4', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"flujo_aprobado\",\"mensaje\":\"Administrador firm\\u00f3 \\\"123123123d\\\" \\u2014 tr\\u00e1mite aprobado\",\"instance_id\":15,\"codigo\":\"FLJ-2026-000015\",\"titulo\":\"123123123d\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/15\",\"icono\":\"fa-check-circle\",\"color\":\"success\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Firma final\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"firmantes\":[\"Administrador\"]}', '2026-09-01 01:02:16', '2026-09-01 00:45:54', '2026-09-01 01:02:16'),
('40325e68-b70f-47a1-89cd-1d6be6b279f4', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":6,\"codigo\":\"FLJ-2026-000006\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":12,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/6\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000006\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-14 09:06:44', '2026-07-23 21:18:14'),
('41adb669-8f34-4d29-8da3-5e9901f49211', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":17,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:45:14', '2026-07-15 02:45:14'),
('41f9138e-d793-41dd-8965-f290a51fcf0a', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 2, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tu flujo FLJ-2026-000009 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-08-06 01:22:58', '2026-07-15 02:52:37', '2026-08-06 01:22:58'),
('4ce3a689-9c16-4623-aa00-83bc3295e230', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":7,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:29', '2026-07-14 08:48:27', '2026-07-14 09:00:29'),
('52c17592-922f-4a3e-a853-4598ebfa8bde', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"assignment_id\":19,\"etapa\":\"aprobacion doble gerenia\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000009\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-15 02:52:21', '2026-07-15 02:51:45', '2026-07-15 02:52:21'),
('534edba6-dcdd-4ff7-8d63-657bc56ce342', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"assignment_id\":19,\"etapa\":\"aprobacion doble gerenia\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000009\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:01:14', '2026-07-15 02:51:45', '2026-07-23 21:01:14'),
('546d2793-82bb-4ebd-b5b4-7de78984370c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:34:33', '2026-07-14 08:34:33'),
('54b9c843-3f5c-46b5-94f9-f053fd61f26f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":9,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:53:21', '2026-07-14 08:52:36', '2026-07-14 08:53:21'),
('564d4577-836f-431c-b023-ee2634aba85d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"prueba edicion\\\"\",\"instance_id\":16,\"codigo\":\"FLJ-2026-000016\",\"titulo\":\"prueba edicion\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/16\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', NULL, '2026-08-31 19:38:55', '2026-08-31 19:38:55'),
('59c471ab-30e5-428f-b72d-ec3913a6826a', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":13,\"codigo\":\"FLJ-2026-000013\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/13\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-08-01 03:06:34', '2026-08-06 01:22:58'),
('5aa963f0-2aaf-4a9e-82c7-a85b914e204e', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"fuerza\\\"\",\"instance_id\":17,\"codigo\":\"FLJ-2026-000017\",\"titulo\":\"fuerza\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/17\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-09-01 01:05:00', '2026-09-01 01:05:00'),
('5cd14ce9-efa1-42df-8943-f3191aeb20f1', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"flujo_aprobado\",\"mensaje\":\"Administrador firm\\u00f3 \\\"prueba edicion\\\" \\u2014 tr\\u00e1mite aprobado\",\"instance_id\":16,\"codigo\":\"FLJ-2026-000016\",\"titulo\":\"prueba edicion\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/16\",\"icono\":\"fa-check-circle\",\"color\":\"success\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Firma final\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"firmantes\":[\"Administrador\"]}', '2026-08-31 20:07:43', '2026-08-31 19:39:44', '2026-08-31 20:07:43'),
('60d24007-4f77-4d73-9af2-b08ae7ae3110', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:34:33', '2026-07-14 08:34:33'),
('6e782a98-3305-4eb8-b360-6423e4e799b1', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":7,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:32', '2026-07-14 08:48:27', '2026-07-14 09:00:32'),
('706b2210-7826-4e17-b91e-2c7d20c37dc3', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":10,\"codigo\":\"FLJ-2026-000010\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/10\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-07-23 21:51:51', '2026-07-23 21:18:35', '2026-07-23 21:51:51'),
('72ba062c-7ca8-4e15-976d-88344c93e87d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:50:59', '2026-07-14 08:50:26', '2026-07-14 08:50:59'),
('74f290c7-73d3-4ab4-9faf-40feae50e205', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba\\\"\",\"instance_id\":11,\"codigo\":\"FLJ-2026-000011\",\"titulo\":\"prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/11\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-08-01 01:51:04', '2026-08-01 01:51:04'),
('777740f9-6f68-4a59-bc6f-2088733ce7ea', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba\\\"\",\"instance_id\":11,\"codigo\":\"FLJ-2026-000011\",\"titulo\":\"prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/11\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-08-01 01:51:04', '2026-08-06 01:22:58'),
('7b5c5401-a8cd-408e-8ad9-949867ace37f', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tu flujo FLJ-2026-000007 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-14 09:07:41', '2026-07-23 21:18:14'),
('7b8364c0-d6b5-4cfc-9898-7479834530b3', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":10,\"codigo\":\"FLJ-2026-000010\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/10\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-07-23 21:18:35', '2026-07-23 21:18:35'),
('7ccb68d2-aeb0-4609-9607-6e93bd85921d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba\\\"\",\"instance_id\":11,\"codigo\":\"FLJ-2026-000011\",\"titulo\":\"prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/11\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-08-01 01:51:04', '2026-08-01 01:51:04'),
('81493a33-0977-46ae-84fe-0c1bd5962167', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 6, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"Cotizaci\\u00f3n cliente ACME\\\"\",\"instance_id\":14,\"codigo\":\"FLJ-2026-000014\",\"titulo\":\"Cotizaci\\u00f3n cliente ACME\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/14\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":null,\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\"}', '2026-08-08 21:59:07', '2026-08-06 01:11:06', '2026-08-08 21:59:07'),
('81f830b3-cd14-435c-87ab-c20a9a967b38', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":15,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-15 02:43:26', '2026-07-15 02:41:54', '2026-07-15 02:43:26'),
('8200e714-ed35-4b45-893c-26ceacf4de94', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"assignment_id\":20,\"etapa\":\"aprobacion doble gerenia\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000009\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-08-06 01:22:58', '2026-07-15 02:51:45', '2026-08-06 01:22:58'),
('851d44d9-0221-438b-badb-f717b195b4e1', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":9,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:24', '2026-07-14 08:52:36', '2026-07-14 09:00:24'),
('8597a231-47d9-4997-824e-9512eadbc2f6', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tu flujo FLJ-2026-000008 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-15 02:46:18', '2026-07-23 21:18:14'),
('87d0d24f-8e97-4aaa-9312-4b521b0d7a69', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"fuerza\\\"\",\"instance_id\":17,\"codigo\":\"FLJ-2026-000017\",\"titulo\":\"fuerza\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/17\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-09-01 01:05:00', '2026-09-01 01:05:00'),
('88b402e4-1e8d-4ef6-b183-36b2ff52e7ca', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":15,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-15 02:43:08', '2026-07-15 02:41:55', '2026-07-15 02:43:08'),
('89b6bd3d-6dfe-45d3-b643-5dc4e7add02e', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":13,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000007\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-14 09:07:29', '2026-07-23 21:18:14'),
('8cc00950-344e-4285-a353-069668265103', 'App\\Notifications\\Tickets\\TicketAsignadoTecnicoNotification', 'App\\Models\\User', 6, '{\"tipo\":\"ticket_asignado\",\"mensaje\":\"Administrador te asign\\u00f3 \\\"prueba de notificaciones\\\"\",\"ticket_id\":10,\"codigo\":\"TKT-10\",\"titulo\":\"prueba de notificaciones\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/tickets\\/10\",\"icono\":\"fa-user-check\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Asignador\"},\"tipo_documento\":\"Otro\",\"tipo_icono\":\"fa-ticket-alt\",\"etapa\":\"Media\"}', '2026-08-06 01:09:15', '2026-08-06 01:08:04', '2026-08-06 01:09:15'),
('907f03a1-3aad-48c9-96a3-f81f10d7f0db', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:48:50', '2026-07-14 08:34:33', '2026-07-14 08:48:50'),
('9090958a-526a-4821-af78-dc8abb0e2c4f', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tu flujo FLJ-2026-000008 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-15 02:46:18', '2026-07-23 21:18:14'),
('95adfce7-42f7-40e7-aa5e-bb80cffcb742', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":13,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000007\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-14 09:07:29', '2026-07-23 21:18:14'),
('96422d64-792a-41f5-85fb-8fcb7bdda7e3', 'App\\Notifications\\Tickets\\TicketAsignadoTecnicoNotification', 'App\\Models\\User', 6, '{\"tipo\":\"ticket_asignado\",\"mensaje\":\"Administrador te asign\\u00f3 \\\"prueba de notificaciones\\\"\",\"ticket_id\":10,\"codigo\":\"TKT-10\",\"titulo\":\"prueba de notificaciones\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/tickets\\/10\",\"icono\":\"fa-user-check\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Asignador\"},\"tipo_documento\":\"Otro\",\"tipo_icono\":\"fa-ticket-alt\",\"etapa\":\"Media\"}', '2026-08-06 01:09:42', '2026-08-06 01:08:04', '2026-08-06 01:09:42'),
('965f1d7a-0b76-4618-a872-30c138fbeda9', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":14,\"etapa\":\"Crear solicitud de compras\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-15 02:40:01', '2026-07-23 21:18:14'),
('a1fbb471-12e2-45d6-b0cf-94eb7b9a8f1b', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":6,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:48:27', '2026-07-14 09:00:39'),
('a4f67f7d-a803-41ed-8d39-522d1046d67b', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":18,\"etapa\":\"Aceptacion de coizaci\\u00f3n por parte del cliente\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-15 02:45:43', '2026-07-23 21:18:14'),
('a548164d-8854-46a3-93fc-5aebc5d87fdc', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"123123123\\\"\",\"instance_id\":15,\"codigo\":\"FLJ-2026-000015\",\"titulo\":\"123123123\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/15\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', NULL, '2026-08-29 03:29:09', '2026-08-29 03:29:09'),
('a5cdb98f-b5ba-480d-8430-bbf329678f72', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":10,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:52:36', '2026-07-14 09:00:39'),
('ab3cd1c5-a546-4104-9dad-612a0ee200ba', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":10,\"codigo\":\"FLJ-2026-000010\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/10\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-07-23 21:18:35', '2026-08-06 01:22:58'),
('adcfe87c-87be-48ab-9d0b-44038cb0894c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":6,\"codigo\":\"FLJ-2026-000006\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":12,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/6\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000006\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-14 09:06:44', '2026-07-23 21:18:14'),
('b66d7214-57fd-4dc3-9cfc-b814af33af25', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 6, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"Cotizaci\\u00f3n cliente ACME\\\"\",\"instance_id\":14,\"codigo\":\"FLJ-2026-000014\",\"titulo\":\"Cotizaci\\u00f3n cliente ACME\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/14\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":null,\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\"}', '2026-08-08 21:59:16', '2026-08-06 01:11:06', '2026-08-08 21:59:16'),
('bbd1ef7a-82bd-4285-95ea-37d8e84631c4', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":15,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:41:55', '2026-07-15 02:41:55'),
('bbef2f06-f9ee-4965-8c6a-6ad1af64ffbb', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba indice\\\"\",\"instance_id\":12,\"codigo\":\"FLJ-2026-000012\",\"titulo\":\"prueba indice\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/12\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', '2026-08-06 01:22:58', '2026-08-01 01:54:42', '2026-08-06 01:22:58'),
('bdfb03f5-b397-465a-94bf-1c4a5aa97dea', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 1, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":11,\"etapa\":\"Firma Final\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:56:04', '2026-07-14 08:53:55', '2026-07-14 08:56:04'),
('c1340ae2-909c-4c61-a729-f52e89a7ae3c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":17,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:45:14', '2026-07-15 02:45:14'),
('c221f6dc-5579-4962-8d78-189220cc6d1f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:51:50', '2026-07-14 08:50:26', '2026-07-14 08:51:50'),
('c2aaf4e4-408a-45c6-af9b-070b01f9524c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"assignment_id\":20,\"etapa\":\"aprobacion doble gerenia\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000009\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-08-06 01:22:58', '2026-07-15 02:51:45', '2026-08-06 01:22:58'),
('c4a1f02c-41da-487b-8eb3-49f7adfc8f93', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":18,\"etapa\":\"Aceptacion de coizaci\\u00f3n por parte del cliente\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-15 02:45:43', '2026-07-23 21:18:14'),
('c6c3401d-11e9-48b9-bb6e-e35e8633d087', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":17,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:45:14', '2026-07-15 02:45:14'),
('ca998026-d510-4bea-9e7a-8b158c45b570', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"123123123\\\"\",\"instance_id\":15,\"codigo\":\"FLJ-2026-000015\",\"titulo\":\"123123123\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/15\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', NULL, '2026-08-29 03:29:09', '2026-08-29 03:29:09'),
('cf06a5de-2b69-4fb5-8457-ea0965cfe8bc', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 1, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":11,\"etapa\":\"Firma Final\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 20:38:10', '2026-07-14 08:53:55', '2026-07-23 20:38:10'),
('d277a959-7591-4fb9-906a-8e10437fdde3', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Administrador requiere tu aprobaci\\u00f3n: \\\"prueba\\\"\",\"instance_id\":11,\"codigo\":\"FLJ-2026-000011\",\"titulo\":\"prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/11\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-08-01 01:51:04', '2026-08-06 01:22:58'),
('d3dec1c7-3279-4991-acbd-478317b7400d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:48:54', '2026-07-14 08:34:33', '2026-07-14 08:48:54'),
('deb81f93-28c9-4e23-a54e-8afd2b9daedd', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Super Administrador requiere tu aprobaci\\u00f3n: \\\"prueba edicion\\\"\",\"instance_id\":16,\"codigo\":\"FLJ-2026-000016\",\"titulo\":\"prueba edicion\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/16\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Super Administrador\",\"iniciales\":\"SA\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"etapa\":\"primera etapa indice\"}', NULL, '2026-08-31 19:38:55', '2026-08-31 19:38:55'),
('e0c3be36-ade2-45df-b9bd-f1839d472c7f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":13,\"codigo\":\"FLJ-2026-000013\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/13\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', NULL, '2026-08-01 03:06:34', '2026-08-01 03:06:34'),
('e296932d-85ed-4a72-9742-3c1d10ee2768', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":10,\"codigo\":\"FLJ-2026-000010\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/10\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-07-23 21:18:35', '2026-08-06 01:22:58'),
('e43ef271-30bc-43ff-a0f1-2784d153fa13', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":10,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:52:36', '2026-07-14 09:00:39'),
('e4aa3201-3a71-4f33-b79a-d51ab08bd378', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 2, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":9,\"codigo\":\"FLJ-2026-000009\",\"titulo\":\"ejemplo de prueba doble\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/9\",\"mensaje\":\"Tu flujo FLJ-2026-000009 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-08-06 01:22:58', '2026-07-15 02:52:37', '2026-08-06 01:22:58'),
('e59c69f1-c381-4842-a468-6a03fd0bf8a8', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":16,\"etapa\":\"Crear solicitud de compras\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-23 21:18:14', '2026-07-15 02:44:18', '2026-07-23 21:18:14'),
('e7bab370-c222-43f9-8ec6-dabd53c12423', 'App\\Notifications\\Tickets\\TicketIniciadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"ticket_iniciado\",\"mensaje\":\"Jhonatan Suarez comenz\\u00f3 a trabajar en \\\"prueba de notificaciones\\\"\",\"ticket_id\":10,\"codigo\":\"TKT-10\",\"titulo\":\"prueba de notificaciones\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/tickets\\/10\",\"icono\":\"fa-sync-alt\",\"color\":\"accent\",\"actor\":{\"name\":\"Jhonatan Suarez\",\"iniciales\":\"JS\",\"rol\":\"T\\u00e9cnico\"},\"tipo_documento\":\"Otro\",\"tipo_icono\":\"fa-ticket-alt\",\"etapa\":\"En progreso\"}', '2026-08-31 20:07:43', '2026-08-06 01:09:46', '2026-08-31 20:07:43'),
('e985f1fd-ac25-49b8-a995-2053041ac443', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tu flujo FLJ-2026-000005 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', '2026-07-23 21:18:14', '2026-07-14 08:56:36', '2026-07-23 21:18:14'),
('eb4bb5c4-5bb7-4225-a623-d43a67ab68e6', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":6,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:48:27', '2026-07-14 09:00:39'),
('f1a76dca-f7c4-4fb3-b2f6-02112531bc52', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 1, '{\"tipo\":\"flujo_aprobado\",\"mensaje\":\"Administrador firm\\u00f3 \\\"123123123d\\\" \\u2014 tr\\u00e1mite aprobado\",\"instance_id\":15,\"codigo\":\"FLJ-2026-000015\",\"titulo\":\"123123123d\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/15\",\"icono\":\"fa-check-circle\",\"color\":\"success\",\"actor\":{\"name\":\"Administrador\",\"iniciales\":\"AD\",\"rol\":\"Firma final\"},\"tipo_documento\":\"prueba de indice\",\"tipo_icono\":\"fa-file-alt\",\"firmantes\":[\"Administrador\"]}', '2026-09-01 01:02:16', '2026-09-01 00:45:54', '2026-09-01 01:02:16'),
('f9bcc6e2-35fd-484b-829a-17b38490616b', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"mensaje\":\"Usuario Final Demo requiere tu aprobaci\\u00f3n: \\\"ejemplo de prueba\\\"\",\"instance_id\":13,\"codigo\":\"FLJ-2026-000013\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/13\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\",\"actor\":{\"name\":\"Usuario Final Demo\",\"iniciales\":\"UD\",\"rol\":\"Iniciador\"},\"tipo_documento\":\"Cotizaci\\u00f3n\",\"tipo_icono\":\"fa-ticket\",\"etapa\":\"aprobacion doble gerenia\"}', '2026-08-06 01:22:58', '2026-08-01 03:06:34', '2026-08-06 01:22:58'),
('faf750ea-0508-4356-bfac-b36aa8b97729', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":8,\"codigo\":\"FLJ-2026-000008\",\"titulo\":\"cotizacion para cliene eis\",\"assignment_id\":17,\"etapa\":\"Solicitud de cotizacion a proveedores\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/8\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000008\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-15 02:45:14', '2026-07-15 02:45:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'tickets.ver-todos', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(2, 'tickets.ver-asignados', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(3, 'tickets.ver-propios', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(4, 'tickets.crear', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(5, 'tickets.asignar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(6, 'tickets.iniciar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(7, 'tickets.resolver', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(8, 'tickets.cerrar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(9, 'tickets.cancelar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(10, 'tickets.eliminar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(11, 'usuarios.ver', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(12, 'usuarios.crear', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(13, 'usuarios.editar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(14, 'usuarios.eliminar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(15, 'usuarios.asignar-empresa', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(16, 'empresas.gestionar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(17, 'sedes.gestionar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(18, 'equipos.editar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(19, 'equipos.ver', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(20, 'auditoria.ver', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(21, 'roles.gestionar', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(22, ' tickets.editar', 'web', NULL, NULL),
(23, 'equipos.crear', 'web', NULL, NULL),
(24, 'equipos.eliminar', 'web', NULL, NULL),
(25, 'mpreventivo.crear', 'web', NULL, NULL),
(26, 'mpreventivo.editar', 'web', NULL, NULL),
(27, 'mpreventivo.ver', 'web', NULL, NULL),
(28, 'mpreventivo.eliminar', 'web', NULL, NULL),
(29, 'flujos.plantillas.gestionar', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(30, 'flujos.tipos-documento.gestionar', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(31, 'flujos.iniciar', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(32, 'flujos.ver-todos', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(33, 'flujos.ver-participando', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(34, 'flujos.cancelar-cualquiera', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(35, 'flujos.corregir-propio', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(36, 'flujos.reabrir', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(37, 'flujos.desistir-propio', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34'),
(38, 'equipos.gestionar', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(39, 'tickets.editar', 'web', NULL, NULL),
(40, 'flujos.editar-cabecera', 'web', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(2, 'admin', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(3, 'tecnico', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25'),
(5, 'gerente', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(6, 'lider-tecnico', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(7, 'lider-ventas', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(8, 'asesor-ventas', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(9, 'auxiliar-bodega', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(10, 'auxiliar-administrativo', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(11, 'talento-humano', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(12, 'auxiliar-cartera', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(13, 'auxiliar-contabilidad', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45'),
(14, 'recepcion', 'web', '2026-08-05 20:20:45', '2026-08-05 20:20:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(4, 1),
(4, 2),
(4, 3),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 14),
(5, 1),
(5, 2),
(5, 5),
(5, 6),
(6, 1),
(6, 2),
(6, 3),
(6, 5),
(6, 6),
(7, 1),
(7, 2),
(7, 3),
(7, 5),
(7, 6),
(8, 1),
(8, 2),
(8, 5),
(8, 6),
(9, 1),
(9, 2),
(9, 5),
(10, 1),
(11, 1),
(11, 2),
(11, 5),
(11, 6),
(11, 7),
(11, 11),
(12, 1),
(12, 2),
(12, 5),
(13, 1),
(13, 2),
(13, 5),
(14, 1),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(16, 5),
(17, 1),
(17, 2),
(17, 5),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(19, 3),
(19, 9),
(20, 1),
(20, 5),
(21, 1),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 1),
(29, 2),
(29, 5),
(30, 1),
(30, 2),
(30, 5),
(31, 1),
(31, 2),
(31, 3),
(31, 5),
(31, 6),
(31, 7),
(31, 8),
(31, 9),
(31, 10),
(31, 11),
(31, 12),
(31, 13),
(31, 14),
(32, 1),
(32, 2),
(32, 5),
(32, 7),
(32, 8),
(33, 1),
(33, 2),
(33, 3),
(33, 6),
(33, 8),
(33, 9),
(33, 10),
(33, 11),
(33, 12),
(33, 13),
(33, 14),
(34, 1),
(34, 2),
(34, 5),
(35, 1),
(35, 2),
(35, 3),
(35, 5),
(35, 6),
(35, 7),
(35, 8),
(35, 9),
(35, 10),
(35, 11),
(35, 12),
(35, 13),
(35, 14),
(36, 1),
(36, 2),
(36, 5),
(37, 1),
(37, 2),
(37, 3),
(37, 5),
(37, 6),
(37, 7),
(37, 8),
(37, 9),
(37, 10),
(37, 11),
(37, 12),
(37, 13),
(37, 14),
(38, 2),
(38, 5),
(38, 6),
(39, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `empresa_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id`, `empresa_id`, `nombre`, `ciudad`, `direccion`, `telefono`, `activo`, `created_at`, `updated_at`) VALUES
(2, 1, 'avenida 0', 'cucuta', 'avenida 0', '3202387718', 1, NULL, NULL),
(3, 1, 'SERGIO ANDRES CAICEDO PEREZ', 'CUCUTA', 'Mz4 Casa 16 La Fortuna', '3202387718', 1, '2026-06-24 21:58:04', '2026-06-24 21:58:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('iA6Suk3YEajJ2KzfulK39B162CRQxffFwk7za8Wa', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiazZWdEphaVNKOEM4eFhpd0tDRG9SQzJ0aE9FS1hwNE1MNW0xRVBociI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY29uZmlndXJhY2lvbi9hanVzdGVzIjtzOjU6InJvdXRlIjtzOjI3OiJjb25maWd1cmFjaW9uLmFqdXN0ZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1788206815),
('ip2pauVBitQzZ44vVnPI72Ju0BtuUufrQjW60WxD', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ2YxalB2MTl6T29uN0JBell2ZWtUQTBYSENReER3eFJqbnlGV1hwYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mbHVqb3MvaW5zdGFuY2lhcy8xNiI7czo1OiJyb3V0ZSI7czoyMjoiZmx1am9zLmluc3RhbmNpYXMuc2hvdyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1788206808);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'string',
  `categoria` varchar(50) NOT NULL DEFAULT 'general',
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `clave`, `valor`, `tipo`, `categoria`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'flujos.edicion_post_cierre', 'true', 'bool', 'flujos', 'Permite a los actores de una etapa agregar adjuntos y comentarios adicionales después de que la etapa fue aprobada o rechazada. Los datos originales (firma, comentario del actor, adjuntos previos) siempre quedan inmutables.', '2026-08-31 14:23:09', '2026-09-01 01:06:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `solicitante_id` bigint(20) UNSIGNED NOT NULL,
  `asignado_a` bigint(20) UNSIGNED DEFAULT NULL,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `categoria` enum('hardware','software','red','impresora','telefonia','acceso','otro') NOT NULL DEFAULT 'otro',
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `estado` enum('abierto','asignado','en_progreso','resuelto','cerrado','cancelado') NOT NULL DEFAULT 'abierto',
  `descripcion` text NOT NULL,
  `foto_solicitud` varchar(255) DEFAULT NULL,
  `evidencia_resolucion` varchar(255) DEFAULT NULL,
  `notas_resolucion` text DEFAULT NULL,
  `asignado_at` timestamp NULL DEFAULT NULL,
  `iniciado_at` timestamp NULL DEFAULT NULL,
  `resuelto_at` timestamp NULL DEFAULT NULL,
  `cerrado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `solicitante_id`, `asignado_a`, `empresa_id`, `sede_id`, `titulo`, `categoria`, `prioridad`, `estado`, `descripcion`, `foto_solicitud`, `evidencia_resolucion`, `notas_resolucion`, `asignado_at`, `iniciado_at`, `resuelto_at`, `cerrado_at`, `created_at`, `updated_at`) VALUES
(7, 4, 3, 1, 2, 'ejemplo de prueba', 'red', 'media', 'cerrado', 'asdasdasdasdasdasdasd', NULL, 'tickets/evidencias/TW9dxUSOIjbeJUN3OIz5Pf9hgfTLfwPQgSQW3bX9.jpg', 'probe y solucione', '2026-06-24 20:49:08', '2026-06-24 20:50:38', '2026-06-24 20:51:42', '2026-06-24 21:05:41', '2026-06-24 02:22:24', '2026-06-24 21:05:41'),
(8, 4, 3, 1, 2, 'ejemplo de prueba', 'impresora', 'baja', 'asignado', 'sadasdasdasdasad', 'tickets/solicitudes/Lcd8QRWLgS7VGa9HSChnO6CStXAf7IZz5DrV5UI7.jpg', NULL, NULL, '2026-07-07 03:16:51', NULL, NULL, NULL, '2026-06-26 02:22:07', '2026-07-07 03:17:12'),
(9, 4, 3, 1, 2, 'ejemplo usuario demo', 'software', 'alta', 'asignado', 'prubahashahsahsa', 'tickets/solicitudes/bXcxZIMJvs0kvBNi6FIwwHLssb0nvZEFYwGaIB1e.jpg', NULL, NULL, '2026-06-26 20:45:28', NULL, NULL, NULL, '2026-06-26 20:45:28', '2026-06-26 20:45:28'),
(10, 1, 6, 1, 2, 'prueba de notificaciones', 'otro', 'media', 'en_progreso', 'probaremos si se notifica', NULL, NULL, NULL, '2026-08-06 01:07:58', '2026-08-06 01:09:46', NULL, NULL, '2026-08-06 01:07:58', '2026-08-06 01:09:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `empresa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sede_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `empresa_id`, `sede_id`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Administrador', 'superadmin@andi.com', NULL, '$2y$12$VHMb/s6STjsSSz/flVZ38uWCzw7pe714P5Eugl6IFeEYA6MyL7cDy', NULL, 1, NULL, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04', NULL),
(2, 'Administrador', 'admin@andi.com', NULL, '$2y$12$DTL/HoxB3gtuXQ0K9fwlkeQbtdlqsfPwFwgj1Ih4t99fD7k.zDxzK', 'tL1QoTgvOzVDg8Ct41IKusnME0zUF9G3e1pRQ32lvVpEagcC68gIGbaQeDz1', NULL, NULL, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04', NULL),
(3, 'Técnico Demo', 'tecnico@andi.com', NULL, '$2y$12$lvaTl5webV88A7g6ycgfze7YgPb9WEo2unQyIwpPtIzCyHo6RCfrS', NULL, NULL, NULL, 1, '2026-06-23 18:46:04', '2026-08-05 20:31:45', '2026-08-05 20:31:45'),
(4, 'Usuario Final Demo', 'usuario@andi.com', NULL, '$2y$12$Y9QzUYKoKGhchd8WYqHKP..Rq8rkyJUjFKai.qGjtV6kdEE5N4Czi', NULL, 1, 2, 1, '2026-06-23 18:46:04', '2026-08-05 20:30:36', '2026-08-05 20:30:36'),
(6, 'Jhonatan Suarez', 'suarez@gmail.com', NULL, '$2y$12$FNqu4k7KtcJt7PzgaflLku3mrPWE8auezy89wSRJgyp/GPkY3ek/2', NULL, NULL, NULL, 1, '2026-08-06 01:07:15', '2026-08-06 01:07:15', NULL),
(7, 'Brayam Florez', 'brayamscf@gmail.com', NULL, '$2y$12$3hHU8CsR11sScKW58zC4aeXS/E5pn4ADwuauO4cFlLuJ4PiO.Tf6K', NULL, NULL, NULL, 1, '2026-08-06 01:55:03', '2026-08-06 01:55:03', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_attachments`
--

CREATE TABLE `workflow_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_step_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subido_por` bigint(20) UNSIGNED NOT NULL,
  `revision` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `nombre_original` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `tamano_bytes` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_attachments`
--

INSERT INTO `workflow_attachments` (`id`, `workflow_instance_id`, `workflow_instance_step_id`, `subido_por`, `revision`, `nombre_original`, `ruta`, `mime_type`, `tamano_bytes`, `created_at`, `updated_at`) VALUES
(1, 5, 5, 3, 1, 'breiner.jpg', 'flujos/5/rev-1/0387b8e5-e8f6-4f3c-93bf-e25ec0161ce6.jpg', 'image/jpeg', 1580039, '2026-07-14 08:39:03', '2026-07-14 08:39:03'),
(2, 5, 7, 3, 1, 'feliz año.png', 'flujos/5/rev-1/6e032d13-b405-4907-af65-a8613a113d85.png', 'image/png', 896859, '2026-07-14 08:52:26', '2026-07-14 08:52:26'),
(3, 5, 8, 2, 1, 'breiner.jpg', 'flujos/5/rev-1/21ef4bee-c007-46fb-9652-db77fc1dd374.jpg', 'image/jpeg', 1580039, '2026-07-14 08:53:38', '2026-07-14 08:53:38'),
(4, 5, 9, 1, 1, 'feliz año.png', 'flujos/5/rev-1/1591a6a7-20a4-4937-a0c2-bfed54337d69.png', 'image/png', 896859, '2026-07-14 08:56:30', '2026-07-14 08:56:30'),
(5, 7, 11, 4, 1, 'Captura de pantalla (1).png', 'flujos/7/rev-1/d400d0d5-a3fd-4b4b-a32d-c9d33c9e75ac.png', 'image/png', 142390, '2026-07-14 09:07:36', '2026-07-14 09:07:36'),
(6, 8, NULL, 4, 1, 'Textos BITS.docx', 'flujos/8/rev-1/b7e3aa3b-7314-42ec-9f7d-ab5913f9268e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:40:42', '2026-07-15 02:40:42'),
(7, 8, 12, 4, 1, 'Textos BITS.docx', 'flujos/8/rev-1/f2b822c3-dce0-4523-81fc-67b83cf5ace1.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:41:47', '2026-07-15 02:41:47'),
(8, 8, 13, 3, 1, 'Textos BITS (1).docx', 'flujos/8/rev-1/b33080aa-4170-4d79-b395-3a54de21850e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:44:01', '2026-07-15 02:44:01'),
(9, 8, 14, 4, 1, 'Textos BITS.docx', 'flujos/8/rev-1/9af4d38e-7352-4524-8adf-ed5cf5b3c4aa.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:45:05', '2026-07-15 02:45:05'),
(10, 8, 15, 3, 1, 'Textos BITS.docx', 'flujos/8/rev-1/86a05f74-9659-4bcd-8651-47d42a099dbd.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:45:31', '2026-07-15 02:45:31'),
(11, 8, 16, 4, 1, 'feliz año.png', 'flujos/8/rev-1/970b06e2-84c9-4f40-980b-d90f8b5a8cbd.png', 'image/png', 896859, '2026-07-15 02:46:07', '2026-07-15 02:46:07'),
(12, 9, 17, 2, 1, 'Textos BITS.docx', 'flujos/9/rev-1/6dbffae6-b9c0-40f8-9196-3b333dfd1f2a.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:51:56', '2026-07-15 02:51:56'),
(13, 9, 17, 4, 1, 'Textos BITS.docx', 'flujos/9/rev-1/cf26c2fa-34e8-415e-b769-29f1c1da75ea.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 27657, '2026-07-15 02:52:31', '2026-07-15 02:52:31'),
(14, 11, 19, 2, 1, 'INFORME.pdf', 'flujos/11/rev-1/b4d71d0b-b9f9-457b-9ca7-0727f7261b55.pdf', 'application/pdf', 284059, '2026-08-01 01:52:27', '2026-08-01 01:52:27'),
(15, 13, 21, 4, 1, 'INFORME.pdf', 'flujos/13/rev-1/21406913-9132-4a6f-b6a9-aed5e81a09ee.pdf', 'application/pdf', 284059, '2026-08-01 03:16:49', '2026-08-01 03:16:49'),
(16, 16, 24, 2, 1, '3.png', 'flujos/16/rev-1/bd6e3480-75af-44cd-a390-a8663236b083.png', 'image/png', 125130, '2026-08-31 19:39:37', '2026-08-31 19:39:37'),
(17, 15, 23, 2, 1, '3.png', 'flujos/15/rev-1/9527b730-6867-4566-86f1-0de1c2aa19be.png', 'image/png', 125130, '2026-09-01 00:45:46', '2026-09-01 00:45:46'),
(18, 15, 23, 2, 1, '2.png', 'flujos/15/rev-1/04b00de4-b7ce-4314-bf2c-aea849571fe9.png', 'image/png', 116323, '2026-09-01 00:58:12', '2026-09-01 00:58:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_instances`
--

CREATE TABLE `workflow_instances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL COMMENT 'Código legible; ej. FLJ-2026-000123',
  `referencia_externa` varchar(50) DEFAULT NULL,
  `workflow_template_id` bigint(20) UNSIGNED NOT NULL,
  `document_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `iniciado_por` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('en_progreso','requiere_correccion','aprobado','cancelado','archivado') NOT NULL DEFAULT 'en_progreso',
  `etapa_actual_id` bigint(20) UNSIGNED DEFAULT NULL,
  `revision` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `iniciado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `cerrado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_instances`
--

INSERT INTO `workflow_instances` (`id`, `codigo`, `referencia_externa`, `workflow_template_id`, `document_type_id`, `titulo`, `descripcion`, `iniciado_por`, `estado`, `etapa_actual_id`, `revision`, `iniciado_en`, `cerrado_en`, `created_at`, `updated_at`) VALUES
(5, 'FLJ-2026-000005', NULL, 3, NULL, 'ejemplo de prueba', 'ejemplo', 4, 'aprobado', NULL, 1, '2026-07-14 08:34:30', '2026-07-14 08:56:36', '2026-07-14 08:34:30', '2026-07-14 08:56:36'),
(6, 'FLJ-2026-000006', NULL, 4, NULL, 'ejemplo de prueba', 'subida', 2, 'en_progreso', 9, 1, '2026-07-14 09:06:42', NULL, '2026-07-14 09:06:42', '2026-07-14 09:06:44'),
(7, 'FLJ-2026-000007', NULL, 4, NULL, 'ejemplo de prueba', NULL, 4, 'aprobado', NULL, 1, '2026-07-14 09:07:29', '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41'),
(8, 'FLJ-2026-000008', NULL, 5, 5, 'cotizacion para cliene eis', NULL, 4, 'aprobado', NULL, 1, '2026-07-15 02:39:58', '2026-07-15 02:46:18', '2026-07-15 02:39:58', '2026-07-15 02:46:18'),
(9, 'FLJ-2026-000009', NULL, 6, 5, 'ejemplo de prueba doble', 'rueba doble', 2, 'aprobado', NULL, 1, '2026-07-15 02:51:44', '2026-07-15 02:52:37', '2026-07-15 02:51:44', '2026-07-15 02:52:37'),
(10, 'FLJ-2026-000010', NULL, 6, 5, 'ejemplo de prueba', NULL, 4, 'en_progreso', 13, 1, '2026-07-23 21:18:33', NULL, '2026-07-23 21:18:33', '2026-07-23 21:18:35'),
(11, 'FLJ-2026-000011', NULL, 6, 5, 'prueba', 'prueba', 2, 'en_progreso', 13, 1, '2026-08-01 01:51:00', NULL, '2026-08-01 01:51:00', '2026-08-01 01:51:04'),
(12, 'FLJ-2026-000012', NULL, 7, 6, 'prueba indice', NULL, 2, 'en_progreso', 14, 1, '2026-08-01 01:54:41', NULL, '2026-08-01 01:54:41', '2026-08-01 01:54:42'),
(13, 'FLJ-2026-000013', NULL, 6, 5, 'ejemplo de prueba', 'prueba de index', 4, 'en_progreso', 13, 1, '2026-08-01 03:06:28', NULL, '2026-08-01 03:06:28', '2026-08-01 03:06:34'),
(14, 'FLJ-2026-000014', '12312312', 3, NULL, 'Cotización cliente ACME', 'Cotización de servicios recurrentes 2026.', 1, 'en_progreso', 6, 1, '2026-08-06 01:11:05', NULL, '2026-08-06 01:11:05', '2026-08-06 01:11:06'),
(15, 'FLJ-2026-000015', '123123123', 7, 6, '123123123d', '1231231d', 1, 'aprobado', NULL, 1, '2026-08-29 03:29:07', '2026-09-01 00:45:51', '2026-08-29 03:29:07', '2026-09-01 00:45:51'),
(16, 'FLJ-2026-000016', '12', 7, 6, 'prueba edicion', NULL, 1, 'aprobado', NULL, 1, '2026-08-31 19:38:53', '2026-08-31 19:39:44', '2026-08-31 19:38:53', '2026-08-31 19:39:44'),
(17, 'FLJ-2026-000017', '12', 6, 5, 'fuerza', NULL, 1, 'en_progreso', 13, 1, '2026-09-01 01:04:58', NULL, '2026-09-01 01:04:58', '2026-09-01 01:05:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_instance_history`
--

CREATE TABLE `workflow_instance_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_id` bigint(20) UNSIGNED NOT NULL,
  `editado_por` bigint(20) UNSIGNED DEFAULT NULL,
  `campo` varchar(50) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `editado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `workflow_instance_history`
--

INSERT INTO `workflow_instance_history` (`id`, `workflow_instance_id`, `editado_por`, `campo`, `valor_anterior`, `valor_nuevo`, `editado_en`) VALUES
(1, 15, 1, 'titulo', '123123123', '123123123d', '2026-08-31 19:10:03'),
(2, 15, 1, 'descripcion', '1231231', '1231231d', '2026-08-31 19:10:03'),
(3, 15, 1, 'referencia_externa', '123123123', '123123123D', '2026-08-31 19:10:03'),
(4, 15, 1, 'referencia_externa', '123123123D', '123123123', '2026-08-31 19:11:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_instance_steps`
--

CREATE TABLE `workflow_instance_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_id` bigint(20) UNSIGNED NOT NULL,
  `workflow_stage_id` bigint(20) UNSIGNED NOT NULL,
  `revision` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `codigo_referencia` varchar(50) DEFAULT NULL,
  `estado` enum('pendiente','en_curso','aprobado','rechazado','devuelto','omitido') NOT NULL DEFAULT 'en_curso',
  `inicio_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `cierre_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_instance_steps`
--

INSERT INTO `workflow_instance_steps` (`id`, `workflow_instance_id`, `workflow_stage_id`, `revision`, `codigo_referencia`, `estado`, `inicio_en`, `cierre_en`, `created_at`, `updated_at`) VALUES
(5, 5, 6, 1, NULL, 'aprobado', '2026-07-14 08:34:30', '2026-07-14 08:48:25', '2026-07-14 08:34:30', '2026-07-14 08:48:25'),
(6, 5, 7, 1, NULL, 'devuelto', '2026-07-14 08:48:25', '2026-07-14 08:50:26', '2026-07-14 08:48:25', '2026-07-14 08:50:26'),
(7, 5, 6, 1, NULL, 'aprobado', '2026-07-14 08:50:26', '2026-07-14 08:52:36', '2026-07-14 08:50:26', '2026-07-14 08:52:36'),
(8, 5, 7, 1, NULL, 'aprobado', '2026-07-14 08:52:36', '2026-07-14 08:53:55', '2026-07-14 08:52:36', '2026-07-14 08:53:55'),
(9, 5, 8, 1, NULL, 'aprobado', '2026-07-14 08:53:55', '2026-07-14 08:56:36', '2026-07-14 08:53:55', '2026-07-14 08:56:36'),
(10, 6, 9, 1, NULL, 'en_curso', '2026-07-14 09:06:42', NULL, '2026-07-14 09:06:42', '2026-07-14 09:06:42'),
(11, 7, 9, 1, NULL, 'aprobado', '2026-07-14 09:07:29', '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41'),
(12, 8, 10, 1, NULL, 'aprobado', '2026-07-15 02:39:58', '2026-07-15 02:41:54', '2026-07-15 02:39:58', '2026-07-15 02:41:54'),
(13, 8, 11, 1, NULL, 'rechazado', '2026-07-15 02:41:54', '2026-07-15 02:44:18', '2026-07-15 02:41:54', '2026-07-15 02:44:18'),
(14, 8, 10, 1, NULL, 'aprobado', '2026-07-15 02:44:18', '2026-07-15 02:45:14', '2026-07-15 02:44:18', '2026-07-15 02:45:14'),
(15, 8, 11, 1, NULL, 'aprobado', '2026-07-15 02:45:14', '2026-07-15 02:45:43', '2026-07-15 02:45:14', '2026-07-15 02:45:43'),
(16, 8, 12, 1, NULL, 'aprobado', '2026-07-15 02:45:43', '2026-07-15 02:46:18', '2026-07-15 02:45:43', '2026-07-15 02:46:18'),
(17, 9, 13, 1, NULL, 'aprobado', '2026-07-15 02:51:44', '2026-07-15 02:52:37', '2026-07-15 02:51:44', '2026-07-15 02:52:37'),
(18, 10, 13, 1, NULL, 'en_curso', '2026-07-23 21:18:33', NULL, '2026-07-23 21:18:33', '2026-07-23 21:18:33'),
(19, 11, 13, 1, NULL, 'en_curso', '2026-08-01 01:51:00', NULL, '2026-08-01 01:51:00', '2026-08-01 01:51:00'),
(20, 12, 14, 1, NULL, 'en_curso', '2026-08-01 01:54:41', NULL, '2026-08-01 01:54:41', '2026-08-01 01:54:41'),
(21, 13, 13, 1, 'REF-INDEX-1', 'en_curso', '2026-08-01 03:06:28', NULL, '2026-08-01 03:06:28', '2026-08-01 03:16:41'),
(22, 14, 6, 1, NULL, 'en_curso', '2026-08-06 01:11:05', NULL, '2026-08-06 01:11:05', '2026-08-06 01:11:05'),
(23, 15, 14, 1, NULL, 'aprobado', '2026-08-29 03:29:07', '2026-09-01 00:45:51', '2026-08-29 03:29:07', '2026-09-01 00:45:51'),
(24, 16, 14, 1, NULL, 'aprobado', '2026-08-31 19:38:53', '2026-08-31 19:39:44', '2026-08-31 19:38:53', '2026-08-31 19:39:44'),
(25, 17, 13, 1, NULL, 'en_curso', '2026-09-01 01:04:58', NULL, '2026-09-01 01:04:58', '2026-09-01 01:04:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_stages`
--

CREATE TABLE `workflow_stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_template_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `codigo_externo` varchar(50) DEFAULT NULL,
  `orden` int(10) UNSIGNED NOT NULL,
  `modo` enum('secuencial','paralelo_todos','paralelo_cualquiera') NOT NULL DEFAULT 'secuencial',
  `politica_rechazo` enum('devolver_iniciador','devolver_etapa_anterior','devolver_etapa_especifica','finalizar') NOT NULL DEFAULT 'devolver_iniciador',
  `etapa_destino_rechazo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requiere_adjunto` tinyint(1) NOT NULL DEFAULT 0,
  `sla_horas` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_stages`
--

INSERT INTO `workflow_stages` (`id`, `workflow_template_id`, `nombre`, `descripcion`, `codigo_externo`, `orden`, `modo`, `politica_rechazo`, `etapa_destino_rechazo_id`, `requiere_adjunto`, `sla_horas`, `created_at`, `updated_at`) VALUES
(6, 3, 'Revisión Técnica', 'Validación de viabilidad técnica: alcance, tiempos y recursos.', NULL, 1, 'secuencial', 'devolver_iniciador', NULL, 1, 24, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(7, 3, 'Aprobación Financiera', 'Validación de márgenes y precios. Requiere adjunto con desglose aprobado.', NULL, 2, 'paralelo_todos', 'devolver_etapa_anterior', NULL, 1, 48, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(8, 3, 'Firma Final', 'Autorización ejecutiva. Requiere PDF final firmado como adjunto.', NULL, 3, 'secuencial', 'devolver_iniciador', NULL, 1, 72, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(9, 4, 'subida de la factura', 'que se suba la factura', NULL, 1, 'secuencial', 'finalizar', NULL, 1, NULL, '2026-07-14 09:05:12', '2026-07-14 09:05:12'),
(10, 5, 'Crear solicitud de compras', '', NULL, 1, 'secuencial', 'devolver_iniciador', NULL, 1, NULL, '2026-07-15 02:38:07', '2026-07-15 02:38:07'),
(11, 5, 'Solicitud de cotizacion a proveedores', 'compras debe solicitar a los proveedores las cotizaciones para que la asesora las pase al cliente', NULL, 2, 'secuencial', 'devolver_etapa_anterior', NULL, 1, 24, '2026-07-15 02:38:07', '2026-07-15 02:38:07'),
(12, 5, 'Aceptacion de coización por parte del cliente', '', NULL, 3, 'secuencial', 'devolver_etapa_especifica', 10, 1, 24, '2026-07-15 02:38:07', '2026-07-15 02:38:07'),
(13, 6, 'aprobacion doble gerenia', '', NULL, 1, 'paralelo_todos', 'finalizar', NULL, 1, 24, '2026-07-15 02:51:03', '2026-07-15 02:51:03'),
(14, 7, 'primera etapa indice', 'prueba de indice', NULL, 1, 'paralelo_todos', 'devolver_iniciador', NULL, 0, 1, '2026-08-01 01:54:00', '2026-08-01 01:54:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_stage_actors`
--

CREATE TABLE `workflow_stage_actors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_stage_id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` enum('role','user') NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `etiqueta` varchar(255) DEFAULT NULL COMMENT 'Etiqueta legible; ej. "Aprobación Financiera"',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_stage_actors`
--

INSERT INTO `workflow_stage_actors` (`id`, `workflow_stage_id`, `actor_type`, `actor_id`, `etiqueta`, `created_at`, `updated_at`) VALUES
(7, 6, 'role', 3, 'Revisor técnico', '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(8, 7, 'role', 2, 'Aprobación de márgenes', '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(9, 7, 'role', 2, 'Aprobación de precios comerciales', '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(10, 8, 'role', 1, 'Firma ejecutiva', '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(11, 9, 'role', 14, 'meseros', '2026-07-14 09:05:12', '2026-08-29 02:06:27'),
(12, 10, 'role', 14, NULL, '2026-07-15 02:38:07', '2026-08-29 02:06:27'),
(13, 11, 'role', 3, NULL, '2026-07-15 02:38:07', '2026-07-15 02:38:07'),
(14, 12, 'role', 14, NULL, '2026-07-15 02:38:07', '2026-08-29 02:06:27'),
(15, 13, 'user', 4, NULL, '2026-07-15 02:51:03', '2026-07-15 02:51:03'),
(16, 13, 'user', 2, NULL, '2026-07-15 02:51:03', '2026-07-15 02:51:03'),
(17, 14, 'role', 2, NULL, '2026-08-01 01:54:00', '2026-08-01 01:54:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_step_assignments`
--

CREATE TABLE `workflow_step_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_step_id` bigint(20) UNSIGNED NOT NULL,
  `workflow_stage_actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','devuelto') NOT NULL DEFAULT 'pendiente',
  `comentario` text DEFAULT NULL,
  `decidido_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_step_assignments`
--

INSERT INTO `workflow_step_assignments` (`id`, `workflow_instance_step_id`, `workflow_stage_actor_id`, `user_id`, `estado`, `comentario`, `decidido_en`, `created_at`, `updated_at`) VALUES
(5, 5, 7, 3, 'aprobado', 'revise bien', '2026-07-14 08:48:25', '2026-07-14 08:34:30', '2026-07-14 08:48:25'),
(6, 6, 8, 2, 'devuelto', 'esta mal', '2026-07-14 08:50:26', '2026-07-14 08:48:25', '2026-07-14 08:50:26'),
(7, 6, 9, NULL, 'devuelto', '(Cerrada por decisión de rechazo/devolución de otro actor)', '2026-07-14 08:50:26', '2026-07-14 08:48:27', '2026-07-14 08:50:26'),
(8, 7, 7, 3, 'aprobado', 'revise de nuevo', '2026-07-14 08:52:36', '2026-07-14 08:50:26', '2026-07-14 08:52:36'),
(9, 8, 8, 2, 'aprobado', NULL, '2026-07-14 08:53:42', '2026-07-14 08:52:36', '2026-07-14 08:53:42'),
(10, 8, 9, 2, 'aprobado', 'correcto', '2026-07-14 08:53:55', '2026-07-14 08:52:36', '2026-07-14 08:53:55'),
(11, 9, 10, 1, 'aprobado', NULL, '2026-07-14 08:56:36', '2026-07-14 08:53:55', '2026-07-14 08:56:36'),
(12, 10, 11, NULL, 'pendiente', NULL, NULL, '2026-07-14 09:06:42', '2026-07-14 09:06:42'),
(13, 11, 11, 4, 'aprobado', NULL, '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41'),
(14, 12, 12, 4, 'aprobado', 'revise el final', '2026-07-15 02:41:54', '2026-07-15 02:39:58', '2026-07-15 02:41:54'),
(15, 13, 13, 3, 'rechazado', 'le falta numer ode parte', '2026-07-15 02:44:18', '2026-07-15 02:41:54', '2026-07-15 02:44:18'),
(16, 14, 12, 4, 'aprobado', 'revise de nuvo', '2026-07-15 02:45:14', '2026-07-15 02:44:18', '2026-07-15 02:45:14'),
(17, 15, 13, 3, 'aprobado', 'todo ok', '2026-07-15 02:45:43', '2026-07-15 02:45:14', '2026-07-15 02:45:43'),
(18, 16, 14, 4, 'aprobado', 'aprobado, comprar el lenovo', '2026-07-15 02:46:18', '2026-07-15 02:45:43', '2026-07-15 02:46:18'),
(19, 17, 15, 4, 'aprobado', 'listo', '2026-07-15 02:52:37', '2026-07-15 02:51:44', '2026-07-15 02:52:37'),
(20, 17, 16, 2, 'aprobado', NULL, '2026-07-15 02:52:01', '2026-07-15 02:51:45', '2026-07-15 02:52:01'),
(21, 18, 15, 4, 'pendiente', NULL, NULL, '2026-07-23 21:18:33', '2026-07-23 21:18:33'),
(22, 18, 16, 2, 'pendiente', NULL, NULL, '2026-07-23 21:18:35', '2026-07-23 21:18:35'),
(23, 19, 15, 4, 'pendiente', NULL, NULL, '2026-08-01 01:51:00', '2026-08-01 01:51:00'),
(24, 19, 16, 2, 'aprobado', NULL, '2026-08-01 01:52:32', '2026-08-01 01:51:04', '2026-08-01 01:52:32'),
(25, 20, 17, NULL, 'pendiente', NULL, NULL, '2026-08-01 01:54:41', '2026-08-01 01:54:41'),
(26, 21, 15, 4, 'aprobado', NULL, '2026-08-01 03:16:52', '2026-08-01 03:06:28', '2026-08-01 03:16:52'),
(27, 21, 16, 2, 'pendiente', NULL, NULL, '2026-08-01 03:06:34', '2026-08-01 03:06:34'),
(28, 22, 7, NULL, 'pendiente', NULL, NULL, '2026-08-06 01:11:05', '2026-08-06 01:11:05'),
(29, 23, 17, 2, 'aprobado', 'OK', '2026-09-01 00:45:51', '2026-08-29 03:29:07', '2026-09-01 00:45:51'),
(30, 24, 17, 2, 'aprobado', 'realizado', '2026-08-31 19:39:44', '2026-08-31 19:38:53', '2026-08-31 19:39:44'),
(31, 25, 15, 4, 'pendiente', NULL, NULL, '2026-09-01 01:04:58', '2026-09-01 01:04:58'),
(32, 25, 16, 2, 'pendiente', NULL, NULL, '2026-09-01 01:04:58', '2026-09-01 01:04:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_step_comments`
--

CREATE TABLE `workflow_step_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_step_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comentario` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `workflow_step_comments`
--

INSERT INTO `workflow_step_comments` (`id`, `workflow_instance_step_id`, `user_id`, `comentario`, `created_at`, `updated_at`) VALUES
(1, 24, 2, 'prueba', '2026-08-31 19:57:11', '2026-08-31 19:57:11'),
(2, 23, 2, 'prueba', '2026-09-01 00:58:43', '2026-09-01 00:58:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_templates`
--

CREATE TABLE `workflow_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_templates`
--

INSERT INTO `workflow_templates` (`id`, `nombre`, `descripcion`, `version`, `activo`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'Aprobación de Cotización', 'Flujo estándar para cotizaciones comerciales: revisión técnica, aprobación financiera paralela y firma ejecutiva.', 1, 1, 1, '2026-07-14 03:30:52', '2026-08-11 03:31:11'),
(4, 'recepcion de facturas', 'almacenaje de facturas', 1, 1, 2, '2026-07-14 09:05:12', '2026-07-14 09:05:12'),
(5, 'Cotización oficanon', 'para el maejo de cotizaciones en oficanon', 1, 1, 2, '2026-07-15 02:38:07', '2026-07-15 02:38:07'),
(6, 'aprobacion doble', NULL, 1, 1, 2, '2026-07-15 02:51:03', '2026-07-15 02:51:03'),
(7, 'orueba de index', NULL, 1, 1, 2, '2026-08-01 01:53:59', '2026-08-01 01:53:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_types_slug_unique` (`slug`),
  ADD KEY `document_types_workflow_template_id_foreign` (`workflow_template_id`),
  ADD KEY `document_types_activo_nombre_index` (`activo`,`nombre`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empresas_nit_unique` (`nit`);

--
-- Indices de la tabla `empresa_user`
--
ALTER TABLE `empresa_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empresa_user_asignacion_unique` (`user_id`,`empresa_id`,`sede_id`),
  ADD KEY `empresa_user_empresa_id_foreign` (`empresa_id`),
  ADD KEY `empresa_user_sede_id_foreign` (`sede_id`),
  ADD KEY `empresa_user_user_id_empresa_id_index` (`user_id`,`empresa_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipos_empresa_serie_unique` (`empresa_id`,`numero_serie`),
  ADD KEY `equipos_sede_id_foreign` (`sede_id`),
  ADD KEY `equipos_responsable_id_foreign` (`responsable_id`),
  ADD KEY `equipos_creado_por_foreign` (`creado_por`),
  ADD KEY `equipos_empresa_id_estado_index` (`empresa_id`,`estado`),
  ADD KEY `equipos_categoria_index` (`categoria`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mantenimientos_preventivos`
--
ALTER TABLE `mantenimientos_preventivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mantenimientos_preventivos_equipo_id_foreign` (`equipo_id`),
  ADD KEY `mantenimientos_preventivos_tecnico_id_foreign` (`tecnico_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sedes_empresa_id_foreign` (`empresa_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`),
  ADD KEY `categoria` (`categoria`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_solicitante_id_foreign` (`solicitante_id`),
  ADD KEY `tickets_asignado_a_foreign` (`asignado_a`),
  ADD KEY `tickets_empresa_id_foreign` (`empresa_id`),
  ADD KEY `tickets_sede_id_foreign` (`sede_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_empresa_id_foreign` (`empresa_id`),
  ADD KEY `users_sede_id_foreign` (`sede_id`);

--
-- Indices de la tabla `workflow_attachments`
--
ALTER TABLE `workflow_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_attachments_workflow_instance_step_id_foreign` (`workflow_instance_step_id`),
  ADD KEY `workflow_attachments_subido_por_foreign` (`subido_por`),
  ADD KEY `workflow_attachments_workflow_instance_id_revision_index` (`workflow_instance_id`,`revision`);

--
-- Indices de la tabla `workflow_instances`
--
ALTER TABLE `workflow_instances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflow_instances_codigo_unique` (`codigo`),
  ADD KEY `workflow_instances_workflow_template_id_foreign` (`workflow_template_id`),
  ADD KEY `workflow_instances_document_type_id_foreign` (`document_type_id`),
  ADD KEY `workflow_instances_iniciado_por_foreign` (`iniciado_por`),
  ADD KEY `workflow_instances_etapa_actual_id_foreign` (`etapa_actual_id`),
  ADD KEY `workflow_instances_estado_iniciado_por_index` (`estado`,`iniciado_por`),
  ADD KEY `workflow_instances_estado_etapa_actual_id_index` (`estado`,`etapa_actual_id`),
  ADD KEY `workflow_instances_referencia_externa_index` (`referencia_externa`);

--
-- Indices de la tabla `workflow_instance_history`
--
ALTER TABLE `workflow_instance_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_instance_id` (`workflow_instance_id`),
  ADD KEY `editado_por` (`editado_por`);

--
-- Indices de la tabla `workflow_instance_steps`
--
ALTER TABLE `workflow_instance_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_instance_steps_workflow_stage_id_foreign` (`workflow_stage_id`),
  ADD KEY `workflow_instance_steps_workflow_instance_id_revision_index` (`workflow_instance_id`,`revision`),
  ADD KEY `workflow_instance_steps_workflow_instance_id_estado_index` (`workflow_instance_id`,`estado`),
  ADD KEY `workflow_instance_steps_codigo_referencia_index` (`codigo_referencia`);

--
-- Indices de la tabla `workflow_stages`
--
ALTER TABLE `workflow_stages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflow_stages_workflow_template_id_orden_unique` (`workflow_template_id`,`orden`),
  ADD KEY `workflow_stages_etapa_destino_rechazo_id_foreign` (`etapa_destino_rechazo_id`),
  ADD KEY `workflow_stages_codigo_externo_index` (`codigo_externo`);

--
-- Indices de la tabla `workflow_stage_actors`
--
ALTER TABLE `workflow_stage_actors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_stage_actors_workflow_stage_id_actor_type_index` (`workflow_stage_id`,`actor_type`),
  ADD KEY `workflow_stage_actors_actor_type_actor_id_index` (`actor_type`,`actor_id`);

--
-- Indices de la tabla `workflow_step_assignments`
--
ALTER TABLE `workflow_step_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_step_assignments_workflow_stage_actor_id_foreign` (`workflow_stage_actor_id`),
  ADD KEY `workflow_step_assignments_workflow_instance_step_id_estado_index` (`workflow_instance_step_id`,`estado`),
  ADD KEY `workflow_step_assignments_user_id_estado_index` (`user_id`,`estado`);

--
-- Indices de la tabla `workflow_step_comments`
--
ALTER TABLE `workflow_step_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_instance_step_id` (`workflow_instance_step_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `workflow_templates`
--
ALTER TABLE `workflow_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_templates_created_by_foreign` (`created_by`),
  ADD KEY `workflow_templates_activo_nombre_index` (`activo`,`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresa_user`
--
ALTER TABLE `empresa_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mantenimientos_preventivos`
--
ALTER TABLE `mantenimientos_preventivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `workflow_attachments`
--
ALTER TABLE `workflow_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `workflow_instances`
--
ALTER TABLE `workflow_instances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `workflow_instance_history`
--
ALTER TABLE `workflow_instance_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `workflow_instance_steps`
--
ALTER TABLE `workflow_instance_steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `workflow_stages`
--
ALTER TABLE `workflow_stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `workflow_stage_actors`
--
ALTER TABLE `workflow_stage_actors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `workflow_step_assignments`
--
ALTER TABLE `workflow_step_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `workflow_step_comments`
--
ALTER TABLE `workflow_step_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `workflow_templates`
--
ALTER TABLE `workflow_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `document_types`
--
ALTER TABLE `document_types`
  ADD CONSTRAINT `document_types_workflow_template_id_foreign` FOREIGN KEY (`workflow_template_id`) REFERENCES `workflow_templates` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `empresa_user`
--
ALTER TABLE `empresa_user`
  ADD CONSTRAINT `empresa_user_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_user_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `equipos_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipos_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mantenimientos_preventivos`
--
ALTER TABLE `mantenimientos_preventivos`
  ADD CONSTRAINT `mantenimientos_preventivos_equipo_id_foreign` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mantenimientos_preventivos_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD CONSTRAINT `sedes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_asignado_a_foreign` FOREIGN KEY (`asignado_a`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_solicitante_id_foreign` FOREIGN KEY (`solicitante_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_sede_id_foreign` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `workflow_attachments`
--
ALTER TABLE `workflow_attachments`
  ADD CONSTRAINT `workflow_attachments_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `workflow_attachments_workflow_instance_id_foreign` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_attachments_workflow_instance_step_id_foreign` FOREIGN KEY (`workflow_instance_step_id`) REFERENCES `workflow_instance_steps` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `workflow_instances`
--
ALTER TABLE `workflow_instances`
  ADD CONSTRAINT `workflow_instances_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_instances_etapa_actual_id_foreign` FOREIGN KEY (`etapa_actual_id`) REFERENCES `workflow_stages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_instances_iniciado_por_foreign` FOREIGN KEY (`iniciado_por`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `workflow_instances_workflow_template_id_foreign` FOREIGN KEY (`workflow_template_id`) REFERENCES `workflow_templates` (`id`);

--
-- Filtros para la tabla `workflow_instance_history`
--
ALTER TABLE `workflow_instance_history`
  ADD CONSTRAINT `fk_wih_instance` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wih_user` FOREIGN KEY (`editado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `workflow_instance_steps`
--
ALTER TABLE `workflow_instance_steps`
  ADD CONSTRAINT `workflow_instance_steps_workflow_instance_id_foreign` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_instance_steps_workflow_stage_id_foreign` FOREIGN KEY (`workflow_stage_id`) REFERENCES `workflow_stages` (`id`);

--
-- Filtros para la tabla `workflow_stages`
--
ALTER TABLE `workflow_stages`
  ADD CONSTRAINT `workflow_stages_etapa_destino_rechazo_id_foreign` FOREIGN KEY (`etapa_destino_rechazo_id`) REFERENCES `workflow_stages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_stages_workflow_template_id_foreign` FOREIGN KEY (`workflow_template_id`) REFERENCES `workflow_templates` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `workflow_stage_actors`
--
ALTER TABLE `workflow_stage_actors`
  ADD CONSTRAINT `workflow_stage_actors_workflow_stage_id_foreign` FOREIGN KEY (`workflow_stage_id`) REFERENCES `workflow_stages` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `workflow_step_assignments`
--
ALTER TABLE `workflow_step_assignments`
  ADD CONSTRAINT `workflow_step_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_step_assignments_workflow_instance_step_id_foreign` FOREIGN KEY (`workflow_instance_step_id`) REFERENCES `workflow_instance_steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_step_assignments_workflow_stage_actor_id_foreign` FOREIGN KEY (`workflow_stage_actor_id`) REFERENCES `workflow_stage_actors` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `workflow_step_comments`
--
ALTER TABLE `workflow_step_comments`
  ADD CONSTRAINT `fk_wsc_step` FOREIGN KEY (`workflow_instance_step_id`) REFERENCES `workflow_instance_steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wsc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `workflow_templates`
--
ALTER TABLE `workflow_templates`
  ADD CONSTRAINT `workflow_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
