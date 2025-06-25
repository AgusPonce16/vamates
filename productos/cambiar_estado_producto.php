<?php
include '../config/config.php';

// Verificar que los datos están presentes
if (!isset($_POST['id']) || !isset($_POST['estado'])) {
    die(json_encode(['success' => false, 'error' => 'Datos incompletos']));
}

$id = intval($_POST['id']);
$estado = $_POST['estado'];

// Validar que el estado es uno de los valores permitidos
if (!in_array($estado, ['activo', 'desactivado'])) {
    die(json_encode(['success' => false, 'error' => 'Estado no válido']));
}

try {
    $sql = "UPDATE productos SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // Para ENUM  's' (string) i es (integer)
    $stmt->bind_param("si", $estado, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>