<?php
include '../config/config.php';

$nombre = $_POST['nombre'];
$precio_compra = $_POST['precio_compra'];
$precio_venta = $_POST['precio_venta'];
$stock = $_POST['stock'];
$producto_ids = $_POST['producto_id'] ?? [];

if (empty($producto_ids)) {
    die('Debe seleccionar al menos un producto.');
}

// Insertar combo
$query = "INSERT INTO combos (nombre, precio_compra, precio_venta, stock, activo) 
            VALUES (?, ?, ?, ?, 1)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sddi", $nombre, $precio_compra, $precio_venta, $stock);
$stmt->execute();
$id_combo = $stmt->insert_id;

// Insertar productos del combo
foreach ($producto_ids as $id_producto) {
    $cantidad_key = 'cantidad_producto_' . $id_producto;
    $cantidad = intval($_POST[$cantidad_key] ?? 1);
    if ($cantidad > 0) {
        $stmt = $conn->prepare("INSERT INTO combo_productos (id_combo, id_producto, cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_combo, $id_producto, $cantidad);
        $stmt->execute();
    }
}

header("Location: index.php?combo_agregado=1");
?>
