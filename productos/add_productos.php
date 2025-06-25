<?php

include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario como arrays
    $nombres = $_POST['nombre'] ?? [];
    $precios_compra = $_POST['precio_compra'] ?? [];
    $precios_venta = $_POST['precio'] ?? [];
    $stocks = $_POST['stock'] ?? [];

    for ($i = 0; $i < count($nombres); $i++) {
        $nombre = trim($nombres[$i]);
        $precio_compra = floatval($precios_compra[$i]);
        $precio_venta = floatval($precios_venta[$i]);
        $stock = intval($stocks[$i]);
        $diferencia = $precio_venta - $precio_compra;

        // Validación básica
        if ($nombre && $precio_compra > 0 && $precio_venta > 0 && $stock >= 0) {
            $sql = "INSERT INTO productos (nombre, precio_compra, precio, stock, diferencia) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sddid", $nombre, $precio_compra, $precio_venta, $stock, $diferencia);
            $stmt->execute();
        }
    }

    header("Location: index.php?alert=added");
    exit;
}
$conn->close();
?>



