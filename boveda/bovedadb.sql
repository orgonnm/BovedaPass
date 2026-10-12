-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-10-2026 a las 19:01:11
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

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `masterHash`, `masterSalt`) VALUES
(1, '$2y$10$lLSgLQjyJfG5x/qvJtbrVe83eziEITXx7ug1EgLqmiVepqFH2FGku', '2f6eac56ca16a474d96fe206c6645010');

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
-- Volcado de datos para la tabla `credenciales`
--

INSERT INTO `credenciales` (`id`, `servicio`, `usuario`, `passwordCifrada`, `vectorInicializacion`, `saltDerivacion`, `fechaCreacion`) VALUES
(1, 'uisil', 'fabian', 'sJXA7/uvgjCD0wG77l3OaA==', '7e018b185a48576ef5098b3085622af0', '53deadf80dd6d935b9e68aeba7c93205', '2026-10-10 14:56:50'),
(2, 'uisil2', 'michael', 'WJmKZOWqVOHMFYKuwOP6+A==', '85b48bba9ab2c0d923291a31059b307d', 'd9e4ef8e04cc552c123b9cc093a020d7', '2026-10-12 16:28:38'),
(3, 'uisil', 'prueba1', 'UAIU18whLpsr9nBRmkXU+w==', '0a553cfa9ca6d4cd650a0b0f62ee231e', 'b0bcaf388f5e7e24e53876d81ea79bfa', '2026-10-12 17:00:04'),
(4, 'uisil', 'prueba2', '0psp1eR7Me5TboB/serZaA==', '8ba018c5a8d695d5436703e3a918508e', 'cc40d1d631d585af30849adac4093f93', '2026-10-12 17:00:20'),
(5, 'uisil', 'prueba3', 'bBuGKKS3pwShAMGf2BKI5A==', '799414b2c5438c5b77bc28347509d23e', '892e04a317167be30162b1ab93d96068', '2026-10-12 17:00:58');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `credenciales`
--
ALTER TABLE `credenciales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
