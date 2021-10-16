-- 
-- Estructura de tabla para la tabla `encuentros`
-- 

DROP TABLE IF EXISTS `encuentros`;
CREATE TABLE `encuentros` (
  `id` int(11) NOT NULL auto_increment,
  `id_1` int(11) NOT NULL default '0',
  `id_2` int(11) NOT NULL default '0',
  `resultado` varchar(255) NOT NULL default '',
  `ganador` int(11) NOT NULL default '-1',
  `fecha` int(11) NOT NULL default '0',
  `id_juego` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


-- 
-- Estructura de tabla para la tabla `equipos`
-- 

DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos` (
  `id` int(11) NOT NULL auto_increment,
  `id_juego` int(11) NOT NULL default '0',
  `nombre_clan` varchar(255) NOT NULL default '',
  `id_jugadores` varchar(255) NOT NULL default '',
  `id_suplentes` varchar(255) NOT NULL default '',
  `ip_registro` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM COMMENT='el primer ID de los jugadores es el capitan del equipo';

-- 
-- Estructura de tabla para la tabla `juegos`
-- 

DROP TABLE IF EXISTS `juegos`;
CREATE TABLE `juegos` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL default '',
  `numero_jugadores` tinyint(4) NOT NULL default '0',
  `numero_suplentes` tinyint(4) NOT NULL default '0',
  `inscripcion_abierta` int(11) NOT NULL default '1',
  `tipo_torneo` int(11) NOT NULL default '1',
  `cuadro` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `jugadores`
-- 

DROP TABLE IF EXISTS `jugadores`;
CREATE TABLE `jugadores` (
  `id` int(11) NOT NULL auto_increment,
  `nick` varchar(255) NOT NULL default '',
  `nombre` varchar(255) NOT NULL default '',
  `dni` varchar(9) NOT NULL default '',
  `localizacion` text NOT NULL,
  `id_juego` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;
