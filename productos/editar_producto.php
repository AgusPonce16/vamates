<?php 
include '../includes/header.php';
include '../config/config.php';

// Verificar que se reciba el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id_producto = intval($_GET['id']);

// Obtener datos del producto
$sql_producto = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql_producto);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();

if (!$producto) {
    header('Location: index.php?alert=error');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates/assets/css/styles.css">
    <link rel="stylesheet" href="/vamates/assets/css/editar/edit.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="edit-container">
    <div class="edit-form">
        <div class="edit-header-section">
            <h2><i class="fas fa-box"></i> Editar Producto #<?= $producto['id'] ?></h2>
            <a href="index.php" class="edit-btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="edit-info-box">
            <h4>Información Actual del Producto</h4>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($producto['nombre']) ?></p>
            <p><strong>Precio Compra:</strong> $<?= number_format($producto['precio_compra'], 2, ',', '.') ?></p>
            <p><strong>Precio Venta:</strong> $<?= number_format($producto['precio'], 2, ',', '.') ?></p>
            <p><strong>Stock Actual:</strong> 
                <span class="edit-badge <?= 
                    $producto['stock'] == 0 ? 'edit-badge-stock-critico' : 
                    ($producto['stock'] == 1 ? 'edit-badge-stock-bajo' : 'edit-badge-stock-normal') 
                ?>">
                    <?= $producto['stock'] ?>
                </span>
            </p>
            <p><strong>Estado:</strong> <?= ucfirst($producto['estado']) ?></p>
        </div>
        
        <form id="formEditarProducto" action="actualizar_producto.php" method="post" class="edit-form-container">
            <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
            
            <div class="edit-form-group">
                <label for="nombre">Nombre del Producto</label>
                <input class="edit-control" type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="precio_compra">Precio de Compra</label>
                <input class="edit-control" type="number" step="0.01" name="precio_compra" id="precio_compra" 
                        value="<?= $producto['precio_compra'] ?>" min="0.01" required>
            </div>
            
            <div class="edit-form-group">
                <label for="precio">Precio de Venta</label>
                <input class="edit-control" type="number" step="0.01" name="precio" id="precio" 
                        value="<?= $producto['precio'] ?>" min="0.01" required>
            </div>
            
            <div class="edit-form-group">
                <label for="stock">Stock</label>
                <input class="edit-control" type="number" name="stock" id="stock" 
                        value="<?= $producto['stock'] ?>" min="0" required>
            </div>
            
            <div class="edit-form-group">
                <label for="estado">Estado</label>
                <select name="estado" class="edit-control" required>
                    <option value="activo" <?= $producto['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="desactivado" <?= $producto['estado'] == 'desactivado' ? 'selected' : '' ?>>Desactivado</option>
                </select>
            </div>
            
            <button type="submit" class="edit-btn-submit">
                <i class="fas fa-save"></i> Actualizar Producto
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    document.getElementById("formEditarProducto").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const precioCompra = parseFloat(document.getElementById("precio_compra").value);
        const precioVenta = parseFloat(document.getElementById("precio").value);
        
        // Validar que el precio de venta sea mayor al de compra
        if (precioVenta <= precioCompra) {
            swal("Error", "El precio de venta debe ser mayor al precio de compra", "error");
            return;
        }
        
        // Validar stock no negativo
        if (parseInt(document.getElementById("stock").value) < 0) {
            swal("Error", "El stock no puede ser negativo", "error");
            return;
        }
        
        // Deshabilitar el botón para evitar múltiples clics
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
        
        // Enviar el formulario
        this.submit();
    });
    
    // Validar precios en tiempo real
    document.getElementById("precio_compra").addEventListener("input", validarPrecios);
    document.getElementById("precio").addEventListener("input", validarPrecios);
});

function validarPrecios() {
    const precioCompra = parseFloat(document.getElementById("precio_compra").value) || 0;
    const precioVenta = parseFloat(document.getElementById("precio").value) || 0;
    
    if (precioVenta > 0 && precioVenta <= precioCompra) {
        document.getElementById("precio").classList.add("edit-error");
    } else {
        document.getElementById("precio").classList.remove("edit-error");
    }
}
</script>

<?php $conn->close(); ?>
</body>
</html>