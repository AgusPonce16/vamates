<?php
header('Content-Type: application/json');
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer datos JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['estado'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }

    $compra_id = intval($input['id']);
    $nuevo_estado = $input['estado'];

    // Validar estado
    if (!in_array($nuevo_estado, ['pendiente', 'pagada', 'cancelada'])) {
        echo json_encode(['success' => false, 'message' => 'Estado inválido']);
        exit;
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // 1. Obtener estado actual de la compra
        $sql = "SELECT estado FROM compras WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $compra_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Compra no encontrada');
        }
        
        $compra = $result->fetch_assoc();
        $estado_anterior = $compra['estado'];
        
        // Si no hay cambio de estado, no hacer nada
        if ($estado_anterior === $nuevo_estado) {
            echo json_encode(['success' => true, 'message' => 'Estado sin cambios']);
            exit;
        }
        
        // 2. Obtener productos de la compra para manejar stock
        $sql_productos = "SELECT id_producto, cantidad FROM detalle_compras WHERE id_compra = ?";
        $stmt_productos = $conn->prepare($sql_productos);
        $stmt_productos->bind_param("i", $compra_id);
        $stmt_productos->execute();
        $productos = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);

        
        // 3. Manejar stock según cambio de estado
        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $cantidad = $producto['cantidad'];
            
            // Lógica de stock:
            // - Si estaba 'pagada' y pasa a 'pendiente' o 'cancelada': RESTAR stock
            // - Si estaba 'pendiente' o 'cancelada' y pasa a 'pagada': SUMAR stock
            // - Entre 'pendiente' y 'cancelada': NO cambia stock
            
            if ($estado_anterior === 'pagada' && ($nuevo_estado === 'pendiente' || $nuevo_estado === 'cancelada')) {
                // Restar stock (revertir suma anterior)
                $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt_stock = $conn->prepare($sql_stock);
                $stmt_stock->bind_param("ii", $cantidad, $id_producto);
                $stmt_stock->execute();
                
            } elseif (($estado_anterior === 'pendiente' || $estado_anterior === 'cancelada') && $nuevo_estado === 'pagada') {
                // Sumar stock
                $sql_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
                $stmt_stock = $conn->prepare($sql_stock);
                $stmt_stock->bind_param("ii", $cantidad, $id_producto);
                $stmt_stock->execute();
            }
            // Si cambia entre 'pendiente' y 'cancelada', no toca el stock
            
        }
        
        // 4. Actualizar estado de la compra
        $sql_update = "UPDATE compras SET estado = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $nuevo_estado, $compra_id);
        
        if (!$stmt_update->execute()) {
            throw new Exception('Error al actualizar estado de la compra');
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Estado actualizado correctamente',
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => $nuevo_estado
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción
        $conn->rollback();
        
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conn->close();
?>