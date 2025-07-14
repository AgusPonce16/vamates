-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2025 a las 17:17:25
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
  `id_proveedor` int(11) DEFAULT NULL,
  `ajuste` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `descripcion`, `monto`, `fecha`, `estado`, `id_proveedor`, `ajuste`) VALUES
(1, '3 camioneros de algarrobo, 2 imperiales de algarrobo', 117000.00, '2025-04-07', 'pagada', 1, 0.00),
(2, '18 baldo 500gr, 5 Canarias Ed. especial', 161500.00, '2025-04-14', 'pagada', 3, 0.00),
(3, '2x Camionero de algarrobo, 1x Imperial de calabaza', 123200.00, '2025-05-05', 'pagada', 4, 0.00),
(4, '10x Baldo 500g, 10x Canarias tradicional 500g', 163550.00, '2025-06-17', 'pagada', 2, 0.00);

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
(1, 1, 6, 10.00, 4170.00),
(2, 1, 7, 2.00, 2325.00),
(3, 2, 8, 5.00, 3185.00),
(4, 2, 9, 3.00, 2860.00),
(5, 3, 5, 2.00, 11200.00),
(6, 3, 6, 1.00, 13200.00),
(7, 4, 1, 10.00, 4170.00),
(8, 4, 2, 10.00, 4430.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL COMMENT 'Precio unitario al momento de la venta',
  `descuento` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de descuento aplicado al producto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `subtotal`, `precio_unitario`, `descuento`) VALUES
(1, 1, 6, 2, 11000.00, 5500.00, 0.00),
(2, 1, 7, 1, 5500.00, 5500.00, 0.00),
(3, 2, 5, 1, 22500.00, 22500.00, 0.00),
(4, 3, 1, 5, 27000.00, 5500.00, 0.00),
(5, 3, 2, 3, 17100.00, 5700.00, 0.00),
(6, 4, 4, 2, 11000.00, 5500.00, 0.00),
(7, 4, 5, 1, 23500.00, 23500.00, 0.00),
(8, 5, 7, 6, 36000.00, 6000.00, 0.00),
(9, 5, 3, 1, 6500.00, 6500.00, 0.00),
(10, 5, 6, 3, 70500.00, 23500.00, 0.00);

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
(1, 'Nafta', 25000.00, '2025-04-05', 'Fijo', 'Combustible', 'pagada'),
(2, 'Comida', 23000.00, '2025-04-05', 'Variable', 'Comida', 'pagada'),
(3, 'Envio santolaria', 9500.00, '2025-04-23', 'Variable', 'Envíos', 'pagada'),
(4, 'Publicidad', 24000.00, '2025-05-09', 'Variable', 'Utilidades', 'pagada');

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
(1, 'Baldo 500g', 4170.00, 5800.00, 74, 0.00, 'activo'),
(2, 'Canarias tradicional 500g', 4430.00, 6000.00, 6, 1370.00, 'activo'),
(3, 'Canarias serena 500g', 4830.00, 6500.00, 2, 1470.00, 'activo'),
(4, 'Rei verde premium 500g', 3620.00, 5500.00, 12, 1560.00, 'activo'),
(5, 'Camionero de calabaza', 11200.00, 22000.00, 3, 9800.00, 'activo'),
(6, 'Imperial de algarrobo', 13200.00, 23500.00, 17, 9300.00, 'activo'),
(7, 'Bombilla de acero', 2500.00, 6000.00, 43, 3000.00, 'activo'),
(8, 'Termos media manija de acero', 17800.00, 27000.00, 4, 7200.00, 'activo'),
(9, 'Verdecita 500g', 2750.00, 4700.00, 25, 0.00, 'activo'),
(10, 'Grabados', 2200.00, 4500.00, 9996, 3300.00, 'activo');

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
(1, 'ik mates', '5% off en efectivo minimo $70.000', 'activo'),
(2, 'todo mates lp', 'Las parejas, envio mas barato, 15kg, no tiene Rei Verde', 'activo'),
(3, 'esperanza', '20kg minimo', 'activo'),
(4, 'de mi tierra', 'No tiene descuento pero esta mas cerca', 'activo'),
(5, 'tu matero', 'En el medio del centro pero tiene termos baratos', 'activo');

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
  `numero_factura` varchar(20) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `fecha`, `total`, `envio`, `estado`, `numero_factura`, `descripcion`) VALUES
(1, '2025-04-05', 52500.00, 0.00, 'pagada', NULL, 'Venta de productos varios'),
(2, '2025-04-13', 22500.00, 0.00, 'pagada', NULL, 'Venta de imperial de algarrobo'),
(3, '2025-05-02', 135900.00, 0.00, 'pagada', NULL, 'Venta mayorista'),
(4, '2025-06-10', 105100.00, 3550.00, 'pagada', NULL, 'Venta con envío'),
(5, '2025-07-14', 113000.00, 0.00, 'pagada', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
