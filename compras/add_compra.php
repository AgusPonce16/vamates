<?php
include '../config/config.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?alert=error");
    exit();
}

// Obtener y validar datos del formulario
$fecha = $_POST['fecha'] ?? '';
$id_proveedor = intval($_POST['id_proveedor'] ?? 0);
$estado = $_POST['estado'] ?? 'pendiente';
$productosJSON = $_POST['productosJSON'] ?? '[]';
$descripcion = $_POST['descripcion'] ?? '';
$monto = floatval($_POST['monto'] ?? 0);
$ajuste = floatval($_POST['ajuste'] ?? 0);

// Después de obtener los datos del POST
$descripcion = trim($_POST['descripcion'] ?? '');

if (empty($descripcion)) {
    // Intentar crear una descripción básica si está vacía
    $productos = json_decode($productosJSON, true);
    $nombres = array_map(function($p) {
        return ($p['cantidad'] ?? 0) . 'x ' . ($p['nombre'] ?? 'Producto');
    }, $productos);
    $descripcion = implode(', ', $nombres);
    
    if (empty($descripcion)) {
        $descripcion = "Compra del " . date('d/m/Y', strtotime($fecha));
    }
}

// Validaciones básicas
if (empty($fecha)) {
    header("Location: index.php?alert=error&message=Fecha requerida");
    exit();
}

if ($id_proveedor <= 0) {
    header("Location: index.php?alert=error&message=Proveedor requerido");
    exit();
}

if ($monto <= 0) {
    header("Location: index.php?alert=error&message=Monto inválido");
    exit();
}

// Decodificar productos
$productos = json_decode($productosJSON, true);
if (json_last_error() !== JSON_ERROR_NONE || empty($productos)) {
    header("Location: index.php?alert=error&message=Productos inválidos");
    exit();
}

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // Insertar la compra
    $stmt = $conn->prepare("INSERT INTO compras (fecha, id_proveedor, descripcion, monto, estado, ajuste) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisdsd", $fecha, $id_proveedor, $descripcion, $monto, $estado, $ajuste);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al guardar la compra: " . $stmt->error);
    }
    
    $id_compra = $stmt->insert_id;
    $stmt->close();
    
    // Insertar los productos de la compra
    $stmt = $conn->prepare("INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    
    foreach ($productos as $producto) {
        $id_producto = intval($producto['id']);
        $cantidad = intval($producto['cantidad']);
        $precio_unitario = floatval($producto['precio']);
        
        $stmt->bind_param("iiid", $id_compra, $id_producto, $cantidad, $precio_unitario);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al guardar productos de la compra: " . $stmt->error);
        }
        
        // Actualizar el stock del producto si la compra está pagada
        if ($estado === 'pagada') {
            $update = $conn->query("UPDATE productos SET stock = stock + $cantidad WHERE id = $id_producto");
            if (!$update) {
                throw new Exception("Error al actualizar stock: " . $conn->error);
            }
        }
    }
    
    $stmt->close();
    
    // Confirmar la transacción
    $conn->commit();
    
    // Redirigir con mensaje de éxito
    header("Location: index.php?alert=added");
    exit();
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    
    // Registrar el error para depuración
    error_log("Error en add_compra.php: " . $e->getMessage());
    
    // Redirigir con mensaje de error
    header("Location: index.php?alert=error&message=" . urlencode($e->getMessage()));
    exit();
}