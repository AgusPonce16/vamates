<?php
include '../config/config.php';

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['compra_id'])) {
    header('Location: index.php?alert=error&message=Datos incompletos');
    exit();
}

$compra_id = intval($_POST['compra_id']);
$fecha = $_POST['fecha'];
$id_proveedor = intval($_POST['id_proveedor']);
$estado = $_POST['estado'];
$ajuste = floatval($_POST['ajuste']);
$total = floatval($_POST['total']);
$productos_originales = json_decode($_POST['productos_originales'], true);

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // 1. Actualizar información básica de la compra
    $sql_update = "UPDATE compras SET 
                  fecha = ?, 
                  id_proveedor = ?, 
                  estado = ?, 
                  monto = ?,
                  descripcion = CONCAT('Compra #', id, ' - ', DATE_FORMAT(fecha, '%d/%m/%Y'))
                  WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sisdi", $fecha, $id_proveedor, $estado, $total, $compra_id);
    $stmt_update->execute();
    
    // 2. Obtener los productos actuales del formulario
    $productos_ids = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $precios_unitarios = $_POST['precio_unitario'];
    
    // 3. Eliminar todos los detalles de la compra actual
    $sql_delete = "DELETE FROM detalle_compras WHERE id_compra = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $compra_id);
    $stmt_delete->execute();
    
    // 4. Preparar consultas
    $sql_insert_detalle = "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) 
                          VALUES (?, ?, ?, ?)";
    $stmt_insert_detalle = $conn->prepare($sql_insert_detalle);
    
    $sql_aumentar_stock = "UPDATE productos SET stock = stock + ? WHERE id = ?";
    $stmt_aumentar_stock = $conn->prepare($sql_aumentar_stock);
    
    $sql_reducir_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
    $stmt_reducir_stock = $conn->prepare($sql_reducir_stock);
    
    // 5. Restaurar stock de productos eliminados (RESTAR del stock - porque eran compras)
    foreach ($productos_originales as $original) {
        $encontrado = false;
        for ($i = 0; $i < count($productos_ids); $i++) {
            if ($productos_ids[$i] == $original['id_producto']) {
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado) {
            // Producto eliminado - RESTAR del stock (porque antes se había sumado al comprar)
            $stmt_reducir_stock->bind_param("ii", $original['cantidad'], $original['id_producto']);
            $stmt_reducir_stock->execute();
        }
    }
    
    // 6. Procesar productos actuales
    for ($i = 0; $i < count($productos_ids); $i++) {
        $producto_id = intval($productos_ids[$i]);
        $cantidad = intval($cantidades[$i]);
        $precio_unitario = floatval($precios_unitarios[$i]);
        
        // Insertar detalle de compra
        $stmt_insert_detalle->bind_param("iiid", $compra_id, $producto_id, $cantidad, $precio_unitario);
        $stmt_insert_detalle->execute();
        
        // Buscar en productos originales
        $original_cantidad = 0;
        foreach ($productos_originales as $original) {
            if ($original['id_producto'] == $producto_id) {
                $original_cantidad = $original['cantidad'];
                break;
            }
        }
        
        // Calcular diferencia
        $diferencia = $cantidad - $original_cantidad;
        
        if ($original_cantidad > 0) {
            // Producto existente - ajustar stock según diferencia
            if ($diferencia != 0) {
                if ($diferencia > 0) {
                    // Más cantidad - SUMAR al stock (comprar más)
                    $stmt_aumentar_stock->bind_param("ii", $diferencia, $producto_id);
                } else {
                    // Menos cantidad - RESTAR del stock (devolver)
                    $diferencia_abs = abs($diferencia);
                    $stmt_reducir_stock->bind_param("ii", $diferencia_abs, $producto_id);
                }
                $stmt_aumentar_stock->execute();
            }
        } else {
            // Producto nuevo - SUMAR al stock (comprar)
            $stmt_aumentar_stock->bind_param("ii", $cantidad, $producto_id);
            $stmt_aumentar_stock->execute();
        }
    }
    
    // Confirmar todas las operaciones
    $conn->commit();
    
    header("Location: index.php?alert=updated&message=Compra actualizada correctamente");
    exit();
    
} catch (Exception $e) {
    // Revertir todas las operaciones en caso de error
    $conn->rollback();
    
    error_log("Error al actualizar compra: " . $e->getMessage());
    
    header("Location: index.php?alert=error&message=Error al actualizar la compra: " . $e->getMessage());
    exit();
}