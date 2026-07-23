-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2026 a las 16:24:13
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
('anditickets-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:37:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:17:\"tickets.ver-todos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:21:\"tickets.ver-asignados\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:19:\"tickets.ver-propios\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:13:\"tickets.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"tickets.asignar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"tickets.iniciar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"tickets.resolver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:14:\"tickets.cerrar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:16:\"tickets.cancelar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"tickets.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"usuarios.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"usuarios.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"usuarios.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"usuarios.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:24:\"usuarios.asignar-empresa\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:18:\"empresas.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:15:\"sedes.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"equipos.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:11:\"equipos.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:13:\"auditoria.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:15:\"roles.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:15:\" tickets.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:4;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:13:\"equipos.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:16:\"equipos.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:17:\"mpreventivo.crear\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:18:\"mpreventivo.editar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:15:\"mpreventivo.ver\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:20:\"mpreventivo.eliminar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:27:\"flujos.plantillas.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:32:\"flujos.tipos-documento.gestionar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:14:\"flujos.iniciar\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:16:\"flujos.ver-todos\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:23:\"flujos.ver-participando\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:26:\"flujos.cancelar-cualquiera\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:22:\"flujos.corregir-propio\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"flujos.reabrir\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:22:\"flujos.desistir-propio\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"tecnico\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:7:\"usuario\";s:1:\"c\";s:3:\"web\";}}}', 1784123448);

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
(3, 'Cotización', 'cotizacion', 'Propuesta comercial dirigida a un cliente. Pasa por validación técnica, financiera y firma.', 'fa-file-invoice-dollar', 3, 1, '2026-07-14 03:30:52', '2026-07-14 03:30:52'),
(4, 'factura de venta', 'factura-venta', 'para uso de comerciales', 'fa-receipt', 4, 1, '2026-07-14 09:03:32', '2026-07-14 09:06:26');

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
(2, 3, 1, NULL, '2026-06-26 20:38:52', '2026-06-26 20:38:52', '2026-06-26 20:38:52'),
(3, 3, 2, NULL, '2026-06-26 20:38:52', '2026-06-26 20:38:52', '2026-06-26 20:38:52'),
(4, 5, 1, 2, '2026-06-26 20:44:40', '2026-06-26 20:44:40', '2026-06-26 20:44:40');

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
(21, '2026_07_13_230029_create_notifications_table', 15);

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
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 4);

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
('0284c6fb-41f1-4e2f-a22f-e3849205d551', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tu flujo FLJ-2026-000005 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', NULL, '2026-07-14 08:56:36', '2026-07-14 08:56:36'),
('08d9f8b7-100f-4b1d-b86a-ef962c675343', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tu flujo FLJ-2026-000007 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', NULL, '2026-07-14 09:07:41', '2026-07-14 09:07:41'),
('120b9879-0779-4f0e-bf81-2a12ef16cd86', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:50:26', '2026-07-14 08:50:26'),
('139a9dbb-2e69-44f1-a5b6-2af618e8883f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:50:26', '2026-07-14 08:50:26'),
('40325e68-b70f-47a1-89cd-1d6be6b279f4', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":6,\"codigo\":\"FLJ-2026-000006\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":12,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/6\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000006\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 09:06:44', '2026-07-14 09:06:44'),
('4ce3a689-9c16-4623-aa00-83bc3295e230', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":7,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:29', '2026-07-14 08:48:27', '2026-07-14 09:00:29'),
('546d2793-82bb-4ebd-b5b4-7de78984370c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:34:33', '2026-07-14 08:34:33'),
('54b9c843-3f5c-46b5-94f9-f053fd61f26f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":9,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:53:21', '2026-07-14 08:52:36', '2026-07-14 08:53:21'),
('60d24007-4f77-4d73-9af2-b08ae7ae3110', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 5, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:34:33', '2026-07-14 08:34:33'),
('6e782a98-3305-4eb8-b360-6423e4e799b1', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":7,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:32', '2026-07-14 08:48:27', '2026-07-14 09:00:32'),
('72ba062c-7ca8-4e15-976d-88344c93e87d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:50:59', '2026-07-14 08:50:26', '2026-07-14 08:50:59'),
('7b5c5401-a8cd-408e-8ad9-949867ace37f', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tu flujo FLJ-2026-000007 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', NULL, '2026-07-14 09:07:41', '2026-07-14 09:07:41'),
('851d44d9-0221-438b-badb-f717b195b4e1', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":9,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:24', '2026-07-14 08:52:36', '2026-07-14 09:00:24'),
('89b6bd3d-6dfe-45d3-b643-5dc4e7add02e', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":13,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000007\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 09:07:29', '2026-07-14 09:07:29'),
('907f03a1-3aad-48c9-96a3-f81f10d7f0db', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:48:50', '2026-07-14 08:34:33', '2026-07-14 08:48:50'),
('95adfce7-42f7-40e7-aa5e-bb80cffcb742', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":7,\"codigo\":\"FLJ-2026-000007\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":13,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/7\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000007\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 09:07:29', '2026-07-14 09:07:29'),
('a1fbb471-12e2-45d6-b0cf-94eb7b9a8f1b', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":6,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:48:27', '2026-07-14 09:00:39'),
('a5cdb98f-b5ba-480d-8430-bbf329678f72', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":10,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:52:36', '2026-07-14 09:00:39'),
('adcfe87c-87be-48ab-9d0b-44038cb0894c', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 4, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":6,\"codigo\":\"FLJ-2026-000006\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":12,\"etapa\":\"subida de la factura\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/6\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000006\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 09:06:44', '2026-07-14 09:06:44'),
('bdfb03f5-b397-465a-94bf-1c4a5aa97dea', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 1, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":11,\"etapa\":\"Firma Final\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:56:04', '2026-07-14 08:53:55', '2026-07-14 08:56:04'),
('c221f6dc-5579-4962-8d78-189220cc6d1f', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":8,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:51:50', '2026-07-14 08:50:26', '2026-07-14 08:51:50'),
('cf06a5de-2b69-4fb5-8457-ea0965cfe8bc', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 1, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":11,\"etapa\":\"Firma Final\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', NULL, '2026-07-14 08:53:55', '2026-07-14 08:53:55'),
('d3dec1c7-3279-4991-acbd-478317b7400d', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 3, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":5,\"etapa\":\"Revisi\\u00f3n T\\u00e9cnica\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 08:48:54', '2026-07-14 08:34:33', '2026-07-14 08:48:54'),
('e43ef271-30bc-43ff-a0f1-2784d153fa13', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":10,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tienes una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:52:36', '2026-07-14 09:00:39'),
('e985f1fd-ac25-49b8-a995-2053041ac443', 'App\\Notifications\\Flujos\\FlujoAprobadoNotification', 'App\\Models\\User', 4, '{\"tipo\":\"flujo_aprobado\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Tu flujo FLJ-2026-000005 fue aprobado\",\"icono\":\"fa-check-circle\",\"color\":\"success\"}', NULL, '2026-07-14 08:56:36', '2026-07-14 08:56:36'),
('eb4bb5c4-5bb7-4225-a623-d43a67ab68e6', 'App\\Notifications\\Flujos\\AsignacionPendienteNotification', 'App\\Models\\User', 2, '{\"tipo\":\"asignacion_pendiente\",\"instance_id\":5,\"codigo\":\"FLJ-2026-000005\",\"titulo\":\"ejemplo de prueba\",\"assignment_id\":6,\"etapa\":\"Aprobaci\\u00f3n Financiera\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/flujos\\/instancias\\/5\",\"mensaje\":\"Ten\\u00e9s una decisi\\u00f3n pendiente en FLJ-2026-000005\",\"icono\":\"fa-hand-paper\",\"color\":\"accent\"}', '2026-07-14 09:00:39', '2026-07-14 08:48:27', '2026-07-14 09:00:39');

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
(37, 'flujos.desistir-propio', 'web', '2026-07-11 03:54:34', '2026-07-11 03:54:34');

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
(4, 'usuario', 'web', '2026-06-23 18:45:25', '2026-06-23 18:45:25');

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
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 4),
(4, 1),
(4, 2),
(4, 4),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(6, 3),
(7, 1),
(7, 2),
(7, 3),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(9, 4),
(10, 1),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(18, 3),
(19, 1),
(19, 2),
(19, 3),
(20, 1),
(21, 1),
(22, 2),
(22, 4),
(23, 2),
(23, 3),
(24, 2),
(25, 2),
(25, 3),
(26, 2),
(26, 3),
(27, 2),
(27, 3),
(28, 2),
(28, 3),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(31, 3),
(31, 4),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(33, 3),
(33, 4),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(35, 3),
(35, 4),
(36, 1),
(36, 2),
(37, 1),
(37, 2),
(37, 3),
(37, 4);

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
('6nlNhdbaF1HYihP6H7Xi22rQWNzCiKOzYPu2s8lQ', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYWl1a1VaOUJIQ2xaNlB6M0o2T1dUOGFaWnJLSjFPTHhqU1d4d3R4cSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZmx1am9zL2luc3RhbmNpYXMvNSI7czo1OiJyb3V0ZSI7czoyMjoiZmx1am9zLmluc3RhbmNpYXMuc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1784037818),
('8rFK5cbvxL4h6z6ypJCIPj7ssCQtL6bCdO9OoF25', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiemVlNnhNSHFRbHpwNTE5QTlRZzhEUU5EcTVCZTl0ZkNSV2J3TXd1cCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mbHVqb3MvaW5zdGFuY2lhcy81IjtzOjU6InJvdXRlIjtzOjIyOiJmbHVqb3MuaW5zdGFuY2lhcy5zaG93Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1784001416),
('otPjCjWTKzK3LlC1FBPxOiuSfAqNbleoYhENOfgW', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoib3NFM01oYWoxb1hhVEFnVXYzbFE2NENSMUZ2UndVdWhpd2ZLZ1BRdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tb3Rvci1mbHVqb3MiO3M6NToicm91dGUiO3M6MjA6InB1YmxpY28ubW90b3ItZmx1am9zIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1784002112);

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
(9, 4, 3, 1, 2, 'ejemplo usuario demo', 'software', 'alta', 'asignado', 'prubahashahsahsa', 'tickets/solicitudes/bXcxZIMJvs0kvBNi6FIwwHLssb0nvZEFYwGaIB1e.jpg', NULL, NULL, '2026-06-26 20:45:28', NULL, NULL, NULL, '2026-06-26 20:45:28', '2026-06-26 20:45:28');

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `empresa_id`, `sede_id`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrador', 'superadmin@andi.com', NULL, '$2y$12$VHMb/s6STjsSSz/flVZ38uWCzw7pe714P5Eugl6IFeEYA6MyL7cDy', NULL, 1, NULL, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04'),
(2, 'Administrador', 'admin@andi.com', NULL, '$2y$12$DTL/HoxB3gtuXQ0K9fwlkeQbtdlqsfPwFwgj1Ih4t99fD7k.zDxzK', 'ul6lwKEhYhOJWcPCOVxVeGxSMWlpIvG3ZfcCGpxYHwejVr0mnlfctcbbK5Hz', NULL, NULL, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04'),
(3, 'Técnico Demo', 'tecnico@andi.com', NULL, '$2y$12$lvaTl5webV88A7g6ycgfze7YgPb9WEo2unQyIwpPtIzCyHo6RCfrS', NULL, NULL, NULL, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04'),
(4, 'Usuario Final Demo', 'usuario@andi.com', NULL, '$2y$12$Y9QzUYKoKGhchd8WYqHKP..Rq8rkyJUjFKai.qGjtV6kdEE5N4Czi', NULL, 1, 2, 1, '2026-06-23 18:46:04', '2026-06-23 18:46:04'),
(5, 'heiner enrique', 'heiner@gmail.com', NULL, '$2y$12$vc3lnJMtfRn2xFLPTdUXWOlXT2Tt3XAWJLR2vaUgDDlNvLl1lrdi.', NULL, NULL, NULL, 1, '2026-06-26 20:44:40', '2026-06-26 20:44:40');

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
(5, 7, 11, 4, 1, 'Captura de pantalla (1).png', 'flujos/7/rev-1/d400d0d5-a3fd-4b4b-a32d-c9d33c9e75ac.png', 'image/png', 142390, '2026-07-14 09:07:36', '2026-07-14 09:07:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_instances`
--

CREATE TABLE `workflow_instances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL COMMENT 'Código legible; ej. FLJ-2026-000123',
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

INSERT INTO `workflow_instances` (`id`, `codigo`, `workflow_template_id`, `document_type_id`, `titulo`, `descripcion`, `iniciado_por`, `estado`, `etapa_actual_id`, `revision`, `iniciado_en`, `cerrado_en`, `created_at`, `updated_at`) VALUES
(5, 'FLJ-2026-000005', 3, 3, 'ejemplo de prueba', 'ejemplo', 4, 'aprobado', NULL, 1, '2026-07-14 08:34:30', '2026-07-14 08:56:36', '2026-07-14 08:34:30', '2026-07-14 08:56:36'),
(6, 'FLJ-2026-000006', 4, 4, 'ejemplo de prueba', 'subida', 2, 'en_progreso', 9, 1, '2026-07-14 09:06:42', NULL, '2026-07-14 09:06:42', '2026-07-14 09:06:44'),
(7, 'FLJ-2026-000007', 4, 4, 'ejemplo de prueba', NULL, 4, 'aprobado', NULL, 1, '2026-07-14 09:07:29', '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_instance_steps`
--

CREATE TABLE `workflow_instance_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_instance_id` bigint(20) UNSIGNED NOT NULL,
  `workflow_stage_id` bigint(20) UNSIGNED NOT NULL,
  `revision` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('pendiente','en_curso','aprobado','rechazado','devuelto','omitido') NOT NULL DEFAULT 'en_curso',
  `inicio_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `cierre_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workflow_instance_steps`
--

INSERT INTO `workflow_instance_steps` (`id`, `workflow_instance_id`, `workflow_stage_id`, `revision`, `estado`, `inicio_en`, `cierre_en`, `created_at`, `updated_at`) VALUES
(5, 5, 6, 1, 'aprobado', '2026-07-14 08:34:30', '2026-07-14 08:48:25', '2026-07-14 08:34:30', '2026-07-14 08:48:25'),
(6, 5, 7, 1, 'devuelto', '2026-07-14 08:48:25', '2026-07-14 08:50:26', '2026-07-14 08:48:25', '2026-07-14 08:50:26'),
(7, 5, 6, 1, 'aprobado', '2026-07-14 08:50:26', '2026-07-14 08:52:36', '2026-07-14 08:50:26', '2026-07-14 08:52:36'),
(8, 5, 7, 1, 'aprobado', '2026-07-14 08:52:36', '2026-07-14 08:53:55', '2026-07-14 08:52:36', '2026-07-14 08:53:55'),
(9, 5, 8, 1, 'aprobado', '2026-07-14 08:53:55', '2026-07-14 08:56:36', '2026-07-14 08:53:55', '2026-07-14 08:56:36'),
(10, 6, 9, 1, 'en_curso', '2026-07-14 09:06:42', NULL, '2026-07-14 09:06:42', '2026-07-14 09:06:42'),
(11, 7, 9, 1, 'aprobado', '2026-07-14 09:07:29', '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workflow_stages`
--

CREATE TABLE `workflow_stages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_template_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
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

INSERT INTO `workflow_stages` (`id`, `workflow_template_id`, `nombre`, `descripcion`, `orden`, `modo`, `politica_rechazo`, `etapa_destino_rechazo_id`, `requiere_adjunto`, `sla_horas`, `created_at`, `updated_at`) VALUES
(6, 3, 'Revisión Técnica', 'Validación de viabilidad técnica: alcance, tiempos y recursos.', 1, 'secuencial', 'devolver_iniciador', NULL, 1, 24, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(7, 3, 'Aprobación Financiera', 'Validación de márgenes y precios. Requiere adjunto con desglose aprobado.', 2, 'paralelo_todos', 'devolver_etapa_anterior', NULL, 1, 48, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(8, 3, 'Firma Final', 'Autorización ejecutiva. Requiere PDF final firmado como adjunto.', 3, 'secuencial', 'devolver_iniciador', NULL, 1, 72, '2026-07-14 08:33:41', '2026-07-14 08:33:41'),
(9, 4, 'subida de la factura', 'que se suba la factura', 1, 'secuencial', 'finalizar', NULL, 1, NULL, '2026-07-14 09:05:12', '2026-07-14 09:05:12');

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
(11, 9, 'role', 4, 'meseros', '2026-07-14 09:05:12', '2026-07-14 09:05:12');

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
(13, 11, 11, 4, 'aprobado', NULL, '2026-07-14 09:07:41', '2026-07-14 09:07:29', '2026-07-14 09:07:41');

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
(3, 'Aprobación de Cotización', 'Flujo estándar para cotizaciones comerciales: revisión técnica, aprobación financiera paralela y firma ejecutiva.', 1, 1, 1, '2026-07-14 03:30:52', '2026-07-14 03:30:52'),
(4, 'recepcion de facturas', 'almacenaje de facturas', 1, 1, 2, '2026-07-14 09:05:12', '2026-07-14 09:05:12');

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
  ADD KEY `workflow_instances_estado_etapa_actual_id_index` (`estado`,`etapa_actual_id`);

--
-- Indices de la tabla `workflow_instance_steps`
--
ALTER TABLE `workflow_instance_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_instance_steps_workflow_stage_id_foreign` (`workflow_stage_id`),
  ADD KEY `workflow_instance_steps_workflow_instance_id_revision_index` (`workflow_instance_id`,`revision`),
  ADD KEY `workflow_instance_steps_workflow_instance_id_estado_index` (`workflow_instance_id`,`estado`);

--
-- Indices de la tabla `workflow_stages`
--
ALTER TABLE `workflow_stages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `workflow_stages_workflow_template_id_orden_unique` (`workflow_template_id`,`orden`),
  ADD KEY `workflow_stages_etapa_destino_rechazo_id_foreign` (`etapa_destino_rechazo_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresa_user`
--
ALTER TABLE `empresa_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `workflow_attachments`
--
ALTER TABLE `workflow_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `workflow_instances`
--
ALTER TABLE `workflow_instances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `workflow_instance_steps`
--
ALTER TABLE `workflow_instance_steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `workflow_stages`
--
ALTER TABLE `workflow_stages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `workflow_stage_actors`
--
ALTER TABLE `workflow_stage_actors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `workflow_step_assignments`
--
ALTER TABLE `workflow_step_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `workflow_templates`
--
ALTER TABLE `workflow_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Filtros para la tabla `workflow_templates`
--
ALTER TABLE `workflow_templates`
  ADD CONSTRAINT `workflow_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
