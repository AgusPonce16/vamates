<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fechas = $_POST['fecha'] ?? [];
    $descripciones = $_POST['descripcion'] ?? [];
    $montos = $_POST['monto'] ?? [];
    $tipos = $_POST['tipo'] ?? [];
    $categorias = $_POST['categoria'] ?? [];
    $estados = $_POST['estado'] ?? [];
    
    // Validar que todos los arrays tengan el mismo tamaño
    if (count($fechas) !== count($descripciones) || 
        count($fechas) !== count($montos) || 
        count($fechas) !== count($tipos) || 
        count($fechas) !== count($categorias) || 
        count($fechas) !== count($estados)) {
        header("Location: index.php?alert=error");
        exit;
    }
    
    // Insertar cada gasto en la base de datos
    $success = true;
    $conn->begin_transaction();
    
    try {
        for ($i = 0; $i < count($fechas); $i++) {
            $fecha = $fechas[$i];
            $descripcion = $descripciones[$i];
            $monto = floatval($montos[$i]);
            $tipo = $tipos[$i];
            $categoria = $categorias[$i];
            $estado = $estados[$i];
            
            $stmt = $conn->prepare("INSERT INTO gastos (fecha, descripcion, monto, tipo, categoria, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsss", $fecha, $descripcion, $monto, $tipo, $categoria, $estado);
            
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $conn->commit();
            header("Location: index.php?alert=added");
        } else {
            $conn->rollback();
            header("Location: index.php?alert=error");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: index.php?alert=error");
    }
    
    exit;
}

header("Location: index.php");
?>