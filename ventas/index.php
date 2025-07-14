<?php include '../includes/header.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/b408879b64.js" crossorigin="anonymous"></script>
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
            padding: 12px 15px;
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
            margin-top: 10px;
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
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .producto-item {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            background-color: #f9f9f9;
            position: relative;
        }

        .producto-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: center;
        }

        .detalle-descuento {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            padding: 5px;
            background-color: #f5f5f5;
            border-radius: 4px;
            display: none;
        }

        .date-header {
            background-color: #f0f0f0 !important;
            font-weight: bold;
            font-size: 15px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-envio {
            background-color: #d1f5e9;
            color: #00796b;
        }
        
        /* Nuevos estilos para los estados */
        .estado-pagada {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .estado-cancelada {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        /* Estilo para el selector de estado en la tabla */
        .selector-estado {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .selector-estado:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.2);
        }

                .btn-factura {
            color: #d63031;
        }
        .factura-activa {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .factura-pendiente {
            background-color: #fff3e0;
            color: #e65100;
            border: 1px solid #ffcc80;
        }
    </style>
</head>

<div class="container">
    <!-- Columna Izquierda: Formulario para Realizar Venta -->
    <div class="column left">
        <h2><i class="fas fa-cash-register"></i> Realizar Venta</h2>
        
        <form action="add_venta.php" method="post" id="formVenta" class="form-container">
            <input class="control" type="date" name="fecha" id="fecha" value="<?php echo date('Y-m-d'); ?>" required>

            <!-- Agregar esto después del input de fecha -->
            <label for="estado">Estado</label>
            <select name="estado" id="estado" class="control" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada" selected>Pagada</option>
            </select>
            
            <div id="productosContainer">
                <div class="producto-item">
                    <div class="producto-grid">
                        <select name="producto_id[]" class="producto-select control" required>
                            <option value="" disabled selected>Selecciona un producto</option>
                            <?php
                            include '../config/config.php';
                            $sql = "SELECT * FROM productos WHERE STOCK > 0 AND ESTADO = 'activo'
                                    ORDER BY nombre ASC";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id']}' data-precio='{$row['precio']}'>{$row['nombre']} - (Stock: {$row['stock']})</option>";
                            }
                            $conn->close();
                            ?>
                        </select>
                        
                        <input type="number" name="cantidad[]" class="cantidad control" placeholder="Cantidad" min="1" value="1" required>
                        
                        <select name="descuento_producto[]" class="descuento control">
                            <option value="0">Sin descuento</option>
                            <?php
                            // Generar opciones de descuento de 5 en 5 hasta 100%
                            for ($i = 5; $i <= 100; $i += 5) {
                                echo "<option value=\"$i\">$i%</option>";
                            }
                            ?>
                        </select>
                        
                        <input type="text" class="subtotal control" placeholder="Subtotal" readonly>
                        
                        <button type="button" class="btn-action btn-delete remove-producto">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    
                    <div class="detalle-descuento">
                        <span class="precio-original"></span> → 
                        <span class="precio-descuento"></span>
                        (<span class="porcentaje-descuento"></span>)
                    </div>
                    
                    <button type="button" class="btn-agregar add-producto">
                        <i class="fas fa-plus"></i> Agregar otro producto
                    </button>
                </div>
            </div>
            
            <label for="envio">Envío</label>
            <input type="number" name="envio" id="envio" class="control" placeholder="Monto del envío" value="0" min="0" step="0.01">
            
            <div class="total-display">
                <strong>Total Venta:</strong> $<span id="total-venta">0.00</span>
            </div>
            
            <input type="hidden" name="total" id="total">
            <input class="btn-submit" type="submit" value="Completar Venta">
        </form>
    </div>

    <!-- Columna Derecha: Lista de Ventas -->
    <div class="column right">
        <h2><i class="fas fa-list"></i> Historial de Ventas</h2>

        <!-- Filtros adicionales -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <select id="filtroEstado" class="control" onchange="filtrarVentas()" style="flex: 1;">
                <option value="">Todos los estados</option>
                <option value="pagada">Pagadas</option>
                <option value="pendiente">Pendientes</option>
                <option value="cancelada">Canceladas</option>
            </select>
            
            <input type="text" id="buscarCompra" class="search-input" placeholder="Buscar compra..." onkeyup="filtrarVentas()" style="flex: 2;">
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'sin_stock'): ?>
            <script>
            Swal.fire({
                icon: 'error',
                title: '¡Stock insuficiente!',
                text: 'No se pudo completar la venta porque falta stock de algún producto.',
                showConfirmButton: true
            });
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['alert'])): ?>
            <script>
            Swal.fire({
                icon: '<?= $_GET['alert'] == 'added' ? 'success' : 'error' ?>',
                title: '<?= $_GET['alert'] == 'added' ? '¡Venta realizada correctamente!' : '¡Error!' ?>',
                text: '<?= isset($_GET['message']) ? $_GET['message'] : '' ?>',
                showConfirmButton: false,
                timer: 3000
            });
            </script>
        <?php endif; ?>

        <?php
        include '../config/config.php';
        $sql = "SELECT v.id, v.fecha, v.envio, v.estado, v.numero_factura,
                GROUP_CONCAT(CONCAT(p.nombre, ' (', dv.cantidad, 'x)')) AS productos,
                SUM(dv.subtotal) + v.envio AS total
                FROM ventas v
                JOIN detalle_ventas dv ON v.id = dv.venta_id
                JOIN productos p ON dv.producto_id = p.id
                GROUP BY v.id
                ORDER BY v.fecha DESC, v.id DESC";

        $result = $conn->query($sql);
        
        // Agrupar ventas por fecha
        $ventas_por_fecha = [];
        while ($row = $result->fetch_assoc()) {
            $fecha = $row['fecha'];
            if (!isset($ventas_por_fecha[$fecha])) {
                $ventas_por_fecha[$fecha] = [];
            }
            $ventas_por_fecha[$fecha][] = $row;
        }
        ?>

        <table id="tablaVentas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Factura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas_por_fecha as $fecha => $ventas): ?>
                    <tr class="date-header">
                        <td colspan="6">📅 <?= date('d/m/Y', strtotime($fecha)) ?>
                    </tr>
                    
                    <?php foreach ($ventas as $venta): ?>
                        <tr id="fila-venta-<?= $venta['id'] ?>" class="venta-row">
                            <td><?= $venta['id'] ?></td>
                            <td><?= htmlspecialchars($venta['productos']) ?></td>
                            <td>
                                $<?= number_format($venta['total'], 2) ?>
                                <?php if ($venta['envio'] > 0): ?>
                                    <span class="badge badge-envio">+envío</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                $claseEstado = '';
                                switch ($venta['estado']) {
                                    case 'pagada':
                                        $claseEstado = 'estado-pagada';
                                        break;
                                    case 'pendiente':
                                        $claseEstado = 'estado-pendiente';
                                        break;
                                    case 'cancelada':
                                        $claseEstado = 'estado-cancelada';
                                        break;
                                }
                                ?>
                                <span class="<?= $claseEstado ?>"><?= ucfirst($venta['estado']) ?></span>
                            </td>
                            <td>
                                <?php if($venta['numero_factura']): ?>
                                    <span class="badge badge-activo"><?= $venta['numero_factura'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-inactivo">Pendiente</span>
                                <?php endif; ?>
                                <a href="generar_factura.php?id_venta=<?= $venta['id'] ?>" class="btn-action" title="Descargar Factura">
                                    <i class="fas fa-file-pdf" style="color: #d63031;"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn-action btn-edit" onclick="window.location.href='editar_venta.php?id=<?= $venta['id'] ?>'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Selector de Estado -->
                                <select onchange="cambiarEstado(<?= $venta['id'] ?>, this.value)" 
                                        class="selector-estado">
                                    <option value="pendiente" <?= $venta['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="pagada" <?= $venta['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada</option>
                                    <option value="cancelada" <?= $venta['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const productosContainer = document.getElementById('productosContainer');
    const envioInput = document.getElementById('envio');
    const formVenta = document.getElementById('formVenta');
    
    // Clonar plantilla de producto
    const productoOriginal = productosContainer.querySelector('.producto-item');
    
    // Función para actualizar precios y total 
    function actualizarPrecios() {
    let total = 0;
    
    document.querySelectorAll('.producto-item').forEach(item => {
        const select = item.querySelector('.producto-select');
        const cantidadInput = item.querySelector('.cantidad');
        const descuentoSelect = item.querySelector('.descuento');
        const subtotalInput = item.querySelector('.subtotal');
        const detalleDescuento = item.querySelector('.detalle-descuento');
        const precioOriginal = item.querySelector('.precio-original');
        const precioDescuento = item.querySelector('.precio-descuento');
        const porcentajeDescuento = item.querySelector('.porcentaje-descuento');
        
        // Validar que todos los elementos existen
        if (!select || !cantidadInput || !descuentoSelect || !subtotalInput) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const precioUnitario = parseFloat(selectedOption?.dataset.precio) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        const descuento = parseFloat(descuentoSelect.value) || 0;
        
        // Extraer el stock disponible del texto de la opción
        const stockMatch = selectedOption?.text?.match(/Stock: (\d+)/);
        const stockDisponible = stockMatch ? parseInt(stockMatch[1]) : 0;
        
        if (cantidad > stockDisponible && select.value) {
            verificarStockEnTiempoReal(cantidadInput);
            return;
        }
        
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
    productosContainer.addEventListener('click', function(e) {
    if (e.target.closest('.add-producto')) {
        const nuevoProducto = productoOriginal.cloneNode(true);
        nuevoProducto.querySelector('.producto-select').selectedIndex = 0;
        nuevoProducto.querySelector('.cantidad').value = 1;
        nuevoProducto.querySelector('.descuento').selectedIndex = 0;
        nuevoProducto.querySelector('.subtotal').value = '';
        nuevoProducto.querySelector('.detalle-descuento').style.display = 'none';
        
        productosContainer.appendChild(nuevoProducto);
        nuevoProducto.querySelector('.producto-select').focus();
        actualizarPrecios(); // Esta línea es nueva - fuerza actualización
    }

        
        // Eliminar producto
        if (e.target.closest('.remove-producto')) {
            const productoItem = e.target.closest('.producto-item');
            if (document.querySelectorAll('.producto-item').length > 1) {
                productoItem.remove();
                actualizarPrecios();
            } else {
                // Si es el último, resetearlo en lugar de eliminarlo
                productoItem.querySelector('.producto-select').selectedIndex = 0;
                productoItem.querySelector('.cantidad').value = 1;
                productoItem.querySelector('.descuento').selectedIndex = 0;
                productoItem.querySelector('.subtotal').value = '';
                productoItem.querySelector('.detalle-descuento').style.display = 'none';
                actualizarPrecios();
            }
        }
    });
    // Event listeners mejorados para actualización de precios
    productosContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select') || 
            e.target.classList.contains('descuento') || 
            e.target.classList.contains('cantidad')) {
            actualizarPrecios();
            
            // Limpiar cache cuando cambie el producto (para validación de stock)
            if (e.target.classList.contains('producto-select') && window.validadorStock) {
                window.validadorStock.limpiarCache();
            }
        }
    });

    productosContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad')) {
            verificarStockEnTiempoReal(e.target);
            actualizarPrecios();
        }
    });
    

    
    // Event listener mejorado para el envío
    envioInput.addEventListener('input', function() {
        // Validar que el envío no sea negativo
        if (parseFloat(this.value) < 0) {
            this.value = 0;
            Swal.fire({
                icon: 'warning',
                title: 'Valor inválido',
                text: 'El monto del envío no puede ser negativo.',
                timer: 2000,
                showConfirmButton: false
            });
        }
        actualizarPrecios();
    });
    
    // Interceptar envío del formulario para validación
    formVenta.addEventListener('submit', async function(e) {
        e.preventDefault(); // Prevenir envío normal
        
        // Mostrar loading
        Swal.fire({
            title: 'Verificando stock...',
            text: 'Por favor espere mientras verificamos la disponibilidad.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        try {
            // Validar con el sistema de verificación
            const esValido = await validarAntesDeEnviar(this);
            
            if (esValido) {
                // Si es válido, enviar el formulario normalmente
                Swal.fire({
                    title: 'Procesando venta...',
                    text: 'Guardando información y actualizando stock.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar formulario
                this.submit();
            } else {
                // Si no es válido, cerrar el loading
                Swal.close();
            }
        } catch (error) {
            console.error('Error en validación:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Ocurrió un error al validar el stock. Intente nuevamente.',
                confirmButtonColor: '#8e44ad'
            });
        }
    });
    
    // Función para actualizar el stock en tiempo real después de una venta
    function actualizarStockPostVenta(ventaData) {
        if (window.validadorStock && ventaData.productos) {
            ventaData.productos.forEach(producto => {
                window.validadorStock.actualizarStockEnSelect(
                    producto.id, 
                    producto.nuevo_stock
                );
            });
        }
    }
    
    // Inicializar precios
    actualizarPrecios();
    
    // NUEVO: Verificación periódica de stock (opcional)
    let verificacionInterval;
    
    function iniciarVerificacionPeriodica() {
        // Verificar stock cada 2 minutos para productos seleccionados
        verificacionInterval = setInterval(() => {
            const productosSeleccionados = [];
            
            document.querySelectorAll('.producto-select').forEach(select => {
                if (select.value) {
                    const cantidadInput = select.closest('.producto-item')
                        .querySelector('.cantidad');
                    productosSeleccionados.push({
                        id: parseInt(select.value),
                        cantidad: parseInt(cantidadInput.value) || 0
                    });
                }
            });
            
            if (productosSeleccionados.length > 0 && window.validadorStock) {
                window.validadorStock.verificarStockMultiple(productosSeleccionados)
                    .then(resultado => {
                        if (resultado && !resultado.stock_suficiente) {
                            // Mostrar notificación discreta
                            const toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                            
                            toast.fire({
                                icon: 'warning',
                                title: 'Stock actualizado',
                                text: 'El stock de algunos productos ha cambiado.'
                            });
                        }
                    });
            }
        }, 120000); // 2 minutos
    }
    
    // Limpiar interval al salir de la página
    window.addEventListener('beforeunload', () => {
        if (verificacionInterval) {
            clearInterval(verificacionInterval);
        }
    });
    
    
    // Guardar el estado anterior del select para poder revertir si el usuario cancela
    document.querySelectorAll('.selector-estado').forEach(select => {
        select.dataset.previousValue = select.value;
    });
    
    // Exponer función para uso externo
    window.actualizarPrecios = actualizarPrecios;
    window.actualizarStockPostVenta = actualizarStockPostVenta;
});

// Las demás funciones permanecen igual
function filtrarVentas() {
    const estadoSeleccionado = document.getElementById("filtroEstado").value.toLowerCase();
    const busqueda = document.getElementById("buscarCompra").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaVentas tr.venta-row");
    
    filas.forEach(fila => {
        const textoFila = fila.textContent.toLowerCase();
        const estadoActual = fila.querySelector(".selector-estado") ? 
                                fila.querySelector(".selector-estado").value.toLowerCase() : '';
        
        const coincideBusqueda = textoFila.includes(busqueda);
        const coincideEstado = estadoSeleccionado === "" || estadoActual === estadoSeleccionado;
        
        fila.style.display = (coincideBusqueda && coincideEstado) ? "" : "none";
    });

    // Ocultar headers de fecha sin resultados visibles
    document.querySelectorAll("#tablaVentas tr.date-header").forEach(header => {
        let siguienteFila = header.nextElementSibling;
        let tieneVisibles = false;
        
        while (siguienteFila && !siguienteFila.classList.contains("date-header")) {
            if (siguienteFila.style.display !== "none") {
                tieneVisibles = true;
                break;
            }
            siguienteFila = siguienteFila.nextElementSibling;
        }
        
        header.style.display = tieneVisibles ? "" : "none";
    });
}

function cambiarEstado(id, nuevoEstado) {
    Swal.fire({
        title: 'Cambiar estado',
        text: `¿Estás seguro de cambiar el estado a "${nuevoEstado}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('cambiar_estado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id, estado: nuevoEstado })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el estado visualmente sin recargar
                    const fila = document.querySelector(`#fila-venta-${id}`);
                    if (fila) {
                        // Actualizar el span de estado
                        const estadoSpan = fila.querySelector("td:nth-child(4) span");
                        if (estadoSpan) {
                            // Remover todas las clases de estado
                            estadoSpan.classList.remove('estado-pagada', 'estado-pendiente', 'estado-cancelada');
                            
                            // Añadir la clase correspondiente al nuevo estado
                            let nuevaClase = '';
                            switch(nuevoEstado) {
                                case 'pagada':
                                    nuevaClase = 'estado-pagada';
                                    break;
                                case 'pendiente':
                                    nuevaClase = 'estado-pendiente';
                                    break;
                                case 'cancelada':
                                    nuevaClase = 'estado-cancelada';
                                    break;
                            }
                            estadoSpan.classList.add(nuevaClase);
                            estadoSpan.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
                        }
                        
                        // Actualizar el valor anterior del select
                        const select = fila.querySelector(".selector-estado");
                        if (select) {
                            select.dataset.previousValue = nuevoEstado;
                        }
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: `El estado de la venta ${id} ha sido cambiado a ${nuevoEstado}.`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al actualizar',
                        text: data.message,
                        showConfirmButton: true
                    });
                    
                    // Revertir el select al valor anterior
                    const select = document.querySelector(`#fila-venta-${id} .selector-estado`);
                    if (select) {
                        select.value = select.dataset.previousValue;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo actualizar el estado. Inténtalo de nuevo más tarde.',
                    showConfirmButton: true
                });
                
                // Revertir el select al valor anterior
                const select = document.querySelector(`#fila-venta-${id} .selector-estado`);
                if (select) {
                    select.value = select.dataset.previousValue;
                }
            });
        } else {
            // Si el usuario cancela, volver al estado anterior en el select
            const select = document.querySelector(`#fila-venta-${id} .selector-estado`);
            if (select) {
                select.value = select.dataset.previousValue;
            }
        }
    });
}


// Función para verificar stock antes de enviar
async function verificarStockAntes() {
    const productos = [];
    
    // Recopilar todos los productos del formulario
    document.querySelectorAll('.producto-item').forEach(item => {
        const select = item.querySelector('.producto-select');
        const cantidadInput = item.querySelector('.cantidad');
        
        if (select.value && cantidadInput.value) {
            productos.push({
                producto_id: parseInt(select.value),
                cantidad: parseInt(cantidadInput.value)
            });
        }
    });
    
    if (productos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin productos',
            text: 'Debe agregar al menos un producto para realizar la venta',
            showConfirmButton: true
        });
        return false;
    }
    
    try {
        const response = await fetch('verificar_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ productos: productos })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            // Mostrar errores de stock
            let mensaje = data.message + '\n\n';
            if (data.errores && data.errores.length > 0) {
                mensaje += 'Problemas encontrados:\n';
                data.errores.forEach((error, index) => {
                    mensaje += `• ${error}\n`;
                });
            }
            
            Swal.fire({
                icon: 'error',
                title: '❌ Stock Insuficiente',
                text: mensaje,
                showConfirmButton: true,
                confirmButtonText: 'Entendido',
                customClass: {
                    popup: 'swal-wide'
                }
            });
            return false;
        }
        
        // Si todo está bien, mostrar confirmación opcional
        let mensaje = '✅ Stock verificado correctamente\n\n';
        if (data.productos_verificados && data.productos_verificados.length > 0) {
            mensaje += 'Productos a vender:\n';
            data.productos_verificados.forEach(producto => {
                mensaje += `• ${producto.nombre}: ${producto.cantidad_solicitada} unidad(es)\n`;
                mensaje += `  Stock actual: ${producto.stock_actual} → Quedará: ${producto.stock_resultante}\n`;
            });
        }
        
        const result = await Swal.fire({
            icon: 'success',
            title: 'Verificación Exitosa',
            text: mensaje,
            showCancelButton: true,
            confirmButtonText: 'Procesar Venta',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'swal-wide'
            }
        });
        
        return result.isConfirmed;
        
    } catch (error) {
        console.error('Error en verificación:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo verificar el stock. Inténtalo de nuevo.',
            showConfirmButton: true
        });
        return false;
    }
}

// Modificar el evento de envío del formulario
document.getElementById('formVenta').addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevenir envío automático
    
    // Verificar stock antes de enviar
    const stockOk = await verificarStockAntes();
    
    if (stockOk) {
        // Si el stock está bien, enviar el formulario
        this.submit();
    }
});

// Agregar estilos CSS para mejorar las alertas
const style = document.createElement('style');
style.textContent = `
    .swal-wide {
        width: 600px !important;
    }
    .swal2-popup .swal2-html-container {
        text-align: left !important;
        white-space: pre-line !important;
        font-family: monospace !important;
        font-size: 14px !important;
    }
`;
document.head.appendChild(style);

function confirmarGenerarFactura(idVenta) {
    swal({
        title: "Generar Factura",
        text: "¿Deseas generar y descargar la factura de esta venta?",
        icon: "info",
        buttons: ["Cancelar", "Generar"],
    }).then((confirmar) => {
        if (confirmar) {
            window.location.href = `generar_factura.php?id_venta=${idVenta}`;
        }
    });
}
</script>