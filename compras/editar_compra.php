<?php 
include '../includes/header.php';
include '../config/config.php';

// Verificar que se reciba el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?alert=error');
    exit;
}

$id_compra = intval($_GET['id']);

// Obtener datos de la compra
$sql_compra = "SELECT * FROM compras WHERE id = ?";
$stmt = $conn->prepare($sql_compra);
$stmt->bind_param("i", $id_compra);
$stmt->execute();
$compra = $stmt->get_result()->fetch_assoc();

if (!$compra) {
    header('Location: index.php?alert=error');
    exit;
}

// Obtener detalles de la compra
$sql_detalles = "SELECT cd.*, p.nombre as producto_nombre 
                FROM detalle_compras cd 
                JOIN productos p ON cd.id_producto = p.id 
                WHERE cd.id_compra = ?";

$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bind_param("i", $id_compra);
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

    .info-compra {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 20px;
    }

    .info-compra h4 {
        margin: 0 0 10px 0;
        color: #1976d2;
    }
</style>

<div class="container">
    <div class="edit-form">
        <div class="header-section">
            <h2>Editar Compra #<?= $compra['id'] ?></h2>
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="info-compra">
            <h4>Información de la Compra</h4>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($compra['fecha'])) ?></p>
            <p><strong>Estado:</strong> <?= ucfirst($compra['estado']) ?></p>
            <p><strong>Monto Original:</strong> $<?= number_format($compra['monto'], 2, ',', '.') ?></p>
        </div>
        
        <form id="formEditarCompra" action="actualizar_compra.php" method="post" class="form-container">
            <input type="hidden" name="id_compra" value="<?= $id_compra ?>">
            
            <input class="control" type="date" name="fecha" value="<?= $compra['fecha'] ?>" required>
            
            <!-- Selector de proveedor -->
            <select name="id_proveedor" class="control" required>
                <option value="">Seleccionar proveedor</option>
                <?php
                $proveedores = $conn->query("SELECT * FROM proveedores ORDER BY nombre ASC");
                while ($prov = $proveedores->fetch_assoc()) {
                    $selected = ($prov['id'] == $compra['id_proveedor']) ? 'selected' : '';
                    echo "<option value='{$prov['id']}' {$selected}>{$prov['nombre']}</option>";
                }
                ?>
            </select>

            <!-- Selector de productos -->
            <select id="productoSelect" class="control">
                <option value="">Seleccioná un producto</option>
                <?php
                $productos = $conn->query("SELECT * FROM productos ORDER BY nombre ASC");
                while ($prod = $productos->fetch_assoc()) {
                    echo "<option value='{$prod['id']}' data-precio='{$prod['precio_compra']}'>{$prod['nombre']} - $ {$prod['precio_compra']} (Stock: {$prod['stock']})</option>";
                }
                ?>
            </select>
            <!-- Dentro del formulario, después del selector de productos -->
            <div class="form-group">
                <label for="ajuste">Ajuste / Recargo (positivo o negativo):</label>
                <input type="number" step="0.01" name="ajuste" id="ajuste" class="control" 
                    value="<?= isset($compra['ajuste']) ? $compra['ajuste'] : '0' ?>">
            </div>

            <!-- Actualizar el display del total para incluir el ajuste -->
            <div class="total-display">
                Subtotal: $<span id="subtotalCompra">0.00</span><br>
                Ajuste: $<span id="ajusteDisplay"><?= isset($compra['ajuste']) ? number_format($compra['ajuste'], 2) : '0.00' ?></span><br>
                <strong>Total: $<span id="totalCompra">0.00</span></strong>
            </div>

            <input type="number" id="cantidadInput" class="control" placeholder="Cantidad" min="1" value="1">
            <input type="number" id="precioInput" class="control" placeholder="Precio unitario" step="0.01" min="0">

            <select name="estado" class="control" required>
                <option value="pendiente" <?= $compra['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="pagada" <?= $compra['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                <option value="cancelada" <?= $compra['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>

            <button type="button" class="btn-agregar" onclick="agregarProducto()">
                <i class="fas fa-plus"></i> Agregar Producto
            </button>
            
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
                Total: $<span id="totalCompra">0.00</span>
            </div>
            
            <!-- Campos ocultos para el formulario -->
            <input type="hidden" name="productosJSON" id="productosJSON">
            <input type="hidden" name="descripcion" id="descripcionInput">
            <input type="hidden" name="monto" id="montoInput">
            
            <input class="btn-submit" type="submit" value="Actualizar Compra">
        </form>
    </div>
</div>

<script>
    // Variables globales
let productosAgregados = [];
let ajuste = <?= isset($compra['ajuste']) ? $compra['ajuste'] : 0 ?>;

// Función para actualizar la tabla y los totales
function actualizarTabla() {
    const tbody = document.querySelector("#tablaProductos tbody");
    tbody.innerHTML = "";
    let subtotal = 0;
    let descripcion = [];
    
    productosAgregados.forEach((prod, index) => {
        subtotal += prod.subtotal;
        descripcion.push(`${prod.cantidad}x ${prod.nombre}`);
        
        tbody.innerHTML += `
            <tr>
                <td>${prod.nombre}</td>
                <td>${prod.cantidad}</td>
                <td>$ ${prod.precio.toFixed(2)}</td>
                <td>$ ${prod.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn-action btn-delete" onclick="quitarProducto(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
    });

    // Actualizar totales y campos ocultos
    document.getElementById("subtotalCompra").textContent = subtotal.toFixed(2);
    document.getElementById("ajusteDisplay").textContent = ajuste.toFixed(2);
    
    const total = subtotal + parseFloat(ajuste);
    document.getElementById("totalCompra").textContent = total.toFixed(2);
    
    document.getElementById("montoInput").value = total;
    document.getElementById("descripcionInput").value = descripcion.join(", ");
    document.getElementById("productosJSON").value = JSON.stringify(productosAgregados);
}

// Event listener para el campo de ajuste
document.getElementById("ajuste").addEventListener('change', function() {
    ajuste = parseFloat(this.value) || 0;
    actualizarTabla();
});

// Al cargar la página, inicializar con productos existentes
document.addEventListener('DOMContentLoaded', function() {
    // Cargar productos existentes
    productosExistentes.forEach(detalle => {
        productosAgregados.push({
            id: detalle.id_producto.toString(),
            nombre: detalle.producto_nombre,
            precio: parseFloat(detalle.precio_unitario),
            cantidad: parseInt(detalle.cantidad),
            subtotal: parseFloat(detalle.precio_unitario) * parseInt(detalle.cantidad)
        });
    });
    
    // Inicializar ajuste si existe
    ajuste = <?= isset($compra['ajuste']) ? $compra['ajuste'] : 0 ?>;
    document.getElementById("ajuste").value = ajuste;
    
    actualizarTabla();

});

    
    // Actualizar precio al seleccionar producto
    document.getElementById("productoSelect").addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById("precioInput").value = option.dataset.precio || '';
        }
    });
    
    // Manejar envío del formulario
    document.getElementById("formEditarCompra").addEventListener("submit", function(e) {
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

    const id = option.value;
    const nombre = option.text.split(" - $")[0];
    const subtotal = precio * cantidad;

    // Buscar producto existente
    const index = productosAgregados.findIndex(p => p.id === id);
    
    if (index >= 0) {
        // Actualizar existente
        productosAgregados[index].cantidad += cantidad;
        productosAgregados[index].subtotal += subtotal;
    } else {
        // Agregar nuevo
        productosAgregados.push({ 
            id, 
            nombre, 
            precio, 
            cantidad, 
            subtotal 
        });
    }

    actualizarTabla();
    cantidadInput.value = 1;
    select.selectedIndex = 0;
    precioInput.value = '';
    select.focus();
}

function actualizarTabla() {
    const tbody = document.querySelector("#tablaProductos tbody");
    tbody.innerHTML = "";
    let total = 0;
    let descripcion = [];
    
    productosAgregados.forEach((prod, index) => {
        total += prod.subtotal;
        descripcion.push(`${prod.cantidad}x ${prod.nombre}`);
        
        tbody.innerHTML += `
            <tr>
                <td>${prod.nombre}</td>
                <td>${prod.cantidad}</td>
                <td>$ ${prod.precio.toFixed(2)}</td>
                <td>$ ${prod.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn-action btn-delete" onclick="quitarProducto(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
    });

    // Actualizar totales y campos ocultos
    document.getElementById("totalCompra").textContent = total.toFixed(2);
    document.getElementById("montoInput").value = total;
    document.getElementById("descripcionInput").value = descripcion.join(", ");
    document.getElementById("productosJSON").value = JSON.stringify(productosAgregados);
}

function quitarProducto(index) {
    productosAgregados.splice(index, 1);
    actualizarTabla();
}
</script>

<?php $conn->close(); ?>