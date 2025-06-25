<?php
include '../config/config.php';

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?alert=error');
    exit;
}

// Verificar que se reciban todos los datos necesarios
$id_venta = intval($_POST['id_venta'] ?? 0);
$fecha = $_POST['fecha'] ?? '';
$estado = $_POST['estado'] ?? '';
$envio = floatval($_POST['envio'] ?? 0);
$descuento = intval($_POST['descuento'] ?? 0);
$productosJSON = $_POST['productosJSON'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$monto = floatval($_POST['monto'] ?? 0);

// Validaciones básicas
if ($id_venta <= 0 || empty($fecha) || empty($estado) || empty($productosJSON)) {
    header('Location: index.php?alert=error&msg=Datos+incompletos');
    exit;
}

// Decodificar productos JSON
$productos = json_decode($productosJSON, true);
if (!$productos || !is_array($productos) || empty($productos)) {
    header('Location: index.php?alert=error&msg=No+hay+productos+válidos');
    exit;
}

// Validar fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    header('Location: index.php?alert=error&msg=Fecha+inválida');
    exit;
}

// Validar estado
$estados_validos = ['pendiente', 'pagada', 'cancelada'];
if (!in_array($estado, $estados_validos)) {
    header('Location: index.php?alert=error&msg=Estado+inválido');
    exit;
}

// Validar descuento (0-30%, múltiplos de 5)
if ($descuento < 0 || $descuento > 30 || $descuento % 5 !== 0) {
    header('Location: index.php?alert=error&msg=Descuento+inválido');
    exit;
}

// Comenzar transacción
$conn->begin_transaction();

try {
    // Verificar que la venta existe
    $sql_check = "SELECT id FROM ventas WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_venta);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        throw new Exception("La venta no existe");
    }
    
    // Obtener los productos actuales de la venta para restaurar stock
    $sql_productos_actuales = "SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?";
    $stmt_actuales = $conn->prepare($sql_productos_actuales);
    $stmt_actuales->bind_param("i", $id_venta);
    $stmt_actuales->execute();
    $productos_actuales = $stmt_actuales->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Restaurar stock de productos anteriores
    foreach ($productos_actuales as $prod_actual) {
        $sql_restaurar_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
        $stmt_restaurar = $conn->prepare($sql_restaurar_stock);
        $stmt_restaurar->bind_param("ii", $prod_actual['cantidad'], $prod_actual['producto_id']);
        $stmt_restaurar->execute();
    }
    
    // Eliminar detalles anteriores
    $sql_delete_detalles = "DELETE FROM detalle_ventas WHERE venta_id = ?";
    $stmt_delete = $conn->prepare($sql_delete_detalles);
    $stmt_delete->bind_param("i", $id_venta);
    $stmt_delete->execute();
    
    // Validar stock y insertar nuevos detalles
    $subtotal_calculado = 0;
    
    foreach ($productos as $producto) {
        $producto_id = intval($producto['id']);
        $cantidad = intval($producto['cantidad']);
        $precio = floatval($producto['precio']);
        
        // Validar datos del producto
        if ($producto_id <= 0 || $cantidad <= 0 || $precio <= 0) {
            throw new Exception("Datos de producto inválidos");
        }
        
        // Verificar que el producto existe y tiene stock suficiente
        $sql_producto = "SELECT id, nombre, stock, estado FROM productos WHERE id = ?";
        $stmt_producto = $conn->prepare($sql_producto);
        $stmt_producto->bind_param("i", $producto_id);
        $stmt_producto->execute();
        $prod_info = $stmt_producto->get_result()->fetch_assoc();
        
        if (!$prod_info) {
            throw new Exception("El producto con ID {$producto_id} no existe");
        }
        
        if ($prod_info['estado'] !== 'activo') {
            throw new Exception("El producto {$prod_info['nombre']} no está activo");
        }
        
        if ($prod_info['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para {$prod_info['nombre']}. Disponible: {$prod_info['stock']}, solicitado: {$cantidad}");
        }
        
        // Actualizar stock del producto
        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        $stmt_update_stock->bind_param("ii", $cantidad, $producto_id);
        $stmt_update_stock->execute();
        
        // Insertar detalle de venta
        $subtotal_producto = $precio * $cantidad;
        $subtotal_calculado += $subtotal_producto;
        
        $sql_detalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        $stmt_detalle->bind_param("iiid", $id_venta, $producto_id, $cantidad, $precio);
        $stmt_detalle->execute();
    }
    
    // Calcular el total con descuento
    $monto_descuento = $subtotal_calculado * ($descuento / 100);
    $total_con_descuento = $subtotal_calculado - $monto_descuento;
    $total_final = $total_con_descuento + $envio;
    
    // Verificar que el monto calculado coincida con el enviado (con una pequeña tolerancia para decimales)
    if (abs($total_final - $monto) > 0.01) {
        throw new Exception("Error en el cálculo del total. Calculado: {$total_final}, Recibido: {$monto}");
    }
    
    // Verificar si la tabla tiene las columnas necesarias
    $columnas_query = "SHOW COLUMNS FROM ventas";
    $columnas_result = $conn->query($columnas_query);
    $columnas_existentes = [];
    while ($col = $columnas_result->fetch_assoc()) {
        $columnas_existentes[] = $col['Field'];
    }
    
    // Construir la consulta UPDATE según las columnas disponibles
    $campos_update = [
        "fecha = ?",
        "estado = ?", 
        "descripcion = ?",
        "total = ?",
        "envio = ?"
    ];
    $tipos_params = "sssdd";
    $valores_params = [$fecha, $estado, $descripcion, $total_final, $envio];
    
    // Agregar descuento si la columna existe
    if (in_array('descuento', $columnas_existentes)) {
        $campos_update[] = "descuento = ?";
        $tipos_params .= "i";
        $valores_params[] = $descuento;
    }
    
    // Agregar subtotal si la columna existe
    if (in_array('subtotal', $columnas_existentes)) {
        $campos_update[] = "subtotal = ?";
        $tipos_params .= "d";
        $valores_params[] = $subtotal_calculado;
    }
    
    // Agregar fecha_actualizacion si la columna existe
    if (in_array('fecha_actualizacion', $columnas_existentes)) {
        $campos_update[] = "fecha_actualizacion = NOW()";
    }
    
    $valores_params[] = $id_venta;
    $tipos_params .= "i";
    
    $sql_update_venta = "UPDATE ventas SET " . implode(", ", $campos_update) . " WHERE id = ?";
    
    $stmt_update_venta = $conn->prepare($sql_update_venta);
    $stmt_update_venta->bind_param($tipos_params, ...$valores_params);
    
    $stmt_update_venta->execute();
    
    // Confirmar transacción
    $conn->commit();
    
    // Redirigir con éxito
    header('Location: index.php?alert=success&msg=Venta+actualizada+correctamente');
    exit;
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    
    // Log del error (opcional)
    error_log("Error al actualizar venta ID {$id_venta}: " . $e->getMessage());
    
    // Redirigir con error
    $error_msg = urlencode($e->getMessage());
    header("Location: editar_venta.php?id={$id_venta}&alert=error&msg={$error_msg}");
    exit;
    
} finally {
    // Cerrar conexión
    $conn->close();
}
?>  