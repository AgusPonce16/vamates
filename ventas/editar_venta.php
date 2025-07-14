<?php
include '../config/config.php';
include '../includes/header.php';

// Verificar si se recibió el ID de la venta
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error&message=Venta no especificada');
    exit();
}

$venta_id = intval($_GET['id']);

// Obtener información de la venta
$sql_venta = "SELECT * FROM ventas WHERE id = ?";
$stmt_venta = $conn->prepare($sql_venta);
$stmt_venta->bind_param("i", $venta_id);
$stmt_venta->execute();
$result_venta = $stmt_venta->get_result();

if ($result_venta->num_rows === 0) {
    header('Location: index.php?alert=error&message=Venta no encontrada');
    exit();
}

$venta = $result_venta->fetch_assoc();

// Obtener los productos de la venta
$sql_productos = "SELECT dv.*, p.nombre, p.precio, p.stock 
                    FROM detalle_ventas dv 
                    JOIN productos p ON dv.producto_id = p.id 
                    WHERE dv.venta_id = ?";
$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bind_param("i", $venta_id);
$stmt_productos->execute();
$result_productos = $stmt_productos->get_result();
$productos_venta = $result_productos->fetch_all(MYSQLI_ASSOC);

// Obtener todos los productos disponibles (incluyendo stock 0 para productos ya en la venta)
$productos_ids_en_venta = array_column($productos_venta, 'producto_id');
$placeholders = implode(',', array_fill(0, count($productos_ids_en_venta), '?'));
$types = str_repeat('i', count($productos_ids_en_venta));

$sql_productos_disponibles = "SELECT * FROM productos 
                             WHERE stock > 0 AND estado = 'activo' 
                             OR id IN ($placeholders)
                             ORDER BY nombre ASC";
$stmt_productos = $conn->prepare($sql_productos_disponibles);

if (!empty($productos_ids_en_venta)) {
    $stmt_productos->bind_param($types, ...$productos_ids_en_venta);
}

$stmt_productos->execute();
$productos_disponibles = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Venta</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/vamates3/assets/css/editar/edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="edit-container">
    <div class="edit-form">
        <div class="edit-header-section">
            <h2><i class="fas fa-edit"></i> Editar Venta #<?= $venta['id'] ?></h2>
            <a href="index.php" class="edit-btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="edit-info-box">
            <h4>Información de la Venta</h4>
            <p><strong>Fecha original:</strong> <?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
            <p><strong>Total original:</strong> $<?= number_format($venta['total'], 2) ?></p>
            <p><strong>Estado:</strong> <?= ucfirst($venta['estado']) ?></p>
        </div>
        
        <form action="actualizar_venta.php" method="post" id="formVenta">
            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?>">
            
            <div class="edit-form-group">
                <label for="fecha">Fecha</label>
                <input class="edit-control" type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($venta['fecha']) ?>" required>
            </div>
            
            <div class="edit-form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="edit-control" required>
                    <option value="pendiente" <?= $venta['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="pagada" <?= $venta['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                </select>
            </div>
            
            <h3>Productos</h3>
            <div id="productosContainer">
                <?php foreach ($productos_venta as $producto): ?>
                    <div class="edit-producto-item">
                        <div class="edit-producto-grid">
                            <select name="producto_id[]" class="edit-producto-select edit-control" required>
                                <option value="">Selecciona un producto</option>
                                <?php foreach ($productos_disponibles as $prod): ?>
                                    <option value="<?= $prod['id'] ?>" 
                                            data-precio="<?= $prod['precio'] ?>"
                                            data-stock-original="<?= $prod['stock'] ?>"
                                            <?= $prod['id'] == $producto['producto_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prod['nombre']) ?> 
                                        (Stock: <?= $prod['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="number" name="cantidad[]" class="edit-cantidad edit-control" 
                                   placeholder="Cantidad" min="1" 
                                   value="<?= $producto['cantidad'] ?>" required>
                            
                            <select name="descuento_producto[]" class="edit-descuento edit-control">
                                <option value="0">Sin descuento</option>
                                <?php for ($i = 5; $i <= 100; $i += 5): ?>
                                    <option value="<?= $i ?>" <?= $i == $producto['descuento'] ? 'selected' : '' ?>>
                                        <?= $i ?>%
                                    </option>
                                <?php endfor; ?>
                            </select>
                            
                            <input type="text" class="edit-subtotal edit-control" placeholder="Subtotal" readonly
                                   value="<?= number_format($producto['subtotal'], 2) ?>">
                            
                            <button type="button" class="edit-btn-action edit-btn-delete edit-remove-producto">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        
                        <div class="edit-detalle-descuento">
                            <span class="edit-precio-original"></span> 
                            <span class="edit-precio-descuento"></span>
                            <span class="edit-porcentaje-descuento"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" class="edit-btn-agregar edit-add-producto">
                <i class="fas fa-plus"></i> Agregar otro producto
            </button>
            
            <div class="edit-form-group">
                <label for="envio">Envío</label>
                <input type="number" name="envio" id="envio" class="edit-control" 
                        placeholder="Monto del envío" value="<?= $venta['envio'] ?>" min="0" step="0.01">
            </div>
            
            <div class="edit-total-display">
                <strong>Total Venta:</strong> $<span id="total-venta"><?= number_format($venta['total'], 2) ?></span>
            </div>
            
            <input type="hidden" name="total" id="total" value="<?= $venta['total'] ?>">
            
            <button type="submit" class="edit-btn-submit">
                <i class="fas fa-save"></i> Actualizar Venta
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productosContainer = document.getElementById('productosContainer');
    const envioInput = document.getElementById('envio');
    const formVenta = document.getElementById('formVenta');
    
    // Clonar plantilla de producto
    const productoOriginal = productosContainer.querySelector('.edit-producto-item');
    
    // Función para actualizar precios y total
    function actualizarPrecios() {
        let total = 0;
        
        document.querySelectorAll('.edit-producto-item').forEach(item => {
            const select = item.querySelector('.edit-producto-select');
            const cantidadInput = item.querySelector('.edit-cantidad');
            const descuentoSelect = item.querySelector('.edit-descuento');
            const subtotalInput = item.querySelector('.edit-subtotal');
            const detalleDescuento = item.querySelector('.edit-detalle-descuento');
            const precioOriginal = item.querySelector('.edit-precio-original');
            const precioDescuento = item.querySelector('.edit-precio-descuento');
            const porcentajeDescuento = item.querySelector('.edit-porcentaje-descuento');
            
            if (!select || !cantidadInput || !descuentoSelect || !subtotalInput) return;
            
            const selectedOption = select.options[select.selectedIndex];
            const precioUnitario = parseFloat(selectedOption?.dataset.precio) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            const descuento = parseFloat(descuentoSelect.value) || 0;
            
            const subtotalSinDescuento = precioUnitario * cantidad;
            const montoDescuento = subtotalSinDescuento * (descuento / 100);
            const subtotalConDescuento = subtotalSinDescuento - montoDescuento;
            
            subtotalInput.value = subtotalConDescuento.toFixed(2);
            total += subtotalConDescuento;
            
            if (detalleDescuento && precioOriginal && precioDescuento && porcentajeDescuento) {
                if (descuento > 0) {
                    detalleDescuento.style.display = 'block';
                    precioOriginal.textContent = '$' + subtotalSinDescuento.toFixed(2);
                    precioDescuento.textContent = '$' + subtotalConDescuento.toFixed(2);
                    porcentajeDescuento.textContent = descuento + '% descuento';
                } else {
                    detalleDescuento.style.display = 'none';
                }
            }
        });
        
        const envio = parseFloat(envioInput.value) || 0;
        total += envio;
        
        document.getElementById('total-venta').textContent = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
    
    // Agregar nuevo producto
    document.querySelector('.edit-add-producto').addEventListener('click', function() {
        const nuevoProducto = productoOriginal.cloneNode(true);
        nuevoProducto.querySelector('.edit-producto-select').selectedIndex = 0;
        nuevoProducto.querySelector('.edit-cantidad').value = 1;
        nuevoProducto.querySelector('.edit-descuento').selectedIndex = 0;
        nuevoProducto.querySelector('.edit-subtotal').value = '';
        nuevoProducto.querySelector('.edit-detalle-descuento').style.display = 'none';
        
        productosContainer.appendChild(nuevoProducto);
        nuevoProducto.querySelector('.edit-producto-select').focus();
    });
    
    // Eliminar producto
    productosContainer.addEventListener('click', function(e) {
        if (e.target.closest('.edit-remove-producto')) {
            const productoItem = e.target.closest('.edit-producto-item');
            if (document.querySelectorAll('.edit-producto-item').length > 1) {
                productoItem.remove();
                actualizarPrecios();
            } else {
                // Si es el último, resetearlo en lugar de eliminarlo
                productoItem.querySelector('.edit-producto-select').selectedIndex = 0;
                productoItem.querySelector('.edit-cantidad').value = 1;
                productoItem.querySelector('.edit-descuento').selectedIndex = 0;
                productoItem.querySelector('.edit-subtotal').value = '';
                productoItem.querySelector('.edit-detalle-descuento').style.display = 'none';
                actualizarPrecios();
            }
        }
    });
    
    // Event listeners para actualización de precios
    productosContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('edit-producto-select') || 
            e.target.classList.contains('edit-descuento')) {
            actualizarPrecios();
        }
    });

    productosContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('edit-cantidad')) {
            actualizarPrecios();
        }
    });
    
    // Event listener para el envío
    envioInput.addEventListener('input', function() {
        if (parseFloat(this.value) < 0) {
            this.value = 0;
        }
        actualizarPrecios();
    });
    
    // Interceptar envío del formulario para validación
    formVenta.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar que haya al menos un producto
        const productos = Array.from(document.querySelectorAll('.edit-producto-select')).filter(select => select.value);
        if (productos.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Productos requeridos',
                text: 'Debes agregar al menos un producto',
                confirmButtonColor: '#8e44ad'
            });
            return;
        }
        
        // Mostrar confirmación
        Swal.fire({
            title: '¿Actualizar venta?',
            text: 'Se actualizará la información de la venta',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#8e44ad'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón para evitar múltiples envíos
                const submitBtn = formVenta.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                
                // Enviar formulario
                formVenta.submit();
            }
        });
    });
    
    // Inicializar precios
    actualizarPrecios();
});
</script>
</body>
</html>
<?php $conn->close(); ?>