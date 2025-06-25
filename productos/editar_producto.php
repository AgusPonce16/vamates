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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates3/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>

<style>
    :root {
        --primary-color: #8e44ad;
        --secondary-color: #9b59b6;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --success-color: #2ecc71;
        --light-color: #f5f5f5;
        --dark-color: #333;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
    }

    .edit-form {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--box-shadow);
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-color);
    }

    h2 {
        color: var(--dark-color);
        margin: 0;
        font-weight: 500;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s;
    }

    .btn-back:hover {
        background-color: #5a6268;
        color: white;
        text-decoration: none;
    }

    .control {
        width: 100%;
        padding: 10px 15px;
        margin: 8px 0 15px;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        box-sizing: border-box;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
    }

    .form-container {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 25px;
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s;
        width: 100%;
    }

    .btn-submit:hover {
        background-color: var(--secondary-color);
    }

    .info-producto {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 20px;
    }

    .info-producto h4 {
        margin: 0 0 10px 0;
        color: #1976d2;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-stock-critico {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .badge-stock-bajo {
        background-color: #fff3e0;
        color: #e65100;
        border: 1px solid #ffcc02;
    }

    .badge-stock-normal {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
</style>

<div class="container">
    <div class="edit-form">
        <div class="header-section">
            <h2>Editar Producto #<?= $producto['id'] ?></h2>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="info-producto">
            <h4>Información Actual del Producto</h4>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($producto['nombre']) ?></p>
            <p><strong>Precio Compra:</strong> $<?= number_format($producto['precio_compra'], 2, ',', '.') ?></p>
            <p><strong>Precio Venta:</strong> $<?= number_format($producto['precio'], 2, ',', '.') ?></p>
            <p><strong>Stock Actual:</strong> 
                <span class="badge <?= 
                    $producto['stock'] == 0 ? 'badge-stock-critico' : 
                    ($producto['stock'] == 1 ? 'badge-stock-bajo' : 'badge-stock-normal') 
                ?>">
                    <?= $producto['stock'] ?>
                </span>
            </p>
            <p><strong>Estado:</strong> <?= ucfirst($producto['estado']) ?></p>
        </div>
        
        <form id="formEditarProducto" action="actualizar_producto.php" method="post" class="form-container">
            <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
            
            <label for="nombre">Nombre del Producto</label>
            <input class="control" type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            
            <label for="precio_compra">Precio de Compra</label>
            <input class="control" type="number" step="0.01" name="precio_compra" id="precio_compra" 
                    value="<?= $producto['precio_compra'] ?>" min="0.01" required>
            
            <label for="precio">Precio de Venta</label>
            <input class="control" type="number" step="0.01" name="precio" id="precio" 
                    value="<?= $producto['precio'] ?>" min="0.01" required>
            
            <label for="stock">Stock</label>
            <input class="control" type="number" name="stock" id="stock" 
                    value="<?= $producto['stock'] ?>" min="0" required>
            
            <label for="estado">Estado</label>
            <select name="estado" class="control" required>
                <option value="activo" <?= $producto['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="desactivado" <?= $producto['estado'] == 'desactivado' ? 'selected' : '' ?>>Desactivado</option>
            </select>
            
            <input class="btn-submit" type="submit" value="Actualizar Producto">
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
        submitBtn.value = "Actualizando...";
        
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
        document.getElementById("precio").classList.add("error");
    } else {
        document.getElementById("precio").classList.remove("error");
    }
}
</script>

<?php $conn->close(); ?>