<?php
include '../config/config.php';

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['venta_id'])) {
    header('Location: index.php?alert=error&message=Datos incompletos');
    exit();
}

$venta_id = intval($_POST['venta_id']);
$fecha = $_POST['fecha'];
$estado = $_POST['estado'];
$envio = floatval($_POST['envio']);
$total = floatval($_POST['total']);

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // 1. Obtener los productos originales de la venta antes de cualquier cambio
    $sql_original = "SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?";
    $stmt_original = $conn->prepare($sql_original);
    $stmt_original->bind_param("i", $venta_id);
    $stmt_original->execute();
    $result_original = $stmt_original->get_result();
    $productos_originales = $result_original->fetch_all(MYSQLI_ASSOC);
    
    // 2. Eliminar todos los detalles de la venta actual (los volveremos a insertar)
    $sql_delete = "DELETE FROM detalle_ventas WHERE venta_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $venta_id);
    $stmt_delete->execute();
    
    // 3. Actualizar información básica de la venta
    $sql_update = "UPDATE ventas SET fecha = ?, estado = ?, envio = ?, total = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssddi", $fecha, $estado, $envio, $total, $venta_id);
    $stmt_update->execute();
    
    // 4. Procesar los productos del formulario
    $productos_ids = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $descuentos = $_POST['descuento_producto'];
    
    // Preparar consultas
    $sql_insert_detalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, descuento, subtotal) 
                          VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_detalle = $conn->prepare($sql_insert_detalle);
    
    $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
    $stmt_update_stock = $conn->prepare($sql_update_stock);
    
    $sql_restore_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
    $stmt_restore_stock = $conn->prepare($sql_restore_stock);
    
    // 5. Restaurar stock de productos eliminados
    $productos_actuales_ids = array_map('intval', $productos_ids);
    
    foreach ($productos_originales as $original) {
        if (!in_array($original['producto_id'], $productos_actuales_ids)) {
            // Producto fue eliminado de la venta - restaurar stock
            $stmt_restore_stock->bind_param("ii", $original['cantidad'], $original['producto_id']);
            $stmt_restore_stock->execute();
        }
    }
    
    // 6. Procesar productos actuales
    for ($i = 0; $i < count($productos_ids); $i++) {
        $producto_id = intval($productos_ids[$i]);
        $cantidad = intval($cantidades[$i]);
        $descuento = floatval($descuentos[$i]);
        
        // Obtener precio del producto
        $sql_precio = "SELECT precio FROM productos WHERE id = ?";
        $stmt_precio = $conn->prepare($sql_precio);
        $stmt_precio->bind_param("i", $producto_id);
        $stmt_precio->execute();
        $result_precio = $stmt_precio->get_result();
        $precio = $result_precio->fetch_assoc()['precio'];
        
        // Calcular subtotal con descuento
        $subtotal_sin_descuento = $precio * $cantidad;
        $monto_descuento = $subtotal_sin_descuento * ($descuento / 100);
        $subtotal = $subtotal_sin_descuento - $monto_descuento;
        
        // Insertar detalle de venta
        $stmt_insert_detalle->bind_param("iiidd", $venta_id, $producto_id, $cantidad, $descuento, $subtotal);
        $stmt_insert_detalle->execute();
        
        // Verificar si el producto estaba en la venta original
        $producto_original = null;
        foreach ($productos_originales as $original) {
            if ($original['producto_id'] == $producto_id) {
                $producto_original = $original;
                break;
            }
        }
        
        if ($producto_original) {
            // Producto existente - ajustar stock según diferencia
            $diferencia = $cantidad - $producto_original['cantidad'];
            if ($diferencia != 0) {
                if ($diferencia > 0) {
                    // Se aumentó la cantidad - restar diferencia del stock
                    $stmt_update_stock->bind_param("ii", $diferencia, $producto_id);
                } else {
                    // Se disminuyó la cantidad - sumar diferencia al stock
                    $diferencia_abs = abs($diferencia);
                    $stmt_restore_stock->bind_param("ii", $diferencia_abs, $producto_id);
                }
                $stmt_update_stock->execute();
            }
        } else {
            // Producto nuevo - restar cantidad del stock
            $stmt_update_stock->bind_param("ii", $cantidad, $producto_id);
            $stmt_update_stock->execute();
        }
    }
    
    // Confirmar todas las operaciones
    $conn->commit();
    
    header("Location: index.php?alert=added&message=Venta actualizada correctamente");
    exit();
    
} catch (Exception $e) {
    // Revertir todas las operaciones en caso de error
    $conn->rollback();
    
    // Registrar el error
    error_log("Error al actualizar venta: " . $e->getMessage());
    
    header("Location: index.php?alert=error&message=Error al actualizar la venta");
    exit();
}