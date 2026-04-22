-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-11-2025 a las 15:43:25
-- Versión del servidor: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `farmacia`
--
CREATE DATABASE IF NOT EXISTS `farmacia` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `farmacia`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

DROP TABLE IF EXISTS `carrito`;
CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `medicamento_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_agregado` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_compra` timestamp NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `usuario_id`, `fecha_compra`, `total`, `estado`) VALUES
(1, 1, '2025-11-14 15:04:20', 35.94, 'completada'),
(2, 2, '2025-11-15 15:28:50', 108.64, 'cancelada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras_detalle`
--

DROP TABLE IF EXISTS `compras_detalle`;
CREATE TABLE `compras_detalle` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `medicamento_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compras_detalle`
--

INSERT INTO `compras_detalle` (`id`, `compra_id`, `medicamento_id`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 6, 5.99),
(2, 2, 1, 11, 5.99),
(3, 2, 6, 9, 4.75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enfermedades`
--

DROP TABLE IF EXISTS `enfermedades`;
CREATE TABLE `enfermedades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `causas` text DEFAULT NULL,
  `sintomas` text DEFAULT NULL,
  `tratamientos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `enfermedades`
--

INSERT INTO `enfermedades` (`id`, `nombre`, `descripcion`, `causas`, `sintomas`, `tratamientos`) VALUES
(1, 'Tos', 'La tos es un reflejo natural del cuerpo para despejar las vías respiratorias.', 'Infecciones virales, alergias, tabaco, etc.', 'Tos seca o con flemas, irritación de garganta.', 'Descansar, beber líquidos, medicamentos antitusivos.'),
(2, 'Dolor de cabeza', 'El dolor de cabeza es un dolor o molestia en la cabeza, el cuero cabelludo o el cuello.', 'Estrés, tensión, migrañas, deshidratación.', 'Dolor punzante, sensibilidad a la luz o al sonido.', 'Analgésicos, reposo, hidratación.'),
(3, 'Fiebre', 'La fiebre es el aumento temporal de la temperatura corporal.', 'Infecciones, enfermedades inflamatorias, deshidratación.', 'Temperatura superior a 38°C, escalofríos, sudoración.', 'Antipiréticos, reposo, hidratación.'),
(4, 'dolor de barriga', 'Es una molestia en la zona entre el pecho y la ingle', 'Puede ser causada por gases, indigestión, estreñimiento o infecciones', 'náuseas, vómitos, diarrea, estreñimiento, hinchazón y gases', 'aplicar calor, descansar y mantenerse hidratado con líquidos claros, además de comer alimentos suaves'),
(5, 'Vómito', 'Expulsión forzada del contenido del estómago a través de la boca, generalmente precedida por náuseas', 'Puede ser causado por una amplia variedad de factores, como infecciones estomacales, intoxicación alimentaria, mareo por movimiento, embarazo, o incluso emociones fuertes', 'náuseas, arcadas, dolor abdominal, diarrea y mareos', 'rehidratación y la ingesta gradual de alimentos blandos, evitando alimentos picantes, grasos u olores fuertes'),
(6, 'Diarrea', 'evacuación de heces blandas o sueltas, de tres o más veces al día, o con más frecuencia de lo normal para una persona', 'nfecciones por virus, bacterias o parásitos, la ingesta de alimentos o agua contaminados,', 'heces blandas o acuosas, evacuaciones frecuentes, cólicos o dolor abdominal,', 'hidratación con líquidos claros (agua, caldos, bebidas rehidratantes) y alimentos blandos y bajos en fibra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

DROP TABLE IF EXISTS `medicamentos`;
CREATE TABLE `medicamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `enfermedad_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `enfermedad_id`, `imagen`) VALUES
(1, 'Jarabe para la tos', 'Alivia la tos seca e irritativa.', 5.99, 83, 1, 'Jarabe_tos.webp'),
(2, 'Pastillas para la tos', 'Alivian la irritación de garganta y reducen la tos.', 3.50, 160, 1, 'pastillas_tos.jpg'),
(3, 'Ibuprofeno', 'Alivia el dolor de cabeza y reduce la fiebre.', 2.99, 200, 2, 'Ibuprofeno.png'),
(4, 'Paracetamol', 'Analgésico y antipirético para el dolor de cabeza.', 1.99, 200, 2, 'paracetamol.webp'),
(5, 'Aspirina', 'Alivia el dolor y reduce la fiebre.', 3.25, 180, 2, 'aspirina.webp'),
(6, 'Antigripal', 'Combinación de medicamentos para síntomas de gripe y fiebre.', 4.75, 115, 3, 'antigripal.webp'),
(7, 'Omeprazol', 'Alivia el dolor causado por la acidez estomacal, el reflujo ácido (ERGE) y las úlceras gástricas o duodenales', 3.00, 80, 4, 'Omeprazol.png'),
(8, 'Buscapina', 'Alivia los espasmos del tracto gastrointestinal, que son contracciones musculares dolorosas', 10.00, 40, 4, 'buscapina.jpg'),
(9, 'Magaldrato ', 'Se utiliza para aliviar la acidez estomacal, la indigestión ácida, la acidez estomacal y el malestar estomacal', 5.00, 101, 4, 'magaldratos.png'),
(10, 'Dimenhidrinato', 'se usa para prevenir y tratar las náuseas, los vómitos y el vahído causados por el mareo por el movimiento', 4.00, 56, 5, 'Dimenhidrinato.jpg'),
(11, 'Metoclopramida', 'Medicamento que aumenta la motilidad (movimientos y contracciones) del estómago y el intestino superior', 8.00, 48, 5, 'Metoclopramida.jpg'),
(12, 'loperamida', 'Un medicamento antidiarreico que se utiliza para tratar la diarrea aguda', 5.00, 60, 6, 'loperamida.jpg'),
(13, 'subsalicilato de bismuto', 'Se usa para tratar la diarrea ocasional, el malestar estomacal, la acidez y las náuseas', 5.33, 45, 6, 'bismuto.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `direccion`, `telefono`, `fecha_registro`) VALUES
(1, 'abraham morales', 'abraham@gmail.com', '$2y$10$MtqRx2V4gkT0yoHPxkQDn.riagQddgDtJQxI3r1M9TxJtUcSNeSo6', 'el cañon-catia-caracas', '04120229926', '2025-11-14 15:04:06'),
(2, 'cristiam', 'cristiam@gmail.com', '$2y$10$f.CINoJi3aNjRC8ku1h3ieciLzBdTZZEPVP6EADaJUSh0WIJ5QI0u', 'la cruz-gramoven-barrio', '04122725141', '2025-11-15 15:28:21'),
(3, 'daniela', 'daniela@gmail.com', '$2y$10$fraKfHkEPvpoeu3xajIlNebxOdzRV.gL802ewkZ5dyV2WWXAkQv7.', 'catia', '04247894561', '2025-11-17 20:07:45');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `medicamento_id` (`medicamento_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `medicamento_id` (`medicamento_id`);

--
-- Indices de la tabla `enfermedades`
--
ALTER TABLE `enfermedades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enfermedad_id` (`enfermedad_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `enfermedades`
--
ALTER TABLE `enfermedades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras_detalle`
--
ALTER TABLE `compras_detalle`
  ADD CONSTRAINT `compras_detalle_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `compras_detalle_ibfk_2` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD CONSTRAINT `medicamentos_ibfk_1` FOREIGN KEY (`enfermedad_id`) REFERENCES `enfermedades` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
