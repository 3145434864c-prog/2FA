-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2025 a las 19:04:05
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
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` text NOT NULL,
  `fyh_creacion_categoria` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `fyh_creacion_categoria`) VALUES
(2, 'Implementos aseo', '2025-10-16 04:59:46'),
(10, 'Computadores', '2025-10-23 02:55:45'),
(11, 'Muebles', '2025-10-23 03:07:11'),
(13, 'Micrófonos', '2025-10-23 14:21:48'),
(14, 'Selladores', '2025-10-23 14:25:43'),
(17, 'Impresoras', '2025-11-08 20:09:43'),
(18, 'Consolas', '2025-11-08 20:20:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(40) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `request_ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `selector`, `token_hash`, `expires_at`, `used`, `request_ip`, `user_agent`, `created_at`) VALUES
(2, 2, 'e9ed39cf9ecbda06', 'c018647ba02977534b98d0bfd1115111d7f818315508366e0e738e487590e4d4', '2025-11-10 22:05:42', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:05:42'),
(3, 2, '1be48012eda6a522', 'aafeb9cbb6f0933e4a43b51420ab6fc80d6c4e97f95893ecd3bb735141d53c5e', '2025-11-10 22:05:49', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:05:49'),
(4, 2, '23c07c82c0bc7b11', '7633a282bf2b195269037b6f12406c31bc5275e3709aa64eb4a642db3fbe9c5d', '2025-11-10 22:12:46', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:12:46'),
(5, 2, '1c05f28af414533b', '813c36d716d3259ede8a402d7b9ea249424745c972c0587d549b1b13178d2622', '2025-11-10 22:12:51', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:12:51'),
(6, 2, 'e89ed755ac5cb056', 'af3770ca1457a01c445254e4d7ae50f7924f08cb4839aa3ce7262f82103193e5', '2025-11-10 22:14:42', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:14:42'),
(7, 2, '139274297da17393', 'f6aae4bf1e940cbaead39a179fea0d667858ffc9353fac71c3741ee5d5349672', '2025-11-10 22:16:54', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:16:54'),
(8, 2, '078d3900d1ca700a', '905fb1758a1c0e5bdc66316267821849b357386e6d6ec1d06e7cecafd2308a21', '2025-11-10 22:16:58', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:16:58'),
(9, 2, 'd61682089963d108', '49a0d8756edcaa79f78ccf1189ff288059b1cb1719442f6f9669667a2431fb2d', '2025-11-10 22:21:28', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:21:28'),
(10, 2, 'e94488dbc214bf2d', '49bd397e166a9875fa81eb5c20e3edb3febd25b1612ac7548c019eec0769fbd8', '2025-11-10 22:21:51', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:21:51'),
(11, 2, 'd1d1945a12c988a5', '7fbd2bc392d307a7a9c6adb76eeefd40bfdfd8b6609b9c306a89dd6d2327c1bd', '2025-11-10 22:29:09', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 20:29:09'),
(12, 2, 'b6476c9b8a0d993a', 'a5b2da48a5b6b42f7b10b3e2a8944f6ba049d4492027f8da4f759e499b0adc36', '2025-11-11 03:19:43', 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-11 01:19:43'),
(13, 2, '77e240f62bd24e84', 'cdfe5da25e1a8d9ac865c9908f5cc89472483e222266b62c8cfb8c09bbdfbfc2', '2025-11-14 16:30:39', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 14:30:39'),
(14, 2, '5d05c112581f5ed8', '0b603992db0afd9533fc7faef0f163cc4040126e124b8f6096792763a3dc0de5', '2025-11-14 18:18:01', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-14 16:18:01'),
(15, 2, 'd9642ae59da5ad24', '289414b916d6e267aa555b2272c7549211b87824a8d6069febcc30dff92279c2', '2025-11-15 05:34:20', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 03:34:20'),
(16, 2, '262527a042790dab', 'f0d5ac57f4f240d989ce76546c5fa5d650f469b00fb9a60e458935ccd98feb05', '2025-11-15 05:34:43', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 04:24:43'),
(17, 2, '97c664fb11c4676b', '6ea2f0f49af2eca49fb1b5556b02aa29e11e119bb654a7256eefe5fc20e67d7d', '2025-11-15 18:03:26', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-15 16:53:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `referencia` varchar(20) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `referencia`, `id_categoria`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `creado_en`, `actualizado_en`) VALUES
(6, 'PROD-0001', 13, 'Micrófono Omnidireccional SF 666', NULL, 100000.00, 3, 'public/uploads/products/21f01e246f81d9a4.jpg', '2025-11-08 19:36:55', '2025-11-10 13:55:50'),
(10, 'PROD-0002', 18, 'Consola PlayStation 5', NULL, 2000000.00, 1, 'public/uploads/products/a3d3d25e8e65fde7.jpg', '2025-11-08 20:37:21', NULL),
(11, 'PROD-0003', 10, 'Laptop Dell XPS 13', 'Portátil ultraligero con pantalla 4K.', 4000000.00, 15, 'public/uploads/products/b6f0577179405599.jpg', '2025-11-08 20:43:00', NULL),
(12, 'PROD-0004', 10, 'Mouse inalámbrico Razer Viper Ultimate', NULL, 800000.00, 15, 'public/uploads/products/bf7d5af05f795dcc.jpg', '2025-11-10 13:53:54', '2025-11-10 13:54:15'),
(13, 'PROD-0005', 13, 'Auriculares Bose QuietComfort 45', NULL, 1400000.00, 15, 'public/uploads/products/7f4f483da8494470.jpg', '2025-11-10 13:55:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `email_usuario` varchar(120) NOT NULL,
  `password_usuario` varchar(255) NOT NULL,
  `perfil_usuario` varchar(50) NOT NULL,
  `foto_usuario` varchar(255) NOT NULL,
  `estado_usuario` int(11) NOT NULL,
  `ultimo_login` datetime NOT NULL,
  `fyh_creacion_usuario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email_usuario`, `password_usuario`, `perfil_usuario`, `foto_usuario`, `estado_usuario`, `ultimo_login`, `fyh_creacion_usuario`) VALUES
(1, 'Cesar David', 'Admin@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$OTNoT1R1d25YRnRnRzExcw$WsnQ6C6f0DwZHyiEgtKncMlvKheMg0LiEgK4DlCb8yQ', 'administrador', 'vistas/recursos/img/usuarios/690f6c9ad8adb_foto_cesar.jpeg', 1, '0000-00-00 00:00:00', '2025-11-08 16:15:23'),
(2, 'usuario x', '3145434864c@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$cy5kQzJ1WGhDTzhVZGhKdA$Mf0QgC4qHH0IBWM839L+e9WWyWfS6LX/LuLV/tB75n4', 'administrador', 'vistas/recursos/img/usuarios/690f46c000553_usuariox.jpg', 1, '0000-00-00 00:00:00', '2025-11-08 13:21:51'),
(16, 'Guacamayo', 'Guacamayo123!@gmail.com', '$argon2id$v=19$m=131072,t=4,p=2$T2lCRUwwaGE4eGt4TkFwWg$/M7PxE4+hQrFPVoS81+eOs0UOxKuaoQueIo2yE9dfQw', 'administrador', 'vistas/recursos/img/usuarios/691760f945f24_guacamayo.jpg', 1, '0000-00-00 00:00:00', '2025-11-14 16:47:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `selector` (`selector`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `referencia` (`referencia`),
  ADD KEY `idx_prod_categoria` (`id_categoria`),
  ADD KEY `idx_prod_nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
