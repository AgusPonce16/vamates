<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_compra = intval($_POST['id_compra']);
    $fecha = $_POST['fecha'];
    $id_proveedor = $_POST['id_proveedor'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];
    $monto = floatval($_POST['monto']);
    $productosJSON = $_POST['productosJSON'];
    $ajuste = isset($_POST['ajuste']) ? floatval($_POST['ajuste']) : 0; // Capturar el ajuste
    
    // Validar datos
    if (empty($fecha) || empty($id_proveedor) || empty($estado) || empty($productosJSON)) {
        header('Location: editar_compra.php?id=' . $id_compra . '&alert=error');
        exit;
    }
    
    $productos = json_decode($productosJSON, true);
    if (empty($productos)) {
        header('Location: editar_compra.php?id=' . $id_compra . '&alert=error');
        exit;
    }
    
    // Calcular monto final con ajuste
    $monto_final = $monto + $ajuste;

    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // 1. Obtener estado actual y productos actuales
        $sql_compra_actual = "SELECT estado FROM compras WHERE id = ?";
        $stmt = $conn->prepare($sql_compra_actual);
        $stmt->bind_param("i", $id_compra);
        $stmt->execute();
        $result = $stmt->get_result();
        $compra_actual = $result->fetch_assoc();
        $estado_anterior = $compra_actual['estado'];
        
        // 2. Obtener productos actuales para manejar stock
        $sql_productos_actuales = "SELECT id_producto, cantidad 
                                    FROM detalle_compras 
                                    WHERE id_compra = ?";
        $stmt = $conn->prepare($sql_productos_actuales);
        $stmt->bind_param("i", $id_compra);
        $stmt->execute();
        $productos_actuales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // 3. Si el estado anterior era 'pagada', revertir el stock
        if ($estado_anterior === 'pagada') {
            foreach ($productos_actuales as $prod_actual) {
                $sql_revertir = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt_revertir = $conn->prepare($sql_revertir);
                $stmt_revertir->bind_param("ii", $prod_actual['cantidad'], $prod_actual['id_producto']);
                $stmt_revertir->execute();
            }
        }

        // 4. Actualizar la compra principal con el monto final (incluyendo ajuste)
        $sql_compra = "UPDATE compras SET 
                        fecha = ?, 
                        descripcion = ?, 
                        monto = ?, 
                        id_proveedor = ?, 
                        estado = ? 
                        WHERE id = ?";
        $stmt_compra = $conn->prepare($sql_compra);
        $stmt_compra->bind_param("ssdisi", $fecha, $descripcion, $monto_final, $id_proveedor, $estado, $id_compra);
        
        if (!$stmt_compra->execute()) {
            throw new Exception("Error al actualizar la compra");
        }
        
        // 5. Eliminar detalles existentes
        $sql_delete_detalles = "DELETE FROM detalle_compras WHERE id_compra = ?";
        $stmt_delete = $conn->prepare($sql_delete_detalles);
        $stmt_delete->bind_param("i", $id_compra);
        $stmt_delete->execute();
        
        // 6. Insertar nuevos detalles
        $sql_detalle = "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) 
                VALUES (?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        
        foreach ($productos as $producto) {
            $id_producto = intval($producto['id']);
            $cantidad = intval($producto['cantidad']);
            $precio = floatval($producto['precio']);
            
            // Insertar detalle
            $stmt_detalle->bind_param("iiid", $id_compra, $id_producto, $cantidad, $precio);
            if (!$stmt_detalle->execute()) {
                throw new Exception("Error al insertar detalle del producto");
            }
            
            // 7. Solo sumar stock si el nuevo estado es 'pagada'
            if ($estado === 'pagada') {
                $sql_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
                $stmt_stock = $conn->prepare($sql_stock);
                $stmt_stock->bind_param("ii", $cantidad, $id_producto);
                if (!$stmt_stock->execute()) {
                    throw new Exception("Error al actualizar stock del producto");
                }
            }
        }
        
        // Confirmar transacción
        $conn->commit();
        
        // Redirigir con éxito
        header('Location: index.php?alert=updated');
        exit;
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        
        // Log del error (opcional)
        error_log("Error en actualizar_compra.php: " . $e->getMessage());
        
        // Redirigir con error
        header('Location: editar_compra.php?id=' . $id_compra . '&alert=error');
        exit;
    }
} else {
    // Si no es POST, redirigir al índice
    header('Location: index.php');
    exit;
}

$conn->close();
?>