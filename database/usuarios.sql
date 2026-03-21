-- Tabla de Usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `usuarios`
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `direccion`, `telefono`, `created_at`, `updated_at`) VALUES
(1, 'joel julien', 'joeljulien576@gmail.com', '$2y$10$S12RJQMrcin.0TpJU27Ia.qTK6ZjV5x1ehqbApBnx6Qx8eD86GCZC', NULL, NULL, '2025-09-04 02:03:48', '2025-09-04 02:03:48');
