<?php
include '../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$nuevoEstado = $data['estado'];

try {
    $conn->begin_transaction();
    
    // 1. Obtener el estado actual y los detalles de la venta
    $sql = "SELECT v.estado, dv.producto_id, dv.cantidad, dv.subtotal 
            FROM ventas v
            JOIN detalle_ventas dv ON v.id = dv.venta_id
            WHERE v.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $detalles = [];
    $estadoActual = '';
    
    if ($row = $result->fetch_assoc()) {
        $estadoActual = $row['estado'];
        $detalles[] = $row;
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
    }
    
    // 2. Actualizar el estado de la venta
    $sql = "UPDATE ventas SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevoEstado, $id);
    $stmt->execute();
    
    // 3. Manejar los cambios de stock según los estados
    if ($estadoActual == 'pagada' && ($nuevoEstado == 'pendiente' || $nuevoEstado == 'cancelada')) {
        // Si estaba pagada y pasa a pendiente/cancelada: sumar al stock
        foreach ($detalles as $detalle) {
            $sql = "UPDATE productos SET stock = stock + ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $detalle['cantidad'], $detalle['producto_id']);
            $stmt->execute();
        }
    } elseif (($estadoActual == 'pendiente' || $estadoActual == 'cancelada') && $nuevoEstado == 'pagada') {
        // Si estaba pendiente/cancelada y pasa a pagada: restar del stock
        foreach ($detalles as $detalle) {
            $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $detalle['cantidad'], $detalle['producto_id']);
            $stmt->execute();
        }
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>