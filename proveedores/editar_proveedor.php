<?php
include '../includes/header.php';
include '../config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id_proveedor = intval($_GET['id']);

// Obtener datos del proveedor
$sql = "SELECT * FROM proveedores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_proveedor);
$stmt->execute();
$proveedor = $stmt->get_result()->fetch_assoc();

if (!$proveedor) {
    header('Location: index.php?alert=error');
    exit;
}

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $detalle = trim($_POST['detalle']);
    
    if (empty($nombre)) {
        header("Location: editar_proveedor.php?id=$id_proveedor&alert=error");
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE proveedores SET nombre = ?, detalle = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $detalle, $id_proveedor);
    
    if ($stmt->execute()) {
        header("Location: index.php?alert=updated");
    } else {
        header("Location: editar_proveedor.php?id=$id_proveedor&alert=error");
    }
    exit;
}
?>

<!-- Mantener el mismo estilo del index.php -->
<div class="container">
    <div class="column left" style="max-width: 800px; margin: 0 auto;">
        <div class="header-section">
            <h2>Editar Proveedor #<?= $proveedor['id'] ?></h2>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <form action="editar_proveedor.php?id=<?= $id_proveedor ?>" method="post">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input class="control" type="text" name="nombre" id="nombre" 
                        value="<?= htmlspecialchars($proveedor['nombre']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="detalle">Detalle</label>
                <textarea class="control" name="detalle" id="detalle" rows="3"><?= htmlspecialchars($proveedor['detalle']) ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Actualizar Proveedor
            </button>
        </form>
    </div>
</div>

<?php 
$conn->close();
?>