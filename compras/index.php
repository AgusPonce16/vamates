<?php include '../includes/header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/vamates3/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            display: flex;
            gap: 30px;
            padding: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .column.left, .column.right {
            flex: 1;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            max-height: 90vh;
            overflow-y: auto;
        }

        h2 {
            color: var(--dark-color);
            margin-bottom: 20px;
            font-weight: 500;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
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

        .control[readonly] {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
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

        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
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
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:hover {
            background-color: #f0e6f6;
        }

        .containerBusq {
            margin-bottom: 15px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            font-size: 14px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
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

        .subtotal-display {
            font-size: 1.1em;
            font-weight: 400;
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .ajuste-display {
            font-size: 1.1em;
            font-weight: 400;
            margin: 10px 0;
            padding: 10px;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .ajuste-positivo {
            background-color: #fff3cd;
            color: #856404;
        }

        .ajuste-negativo {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .ajuste-cero {
            background-color: #f8f9fa;
            color: #6c757d;
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

        .btn-edit {
            color: var(--primary-color);
        }

        .btn-delete {
            color: var(--danger-color);
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-proveedor {
            background-color: #d1c4e9;
            color: #4527a0;
        }

        .fecha-header {
            background-color: #f0f0f0 !important;
            color: #333 !important;
            font-weight: bold !important;
            font-size: 15px !important;
            text-align: center !important;
            padding: 15px !important;
            border: none !important;
            border-top: 2px solid #ddd !important;
            border-bottom: 2px solid #ddd !important;
        }

        .fecha-header td {
            border: none !important;
            background-color: #f0f0f0 !important;
            color: #333 !important;
            font-weight: bold !important;
        }

        .compra-row:hover {
            background-color: #f0e6f6 !important;
        }

        .product-details {
            padding-left: 30px !important;
        }

        .estado-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .estado-pagada {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .estado-cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }

        .control.error {
            border-color: var(--danger-color);
            background-color: #fff5f5;
        }

        .error-message {
            color: var(--danger-color);
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
            display: none;
        }

        .error-message.show {
            display: block;
        }
    </style>
</head>

<div class="container">
    <!-- Columna Izquierda: Formulario para Agregar Compra -->
    <div class="column left">
        <h2>Agregar Compra</h2>
        
        <form id="formCompra" action="add_compra.php" method="post" class="form-container">
            <input class="control" type="date" name="fecha" id="fechaInput" required>
            <div class="error-message" id="fechaError">La fecha debe ser de 2025 en adelante</div>
            
            <!-- Selector de proveedor -->
            <select name="id_proveedor" class="control" required>
                <option value="">Seleccionar proveedor</option>
                <?php
                include '../config/config.php';
                $proveedores = $conn->query("SELECT * FROM proveedores ORDER BY nombre ASC");
                while ($prov = $proveedores->fetch_assoc()) {
                    echo "<option value='{$prov['id']}'>{$prov['nombre']}</option>";
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

            <input type="number" id="cantidadInput" class="control" placeholder="Cantidad" min="1" value="1">
            <input type="number" id="precioInput" class="control" placeholder="Precio unitario" step="0.01" min="0" readonly>
            <small style="color: #666; font-size: 12px; margin-top: -10px; display: block;">* El precio se completa automáticamente</small>

            <select name="estado" class="control" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
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
            
            <!-- Mostrar subtotal de productos -->
            <div class="subtotal-display">
                Subtotal productos: $<span id="subtotalProductos">0.00</span>
            </div>

            <!-- Campo para descuento/recargo -->
            <div style="margin-bottom: 15px;">
                <label for="ajuste" style="font-weight: 500;">Descuento o Recargo ($):</label>
                <input type="number" placeholder="Ej: -100 para descuento o 50 para recargo" step="0.01" id="ajuste" class="control" value="0" oninput="calcularTotalFinal()">
                <small style="color: #666; font-size: 12px;">* Valores negativos = descuento, valores positivos = recargo</small>
            </div>

            <!-- Mostrar el ajuste aplicado -->
            <div id="ajusteDisplay" class="ajuste-display ajuste-cero" style="display: none;">
                Ajuste: $<span id="valorAjuste">0.00</span>
            </div>

            <!-- Total final -->
            <div class="total-display">
                <strong>Total final: $<span id="totalFinal">0.00</span></strong>
            </div>
            
            <!-- Campos ocultos para el formulario -->
            <input type="hidden" name="productosJSON" id="productosJSON">
            <input type="hidden" name="monto" id="montoInput">
            <input type="hidden" name="ajuste" id="ajusteInput" value="0">
            <input type="hidden" name="descripcion" id="descripcionInput">
            <input class="btn-submit" type="submit" value="Guardar Compra">
            
        </form>
    </div>

    <!-- Columna Derecha: Tabla de Compras -->
    <div class="column right">
        <h2>Historial de Compras</h2>

        <!-- Filtros adicionales -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <select id="filtroEstado" class="control" onchange="filtrarCompras()" style="flex: 1;">
                <option value="">Todos los estados</option>
                <option value="pagada">Pagadas</option>
                <option value="pendiente">Pendientes</option>
                <option value="cancelada">Canceladas</option>
            </select>
            
            <input type="text" id="buscarCompra" class="search-input" placeholder="Buscar compra..." onkeyup="filtrarCompras()" style="flex: 2;">
        </div>

        <?php if (isset($_GET['alert'])): ?>
            <script>
                swal({
                    icon: '<?= $_GET['alert'] === 'error' ? 'error' : 'success' ?>',
                    title: '<?= 
                        $_GET['alert'] === 'added' ? '¡Compra agregada!' : 
                        ($_GET['alert'] === 'updated' ? '¡Compra actualizada!' : 
                        ($_GET['alert'] === 'state_changed' ? '¡Estado actualizado!' : 
                        '¡Operación completada!')) ?>',
                    timer: 3000
                });
            </script>
        <?php endif; ?>

        <table id="tablaCompras">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta agrupada por fecha
                $sql = "SELECT c.*, p.nombre AS nombre_proveedor
                        FROM compras c
                        LEFT JOIN proveedores p ON c.id_proveedor = p.id
                        ORDER BY c.fecha DESC, c.id DESC";
                $result = $conn->query($sql);
                
                $fechaActual = '';
                $numeroColumnas = 6; // Número de columnas en la tabla (sin contar la fecha)
                
                while($compra = $result->fetch_assoc()):
                    $fechaCompra = $compra['fecha'];
                    $claseEstado = "estado-" . $compra['estado'];
                    
                    // Si es una nueva fecha, mostrar el header de fecha
                    if ($fechaActual !== $fechaCompra) {
                        $fechaActual = $fechaCompra;
                        $fechaFormateada = date('d/m/Y', strtotime($fechaCompra));
                        echo "<tr class='fecha-header'>
                                <td colspan='{$numeroColumnas}'>📅 {$fechaFormateada}</td>
                            </tr>";
                    }
                ?>
                <tr id="fila-compra-<?= $compra['id'] ?>" class="compra-row">
                    <td><?= $compra['id'] ?></td>
                    <td><?= htmlspecialchars($compra['descripcion']) ?></td>
                    <td>$<?= number_format($compra['monto'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($compra['nombre_proveedor'] ?? 'N/A') ?></td>
                    <td>
                        <span class="estado-badge <?= $claseEstado ?>">
                            <?= ucfirst($compra['estado']) ?>
                        </span>
                    </td>
                    <td>
                        <!-- Botón Editar -->
                        <button class="btn-action btn-edit" onclick="editarCompra(<?= $compra['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <!-- Selector de Estado -->
                        <select onchange="cambiarEstado(<?= $compra['id'] ?>, this.value)" 
                                class="control" style="width: auto; padding: 5px;">
                            <option value="pendiente" <?= $compra['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="pagada" <?= $compra['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                            <option value="cancelada" <?= $compra['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Variables globales
let productosAgregados = [];

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
        input.classList.add('error');
        error.classList.add('show');
    } else {
        input.classList.remove('error');
        error.classList.remove('show');
    }
}

function filtrarCompras() {
    const estado = document.getElementById("filtroEstado").value.toLowerCase();
    const busqueda = document.getElementById("buscarCompra").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaCompras tbody tr");

    filas.forEach(fila => {
        // Saltear las filas de fecha
        if (fila.classList.contains('fecha-header')) {
            return;
        }
        
        const textoFila = fila.textContent.toLowerCase();
        const estadoCompra = fila.querySelector("td:nth-child(5)")?.textContent.toLowerCase() || "";

        const coincideBusqueda = textoFila.includes(busqueda);
        const coincideEstado = estado === "" || estadoCompra.includes(estado);

        fila.style.display = (coincideBusqueda && coincideEstado) ? "" : "none";
    });
    
    // Ocultar headers de fecha que no tengan compras visibles
    const fechasHeaders = document.querySelectorAll("#tablaCompras tbody .fecha-header");
    fechasHeaders.forEach(fechaHeader => {
        let siguienteFila = fechaHeader.nextElementSibling;
        let tieneComprasVisibles = false;
        
        // Verificar si hay compras visibles después de este header de fecha
        while (siguienteFila && !siguienteFila.classList.contains('fecha-header')) {
            if (siguienteFila.style.display !== 'none') {
                tieneComprasVisibles = true;
                break;
            }
            siguienteFila = siguienteFila.nextElementSibling;
        }
        
        fechaHeader.style.display = tieneComprasVisibles ? "" : "none";
    });
}

function agregarProducto() {
    const select = document.getElementById("productoSelect");
    const cantidadInput = document.getElementById("cantidadInput");
    const precioInput = document.getElementById("precioInput");
    
    const cantidad = parseInt(cantidadInput.value);
    const precio = parseFloat(precioInput.value);
    const option = select.options[select.selectedIndex];

    if (!option.value) {
        swal("Error", "Seleccioná un producto", "error");
        return;
    }

    if (isNaN(cantidad) || cantidad <= 0) {
        swal("Error", "Ingresá una cantidad válida", "error");
        return;
    }

    if (isNaN(precio) || precio <= 0) {
        swal("Error", "El precio debe ser mayor a 0", "error");
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

// Función para actualizar la tabla de productos y calcular totales
function actualizarTabla() {
    const tbody = document.querySelector("#tablaProductos tbody");
    tbody.innerHTML = "";
    let subtotalProductos = 0;
    let descripcion = [];
    
    if (productosAgregados.length === 0) {
        document.getElementById("descripcionInput").value = "Sin productos";
        document.getElementById("subtotalProductos").textContent = "0.00";
        calcularTotalFinal();
        return;
    }
    
    productosAgregados.forEach((prod, index) => {
        // Validar que el producto tenga nombre
        const nombreProducto = prod.nombre || `Producto ${index + 1}`;
        subtotalProductos += prod.subtotal;
        descripcion.push(`${prod.cantidad}x ${nombreProducto}`);
        
        tbody.innerHTML += `
            <tr>
                <td>${nombreProducto}</td>
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

    // Actualizar subtotal
    document.getElementById("subtotalProductos").textContent = subtotalProductos.toFixed(2);
    
    // Actualizar descripción (asegurarse de que siempre tenga valor)
    const descripcionTexto = descripcion.length > 0 ? descripcion.join(", ") : "Compra sin productos";
    document.getElementById("descripcionInput").value = descripcionTexto;
    console.log("Descripción generada:", descripcionTexto); // Para depuración
    
    // Actualizar JSON de productos
    document.getElementById("productosJSON").value = JSON.stringify(productosAgregados);
    
    // Recalcular total
    calcularTotalFinal();
}

// Función para calcular el total final con descuento/recargo
function calcularTotalFinal() {
    const subtotalProductos = parseFloat(document.getElementById("subtotalProductos").textContent) || 0;
    const ajuste = parseFloat(document.getElementById("ajuste").value) || 0;
    const totalFinal = subtotalProductos + ajuste;

    // Actualizar visualización del ajuste
    const ajusteDisplay = document.getElementById("ajusteDisplay");
    const valorAjuste = document.getElementById("valorAjuste");
    
    if (ajuste !== 0) {
        ajusteDisplay.style.display = "block";
        valorAjuste.textContent = ajuste.toFixed(2);
        
        // Cambiar clase según el tipo de ajuste
        ajusteDisplay.className = "ajuste-display ";
        if (ajuste > 0) {
            ajusteDisplay.className += "ajuste-positivo";
        } else {
            ajusteDisplay.className += "ajuste-negativo";
        }
    } else {
        ajusteDisplay.style.display = "none";
    }

    // Actualizar total final
    document.getElementById("totalFinal").textContent = totalFinal.toFixed(2);
    
    // Actualizar campos ocultos
    document.getElementById("montoInput").value = totalFinal;
    document.getElementById("ajusteInput").value = ajuste;
}

function quitarProducto(index) {
    productosAgregados.splice(index, 1);
    actualizarTabla();
}

function cambiarEstado(idCompra, nuevoEstado) {
    const selectElement = event.target;
    const originalValue = selectElement.value;
    selectElement.disabled = true;
    
    fetch('actualizar_estado_compra.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: idCompra,
            estado: nuevoEstado
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Error al actualizar estado');
        }
        
        // Actualizar visualización
        const badge = document.querySelector(`#fila-compra-${idCompra} .estado-badge`);
        badge.className = `estado-badge estado-${nuevoEstado}`;
        badge.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
        
        swal({
            title: "¡Éxito!",
            text: "Estado actualizado correctamente",
            icon: "success",
            timer: 2000
        });
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = originalValue;
        swal({
            title: "Error",
            text: error.message || "Error al actualizar el estado",
            icon: "error"
        });
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

// Función para editar compra
function editarCompra(id) {
    window.location.href = `editar_compra.php?id=${id}`;
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Establecer fecha actual por defecto
    const hoy = new Date().toISOString().split('T')[0];
    document.querySelector('input[type="date"]').value = hoy;
    
    // Validación de fecha en tiempo real
    document.getElementById('fechaInput').addEventListener('change', function() {
        const fechaValida = validarFecha(this.value);
        mostrarError('fechaInput', 'fechaError', !fechaValida);
    });
    
    // Actualizar precio al seleccionar producto (precio readonly)
    document.getElementById("productoSelect").addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const precioInput = document.getElementById("precioInput");
        
        if (option.value) {
            precioInput.value = option.dataset.precio || '';
        } else {
            precioInput.value = '';
        }
    });
    
    // Validación del submit del formulario
    document.getElementById("formCompra").addEventListener("submit", function(e) {
        e.preventDefault();
        
        // Validar fecha
        const fechaInput = document.getElementById('fechaInput');
        if (!validarFecha(fechaInput.value)) {
            mostrarError('fechaInput', 'fechaError', true);
            swal("Error", "La fecha debe ser de 2025 en adelante", "error");
            return;
        }
        
        // Validar productos
        if (productosAgregados.length === 0) {
            swal("Error", "Debés agregar al menos un producto", "error");
            return;
        }
        
        // Validar que todos los campos obligatorios estén completos
        const proveedor = document.querySelector('select[name="id_proveedor"]').value;
        if (!proveedor) {
            swal("Error", "Seleccioná un proveedor", "error");
            return;
        }
        
        // Obtener valores para la confirmación
        const subtotal = parseFloat(document.getElementById("subtotalProductos").textContent) || 0;
        const ajuste = parseFloat(document.getElementById("ajuste").value) || 0;
        const total = parseFloat(document.getElementById("totalFinal").textContent) || 0;
        
        let mensajeConfirmacion = `Subtotal: $${subtotal.toFixed(2)}`;
        if (ajuste !== 0) {
            const tipoAjuste = ajuste > 0 ? "Recargo" : "Descuento";
            mensajeConfirmacion += `\n${tipoAjuste}: $${Math.abs(ajuste).toFixed(2)}`;
        }
        mensajeConfirmacion += `\nTotal final: $${total.toFixed(2)}`;
        
        // Confirmar antes de guardar
        swal({
            title: "¿Guardar compra?",
            text: mensajeConfirmacion,
            icon: "question",
            buttons: {
                cancel: "Cancelar",
                confirm: "Guardar"
            }
        }).then((willSave) => {
            if (willSave) {
                // Deshabilitar el botón para evitar múltiples clics
                const submitBtn = this.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.value = "Guardando...";
                
                // Enviar el formulario
                this.submit();
            }
        });
    });
});
</script>
<?php $conn->close(); ?>