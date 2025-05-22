-- Estructura de tabla para la tabla usuarios
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
);

-- Estructura de tabla para la tabla tareas
CREATE TABLE IF NOT EXISTS `Tareas` (
  `id_tarea` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario_fk` int(11) NOT NULL,
  `titulo_tarea` varchar(255) NOT NULL,
  `descripcion_detallada` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date DEFAULT NULL,
  `prioridad` enum('Alta','Media','Baja') NOT NULL DEFAULT 'Media',
  `estado_tarea` enum('Pendiente','En Progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente',
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tarea`),
  KEY `id_usuario_fk` (`id_usuario_fk`),
  CONSTRAINT `fk_tareas_usuarios` FOREIGN KEY (`id_usuario_fk`) REFERENCES `Usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
);