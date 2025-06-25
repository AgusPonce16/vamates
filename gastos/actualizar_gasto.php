<?php
include '../config/config.php';

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha']; // Formato YYYY-MM-DD
    

    // Consulta para actualizar la compra en la base de datos
    $sql = "UPDATE gastos SET descripcion = ?, monto = ?, fecha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsi", $descripcion, $monto, $fecha, $id);

    if ($stmt->execute()) {
        // Redirigir a index.php con un mensaje de éxito en la URL
        header("Location: index.php?alert=updated");
        exit(); 
    } else {
        echo "Error al actualizar la compra: " . $stmt->error;
    }
}

$conn->close();
?>


