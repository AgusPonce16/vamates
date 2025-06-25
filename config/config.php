<?php
// config.php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'mates_ventas';

// Conexión con MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
