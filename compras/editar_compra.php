<?php
include '../config/config.php';
include '../includes/header.php';

// Verificar si se recibió el ID de la compra
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error&message=Compra no especificada');
    exit();
}

$compra_id = intval($_GET['id']);

// Obtener información de la compra
$sql_compra = "SELECT c.*, p.nombre AS nombre_proveedor 
                FROM compras c
                LEFT JOIN proveedores p ON c.id_proveedor = p.id
                WHERE c.id = ?";
$stmt_compra = $conn->prepare($sql_compra);
$stmt_compra->bind_param("i", $compra_id);
$stmt_compra->execute();
$result_compra = $stmt_compra->get_result();

if ($result_compra->num_rows === 0) {
    header('Location: index.php?alert=error&message=Compra no encontrada');
    exit();
}

$compra = $result_compra->fetch_assoc();

// Obtener los productos de la compra
$sql_productos = "SELECT dc.*, p.nombre, p.precio_compra, p.stock 
                    FROM detalle_compras dc 
                    JOIN productos p ON dc.id_producto = p.id 
                    WHERE dc.id_compra = ?";
$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bind_param("i", $compra_id);
$stmt_productos->execute();
$result_productos = $stmt_productos->get_result();
$productos_compra = $result_productos->fetch_all(MYSQLI_ASSOC);

// Obtener todos los productos disponibles (incluyendo stock 0 para productos ya en la compra)
$productos_ids_en_compra = array_column($productos_compra, 'id_producto');
$placeholders = implode(',', array_fill(0, count($productos_ids_en_compra), '?'));
$types = str_repeat('i', count($productos_ids_en_compra));

$sql_productos_disponibles = "SELECT * FROM productos 
                                WHERE estado = 'activo' 
                                OR id IN ($placeholders)
                                ORDER BY nombre ASC";
$stmt_productos = $conn->prepare($sql_productos_disponibles);

if (!empty($productos_ids_en_compra)) {
    $stmt_productos->bind_param($types, ...$productos_ids_en_compra);
}

$stmt_productos->execute();
$productos_disponibles = $stmt_productos->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener proveedores
$proveedores = $conn->query("SELECT * FROM proveedores ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Compra</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/vamates/assets/css/editar/edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="edit-container">
    <div class="edit-form">
        <div class="edit-header-section">
            <h2><i class="fas fa-edit"></i> Editar Compra #<?= $compra['id'] ?></h2>
            
            <a href="index.php" class="edit-btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="edit-info-box">
            <h4>Información de la Compra</h4>
            <p><strong>Fecha original:</strong> <?= date('d/m/Y', strtotime($compra['fecha'])) ?></p>
            <p><strong>Proveedor original:</strong> <?= htmlspecialchars($compra['nombre_proveedor'] ?? 'No especificado') ?></p>
            <p><strong>Total original:</strong> $<?= number_format($compra['monto'], 2) ?></p>
        </div>
        
        <form action="actualizar_compra.php" method="post" id="formCompra">
            <input type="hidden" name="compra_id" value="<?= $compra['id'] ?>">
            
            <div class="edit-form-group">
                <label for="fecha">Fecha</label>
                <input class="edit-control" type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($compra['fecha']) ?>" required>
                <div class="edit-error-message" id="fechaError">La fecha debe ser de 2025 en adelante</div>
            </div>
            
            <div class="edit-form-group">
                <label for="id_proveedor">Proveedor</label>
                <select name="id_proveedor" id="id_proveedor" class="edit-control" required>
                    <option value="">Seleccionar proveedor</option>
                    <?php while ($prov = $proveedores->fetch_assoc()): ?>
                        <option value="<?= $prov['id'] ?>" <?= $prov['id'] == $compra['id_proveedor'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prov['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="edit-form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="edit-control" required>
                    <option value="pendiente" <?= $compra['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="pagada" <?= $compra['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                    <option value="cancelada" <?= $compra['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            
            <h3>Productos</h3>
            <div id="productosContainer">
                <?php foreach ($productos_compra as $producto): ?>
                    <div class="edit-producto-item">
                        <div class="edit-producto-grid">
                            <select name="producto_id[]" class="edit-producto-select edit-control" required>
                                <option value="">Selecciona un producto</option>
                                <?php foreach ($productos_disponibles as $prod): ?>
                                    <option value="<?= $prod['id'] ?>" 
                                            data-precio="<?= $prod['precio_compra'] ?>"
                                            data-stock="<?= $prod['stock'] ?>"
                                            <?= $prod['id'] == $producto['id_producto'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prod['nombre']) ?> 
                                        (Stock: <?= $prod['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <input type="number" name="cantidad[]" class="edit-cantidad edit-control" 
                                    placeholder="Cantidad" min="1" 
                                    value="<?= $producto['cantidad'] ?>" required>
                            
                            <input type="number" name="precio_unitario[]" class="edit-precio edit-control" 
                                    placeholder="Precio unitario" step="0.01" min="0"
                                    value="<?= $producto['precio_unitario'] ?>" required>
                                    
                            <input type="text" class="edit-subtotal edit-control" placeholder="Subtotal" readonly
                                   value="<?= number_format($producto['cantidad'] * $producto['precio_unitario'], 2) ?>">
                                    
                            <button type="button" class="edit-btn-action edit-btn-delete edit-remove-producto">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" class="edit-btn-agregar edit-add-producto">
                <i class="fas fa-plus"></i> Agregar otro producto
            </button>
            
            <div class="edit-form-group">
                <label for="ajuste">Descuento o Recargo ($)</label>
                <input type="number" name="ajuste" id="ajuste" class="edit-control" 
                        placeholder="Ej: -100 para descuento o 50 para recargo" step="0.01" 
                        value="<?= $compra['monto'] - array_reduce($productos_compra, function($carry, $item) {
                           return $carry + ($item['cantidad'] * $item['precio_unitario']);
                        }, 0) ?>">
            </div>
            
            <div class="edit-total-display">
                <strong>Total Compra:</strong> $<span id="total-compra"><?= number_format($compra['monto'], 2) ?></span>
            </div>
            
            <input type="hidden" name="total" id="total" value="<?= $compra['monto'] ?>">
            <input type="hidden" name="productos_originales" id="productos_originales" 
                    value="<?= htmlspecialchars(json_encode(array_map(function($p) {
                        return ['id_producto' => $p['id_producto'], 'cantidad' => $p['cantidad']];
                    }, $productos_compra))) ?>">
            
            <button type="submit" class="edit-btn-submit">
                <i class="fas fa-save"></i> Actualizar Compra
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productosContainer = document.getElementById('productosContainer');
    const ajusteInput = document.getElementById('ajuste');
    const formCompra = document.getElementById('formCompra');
    
    // Clonar plantilla de producto
    const productoOriginal = productosContainer.querySelector('.edit-producto-item');
    
    // Función para actualizar precios y total
    function actualizarPrecios() {
        let subtotalProductos = 0;
        
        document.querySelectorAll('.edit-producto-item').forEach(item => {
            const select = item.querySelector('.edit-producto-select');
            const cantidadInput = item.querySelector('.edit-cantidad');
            const precioInput = item.querySelector('.edit-precio');
            const subtotalInput = item.querySelector('.edit-subtotal');
            
            if (!select || !cantidadInput || !precioInput || !subtotalInput) return;
            
            const cantidad = parseInt(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const subtotal = cantidad * precio;
            
            subtotalInput.value = subtotal.toFixed(2);
            subtotalProductos += subtotal;
        });
        
        const ajuste = parseFloat(ajusteInput.value) || 0;
        const total = subtotalProductos + ajuste;
        
        document.getElementById('total-compra').textContent = total.toFixed(2);
        document.getElementById('total').value = total.toFixed(2);
    }
    
    // Agregar nuevo producto
    document.querySelector('.edit-add-producto').addEventListener('click', function() {
        const nuevoProducto = productoOriginal.cloneNode(true);
        nuevoProducto.querySelector('.edit-producto-select').selectedIndex = 0;
        nuevoProducto.querySelector('.edit-cantidad').value = 1;
        nuevoProducto.querySelector('.edit-precio').value = '';
        nuevoProducto.querySelector('.edit-subtotal').value = '';
        
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
                productoItem.querySelector('.edit-precio').value = '';
                productoItem.querySelector('.edit-subtotal').value = '';
            }
        }
    });
    
    // Actualizar precio cuando se selecciona un producto
    productosContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('edit-producto-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const precioInput = e.target.closest('.edit-producto-item').querySelector('.edit-precio');
            
            if (selectedOption.value && selectedOption.dataset.precio) {
                precioInput.value = selectedOption.dataset.precio;
            } else {
                precioInput.value = '';
            }
            
            actualizarPrecios();
        }
    });
    
    // Actualizar precios cuando cambian cantidades o precios
    productosContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('edit-cantidad') || e.target.classList.contains('edit-precio')) {
            actualizarPrecios();
        }
    });
    
    // Actualizar total cuando cambia el ajuste
    ajusteInput.addEventListener('input', actualizarPrecios);
    
    // Validar fecha
    document.getElementById('fecha').addEventListener('change', function() {
        const fechaValida = validarFecha(this.value);
        mostrarError('fecha', 'fechaError', !fechaValida);
    });
    
    // Función para validar fecha
    function validarFecha(fecha) {
        const fechaSeleccionada = new Date(fecha);
        const año = fechaSeleccionada.getFullYear();
        return año >= 2025;
    }
    
    // Función para mostrar/ocultar error
    function mostrarError(inputId, errorId, mostrar) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        
        if (mostrar) {
            input.classList.add('edit-error');
            error.classList.add('edit-show');
        } else {
            input.classList.remove('edit-error');
            error.classList.remove('edit-show');
        }
    }
    
    // Interceptar envío del formulario para validación
    formCompra.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar fecha
        const fechaInput = document.getElementById('fecha');
        if (!validarFecha(fechaInput.value)) {
            mostrarError('fecha', 'fechaError', true);
            Swal.fire({
                icon: 'error',
                title: 'Fecha inválida',
                text: 'La fecha debe ser de 2025 en adelante',
                confirmButtonColor: '#8e44ad'
            });
            return;
        }
        
        // Validar proveedor
        const proveedor = document.getElementById('id_proveedor').value;
        if (!proveedor) {
            Swal.fire({
                icon: 'error',
                title: 'Proveedor requerido',
                text: 'Debes seleccionar un proveedor',
                confirmButtonColor: '#8e44ad'
            });
            return;
        }
        // Validar productos
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
            title: '¿Actualizar compra?',
            text: 'Se actualizará la información de la compra y el stock de los productos',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#8e44ad'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar botón para evitar múltiples envíos
                const submitBtn = formCompra.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                
                // Enviar formulario
                formCompra.submit();
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