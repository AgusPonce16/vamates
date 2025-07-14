<?php
include '../config/config.php';

// Obtener datos del formulario
$fecha = $_POST['fecha'];
$estado = $_POST['estado'];
$envio = $_POST['envio'] ?? 0;
$total = $_POST['total'];
$productos = $_POST['producto_id'];
$cantidades = $_POST['cantidad'];
$precio_unitario = $_POST['precio_unitario'];
$descuentos = $_POST['descuento_producto'] ?? array_fill(0, count($productos), 0);

// Verificar stock antes de comenzar la transacción
foreach ($productos as $i => $producto_id) {
    $cantidad = $cantidades[$i];
    $sql = "SELECT stock FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    
    if ($producto['stock'] < $cantidad && $estado == 'pagada') {
        header("Location: index.php?error=sin_stock");
        exit();
    }
}

try {
    $conn->begin_transaction();
    
    // 1. Insertar la venta
    $sql = "INSERT INTO ventas (fecha, estado, envio, total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdd", $fecha, $estado, $envio, $total);
    $stmt->execute();
    $venta_id = $conn->insert_id;
    
    // 2. Insertar detalles de venta y actualizar stock si es pagada
    for ($i = 0; $i < count($productos); $i++) {
        $producto_id = $productos[$i];
        $cantidad = $cantidades[$i];
        $descuento = $descuentos[$i];
        
        // Obtener precio del producto
        $sql = "SELECT precio FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        
        $precio = $producto['precio'];
        $subtotal = $precio * $cantidad * (1 - $descuento / 100);
        
        $sql = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, descuento, subtotal, precio_unitario) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiddd", $venta_id, $producto_id, $cantidad, $descuento, $subtotal, $precio);
        $stmt->execute();

        
        // Actualizar stock solo si la venta es pagada
        if ($estado == 'pagada') {
            $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $cantidad, $producto_id);
            $stmt->execute();
        }
    }
    
    $conn->commit();
    header("Location: index.php?alert=added");
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en venta: " . $e->getMessage());
    header("Location: index.php?alert=error&message=" . urlencode("Error: " . $e->getMessage()));
    exit();
}

$conn->close();
?>