<?php
include '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que no haya ya un admin (doble verificación)
    $check = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE es_admin = 1");
    $row = $check->fetch_assoc();
    if ($row['total'] > 0) {
        header("Location: login.php");
        exit();
    }

    // Validar datos
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $contraseña = $_POST['contraseña'];
    $confirmar = $_POST['confirmar_contraseña'];

    if (empty($nombre) || empty($contraseña) || empty($confirmar)) {
        header("Location: login.php?error=campos_vacios");
        exit();
    }

    if ($contraseña !== $confirmar) {
        header("Location: login.php?error=contraseñas_no_coinciden");
        exit();
    }

    if (strlen($contraseña) < 6) {
        header("Location: login.php?error=contraseña_corta");
        exit();
    }

    // Crear hash de contraseña
    $hash = password_hash($contraseña, PASSWORD_BCRYPT);

    // Insertar nuevo admin
    $query = "INSERT INTO usuarios (nombre, contraseña, es_admin) VALUES ('$nombre', '$hash', 1)";
    if ($conn->query($query)) {
        // Iniciar sesión automáticamente
        $_SESSION['usuario_id'] = $conn->insert_id;
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['es_admin'] = 1;
        header("Location: ../dashboard/index.php");; 
        exit();
    } else {
        header("Location: login.php?error=db_error");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>