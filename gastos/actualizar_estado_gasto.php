<?php
header('Content-Type: application/json');

include '../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$estado = $data['estado'];

// Validar estado
$estadosPermitidos = ['pendiente', 'pagada', 'cancelada'];
if (!in_array($estado, $estadosPermitidos)) {
    echo json_encode(['success' => false, 'message' => 'Estado no válido']);
    exit;
}

// Actualizar en la base de datos
$sql = "UPDATE gastos SET estado = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos']);
}

$stmt->close();
$conn->close();
?>