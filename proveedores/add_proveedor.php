<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $detalle = trim($_POST['detalle']);
    
    if (empty($nombre)) {
        header('Location: index.php?alert=error');
        exit;
    }
    
    // Estado por defecto 'activo' para nuevos proveedores
    $stmt = $conn->prepare("INSERT INTO proveedores (nombre, detalle, estado) VALUES (?, ?, 'activo')");
    $stmt->bind_param("ss", $nombre, $detalle);
    
    if ($stmt->execute()) {
        header('Location: index.php?alert=added');
    } else {
        header('Location: index.php?alert=error');
    }
    exit;
}

header('Location: index.php');
?>