<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $id_proveedor = $_POST['id_proveedor'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $estado = strtolower(trim($_POST['estado']));
    $productos = json_decode($_POST['productosJSON'], true);
    $recargo = isset($_POST['recargo']) ? floatval($_POST['recargo']) : 0;
    $ajuste = isset($_POST['ajuste']) ? floatval($_POST['ajuste']) : 0;

    
    // Validar estado
    if (!in_array($estado, ['pendiente', 'pagada'])) {
        header("Location: index.php?alert=error&message=Estado no válido");
        exit;
    }

    try {
        $conn->begin_transaction();
        
        // 1. Insertar la compra principal
        $sql_compra = "INSERT INTO compras (fecha, descripcion, monto, estado, id_proveedor) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt_compra = $conn->prepare($sql_compra);
        $stmt_compra->bind_param("ssdsi", $fecha, $descripcion, $monto, $estado, $id_proveedor);
        $stmt_compra->execute();
        $id_compra = $conn->insert_id;
        
        // 2. Insertar detalles de compra
        // El trigger se encargará automáticamente del stock si es necesario
        foreach ($productos as $producto) {
            $sql_detalle = "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) 
                            VALUES (?, ?, ?, ?)";
            $stmt_detalle = $conn->prepare($sql_detalle);
            $stmt_detalle->bind_param("iidd", $id_compra, $producto['id'], $producto['cantidad'], $producto['precio']);
            $stmt_detalle->execute();
        }

        $conn->commit();
        header("Location: index.php?alert=added");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error en compras: " . $e->getMessage());
        header("Location: index.php?alert=error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>