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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates/assets/css/editar/edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="edit-container">
    <div class="edit-form">
        <div class="edit-header-section">
            <h2><i class="fas fa-truck"></i> Editar Proveedor #<?= $proveedor['id'] ?></h2>
            <a href="index.php" class="edit-btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (isset($_GET['alert']) && $_GET['alert'] == 'error'): ?>
            <div class="edit-alert edit-alert-error">
                Error al actualizar el proveedor. Por favor, verifica los datos.
            </div>
        <?php endif; ?>

        <div class="edit-info-box">
            <h4>Información Actual del Proveedor</h4>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($proveedor['nombre']) ?></p>
            <p><strong>Detalle:</strong> <?= htmlspecialchars($proveedor['detalle']) ?></p>
        </div>
        
        <form action="editar_proveedor.php?id=<?= $id_proveedor ?>" method="post">
            <div class="edit-form-group">
                <label for="nombre">Nombre</label>
                <input class="edit-control" type="text" name="nombre" id="nombre" 
                        value="<?= htmlspecialchars($proveedor['nombre']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="detalle">Detalle</label>
                <textarea class="edit-control" name="detalle" id="detalle" rows="3"><?= htmlspecialchars($proveedor['detalle']) ?></textarea>
            </div>
            
            <button type="submit" class="edit-btn-submit">
                <i class="fas fa-save"></i> Actualizar Proveedor
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Interceptar envío del formulario para validación
    document.querySelector('form').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        
        if (nombre === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El nombre del proveedor es requerido',
                confirmButtonColor: '#8e44ad'
            });
            return;
        }
        
        // Deshabilitar el botón para evitar múltiples clics
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    });
});
</script>

<?php 
$conn->close();
?>
</body>
</html>