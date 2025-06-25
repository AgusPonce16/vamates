<?php
include '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $contraseña = $_POST['contraseña'];

    $query = "SELECT id, nombre, contraseña, es_admin FROM usuarios WHERE nombre = '$nombre' LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            header("Location: ../dashboard/index.php"); 
            exit();
        }
    }

    header("Location: login.php?error=1");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>