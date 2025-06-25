<?php
include '../config/config.php';

$id = $_POST['id'];
$precioCompra = $_POST['precio_compra'];
$precioVenta = $_POST['precio'];

$sql = "UPDATE productos SET precio_compra = ?, precio = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ddi", $precioCompra, $precioVenta, $id);

if ($stmt->execute()) {
    header("Location: index.php?alert=updated");
} else {
    header("Location: index.php?alert=error");
}
