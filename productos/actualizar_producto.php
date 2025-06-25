<?php
include '../config/config.php';

// Verificar que se reciban los datos
if (!isset($_POST['id_producto']) || !isset($_POST['nombre']) || !isset($_POST['precio_compra']) || 
    !isset($_POST['precio']) || !isset($_POST['stock']) || !isset($_POST['estado'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id = intval($_POST['id_producto']);
$nombre = trim($_POST['nombre']);
$precio_compra = floatval($_POST['precio_compra']);
$precio = floatval($_POST['precio']);
$stock = intval($_POST['stock']);
$estado = $_POST['estado'];

// Validaciones adicionales
if ($precio <= $precio_compra) {
    header("Location: editar_producto.php?id=$id&alert=error_precio");
    exit;
}

if ($stock < 0) {
    header("Location: editar_producto.php?id=$id&alert=error_stock");
    exit;
}

// Validar que el estado sea uno de los permitidos
if (!in_array($estado, ['activo', 'desactivado'])) {
    header("Location: editar_producto.php?id=$id&alert=error_estado");
    exit;
}

// Actualizar en la base de datos
$sql = "UPDATE productos SET 
        nombre = ?, 
        precio_compra = ?, 
        precio = ?, 
        stock = ?, 
        estado = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sdddsi", $nombre, $precio_compra, $precio, $stock, $estado, $id);

if ($stmt->execute()) {
    header("Location: index.php?alert=updated");
} else {
    header("Location: editar_producto.php?id=$id&alert=error");
}

$stmt->close();
$conn->close();
?>