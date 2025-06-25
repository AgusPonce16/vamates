<?php
include '../config/config.php';

$id = $_GET['id'];
$sql = "SELECT precio FROM productos WHERE id = $id";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    echo json_encode(['precio' => $row['precio']]);
} else {
    echo json_encode(['precio' => 0]);
}
?>
