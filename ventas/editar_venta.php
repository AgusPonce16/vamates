<?php 
include '../includes/header.php';
include '../config/config.php';

// Verificar que se reciba el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id_venta = intval($_GET['id']);

// Obtener datos de la venta
$sql_venta = "SELECT * FROM ventas WHERE id = ?";
$stmt = $conn->prepare($sql_venta);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) {
    header('Location: index.php?alert=error');
    exit;
}

// Obtener detalles de la venta
$sql_detalles = "SELECT vd.*, p.nombre as producto_nombre 
                FROM detalle_ventas vd 
                left JOIN productos p ON vd.producto_id = p.id 
                WHERE vd.venta_id = ?";

$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bind_param("i", $id_venta);
$stmt_detalles->execute();
$detalles = $stmt_detalles->get_result()->fetch_all(MYSQLI_ASSOC);
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

    .btn-agregar {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .btn-agregar:hover {
        background-color: var(--secondary-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 14px;
    }

    th, td {
        border: 1px solid #e0e0e0;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
    }

    tr:nth-child(even) {
        background-color: #fafafa;
    }

    .total-display {
        font-size: 1.2em;
        font-weight: 500;
        margin: 15px 0;
        padding: 12px;
        background-color: #e8f5e9;
        border-radius: var(--border-radius);
        text-align: center;
    }

    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        margin: 0 3px;
        font-size: 16px;
        transition: transform 0.2s;
    }

    .btn-action:hover {
        transform: scale(1.1);
    }

    .btn-delete {
        color: var(--danger-color);
    }

    .info-venta {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 20px;
    }

    .info-venta h4 {
        margin: 0 0 10px 0;
        color: #1976d2;
    }

    .form-row {
        display: flex;
        gap: 15px;
        align-items: end;
    }

    .form-row .control {
        flex: 1;
    }

    .form-row label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
</style>

<div class="container">
    <div class="edit-form">
        <div class="header-section">
            <h2>Editar Venta #<?= $venta['id'] ?></h2>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="info-venta">
            <h4>Información de la Venta</h4>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
            <p><strong>Estado:</strong> <?= ucfirst($venta['estado']) ?></p>
            <p><strong>Monto Original:</strong> $<?= number_format($venta['total'], 2, ',', '.') ?></p>
            <p><strong>Envío Original:</strong> $<?= number_format($venta['envio'] ?? 0, 2, ',', '.') ?></p>
            <p><strong>Descuento Original:</strong> <?= $venta['descuento'] ?? 0 ?>%</p>
        </div>
        
        <form id="formEditarVenta" action="actualizar_venta.php" method="post" class="form-container">
            <input type="hidden" name="id_venta" value="<?= $id_venta ?>">
            
            <div class="form-row">
                <div>
                    <label for="fecha">Fecha:</label>
                    <input class="control" type="date" name="fecha" id="fecha" value="<?= $venta['fecha'] ?>" required>
                </div>
                <div>
                    <label for="estado">Estado:</label>
                    <select name="estado" id="estado" class="control" required>
                        <option value="pendiente" <?= $venta['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="pagada" <?= $venta['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                        <option value="cancelada" <?= $venta['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div>
                    <label for="envio">Costo de envío:</label>
                    <input type="number" step="0.01" name="envio" id="envio" class="control" value="<?= $venta['envio'] ?? 0 ?>">
                </div>
                <div>
                    <label for="descuento">Descuento (%):</label>
                    <select name="descuento" id="descuento" class="control">
                        <?php for($i = 0; $i <= 30; $i += 5): ?>
                            <option value="<?= $i ?>" <?= ($venta['descuento'] ?? 0) == $i ? 'selected' : '' ?>><?= $i ?>%</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <!-- Selector de productos -->
            <div class="form-row">
                <div>
                    <label for="productoSelect">Producto:</label>
                    <select id="productoSelect" class="control">
                        <option value="">Seleccioná un producto</option>
                        <?php
                        $productos = $conn->query("SELECT * FROM productos WHERE estado = 'activo' AND stock > 0 ORDER BY nombre ASC");
                        while ($prod = $productos->fetch_assoc()) {
                            echo "<option value='{$prod['id']}' data-precio='{$prod['precio']}' data-stock='{$prod['stock']}'>{$prod['nombre']} - $ {$prod['precio']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="cantidadInput">Cantidad:</label>
                    <input type="number" id="cantidadInput" class="control" placeholder="Cantidad" min="1" value="1">
                </div>
                <div>
                    <label for="precioInput">Precio unitario:</label>
                    <input type="number" id="precioInput" class="control" placeholder="Precio unitario" step="0.01" min="0">
                </div>
                <div>
                    <button type="button" class="btn-agregar" onclick="agregarProducto()">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>
            
            <!-- Tabla de productos agregados -->
            <table id="tablaProductos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            
            <div class="total-display">
                Subtotal: $<span id="subtotalVenta">0.00</span><br>
                Descuento (<span id="descuentoDisplay"><?= $venta['descuento'] ?? 0 ?></span>%): -$<span id="montoDescuento">0.00</span><br>
                Envío: $<span id="envioDisplay"><?= number_format($venta['envio'] ?? 0, 2) ?></span><br>
                <strong>Total: $<span id="totalVenta">0.00</span></strong>
            </div>
            
            <!-- Campos ocultos para el formulario -->
            <input type="hidden" name="productosJSON" id="productosJSON">
            <input type="hidden" name="descripcion" id="descripcionInput">
            <input type="hidden" name="monto" id="montoInput">
            
            <input class="btn-submit" type="submit" value="Actualizar Venta">
        </form>
    </div>
</div>

<script>
    // Variables globales
let productosAgregados = [];
let productosDisponibles = {}; // Cache de productos disponibles

// Cargar productos existentes
const productosExistentes = <?= json_encode($detalles) ?>;

// Inicializar con productos existentes
document.addEventListener('DOMContentLoaded', function() {
    // Crear cache de productos disponibles
    const selectProductos = document.getElementById("productoSelect");
    Array.from(selectProductos.options).forEach(option => {
        if (option.value) {
            productosDisponibles[option.value] = {
                nombre: option.text.split(" - $")[0],
                precio: parseFloat(option.dataset.precio),
                stock: parseInt(option.dataset.stock)
            };
        }
    });
    
    // Cargar productos existentes
    productosExistentes.forEach(detalle => {
        const productoId = detalle.producto_id.toString();
        
        productosAgregados.push({
            id: productoId,
            nombre: detalle.producto_nombre,
            precio: parseFloat(detalle.precio_unitario),
            cantidad: parseInt(detalle.cantidad),
            subtotal: parseFloat(detalle.precio_unitario) * parseInt(detalle.cantidad),
            esExistente: true // Marcar como producto existente
        });
    });
    
    actualizarTabla();

    // Actualizar precio al seleccionar producto
    document.getElementById("productoSelect").addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const precioInput = document.getElementById("precioInput");
        const cantidadInput = document.getElementById("cantidadInput");
        
        if (option && option.value) {
            const precio = parseFloat(option.dataset.precio) || 0;
            const stock = parseInt(option.dataset.stock) || 0;
            
            // Autocompletar el precio
            precioInput.value = precio.toFixed(2);
            cantidadInput.max = stock;
            
            // Enfocar el campo de cantidad para facilitar la entrada
            cantidadInput.focus();
            cantidadInput.select();
        } else {
            precioInput.value = '';
            cantidadInput.removeAttribute('max');
        }
    });

    // Actualizar total cuando cambia el envío
    document.getElementById("envio").addEventListener('change', function() {
        document.getElementById("envioDisplay").textContent = parseFloat(this.value || 0).toFixed(2);
        calcularTotal();
    });

    // Actualizar total cuando cambia el descuento
    document.getElementById("descuento").addEventListener('change', function() {
        document.getElementById("descuentoDisplay").textContent = this.value;
        calcularTotal();
    });
    
    // Manejar envío del formulario
    document.getElementById("formEditarVenta").addEventListener("submit", function(e) {
        e.preventDefault();
        
        if (productosAgregados.length === 0) {
            swal("Error", "Debés agregar al menos un producto", "error");
            return;
        }
        
        // Deshabilitar el botón para evitar múltiples clics
        const submitBtn = this.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.value = "Actualizando...";
        
        // Enviar el formulario
        this.submit();
    });
});

function agregarProducto() {
    const select = document.getElementById("productoSelect");
    const cantidadInput = document.getElementById("cantidadInput");
    const precioInput = document.getElementById("precioInput");
    
    const cantidad = parseInt(cantidadInput.value);
    const precio = parseFloat(precioInput.value);
    const option = select.options[select.selectedIndex];

    if (!option.value || isNaN(cantidad) || cantidad <= 0 || isNaN(precio) || precio <= 0) {
        swal("Error", "Completá todos los campos con valores válidos", "error");
        return;
    }

    // Verificar stock
    const stock = parseInt(option.dataset.stock);
    if (cantidad > stock) {
        swal("Error", `No hay suficiente stock. Disponible: ${stock}`, "error");
        return;
    }

    const id = option.value;
    const nombre = option.text.split(" - $")[0];
    const subtotal = precio * cantidad;

    // Buscar producto existente
    const index = productosAgregados.findIndex(p => p.id === id);
    
    if (index >= 0) {
        // Verificar que la suma no exceda el stock
        const nuevaCantidad = productosAgregados[index].cantidad + cantidad;
        if (nuevaCantidad > stock) {
            swal("Error", `No hay suficiente stock. Disponible: ${stock}, ya agregado: ${productosAgregados[index].cantidad}`, "error");
            return;
        }
        // Actualizar existente
        productosAgregados[index].cantidad = nuevaCantidad;
        productosAgregados[index].precio = precio; // Actualizar precio también
        productosAgregados[index].subtotal = precio * nuevaCantidad;
    } else {
        // Agregar nuevo
        productosAgregados.push({ 
            id, 
            nombre, 
            precio, 
            cantidad, 
            subtotal,
            esExistente: false
        });
    }

    actualizarTabla();
    cantidadInput.value = 1;
    select.selectedIndex = 0;
    precioInput.value = '';
    cantidadInput.removeAttribute('max');
    select.focus();
}

function actualizarTabla() {
    const tbody = document.querySelector("#tablaProductos tbody");
    tbody.innerHTML = "";
    let subtotal = 0;
    let descripcion = [];
    
    productosAgregados.forEach((prod, index) => {
        subtotal += prod.subtotal;
        descripcion.push(`${prod.cantidad}x ${prod.nombre}`);
        
        // Determinar si se puede editar inline
        const puedeEditar = productosDisponibles[prod.id] !== undefined;
        
        tbody.innerHTML += `
            <tr>
                <td>${prod.nombre}</td>
                <td>
                    ${puedeEditar ? 
                        `<input type="number" value="${prod.cantidad}" min="1" max="${productosDisponibles[prod.id]?.stock || 999}" 
                         style="width: 60px; padding: 2px 5px; border: 1px solid #ddd; border-radius: 3px;" 
                         onchange="actualizarCantidad(${index}, this.value)">` 
                        : prod.cantidad}
                </td>
                <td>
                    ${puedeEditar ? 
                        `<input type="number" value="${prod.precio.toFixed(2)}" step="0.01" min="0" 
                         style="width: 80px; padding: 2px 5px; border: 1px solid #ddd; border-radius: 3px;" 
                         onchange="actualizarPrecio(${index}, this.value)">` 
                        : `$ ${prod.precio.toFixed(2)}`}
                </td>
                <td>$ ${prod.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn-action btn-delete" onclick="quitarProducto(${index})" title="Eliminar producto">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    ${!puedeEditar ? '<br><small style="color: #999;">Producto no disponible</small>' : ''}
                </td>
            </tr>`;
    });

    // Actualizar totales y campos ocultos
    document.getElementById("subtotalVenta").textContent = subtotal.toFixed(2);
    calcularTotal();
    document.getElementById("descripcionInput").value = descripcion.join(", ");
    document.getElementById("productosJSON").value = JSON.stringify(productosAgregados);
}

function actualizarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);
    
    if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
        swal("Error", "La cantidad debe ser mayor a 0", "error");
        actualizarTabla(); // Restaurar valor anterior
        return;
    }
    
    const producto = productosAgregados[index];
    const stockDisponible = productosDisponibles[producto.id]?.stock || 999;
    
    if (nuevaCantidad > stockDisponible) {
        swal("Error", `Stock insuficiente. Disponible: ${stockDisponible}`, "error");
        actualizarTabla(); // Restaurar valor anterior
        return;
    }
    
    productosAgregados[index].cantidad = nuevaCantidad;
    productosAgregados[index].subtotal = producto.precio * nuevaCantidad;
    
    actualizarTabla();
}

function actualizarPrecio(index, nuevoPrecio) {
    nuevoPrecio = parseFloat(nuevoPrecio);
    
    if (isNaN(nuevoPrecio) || nuevoPrecio < 0) {
        swal("Error", "El precio debe ser mayor o igual a 0", "error");
        actualizarTabla(); // Restaurar valor anterior
        return;
    }
    
    const producto = productosAgregados[index];
    productosAgregados[index].precio = nuevoPrecio;
    productosAgregados[index].subtotal = nuevoPrecio * producto.cantidad;
    
    actualizarTabla();
}

function calcularTotal() {
    const subtotal = parseFloat(document.getElementById("subtotalVenta").textContent) || 0;
    const descuentoPorcentaje = parseFloat(document.getElementById("descuento").value) || 0;
    const envio = parseFloat(document.getElementById("envio").value) || 0;
    
    const montoDescuento = subtotal * (descuentoPorcentaje / 100);
    const subtotalConDescuento = subtotal - montoDescuento;
    const total = subtotalConDescuento + envio;
    
    document.getElementById("montoDescuento").textContent = montoDescuento.toFixed(2);
    document.getElementById("totalVenta").textContent = total.toFixed(2);
    document.getElementById("montoInput").value = total;
}

function quitarProducto(index) {
    const producto = productosAgregados[index];
    
    swal({
        title: "¿Estás seguro?",
        text: `¿Querés eliminar "${producto.nombre}" de la venta?`,
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
        dangerMode: true,
    }).then((confirmar) => {
        if (confirmar) {
            productosAgregados.splice(index, 1);
            actualizarTabla();
        }
    });
}

</script>

<?php $conn->close(); ?>