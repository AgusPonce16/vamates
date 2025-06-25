-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-06-2025 a las 13:41:53
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
-- Base de datos: `mates_ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('pendiente','pagada','cancelada') DEFAULT 'pendiente',
  `id_proveedor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `descripcion`, `monto`, `fecha`, `estado`, `id_proveedor`) VALUES
(57, '15x Baldo 5kg, 15x REI VERDE PREMIUM 1kg', 630000.00, '2025-06-24', 'pagada', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compras`
--

INSERT INTO `detalle_compras` (`id`, `id_compra`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(156, 57, 60, 15.00, 35000.00),
(157, 57, 59, 15.00, 7000.00);

--
-- Disparadores `detalle_compras`
--
DELIMITER $$
CREATE TRIGGER `actualizar_stock_despues_compra` AFTER INSERT ON `detalle_compras` FOR EACH ROW BEGIN
    -- Declarar variable para el estado
    DECLARE estado_compra VARCHAR(20);
    
    -- Obtener el estado de la compra
    SELECT estado INTO estado_compra 
    FROM compras 
    WHERE id = NEW.id_compra;
    
    -- Solo actualizar stock si la compra está pagada
    IF estado_compra = 'pagada' THEN
        UPDATE productos 
        SET stock = stock + NEW.cantidad 
        WHERE id = NEW.id_producto;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL COMMENT 'Precio unitario al momento de la venta',
  `descuento` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de descuento aplicado al producto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `subtotal`, `precio_unitario`, `descuento`) VALUES
(352, 161, 60, 10, 500000.00, 50000.00, 0.00),
(353, 161, 58, 1, 30000.00, 30000.00, 0.00),
(354, 161, 59, 1, 11000.00, 11000.00, 0.00),
(355, 162, 58, 1, 30000.00, 30000.00, 0.00),
(356, 162, 57, 18, 540000.00, 30000.00, 0.00),
(357, 163, 60, 1, 45000.00, 50000.00, 10.00),
(358, 164, 58, 4, 102000.00, 30000.00, 15.00),
(359, 165, 60, 1, 50000.00, 50000.00, 0.00),
(360, 166, 60, 1, 50000.00, 50000.00, 0.00),
(361, 167, 59, 12, 132000.00, 11000.00, 0.00),
(362, 168, 58, 1, 30000.00, 30000.00, 0.00),
(363, 169, 59, 15, 165000.00, 11000.00, 0.00),
(364, 169, 57, 15, 405000.00, 30000.00, 10.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('Fijo','Variable') NOT NULL,
  `categoria` enum('Servicios','Transporte','Comida','Boludeces','Utilidades','Envíos','Combustible','Devoluciones','Educacion') NOT NULL,
  `estado` enum('pendiente','pagada','cancelada') NOT NULL DEFAULT 'pagada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `descripcion`, `monto`, `fecha`, `tipo`, `categoria`, `estado`) VALUES
(67, 'utilidades auto', 6825.18, '2025-05-20', 'Variable', 'Utilidades', 'pagada'),
(68, 'comida auto', 13094.99, '2025-04-09', 'Fijo', 'Comida', 'pagada'),
(69, 'comida auto', 11341.55, '2025-05-30', 'Fijo', 'Comida', 'pagada'),
(70, 'transporte auto', 3788.65, '2025-04-28', 'Variable', 'Transporte', 'pagada'),
(71, 'educacion auto', 16471.50, '2025-04-24', 'Fijo', 'Educacion', 'pagada'),
(72, 'envíos auto', 28587.05, '2025-04-16', 'Variable', 'Envíos', 'pagada'),
(73, 'servicios auto', 17183.63, '2025-05-25', 'Variable', 'Servicios', 'pagada'),
(74, 'educacion auto', 7543.58, '2025-06-10', 'Fijo', 'Educacion', 'pagada'),
(75, 'transporte auto', 3509.48, '2025-04-20', 'Variable', 'Transporte', 'pagada'),
(76, 'comida auto', 29657.85, '2025-05-02', 'Variable', 'Comida', 'pagada'),
(77, 'Comida', 149604.00, '2025-06-24', 'Variable', 'Comida', 'pagada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `diferencia` decimal(10,2) NOT NULL,
  `estado` enum('activo','desactivado') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio_compra`, `precio`, `stock`, `diferencia`, `estado`) VALUES
(57, 'Torpedo premium', 20000.00, 30000.00, 67, 10000.00, 'activo'),
(58, 'Imperial roma ', 15800.00, 30000.00, 5, 14200.00, 'activo'),
(59, 'REI VERDE PREMIUM 1kg', 7000.00, 11000.00, 137, 4000.00, 'activo'),
(60, 'Baldo 5kg', 35000.00, 50000.00, 52, 15000.00, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `detalle` text DEFAULT NULL,
  `estado` enum('activo','desactivado') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `detalle`, `estado`) VALUES
(11, 'Esperanza SA', 'Yerbas', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `envio` decimal(10,2) DEFAULT 0.00 COMMENT 'Costo de envío de la venta',
  `estado` enum('pendiente','pagada','cancelada') NOT NULL DEFAULT 'pagada',
  `numero_factura` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `total`, `envio`, `estado`, `numero_factura`) VALUES
(151, '2025-04-04', 24959.94, 1500.00, 'pagada', NULL),
(152, '2025-04-28', 58495.57, 0.00, 'pagada', NULL),
(153, '2025-05-17', 63104.93, 3000.00, 'pagada', NULL),
(154, '2025-06-14', 20599.22, 3000.00, 'pagada', NULL),
(155, '2025-05-11', 45085.83, 5000.00, 'pagada', NULL),
(156, '2025-05-01', 87313.79, 5000.00, 'pagada', NULL),
(157, '2025-05-22', 75059.82, 3000.00, 'pagada', NULL),
(158, '2025-05-18', 66511.76, 1500.00, 'pagada', NULL),
(159, '2025-04-19', 27075.81, 3000.00, 'pagada', NULL),
(160, '2025-05-18', 60093.61, 1500.00, 'pagada', NULL),
(161, '2025-06-24', 541000.00, 0.00, 'pagada', NULL),
(162, '2025-06-24', 574000.00, 4000.00, 'pagada', 'FAC-2025-00162'),
(163, '2025-06-24', 45000.00, 0.00, 'pagada', 'FAC-2025-00163'),
(164, '2025-06-16', 103589.00, 1589.00, 'pagada', 'FAC-2025-00164'),
(165, '2025-06-14', 50000.00, 0.00, 'pagada', NULL),
(166, '2025-06-12', 50000.00, 0.00, 'pagada', NULL),
(167, '2025-05-14', 11000.00, 0.00, 'pagada', NULL),
(168, '2025-05-27', 30000.00, 0.00, 'pagada', NULL),
(169, '2025-05-20', 577000.00, 7000.00, 'pagada', NULL);

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `after_venta_update` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE producto_id INT;
    DECLARE cantidad INT;
    DECLARE cur CURSOR FOR 
        SELECT producto_id, cantidad 
        FROM detalle_ventas 
        WHERE venta_id = NEW.id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    IF OLD.estado != NEW.estado THEN
        IF OLD.estado = 'pagada' AND (NEW.estado = 'pendiente' OR NEW.estado = 'cancelada') THEN
            -- Si estaba pagada y pasa a pendiente/cancelada: sumar al stock
            OPEN cur;
            read_loop: LOOP
                FETCH cur INTO producto_id, cantidad;
                IF done THEN
                    LEAVE read_loop;
                END IF;
                
                UPDATE productos SET stock = stock + cantidad WHERE id = producto_id;
            END LOOP;
            CLOSE cur;
        ELSEIF (OLD.estado = 'pendiente' OR OLD.estado = 'cancelada') AND NEW.estado = 'pagada' THEN
            -- Si estaba pendiente/cancelada y pasa a pagada: restar del stock
            OPEN cur;
            read_loop: LOOP
                FETCH cur INTO producto_id, cantidad;
                IF done THEN
                    LEAVE read_loop;
                END IF;
                
                UPDATE productos SET stock = stock - cantidad WHERE id = producto_id;
            END LOOP;
            CLOSE cur;
        END IF;
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `fk_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
