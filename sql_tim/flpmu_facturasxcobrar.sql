-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 06-12-2014 a las 19:06:32
-- Versión del servidor: 5.5.40-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `integradb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flpmu_facturasxcobrar`
--

CREATE TABLE IF NOT EXISTS `flpmu_facturasxcobrar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_odv` int(11) NOT NULL,
  `url_xml` varchar(255) NOT NULL,
  `campo1` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `flpmu_facturasxcobrar`
--

INSERT INTO `flpmu_facturasxcobrar` (`id`, `id_odv`, `url_xml`, `campo1`) VALUES
(1, 3, 'facturas/Integrado1/GRUPOSARDEX.xml', ''),
(2, 5, 'facturas/Integrado8/JUEGOSBANCARIOS11.05.14.xml', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
