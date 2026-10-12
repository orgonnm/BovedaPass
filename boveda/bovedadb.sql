-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-10-2026 a las 18:07:28
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
-- Base de datos: `bovedadb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `masterHash` varchar(255) NOT NULL,
  `masterSalt` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credenciales`
--

CREATE TABLE `credenciales` (
  `id` int(11) NOT NULL,
  `servicio` varchar(100) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `passwordCifrada` text NOT NULL,
  `vectorInicializacion` varchar(64) NOT NULL,
  `saltDerivacion` varchar(64) NOT NULL,
  `fechaCreacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `credenciales`
--
ALTER TABLE `credenciales`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `credenciales`
--
ALTER TABLE `credenciales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
